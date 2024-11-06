<?php
    require '../db_connection.php';
    $billing_forwarder_details_ref = $_POST['billing_forwarder_details_ref'];
    $billing_details_ref = $_POST['billing_details_ref'];
    $shipping_line = $_POST['shipping_line'];
    $origin_port = $_POST['origin_port'] != "" ? $_POST['origin_port'] : null;
    $destination_port = $_POST['destination_port'] != "" ? $_POST['destination_port'] : null;
    $basis = $_POST['basis'];
    $month = $_POST['month'];
    $year = $_POST['year'];

    $for_date = new DateTime();
    $for_date -> setDate($year, $month, 1);
    $for_date = $for_date -> format('Y-m-d');

    //add more when the thing wants more things
    $rate = $_POST['rate'];

    $skipped = true;
    if ($basis === "BL") {
        $skipped = false;
        $computation_set = [
            'basis' => "BL",
            'data_set' => [
                'rate' => $rate,
            ],
        ];
    }

    if ($basis === "CNTR") {
        $skipped = false;
        $computation_set = [
            'basis' => "CNTR",
            'data_set' => [
                'rate' => $rate,
            ],
        ];
    }

    $computation_set = json_encode($computation_set);
    if (!$skipped) { //ensures that only supported basis gets the update treatment
        $billing_compute_ref = uniqid('billing_m_', true);
        $sql = "INSERT into m_billing_compute (billing_forwarder_details_ref, billing_details_ref, shipping_line, origin_port, destination_port, computation_set, billing_compute_ref, for_date) values (:billing_forwarder_details_ref, :billing_details_ref, :shipping_line, :origin_port, :destination_port, :computation_set, :billing_compute_ref, :for_date)";
        $stmt = $conn -> prepare($sql);
        $stmt -> bindParam(":billing_forwarder_details_ref", $billing_forwarder_details_ref);
        $stmt -> bindParam(":billing_details_ref", $billing_details_ref);
        $stmt -> bindParam(":shipping_line", $shipping_line);
        $stmt -> bindParam(":origin_port", $origin_port);
        $stmt -> bindParam(":destination_port", $destination_port);
        $stmt -> bindParam(":computation_set", $computation_set);
        $stmt -> bindParam(":billing_compute_ref", $billing_compute_ref);
        $stmt -> bindParam(":for_date", $for_date);
        $stmt -> execute();

        $notification = [
            "icon" => "success",
            "text" => "Details logged",
        ];
        $return_body = [];
        $return_body['notification'] = $notification;
        echo json_encode($return_body);
    } else {
        $notification = [
            "icon" => "warning",
            "text" => "If you see this, something is wrong",
        ];
        $return_body = [];
        $return_body['notification'] = $notification;
        echo json_encode($return_body);
    }