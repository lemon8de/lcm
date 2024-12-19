<div class="container mt-2"></div>
<form id="OutgoingFsiLASearchForm">
    <div class="d-flex w-75 m-2 p-3 align-items-center" style="background-color:#ffffff;box-shadow:0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2);background-clip:border-box;border-radius:.25rem;">
            <div class="col-3">
                <select class="form-control" name="month" onchange="outgoing_fsi_la_search(true)">
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
            <div class="col-3">
                <select class="form-control" name="year" onchange="outgoing_fsi_la_search(true)">
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
            <div class="col-3">
                <select class="form-control" name="car_model" onchange="outgoing_fsi_la_search(false)" id="car_model_select">
                    <option disabled selected value="">Car Model</option>
                </select>
            </div>
            <div class="col-3 mr-4">
                <select class="form-control" name="vessel_name" onchange="outgoing_fsi_la_search(false)" id="switch_invoice_select">
                    <option selected value="">Vessel</option>
                    <?php 
                        $sql = "SELECT DISTINCT vessel_name, etd_mnl from m_outgoing_vessel_details where vessel_name is not null order by etd_mnl desc";
                        $stmt = $conn -> query($sql);
                        while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                            echo <<<HTML
                                <option>{$data['vessel_name']}</option>
                            HTML;
                        }
                    ?>
                </select>
            </div>
            <div class="col-3">
                <button type="button" class="btn btn-block btn-primary" onclick="export_button()">Export Data</button>
            </div>
        </div>
    </div>
</form>
</div>
<div class="container" id="OutgoingFsiLAReportContent">
</div>

<script>
    function outgoing_fsi_la_search(should_clear) {
        if (should_clear) {
            document.getElementById("car_model_select").value = "";
            document.getElementById('OutgoingFsiLAReportContent').innerHTML = "";
        }
        var formData = $('#OutgoingFsiLASearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/report_outgoing_fsi_la.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.car_model) {
                    document.getElementById('car_model_select').innerHTML = response.car_model;
                }
                if (response.inner_html || response.inner_html == "") {
                    document.getElementById('OutgoingFsiLAReportContent').innerHTML = response.inner_html;
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX request failed:', textStatus, errorThrown);
        alert('An error occurred while processing your request. Please try again later.');
    }
        });
    }

    function export_button() {
        var formData = $('#OutgoingFsiLASearchForm').serialize();
        //gather data, if show active only, month and year
        $.ajax({
            url: '../php_api/export_outgoing_fsi_la.php',
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
                link.download = 'LCM-OUTGOING-FSI_LA[' + formattedDate +  '].csv'; // Set the file name
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