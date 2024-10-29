<?php
    require '../php_static/session_lookup.php';
    $directory = " / Billing Monitoring / Add";
    $bar_whois_active = "bill_monitoring_add";
    require '../php_static/block_urlcreep.php';
    require '../php_api/db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php include '../php_static/web/link-rels.php'?>
        <?php include '../php_static/web/scripts-rels.php'?>
    </head>
    <body class="sidebar-mini layout-fixed">
        <div class="wrapper">
            <?php include '../php_static/web/nav-bar.php'?>
            <?php include '../php_static/web/sidebar.php'?>
            <div class="content-wrapper">
                <div class="content-header">
                    <div class="container-fluid">
                        <?php include 'content/add_billing_content.php'?>
                    </div>
                </div>
            </div>
            <?php include '../php_static/web/footer.php'?>
        </div>
    </body>
    <?php include '../php_static/web/notification_handler.php';?>
</html>