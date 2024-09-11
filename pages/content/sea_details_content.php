<div class="container mt-2">
    <form id="PolytainerReportSearchForm">
        <div class="container mb-2">
            <div class="row">
                <div class="col-3">
                    <select class="form-control" name="month" onchange="search_polytainer_report()">
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
                <div class="col-2">
                    <select class="form-control" name="year" onchange="search_polytainer_report()">
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
        </div>
    </form>
</div>
<div class="container table-responsive">
    <?php include '../php_static/content_tables/polytainer_report.php';?>
</div>

<script>
    function search_polytainer_report() {
        var formData = $('#PolytainerReportSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/refine_polytainer_report.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (!response.exited) {
                    document.getElementById("PolytainerReportContent").innerHTML = response.inner_html;
                }
            },
        });
    }
</script>