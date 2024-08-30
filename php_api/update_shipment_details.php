<?php
    require 'db_connection.php';
    $shipment_details_ref = $_POST['shipment_details_ref'];
    $bl_number = $_POST['bl_number'];
    $container = $_POST['container'];
    $container_size = $_POST['container_size'];
    $commercial_invoice = $_POST['commercial_invoice'];
    $commodity = $_POST['commodity'];
    $shipping_lines = $_POST['shipping_lines'];
    $forwarder_name = $_POST['forwarder_name'];
    $origin_port = $_POST['origin_port'];
    $shipment_status = $_POST['shipment_status'];

    //check if changed, $commodity value is fucked and not the actual value on m_shipment_sea_details
    //why do i do this to myself
    $method = 'sea';
    $sql = "SELECT type_of_expense, display_name, classification from list_commodity where value = :commodity and method = :method";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue(':commodity', $commodity);
    $stmt -> bindValue(':method', $method);
    $stmt -> execute();

    if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) { //get away with the array notice
        $type_of_expense = $result['type_of_expense'];
        $commodity = $result['display_name'];
        $classification = $result['classification'];
    }
    //now we can check, saves on api processing; if we can just stop right here it would be nice
    $sql = "SELECT bl_number, container, container_size, commercial_invoice, commodity, shipping_lines, forwarder_name, origin_port, shipment_status from m_shipment_sea_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $a = $stmt -> fetch(PDO::FETCH_ASSOC);

    if ($a) {
        if ($bl_number == $a['bl_number'] and $container == $a['container'] and $container_size == $a['container_size'] and $commercial_invoice == $a['commercial_invoice'] and $commodity == $a['commodity'] and $shipping_lines == $a['shipping_lines'] and $forwarder_name == $a['forwarder_name'] and $origin_port == $a['origin_port'] and $shipment_status == $a['shipment_status']) {
            $conn = null;
            //header('location: ../pages/incoming_sea.php');
            //exit();
            $notification = [
                "icon" => "info",
                "text" => "No changes were made",
            ];
            $return_body = [];
            $return_body['notification'] = $notification;
            echo json_encode($return_body);
            exit();
        }
    }

    
    echo 'what';