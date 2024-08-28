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
            <div class="row mb-3">
                <div class="col-3">
                    <button class="btn btn-warning btn-block btn-file" onclick="fileexplorer()">
                        <form id="file_form" enctype="multipart/form-data" action="../php_api/import_sea_shipment.php" method="POST">
                            <span><i class="fas fa-upload mr-2"></i>Import Forwarder's File</span><input type="file" id="import_sea" name="import_sea_shipment_file" onchange="submit()" accept=".csv" style="opacity:0; display:none;">
                        </form>
                    </button>
                </div>
                <div class="col-3">
                    <a href="../php_api/download_forwarder_sea_template.php">
                        <button class="btn btn-info btn-block btn-file">
                            <span><i class="fas fa-download mr-2"></i> Download Template </span>
                        </button>
                    </a>
                </div>
            </div>
            <?php include '../forms/add_shipment_sea_details_form.php';?>
        </div>
    </div>
</div>

<div class="card card-gray-dark card-outline">
    <div class="card-header collapsed">
        <h3 class="card-title">Shipment Documentation</h3>
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
            <!-- <select id="shipment_details_ref_select" class="form-control" required onchange="loaddata.call(this)">
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
            </select> -->
            <div class="col-5 mx-auto">
                <input class="form-control" id="ContainerInput" placeholder="Search By Container" onkeyup="debounce(searchContainer, 150)">
            </div>
        </div>
        <div class="row">
            <div class="col-6 mx-auto">
                <table class="table table-head-fixed text-nowrap table-hover mb-4">
                    <thead>
                        <tr>
                            <th>BL NUMBER</th>
                            <th>CONTAINER</th>
                        </tr>
                    </thead>
                    <tbody id="ContainerSearch">
                    </tbody>
                </table>
            </div>
        </div>
        <?php include '../php_static/content_tables/shipment_details.php';?>
        <?php include '../php_static/content_tables/vessel_details.php';?>

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

    function debounce(method, delay) {
        clearTimeout(method._tId);
        method._tId = setTimeout(function() {
            method();
        }, delay);
    }

    function searchContainer() {
        console.log('started');

        console.log(document.getElementById('ContainerInput').value);
        $.ajax({
            url: '../php_api/search_container.php',
            type: 'GET',
            data: {
                'container' : document.getElementById('ContainerInput').value,
            },
            dataType: 'json',
            success: function (response) {
                document.getElementById('ContainerSearch').innerHTML = response.inner_html;
            }
        });
        console.log('finished');
    }

    function fileexplorer() {
        document.getElementById("import_sea").click();
    }

    //function loaddata() {
    function loaddata(row) {
        //console.log(this.value);
        var value = row.getAttribute('data-value');
        var shipment_details_ref = value;

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
                    document.getElementById('CompletionContent').innerHTML = "<tr><td colspan='3' class='text-muted text-center'>NO DATA</td></tr>";
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