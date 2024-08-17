<div class="card card-gray-dark card-outline">
    <div class="card-header">
        <h3 class="card-title">Import Data + Update from Forwarder's File</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="container">
            <div class="row">
            </div>
            <?php include '../forms/add_shipment_sea_details_form.php';?>
        </div>
    </div>
</div>

<div class="card card-gray-dark card-outline">
    <div class="card-header collapsed">
        <h3 class="card-title">Confirm Shipment</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>
    <div class="card-body table-responsive">
        <!-- <div class="container"> -->
            <?php include '../php_static/content_tables/incoming_sea_confirm.php';?>
        <!-- </div> -->
    </div>
</div>

<div class="card card-gray-dark card-outline">
    <div class="card-header collapsed">
        <h3 class="card-title">View Data from Forwarder's File</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>
    <div class="card-body table-responsive">
        <!-- <div class="container"> -->
            <?php include '../php_static/content_tables/incoming_sea_view.php';?>
        <!-- </div> -->
    </div>
</div>

<div class="card card-gray-dark card-outline">
    <div class="card-header collapsed">
        <h3 class="card-title">View + Edit Rest of Data</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>
    <div class="card-body table-responsive">
        <!-- <div class="container"> -->
        <div class="row mb-4">
            <select id="shipment_details_ref_select" class="form-control" required onchange="loaddata.call(this)">
                <option value="" disabled selected>Select BL  |  CONTAINER</option>
                <?php 
                    $sql = "SELECT shipment_details_ref, bl_number, container from m_shipment_sea_details";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo <<<HTML
                            <option value="{$row['shipment_details_ref']}">{$row['bl_number']}  |  {$row['container']}</option>
                        HTML;
                    }
                ?>
            </select>
        </div>
        <?php include '../php_static/content_tables/delivery_plan.php';?>
        <?php include '../php_static/content_tables/completion_details.php';?>
        <?php include '../php_static/content_tables/polytainer_details.php';?>
        <?php include '../php_static/content_tables/mm_system.php';?>
        <div style="max-height: 300px; overflow-y: auto;">
            <?php include '../php_static/content_tables/history.php';?>
        </div>
    </div>
</div>

<script>
    function loaddata() {
        console.log(this.value);
        var shipment_details_ref = this.value;

        $.ajax({
            url: '../php_api/detailsdump.php',
            type: 'GET',
            data: {
                'shipment_details_ref' : shipment_details_ref,
            },
            dataType: 'json',
            success: function (response) {
                if (response.delivery_plan) {
                    document.getElementById('DeliveryPlanContent').innerHTML = response.delivery_plan;
                    console.log(response)
                } else {
                    document.getElementById('DeliveryPlanContent').innerHTML = "<tr><td colspan='4' class='text-muted text-center'>NO DATA</td></tr>";
                }
                if (response.completion_details) {
                    document.getElementById('CompletionContent').innerHTML = response.completion_details;
                    console.log(response)
                } else {
                    document.getElementById('CompletionContent').innerHTML = "<tr><td colspan='2' class='text-muted text-center'>NO DATA</td></tr>";
                }
                if (response.polytainer_details) {
                    document.getElementById('PolytainerDetailsContent').innerHTML = response.polytainer_details;
                    console.log(response)
                } else {
                    document.getElementById('PolytainerDetailsContent').innerHTML = "<tr><td colspan='4' class='text-muted text-center'>NO DATA</td></tr>";
                }
                if (response.mmsystem_details) {
                    document.getElementById('MMDetailsContent').innerHTML = response.mmsystem_details;
                    console.log(response)
                } else {
                    document.getElementById('MMDetailsContent').innerHTML = "<tr><td colspan='5' class='text-muted text-center'>NO DATA</td></tr>";
                }
                if (response.history) {
                    document.getElementById('HistoryContent').innerHTML = response.history;
                    console.log(response)
                } else {
                    document.getElementById('HistoryContent').innerHTML = "<tr><td colspan='4' class='text-muted text-center'>NO DATA</td></tr>";
                }
            }
        });
    }
</script>