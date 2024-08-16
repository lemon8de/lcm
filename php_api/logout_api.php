<?php 
    require '../php_static/session_lookup.php';

    $_SESSION['notification_logout'] = 'User ' . $_SESSION['username'] . ' logged out';
    header('location: ../pages/signin.php');
    exit();