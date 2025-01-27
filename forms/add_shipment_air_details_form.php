<form id="AddAirShipmentForm">
    <div class="row mb-1">
        <h3>Shipment Details</h3>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>FORWARDER</label>
            <input type="text" class="form-control" name="forwarder" required>
        </div>
        <div class="col-3">
            <label>ORIGIN</label>
            <input type="text" class="form-control" name="origin" required>
        </div>
        <div class="col-3">
            <label>HAWB / AWB</label>
            <input type="text" class="form-control" name="hawb_awb" required>
        </div>
        <div class="col-3">
            <label>ETA</label>
            <input type="date" class="form-control" name="eta">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>GROSS WEIGHT (KG)</label>
            <input type="number" step="0.01" class="form-control" name="gross_weight" required>
        </div>
        <div class="col-3">
            <label>CHARGEABLE WEIGHT (KG)</label>
            <input type="number" step="0.01" class="form-control" name="chargeable_weight" required>
        </div>
        <div class="col-3">
            <label>NO. OF PACKAGES</label>
            <input type="text" class="form-control" name="no_packages" required>
        </div>
        <div class="col-3">
            <label>COMMODITY</label>
            <input type="text" class="form-control" name="commodity" required>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-6">
            <label>INVOICE NO.</label>
            <input type="text" class="form-control" name="invoice_no" required>
        </div>
        <div class="col-3">
            <label>CLASSIFICATION</label>
            <select class="form-control" required name="classification">
                <option selected value="">Select Commodity</option>
                <option>RAW MATERIALS</option>
                <option>OTHERS</option>
            </select>
        </div>
        <div class="col-3">
            <label>INCOTERM</label>
            <input type="text" class="form-control" name="incoterm" required>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-3">
            <label>SHIPMENT STATUS</label>
            <input type="text" class="form-control" name="shipment_status" required>
        </div>
        <div class="col-3">
            <label>SHIPMENT STATUS PROGRESS</label>
            <select class="form-control" name="shipment_status_progress" required>
                <option disabled value="" selected>Select Shipment Progress</option>
                <option>ACTIVE</option>
                <option>FOR RELEASE</option>
                <option>DELIVERED</option>
            </select>
        </div>
    </div>
    <div class="row mb-1">
        <div class="col-3">
            <h3>Delivery Details</h3>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>TENTATIVE DELIVERY SCHEDULE</label>
            <input type="date" class="form-control" name="tentative_delivery_schedule">
        </div>
        <div class="col-3">
            <label>REQUIRED DELIVERY SCHEDULE</label>
            <input type="date" class="form-control" name="required_delivery">
        </div>
        <div class="col-3">
            <label>ACTUAL DATE OF DELIVERY</label>
            <input type="date" class="form-control" name="actual_date_of_delivery">
        </div>
        <div class="col-3">
            <label>TIME RECEIVED</label>
            <input type="text" class="form-control" name="time_received">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>RECEIVED BY</label>
            <input type="text" class="form-control" name="received_by">
        </div>
    </div>

    <div class="row">
        <div class="col-4 mx-auto mt-3">
            <button type="submit" class="btn bg-primary btn-block">Create Shipment</button>
        </div>
    </div>
</form>

<script>
    $('#AddAirShipmentForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        // console.log(formData);

        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/add_shipment_air_details.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.notification) {
                    Toast.fire({
		                icon: response.notification.icon,
		                title: response.notification.text,
	                })
                }

                if (response.added) {
                    setTimeout(function() {
                        location.reload();
                    }, 2000); // 2000 milliseconds = 2 seconds
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
                alert("An error occurred while processing your request. Please try again later.");
            }
        });
    });
</script>