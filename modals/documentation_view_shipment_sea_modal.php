<!-- Modal -->
<div class="modal fade" id="documentation_view_shipment_sea_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body container">
                <div class="row">
                    <div class="col-6">
                        <h6><span class="badge badge-info" style="font-size:125%;" id="bl_number_display">BL_NUMBER</span>&nbsp;<i class="fas fa-caret-right"></i>&nbsp;<span class="badge badge-warning" style="font-size:125%;display:none;" id="display_container">CONTAINER</span></h6>
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" class="btn btn-flat text-danger" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <div class="container" style="height:400px;overflow-y:auto;">
                            <table class="table table-head-fixed text-nowrap table-hover mb-4">
                                <thead class="text-center">
                                    <tr>
                                        <th>CONTAINER</th>
                                    </tr>
                                </thead>
                                <tbody id="ContainerSearch" class="text-center">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-9" style="height:400px;" id="choose_container_window">
                        <div class="container d-flex justify-content-center align-items-center h-100">
                            <h5 class="text-secondary">CHOOSE A CONTAINER</h5>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="card card-secondary card-tabs" id="container-card-information" style="display:none;">
                            <div class="card-header p-0 pt-1">
                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
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
        </div>
    </div>
</div>

<script>
    var incoming_sea_edit_container_focus_code = null;
    function edit_container_focus(initiator) {
        container_tabs = document.querySelectorAll('.edit_container-tab');
        document.getElementById('display_container').innerHTML = initiator.textContent;
        document.getElementById('display_container').style.display = "inline-block";
        document.getElementById('choose_container_window').style.display = "none";

        document.getElementById('container-card-information').style.display = "block";

        container_tabs.forEach(container => {
            if (container !== initiator) {
                container.style.backgroundColor = 'transparent';
                container.style.color = 'black';
            } else {
                container.style.backgroundColor = '#ffc107';
                container.style.color = 'black';
            }
        });
        incoming_sea_edit_container_focus_code = initiator.id;
        get_container_details_api(initiator.id);
    }

    function get_container_details_api(shipment_details_ref) {
        //revision to let the update button on all form tables to reload the form
        if (shipment_details_ref == null) {
            shipment_details_ref = incoming_sea_edit_container_focus_code;
        }
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
                    document.getElementById('HistoryContent').innerHTML = "<th colspan='5' class='text-muted text-center'>NO DATA</th>";
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
    
    function edit_container_information(initiator) {
        //need to hide a few things,
        document.getElementById('display_container').style.display = 'none';
        document.getElementById('container-card-information').style.display = "none";
        document.getElementById('choose_container_window').style.display = "block";

        document.getElementById('bl_number_display').innerHTML = initiator.id;
        $.ajax({
            url: '../php_api/get_documentation_container_list.php',
            type: 'GET',
            data: {
                'bl_number' : initiator.id,
            },
            dataType: 'json',
            success: function (response) {
                document.getElementById('ContainerSearch').innerHTML = response.inner_html;
                //what the fuck is this? not needed
                //document.getElementById('display_container').innerHTML = response.container;
            }
        });
    }
</script>