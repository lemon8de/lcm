<div class="container mt-2">
    <form id="ImportReportSearchForm">
        <div class="d-flex align-items-center mb-3">
            <div class="d-flex w-50 m-1 p-2 align-items-center" style="background-color:#ffffff;box-shadow:0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2);background-clip:border-box;border-radius:.25rem;">
                <div class="col-7 form-check">
                    <input class="form-check-input" id="show_active_only_box" type="checkbox" name="remove_active" onchange="search_import_report()">
                    <label class="form-check-label">Show Received Shipments Only<br><span class="text-muted small">&nbsp;For Accurate Historical Logs</span></label>
                </div>
                <div class="col-4">
                    <button type="button" onclick="export_button()" class="btn btn-block btn-primary">Export Data</button>
                </div>
            </div>
            <div class="d-flex" id="SummationContainer">
                <!-- <div class="ml-1">
                    <div class="bg-info pl-4 pr-4" style="border-radius:.350rem;padding:0rem .350rem">
                        <h4 style="font-weight:700;line-height:1.5;"><span style="font-size:75%;font-weight:500;">&nbsp;Total</span></h4>
                    </div>
                </div>
                <div class="ml-1">
                    <div class="bg-success pl-4 pr-4" style="border-radius:.350rem;padding:0rem .350rem">
                        <h4 style="font-weight:700;line-height:1.5;"><span style="font-size:75%;font-weight:500;">&nbsp;Received</span></h4>
                    </div>
                </div> -->
                <div class="ml-1" id="PolytainerVesselSummation">
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-3">
                <select class="form-control" id="monthSelect" name="month" onchange="search_import_report()">
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
                <script>
                    // Get the current month (0-11)
                    const currentMonth = new Date().getMonth() + 1;

                    // Select the month in the dropdown
                    const monthSelect = document.getElementById('monthSelect');
                    monthSelect.selectedIndex = currentMonth;
                </script>
            </div>
            <div class="col-2">
                <select class="form-control" id="active_year" name="year" onchange="search_import_report()">
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

<div class="card p-2 m-2 container-fluid" style="max-height:70vh; overflow-y: auto;" id="ImportDataMain">
    <?php include '../php_static/content_tables/importsea_data_report.php';?>
</div>

<div class="card p-2 m-2 container-fluid" style="max-height:70vh;overflow-y:auto;display:none;" id="ContainerBreakdownSwitch">
    <?php include '../php_static/content_tables/container_breakdown.php';?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        search_import_report();
    });

    function show_breakdown() {
        $.ajax({
            url: '../php_api/get_container_breakdown.php',
            type: 'GET',
            data: {
                'shipping_invoice' : this.id,
            },
            dataType: 'json',
            success: function (response) {
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
                    document.getElementById("SummationContainer").innerHTML = response.counter;
                }
            },
        });
    }
    function export_button() {
        var import_data = {
            'show_active_only' : document.getElementById('show_active_only_box').checked,
            'month' : document.getElementById('monthSelect').value,
            'year' : document.getElementById('active_year').value,

        }
        console.log(import_data);
        $.ajax({
            url: '../php_api/export_import_data.php',
            type: 'POST',
            data: import_data,
            xhrFields: {
                responseType: 'blob' // Set the response type to blob
            },

            success: function(data) {
                // Create a link element
                var link = document.createElement('a');
                var url = window.URL.createObjectURL(data);
                link.href = url;
                // Create a new Date object
                const currentDate = new Date();
                const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // 01-12
                const year = currentDate.getFullYear(); // 4-digit year
                // Format as MM/YYYY
                const formattedDate = `${month}/${year}`;
                link.download = 'LCM-IMPORTDATA[' + formattedDate +  '].csv'; // Set the file name
                document.body.appendChild(link);
                link.click(); // Simulate click to download
                document.body.removeChild(link); // Remove the link
            },
            error: function() {
                alert('Error exporting data.');
            }
        });
    }
</script>