<?php
    if (!isset($_GET['month'])) {
        echo json_encode(["exited" => true]);
        exit();
    }
    require 'db_connection.php'; 

    $invoice_no = $_GET['invoice_no'];
    $container_no = $_GET['container_no'];
    $month = $_GET['month'];
    $year = $_GET['year'];

    $sql_search_partial_s = "SELECT outgoing_details_ref, invoice_no, container_no, destination_service_center from m_outgoing_fsib where 1=1";
    if ($invoice_no != "") {
        //add invoice query
        $invoice_no = "%" . $invoice_no . "%";
        $sql_search_partial_s .= " AND invoice_no like :invoice_no";
    }
    if ($container_no != "") {
        $container_no = "%" . $container_no . "%";
        $sql_search_partial_s .= " AND container_no like :container_no";
    }

    //currently, we are using month and year to filter with ship_out_date
    $sql_search = $sql_search_partial_s . " AND ship_out_date BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE))";
    $stmt_search = $conn -> prepare($sql_search);
    if ($invoice_no != "") {
        $stmt_search -> bindParam(":invoice_no", $invoice_no);
    }
    if ($container_no != "") {
        $stmt_search -> bindParam(":container_no", $container_no);
    }
    $stmt_search -> bindParam(":start_month", $month);
    $stmt_search -> bindParam(":start_month2", $month);
    $stmt_search -> bindParam(":start_year", $year);
    $stmt_search -> bindParam(":start_year2", $year);
    $stmt_search -> execute();


    $inner_html = "";
    while ($data = $stmt_search -> fetch(PDO::FETCH_ASSOC)) {
        $inner_html .= <<<HTML
            <tr id="{$data['outgoing_details_ref']}" onclick="loaddata.call(this)" class="modal-trigger" data-toggle="modal" data-target="#edit_outgoing_modal">
                <td>{$data['invoice_no']}</td>
                <td>{$data['container_no']}</td>
                <td>{$data['destination_service_center']}</td>
            </tr>
        HTML;
    }

    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);