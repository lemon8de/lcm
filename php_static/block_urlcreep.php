<?php
    if (!isset($_SESSION['username'])) {
        header('location: ../pages/signin.php');
    }