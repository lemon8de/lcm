<!-- Modal -->
<form action="../../process/alert_table_click_modal_api.php" method="POST"></form>
<div class="modal fade" id="documentation_view_shipment_sea_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-body container">
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
                  <!-- <li class="nav-item">
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
                  </li> -->
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
function loaddata(row) {
        var value = row.getAttribute('id');
        var shipment_details_ref = value;
        console.log(shipment_details_ref);
        var container = row.getAttribute('data-value');
        document.getElementById('TabTitle').innerHTML = "&nbsp;" + container + "&nbsp;";

        $.ajax({
            url: '../php_api/detailsdump.php',
            type: 'GET',
            data: {
                'shipment_details_ref' : shipment_details_ref,
            },
            dataType: 'json',
            success: function (response) {
                if (response.history) {
                    document.getElementById('HistoryContent').innerHTML = response.history;
                    //console.log(response)
                } else {
                    document.getElementById('HistoryContent').innerHTML = "";
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