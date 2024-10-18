<?php
    require 'db_connection.php';
    $hawb_awb = $_GET['hawb_awb'];

    $sql = "SELECT no_packages, round(gross_weight, 2) as gross_weight, round(chargeable_weight, 2) as chargeable_weight, format(tentative_delivery_schedule, 'yyyy-MM-dd') as tentative_delivery_schedule, format(required_delivery, 'yyyy-MM-dd') as required_delivery, format(actual_date_of_delivery, 'yyyy-MM-dd') as actual_date_of_delivery from t_shipment_air_details as a left join t_air_delivery_details as b on a.shipment_details_ref = b.shipment_details_ref where hawb_awb = :hawb_awb";

    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":hawb_awb", $hawb_awb);
    $stmt -> execute();

    $inner_html = "";
    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $inner_html .= <<<HTML
            <div class="container">
                <div class="row">
                    <div class="col-5">NO OF PACKAGES</div>
                    <div class="col-1">:</div>
                    <div class="col-6">{$data['no_packages']}</div> <!-- Placeholder for data -->
                </div>
                <div class="row">
                    <div class="col-5">GROSS WEIGHT</div>
                    <div class="col-1">:</div>
                    <div class="col-6">{$data['gross_weight']}</div> <!-- Placeholder for data -->
                </div>
                <div class="row">
                    <div class="col-5">CHARGEABLE WEIGHT</div>
                    <div class="col-1">:</div>
                    <div class="col-6">{$data['chargeable_weight']}</div> <!-- Placeholder for data -->
                </div>
                <div class="row">
                    <div class="col-5">TENTATIVE DELIVERY</div>
                    <div class="col-1">:</div>
                    <div class="col-6">{$data['tentative_delivery_schedule']}</div> <!-- Placeholder for data -->
                </div>
                <div class="row">
                    <div class="col-5">REQUIRED DELIVERY</div>
                    <div class="col-1">:</div>
                    <div class="col-6">{$data['required_delivery']}</div> <!-- Placeholder for data -->
                </div>
                <div class="row">
                    <div class="col-5">ACTUAL DELIVERY AT FALP</div>
                    <div class="col-1">:</div>
                    <div class="col-6">{$data['actual_date_of_delivery']}</div> <!-- Placeholder for data -->
                </div>
            </div>
        HTML;
    }
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);