<?php
require 'db_connection.php';
require '../php_static/session_lookup.php';

$outgoing_details_ref = $_POST['outgoing_details_ref'];
$mode_of_shipment = $_POST['mode_of_shipment'] == "" ? null : $_POST['mode_of_shipment'];
$vessel_name = $_POST['vessel_name'] == "" ? null : $_POST['vessel_name'];
$pattern = '/(.*)(\s*V\.\s*)(0*)(.*)/';
if (preg_match_all($pattern, $vessel_name, $matches)) {
    $vessel_name = trim($matches[1][0]) . " " . trim($matches[2][0]) .  " " . trim($matches[4][0]);
}
$shipping_line = $_POST['shipping_line'] == "" ? null : $_POST['shipping_line'];
$etd_mnl = $_POST['etd_mnl'] == "" ? null : $_POST['etd_mnl'];
$eta_destination = $_POST['eta_destination'] == "" ? null : $_POST['eta_destination'];

//something has to be edited
if ($vessel_name == "" && $shipping_line == "" && $etd_mnl == "" && $eta_destination == "") {
    $notification = [
        "icon" => "warning",
        "text" => "No Edits Made",
    ];
    $return_body = [];
    $return_body['notification'] = $notification;
    echo json_encode($return_body);
    exit();
}

//check if the vessel name is changed so we know if we have to only make edits on this one alone or similar invoices
$sql = "SELECT vessel_name, mode_of_shipment, shipping_line, format(etd_mnl, 'yyyy-MM-dd') as etd_mnl, format(eta_destination, 'yyyy-MM-dd') as eta_destination from m_outgoing_vessel_details where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn -> prepare($sql);
$stmt -> bindParam(":outgoing_details_ref", $outgoing_details_ref);
$stmt -> execute();

$sql = "SELECT outgoing_details_ref from m_outgoing_vessel_details where vessel_name = :vessel_name";

if ($data_vessel = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    if ($data_vessel['vessel_name'] === $vessel_name) {
        //we know that we have to make edits to all similar vessels
        $stmt_all = $conn -> prepare($sql);
        $stmt_all -> bindParam(":vessel_name", $vessel_name);
        $stmt_all -> execute();
        $outgoing_details_refs = $stmt_all -> fetchAll(PDO::FETCH_COLUMN);
    } else {
        //make the edit on this guy alone
        $outgoing_details_refs = [$outgoing_details_ref];
    }
}

//find what changed, and make m_change_history logs for all shipment_details_ref
$compare_set_user = [$vessel_name, $mode_of_shipment, $shipping_line, $etd_mnl, $eta_destination];
$compare_set_database_values = array_values($data_vessel);
$compare_set_database_keys = array_keys($data_vessel);
$sql_history = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to, username) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to, :username)";
$stmt_history = $conn -> prepare($sql_history);
$stmt_history -> bindParam(":username", $_SESSION['username']);
$stmt_history -> bindValue(':table_name', 'm_outgoing_vessel_details');

//so we now check for changes here
for ($i = 0; $i < count($compare_set_database_keys); $i++) {
    if ($compare_set_user[$i] !== $compare_set_database_values[$i]) {
        //another loop to make history logs for each and every container
        foreach($outgoing_details_refs as $container_shipment_details_ref) {
            $stmt_history -> bindValue(':shipment_details_ref', $container_shipment_details_ref);
            $stmt_history -> bindValue(':column_name', $compare_set_database_keys[$i]);
            $stmt_history -> bindValue(':changed_from', $compare_set_database_values[$i]);
            $stmt_history -> bindValue(':changed_to', $compare_set_user[$i]);
            $stmt_history -> execute();
        }
    }
}

//changes logged, now do the update set for real
$placeholders = rtrim(str_repeat('?,', count($outgoing_details_refs)), ',');
$sql_update = "UPDATE m_outgoing_vessel_details set vessel_name = ?, mode_of_shipment = ?, shipping_line = ?, etd_mnl = ?, eta_destination = ? WHERE outgoing_details_ref IN ($placeholders)";
$stmt_update = $conn -> prepare($sql_update);
$stmt_update->execute(array_merge([$vessel_name, $mode_of_shipment, $shipping_line, $etd_mnl, $eta_destination], $outgoing_details_refs));

$conn = null;
//header('location: ../pages/incoming_sea.php');
//exit();
$notification = [
    "icon" => "success",
    "text" => "Details Updated",
];
$return_body = [];
$return_body['notification'] = $notification;

echo json_encode($return_body);