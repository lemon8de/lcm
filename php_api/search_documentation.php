<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $bl_number = isset($_GET['bl_number']) ? $_GET['bl_number'] : "";
    $shipment_status = isset($_GET['shipment_status']) ? $_GET['shipment_status'] : "";
    $refresh_filters = $_GET['refresh_filters'];
    $month = $_GET['month'];
    $year = $_GET['year'];
    $response_body = [];

    $sql = "EXEC SearchBLCardsThisMonth :BlNumber, :ShipmentStatus, :StartYear, :StartMonth, :Username";
    $stmt_get_cards = $conn -> prepare($sql);
    $bl_number = "%" . $bl_number . "%";
    $shipment_status = "%" . $shipment_status . "%";
    $stmt_get_cards -> bindParam(":BlNumber", $bl_number);
    $stmt_get_cards -> bindParam(":ShipmentStatus", $shipment_status);
    $stmt_get_cards -> bindParam(":StartYear", $year);
    $stmt_get_cards -> bindParam(":StartMonth", $month);
    $stmt_get_cards -> bindParam(":Username", $_SESSION['username']);
    $stmt_get_cards -> execute();

    $sql = "SELECT shipment_status, color from m_shipment_status";
    $stmt_callout_colors = $conn -> prepare($sql);
    $stmt_callout_colors -> execute();
    $colors = [];

    // Fetch the data and build the associative array
    while ($row = $stmt_callout_colors->fetch(PDO::FETCH_ASSOC)) {
        $colors[$row['shipment_status']] = $row['color'];
    }

    //this query will be used to wipe the filters clean and replace it
    //will only occur when you change month, year, bl_number
    //don't wipe it if shipment status is not '' 
    if ($refresh_filters == 'true') {
        $sql = "EXEC SearchFiltersThisMonth :BlNumber, :StartYear, :StartMonth";
        $stmt_get_filters = $conn -> prepare($sql);
        $bl_number = "%" . $bl_number . "%";
        $stmt_get_filters -> bindParam(":BlNumber", $bl_number);
        $stmt_get_filters -> bindParam(":StartYear", $year);
        $stmt_get_filters -> bindParam(":StartMonth", $month);
        $stmt_get_filters -> execute();

        $inner_html_filter = "";
        while ($data = $stmt_get_filters -> fetch(PDO::FETCH_ASSOC)) {
            $color = $colors[$data['shipment_status']] ?? $colors['default'];
            $inner_html_filter .= <<<HTML
                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input ck-shipment-status" onchange="filter_shipment(this)" id="{$data['shipment_status']}">
                    <label class="form-check-label" for="{$data['shipment_status']}">{$data['shipment_status']}&nbsp;<span class="right badge" style="color:#fff;background-color:{$color};">{$data['count']}</span></label>
                </div>
            HTML;
        }
        $response_body['inner_html_filter'] = $inner_html_filter;
    }

    $inner_html = "";
    while ($data = $stmt_get_cards -> fetch(PDO::FETCH_ASSOC)) {
        if ($data['confirm_departure'] == '0') {
            $not_confirmed = "<p class='badge' style='color:#fff; background-color:#dc3545'><i class='fas fa-box'></i>&nbsp;&nbsp;<i class='fas fa-times'></i></p>";
        } else {
            $not_confirmed = "";
        }

        $date_to_show = "TBA";
        if (isset($data['ata_mnl'])) {
            $date_to_show = "ATA: " . substr($data['ata_mnl'], 0, 10);
        } elseif (isset($data['eta_mnl'])) {
            $date_to_show = "ETA: " . substr($data['eta_mnl'], 0, 10);
        }

        $shipment_status_breakdown = "";
        $shipment_statuses = explode(', ', $data['shipment_status']);
        // Count the occurrences of each value
        $valueCounts = array_count_values($shipment_statuses);
    
        // Filter the array to keep only values that occur more than once
        $uniqueValues = array_filter($valueCounts, function($count) {
            return $count; // Keep only values that occur more than once
        });

        $shipment_statuses = array_keys($uniqueValues);

        foreach ($shipment_statuses as $shipment_status) {
            $border_color_multiple = $colors[$shipment_status] ?? $colors['default'];
            $shipment_status_breakdown .= <<<HTML
                <span style="font-size:75%;font-weight:700;border-radius:.25rem;padding:.25em .4em;color:#fff;background-color:{$border_color_multiple}">{$shipment_status}</span> 
            HTML;
        }
        $border_color = $colors[$shipment_statuses[0]] ?? $colors['default'];


        if (isset($data['favorite']) && $data['favorite'] == '1') {
            $star = <<<HTML
                <i class="fas fa-star" style="cursor:pointer;" id="star-{$data['bl_number']}" onclick="container_favorite(this, 'unfavorite')"></i>
            HTML;
        } else {
            $star = <<<HTML
                <i class="far fa-star" style="cursor:pointer;" id="star-{$data['bl_number']}" onclick="container_favorite(this, 'favorite')"></i>
            HTML;
        }
        $inner_html .= <<<HTML
            <div class="callout" style="border-left-color:{$border_color};">
                <div class="container">
                    <div class="row">
                        <div class="container d-flex justify-content-between text-warning" style="font-size: 115%;">
                            <div class="text-left">{$not_confirmed}</div>
                            <div class="text-right">{$star}</div>
                        </div>
                    </div>
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
                            {$shipment_status_breakdown}
                        </div>
                        <div class="col-6">
                            {$data['commodity']}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <a class="collapsed text-primary" style="text-decoration:none;" id="{$data['bl_number']}" data-toggle="collapse" href="#viewmore-{$data['bl_number']}" onclick="load_containers(this)">
                                <i class="fas fa-search"></i>&nbsp;View
                            </a>
                        </div>
                    </div>
                </div>
                <div class="container collapse mt-2" id="viewmore-{$data['bl_number']}">
                    <div class="row">
                        <table class="table table-head-fixed text-nowrap table-hover">
                            <thead>
                                <tr style="border-bottom:1px solid black">
                                    <th>AR@F</th>
                                    <th>CONTAINER</th>
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
                    <div class="row">
                        <a class="text-primary ml-3 mr-3 modal-trigger" style="text-decoration:none;cursor:pointer;" id="{$data['bl_number']}" data-toggle="modal" data-target="#documentation_view_shipment_sea_modal" onclick="edit_container_information(this)">
                            <i class="fas fa-box"></i>&nbsp;Edit Container
                        </a>

                        <a class="text-primary ml-3 mr-3 modal-trigger" style="text-decoration:none;cursor:pointer;" id="{$data['bl_number']}" data-toggle="modal" data-target="#documentation_view_invoice_modal" onclick="edit_invoice_information(this)">
                            <i class="fas fa-file-invoice"></i>&nbsp;Edit Invoice
                        </a>
                    </div>
                </div>
            </div>
        HTML;
    }

    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);