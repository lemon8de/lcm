<?php
    require '../php_static/session_lookup.php';
    $directory = " / Incoming Delivery / AIR / Documentation";
    $bar_whois_active = "shipment_documentation_air";
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
                        <?php //include 'content/documentation_sea_content.php'?>
                        <?php include 'content/documentation_air_content.php'?>
                    </div>
                </div>
            </div>
            <?php include '../php_static/web/footer.php'?>
        </div>
    </body>
    <?php //include '../modals/confirm_deletion_modal.php';?>
    <?php include '../modals/documentation_view_shipment_air_modal.php';?>
    <?php include '../modals/documentation_view_invoice_modal_air.php';?>
    <?php include '../php_static/web/notification_handler.php';?>

</html>