<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $forwarder_partner = $_POST['forwarder_partner'];
    $business_name = $_POST['business_name'];

    // Check if the file was uploaded without errors
    if (isset($_FILES['forwarder_logo']) && $_FILES['forwarder_logo']['error'] == 0) {
        $uploadedFile = $_FILES['forwarder_logo'];
        $uploadDir = '../static/img/';
        $file_name = str_replace(".", "", uniqid('image_', true)) .  basename($uploadedFile['name']);
        $uploadFilePath = $uploadDir . $file_name;
        // Move the uploaded file to the desired directory
        if (move_uploaded_file($uploadedFile['tmp_name'], $uploadFilePath)) {
            $billing_forwarder_details_ref = uniqid('forwarder_', true);
            $sql = "INSERT into m_billing_forwarder (billing_forwarder_details_ref, forwarder_partner, forwarder_logo, business_name) values (:billing_forwarder_details_ref, :forwarder_partner, :forwarder_logo, :business_name)";
            $stmt = $conn -> prepare($sql);
            $stmt -> bindParam(":billing_forwarder_details_ref", $billing_forwarder_details_ref);
            $stmt -> bindParam(":forwarder_partner", $forwarder_partner);
            $stmt -> bindParam(":forwarder_logo", $uploadFilePath);
            $stmt -> bindParam(":business_name", $business_name);
            $stmt -> execute();

            $notification = [
                "icon" => "success",
                "text" => "Forwarder Created",
            ];
            $_SESSION['notification'] = json_encode($notification);
            header('location: ../pages/edit_billing_forwarder.php');
            exit();
        } else {
            echo "Error moving the uploaded file.";
        }
    } else {
        echo "Bad Upload";
    }