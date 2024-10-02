<?php
    require 'db_connection.php';

    $shipment_details_ref = $_POST['shipment_details_ref'];

    //confirmed status
    $sql = "UPDATE m_shipment_sea_details set confirm_departure = 1 where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue('shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    //log this in changes_table
    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> bindValue(':table_name', 'm_shipment_sea_details');
    $stmt -> bindValue(':column_name', 'confirm_departure');
    $stmt -> bindValue(':changed_from', 0);
    $stmt -> bindValue(':changed_to', 1);
    $stmt -> execute();

    //create rows for other tables: delivery plan, completion details, polytainer details
    $sql = "INSERT into m_delivery_plan (shipment_details_ref) values (:shipment_details_ref)";
    $stmt = $conn ->prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    $sql = "INSERT into m_completion_details (shipment_details_ref) values (:shipment_details_ref)";
    $stmt = $conn ->prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    $sql = "INSERT into m_mmsystem (shipment_details_ref) values (:shipment_details_ref)";
    $stmt = $conn ->prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    //find if a polytainer detail is valid polytainer, plastic pallet, wireharness
    //select commodity from table where ref = ref
    $sql = "SELECT a.commodity, b.polytainer_detail from m_shipment_sea_details as a left join list_commodity as b on a.commodity = b.display_name where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn ->prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) { //get away with the array lookup notice
        if($result['polytainer_detail']) {
            $sql = "INSERT into m_polytainer_details (shipment_details_ref) values (:shipment_details_ref)";
            $stmt = $conn ->prepare($sql);
            $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
            $stmt -> execute();
        }
    }

    //invoice table creation
    $sql = "SELECT commercial_invoice from m_shipment_sea_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    $sql_duplicate = "SELECT id from import_data where shipping_invoice = :shipping_invoice";
    $stmt_duplicate = $conn -> prepare($sql_duplicate);

    $sql_invoice = "INSERT into import_data (shipment_details_ref, shipping_invoice) values (:shipment_details_ref, :shipping_invoice)";
    $stmt_invoice = $conn -> prepare($sql_invoice);

    $invoices  = $stmt -> fetch(PDO::FETCH_COLUMN);
    if ($invoices) {
        $pattern = '/([A-Za-z0-9-_]+)/';

        if (preg_match_all($pattern, $invoices, $matches)) {
            foreach ($matches[0] as $match) {

                $stmt_duplicate -> bindParam(':shipping_invoice', $match);
                $stmt_duplicate -> execute();
                $duplicate = $stmt_duplicate -> fetch(PDO::FETCH_ASSOC);

                if (!$duplicate) {
                    $stmt_invoice -> bindParam(':shipment_details_ref', $shipment_details_ref);
                    $stmt_invoice -> bindParam(':shipping_invoice', $match);
                    $stmt_invoice -> execute();
                }
            }
        } 
    }

    $conn = null;
    header('location: ../pages/documentation_sea.php');
    exit();