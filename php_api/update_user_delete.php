<?php
    require 'db_connection.php';
    $id = $_POST['id'];
    $id = str_replace('user-', '', $id);
    $sql = "DELETE from m_user_accounts where id = $id";
    $conn -> query($sql);

    echo json_encode(['status' => true]);