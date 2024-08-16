<?php
    include '../php_static/session_lookup.php';
    require 'db_connection.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "select * from m_user_accounts where username = :username  and password = :password ";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    $stmt->execute();
    $conn = null;

    //fetch one row
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user) {
        //user found
        $_SESSION['username'] = $user['username'];
        $_SESSION['site_role'] = $user['site_role'];

        $_SESSION['notification_login_success'] = 'User ' . $user['username'] . ' logged in';
        header('location: ../pages/dashboard.php');
        exit();
    } else {
        $_SESSION['notification_login_fail'] = 'Invalid username and password.';
        header('location: ../pages/signin.php');
        exit();
    }