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
        if ($_SESSION['editing_privileges'] != null) {
            $edit_buttons = <<<HTML
                <div class="row">
                    <a class="text-primary ml-3 mr-3 modal-trigger" style="text-decoration:none;cursor:pointer;" id="{$data['hawb_awb']}" data-toggle="modal" data-target="#documentation_view_shipment_sea_modal" onclick="edit_container_information(this)">
                        <i class="fas fa-box"></i>&nbsp;Edit Shipment
                    </a>

                    <a class="text-primary ml-3 mr-3 modal-trigger" style="text-decoration:none;cursor:pointer;" id="{$data['hawb_awb']}" data-toggle="modal" data-target="#documentation_view_invoice_modal" onclick="edit_invoice_information(this)">
                        <i class="fas fa-file-invoice"></i>&nbsp;Edit Invoice
                    </a>
                </div>
            HTML;
        } else {
            $edit_buttons = "";
        }

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
                                <input type="checkbox" class="form-check-input ck-blnumber" id="{$data['hawb_awb']}">
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
                            {$data['shipment_status']}
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