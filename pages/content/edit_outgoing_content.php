<form id="OutgoingSearchForm">
    <div class="card container pt-2 mt-2">
        <div class="row mb-2">
            <div class="col-2">
                <input class="form-control" placeholder="CONT/INV/VESSEL" name="invoice_no" onkeyup="debounce(outgoing_search, 350)" autocomplete="off">
            </div>
            <div class="col-3" style="display:none;">
                <input class="form-control" placeholder="CONTAINER NO." name="container_no" onkeyup="debounce(outgoing_search, 350)" autocomplete="off">
            </div>
            <div class="col-2">
                <select class="form-control" name="month" onchange="outgoing_search()" id="active_month">
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
                    <option value="" selected>Destination</option>
                </select>
            </div>
            <div class="col-2">
                <select class="form-control" id="destination_select" name="status" onchange="outgoing_search()">
                    <option value="" selected>Status</option>
                    <option>N/A</option>
                    <option>FOR REQUEST</option>
                    <option>RECEIVED</option>
                    <option>ONGOING</option>
                </select>
            </div>
            <div class="col-2">
                <select class="form-control" id="destination_select" name="co_status" onchange="outgoing_search()">
                    <option value="" selected>CO-Status</option>
                    <option>N/A</option>
                    <option>FOR REQUEST</option>
                    <option>COMPLETE</option>
                    <option>ONGOING</option>
                </select>
            </div>
        </div>
    </div>
</form>
<div class="container">
    <div class="row mb-2">
        <div class="col-3">
            <button class="btn btn-primary" onclick="edit_selected()">Edit Vessel of Selected</button>
        </div>
    </div>
</div>
<div class="card p-2 m-2 container-fluid" style="max-height: 80vh; overflow-y:auto;">
<table class="table table-head-fixed text-nowrap table-hover">
    <thead>
        <tr style="border-bottom:1px solid black">
            <th><input type="checkbox" onchange="checkall(this)"></th>
            <th>INVOICE NO.</th>
            <th>CONTAINER NO.</th>
            <th>DESTINATION (Service Center)</th>
            <th>DESTINATION</th>
            <th>CAR_MODEL</th>
            <th>SHIP OUT DATE</th>
            <th>NO PALLETS</th>
            <th>NO CARTONS</th>
            <th>PACK QTY</th>
            <th>INVOICE AMOUNT</th>
            <th>MODE OF SHIPMENT</th>
            <th>VESSEL NAME</th>
            <th>SHIPPING LINE</th>
            <th>ETD MNL</th>
            <th>ETA DESTINATION</th>
            <th>SHIPPING TERMS</th>
            <th>NET WEIGHT</th>
            <th>GROSS WEIGHT</th>
            <th>CBM</th>
            <th>FALP IN/REUSE</th>
            <th>STATUS OF CONTAINER</th>
            <th>CONTAINER SIZE</th>
            <th>FORWARDER</th>
            <th>ED REFERENCE</th>
            <th>SHIPPING SEAL</th>
            <th>PEZA SEAL</th>
            <th>FALP OUT DATE</th>
            <th>FALP OUT TIME</th>
            <th>TRUCKHEAD STATUS</th>
            <th>BL_DATE</th>
            <th>BL_NUMBER</th>
            <th>IRREGULAR SHIPMENT</th>
            <th>STATUS</th>
            <th>CO-STATUS</th>
        </tr>
    </thead>
    <tbody id="OutgoingSearchTableBody">
    </tbody>
</table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        outgoing_search();
    });
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