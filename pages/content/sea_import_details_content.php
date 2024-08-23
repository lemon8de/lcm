<div class="card card-gray-dark card-outline">
    <div class="card-header collapsed">
        <h3 class="card-title">Import Data Report</h3>
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
            <?php include '../php_static/content_tables/importsea_data_report.php';?>
        <!-- </div> -->
    </div>
</div>

<div class="card card-gray-dark card-outline">
    <div class="card-header collapsed">
        <h3 class="card-title">Edit Data</h3>
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
        <div class="row">
            <select onchange="loaddata.call(this)" class="form-control mb-4">
                <option value = "" disabled selected>Select Invoice</option>
                <?php 
                    $sql = "SELECT shipping_invoice from import_data";
                    $stmt = $conn -> prepare($sql);
                    $stmt -> execute();
                    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                        echo <<<HTML
                            <option value="{$row['shipping_invoice']}">{$row['shipping_invoice']}</option>
                        HTML;
                    }
                ?>
            </select>
        </div>
        <!-- <div class="container"> -->
            <?php include '../forms/import_data_edit_form.php';?>
        <!-- </div> -->
    </div>
</div>

<script>
    function loaddata() {
        console.log(this.value);
        $.ajax({
            url: '../php_api/get_import_data.php',
            type: 'GET',
            data: {
                'shipping_invoice' : this.value,
            },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                document.getElementById('ImportInformation').innerHTML = response.html;
            }
        });
    }
</script>