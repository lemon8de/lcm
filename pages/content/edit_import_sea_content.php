<form id="ImportSearchForm">
    <div class="container">
        <div class="row mb-3">
            <div class="col-4">
                <input class="form-control" placeholder="BL NUMBER" name="bl_number" onkeyup="debounce(import_search, 350)" autocomplete="off">
            </div>
            <div class="col-4">
                <input class="form-control" placeholder="CONTAINER" name="container" onkeyup="debounce(import_search, 350)" autocomplete="off">
            </div>
            <div class="col-4">
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

    function import_search() {
        var formData = $('#ImportSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/search_import.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (!response.search_state){
                    document.getElementById('EditImportDataTableBody').innerHTML = response.inner_html;
                } else {
                    document.getElementById('EditImportDataTableBody').innerHTML = "";
                }
            },
        });
    }
</script>