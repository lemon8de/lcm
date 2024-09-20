<form action="../php_api/add_outgoing_shipment.php" method="POST">
    <div class="row mb-1">
        <h3>Outgoing Details</h3>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>INVOICE NO.</label>
            <input type="text" class="form-control" name="invoice_no" required>
        </div>
        <div class="col-3">
            <label>CONTAINER NO.</label>
            <input type="text" class="form-control" name="container_no" required>
        </div>
        <div class="col-3">
            <label>Destination (Service Center)</label>
            <input type="text" class="form-control" name="destination_service_center" required>
        </div>
        <div class="col-3">
            <label>Ship Out Date</label>
            <input type="date" class="form-control" name="ship_out_date" required>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>NO. OF PALLETS</label>
            <input type="number" class="form-control" name="no_pallets" required>
        </div>
        <div class="col-3">
            <label>NO. OF CARTONS</label>
            <input type="number" class="form-control" name="no_cartons" required>
        </div>
        <div class="col-3">
            <label>PACK QTY</label>
            <input type="number" class="form-control" name="pack_qty" required>
        </div>
        <div class="col-3">
            <label>INVOICE AMOUNT</label>
            <input type="number" step="0.01" class="form-control" name="invoice_amount" required>
        </div>
    </div>
    <div class="row">
        <div class="col-4 mx-auto mt-3">
            <button type="submit" class="btn bg-primary btn-block">Add</button>
        </div>
    </div>
</form>