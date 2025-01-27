<div class="d-flex">
    <div class="col-8">
        <!-- top searching -->
        <div class="col-12 d-flex" style="margin-top:-0.4em;">
            <div class="btn-group ml-1 mr-1" style="display:none;">
                <button type="button" class="btn btn-info" onclick="select_all_bl(this)"><i class="fas fa-check"></i></button>
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu" role="menu" style="cursor:pointer;padding:0;background-color:transparent">
                    <a class="dropdown-item bg-danger" onclick="delete_shipment()">Delete Shipment</a>
                </div>
            </div>
            <div class="flex-grow-1 ml-1 mr-1">
                <input class="form-control w-100" placeholder="HAWB / AWB / INVOICE NO." id="hawb_awb_search" onkeyup="debounce(search_documentation, 350)">
            </div>
            <div class="ml-1 mr-1">
            <select class="form-control" id="month_search" onchange="search_documentation()">
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
            <div class="ml-1 mr-1">
            <select class="form-control" id="year_search" onchange="search_documentation()">
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
            <div class="ml-1 mr-1">
                <button class="btn btn-block btn-info" style="border-radius:1rem;padding:.375rem .375rem" onclick="refresh_all()"><i class="fas fa-redo"></i>&nbsp;Refresh</button>
            </div>
        </div>

        <!-- history alert -->
        <div class="col-12 alert alert-info mt-1" style="display:none;" id="HistoricalAlert">
            <span style="font-weight:700; font-size:90%;"><i class="icon fas fa-info"></i>&nbsp;Viewing Shipments of Selected Month and Year</span>
        </div>

        <!-- main content -->
        <div class="col-12 mt-3" id="DocumentationMainContainer" style="max-height:70vh;overflow-y:auto;">
        </div>
    </div>

    <div class="col-4">
        <div class="card card-default col-12">
            <div class="card-header d-flex justify-content-between" style="padding:.40rem .40rem;">
                <div class="flex-grow-1">
                    <i class="fas fa-tasks"></i>&nbsp;Status Progress
                </div>
                <div>
                    <button class="btn btn-block btn-secondary" onclick="clear_radio_status()" style="line-height:1;padding:.375rem .875rem;border-radius:1rem;">Clear</button>
                </div>
            </div>
            <form id="ShipmentProgressRadio">
            <div class="card-body mt-1 mb-1 pl-4" style="padding:1px;font-size:85%;" id="ProgressFilterContent">
            </div>
            </form>
        </div>

        <div class="card card-default col-12">
            <div class="card-header d-flex justify-content-between" style="padding:.40rem .40rem">
                <div class="flex-grow-1">
                    <i class="fas fa-pallet"></i></i>&nbsp;In-Storage Breakdown
                </div>
                <div>
                    <button class="btn btn-block btn-secondary" onclick="" style="line-height:1;padding:.375rem .875rem;border-radius:1rem;display:none;">Clear</button>
                </div>
            </div>
            <form id="ShipmentStorageRadio">
            <div class="card-body mt-1 mb-1 pl-4" style="padding:1px;" id="StorageFilterContent">
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        search_documentation();
    });

    let ck_bl_status = true;
    function select_all_bl(source) {
        checkboxes = document.querySelectorAll('.ck-blnumber');
        checkboxes.forEach(checkbox => {
            checkbox.checked = ck_bl_status;
        });
        ck_bl_status = !ck_bl_status;
    }

    function search_documentation(refresh_filters = true) {
        ck_bl_status = true; //makes the select all button work again
        var selectedValue = document.getElementById('ShipmentStorageRadio').querySelector('input[name="radio"]:checked');
        if (selectedValue) {
            storage = selectedValue.value;
        } else {
            storage = "";
        }

        var selectedValue = document.getElementById('ShipmentProgressRadio').querySelector('input[name="radio"]:checked');
        if (selectedValue) {
            progress = selectedValue.value;
        } else {
            progress = "";
        }

        //build the data, unfortunately this won't be a form serialize
        data = {
            "hawb_awb" : document.getElementById('hawb_awb_search').value,
            "month" : document.getElementById('month_search').value,
            "year" : document.getElementById('year_search').value,
            "refresh_filters" : refresh_filters,
            "shipment_status_progress" : progress,
            "storage" : storage
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
            url: '../php_api/search_documentation_air.php',
            type: 'GET',
            data: data,
            dataType: 'json',
            success: function (response) {
                document.getElementById("DocumentationMainContainer").innerHTML = response.inner_html;
                if (refresh_filters) {
                    document.getElementById("ProgressFilterContent").innerHTML = response.inner_html_progress;
                    document.getElementById("StorageFilterContent").innerHTML = response.inner_html_storage;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
                alert("An error occurred while processing your request. Please try again later.");
            }
        });
    }

    function debounce(method, delay) {
        clearTimeout(method._tId);
        method._tId = setTimeout(function() {
            method();
        }, delay);
    }

    function refresh_all() {
        document.getElementById('hawb_awb_search').value = "";
        document.getElementById("ShipmentProgressRadio").reset();
        document.getElementById("ShipmentStorageRadio").reset();
        search_documentation();
        Toast.fire({
		    icon: "success",
		    title: "Documentation Refreshed",
	    });
    }
    
    function load_shipment(initiator) {
        $.ajax({
            url: '../php_api/get_documentation_shipment_air.php',
            type: 'GET',
            data: {
                'hawb_awb' : initiator.id,
            },
            dataType: 'json',
            success: function (response) {
                document.getElementById('breakdown-' + initiator.id).innerHTML = response.inner_html;
            }
        });
    }

    function clear_radio_status() {
        document.getElementById("ShipmentProgressRadio").reset();
        document.getElementById("ShipmentStorageRadio").reset();
        search_documentation(false);
    }
</script>