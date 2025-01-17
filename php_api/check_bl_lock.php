<?php
    require 'db_connection.php';

    $sql = "EXEC outgoing_LOCK_bl_input :OutgoingDetailsRef";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":OutgoingDetailsRef", $_GET['outgoing_details_ref']);
    $stmt -> execute();

    $return_body = [];
    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        if ($data['lock_bl'] == "True") {
            $notification = [
                "icon" => "warning",
                "text" => "Details Still Incomplete",
            ];
            $return_body['notification'] = $notification;
            $return_body['locked'] = 'lock';
        } else {
            $notification = [
                "icon" => "success",
                "text" => "Unlocked",
            ];
            $return_body['notification'] = $notification;
            $return_body['locked'] = 'unlock';
        }
    }

    echo json_encode($return_body);
    exit();