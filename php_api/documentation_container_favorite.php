<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $bl_number = str_replace("star-", "", $_POST['bl_number']);
    $action = $_POST['action'];

    $fav_value = $action == 'favorite' ? '1' : '0';

    if ($action == 'favorite') {
        $sql = "INSERT into t_sea_user_favorites (username, bl_number, favorite) values (:username, :bl_number, '1')";
    } else {
        $sql = "DELETE from t_sea_user_favorites where username = :username and bl_number = :bl_number";
    }
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':bl_number', $bl_number);
    $stmt -> bindParam(':username', $_SESSION['username']);
    $stmt -> execute();

    echo json_encode(["result" => "done"]);