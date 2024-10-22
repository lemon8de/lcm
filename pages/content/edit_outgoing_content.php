<form id="OutgoingSearchForm">
    <div class="container">
        <div class="row mb-2">
            <div class="col-3">
                <input class="form-control" placeholder="INVOICE NO." name="invoice_no" onkeyup="debounce(outgoing_search, 350)" autocomplete="off">
            </div>
            <div class="col-3">
                <input class="form-control" placeholder="CONTAINER NO." name="container_no" onkeyup="debounce(outgoing_search, 350)" autocomplete="off">
            </div>
            <div class="col-2">
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
            <div class="col-2">
                <select class="form-control" id="destination_select" name="destination_service_center" onchange="outgoing_search()">
                    <option disabled value="" selected>Destination</option>
                </select>
            </div>
        </div>
    </div>
</form>
<div class="container">
    <div class="row mb-2">
        <div class="col-3">
            <button class="btn btn-block btn-primary" onclick="edit_selected()">Edit Selected</button>
        </div>
    </div>
</div>

<div class="container-fluid" style="max-height: 80vh; overflow-y:auto;">
<table class="table table-head-fixed text-nowrap table-hover">
    <thead>
        <tr style="border-bottom:1px solid black">
            <th><input type="checkbox" onchange="checkall(this)"></th>
            <th>INVOICE NO.</th>
            <!-- <th>CONTAINER NO.</th> -->
            <th>DESTINATION (Service Center)</th>
            <th>STATUS</th>
            <th>CO-STATUS</th>
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
                if (!response.exited) {
                    document.getElementById('OutgoingSearchTableBody').innerHTML = response.inner_html;
                    document.getElementById('destination_select').innerHTML = response.selection;
                }
            },
        });
    }
    
    function checkall(source) {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }
    
    let selectedIds = [];
    function edit_selected() {
        const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
        selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.id);
        if (selectedIds.length == 0) {
            Toast.fire({
		        icon: "info",
		        title: "None selected",
	        })
        } else {
            $('#editb_outgoing_modal').modal('show');
        }
    }
</script>