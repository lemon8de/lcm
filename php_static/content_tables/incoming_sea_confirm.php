<table id="ConfirmIncomingSeaTable" class="table table-head-fixed text-nowrap table-hover">
    <thead>
        <tr style="border-bottom:1px solid black">
            <th>BL NUMBER</th>
            <th>CONTAINER</th>
            <th colspan="2" class="text-center">ACTION</th>
        </tr>
    </thead>
    <tbody id="ConfirmIncomingSeaTableBody">
<?php
$sql = "SELECT shipment_details_ref, bl_number, container from m_shipment_sea_details where confirm_departure = 0 ";
$stmt = $conn->prepare($sql);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo <<<HTML
        <tr>
            <td>{$row['bl_number']}</td>
            <td>{$row['container']}</td>
            <td>
                <form action="../php_api/sea_confirm_departure.php" method="POST">
                    <input type="hidden" readonly value="{$row['shipment_details_ref']}" name="shipment_details_ref">
                    <button type="submit" class="btn bg-primary btn-block">Confirm</button>
                </form>
            </td>
            <td>
                <form action="../php_api/sea_delete_departure.php" method="POST">
                    <input type="hidden" readonly value="{$row['shipment_details_ref']}" name="shipment_details_ref">
                    <button type="submit" class="btn bg-danger btn-block">Delete</button>
                </form>
            </td>
        </tr>
    HTML;
}
?>
    </tbody>
</table>