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

<div class="container-fliud">
    <div class="row">
        <div class="col-3">
            <div class="card p-2 d-flex flex-column">
                <div>
                    <label>SHIPPING LINE</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-info" onclick="make_any(this)">ANY</button>
                        </div>
                        <input type="text" class="form-control" name="shipping_line" required autocomplete="off">
                    </div>
                </div>
                <div>
                    <label>ORIGIN PORT</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-info" onclick="make_any(this)">ANY</button>
                        </div>
                        <input type="text" class="form-control" name="origin_port" required autocomplete="off">
                    </div>
                </div>
                <div>
                    <label>DESTINATION PORT</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-info" onclick="make_any(this)">ANY</button>
                        </div>
                        <input type="text" class="form-control" name="destination_port" required autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card p-2">
                <label>TYPE OF TRANSACTION</label>
                <select class="form-control" name="type_transaction" onchange="load_details_of_charge(this.value)">
                    <option value="" selected disabled required>Select an Option</option>
                    <option>IMPORT SEA</option>
                    <option>IMPORT AIR</option>
                    <option>EXPORT SEA</option>
                    <option>EXPORT AIR</option>
                </select>
            </div>
        </div>
        <div class="col-4">
            <div class="card p-2">
                <label>DETAIL OF CHARGE</label>
                <select class="form-control" name="type_transaction" id="select_details_of_charge">
                    <option value="" selected disabled required>WAITING FOR TYPE</option>
                </select>
            </div>
        </div>
        <div class="col-2 d-flex justify-content-center align-items-start">
            <button type="button" class="btn btn-block btn-info">PULL DATA</button>
        </div>
    </div>
</div>

<div class="container-fluid card" style="min-height:70vh;"></div>

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

    function load_details_of_charge(type) {
        $.ajax({
            type: 'GET', // or 'GET' depending on your needs
            url: '../php_api/billing_monitoring_computation/load_details_of_charge.php',
            data: {
                'type_of_transaction' : type,
            },
            dataType: 'json',
            success: function(response) {
                document.getElementById('select_details_of_charge').innerHTML = response.select;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
                alert("An error occurred while processing your request. Please try again later.");
            }
        });
    }
    
    function make_any(button) {
        const input = button.closest('.input-group').querySelector('input');
        // Check if the input exists
        if (input) {
            // Set the value to 'ANY'
            input.value = 'ANY';
        }
    }
</script>