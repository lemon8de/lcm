<form id="DocumentationSearchForm">
    <div class="container">
        <div class="row mb-3">
            <div class="col-4">
                <input class="form-control" placeholder="BL NUMBER" name="bl_number" onkeyup="debounce(import_search, 350)">
            </div>
            <div class="col-4">
                <input class="form-control" placeholder="CONTAINER" name="container" onkeyup="debounce(import_search, 350)">
            </div>
            <div class="col-4">
                <input class="form-control" placeholder="INVOICE" name="commercial_invoice" onkeyup="debounce(import_search, 350)">
            </div>
        </div>
    </div>
</form>
<div class="container">
    <?php include '../php_static/content_tables/incoming_sea_confirm.php';?>
</div>

<script>
    function debounce(method, delay) {
        clearTimeout(method._tId);
        method._tId = setTimeout(function() {
            method();
        }, delay);
    }

    function import_search() {
        var formData = $('#DocumentationSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/search_documentation.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                document.getElementById('ConfirmIncomingSeaTableBody').innerHTML = response.inner_html;
            },
        });
    }
</script>