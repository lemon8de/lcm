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
    <table id="" class="table table-head-fixed table-hover mb-4" style="line-height:1;">
        <thead class="text-nowrap">
            <tr style="border-bottom:1px solid black">
                <th>FORWARDER</th>
                <th>ORIGIN</th>
                <th>HAWB / AWB</th>
                <th>ETA</th>
                <th>GROSS WEIGHT</th>
                <th>CHARGEABLE WEIGHT</th>
                <th>NO. OF PACKAGES</th>
                <th>COMMODITY</th>
                <th>CLASSIFICATION</th>
                <th>TYPE OF EXPENSE</th>
                <th>INCOTERM</th>
                <th>SHIPMENT STATUS</th>
                <th>SHIPMENT STATUS PROGRESS</th>
                <th>TENTATIVE DELIVERY SCHEDULE</th>
                <th>REQUIRED DELIVERY</th>
                <th>ACTUAL DATE OF DELIVERY</th>
                <th>SHIPPER</th>
                <th>PORT</th>
                <th>SHIPPING INVOICE</th>
                <th>COMMODITY QUANTITY</th>
                <th>COMMODITY UO</th>
                <th>COMMERCIAL INVOICE CURRENCY</th>
                <th>COMMERCIAL INVOICE AMOUNT</th>
                <th>GROSS WEIGHT</th>
                <th>INCOTERM</th>
                <th>IP NUMBER</th>
                <th>DR NUMBER</th>
                <th>TOTAL CUSTOM VALUE</th>
                <th>DUTIABLE VALUE</th>
                <th>RATE</th>
                <th>CUSTOMS DUTY</th>
                <th>LANDED COST</th>
                <th>VAT</th>
                <th>BANK CHARGES</th>
                <th>ENTRY NO</th>
                <th>OR NUMBER</th>
                <th>ASSESSMENT DATE</th>
                <th>BROKERAGE FEE</th>
                <th>FLIGHT NO.</th>
            </tr>
        </thead>
        <tbody id="ImportReportContent">
        
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        search_import_report();
    });

    function search_import_report() {
        var formData = $('#ImportReportSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/refine_import_air_report.php',
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
        var formData = $('#ImportReportSearchForm').serialize();
        $.ajax({
            url: '../php_api/export_import_data_air.php',
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
                link.download = 'LCM-AIR-IMPORTDATA[' + formattedDate +  '].csv'; // Set the file name
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