<table id="" class="table table-head-fixed text-nowrap table-hover mb-4">
    <thead>
        <tr>
            <th colspan="2" style="border: 1px solid black; text-align: center;">Import Data</th>
        </tr>
        <tr style="border-bottom:1px solid black">
            <th>DATABASE CODE</th>
            <th>INVOICE</th>
        </tr>
    </thead>
    <tbody id="">
        <?php
            $sql = "SELECT shipment_details_ref, shipping_invoice from import_data";
            $stmt = $conn -> prepare($sql);
            $stmt -> execute();

            while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                echo <<<HTML
                    <tr>
                        <td>{$row['shipment_details_ref']}</td>
                        <td>{$row['shipping_invoice']}</td>
                    </tr>
                HTML;
            }
        ?>
    </tbody>
</table>