<!-- Modal -->
<div class="modal fade" id="edit_outgoing_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-body container">
        <div class="card card-secondary card-tabs">
          <div class="card-header p-0 pt-1">
              <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                  <li class="nav-item">
                      <a class="nav-link active" data-toggle="pill" href="FISBDiv" role="tab">FSIB</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="VesselDetailsDiv" role="tab">Vessel</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="InvoiceDetailsDiv" role="tab">Invoice</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="ContainerDetailsDiv" role="tab">Container</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="DispatchingDetailsDiv" role="tab">Dispatching</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="ContLineUpDiv" role="tab">Cont Line Up</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="BLDetailsDiv" role="tab">BL Details</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="RTVDiv" role="tab">RTV</a>
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
        console.log(outgoing_details_ref);
        //var container = row.getAttribute('data-value');
        //document.getElementById('TabTitle').innerHTML = "&nbsp;" + container + "&nbsp;";

        //$.ajax({
            //url: '../php_api/detailsdump.php',
            //type: 'GET',
            //data: {
                //'shipment_details_ref' : shipment_details_ref,
            //},
            //dataType: 'json',
            //success: function (response) {
                //if (response.history) {
                    //document.getElementById('HistoryContent').innerHTML = response.history;
                    ////console.log(response)
                //} else {
                    //document.getElementById('HistoryContent').innerHTML = "";
                //}
                //if (response.shipment) {
                    //document.getElementById('ShipmentDetailsContent').innerHTML = response.shipment;
                    ////console.log(response)
                //} else {
                    //document.getElementById('ShipmentDetailsContent').innerHTML = "<center><span class='text-muted'>NO DATA</span></center>";
                //}
                //if (response.vessel) {
                    //document.getElementById('VesselDetailsContent').innerHTML = response.vessel;
                    ////console.log(response)
                //} else {
                    //document.getElementById('VesselDetailsContent').innerHTML = "<center><span class='text-muted'>NO DATA</span></center>";
                //}
            //}
        //});
    }
</script>