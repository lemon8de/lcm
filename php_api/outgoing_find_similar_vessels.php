<?php
    require 'db_connection.php';
    $vessel_name = $_GET['vessel_name'];
    $pattern = '/(.*)(\s*V\.\s*)(0*)(.*)/';
    if (preg_match_all($pattern, $vessel_name, $matches)) {
        $vessel_name = trim($matches[1][0]) . " " . trim($matches[2][0]) .  " " . trim($matches[4][0]);
    }

    $sql = "SELECT top 1 shipping_line, format(etd_mnl, 'yyyy-MM-dd') as etd_mnl, format(eta_destination, 'yyyy-MM-dd') as eta_destination from m_outgoing_vessel_details where vessel_name = :vessel_name";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":vessel_name", $vessel_name);
    $stmt -> execute();

    $sql = "SELECT vessel_name, string_agg(invoice_no, ', ') as invoices from m_outgoing_fsib as a left join m_outgoing_vessel_details as b on a.outgoing_details_ref = b.outgoing_details_ref where vessel_name = :vessel_name group by vessel_name";
    $stmt_vessel = $conn -> prepare($sql);
    $stmt_vessel -> bindParam(":vessel_name", $vessel_name);
    $stmt_vessel -> execute();

    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC && $linked_data = $stmt_vessel -> fetch(PDO::FETCH_ASSOC))) {
        $response_body = [
            'shipping_line' => $data['shipping_line'],
            'etd_mnl' => $data['etd_mnl'],
            'eta_destination' => $data['eta_destination'],
            'list_of_invoices' => $linked_data['invoices']
        ];
    } else {
        $response_body = [
            'exited' => true,
        ];
    }
    echo json_encode($response_body);
    exit();