<!-- Modal -->
<div class="modal fade" id="edit_outgoing_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-body container">
        <h4>FSIB</h4>
        <div class="card card-secondary card-tabs">
          <div class="card-header p-0 pt-1">
              <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                  <li class="nav-item">
                      <a class="nav-link active" data-toggle="pill" href="#EditOutgoingForms" role="tab">Edit</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#HistoryTabDiv" role="tab">History</a>
                  </li>
              </ul>
            </div>  
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <div class="tab-pane fade active show" id="EditOutgoingForms" role="tabpanel">
                        <h4 class="font-weight-bold">FSIB</h4>
                        <div id="FSIBDiv">
                            <?php include '../php_static/content_tables/outgoing_fsib.php';?>
                        </div>
                        <div style="height: 5px; background-color: #6c757d;"></div>
                        <h4 class="font-weight-bold">Vessel</h4>
                        <div id="VesselDetailsDiv">
                            <?php include '../php_static/content_tables/outgoing_vessel.php';?>
                        </div>
                        <div style="height: 5px; background-color: #6c757d;"></div>
                        <h4 class="font-weight-bold">Invoice</h4>
                        <div id="InvoiceDetailsDiv">
                            <?php include '../php_static/content_tables/outgoing_invoice.php';?>
                        </div>
                        <div style="height: 5px; background-color: #6c757d;"></div>
                        <h4 class="font-weight-bold">Container</h4>
                        <div id="ContainerDetailsDiv">
                            <?php include '../php_static/content_tables/outgoing_container.php';?>
                        </div>
                        <div style="height: 5px; background-color: #6c757d;"></div>
                        <h4 class="font-weight-bold">Dispatching</h4>
                        <div id="DispatchingDetailsDiv">
                            <?php include '../php_static/content_tables/outgoing_dispatch.php';?>
                        </div>
                        <div style="height: 5px; background-color: #6c757d;"></div>
                        <h4 class="font-weight-bold">Container Line Up</h4>
                        <div id="ContLineUpDiv">
                            <?php include '../php_static/content_tables/outgoing_contlineup.php';?>
                        </div>
                        <div style="height: 5px; background-color: #6c757d;"></div>
                        <h4 class="font-weight-bold mb-0">BL</h4>
                        <div id="BLDetailsDiv">
                            <?php include '../php_static/content_tables/outgoing_bl.php';?>
                        </div>
                        <div style="height: 5px; background-color: #6c757d;"></div>
                        <h4 class="font-weight-bold">RTV</h4>
                        <div id="RTVDiv">
                            <?php include '../php_static/content_tables/outgoing_rtv.php';?>
                        </div>
                        <div style="height: 5px; background-color: #6c757d;"></div>
                        <h4 class="font-weight-bold">Status / Co-Status</h4>
                        <div id="StatusDiv">
                            <?php include '../php_static/content_tables/outgoing_status.php';?>
                        </div>
                        <div style="height: 5px; background-color: #6c757d;"></div>
                        <div class="d-flex flex-row justify-content-center mt-3">
                            <button class="btn btn-primary" onclick="outgoing_modal_form_submit_all()">Update ALL</button>
                        </div>
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
    function outgoing_modal_form_submit_all() {
        $('#OutgoingFSIBForm').submit();
        $('#OutgoingVesselForm').submit();
        $('#OutgoingInvoiceForm').submit();
        $('#OutgoingContainerForm').submit();
        $('#OutgoingDispatchForm').submit();
        $('#OutgoingContLineUpForm').submit();
        $('#OutgoingBLForm').submit();
        $('#OutgoingRTVForm').submit();
        $('#OutgoingStatusForm').submit();
    }

    var outgoing_details_ref_focus = null;
    $('#edit_outgoing_modal').on('hidden.bs.modal', function () {
        // Your custom function here
        outgoing_search();
    });
    function loaddata(auto_update = false) {
        // auto update for clicking update button on all outgoing edit forms
        if (auto_update) {
            var outgoing_details_ref = outgoing_details_ref_focus;
        } else {
            var outgoing_details_ref = this.id;
            outgoing_details_ref_focus = this.id;
        }

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
        if (initiator.value == "RECEIVED") {
            document.getElementById('co_status_select').disabled = false;
        } else {
            document.getElementById('co_status_select').disabled = true;
        }
    }
    
    function find_similar_vessels(input) {
        $.ajax({
            url: '../php_api/outgoing_find_similar_vessels.php',
            type: 'GET',
            data: {
                'vessel_name' : input.value,
            },
            dataType: 'json',
            success: function (response) {
                if (!response.exited) {
                    document.getElementById("vessel_shipping_line").value = response.shipping_line;
                    document.getElementById("vessel_etd_mnl").value = response.etd_mnl;
                    //17 january outgoing vessel, eta update should not propagate, and add mode of shipment to the fetch mix
                    //document.getElementById("vessel_eta_destination").value = response.eta_destination;
                    document.getElementById("vessel_mode_of_shipment").value = response.mode_of_shipment;
                    document.getElementById("VesselDetailsEditToolTipInfo").innerHTML = `
                        <div class='container-fluid alert alert-info'>
                            <i class='icon fas fa-info'></i>This invoice will share details with invoices: ${response.list_of_invoices}
                        </div>
                    `;
                } else {
                    document.getElementById("VesselDetailsEditToolTipInfo").innerHTML = "";
                }
            }
        });
    }

    // auto updating form made this function obselete
    // function check_bl_lock(item_id) {
    //     $.ajax({
    //         type: 'GET',
    //         url: '../php_api/check_bl_lock.php',
    //         data: {
    //             "outgoing_details_ref" : item_id,
    //         },
    //         dataType: 'json',
    //         success: function(response) {
    //             if (response.notification) {
    //                 Toast.fire({
	// 	                icon: response.notification.icon,
	// 	                title: response.notification.text,
	//                 })
    //             }
    //             if (response.locked == "lock") {
    //                 document.getElementById('lock-bl_number').disabled = true;
    //                 document.getElementById('lock-bl_date').disabled = true;
    //                 document.getElementById('lock-bl_update').disabled = true;
    //             } else {
    //                 document.getElementById('lock-bl_number').disabled = false;
    //                 document.getElementById('lock-bl_date').disabled = false;
    //                 document.getElementById('lock-bl_update').disabled = false;
    //             }
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
    //             alert("An error occurred while processing your request. Please try again later.");
    //         }
    //     });
    // }
</script>