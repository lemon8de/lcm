<?php
    require 'db_connection.php';
    $shipping_invoice = $_GET['shipping_invoice'];

    $sql = "SELECT * from import_data where shipping_invoice = :shipping_invoice";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':shipping_invoice', $shipping_invoice);
    $stmt -> execute();

    $inner_html = "";
    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        //floating point rounding
        $data['commercial_invoice_amount'] = round((float)$data['commercial_invoice_amount'], 2);
        $data['gross_weight'] = round((float)$data['gross_weight'], 2);
        $data['total_custom_value'] = round((float)$data['total_custom_value'], 2);
        $data['duitable_value'] = round((float)$data['duitable_value'], 2);
        $data['rate'] = round((float)$data['rate'], 2);
        $data['customs_duty'] = round((float)$data['customs_duty'], 2);
        $data['landed_cost'] = round((float)$data['landed_cost'], 2);
        $data['vat'] = round((float)$data['vat'], 2);
        $data['bank_charges'] = round((float)$data['bank_charges'], 2);
        $data['wharfage'] = round((float)$data['wharfage'], 2);
        $data['arrastre_charges'] = round((float)$data['arrastre_charges'], 2);

        //date snip
        //$data['assessment_date'] = $data['assessment_date'] !== null ? substr($data['assessment_date'], 0, 10) : "";
        $data['assessment_date'] = $data['assessment_date'] == null ? null : substr($data['assessment_date'], 0, 10);
        $inner_html .= <<<HTML
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>SHIPPER</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="shipper" value="{$data['shipper']}">
                    </div>
                    <div class="col-3">
                        <label>PORT</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="port" value="{$data['port']}">
                    </div>
                </div>
                <input class="form-control" style="display:none;" readonly type="text" name="shipping_invoice" value="{$data['shipping_invoice']}">
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>COMMODITY QUANTITY</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="commodity_quantity" value="{$data['commodity_quantity']}">
                    </div>
                    <div class="col-3">
                        <label>COMMODITY UO</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="commodity_uo" value="{$data['commodity_uo']}">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>COMMERCIAL INVOICE CURRENCY</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="commercial_invoice_currency" value="{$data['commercial_invoice_currency']}">
                    </div>
                    <div class="col-3">
                        <label>COMMERCIAL INVOICE AMOUNT</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="commercial_invoice_amount" value="{$data['commercial_invoice_amount']}">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>GROSS WEIGHT</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="gross_weight" value="{$data['gross_weight']}">
                    </div>
                    <div class="col-3">
                        <label>INCOTERM</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="incoterm" value="{$data['incoterm']}">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>IP NUMBER</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="ip_number" value="{$data['ip_number']}">
                    </div>
                    <div class="col-3">
                        <label>DR NUMBER</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="dr_number" value="{$data['dr_number']}">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>RECEIVED BY</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="received_by" value="{$data['received_by']}">
                    </div>
                    <div class="col-3">
                        <label>TIME RECEIVED</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="time_received" value="{$data['time_received']}">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>TOTAL CUSTOM VALUE</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="total_custom_value" value="{$data['total_custom_value']}">
                    </div>
                    <div class="col-3">
                        <label>DUITABLE VALUE</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="duitable_value" value="{$data['duitable_value']}">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>RATE</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="rate" value="{$data['rate']}">
                    </div>
                    <div class="col-3">
                        <label>CUSTOMS DUTY</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="customs_duty" value="{$data['customs_duty']}">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>LANDED COST</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="landed_cost" value="{$data['landed_cost']}">
                    </div>
                    <div class="col-3">
                        <label>VAT</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="vat" value="{$data['vat']}">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>BANK CHARGES</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="bank_charges" value="{$data['bank_charges']}">
                    </div>
                    <div class="col-3">
                        <label>WHARFAGE</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="wharfage" value="{$data['wharfage']}">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>ARRASTRE CHARGES</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="number" step="0.01" name="arrastre_charges" value="{$data['arrastre_charges']}">
                    </div>
                    <div class="col-3">
                        <label>ENTRY NO</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="entry_no" value="{$data['entry_no']}">
                    </div>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-3">
                        <label>OR NUMBER</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="text" name="or_number" value="{$data['or_number']}">
                    </div>
                    <div class="col-3">
                        <label>ASSESSMENT DATE</label>
                    </div>
                    <div class="col-3">
                        <input class="form-control" type="date" name="assessment_date" value="{$data['assessment_date']}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-4 mx-auto">
                        <button class="btn btn-block btn-primary" type="submit">Update Import Data Details</button>
                    </div>
                </div>
        HTML;
    }
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);