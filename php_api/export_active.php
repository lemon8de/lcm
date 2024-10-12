<?php
require 'db_connection.php';

$show_active = $_POST['show_active_only'];
$month = $_POST['month'];
$year = $_POST['year'];

if ($show_active == "false" && ($year == "" || $month == "")) {
    exit();
}

if ($show_active == "true") {
    $sql = "SELECT * from active_report";
    $stmt = $conn -> prepare($sql);
    $stmt -> execute();
} else {
    $sql = "SELECT a.forwarder_name, b.vessel_name, b.eta_mnl, b.ata_mnl, b.atb, a.bl_number, a.container, a.commercial_invoice, a.commodity, c.required_delivery_sched, c.deliver_plan, c.tabs, a.shipment_status, a.origin_port, d.no_days_port, a.type_of_expense from m_shipment_sea_details as a left join m_vessel_details as b on a.shipment_details_ref = b.shipment_details_ref left join m_delivery_plan as c on a.shipment_details_ref = c.shipment_details_ref left join m_mmsystem as d on a.shipment_details_ref = d.shipment_details_ref left join m_completion_details as e on a.shipment_details_ref = e.shipment_details_ref WHERE a.confirm_departure = '1' and actual_received_at_falp BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE))";

    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':start_year', $year);
    $stmt -> bindParam(':start_year2', $year);
    $stmt -> bindParam(':start_month', $month);
    $stmt -> bindParam(':start_month2', $month);
    $stmt -> execute();
}

$headers = "";
$csv_data = [];
while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    if ($headers == "") {
        $headers = array_keys($data);
    }
    array_push($csv_data, array_values($data));
}

header('Content-Type: text/csv');
$output = fopen('php://output', 'w');

fputcsv($output, $headers);
foreach ($csv_data as $row) {
    fputcsv($output, $row);
}

fclose($output);