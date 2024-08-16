<?php
    require 'db_connection.php';

    $shipment_details_ref = $_POST['shipment_details_ref'];

    $sql = "DELETE from m_shipment_sea_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue('shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    $sql = "DELETE from m_vessel_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue('shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    header('location: ../pages/incoming_sea.php');
    exit();