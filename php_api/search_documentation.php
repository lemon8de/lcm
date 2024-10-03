<?php
    require 'db_connection.php';
    $bl_number = isset($_GET['bl_number']) ? $_GET['bl_number'] : null;
    $shipment_status = isset($_GET['shipment_status']) ? $_GET['shipment_status'] : null;

    if ($bl_number !== null) {
        $bl_number .= "%";
        $sql = "SELECT distinct bl_number, max(forwarder_name) as forwarder_name, max(commercial_invoice) as commercial_invoice, max(shipment_status) as shipment_status, max(commodity) as commodity, max(eta_mnl) as eta_mnl, max(ata_mnl) as ata_mnl, min(confirm_departure) as confirm_departure from m_shipment_sea_details as a left join m_vessel_details as b on a.shipment_details_ref = b.shipment_details_ref where bl_number like :bl_number group by bl_number;";
        $stmt_get_cards = $conn -> prepare($sql);
        $stmt_get_cards -> bindParam(":bl_number", $bl_number);
        $stmt_get_cards -> execute();
    } else {
        $shipment_status = "%" . $shipment_status . "%";
        $sql = "SELECT distinct bl_number, max(forwarder_name) as forwarder_name, max(commercial_invoice) as commercial_invoice, max(shipment_status) as shipment_status, max(commodity) as commodity, max(eta_mnl) as eta_mnl, max(ata_mnl) as ata_mnl, min(confirm_departure) as confirm_departure from m_shipment_sea_details as a left join m_vessel_details as b on a.shipment_details_ref = b.shipment_details_ref where shipment_status like :shipment_status group by bl_number;";
        $stmt_get_cards = $conn -> prepare($sql);
        $stmt_get_cards -> bindParam(":shipment_status", $shipment_status);
        $stmt_get_cards -> execute();

    }

    $sql = "SELECT shipment_status, color from m_shipment_status";
    $stmt_callout_colors = $conn -> prepare($sql);
    $stmt_callout_colors -> execute();
    $colors = [];

    // Fetch the data and build the associative array
    while ($row = $stmt_callout_colors->fetch(PDO::FETCH_ASSOC)) {
        $colors[$row['shipment_status']] = $row['color'];
    }

    $inner_html = "";
    while ($data = $stmt_get_cards -> fetch(PDO::FETCH_ASSOC)) {

        $border_color = $colors[$data['shipment_status']] ?? $colors['default'];

        if ($data['confirm_departure'] == '0') {
            $not_confirmed = "<p class='badge' style='color:#fff; background-color:#dc3545'>NOT YET CONFIRMED</p>";
        } else {
            $not_confirmed = "";
        }

        $date_to_show = "TBA";
        if (isset($data['ata_mnl'])) {
            $date_to_show = "ATA: " . substr($data['ata_mnl'], 0, 10);
        } elseif (isset($data['eta_mnl'])) {
            $date_to_show = "ETA: " . substr($data['eta_mnl'], 0, 10);
        }

        $inner_html .= <<<HTML
            <div class="callout" style="border-left-color:{$border_color};">
                <div class="container">
                    {$not_confirmed}
                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input ck-blnumber" id="{$data['bl_number']}">
                                <label class="form-check-label" for="{$data['bl_number']}"><h4>{$data['bl_number']}</h4></label>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4>{$data['forwarder_name']}</h4>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            {$date_to_show}
                        </div>
                        <div class="col-6">
                            {$data['commercial_invoice']}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            {$data['shipment_status']}
                        </div>
                        <div class="col-6">
                            {$data['commodity']}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <a class="collapsed text-primary" id="{$data['bl_number']}" data-toggle="collapse" href="#viewmore-{$data['bl_number']}" onclick="load_containers(this)">
                                View More
                            </a>
                        </div>
                    </div>
                </div>
                <div class="container collapse mt-2" id="viewmore-{$data['bl_number']}">
                    <div class="row">
                        <table class="table table-head-fixed text-nowrap table-hover">
                            <thead>
                                <tr style="border-bottom:1px solid black">
                                    <th>CONTAINER NO.</th>
                                    <th>R. DELIVERY</th>
                                    <th>T. DELIVERY</th>
                                    <th>TABS</th>
                                </tr>
                            </thead>
                            <tbody id="table-{$data['bl_number']}">
                                <tr>
                                    <td>DATA</td>
                                    <td>DATA</td>
                                    <td>DATA</td>
                                    <td>DATA</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        HTML;
    }
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);