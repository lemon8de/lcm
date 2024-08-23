<table id="" class="table table-head-fixed text-nowrap table-hover mb-4">
    <thead>
        <tr style="border-bottom:1px solid black">
            <th>SHIPPER</th>
            <th>ORIGIN</th>
            <th>PORT</th>
            <th>SHIPPING INVOICE</th>
            <th>COMMODITY</th>
            <th>CLASSIFICATION</th>
            <th>COMMODITY QUANTITY</th>
            <th>COMMODITY U/O</th>
            <th>COMMERCIAL INVOICE CURRENCY</th>
            <th>COMMERCIAL INVOICE AMOUNT</th>
            <th>INCOTERM</th>
            <th>GROSS WEIGHT</th>
            <th>FORWARDER'S NAME</th>
            <th>BL NUMBER</th>
            <th>VESSEL NAME</th>
            <th>ETA MNL (YYYY/MM/DD)</th>
            <th>ATA MNL (YYYY/MM/DD)</th>
            <th>ATB (YYYY/MM/DD)</th>
            <th>CONTAINER</th>
            <th>CONTAINER SIZE / CBM</th>
            <th>SHIPPING LINES</th>
            <th>SHIPMENT STATUS</th>
            <th>REQUIRED DELIVERY SCHEDULE</th>
            <th>DELIVERY PLAN</th>
            <th>TABS</th>
            <th>DATE PORT OUT</th>
            <th>ACTUAL RECEIVED AT FALP</th>
            <th>POLYTAINER SIZE</th>
            <th>POLYTAINER QUANTITY</th>
            <th>ETD</th>
            <th>CONTAINER STATUS</th>
            <th>DATE OF RETURN / REUSED</th>
            <th>NO OF DAYS AT PORT</th>
            <th>NO OF DAYS AT FALP</th>
            <th>TYPE OF EXPENSE</th>
            <th>ETD</th>
            <th>IP NUMBER</th>
            <th>DR NUMBER</th>
            <th>RECEIVED BY</th>
            <th>TIME RECEIVED</th>
            <th>TOTAL CUSTOM VALUE</th>
            <th>DUTIABLE VALUE</th>
            <th>RATE</th>
            <th>CUSTOMS DUTY</th>
            <th>LANDED COST</th>
            <th>VAT</th>
            <th>BANK CHARGES</th>
            <th>WHARFAGE</th>
            <th>ARRASTRE CHARGES</th>
            <th>ENTRY NO (CUSTOM REFERENCE NUMBER)</th>
            <th>OR NUMBER (REFERENCE NO)</th>
            <th>ASSESSMENT DATE</th>
        </tr>
    </thead>
    <tbody id="">
        <?php
            $sql = "SELECT a.*, b.*, c.*, d.*, e.*, f.*, g.*, f.etd as etd_1, a.etd as etd_2, a.incoterm as import_incoterm from import_data as a left join m_shipment_sea_details as b on a.shipment_details_ref = b.shipment_details_ref left join m_vessel_details as c on a.shipment_details_ref = c.shipment_details_ref left join m_delivery_plan as d on a.shipment_details_ref = d.shipment_details_ref left join m_completion_details as e on a.shipment_details_ref = e.shipment_details_ref left join m_polytainer_details as f on a.shipment_details_ref = f.shipment_details_ref left join m_mmsystem as g on a.shipment_details_ref = g.shipment_details_ref";
            $stmt = $conn -> prepare($sql);
            $stmt -> execute();

            while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                echo <<<HTML
                    <tr>
                        <td>{$row['shipper']}</td>
                        <td>{$row['origin_port']}</td>
                        <td>{$row['port']}</td>
                        <td>{$row['shipping_invoice']}</td>
                        <td>{$row['commodity']}</td>
                        <td>{$row['classification']}</td>
                        <td>{$row['commodity_quantity']}</td>
                        <td>{$row['commodity_uo']}</td>
                        <td>{$row['commercial_invoice_currency']}</td>
                        <td>{$row['commercial_invoice_amount']}</td>
                        <td>{$row['import_incoterm']}</td>
                        <td>{$row['gross_weight']}</td>
                        <td>{$row['forwarder_name']}</td>
                        <td>{$row['bl_number']}</td>
                        <td>{$row['vessel_name']}</td>
                        <td>{$row['eta_mnl']}</td>
                        <td>{$row['ata_mnl']}</td>
                        <td>{$row['atb']}</td>
                        <td>{$row['container']}</td>
                        <td>{$row['container_size']}</td>
                        <td>{$row['shipping_lines']}</td>
                        <td>{$row['shipment_status']}</td>
                        <td>{$row['required_delivery_sched']}</td>
                        <td>{$row['deliver_plan']}</td>
                        <td>{$row['tabs']}</td>
                        <td>{$row['date_port_out']}</td>
                        <td>{$row['actual_received_at_falp']}</td>
                        <td>{$row['polytainer_size']}</td>
                        <td>{$row['polytainer_quantity']}</td>
                        <td>{$row['etd_1']}</td>
                        <td>{$row['container_status']}</td>
                        <td>{$row['date_return_reused']}</td>
                        <td>{$row['no_days_port']}</td>
                        <td>{$row['no_days_falp']}</td>
                        <td>{$row['type_of_expense']}</td>
                        <td>{$row['etd_2']}</td>
                        <td>{$row['ip_number']}</td>
                        <td>{$row['dr_number']}</td>
                        <td>{$row['received_by']}</td>
                        <td>{$row['time_received']}</td>
                        <td>{$row['total_custom_value']}</td>
                        <td>{$row['duitable_value']}</td>
                        <td>{$row['rate']}</td>
                        <td>{$row['customs_duty']}</td>
                        <td>{$row['landed_cost']}</td>
                        <td>{$row['vat']}</td>
                        <td>{$row['bank_charges']}</td>
                        <td>{$row['wharfage']}</td>
                        <td>{$row['arrastre_charges']}</td>
                        <td>{$row['entry_no']}</td>
                        <td>{$row['or_number']}</td>
                        <td>{$row['assessment_date']}</td>
                    </tr>
                HTML;
            }
        ?>
    </tbody>
</table>