<h1>content</h1>
<?php

require '../php_api/db_connection.php';
//functions: the database connection $conn
require '../php_static/lookup_column.php';
//functions: the function get_columns

$column_names = get_columns('m_shipment_sea_details', $conn);

foreach ($column_names as $col) {
    echo <<<HTML
        <p>$col</p>
    HTML;
}

$conn = null;