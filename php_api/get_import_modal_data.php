<?php
    require 'db_connection.php';
    $shipping_invoice = $_GET['shipping_invoice'];

    $sql = "SELECT * from import_data where shipping_invoice = :shipping_invoice";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':shipping_invoice', $shipping_invoice);
    $stmt -> execute();

    $inner_html = "";
    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $inner_html .= <<<HTML
            <form action="../php_api/update_import_data.php" method="POST" id="ImportInformation">
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>SHIPPER</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="shipper" value="">
                    </div>
                    <div class="col-3">
                        <label>PORT</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="port" value="">
                    </div>
                </div>
                <input class="form-control" style="display:none;" readonly="" type="text" name="shipping_invoice" value="FAPD-X-2024-01818_S">
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>COMMODITY QUANTITY</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="commodity_quantity" value="">
                    </div>
                    <div class="col-3">
                        <label>COMMODITY UO</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="commodity_uo" value="">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>COMMERCIAL INVOICE CURRENCY</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="commercial_invoice_currency" value="">
                    </div>
                    <div class="col-3">
                        <label>COMMERCIAL INVOICE AMOUNT</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="commercial_invoice_amount" value="">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>GROSS WEIGHT</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="gross_weight" value="">
                    </div>
                    <div class="col-3">
                        <label>INCOTERM</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="incoterm" value="">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>IP NUMBER</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="ip_number" value="">
                    </div>
                    <div class="col-3">
                        <label>DR NUMBER</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="dr_number" value="">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>RECEIVED BY</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="received_by" value="">
                    </div>
                    <div class="col-3">
                        <label>TIME RECEIVED</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="time_received" value="">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>TOTAL CUSTOM VALUE</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="total_custom_value" value="">
                    </div>
                    <div class="col-3">
                        <label>DUITABLE VALUE</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="duitable_value" value="">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>RATE</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="rate" value="">
                    </div>
                    <div class="col-3">
                        <label>CUSTOMS DUTY</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="customs_duty" value="">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>LANDED COST</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="landed_cost" value="">
                    </div>
                    <div class="col-3">
                        <label>VAT</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="vat" value="">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>BANK CHARGES</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="bank_charges" value="">
                    </div>
                    <div class="col-3">
                        <label>WHARFAGE</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="wharfage" value="">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>ARRASTRE CHARGES</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="arrastre_charges" value="">
                    </div>
                    <div class="col-3">
                        <label>ENTRY NO</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="entry_no" value="">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>OR NUMBER</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="or_number" value="">
                    </div>
                    <div class="col-3">
                        <label>ASSESSMENT DATE</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="date" name="assessment_date" value="">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-4 mx-auto">
                        <button class="btn btn-block btn-primary" type="submit">Update Import Data Details</button>
                    </div>
                </div>
            </form>
        HTML;
    }
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);