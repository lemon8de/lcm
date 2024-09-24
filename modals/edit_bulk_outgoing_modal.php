<!-- Modal -->
<div class="modal fade" id="editb_outgoing_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-body container">
        <div class="card card-secondary card-tabs">
          <div class="card-header p-0 pt-1">
              <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                  <!-- <li class="nav-item">
                      <a class="nav-link active" data-toggle="pill" href="#FSIBDiv" role="tab">FSIB</a>
                  </li> -->
                  <li class="nav-item">
                      <a class="nav-link active" data-toggle="pill" href="#VesselDetailsDivb" role="tab">Vessel</a>
                  </li>
                  <!-- <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#InvoiceDetailsDiv" role="tab">Invoice</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#ContainerDetailsDiv" role="tab">Container</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#DispatchingDetailsDiv" role="tab">Dispatching</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#ContLineUpDiv" role="tab">Cont Line Up</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#BLDetailsDiv" role="tab">BL Details</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#RTVDiv" role="tab">RTV</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#HistoryTabDiv" role="tab">History</a>
                  </li> -->
              </ul>
            </div>  
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <!-- <div class="tab-pane fade active show" id="FSIBDiv" role="tabpanel">
                        <?php include '../php_static/content_tables/outgoing_fsib.php';?>
                    </div> -->
                    <div class="tab-pane fade active show" id="VesselDetailsDivb" role="tabpanel">
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
                    <!-- <div class="tab-pane fade" id="InvoiceDetailsDiv" role="tabpanel">
                        <?php include '../php_static/content_tables/outgoing_invoice.php';?>
                    </div>
                    <div class="tab-pane fade" id="ContainerDetailsDiv" role="tabpanel">
                        <?php include '../php_static/content_tables/outgoing_container.php';?>
                    </div>
                    <div class="tab-pane fade" id="DispatchingDetailsDiv" role="tabpanel">
                        <?php include '../php_static/content_tables/outgoing_dispatch.php';?>
                    </div>
                    <div class="tab-pane fade" id="ContLineUpDiv" role="tabpanel">
                        <?php include '../php_static/content_tables/outgoing_contlineup.php';?>
                    </div>
                    <div class="tab-pane fade" id="BLDetailsDiv" role="tabpanel">
                        <?php include '../php_static/content_tables/outgoing_bl.php';?>
                    </div>
                    <div class="tab-pane fade" id="RTVDiv" role="tabpanel">
                        <?php include '../php_static/content_tables/outgoing_rtv.php';?>
                    </div>
                    <div class="tab-pane fade" id="HistoryTabDiv" role="tabpanel">
                        <div style="max-height: 300px; overflow-y: auto;">
                            <?php include '../php_static/content_tables/history.php';?>
                        </div>
                    </div> -->
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
$(document).ready(function() {
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
});
</script>