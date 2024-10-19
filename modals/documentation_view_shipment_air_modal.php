<!-- Modal -->
<div class="modal fade" id="documentation_view_shipment_air_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body container">
                <div class="row">
                    <div class="col-6">
                        <h6><span class="badge badge-info" style="font-size:125%;" id="hawb_awb_display">BL_NUMBER</span></h6>
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" class="btn btn-flat text-danger" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card card-secondary card-tabs" id="container-card-information">
                            <div class="card-header p-0 pt-1">
                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="pill" href="#ShipmentAirTabDiv" role="tab">Shipment</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="pill" href="#DeliveryAirTabDiv" role="tab">Delivery</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="pill" href="#HistoryAirTabDiv" role="tab">History</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                    <div class="tab-pane fade active show" id="ShipmentAirTabDiv" role="tabpanel">
                                        <form id="ShipmentAirEditForm">
                                            <div class="container" id="AirShipmentDetailsContent">
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="DeliveryAirTabDiv" role="tabpanel">
                                        <form id="DeliveryAirEditForm">
                                            <div class="container" id="AirDeliveryDetailsContent">
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="HistoryAirTabDiv" role="tabpanel" style="max-height:300px;overflow-y:auto;">
                                        <table id="" class="table table-head-fixed table-hover mb-4">
                                            <thead class="text-nowrap">
                                                <tr style="border-bottom:1px solid black">
                                                    <th>DATE MODIFIED</th>
                                                    <th>USERNAME</th>
                                                    <th>DATA</th>
                                                    <th style="background-color: #ffcecb;">CHANGED FROM</th>
                                                    <th style="background-color: #d1f8d9;">CHANGED TO</th>
                                                </tr>
                                            </thead>
                                            <tbody id="HistoryContent">
                                                <th colspan="4" class="text-muted text-center">Make a selection to view its data</th>
                                            </tbody>
                                        </table>
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
    //detail dump
    function edit_shipment_information(initiator) {
        console.log(initiator.id);
        document.getElementById('hawb_awb_display').innerHTML = initiator.id;
        $.ajax({
            url: '../php_api/air_shipment_details_dump.php',
            type: 'GET',
            data: {
                'hawb_awb' : initiator.id,
            },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                document.getElementById('AirShipmentDetailsContent').innerHTML = response.inner_html_shipment;
                document.getElementById('AirDeliveryDetailsContent').innerHTML = response.inner_html_delivery;
                document.getElementById('HistoryContent').innerHTML = response.inner_html_history;
            }
        });
    }

    //form submit ajax, two of them for the forms shipment and delivery
    $('#ShipmentAirEditForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_shipment_air_details.php', // Replace with your server endpoint
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.notification) {
                    Toast.fire({
		                icon: response.notification.icon,
		                title: response.notification.text,
	                })
                }
                search_documentation(false);
            },
        });
    });
    $('#DeliveryAirEditForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_delivery_air_details.php', // Replace with your server endpoint
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.notification) {
                    Toast.fire({
		                icon: response.notification.icon,
		                title: response.notification.text,
	                })
                }
                search_documentation(false);
            },
        });
    });
</script>