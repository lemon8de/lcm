<div class="container">
    <div class="row mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" checked onchange="historical_mode.call(this)">
            <label class="form-check-label">Show Active Only<span class="text-muted small">&nbsp;Disable for Historical Logs</span></label>
        </div>
    </div>
    <form id="ActiveReportSearchForm">
    <div class="row mb-3" id="ActiveReportSearchBars" style="display:none;">
        <div class="col-3">
            <select class="form-control" name="month" onchange="search_active_report()">
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
            <select class="form-control" name="year" onchange="search_active_report()">
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

<div class="container" style="max-height: 600px; overflow-y: auto;" id="ImportDataMain">
    <?php include '../php_static/content_tables/active_sea_report.php';?>
</div>

<script>
    function historical_mode() {
        //this is a cheeky way lol, so the active table will autoreload and will load again when you
        //check the box
        //this makes this page different as import and polytainer pages do not have
        //preloaded content, this will make life hard.
        //though we can argue that it is optimized that way
        // IDK IDKDIDKD IDKD IDK
        if (this.checked) {
            document.getElementById("ActiveReportSearchBars").style.display = 'none';
            location.reload();
            //do an ajax request here of empty search parameters to reload it
            //probably recall the function or just copy it here
        } else {
            document.getElementById("ActiveReportSearchBars").style.display = 'flex';
            document.getElementById("ActiveReportContent").innerHTML = '';
        }
    }
    function search_active_report() {
        var formData = $('#ActiveReportSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/refine_active_report.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (!response.exited) {
                    document.getElementById("ActiveReportContent").innerHTML = response.inner_html;
                }
            },
        });
    }
</script>