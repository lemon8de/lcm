<div class="card container-fluid p-4">
    <h3>Choose a Forwarder</h3>
    <div class="row">
    <?php
        $sql = "SELECT * from m_billing_forwarder";
        $stmt = $conn -> prepare($sql);
        $stmt -> execute();

        while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            echo <<<HTML
                <div class="col-3 mb-2" style="cursor:pointer;">
                    <div class="forwarder_tile d-flex justify-content-center align-items-center" style="height:120px;border:1px solid black;border-radius:10px" onclick="clicked_forwarder(this)" id="{$data['billing_forwarder_details_ref']}">
                        <img src="{$data['forwarder_logo']}" alt="Forwarder TCL" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                    </div>
                </div>
            HTML;
        }
    ?>
    </div>
</div>

<div class="card container-fluid p-4">
    <h3>Fill Information</h3>
    <div class="row mb-2">
        <div class="col-3">
            <label>SHIPPING LINE</label>
            <input type="text" class="form-control" name="shipping_line" required>
        </div>
        <div class="col-3">
            <label>ORIGIN PORT</label>
            <input type="text" class="form-control" name="origin_port" required>
        </div>
        <div class="col-3">
            <label>DESTINATION PORT</label>
            <input type="text" class="form-control" name="destination_port" required>
        </div>
        <div class="col-3">
            <label>TYPE OF TRANSACTION</label>
            <select class="form-control" name="type_transaction">
                <option value="" selected disabled required>Select an Option</option>
                <option>IMPORT SEA</option>
                <option>IMPORT AIR</option>
                <option>EXPORT SEA</option>
                <option>EXPORT AIR</option>
            </select>
        </div>
    </div>
</div>

<script>
    let forwarder_selected = "";
    function clicked_forwarder(initiator) {
        forwarders = document.querySelectorAll('.forwarder_tile');
        forwarders.forEach(forwarder => {
            if (forwarder !== initiator) {
                forwarder.style.border = "1px solid black";
            } else {
                forwarder.style.border = "4px solid black";
                forwarder_selected = initiator.id;
            }
        });
        console.log(forwarder_selected)
    }
</script>