<?php
    require 'db_connection.php';
    $bl_number = str_replace("star-", "", $_POST['bl_number']);
    $action = $_POST['action'];

    $fav_value = $action == 'favorite' ? '1' : '0';

    $sql = "UPDATE m_shipment_sea_details set favorite = :fav_value where bl_number = :bl_number";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':bl_number', $bl_number);
    $stmt -> bindParam(':fav_value', $fav_value);
    $stmt -> execute();

    echo json_encode(["result" => "done"]);