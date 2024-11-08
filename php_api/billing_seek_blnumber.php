<?php
    require 'db_connection.php';
    require 'billing_monitoring_computation/import_sea.php';
    require 'billing_monitoring_computation/import_air.php';
    require 'billing_monitoring_computation/export_sea.php';
    require 'billing_monitoring_computation/export_air.php';
    $bl_numbers = $_GET['bl_numbers'];

    //figure out if these bl numbers is import air, import sea, export sea, export air
    //precheck only if it clears this algo all of them have the same type of transaction, we proceed
    $sql_import_sea = "SELECT shipment_details_ref from m_shipment_sea_details where bl_number = :bl_number";
    $sql_import_air = "SELECT shipment_details_ref from t_shipment_air_details where hawb_awb = :bl_number";
    $sql_export_air = "SELECT a.outgoing_details_ref from m_outgoing_fsib as a left join m_outgoing_vessel_details as b on a.outgoing_details_ref = b.outgoing_details_ref left join m_outgoing_bl_details as c on a.outgoing_details_ref = c.outgoing_details_ref where bl_number = :bl_number and mode_of_shipment = 'AIR'";
    $sql_export_sea = "SELECT a.outgoing_details_ref from m_outgoing_fsib as a left join m_outgoing_vessel_details as b on a.outgoing_details_ref = b.outgoing_details_ref left join m_outgoing_bl_details as c on a.outgoing_details_ref = c.outgoing_details_ref where bl_number = :bl_number and mode_of_shipment = 'SEA'";

    $stmt_is = $conn -> prepare($sql_import_sea);
    $stmt_ia = $conn -> prepare($sql_import_air);
    $stmt_es = $conn -> prepare($sql_export_sea);
    $stmt_ea = $conn -> prepare($sql_export_air);

    $import_sea = [];
    $import_air = [];
    $export_sea = [];
    $export_air = [];
    foreach ($bl_numbers as $bl_number) {
        $bl_number = trim($bl_number);
        $stmt_is -> bindParam(":bl_number", $bl_number);
        $stmt_is -> execute();
        if ($data = $stmt_is -> fetch(PDO::FETCH_ASSOC)) {
            array_push($import_sea, $data['shipment_details_ref']);
            continue;
        }
        $stmt_ia -> bindParam(":bl_number", $bl_number);
        $stmt_ia -> execute();
        if ($data = $stmt_ia -> fetch(PDO::FETCH_ASSOC)) {
            array_push($import_air, $data['shipment_details_ref']);
            continue;
        }
        $stmt_es -> bindParam(":bl_number", $bl_number);
        $stmt_es -> execute();
        if ($data = $stmt_es -> fetch(PDO::FETCH_ASSOC)) {
            array_push($export_sea, $data['outgoing_details_ref']);
            continue;
        }
        $stmt_ea -> bindParam(":bl_number", $bl_number);
        $stmt_ea -> execute();
        if ($data = $stmt_ea -> fetch(PDO::FETCH_ASSOC)) {
            array_push($export_air, $data['outgoing_details_ref']);
            continue;
        }
    }
    //all bl numbers are now split into their type of transactions
    $response_body = [];
    $inner_html = "";
    if (!empty($import_sea)) {
        $mega = import_sea_compute($conn, $import_sea);
        $render_data = import_sea_table_render($conn, $mega);

        $inner_html .= <<<HTML
            <div class="card card-gray-dark card-outline collapsed collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">IMPORT SEA</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container table-responsive">
                        <table class="table table-hover mb-4 text-nowrap">
                        <thead>
                            <tr>
                                <th>DETAIL OF CHARGE</th>
                                {$render_data[1]}
                            </tr>
                            <tr>
                                {$render_data[0]}
                            </tr>
                        </thead>
                        <tbody>
                            {$render_data[2]}
                        </tbody>
                    </table>
                </div>
            </div>
        HTML;
        $response_body['import_sea'] = $inner_html;
    }

    $inner_html = "";
    if (!empty($import_air)) {
        $mega = import_air_compute($conn, $import_air);
        $render_data = import_air_table_render($conn, $mega);

        $inner_html .= <<<HTML
            <div class="card card-gray-dark card-outline collapsed collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">IMPORT AIR (disabled)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                <div class="container table-responsive">
                    <table class="table table-hover mb-4 text-nowrap">
                        <thead>
                            <tr>
                                <th>DETAIL OF CHARGE</th>
                                {$render_data[0]}
                            </tr>
                        </thead>
                        <tbody>
                            {$render_data[1]}
                        </tbody>
                    </table>
                </div>
            </div>
        HTML;
        $response_body['import_air'] = $inner_html;
    }

    $inner_html = "";
    if (!empty($export_sea)) {
        $mega = export_sea_compute($conn, $export_sea);
        $render_data = export_sea_table_render($conn, $mega);

        $inner_html .= <<<HTML
            <div class="card card-gray-dark card-outline collapsed collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">EXPORT SEA (disabled)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                <div class="container table-responsive">
                    <table class="table table-hover mb-4 text-nowrap">
                        <thead>
                            <tr>
                                <th>DETAIL OF CHARGE</th>
                                {$render_data[0]}
                            </tr>
                        </thead>
                        <tbody>
                            {$render_data[1]}
                        </tbody>
                    </table>
                </div>
            </div>
        HTML;
        $response_body['export_sea'] = $inner_html;
    }
    $inner_html = "";
    if (!empty($export_air)) {
        $mega = export_air_compute($conn, $export_air);
        $render_data = export_air_table_render($conn, $mega);

        $inner_html .= <<<HTML
            <div class="card card-gray-dark card-outline collapsed collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">EXPORT AIR (disabled)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container table-responsive">
                        <table class="table table-hover mb-4 text-nowrap">
                        <thead>
                            <tr>
                                <th>DETAIL OF CHARGE</th>
                                {$render_data[0]}
                            </tr>
                        </thead>
                        <tbody>
                            {$render_data[1]}
                        </tbody>
                    </table>
                </div>
            </div>
        HTML;
        $response_body['export_air'] = $inner_html;
    }
    echo json_encode($response_body);