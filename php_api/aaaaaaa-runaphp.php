<?php
    require 'db_connection.php';

    $compute_set = [
        'basis' => 'BL',
        'data_set' => [
            'rate' => 500,
        ],
    ];

    $sql = "UPDATE m_billing_compute set computation_set = :computation_set where billing_compute_ref = :billing_compute_ref";
    $stmt = $conn -> prepare($sql);
    $encoded_compute_set = json_encode($compute_set);
    $stmt -> bindParam(":computation_set", $encoded_compute_set);
    $stmt -> bindValue("billing_compute_ref", 'billing_m_6722f5d4330684.38118791');
    $stmt -> execute();