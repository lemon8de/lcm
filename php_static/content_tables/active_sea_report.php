<table id="" class="table table-head-fixed text-nowrap table-hover mb-4">
    <thead>
        <tr style="border-bottom:1px solid black">
            <th>FORWARDER'S NAME</th>
            <th>VESSEL NAME</th>
            <th>ETA MNL</th>
            <th>ATA MNL</th>
            <th>ATB</th>
            <th>BL NUMBER</th>
            <th>CONTAINER</th>
            <th>COMMERCIAL INVOICE</th>
            <th>COMMODITY</th>
            <th>REQUIRED DELIVERY SCHEDULE</th>
            <th>DELIVERY PLAN</th>
            <th>TABS</th>
            <th>SHIPMENT STATUS</th>
            <th>ORIGIN</th>
            <th>NO OF DAYS AT PORT</th>
            <th>TYPE OF EXPENSE</th>
        </tr>
    </thead>
    <tbody id="ActiveReportContent">
        <?php
            //$sql = "SELECT a.forwarder_name, b.vessel_name, b.eta_mnl, b.ata_mnl, b.atb, a.bl_number, a.container, a.commercial_invoice, a.commodity, c.required_delivery_sched, c.deliver_plan, c.tabs, a.shipment_status, a.origin_port, d.no_days_port, a.type_of_expense from m_shipment_sea_details as a left join m_vessel_details as b on a.shipment_details_ref = b.shipment_details_ref left join m_delivery_plan as c on a.shipment_details_ref = c.shipment_details_ref left join m_mmsystem as d on a.shipment_details_ref = d.shipment_details_ref left join m_completion_details as e on a.shipment_details_ref = e.shipment_details_ref where e.actual_received_at_falp is null";
            $sql = "SELECT * from active_report";
            $stmt = $conn -> prepare($sql);
            $stmt -> execute();

            while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                $row['commercial_invoice'] =$row['commercial_invoice'] == null ? null : str_replace(" ", "<br>", $row['commercial_invoice']);

                $row['eta_mnl'] = $row['eta_mnl'] == null ? null : substr($row['eta_mnl'], 0, 10);
                $row['ata_mnl'] = $row['ata_mnl'] == null ? null : substr($row['ata_mnl'], 0, 10);
                $row['atb'] = $row['atb'] == null ? null : substr($row['atb'], 0, 10);
                $row['required_delivery_sched'] = $row['required_delivery_sched'] == null ? null : substr($row['required_delivery_sched'], 0, 10);
                $row['deliver_plan'] = $row['deliver_plan'] == null ? null : substr($row['deliver_plan'], 0, 10);

                echo <<<HTML
                <tr style="border-bottom:1px solid black">
                    <td>{$row['forwarder_name']}</td>
                    <td>{$row['vessel_name']}</td>
                    <td>{$row['eta_mnl']}</td>
                    <td>{$row['ata_mnl']}</td>
                    <td>{$row['atb']}</td>
                    <td>{$row['bl_number']}</td>
                    <td>{$row['container']}</td>
                    <td>{$row['commercial_invoice']}</td>
                    <td>{$row['commodity']}</td>
                    <td>{$row['required_delivery_sched']}</td>
                    <td>{$row['deliver_plan']}</td>
                    <td>{$row['tabs']}</td>
                    <td>{$row['shipment_status']}</td>
                    <td>{$row['origin_port']}</td>
                    <td>{$row['no_days_port']}</td>
                    <td>{$row['type_of_expense']}</td>
                </tr>
            HTML;
            }
        ?>
    </tbody>
</table>