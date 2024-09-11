<div class="container">
    <div class="row">
        <div class="col-3">
            <div class="row">
                <div class="col-12">
                <input class="form-control" id="ContainerInput" placeholder="Search By Container" onkeyup="debounce(searchContainer, 150)" autocomplete="off">
                </div>
            </div>
            <div class="row">
                <div class="col-12" style="max-height:400px;overflow-y:auto;">
                    <table class="table table-head-fixed text-nowrap table-hover mb-4">
                        <thead>
                            <tr>
                                <th>CONTAINER</th>
                                <th>BL</th>
                            </tr>
                        </thead>
                        <tbody id="ContainerSearch">
                        </tbody>
                    </table>
                </div>
            </div>
        </div> 
        <div class="col-9">
            <div class="card card-secondary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        <li class="pt-2 px-3"><h3 class="card-title text-primary bg-warning" id="TabTitle">&nbsp;Container&nbsp;</h3></li>
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#ShipmentTabDiv" role="tab">Shipment</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#VesselTabDiv" role="tab">Vessel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#DeliveryTabDiv" role="tab">Delivery</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#CompletionTabDiv" role="tab">Completion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#PolytainerTabDiv" role="tab">Polytainer</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#MMTabDiv" role="tab">MM</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#HistoryTabDiv" role="tab">History</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade active show" id="ShipmentTabDiv" role="tabpanel">
                            <?php include '../php_static/content_tables/shipment_details.php';?>
                        </div>
                        <div class="tab-pane fade" id="VesselTabDiv" role="tabpanel">
                            <?php include '../php_static/content_tables/vessel_details.php';?>
                        </div>
                        <div class="tab-pane fade" id="DeliveryTabDiv" role="tabpanel">
                            <?php include '../php_static/content_tables/delivery_plan.php';?>
                        </div>
                        <div class="tab-pane fade" id="CompletionTabDiv" role="tabpanel">
                            <?php include '../php_static/content_tables/completion_details.php';?>
                        </div>
                        <div class="tab-pane fade" id="PolytainerTabDiv" role="tabpanel">
                            <?php include '../php_static/content_tables/polytainer_details.php';?>
                        </div>
                        <div class="tab-pane fade" id="MMTabDiv" role="tabpanel">
                            <?php include '../php_static/content_tables/mm_system.php';?>
                        </div>
                        <div class="tab-pane fade" id="HistoryTabDiv" role="tabpanel">
                            <div style="max-height: 300px; overflow-y: auto;">
                                <?php include '../php_static/content_tables/history.php';?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function debounce(method, delay) {
        clearTimeout(method._tId);
        method._tId = setTimeout(function() {
            method();
        }, delay);
    }

    function searchContainer() {
        $.ajax({
            url: '../php_api/search_container.php',
            type: 'GET',
            data: {
                'container' : document.getElementById('ContainerInput').value,
            },
            dataType: 'json',
            success: function (response) {
                document.getElementById('ContainerSearch').innerHTML = response.inner_html;
            }
        });
    }

    //function loaddata() {
    function loaddata(row) {
        var value = row.getAttribute('data-value');
        var shipment_details_ref = value;
        document.getElementById('TabTitle').innerHTML = "&nbsp;" + row.getAttribute('id') + "&nbsp;";

        $.ajax({
            url: '../php_api/detailsdump.php',
            type: 'GET',
            data: {
                'shipment_details_ref' : shipment_details_ref,
            },
            dataType: 'json',
            success: function (response) {
                if (response.delivery_plan) {
                    document.getElementById('DeliveryPlanContent').innerHTML = response.delivery_plan;
                    //console.log(response)
                } else {
                    document.getElementById('DeliveryPlanContent').innerHTML = "<center><span class='text-muted'>NO DATA</span></center>";
                }
                if (response.completion_details) {
                    document.getElementById('CompletionContent').innerHTML = response.completion_details;
                    //console.log(response)
                } else {
                    document.getElementById('CompletionContent').innerHTML = "<center><span class='text-muted'>NO DATA</span></center>";
                }
                if (response.polytainer_details) {
                    document.getElementById('PolytainerDetailsContent').innerHTML = response.polytainer_details;
                    //console.log(response)
                } else {
                    document.getElementById('PolytainerDetailsContent').innerHTML = "<center><span class='text-muted'>NO DATA</span></center>";
                }
                if (response.mmsystem_details) {
                    document.getElementById('MMDetailsContent').innerHTML = response.mmsystem_details;
                    //console.log(response)
                } else {
                    document.getElementById('MMDetailsContent').innerHTML = "<center><span class='text-muted'>NO DATA</span></center>";
                }
                if (response.history) {
                    document.getElementById('HistoryContent').innerHTML = response.history;
                    //console.log(response)
                } else {
                    document.getElementById('HistoryContent').innerHTML = "<center><span class='text-muted'>NO DATA</span></center>";
                }
                if (response.shipment) {
                    document.getElementById('ShipmentDetailsContent').innerHTML = response.shipment;
                    //console.log(response)
                } else {
                    document.getElementById('ShipmentDetailsContent').innerHTML = "<center><span class='text-muted'>NO DATA</span></center>";
                }
                if (response.vessel) {
                    document.getElementById('VesselDetailsContent').innerHTML = response.vessel;
                    //console.log(response)
                } else {
                    document.getElementById('VesselDetailsContent').innerHTML = "<center><span class='text-muted'>NO DATA</span></center>";
                }
            }
        });
    }
</script>