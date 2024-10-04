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
                <?php
                    $sql = "EXEC GetBLCardsThisMonth :StartYear, :StartMonth"; //mssql server stored procedure
                    $stmt_get_cards = $conn -> prepare($sql);
                    $month = date('n');
                    $year = date('Y');
                    $stmt_get_cards -> bindParam(':StartYear', $year);
                    $stmt_get_cards -> bindParam(':StartMonth', $month);
                    $stmt_get_cards -> execute();

                    $sql = "SELECT shipment_status, color from m_shipment_status";
                    $stmt_callout_colors = $conn -> prepare($sql);
                    $stmt_callout_colors -> execute();
                    $colors = [];

                    // Fetch the data and build the associative array
                    while ($row = $stmt_callout_colors->fetch(PDO::FETCH_ASSOC)) {
                        $colors[$row['shipment_status']] = $row['color'];
                    }

                    while ($data = $stmt_get_cards -> fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="callout" style="border-left-color: <?php echo $colors[$data['shipment_status']] ?? $colors['default']; ?>;">
                    <div class="container">
                        <?php
                            if ($data['confirm_departure'] == '0') {
                        ?>
                        <p class="badge" style="color:#fff; background-color:#dc3545">NOT YET CONFIRMED</p>
                        <?php
                            }   
                        ?>
                        <div class="row mb-2">
                            <div class="col-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input ck-blnumber" id="<?php echo $data['bl_number']; ?>">
                                    <label class="form-check-label" for="<?php echo $data['bl_number']; ?>"><h4><?php echo $data['bl_number']; ?></h4></label>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4><?php echo $data['forwarder_name']; ?></h4>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <?php
                                    if (isset($data['ata_mnl'])) {
                                        echo "ATA: " . substr($data['ata_mnl'], 0, 10);
                                    } elseif (isset($data['eta_mnl'])) {
                                        echo "ETA: " . substr($data['eta_mnl'], 0, 10);
                                    } else {
                                        echo 'TBA';
                                    }
                                ?>
                            </div>
                            <div class="col-6">
                                <?php echo $data['commercial_invoice']; ?>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <span style="font-size:75%;font-weight:700;border-radius:.25rem;padding:.25em .4em;color:#fff;background-color:<?php echo $colors[$data['shipment_status']] ?? $colors['default']; ?>"><?php echo $data['shipment_status']; ?></span>
                            </div>
                            <div class="col-6">
                                <?php echo $data['commodity']; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <a class="collapsed text-primary" id="<?php echo $data['bl_number']; ?>" data-toggle="collapse" href="#viewmore-<?php echo $data['bl_number']; ?>" onclick="load_containers(this)">
                                    View More
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="container collapse mt-2" id="viewmore-<?php echo $data['bl_number']; ?>">
                        <div class="row">
                            <table class="table table-head-fixed text-nowrap table-hover">
                                <thead>
                                    <tr style="border-bottom:1px solid black">
                                        <th>CONTAINER NO.</th>
                                        <th>R. DELIVERY</th>
                                        <th>T. DELIVERY</th>
                                        <th>TABS</th>
                                    </tr>
                                </thead>
                                <tbody id="table-<?php echo $data['bl_number']; ?>">
                                    <tr>
                                        <td>DATA</td>
                                        <td>DATA</td>
                                        <td>DATA</td>
                                        <td>DATA</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
                    }//ends the while loop above
                ?>
            </div>
        </div>
        <div class="col-4">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Status Filter</h3>
                </div>
                <div class="card-body" id="FilterContent">
                    <?php
                        $sql = "EXEC GetFiltersThisMonth :StartYear, :StartMonth"; //mssql server stored procedure
                        $stmt = $conn -> prepare($sql);
                        $stmt -> bindParam(':StartYear', $year);
                        $stmt -> bindParam(':StartMonth', $month);
                        $stmt -> execute();

                        while ($filter_option = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                            $color = $colors[$filter_option['shipment_status']] ?? $colors['default'];
                            echo <<<HTML
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input ck-shipment-status" onchange="filter_shipment(this)" id="{$filter_option['shipment_status']}">
                                    <label class="form-check-label" for="{$filter_option['shipment_status']}">{$filter_option['shipment_status']}&nbsp;<span class="right badge" style="color:#fff;background-color:{$color};">{$filter_option['count']}</span></label>
                                </div>
                            HTML;
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
            console.log('show');
            document.getElementById('HistoricalAlert').style.display = 'block';
        } else {
            console.log('hide');
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
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
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