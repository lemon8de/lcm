<div class="container">
    <div class="row mb-3">
        <div class="col-3">
            <button class="btn btn-warning btn-block btn-file" onclick="fileexplorer()"  class="modal-trigger" data-toggle="modal" data-target="#loading_modal">
                <form id="file_form" enctype="multipart/form-data" action="../php_api/import_outgoing_shipment.php" method="POST">
                    <span><i class="fas fa-upload mr-2"></i>Import FSIB File</span><input type="file" id="import_outgoing_fsib" name="outgoing_shipment_file" onchange="submit()" accept=".csv" style="opacity:0; display:none;">
                </form>
            </button>
        </div>
    </div>
    <?php include "../forms/add_outgoing_shipment_form.php"; ?>
</div>
<script>
    function fileexplorer() {
        document.getElementById("import_outgoing_fsib").click();
    }
</script>