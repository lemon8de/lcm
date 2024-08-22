<table id="ViewIncomingSeaTable" class="table table-head-fixed text-nowrap table-hover">
    <thead>
        <tr>
            <th colspan="3" style="border: 1px solid black; text-align: center;">(2) Vessel Details</th>
            <th colspan="5" style="border: 1px solid black; text-align: center;">(1) Shipment Details</th>
        </tr>
        <tr style="border-bottom:1px solid black">

            <th>VESSEL NAME</th>
            <th>ETA MNL</th>
            <th>ATA MNL</th>
            <th>BL NUMBER</th>
            <th>CONTAINER</th>
            <th>COMMODITY</th>
            <th>FORWARDER NAME</th>
            <th>SHIPMENT STATUS</th>
            <th>CONFIRMED DEPARTURE</th>
        </tr>
    </thead>
    <tbody id="ViewIncomingSeaTableBody">
<?php
$sql = "SELECT a.bl_number, a.container, a.commodity, a.forwarder_name, a.shipment_status, a.confirm_departure, b.vessel_name, b.eta_mnl, b.ata_mnl from m_shipment_sea_details as a left join m_vessel_details as b on a.shipment_details_ref = b.shipment_details_ref order by a.container desc";

$stmt = $conn->prepare($sql);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row['confirm_departure'] = $row['confirm_departure'] == 0 ? 'NO' : 'YES';
    //empty on date columns gives you this 1900 date
    $row['eta_mnl'] = $row['eta_mnl'] == null ? 'TBA' : date('Y/m/d', strtotime($row['eta_mnl']));
    $row['ata_mnl'] = $row['ata_mnl'] == null ? 'TBA' : date('Y/m/d', strtotime($row['ata_mnl']));
    echo <<<HTML
        <tr>
            <td>{$row['vessel_name']}</td>
            <td>{$row['eta_mnl']}</td>
            <td>{$row['ata_mnl']}</td>
            <td>{$row['bl_number']}</td>
            <td>{$row['container']}</td>
            <td>{$row['commodity']}</td>
            <td>{$row['forwarder_name']}</td>
            <td>{$row['shipment_status']}</td>
            <td>{$row['confirm_departure']}</td>
        </tr>
    HTML;
}
?>
    </tbody>
</table>