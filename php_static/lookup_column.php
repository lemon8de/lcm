<?php

function get_columns( $table_name, $conn) {
    $sql = "SELECT column_name from information_schema.columns where table_name = :table_name";
    $stmt = $conn -> prepare($sql);

    $stmt -> bindValue(':table_name', $table_name);
    $stmt -> execute();

    $column_names = $stmt -> fetchAll(PDO::FETCH_COLUMN);
    return $column_names;
}