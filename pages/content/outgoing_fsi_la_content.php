<form id="OutgoingFsiLASearchForm">
    <div class="container">
        <div class="row mb-2">
            <div class="col-2">
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
            <div class="col-2">
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
            <div class="col-2">
                <select class="form-control" name="car_model" onchange="outgoing_fsi_la_search(false)" id="car_model_select">
                    <option disabled selected value="">Car Model</option>
                </select>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-block btn-primary">Export Data</button>
            </div>
        </div>
    </div>
</form>
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
                if (response.inner_html) {
                    document.getElementById('OutgoingFsiLAReportContent').innerHTML = response.inner_html;
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX request failed:', textStatus, errorThrown);
        alert('An error occurred while processing your request. Please try again later.');
    }
        });
    }
</script>