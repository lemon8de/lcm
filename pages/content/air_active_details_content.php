<div class="container mt-2">
    <form id="ActiveReportSearchForm">
    <div class="d-flex align-items-center mb-3">
        <div class="d-flex w-50 m-1 p-2 align-items-center" style="background-color:#ffffff;box-shadow:0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2);background-clip:border-box;border-radius:.25rem;">
            <div class="col-5 form-check">
                <input class="form-check-input" name="show_active" id="show_active_only_box" type="checkbox" checked onchange="historical_mode.call(this)">
                <label class="form-check-label">Show Active Only<br><span class="text-muted small">&nbsp;Disable for Historical Logs</span></label>
            </div>
            <div class="col-4">
                <button type="button" onclick="export_button()" class="btn btn-block btn-primary">Export Data</button>
            </div>
        </div>
        <div class="d-flex" id="SummationContainer">
        </div>
    </div>
    <div class="row mb-3" id="ActiveReportSearchBars" style="display:none;">
        <div class="col-3">
            <select class="form-control" id="active_month" name="month" onchange="search_active_report()">
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
                const currentMonth = new Date().getMonth();
                // Select the month in the dropdown
                const monthSelect = document.getElementById('active_month');
                monthSelect.selectedIndex = currentMonth;
            </script>
        </div>
        <div class="col-2">
            <select class="form-control" id="active_year" name="year" onchange="search_active_report()">
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

<div class="card p-2 m-2 container-fluid" style="max-height: 70vh; overflow-y: auto;" id="ImportDataMain">
    <table id="" class="table table-head-fixed table-hover mb-4">
        <thead class="text-nowrap">
            <tr style="border-bottom:1px solid black">
                <th>FORWARDER</th>
                <th>ORIGIN</th>
                <th>HAWB / AWB</th>
                <th>ETA</th>
                <th>GROSS WEIGHT (KG)</th>
                <th>CHARGEABLE WEIGHT (KG)</th>
                <th>NO. OF PACKAGES</th>
                <th>INVOICE NO.</th>
                <th>COMMODITY</th>
                <th>CLASSIFICATION</th>
                <th>TYPE OF EXPENSE</th>
                <th>INCOTERM</th>
                <th>SHIPMENT STATUS</th>
                <th>SHIPMENT STATUS PROGRESS</th>
                <th>TENTATIVE DELIVERY SCHEDULE</th>
                <th>REQUIRED DELIVERY</th>
                <th>ACTUAL DATE OF DELIVERY</th>
                <th>TIME RECEIVED</th>
                <th>RECEIVED BY</th>
            </tr>
        </thead>
        <tbody id="ActiveReportContent">
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        search_active_report();
    });
    function historical_mode() {
        //this is a cheeky way lol, so the active table will autoreload and will load again when you
        //check the box
        //this makes this page different as import and polytainer pages do not have
        //preloaded content, this will make life hard.
        //though we can argue that it is optimized that way
        // IDK IDKDIDKD IDKD IDK
        if (this.checked) {
            document.getElementById("ActiveReportSearchBars").style.display = 'none';
            //do an ajax request here of empty search parameters to reload it
            //probably recall the function or just copy it here
            monthSelect.selectedIndex = currentMonth;
        } else {
            document.getElementById("ActiveReportSearchBars").style.display = 'flex';
            document.getElementById("ActiveReportContent").innerHTML = '';
        }
        search_active_report();
    }
    function search_active_report() {
        var formData = $('#ActiveReportSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/refine_active_air_report.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (!response.exited) {
                    document.getElementById("ActiveReportContent").innerHTML = response.inner_html;
                    document.getElementById("SummationContainer").innerHTML = response.inner_html_count;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
                alert("An error occurred while processing your request. Please try again later.");
            }
        });
    }

    function export_button() {
        var formData = $('#ActiveReportSearchForm').serialize();
        //gather data, if show active only, month and year
        $.ajax({
            url: '../php_api/export_active_air.php',
            type: 'POST',
            data: formData,
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
                link.download = 'LCM-AIR-ACTIVE[' + formattedDate +  '].csv'; // Set the file name
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