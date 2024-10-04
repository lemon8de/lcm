<div class="container">
    <div class="container">
        <div class="row mb-3">
            <div class="col-12 d-flex">
                <div class="btn-group">
                    <button type="button" class="btn btn-success" onclick="select_all_bl(this)">Select&nbsp;All</button>
                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" role="menu" style="cursor:pointer;">
                        <a class="dropdown-item" onclick="confirm_shipment()">Confirm Shipment</a>
                        <a class="dropdown-item" onclick="delete_shipment()">Delete Shipment</a>
                    </div>
                </div>
                <input class="form-control ml-3 w-25" placeholder="BL NUMBER" name="bl_number" id="bl_number_search" onkeyup="debounce(search_documentation, 350)">
                <div>
                <select class="form-control ml-2" id="month_search" onchange="search_documentation()">
                    <?php 
                        $month_today = date('n');
                        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        for ($i = 1; $i <= 12; $i++) {
                    ?>
                        <option value="<?php echo $i; ?>"<?php echo $i == $month_today ? " selected" : ""; ?>><?php echo $months[$i - 1]; ?></option>
                    <?php
                        }
                    ?>
                    </select>
                </div>
                <div>
                <select class="form-control ml-3" id="year_search" onchange="search_documentation()">
                        <?php
                            $current_year = date("Y");
                            $end_year = $current_year - 10;
                            for ($year = $current_year; $year >= $end_year; $year--) {
                                echo <<<HTML
                                    <option>{$year}</option>
                                HTML;
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-8">
            <div class="alert alert-info" style="display:none;" id="HistoricalAlert">
                <span><i class="icon fas fa-info"></i>&nbsp;Viewing NON-Active Shipments of Selected Month and Year</span>
            </div>
            <div class="container" id="DocumentationMainContainer" style="max-height:70vh;overflow-y:auto;">
            </div>
        </div>
        <div class="col-4">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Status Filter</h3>
                </div>
                <div class="card-body" id="FilterContent">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        search_documentation();
    });
    function load_containers(initiator) {
        $.ajax({
            url: '../php_api/get_documentation_containers.php',
            type: 'GET',
            data: {
                'bl_number' : initiator.id,
            },
            dataType: 'json',
            success: function (response) {
                document.getElementById('table-' + initiator.id).innerHTML = response.inner_html;
            }
        });
    }

    function search_documentation(refresh_filters = true) {
        ck_bl_status = true; //makes the select all button work again

        //extract the shipment status
        shipment_status = "";
        checkboxes = document.querySelectorAll('.ck-shipment-status');
        checkboxes.forEach(checkbox => {
            if (checkbox.checked == true) {
                shipment_status = checkbox.id;
            }
        });
        //build the data, unfortunately this won't be a form serialize
        data = {
            "bl_number" : document.getElementById('bl_number_search').value,
            "month" : document.getElementById('month_search').value,
            "year" : document.getElementById('year_search').value,
            //a bit special, we need to get it using jquery
            "shipment_status" :shipment_status,
            "refresh_filters" : refresh_filters
        };

        //show historical warning alert
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth() + 1; // 0 for January, 1 for February, etc.
        const currentYear = currentDate.getFullYear();

        if (data['month'] !== currentMonth.toString() || data['year'] !== currentYear.toString()) {
            document.getElementById('HistoricalAlert').style.display = 'block';
        } else {
            document.getElementById('HistoricalAlert').style.display = 'none';
        }

        //ajax now
        $.ajax({
            url: '../php_api/search_documentation.php',
            type: 'GET',
            data: data,
            dataType: 'json',
            success: function (response) {
                //console.log(response);
                document.getElementById("DocumentationMainContainer").innerHTML = response.inner_html;
                if (refresh_filters) {
                    document.getElementById("FilterContent").innerHTML = response.inner_html_filter;
                }
            }
        });
    }

    let selectedIds = [];
    function confirm_shipment() {
        const selectedCheckboxes = document.querySelectorAll('.ck-blnumber:checked');
        selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.id);
        if (selectedIds.length == 0) {
            Toast.fire({
		        icon: "info",
		        title: "None selected",
	        })
        } else {
            $.ajax({
            url: '../php_api/sea_confirm_departure.php',
            type: 'POST',
            data: {
                'bl_numbers' : selectedIds,
            },
            dataType: 'json',
            success: function (response) {
                if (response.notification) {
                    Toast.fire({
		                icon: response.notification.icon,
		                title: response.notification.text,
	                })
                    ck_bl_status = true; //makes the select all button work again
                    search_documentation(false);
                }

            }
        });
        }
    }
    function delete_shipment() {
        const selectedCheckboxes = document.querySelectorAll('.ck-blnumber:checked');
        selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.id);
        if (selectedIds.length == 0) {
            Toast.fire({
		        icon: "info",
		        title: "None selected",
	        })
        } else {
            console.log(selectedIds);
            //ajax here to update the modal information, just show the blnumber and stuff
            $.ajax({
                url: '../php_api/sea_get_confirm_deletion_details.php',
                type: 'GET',
                data: {
                    'bl_numbers' : selectedIds,
                },
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    document.getElementById('ConfirmDeletionContent').innerHTML = response.inner_html;
                }
            });
            $('#confirm_deletion_modal').modal('show');
            confirm_deletion_modal_start_timer();
        }
    }

    function filter_shipment(initiator) {
        //retarded radio button logic
        checkboxes = document.querySelectorAll('.ck-shipment-status');
        checkboxes.forEach(checkbox => {
            if (checkbox != initiator) {
                checkbox.checked = false;
            }
        });
        search_documentation(false);
    }

    function debounce(method, delay) {
        clearTimeout(method._tId);
        method._tId = setTimeout(function() {
            method();
        }, delay);
    }

    let ck_bl_status = true;
    function select_all_bl(source) {
        checkboxes = document.querySelectorAll('.ck-blnumber');
        checkboxes.forEach(checkbox => {
            checkbox.checked = ck_bl_status;
        });
        ck_bl_status = !ck_bl_status;
    }
</script>