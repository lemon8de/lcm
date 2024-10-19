<!-- Modal -->
<div class="modal fade" id="documentation_view_invoice_modal_air" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                    <div class="col-3">
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
                    <div class="col-9" style="height:400px;" id="choose_invoice_window">
                        <div class="container d-flex justify-content-center align-items-center h-100">
                            <h5 class="text-secondary">CHOOSE AN INVOICE</h5>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="card card-secondary card-tabs" id="container-card-information-invoice" style="display:block;">
                            <div class="card-header p-0 pt-1">
                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="pill" href="#GeneralInvoice" role="tab">General</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="pill" href="#SpecificInvoice" role="tab">Invoice</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="pill" href="#HistoryTabDivInvoice" role="tab">History</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                    <div class="tab-pane fade active show" id="GeneralInvoice" role="tabpanel">
                                        <form id="GeneralInvoiceForm">
                                            <div class="container" id="GeneralInvoiceContent">
                                                <div class="row">
                                                    <div class="col-12 text-center">
                                                        <span class="text-muted">Make a selection to view its data.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="SpecificInvoice" role="tabpanel">
                                        <form id="InvoiceSpecificForm">
                                            <div class="container" id="InvoiceSpecificContent">
                                                <div class="row">
                                                    <div class="col-12 text-center">
                                                        <span class="text-muted">Make a selection to view its data.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="HistoryTabDivInvoice" role="tabpanel">
                                        <div style="max-height: 300px; overflow-y: auto;">
                                            <table class="table table-head-fixed table-hover mb-4">
                                                <thead>
                                                    <tr style="border-bottom:1px solid black">
                                                        <th>DATE MODIFIED</th>
                                                        <th>USERNAME</th>
                                                        <th>DATA</th>
                                                        <th style="background-color: #ffcecb;">CHANGED FROM</th>
                                                        <th style="background-color: #d1f8d9;">CHANGED TO</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="HistoryContentInvoice">
                                                    <th colspan="5" class="text-muted text-center">Make a selection to view its data</th>
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
</div>

<script>
    $('#GeneralInvoiceForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_import_data.php', // Replace with your server endpoint
            data: formData,
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

    $('#InvoiceSpecificForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_import_data.php', // Replace with your server endpoint
            data: formData,
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

    function edit_invoice_information(initiator) {
        document.getElementById('display_invoice').style.display = 'none';
        document.getElementById('container-card-information-invoice').style.display = "none";
        document.getElementById('choose_invoice_window').style.display = "block";

        document.getElementById('invoice_bl_number_display').innerHTML = initiator.getAttribute('data-nameinvoice');
        $.ajax({
            url: '../php_api/get_documentation_invoice_list_air.php',
            type: 'GET',
            data: {
                'shipment_details_ref' : initiator.id,
            },
            dataType: 'json',
            success: function (response) {
                document.getElementById('InvoiceSearch').innerHTML = response.inner_html;
            }
        });
    }

    function edit_invoice_focus(initiator) {
        invoice_tabs = document.querySelectorAll('.edit_invoice-tab');
        document.getElementById('display_invoice').innerHTML = initiator.textContent;
        document.getElementById('display_invoice').style.display = "inline-block";
        document.getElementById('choose_invoice_window').style.display = "none";
        document.getElementById('container-card-information-invoice').style.display = "block";

        invoice_tabs.forEach(invoice => {
            if (invoice !== initiator) {
                invoice.style.backgroundColor = 'transparent';
                invoice.style.color = 'black';
            } else {
                invoice.style.backgroundColor = '#28a745';
                invoice.style.color = 'black';
            }
        });

        //old code still on the codebase but not needed for now
        //console.log(initiator.getAttribute('data-id'));
        get_invoice_details_api(initiator.id);
    }

    function get_invoice_details_api(shipping_invoice) {
        $.ajax({
            url: '../php_api/invoice_detailsdump_air.php',
            type: 'GET',
            data: {
                'shipping_invoice' : shipping_invoice,
            },
            dataType: 'json',
            success: function (response) {
                document.getElementById('InvoiceSpecificContent').innerHTML = response.inner_html_invoice;
                document.getElementById('GeneralInvoiceContent').innerHTML = response.inner_html_general;
                document.getElementById('HistoryContentInvoice').innerHTML = response.inner_html_history;
            }
        });
    }
</script>