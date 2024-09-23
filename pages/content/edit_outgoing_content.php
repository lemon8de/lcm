<form id="OutgoingSearchForm">
    <div class="container">
        <div class="row mb-3">
            <div class="col-3">
                <input class="form-control" placeholder="INVOICE NO." name="invoice_no" onkeyup="debounce(outgoing_search, 350)" autocomplete="off">
            </div>
            <div class="col-3">
                <input class="form-control" placeholder="CONTAINER NO." name="container_no" onkeyup="debounce(outgoing_search, 350)" autocomplete="off">
            </div>
            <div class="col-3">
                <select class="form-control" name="month" onchange="outgoing_search()">
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
                <select class="form-control" name="year" onchange="outgoing_search()">
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

<div class="container" style="max-height: 80vh; overflow-y:auto;">
<table class="table table-head-fixed text-nowrap table-hover">
    <thead>
        <tr style="border-bottom:1px solid black">
            <th>INVOICE NO.</th>
            <th>CONTAINER NO.</th>
            <th>DESTINATION (Service Center)</th>
        </tr>
    </thead>
    <tbody id="OutgoingSearchTableBody">
    </tbody>
</table>
</div>

<script>
    function debounce(method, delay) {
        clearTimeout(method._tId);
        method._tId = setTimeout(function() {
            method();
        }, delay);
    }

    function outgoing_search() {
        var formData = $('#OutgoingSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/search_outgoing.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                document.getElementById('OutgoingSearchTableBody').innerHTML = response.inner_html;
            },
        });
    }
</script>