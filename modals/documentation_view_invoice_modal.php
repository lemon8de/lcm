<!-- Modal -->
<div class="modal fade" id="documentation_view_invoice_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body container">
                <div class="row">
                    <div class="col-6">
                        <h6><span class="badge badge-info" style="font-size:125%;" id="invoice_bl_number_display">BL_NUMBER</span>&nbsp;<i class="fas fa-caret-right"></i>&nbsp;<span class="badge badge-success" style="font-size:125%;display:none;" id="display_invoice">INVOICE</span></h6>
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" class="btn btn-flat text-danger" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="container" style="height:400px;overflow-y:auto;">
                            <table class="table table-head-fixed text-nowrap table-hover mb-4">
                                <thead class="text-center">
                                    <tr>
                                        <th>INVOICE</th>
                                    </tr>
                                </thead>
                                <tbody id="InvoiceSearch" class="text-center">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-8" style="height:400px;" id="choose_invoice_window">
                        <div class="container d-flex justify-content-center align-items-center h-100">
                            <h5 class="text-secondary">CHOOSE AN INVOICE</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function edit_invoice_information(initiator) {
        //document.getElementById('display_container').style.display = 'none';
        //document.getElementById('container-card-information').style.display = "none";
        //document.getElementById('choose_container_window').style.display = "block";

        document.getElementById('invoice_bl_number_display').innerHTML = initiator.id;
        $.ajax({
            url: '../php_api/get_documentation_invoice_list.php',
            type: 'GET',
            data: {
                'bl_number' : initiator.id,
            },
            dataType: 'json',
            success: function (response) {
                document.getElementById('InvoiceSearch').innerHTML = response.inner_html;
            }
        });
    }

    function edit_invoice_focus(initiator) {
        invoice_tabs = document.querySelectorAll('.edit_invoice-tab');
        //document.getElementById('display_container').innerHTML = initiator.textContent;
        //document.getElementById('display_container').style.display = "inline-block";
        //document.getElementById('choose_container_window').style.display = "none";

        //document.getElementById('container-card-information').style.display = "block";

        invoice_tabs.forEach(invoice => {
            if (invoice !== initiator) {
                invoice.style.backgroundColor = 'transparent';
                invoice.style.color = 'black';
            } else {
                invoice.style.backgroundColor = '#28a745';
                invoice.style.color = 'black';
            }
        });
        //get_container_details_api(initiator.id);
    }
</script>