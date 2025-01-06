<!-- Modal -->
<div class="modal fade" id="editb_outgoing_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-body container">
        <div class='container-fluid alert alert-info'>
            <i class='icon fas fa-info'></i>Editing all selected items.
        </div>
        <div class="card card-secondary card-tabs">
          <div class="card-header p-0 pt-1">
              <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                  <li class="nav-item">
                      <a class="nav-link active" data-toggle="pill" href="#FSIBDivb" role="tab">FSIB</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#VesselDetailsDivb" role="tab">Vessel</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#InvoiceDetailsDivb" role="tab">Invoice</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#ContainerDetailsDivb" role="tab">Container</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#DispatchingDetailsDivb" role="tab">Dispatching</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#ContLineUpDivb" role="tab">Cont Line Up</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#BLDetailsDivb" role="tab">BL Details</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#RTVDivb" role="tab">RTV</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#StatusDivb" role="tab">Status</a>
                  </li>
              </ul>
            </div>  
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <div class="tab-pane fade active show" id="FSIBDivb" role="tabpanel">
                        <form id="OutgoingFSIBFormb">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>INVOICE NO.</label>
                                    <input type="text" class="form-control" name="invoice_no">
                                </div>
                                <div class="col-6">
                                    <label>CONTAINER NO.</label>
                                    <input type="text" class="form-control" name="container_no" pattern=".{11}">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>DESTINATION (SERVICE CENTER)</label>
                                    <input type="text" class="form-control" name="destination_service_center">
                                </div>
                                <div class="col-6">
                                    <label>DESTINATION</label>
                                    <input type="text" class="form-control" name="destination">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>CAR MODEL</label>
                                    <input type="text" class="form-control" name="car_model">
                                </div>
                                <div class="col-6">
                                    <label>SHIP OUT DATE</label>
                                    <input type="date" class="form-control" name="ship_out_date">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>NO. PALLETS</label>
                                    <input type="number" class="form-control" name="no_pallets">
                                </div>
                                <div class="col-6">
                                    <label>NO. CARTONS</label>
                                    <input type="number" class="form-control" name="no_cartons">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>PACK QTY.</label>
                                    <input type="number" class="form-control" name="pack_qty">
                                </div>
                                <div class="col-6">
                                    <label>INVOICE AMOUNT</label>
                                    <input type="number" class="form-control" name="invoice_amount">
                                </div>
                            </div>
                            <div class="row mb-2 d-flex align-items-center">
                                <div class="col-3 ml-auto">
                                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="VesselDetailsDivb" role="tabpanel">
                        <form id="OutgoingVesselFormb">
                            <div class="container">
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <label>MODE OF SHIPMENT</label>
                                        <input type="text" class="form-control" name="mode_of_shipment">
                                    </div>
                                    <div class="col-6">
                                        <label>VESSEL NAME</label>
                                        <input type="text" class="form-control" name="vessel_name">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <label>SHIPPING LINE</label>
                                        <input type="text" class="form-control" name="shipping_line">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <label>ETD MNL</label>
                                        <input type="date" class="form-control" name="etd_mnl">
                                    </div>
                                    <div class="col-6">
                                        <label>ETA DESTINATION</label>
                                        <input type="date" class="form-control" name="eta_destination">
                                    </div>
                                </div>
                                <div class="row mb-2 d-flex align-items-center">
                                    <div class="col-3 ml-auto">
                                        <button type="submit" class="btn bg-primary btn-block">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="InvoiceDetailsDivb" role="tabpanel">
                        <form id="OutgoingInvoiceDetailsFormb">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>SHIPPING TERMS</label>
                                    <select class="form-control" name="shipping_terms">
                                        <option value="" disabled selected>Shipping Term</option>
                                        <?php
                                            $sql = "SELECT shipping_terms from m_shipping_terms order by id asc";
                                            $stmt = $conn -> query($sql);
                                            $shipping_terms_options = '';
                                            while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                                                $shipping_terms_options .= <<<HTML
                                                    <option>{$data['shipping_terms']}</option>
                                                HTML;
                                            }
                                            echo $shipping_terms_options;
                                        ?>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label>NET WEIGHT</label>
                                    <input type="number" step="0.0001" class="form-control" name="net_weight">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>GROSS WEIGHT</label>
                                    <input type="number" step="0.0001" class="form-control" name="gross_weight">
                                </div>
                                <div class="col-6">
                                    <label>CBM</label>
                                    <input type="number" step="0.0001" class="form-control" name="cbm">
                                </div>
                            </div>
                            <div class="row mb-2 d-flex align-items-center">
                                <div class="col-3 ml-auto">
                                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="ContainerDetailsDivb" role="tabpanel">
                        <form id="OutgoingContainerDetailsFormb">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>FALP IN / REUSE</label>
                                    <input type="date" class="form-control" name="falp_in_reuse">
                                </div>
                                <div class="col-6">
                                    <label>STATUS OF CONTAINER</label>
                                    <input type="text" class="form-control" name="status_of_container">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>CONTAINER SIZE</label>
                                    <input type="text" class="form-control" name="container_size">
                                </div>
                                <div class="col-6">
                                    <label>FORWARDER</label>
                                    <select class="form-control" name="forwarder">
                                        <option value="" disabled selected>Forwarder</option>
                                        <?php
                                            //19 december make dropdown
                                            $sql = "SELECT forwarder_partner from m_billing_forwarder order by id asc";
                                            $stmt = $conn -> query($sql);
                                            $forwarder_options = '';
                                            while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                                                $forwarder_options .= <<<HTML
                                                    <option>{$data['forwarder_partner']}</option>
                                                HTML;
                                            }
                                            echo $forwarder_options;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2 d-flex align-items-center">
                                <div class="col-3 ml-auto">
                                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="DispatchingDetailsDivb" role="tabpanel">
                        <form id="OutgoingDispatchingDetailsFormb">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>ED REFERENCE</label>
                                    <input type="text" class="form-control" name="ed_reference">
                                </div>
                                <div class="col-6">
                                    <label>SHIPPING SEAL</label>
                                    <input type="text" class="form-control" name="shipping_seal">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>PEZA SEAL</label>
                                    <input type="text" class="form-control" name="peza_seal">
                                </div>
                            </div>
                            <div class="row mb-2 d-flex align-items-center">
                                <div class="col-3 ml-auto">
                                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="ContLineUpDivb" role="tabpanel">
                        <form id="OutgoingContLineUpFormb">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>FALP OUT DATE</label>
                                    <input type="date" class="form-control" name="falp_out_date">
                                </div>
                                <div class="col-6">
                                    <label>FALP OUT TIME</label>
                                    <input type="text" class="form-control" name="falp_out_time">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>TRUCK HEAD STATUS</label>
                                    <input type="text" class="form-control" name="truckhead_status">
                                </div>
                            </div>
                            <div class="row mb-2 d-flex align-items-center">
                                <div class="col-3 ml-auto">
                                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="BLDetailsDivb" role="tabpanel">
                        <form id="OutgoingBLDetailsFormb">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>BL DATE</label>
                                    <input type="date" class="form-control" name="bl_date">
                                </div>
                                <div class="col-6">
                                    <label>BL NUMBER</label>
                                    <input type="text" class="form-control" name="bl_number">
                                </div>
                            </div>
                            <div class="row mb-2 d-flex align-items-center">
                                <div class="col-3 ml-auto">
                                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="RTVDivb" role="tabpanel">
                        <form id="OutgoingRTVFormb">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>IRREGULAR SHIPMENT (DEPARTMENT)</label>
                                    <input type="text" class="form-control" name="irregular_shipment">
                                </div>
                            </div>
                            <div class="row mb-2 d-flex align-items-center">
                                <div class="col-3 ml-auto">
                                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="StatusDivb" role="tabpanel">
                        <form id="OutgoingStatusFormb">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label>STATUS</label>
                                    <select class="form-control" name="status">
                                        <option value="" selected>N/A</option>
                                        <option>FOR REQUEST</option>
                                        <option>RECEIVED</option>
                                        <option>ONGOING</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label>CO STATUS</label>
                                    <select class="form-control" id="co_status_select" name="co_status">
                                        <option value="" selected>N/A</option>
                                        <option>FOR REQUEST</option>
                                        <option>COMPLETE</option>
                                        <option>ONGOING</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2 d-flex align-items-center">
                                <div class="col-3 ml-auto">
                                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
    $('#editb_outgoing_modal').on('hidden.bs.modal', function () {
        // Your custom function here
        outgoing_search();
    });
    $('#OutgoingFSIBFormb').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        var additionalData = $.param({ 'outgoing_details_ref': selectedIds });
        var combinedData = formData + '&' + additionalData;
        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/outgoing_bulk_edits/fsib.php', // Replace with your server endpoint
            data: combinedData,
            dataType: 'json',
            success: function(response) {
                if (response.notification) {
                    Toast.fire({
		                icon: response.notification.icon,
		                title: response.notification.text,
	                })
                }
            },
        });
    });

    $('#OutgoingVesselFormb').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        var additionalData = $.param({ 'outgoing_details_ref': selectedIds });
        var combinedData = formData + '&' + additionalData;
        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_outgoing_vessel.php', // Replace with your server endpoint
            data: combinedData,
            dataType: 'json',
            success: function(response) {
                if (response.notification) {
                    Toast.fire({
		                icon: response.notification.icon,
		                title: response.notification.text,
	                })
                }
            },
        });
    });
</script>