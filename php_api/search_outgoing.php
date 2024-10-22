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

    if (!isset($_GET['destination_service_center'])) {
        $destination_service_center = "";
    } else {
        $destination_service_center = $_GET['destination_service_center'];
    }

    $sql_search_partial_s = "SELECT a.outgoing_details_ref, invoice_no, container_no, destination_service_center, status, co_status from m_outgoing_fsib as a left join m_outgoing_status_details as b on a.outgoing_details_ref = b.outgoing_details_ref where 1=1";
    $sql_destination = "SELECT distinct destination_service_center from m_outgoing_fsib where 1=1";
    if ($invoice_no != "") {
        //add invoice query
        $invoice_no = "%" . $invoice_no . "%";
        $sql_search_partial_s .= " AND invoice_no like :invoice_no";
        $sql_destination .= " AND invoice_no like :invoice_no";
    }
    if ($container_no != "") {
        $container_no = "%" . $container_no . "%";
        $sql_search_partial_s .= " AND container_no like :container_no";
        $sql_destination .= " AND container_no like :container_no";
    }
    if ($destination_service_center != "" && isset($_GET['destination_service_center'])) {
        $sql_search_partial_s .= " AND destination_service_center = :destination_service_center";
        //$sql_destination .= " AND destination_service_center like :destination_service_center";
    }

    //currently, we are using month and year to filter with ship_out_date
    $sql_search = $sql_search_partial_s . " AND ship_out_date BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE))";
    $stmt_search = $conn -> prepare($sql_search);

    $sql_destination = $sql_destination . " AND ship_out_date BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE))";
    $stmt_destination = $conn -> prepare($sql_destination);

    if ($invoice_no != "") {
        $stmt_search -> bindParam(":invoice_no", $invoice_no);
        $stmt_destination -> bindParam(":invoice_no", $invoice_no);
    }
    if ($container_no != "") {
        $stmt_search -> bindParam(":container_no", $container_no);
        $stmt_destination -> bindParam(":container_no", $container_no);
    }
    if ($destination_service_center != "" && isset($_GET['destination_service_center'])) {
        $stmt_search -> bindParam(":destination_service_center", $destination_service_center);
        //$stmt_destination -> bindParam(":destination_service_center", $destination_service_center);
    }
    $stmt_search -> bindParam(":start_month", $month);
    $stmt_search -> bindParam(":start_month2", $month);
    $stmt_search -> bindParam(":start_year", $year);
    $stmt_search -> bindParam(":start_year2", $year);
    $stmt_search -> execute();

    $stmt_destination -> bindParam(":start_month", $month);
    $stmt_destination -> bindParam(":start_month2", $month);
    $stmt_destination -> bindParam(":start_year", $year);
    $stmt_destination -> bindParam(":start_year2", $year);
    $stmt_destination -> execute();


    $inner_html = "";
    while ($data = $stmt_search -> fetch(PDO::FETCH_ASSOC)) {
        $inner_html .= <<<HTML
            <tr id="{$data['outgoing_details_ref']}" onclick="loaddata.call(this)" class="modal-trigger" data-toggle="modal" data-target="#edit_outgoing_modal">
                <td><input type="checkbox" class="row-checkbox" id="ck-{$data['outgoing_details_ref']}" onclick="event.stopPropagation();"></td>
                <td>{$data['invoice_no']}</td>
                <!-- <td>{$data['container_no']}</td> -->
                <td>{$data['destination_service_center']}</td>
                <td>{$data['status']}</td>
                <td>{$data['co_status']}</td>
            </tr>
        HTML;
    }
    
    $selection = '<option value="">Destination</option>';
    while ($data = $stmt_destination -> fetch(PDO::FETCH_ASSOC)) {
        if ($destination_service_center == $data['destination_service_center']) {
            $selected = "selected ";
        } else {
            $selected = "";
        }
        $selection .= <<<HTML
            <option {$selected}value="{$data['destination_service_center']}">{$data['destination_service_center']}</option>
        HTML;
    }

    $response_body['selection'] = $selection;
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);