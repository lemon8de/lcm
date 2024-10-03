<div class="container">
<form id="">
    <div class="container">
        <div class="row mb-3">
            <div class="col-12 d-flex">
                <div class="btn-group">
                    <button type="button" class="btn btn-success" onclick="select_all_bl(this)">Select&nbsp;All</button>
                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" role="menu" style="">
                        <a class="dropdown-item" href="#"></a>
                        <a class="dropdown-item" onclick="confirm_shipment()">Confirm Shipment</a>
                        <a class="dropdown-item" onclick="delete_shipment()">Delete Shipment</a>
                    </div>
                </div>
                <input class="form-control ml-3 w-25" placeholder="BL NUMBER" name="bl_number" onkeyup="debounce(import_search, 350)">
            </div>
        </div>
    </div>
</form>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-8">
            <div class="container" id="DocumentationMainContainer">
            <div class="callout callout-danger">
                <div class="container">
                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input ck-blnumber" id="bl_number">
                                <label class="form-check-label" for="bl_number"><h4>BL NUMBER</h4></label>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4>SWC</h4>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            08.10.2024
                        </div>
                        <div class="col-6">
                            ASDFASDFAd, ASDFASDF, ASDFASDFASDF, ASDFASDFASDFASDF, ASDFASDFASDFASDF, ASDFASDFAd, ASDFASDF, ASDFASDFASDF, ASDFASDFASDFASDF, ASDFASDFASDFASDF 
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            DELIVERED
                        </div>
                        <div class="col-6">
                            RAW MATERIALS
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <a class="collapsed text-primary" data-toggle="collapse" href="#viewmore-bl_number">
                                View more Details
                            </a>
                        </div>
                    </div>
                </div>
                <div class="container collapse mt-2" id="viewmore-bl_number">
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
                            <tbody id="OutgoingSearchTableBody">
                                <tr>
                                    <td>ABCDEFGHIJK</td>
                                    <td>08/12/2024</td>
                                    <td>08/12/2024</td>
                                    <td>2300H</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="callout callout-danger">
                <h5>I am a danger callout!</h5>
                <p>There is a problem that we need to fix. A wonderful serenity has taken possession of my entire</p>
            </div>
            <div class="callout callout-danger">
                <h5>I am a danger callout!</h5>
                <p>There is a problem that we need to fix. A wonderful serenity has taken possession of my entire</p>
            </div>
            <div class="callout callout-danger">
                <h5>I am a danger callout!</h5>
                <p>There is a problem that we need to fix. A wonderful serenity has taken possession of my entire</p>
            </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Shipment Status Filter</h3>
                </div>
                <div class="card-body">
                    <?php
                        $sql = "SELECT distinct a.shipment_status, count(*) as count from m_shipment_sea_details as a left join m_completion_details as b on a.shipment_details_ref = b.shipment_details_ref where confirm_departure = 0 or b.actual_received_at_falp is null group by a.shipment_status";
                        $stmt = $conn -> prepare($sql);
                        $stmt -> execute();

                        while ($filter_option = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                            echo <<<HTML
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="{$filter_option['shipment_status']}">
                                    <label class="form-check-label" for="{$filter_option['shipment_status']}">{$filter_option['shipment_status']}&nbsp;<span class="right badge badge-danger">{$filter_option['count']}</span></label>
                                </div>
                            HTML;
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<h3>-------</h3>
<form id="DocumentationSearchForm">
    <div class="container">
        <div class="row mb-3">
            <div class="col-4">
                <input class="form-control" placeholder="BL NUMBER" name="bl_number" onkeyup="debounce(import_search, 350)">
            </div>
            <div class="col-4">
                <input class="form-control" placeholder="CONTAINER" name="container" onkeyup="debounce(import_search, 350)">
            </div>
            <div class="col-4">
                <input class="form-control" placeholder="INVOICE" name="commercial_invoice" onkeyup="debounce(import_search, 350)">
            </div>
        </div>
    </div>
</form>
<div class="container table-responsive">
    <?php include '../php_static/content_tables/incoming_sea_confirm.php';?>
</div>

<script>
    function confirm_shipment() {
        console.log('test');
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
        const checkboxes = document.querySelectorAll('.ck-blnumber');
        checkboxes.forEach(checkbox => {
            checkbox.checked = ck_bl_status;
        });
        ck_bl_status = !ck_bl_status;
    }

    function import_search() {
        var formData = $('#DocumentationSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/search_documentation.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                document.getElementById('ConfirmIncomingSeaTableBody').innerHTML = response.inner_html;
            },
        });
    }
</script>