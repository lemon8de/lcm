<div class="container mt-2">
    <form id="PolytainerReportSearchForm">
        <div class="d-flex align-items-center mb-3">
            <div class="d-flex w-50 m-1 p-2 align-items-center" style="background-color:#ffffff;box-shadow:0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2);background-clip:border-box;border-radius:.25rem;">
                <div class="col-7 form-check">
                    <input class="form-check-input" type="checkbox" name="remove_active" onchange="search_polytainer_report()">
                    <label class="form-check-label">Show Received Shipments Only<br><span class="text-muted small">&nbsp;For Accurate Historical Logs</span></label>
                </div>
                <div class="col-4">
                    <button type="button" id="exportButton" class="btn btn-block btn-primary">Export Data</button>
                </div>
            </div>
            <div class="d-flex ml-1" id="PolytainerVesselSummation">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-3">
                <select class="form-control" id="monthSelect" name="month" onchange="search_polytainer_report()">
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
    </form>
</div>

<div class="card p-2 m-2 container-fluid" style="max-height: 70vh; overflow-y: auto;">
    <?php include '../php_static/content_tables/polytainer_report.php';?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        search_polytainer_report();
    });
    function search_polytainer_report() {
        var formData = $('#PolytainerReportSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/refine_polytainer_report.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (!response.exited) {
                    document.getElementById("PolytainerReportContent").innerHTML = response.final_html;
                    document.getElementById("PolytainerVesselSummation").innerHTML = response.counter;
                }
            },
        });
    }
    document.getElementById('exportButton').addEventListener('click', function() {
        // Get the table element
        const table = document.getElementById('PolytainerReportTable');
        const headerRow = table.querySelector('thead tr:nth-child(3)'); // Select the third header row
        const headerCells = Array.from(headerRow.cells).map(cell => cell.textContent);
        const rows = Array.from(table.querySelectorAll('tbody tr')); // Select only tbody rows
            
        // Create CSV content with header
        const csvContent = [headerCells.join(','), ...rows.map(row => {
            const cells = Array.from(row.cells).map(cell => cell.textContent);
            return cells.join(',');
        })].join('\n');

        // Create a blob and a link to download the CSV
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);

        const currentDate = new Date();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // 01-12
        const year = currentDate.getFullYear(); // 4-digit year
        // Format as MM/YYYY
        const formattedDate = `${month}/${year}`;
        var filename = 'LCM-SEA-ACTIVE[' + formattedDate +  '].csv'; // Set the file name

        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
</script>