<?php
    require 'db_connection.php';
    $bl_numbers = $_POST['bl_numbers'];

    $placeholders = rtrim(str_repeat('?,', count($bl_numbers)), ',');
    $sql = "SELECT STRING_AGG(shipment_details_ref, ', ') as shipment_details_ref from m_shipment_sea_details where bl_number in ($placeholders)";
    $stmt = $conn -> prepare($sql);
    $stmt -> execute($bl_numbers);

    $sql = "EXEC CompleteDeletion :ShipmentDetailsRef";
    $stmt_god_helpme = $conn -> prepare($sql);

    if ($shipment_details_ref_list = $stmt -> fetch(PDO::FETCH_COLUMN)) {
        $shipment_details_ref_list = explode(", ", $shipment_details_ref_list);
        foreach ($shipment_details_ref_list as $shipment_details_ref) {
            $stmt_god_helpme -> bindParam(":ShipmentDetailsRef", $shipment_details_ref);
            $stmt_god_helpme -> execute();
        }
    }

    $notification = [
        "icon" => "success",
        "text" => "DELETED",
    ];
    $return_body = [];
    $return_body['notification'] = $notification;
    echo json_encode($return_body);
