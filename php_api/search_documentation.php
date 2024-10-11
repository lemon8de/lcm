<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $bl_number = $_GET['bl_number'];
    $shipment_status_percentage = $_GET['percentage'];
    $vessel_name = $_GET['vessel'];
    $storage = $_GET['storage'];
    $refresh_filters = $_GET['refresh_filters'];
    $month = $_GET['month'];
    $year = $_GET['year'];
    $response_body = [];

    $bl_number = "%" . $bl_number . "%";
    $vessel_name = "%" . $vessel_name . "%";

    $sql = "EXEC SearchBLCardsThisMonth :BlNumber, :StartYear, :StartMonth, :ShipmentStatusPercentage, :VesselName, :Storage";
    $stmt_get_cards = $conn -> prepare($sql);
    $stmt_get_cards -> bindParam(":BlNumber", $bl_number);
    $stmt_get_cards -> bindParam(":ShipmentStatusPercentage", $shipment_status_percentage);
    $stmt_get_cards -> bindParam(":VesselName", $vessel_name);
    $stmt_get_cards -> bindParam(":Storage", $storage);
    $stmt_get_cards -> bindParam(":StartYear", $year);
    $stmt_get_cards -> bindParam(":StartMonth", $month);
    $stmt_get_cards -> execute();

    $sql = "SELECT shipment_status_percentage, color from m_shipment_status";
    $stmt_callout_colors = $conn -> prepare($sql);
    $stmt_callout_colors -> execute();
    $colors = [];

    // Fetch the data and build the associative array
    while ($row = $stmt_callout_colors->fetch(PDO::FETCH_ASSOC)) {
        $colors[$row['shipment_status_percentage']] = $row['color'];
    }

    //this query will be used to wipe the filters clean and replace it
    //will only occur when you change month, year, bl_number
    //don't wipe it if shipment status is not '' 
    if ($refresh_filters == 'true') {
        $sql = "EXEC SearchFiltersThisMonthPERCENTAGE :BlNumber, :StartYear, :StartMonth";
        $stmt_get_filters = $conn -> prepare($sql);
        $stmt_get_filters -> bindParam(":BlNumber", $bl_number);
        $stmt_get_filters -> bindParam(":StartYear", $year);
        $stmt_get_filters -> bindParam(":StartMonth", $month);
        $stmt_get_filters -> execute();

        $badges = [];
        $default_badge = <<<HTML
            <span class="right badge" style="background-color:#adb5bd;">0</span>
        HTML;
        for ($i = 0; $i <= 100; $i += 10) {
            $color_to_use = $colors[$i];
            array_push($badges, $default_badge);
        } 

        while ($data = $stmt_get_filters -> fetch(PDO::FETCH_ASSOC)) {
            $color = $colors[(int)$data['shipment_status_percentage']];
            //dirtiest fix known to man
            $badges[((int)$data['shipment_status_percentage'] / 10)] = <<<HTML
                <span class="right badge" style="background-color:{$color};">{$data['count']}</span>
            HTML;
        }

        $badges = array_values($badges);

        $inner_html_status_filter = <<<HTML
            <div class="container">
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-30" type="radio" value="30" name="radio">
                    <label class="form-check-label" for="radio-30">30&nbsp;{$badges[3]}</label>
                </div>
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-20" type="radio" value="20" name="radio">
                    <label class="form-check-label" for="radio-20">20&nbsp;{$badges[2]}</label>
                </div>
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-10" type="radio" value="10" name="radio">
                    <label class="form-check-label" for="radio-10">10&nbsp;{$badges[1]}</label>
                </div>
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-0" type="radio" value="0" name="radio">
                    <label class="form-check-label" for="radio-0">0&nbsp;{$badges[0]}</label>
                </div>
            </div>
            <div class="container">
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-70" type="radio" value="70" name="radio">
                    <label class="form-check-label" for="radio-70">70&nbsp;{$badges[7]}</label>
                </div>
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-60" type="radio" value="60" name="radio">
                    <label class="form-check-label" for="radio-60">60&nbsp;{$badges[6]}</label>
                </div>
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-50" type="radio" value="50" name="radio">
                    <label class="form-check-label" for="radio-50">50&nbsp;{$badges[5]}</label>
                </div>
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-40" type="radio" value="40" name="radio">
                    <label class="form-check-label" for="radio-40">40&nbsp;{$badges[4]}</label>
                </div>
            </div>
            <div class="container">
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-100" type="radio" value="100" name="radio">
                    <label class="form-check-label" for="radio-100">100&nbsp;{$badges[10]}</label>
                </div>
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-90" type="radio" value="90" name="radio">
                    <label class="form-check-label" for="radio-90">90&nbsp;{$badges[9]}</label>
                </div>
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-80" type="radio" value="80" name="radio">
                    <label class="form-check-label" for="radio-80">80&nbsp;{$badges[8]}</label>
                </div>
            </div>
        HTML;
        $response_body['inner_html_status_filter'] = $inner_html_status_filter;

        $sql = "EXEC SearchVesselsThisMonth :BlNumber, :StartYear, :StartMonth";
        $stmt_get_vessels = $conn -> prepare($sql);
        $stmt_get_vessels -> bindParam(":BlNumber", $bl_number);
        $stmt_get_vessels -> bindParam(":StartYear", $year);
        $stmt_get_vessels -> bindParam(":StartMonth", $month);
        $stmt_get_vessels -> execute();
        
        $inner_html_vessel_filter = "";
        while ($data = $stmt_get_vessels -> fetch(PDO::FETCH_ASSOC)) {
            $date = substr($data['date'],5,5);
            $inner_html_vessel_filter .= <<<HTML
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-{$data['vessel_name']}" type="radio" value="{$data['vessel_name']}" name="radio">
                    <label class="form-check-label" for="radio-{$data['vessel_name']}"><span class="text-right badge" style="background-color:#007bff;color:#ffffff">{$date}</span>&nbsp;<span class="text-right badge" style="background-color:#17a2b8;color:#ffffff">{$data['count']}</span>&nbsp;{$data['vessel_name']}</label>
                </div>
            HTML;
        }
        $response_body['inner_html_vessel_filter'] = $inner_html_vessel_filter;

        $sql = "EXEC SearchStorageThisMonth :BlNumber, :StartYear, :StartMonth";
        $stmt_get_storage = $conn -> prepare($sql);
        $stmt_get_storage -> bindParam(":BlNumber", $bl_number);
        $stmt_get_storage -> bindParam(":StartYear", $year);
        $stmt_get_storage -> bindParam(":StartMonth", $month);
        $stmt_get_storage -> execute();

        $inner_html_storage_filter = "";
        if ($data = $stmt_get_storage -> fetch(PDO::FETCH_ASSOC)) {
            $inner_html_storage_filter .= <<<HTML
                <div class="form-check flex-fill">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-orange" type="radio" value="orange" name="radio">
                    <label class="form-check-label" for="radio-orange"><span class="text-right badge" style="background-color:#ff851b;color:#ffffff">{$data['orange']}</span>&nbsp;1&nbsp;-&nbsp;4</label>
                </div>
                <div class="form-check flex-fill">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-red" type="radio" value="red" name="radio">
                    <label class="form-check-label" for="radio-red"><span class="text-right badge" style="background-color:#dc3545;color:#ffffff">{$data['red']}</span>&nbsp;5&nbsp;-&nbsp;7</label>
                </div>
                <div class="form-check flex-fill">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-purple" type="radio" value="purple" name="radio">
                    <label class="form-check-label" for="radio-purple"><span class="text-right badge" style="background-color:#6610f2;color:#ffffff">{$data['purple']}</span>&nbsp;8&nbsp;beyond</label>
                </div>
            HTML;
        }
        $response_body['inner_html_storage_filter'] = $inner_html_storage_filter;
    }

    $inner_html = "";
    while ($data = $stmt_get_cards -> fetch(PDO::FETCH_ASSOC)) {
        if ($data['confirm_departure'] == '0') {
            $not_confirmed = "&nbsp;<p class='badge' style='color:#fff; background-color:#dc3545'><i class='fas fa-box'></i></p>";
        } else {
            $not_confirmed = "";
        }

        $date_to_show = "TBA";
        if (isset($data['ata_mnl'])) {
            $date_to_show = "ATA: " . substr($data['ata_mnl'], 0, 10);
        } elseif (isset($data['eta_mnl'])) {
            $date_to_show = "ETA: " . substr($data['eta_mnl'], 0, 10);
        }

        //$rounded = round($data['shipment_status'] ?? '0', -1, PHP_ROUND_HALF_DOWN);
        //$border_color = $colors[$rounded];
        $border_color = 'white';

        // if (isset($data['favorite']) && $data['favorite'] == '1') {
        //     $star = <<<HTML
        //         <i class="fas fa-star" style="cursor:pointer;" id="star-{$data['bl_number']}" onclick="container_favorite(this, 'unfavorite')"></i>
        //     HTML;
        // } else {
        //     $star = <<<HTML
        //         <i class="far fa-star" style="cursor:pointer;" id="star-{$data['bl_number']}" onclick="container_favorite(this, 'favorite')"></i>
        //     HTML;
        // }
        // star function removed
        // <!-- <div class="text-right">{$star}</div> -->
        if (isset($data['urgent'])) {
            $urgent = (int)$data['urgent'] <= 3 ? "<span class='badge' style='color:#fff;background-color:#dc3545;font-size:75%;'>URGENT!</span>" : "";
        } else {
            $urgent = "";
        }

        //$shipment_status = explode(", ", $data['shipment_status']);
        //$shipment_status = array_unique($shipment_status);
        //$shipment_status = implode(", ", $shipment_status);

        $inner_html .= <<<HTML
            <div class="callout" style="border-left-color:{$border_color};">
                <div class="container">
                    <div class="row">
                        <div class="container" style="font-size: 115%;">
                            <div class="text-right">{$urgent}</div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:-1em;">
                        <div class="col-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input ck-blnumber" id="{$data['bl_number']}">
                                <label class="form-check-label" for="{$data['bl_number']}"><h4 style="font-family:monospace;">{$data['bl_number']}{$not_confirmed}</h4></label>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4>{$data['forwarder_name']}</h4>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">{$data['vessel_name']}</div>
                        <div class="col-6">{$data['shipping_lines']}</div>
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