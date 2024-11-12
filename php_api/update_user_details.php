<?php
    require 'db_connection.php';
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $can_edit = $_POST['can_edit'];
    $is_admin = $_POST['is_admin'];

    if ($can_edit === "true") {
        $site_role = "EDITOR";
        $editing_privileges = "all";
    } else {
        $site_role = "GUEST";
        $editing_privileges = null;
    }
    if ($is_admin === "true") {
        $site_role = "ADMIN";
    }
    $id = str_replace('user-', '', $id);
    $sql = "UPDATE m_user_accounts set username = :username, password = :password, site_role = :site_role, editing_privileges = :editing_privileges where id = :id";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":username", $username);
    $stmt -> bindParam(":password", $password);
    $stmt -> bindParam(":site_role", $site_role);
    $stmt -> bindParam(":editing_privileges", $editing_privileges);
    $stmt -> bindParam(":id", $id);
    $stmt -> execute();

    echo json_encode(['status' => true]);