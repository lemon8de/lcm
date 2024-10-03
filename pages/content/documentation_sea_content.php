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
                <input class="form-control ml-3 w-25" placeholder="BL NUMBER" name="bl_number" id="bl_number_search" onkeyup="debounce(search_bl_number, 350)">
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-8">
            <div class="container" id="DocumentationMainContainer">
                <?php
                    $sql = "SELECT distinct bl_number, max(forwarder_name) as forwarder_name, max(commercial_invoice) as commercial_invoice, max(shipment_status) as shipment_status, max(commodity) as commodity, max(eta_mnl) as eta_mnl, max(ata_mnl) as ata_mnl, min(confirm_departure) as confirm_departure from m_shipment_sea_details as a left join m_vessel_details as b on a.shipment_details_ref = b.shipment_details_ref group by bl_number;";
                    $stmt_get_cards = $conn -> prepare($sql);
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
                                <?php echo $data['shipment_status']; ?>
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
                <div class="card-body">
                    <?php
                        $sql = "SELECT distinct a.shipment_status, count(*) as count from m_shipment_sea_details as a left join m_completion_details as b on a.shipment_details_ref = b.shipment_details_ref where confirm_departure = 0 or b.actual_received_at_falp is null group by a.shipment_status";
                        $stmt = $conn -> prepare($sql);
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
    function confirm_shipment() {
        console.log('test');
    }

    function search_bl_number() {
        ck_bl_status = true;
        //you cant search for shipment status when you do a bl search,
        checkboxes = document.querySelectorAll('.ck-shipment-status');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        $.ajax({
            url: '../php_api/search_documentation.php',
            type: 'GET',
            data: {
                "bl_number" : document.getElementById('bl_number_search').value
            },
            dataType: 'json',
            success: function (response) {
                document.getElementById("DocumentationMainContainer").innerHTML = response.inner_html;
            }
        });
    }

    function filter_shipment(initiator) {
        ck_bl_status = true;
        //you cant search for bl number and status at the same time, it will be lowkey retarded
        document.getElementById('bl_number_search').value = "";
        checkboxes = document.querySelectorAll('.ck-shipment-status');
        checkboxes.forEach(checkbox => {
            if (checkbox != initiator) {
                checkbox.checked = false;
            }
        });
        //now build the formdata and make the ajax request
        data = {"shipment_status" : ""};
        checkboxes.forEach(checkbox => {
            if (checkbox.checked == true) {
                data = {
                    "shipment_status" : checkbox.id
                };
                //something with illegal bulshit idk this commented out will work fine
                //break;
            }
        });
        $.ajax({
            url: '../php_api/search_documentation.php',
            type: 'GET',
            data: data,
            dataType: 'json',
            success: function (response) {
                document.getElementById("DocumentationMainContainer").innerHTML = response.inner_html;
            }
        });
    }

    function delete_shipment() {
        console.log('test2');
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