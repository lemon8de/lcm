<?php
    require 'db_connection.php';

    $shipment_details_ref = $_POST['shipment_details_ref'];

    //confirmed status
    $sql = "UPDATE m_shipment_sea_details set confirm_departure = 1 where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue('shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    //create rows for other tables: delivery plan, completion details, polytainer details
    $sql = "INSERT into m_delivery_plan (shipment_details_ref) values (:shipment_details_ref)";
    $stmt = $conn ->prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    $sql = "INSERT into m_completion_details (shipment_details_ref) values (:shipment_details_ref)";
    $stmt = $conn ->prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    //find if a polytainer detail is valid polytainer, plastic pallet, wireharness
    //select commodity from table where ref = ref
    $sql = "SELECT a.commodity, b.polytainer_detail from m_shipment_sea_details as a left join list_commodity as b on a.commodity = b.display_name where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn ->prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) { //get away with the array lookup notice
        if($result['polytainer_detail']) {
            $sql = "INSERT into m_polytainer_details (shipment_details_ref) values (:shipment_details_ref)";
            $stmt = $conn ->prepare($sql);
            $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
            $stmt -> execute();
        }
    }


    header('location: ../pages/incoming_sea.php');
    exit();