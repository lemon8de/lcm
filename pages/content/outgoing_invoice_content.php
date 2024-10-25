<div class="container mt-2">
    <form id="OutgoingInvoiceSearchForm">
        <div class="d-flex align-items-center mb-3">
            <div class="d-flex w-50 m-2 p-3 align-items-center" style="background-color:#ffffff;box-shadow:0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2);background-clip:border-box;border-radius:.25rem;">
            <div class="col-3">
                <select class="form-control" id="active_month" name="month" onchange="outgoing_invoice_search()">
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
            <div class="col-3">
                <select class="form-control" name="year" onchange="outgoing_invoice_search()">
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
            <div class="col-4">
                <button type="button" class="btn btn-block btn-primary" onclick="export_button()">Export Data</button>
            </div>
            </div>
            <div class="d-flex" id="SummationContainer">
                <h1>asdf</h1>
            </div>
        </div>
    </form>
</div>

<div class="card p-2 m-2 container-fluid" style="max-height: 70vh; overflow-y: auto;">
    <table id="" class="table table-head-fixed table-hover mb-4">
        <thead class="text-nowrap">
            <tr style="border-bottom:1px solid black">
                <th>INVOICE NO.</th>
                <th>INVOICE DATE</th>
                <th>CONTAINER NO.</th>
                <th>ATD</th>
                <th>NO. OF SETS</th>
                <th>INVOICE AMOUNT</th>
                <th>VESSEL NAME</th>
            </tr>
        </thead>
        <tbody id="ImportReportContent">
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        outgoing_invoice_search();
    });
    function outgoing_invoice_search() {
        var formData = $('#OutgoingInvoiceSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/report_outgoing_invoice.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                document.getElementById('ImportReportContent').innerHTML = response.inner_html;
                document.getElementById('SummationContainer').innerHTML = response.count;
            },
        });
    }

    function export_button() {
        var formData = $('#OutgoingInvoiceSearchForm').serialize();
        //gather data, if show active only, month and year
        $.ajax({
            url: '../php_api/export_outgoing_import.php',
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
                link.download = 'LCM-OUTGOING-INVOICE[' + formattedDate +  '].csv'; // Set the file name
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