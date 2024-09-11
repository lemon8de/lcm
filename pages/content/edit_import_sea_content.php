<div class="container mb-3">
    <div class="row">
        <div class="col-3">
            <button class="btn btn-warning btn-block btn-file" onclick="fileexplorer()">
                <form id="file_form" enctype="multipart/form-data" action="../php_api/import_sea_peza_array-invoices.php" method="POST">
                    <span><i class="fas fa-upload mr-2"></i>PEZA Import</span><input type="file" id="peza_import_sea" name="import_sea_peza_file" onchange="submit()" accept=".csv" style="opacity:0; display:none;">
                </form>
            </button>
        </div>
    </div>
</div>
<form id="ImportSearchForm">
    <div class="container">
        <div class="row mb-3">
            <div class="col-3">
                <input class="form-control" placeholder="BL NUMBER" name="bl_number" onkeyup="debounce(import_search, 350)" autocomplete="off">
            </div>
            <div class="col-3">
                <input class="form-control" placeholder="CONTAINER" name="container" onkeyup="debounce(import_search, 350)" autocomplete="off">
            </div>
            <div class="col-3">
                <input class="form-control" placeholder="INVOICE" name="commercial_invoice" onkeyup="debounce(import_search, 350)" autocomplete="off">
            </div>
        </div>
    </div>
</form>
<div class="container" style="height:80vh; overflow-y:auto;">
    <?php include '../php_static/content_tables/import_sea_table.php';?>
</div>
<script>
    function debounce(method, delay) {
        clearTimeout(method._tId);
        method._tId = setTimeout(function() {
            method();
        }, delay);
    }
    function fileexplorer() {
        document.getElementById("peza_import_sea").click();
    }

    function import_search() {
        var formData = $('#ImportSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/search_import.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (!response.search_state){
                    document.getElementById('EditImportDataTableBody').innerHTML = response.inner_html;
                } else {
                    document.getElementById('EditImportDataTableBody').innerHTML = "";
                }
            },
        });
    }

    function load_import_data() {
        $.ajax({
            type: 'GET',
            url: '../php_api/get_import_modal_data.php',
            data: {
                'shipping_invoice' : this.id,
            },
            dataType: 'json',
            success: function(response) {
                document.getElementById('EditImportModalBody').innerHTML = response.inner_html;
            },
        });
    }
</script>