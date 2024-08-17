<table id="ViewIncomingSeaTable" class="table table-head-fixed text-nowrap table-hover">
    <thead>
        <tr>
            <th colspan="12" style="border: 1px solid black; text-align: center;">(1) Shipment Details</th>
            <th colspan="4" style="border: 1px solid black; text-align: center;">(2) Vessel Details</th>
        </tr>
        <tr style="border-bottom:1px solid black">
            <th>BL NUMBER</th>
            <th>CONTAINER</th>
            <th>CONTAINER SIZE</th>
            <th>COMMERCIAL INVOICE</th>
            <th>COMMODITY</th>
            <th>TYPE OF EXPENSE</th>
            <th>CLASSIFICATION</th>
            <th>SHIPPING LINES</th>
            <th>FORWARDER NAME</th>
            <th>ORIGIN PORT</th>
            <th>SHIPMENT STATUS</th>
            <th>CONFIRMED DEPARTURE</th>
            <th>VESSEL NAME</th>
            <th>ETA MNL</th>
            <th>ATA MNL</th>
            <th>ATB</th>
        </tr>
    </thead>
    <tbody id="ViewIncomingSeaTableBody">
<?php
$sql = "SELECT a.bl_number, a.container, a.container_size, a.commercial_invoice, a.commodity, a.type_of_expense, a.classification, a.shipping_lines, a.forwarder_name, a.origin_port, a.shipment_status, a.confirm_departure, b.vessel_name, b.eta_mnl, b.ata_mnl, b.atb from m_shipment_sea_details as a left join m_vessel_details as b on a.shipment_details_ref = b.shipment_details_ref";

$stmt = $conn->prepare($sql);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row['confirm_departure'] = $row['confirm_departure'] == 0 ? 'NO' : 'YES';
    //empty on date columns gives you this 1900 date
    $row['eta_mnl'] = $row['eta_mnl'] == null ? 'TBA' : date('Y/m/d', strtotime($row['eta_mnl']));
    $row['ata_mnl'] = $row['ata_mnl'] == null ? 'TBA' : date('Y/m/d', strtotime($row['ata_mnl']));
    $row['atb'] = $row['atb'] == null ? 'TBA' : date('Y/m/d', strtotime($row['atb']));
    echo <<<HTML
        <tr>
            <td>{$row['bl_number']}</td>
            <td>{$row['container']}</td>
            <td>{$row['container_size']}</td>
            <td>{$row['commercial_invoice']}</td>
            <td>{$row['commodity']}</td>
            <td>{$row['type_of_expense']}</td>
            <td>{$row['classification']}</td>
            <td>{$row['shipping_lines']}</td>
            <td>{$row['forwarder_name']}</td>
            <td>{$row['origin_port']}</td>
            <td>{$row['shipment_status']}</td>
            <td>{$row['confirm_departure']}</td>
            <td>{$row['vessel_name']}</td>
            <td>{$row['eta_mnl']}</td>
            <td>{$row['ata_mnl']}</td>
            <td>{$row['atb']}</td>
        </tr>
    HTML;
}
?>
    </tbody>
</table>