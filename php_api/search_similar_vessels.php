<?php
require 'db_connection.php';
$vessel_name = trim($_GET['vessel_name']);

//9 OCT revision, validating the vessel_name input
//TS KAOHSIUNG V.24017S
//CALIDRIS    V.0134S
//CALIDRISV.     134S
//cases, too many spaces, no spaces, zero on the voyage number
$pattern = '/(.*)(\s*V.\s*)(0*)(.*)/';
if (preg_match_all($pattern, $vessel_name, $matches)) {
    $vessel_name = trim($matches[1][0]) . " " . trim($matches[2][0]) .  " " . trim($matches[4][0]);
}


$return_body = [];

$sql = "SELECT top 1 eta_mnl, ata_mnl, atb, vessel_name from m_vessel_details where vessel_name = :vessel_name order by id desc";
$stmt = $conn -> prepare($sql);
$stmt -> bindParam(':vessel_name', $vessel_name);
$stmt -> execute();
$data = $stmt -> fetch(PDO::FETCH_ASSOC);

$sql_ref = "SELECT shipment_details_ref, vessel_name from m_vessel_details where vessel_name = :vessel_name and (eta_mnl = :eta_mnl or eta_mnl is null) and (ata_mnl = :ata_mnl or ata_mnl is null) and (atb = :atb or atb is null) order by id desc";
$stmt_ref = $conn -> prepare($sql_ref);
$sql_container = "SELECT container from m_shipment_sea_details where shipment_details_ref = :shipment_details_ref";
$stmt_container = $conn -> prepare($sql_container);

if ($data) {
    $return_body['exists'] = true;
    $return_body['eta_mnl'] = !isset($data['eta_mnl']) ? null : date('Y-m-d', strtotime($data['eta_mnl']));
    $return_body['ata_mnl'] = !isset($data['ata_mnl']) ? null : date('Y-m-d', strtotime($data['ata_mnl']));
    $return_body['atb'] = !isset($data['atb']) ? null : date('Y-m-d', strtotime($data['atb']));

    $stmt_ref -> bindParam(':vessel_name', $data['vessel_name']);
    $stmt_ref -> bindParam(':eta_mnl', $data['eta_mnl']);
    $stmt_ref -> bindParam(':ata_mnl', $data['ata_mnl']);
    $stmt_ref -> bindParam(':atb', $data['atb']);
    $stmt_ref -> execute();
    $info_html = "<i class='icon fas fa-info'></i>This entry shares vessel details with: ";
    while ($shipment = $stmt_ref -> fetch(PDO::FETCH_ASSOC)) {
        $stmt_container -> bindParam(':shipment_details_ref', $shipment['shipment_details_ref']);
        $stmt_container -> execute();
        if ($shipment_data = $stmt_container -> fetch(PDO::FETCH_ASSOC)) {
            $info_html .= $shipment_data['container'] . ", ";
        }
    }
    $return_body['info_html'] = $info_html;
} else {
    $return_body['exists'] = false;
}

echo json_encode($return_body);