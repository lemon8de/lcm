<!-- Modal -->
<div class="modal fade" id="edit_outgoing_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-body container">
        <div class="card card-secondary card-tabs">
          <div class="card-header p-0 pt-1">
              <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                  <li class="nav-item">
                      <a class="nav-link active" data-toggle="pill" href="#FSIBDiv" role="tab">FSIB</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#VesselDetailsDiv" role="tab">Vessel</a>
                  </li>
                  <li class="nav-item">
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
                      <a class="nav-link" data-toggle="pill" href="#StatusDiv" role="tab">Status</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#HistoryTabDiv" role="tab">History</a>
                  </li>
              </ul>
            </div>  
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <div class="tab-pane fade active show" id="FSIBDiv" role="tabpanel">
                        <?php include '../php_static/content_tables/outgoing_fsib.php';?>
                    </div>
                    <div class="tab-pane fade" id="VesselDetailsDiv" role="tabpanel">
                        <?php include '../php_static/content_tables/outgoing_vessel.php';?>
                    </div>
                    <div class="tab-pane fade" id="InvoiceDetailsDiv" role="tabpanel">
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
                    <div class="tab-pane fade" id="StatusDiv" role="tabpanel">
                        <?php include '../php_static/content_tables/outgoing_status.php';?>
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
</form>

<script>
function loaddata() {
        var outgoing_details_ref = this.id;

        $.ajax({
            url: '../php_api/outgoing_detailsdump.php',
            type: 'GET',
            data: {
                'outgoing_details_ref' : outgoing_details_ref,
            },
            dataType: 'json',
            success: function (response) {
                document.getElementById('OutgoingFSIBContent').innerHTML = response.outgoing_fsib;
                document.getElementById('OutgoingVesselContent').innerHTML = response.outgoing_vessel;
                document.getElementById('OutgoingInvoiceContent').innerHTML = response.outgoing_invoice;
                document.getElementById('OutgoingContainerContent').innerHTML = response.outgoing_container;
                document.getElementById('OutgoingDispatchContent').innerHTML = response.outgoing_dispatch;
                document.getElementById('OutgoingContLineUpContent').innerHTML = response.outgoing_contlineup;
                document.getElementById('OutgoingBLContent').innerHTML = response.outgoing_bl;
                document.getElementById('OutgoingRTVContent').innerHTML = response.outgoing_rtv;
                document.getElementById('OutgoingStatusContent').innerHTML = response.outgoing_status;
                document.getElementById('HistoryContent').innerHTML = response.history;
            }
        });
    }

    function check_co_status(initiator) {
        console.log(initiator.value);
        if (initiator.value == "RECEIVED") {
            document.getElementById('co_status_select').disabled = false;
        } else {
            document.getElementById('co_status_select').disabled = true;
        }
    }
</script>