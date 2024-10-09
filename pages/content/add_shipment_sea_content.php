<!-- <div class="card card-gray-dark card-outline">
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
</div> -->
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
        <!-- peza button moved here 9 oct  -->
        <div class="col-3">
            <button class="btn btn-warning btn-block btn-file" onclick="fileexplorer_peza()">
                <form id="file_form" enctype="multipart/form-data" action="../php_api/import_sea_peza_array-invoices.php" method="POST">
                    <span><i class="fas fa-upload mr-2"></i>PEZA Import</span><input type="file" id="peza_import_sea" name="import_sea_peza_file" onchange="submit()" accept=".csv" style="opacity:0; display:none;">
                </form>
            </button>
        </div>
    </div>
    <?php include '../forms/add_shipment_sea_details_form.php';?>
</div>
<script>
    function fileexplorer() {
        document.getElementById("import_sea").click();
    }

    function fileexplorer_peza() {
        document.getElementById("peza_import_sea").click();
    }
</script>