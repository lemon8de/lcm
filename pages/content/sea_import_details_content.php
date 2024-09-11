<div class="container mt-2 mb-3">
    <div class="row">
            <div class="col-4">
                <div class="container">
                <form id="ImportReportSearchForm">
                <div class="row">
                    <div class="col-8">
                        <select class="form-control" name="month" onchange="search_import_report()">
                            <option value="" selected disabled>Select Month</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <select class="form-control" name="year" onchange="search_import_report()">
                            <?php
                                $current_year = date("Y");
                                $end_year = $current_year - 10;
                                for ($year = $current_year; $year >= $end_year; $year--) {
                                    echo <<<HTML
                                        <option value="{$year}">{$year}</option>
                                    HTML;
                                }
                            ?>
                        </select>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container" style="max-height: 600px; overflow-y: auto;" id="ImportDataMain">
    <?php include '../php_static/content_tables/importsea_data_report.php';?>
</div>
<div class="container" id="ContainerBreakdownSwitch" style="display:none;">
    <?php include '../php_static/content_tables/container_breakdown.php';?>
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
        //console.log(this.value);
        $.ajax({
            url: '../php_api/get_import_data.php',
            type: 'GET',
            data: {
                'shipping_invoice' : this.value,
            },
            dataType: 'json',
            success: function (response) {
                //console.log(response);
                document.getElementById('ImportInformation').innerHTML = response.html;
            }
        });
    }

    function show_breakdown() {
        console.log(this.id);
        $.ajax({
            url: '../php_api/get_container_breakdown.php',
            type: 'GET',
            data: {
                'shipping_invoice' : this.id,
            },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                document.getElementById('ContainerBreakdownContent').innerHTML = response.html;
                //hide the main table, show the table switch
            }
        });
        //show and hide stuff
        document.getElementById('ImportDataMain').style.display = 'none';
        document.getElementById('ContainerBreakdownSwitch').style.display = 'block';
    }

    function search_import_report() {
        var formData = $('#ImportReportSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/refine_import_report.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (!response.exited) {
                    document.getElementById("ImportReportContent").innerHTML = response.inner_html;
                }
            },
        });
    }

</script>