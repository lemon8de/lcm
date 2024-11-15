<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $hawb_awb = $_GET['hawb_awb'];
    $month = $_GET['month'];
    $year = $_GET['year'];
    $refresh_filters = $_GET['refresh_filters'];
    $shipment_status_progress = $_GET['shipment_status_progress'];
    $storage = $_GET['storage'];

    $response_body = [];
    $hawb_awb = "%" .  $hawb_awb . "%";

    $sql = "EXEC SearchHAWBCardsThisMonth :HAWB, :StartYear, :StartMonth, :ShipmentStatusProgress, :Storage";
    $stmt_get_cards = $conn -> prepare($sql);
    $stmt_get_cards -> bindParam(":HAWB", $hawb_awb);
    $stmt_get_cards -> bindParam(":StartYear", $year);
    $stmt_get_cards -> bindParam(":StartMonth", $month);
    $stmt_get_cards -> bindParam(":ShipmentStatusProgress", $shipment_status_progress);
    $stmt_get_cards -> bindParam(":Storage", $storage);
    $stmt_get_cards -> execute();

    if ($refresh_filters == 'true') {
        //need to build the filters
        $sql = "EXEC SearchAirProgressFilter :HAWB, :StartYear, :StartMonth";
        $stmt = $conn -> prepare($sql);
        $stmt -> bindParam(":HAWB", $hawb_awb);
        $stmt -> bindParam(":StartYear", $year);
        $stmt -> bindParam(":StartMonth", $month);
        $stmt -> execute();

        $shipment_progress_color = [
            "ACTIVE" => "#2962ff",
            "FOR RELEASE" => "#f34c41",
            "DELIVERED" => "#45bf55",
        ];

        if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            $inner_html_progress = <<<HTML
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-active" type="radio" value="ACTIVE" name="radio">
                    <label class="form-check-label" for="radio-active"><span class="text-right badge" style="background-color:{$shipment_progress_color['ACTIVE']};color:#ffffff">{$data['ACTIVE']}</span>&nbsp;ACTIVE</label>
                </div>
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-forrelease" type="radio" value="FOR RELEASE" name="radio">
                    <label class="form-check-label" for="radio-forrelease"><span class="text-right badge" style="background-color:{$shipment_progress_color['FOR RELEASE']};color:#ffffff">{$data['FOR_RELEASE']}</span>&nbsp;FOR RELEASE</label>
                </div>
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-delivered" type="radio" value="DELIVERED" name="radio">
                    <label class="form-check-label" for="radio-delivered"><span class="text-right badge" style="background-color:{$shipment_progress_color['DELIVERED']};color:#ffffff">{$data['DELIVERED']}</span>&nbsp;DELIVERED</label>
                </div>
            HTML;
        }
        $response_body['inner_html_progress'] = $inner_html_progress;

        $sql = "EXEC SearchStorageAirShipment :HAWB, :StartYear, :StartMonth";
        $stmt = $conn -> prepare($sql);
        $stmt -> bindParam(":HAWB", $hawb_awb);
        $stmt -> bindParam(":StartYear", $year);
        $stmt -> bindParam(":StartMonth", $month);
        $stmt -> execute();

        $storage_color = [
            "orange" => "#ff851b",
            "red" => "#dc3545",
            "purple" => "#6610f2",
        ];

        if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            $inner_html_storage = <<<HTML
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-orange" type="radio" value="orange" name="radio">
                    <label class="form-check-label" for="radio-orange"><span class="text-right badge" style="background-color:#ff851b;color:#ffffff">{$data['orange']}</span>&nbsp;0&nbsp;-&nbsp;2</label>
                </div>
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-red" type="radio" value="red" name="radio">
                    <label class="form-check-label" for="radio-red"><span class="text-right badge" style="background-color:#dc3545;color:#ffffff">{$data['red']}</span>&nbsp;3&nbsp;-&nbsp;5</label>
                </div>
                <div class="form-check">
                    <input onchange="search_documentation(false)" class="form-check-input" id="radio-purple" type="radio" value="purple" name="radio">
                    <label class="form-check-label" for="radio-purple"><span class="text-right badge" style="background-color:#6610f2;color:#ffffff">{$data['purple']}</span>&nbsp;6&nbsp;beyond</label>
                </div>
            HTML;
        }
        $response_body['inner_html_storage'] = $inner_html_storage;
    } 
    $inner_html = "";
    while ($data = $stmt_get_cards -> fetch(PDO::FETCH_ASSOC)) {

        //urgent badge
        if (isset($data['urgent'])) {
            $urgent = (int)$data['urgent'] <= 3 ? "<span class='badge' style='color:#fff;background-color:#dc3545;font-size:75%;'>URGENT!</span>" : "";
        } else {
            $urgent = "";
        }

        //editing privileges
        if ($_SESSION['editing_privileges'] == "INCOMING" || $_SESSION['editing_privileges'] == "ALL") {
            $edit_buttons = <<<HTML
                <div class="row">
                    <a class="text-primary ml-3 mr-3 modal-trigger" style="text-decoration:none;cursor:pointer;" id="{$data['hawb_awb']}" data-toggle="modal" data-target="#documentation_view_shipment_air_modal" onclick="edit_shipment_information(this)">
                        <i class="fas fa-box"></i>&nbsp;Edit Shipment
                    </a>

                    <a class="text-primary ml-3 mr-3 modal-trigger" style="text-decoration:none;cursor:pointer;" id="{$data['shipment_details_ref']}" data-nameinvoice="{$data['hawb_awb']}" data-toggle="modal" data-target="#documentation_view_invoice_modal_air" onclick="edit_invoice_information(this)">
                        <i class="fas fa-file-invoice"></i>&nbsp;Edit Invoice
                    </a>
                </div>
            HTML;
        } else {
            $edit_buttons = "";
        }

        //swapping shipment status or required delivery
        $status_or_date = isset($data['required_delivery']) ? "Required Delivery: " . $data['required_delivery'] : $data['shipment_status'];

        //main card
        $inner_html .= <<<HTML
            <div class="callout" style="border-left-color:white;">
                <div class="container">
                    <div class="row">
                        <div class="container" style="font-size: 115%;">
                            <div class="text-right">{$urgent}</div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:-1em;">
                        <div class="col-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input ck-blnumber" id="{$data['hawb_awb']}" style="visibility:hidden;">
                                <label class="form-check-label" for="{$data['hawb_awb']}"><h4 style="font-family:monospace;">{$data['hawb_awb']}</h4></label>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4>{$data['forwarder']}</h4>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">{$data['origin']}</div>
                        <div class="col-6">{$data['invoice_no']}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            ETA: {$data['eta']}
                        </div>
                        <div class="col-6">
                            {$data['commodity']}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            {$status_or_date}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <a class="collapsed text-primary" style="text-decoration:none;" id="{$data['hawb_awb']}" data-toggle="collapse" href="#viewmore-{$data['hawb_awb']}" onclick="load_shipment(this)">
                                <i class="fas fa-search"></i>&nbsp;View
                            </a>
                        </div>
                    </div>
                </div>
                <div class="container collapse mt-2" id="viewmore-{$data['hawb_awb']}">
                    <div style="height: 2px; background-color: #5a6268; margin: 10px 0;"></div>
                    <div class="row mb-2" style="padding-left:6vw;padding-right:6vw" id="breakdown-{$data['hawb_awb']}">
                        <div class="container">
                            <div class="row">
                                <div class="col-5">NO OF PACKAGES</div>
                                <div class="col-1">:</div>
                                <div class="col-6"></div> <!-- Placeholder for data -->
                            </div>
                            <div class="row">
                                <div class="col-5">GROSS WEIGHT</div>
                                <div class="col-1">:</div>
                                <div class="col-6"></div> <!-- Placeholder for data -->
                            </div>
                            <div class="row">
                                <div class="col-5">CHARGEABLE WEIGHT</div>
                                <div class="col-1">:</div>
                                <div class="col-6"></div> <!-- Placeholder for data -->
                            </div>
                            <div class="row">
                                <div class="col-5">TENTATIVE DELIVERY</div>
                                <div class="col-1">:</div>
                                <div class="col-6"></div> <!-- Placeholder for data -->
                            </div>
                            <div class="row">
                                <div class="col-5">REQUIRED DELIVERY</div>
                                <div class="col-1">:</div>
                                <div class="col-6"></div> <!-- Placeholder for data -->
                            </div>
                            <div class="row">
                                <div class="col-5">ACTUAL DELIVERY AT FALP</div>
                                <div class="col-1">:</div>
                                <div class="col-6"></div> <!-- Placeholder for data -->
                            </div>
                        </div>
                    </div>
                    {$edit_buttons}
                </div>
            </div>
        HTML;
    }
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);