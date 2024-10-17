<?php
require 'db_connection.php';

$show_active = $_POST['show_active_only'];
$month = $_POST['month'];
$year = $_POST['year'];

if ($show_active == "false" && ($year == "" || $month == "")) {
    exit();
}

if ($show_active == "true") {
    $sql = "SELECT shipper, origin_port, destination_port, shipping_invoice, commodity, classification, commodity_quantity, commodity_uo, commercial_invoice_currency, commercial_invoice_amount, incoterm, gross_weight, forwarder_name, bl_number, vessel_name, eta_mnl, ata_mnl, atb, (SELECT string_agg(container, ' | ') from m_shipment_sea_details where bl_number = b.bl_number) as container, container_size, shipping_lines, shipment_status, required_delivery_sched, deliver_plan, tabs, date_port_out, actual_received_at_falp, polytainer_size, polytainer_quantity, etd, container_status, date_return_reused, no_days_port, no_days_falp, type_of_expense, ip_number, dr_number, received_by, time_received, total_custom_value, duitable_value, rate, customs_duty, landed_cost, vat, bank_charges, wharfage, arrastre_charges, entry_no, or_number, brokerage_fee, assessment_date from import_data as a left join m_shipment_sea_details as b on a.shipment_details_ref = b.shipment_details_ref left join m_vessel_details as c on a.shipment_details_ref = c.shipment_details_ref left join m_delivery_plan as d on a.shipment_details_ref = d.shipment_details_ref left join m_completion_details as e on a.shipment_details_ref = e.shipment_details_ref left join m_polytainer_details as f on a.shipment_details_ref = f.shipment_details_ref left join m_mmsystem as g on a.shipment_details_ref = g.shipment_details_ref WHERE actual_received_at_falp BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE)) ORDER BY shipping_invoice asc";
} else {
    $sql = "SELECT shipper, origin_port, destination_port, shipping_invoice, commodity, classification, commodity_quantity, commodity_uo, commercial_invoice_currency, commercial_invoice_amount, incoterm, gross_weight, forwarder_name, bl_number, vessel_name, eta_mnl, ata_mnl, atb, (SELECT string_agg(container, ' | ') from m_shipment_sea_details where bl_number = b.bl_number) as container, container_size, shipping_lines, shipment_status, required_delivery_sched, deliver_plan, tabs, date_port_out, actual_received_at_falp, polytainer_size, polytainer_quantity, etd, container_status, date_return_reused, no_days_port, no_days_falp, type_of_expense, ip_number, dr_number, received_by, time_received, total_custom_value, duitable_value, rate, customs_duty, landed_cost, vat, bank_charges, wharfage, arrastre_charges, entry_no, or_number, brokerage_fee, assessment_date from import_data as a left join m_shipment_sea_details as b on a.shipment_details_ref = b.shipment_details_ref left join m_vessel_details as c on a.shipment_details_ref = c.shipment_details_ref left join m_delivery_plan as d on a.shipment_details_ref = d.shipment_details_ref left join m_completion_details as e on a.shipment_details_ref = e.shipment_details_ref left join m_polytainer_details as f on a.shipment_details_ref = f.shipment_details_ref left join m_mmsystem as g on a.shipment_details_ref = g.shipment_details_ref WHERE actual_received_at_falp IS NULL OR actual_received_at_falp BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE)) ORDER BY shipping_invoice asc";
}
$stmt = $conn -> prepare($sql);
$stmt -> bindParam(':start_year', $year);
$stmt -> bindParam(':start_year2', $year);
$stmt -> bindParam(':start_month', $month);
$stmt -> bindParam(':start_month2', $month);
$stmt -> execute();

$headers = "";
$csv_data = [];

$sql = "SELECT container, actual_received_at_falp from m_shipment_sea_details as a left join m_completion_details as b on a.shipment_details_ref = b.shipment_details_ref where commercial_invoice like :shipping_invoice";
$stmt_containers = $conn -> prepare($sql);

$max_header_count_highest = 0;
while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    if ($headers == "") {
        $headers = array_keys($data);
    }
    $shipping_invoice = '%' . $data['shipping_invoice'] . '%';
    $stmt_containers -> bindParam(':shipping_invoice', $shipping_invoice);
    $stmt_containers -> execute();
    $containers = $stmt_containers -> fetchAll(PDO::FETCH_COLUMN, 0);
    $stmt_containers -> execute();
    $dates = $stmt_containers -> fetchAll(PDO::FETCH_COLUMN, 1);

    //stitch this container to this one, fuck this i wanna go home
    $container_breakdown = [];
    $max_header_count_this = 0;
    for ($i = 0; $i < count($containers); $i++) {
        $container_breakdown = array_merge($container_breakdown, [$containers[$i], $dates[$i]]);
        $max_header_count_this++;
    }

    if ($max_header_count_this > $max_header_count_highest) {
        $max_header_count_highest = $max_header_count_this;
    }

    $giga = array_merge(array_values($data), $container_breakdown);
    array_push($csv_data, $giga);
}

header('Content-Type: text/csv');
$output = fopen('php://output', 'w');

$extra_container_headers = [];
for ($i = 1; $i <= $max_header_count_highest; $i++) {
    array_push($extra_container_headers, 'CONTAINER NUMBER' . $i);
    array_push($extra_container_headers, 'RECEIVED DATE' . $i);
}

$headers = array_merge($headers, $extra_container_headers);
fputcsv($output, $headers);
foreach ($csv_data as $row) {
    fputcsv($output, $row);
}

fclose($output);