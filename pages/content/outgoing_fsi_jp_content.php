<form id="OutgoingFsiJpSearchForm">
    <div class="container">
        <div class="row mb-2">
            <div class="col-2">
                <select class="form-control" name="month" onchange="outgoing_fsi_jp_search(true)">
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
                <select class="form-control" name="year" onchange="outgoing_fsi_jp_search(true)">
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
                <select class="form-control" name="switch_invoice" onchange="outgoing_fsi_jp_search(false)" id="switch_invoice_select">
                    <option disabled selected value="">Switch Invoice</option>
                    <?php
                    //$sql = "SELECT DISTINCT SUBSTRING( invoice_no, CHARINDEX('-', invoice_no) + 1, CHARINDEX('-', invoice_no, CHARINDEX('-', invoice_no) + 1) - CHARINDEX('-', invoice_no) - 1) AS switch_invoice FROM m_outgoing_fsib WHERE ship_out_date BETWEEN CAST(CONCAT('2024', '-', '7', '-01') AS DATE) AND EOMONTH(CAST(CONCAT('2024', '-', '7', '-01') AS DATE));";

                    ?>
                </select>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-block btn-primary">Export Data</button>
            </div>
        </div>
    </div>
</form>
<div class="container" id="OutgoingFsiJpReportContent">
</div>

<script>
    function outgoing_fsi_jp_search(should_clear) {
        if (should_clear) {
            document.getElementById("switch_invoice_select").value = "";
            document.getElementById('OutgoingFsiJpReportContent').innerHTML = "";
        }
        var formData = $('#OutgoingFsiJpSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/report_outgoing_fsi_jp.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.switch_invoice) {
                    document.getElementById('switch_invoice_select').innerHTML = response.switch_invoice;
                }
                if (response.inner_html) {
                    document.getElementById('OutgoingFsiJpReportContent').innerHTML = response.inner_html;
                }
            },
        });
    }
</script>