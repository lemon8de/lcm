<?php 
    require '../php_static/session_lookup.php';
    $_SESSION['username'] = null;

    $notification = [
        "icon" => "info",
        "text" => "User has logged out",
    ];
    $_SESSION['notification'] = json_encode($notification);
    header('location: ../pages/signin.php');
    exit();