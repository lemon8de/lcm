<form action="../php_api/add_shipment_sea_details.php" method="POST">
    <div class="row mb-1">
        <h3>Shipment Details</h3>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>BL NUMBER</label>
            <input type="text" class="form-control" name="bl_number" required>
        </div>
        <div class="col-3">
            <label>CONTAINER</label>
            <input type="text" class="form-control" name="container" required>
        </div>
        <div class="col-3">
            <label>CONTAINER SIZE / CBM</label>
            <input type="text" class="form-control" name="container_size" required>
        </div>
        <div class="col-3">
            <label>COMMERCIAL INVOICE</label>
            <input type="text" class="form-control" name="commercial_invoice" required>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>COMMODITY</label>
            <select name="commodity" class="form-control" required>
                <option value="" disabled selected>Select Commodity</option>
                <?php 
                    $sql = "SELECT method, value, display_name from list_commodity where method = 'sea' order by display_name asc";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo <<<HTML
                            <option value="{$row['value']}">{$row['display_name']}</option>
                        HTML;
                    }
                ?>
            </select>
        </div>
        <div class="col-3">
            <label>SHIPPING LINES</label>
            <input type="text" class="form-control" name="shipping_lines" required>
        </div>
        <div class="col-3">
            <label>FORWARDER'NAME</label>
            <input type="text" class="form-control" name="forwarder_name" required>
        </div>
        <div class="col-3">
            <label>ORIGIN</label>
            <input type="text" class="form-control" name="origin_port" required>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>SHIPMENT STATUS</label>
            <input type="text" class="form-control" name="shipment_status" required>
        </div>
    </div>
    <div class="row mb-1">
        <h3>Vessel Details</h3>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>VESSEL NAME</label>
            <input type="text" class="form-control" name="vessel_name" required>
        </div>
        <div class="col-3">
            <label>ETA MNL (YYY/MM/DD)</label>
            <input type="date" class="form-control" name="eta_mnl">
        </div>
        <div class="col-3">
            <label>ATA MNL (YYYY/MM/DD)</label>
            <input type="date" class="form-control" name="ata_mnl">
        </div>
        <div class="col-3">
            <label>ATB(YYYY/MM/DD)</label>
            <input type="date" class="form-control" name="atb">
        </div>
    </div>
    <div class="row">
        <div class="col-4 mx-auto mt-3">
            <button type="submit" class="btn bg-primary btn-block">Add</button>
        </div>
    </div>
</form>