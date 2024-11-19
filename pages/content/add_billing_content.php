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
                    <div class="forwarder_tile d-flex justify-content-center align-items-center" style="height:120px;border:1px solid black;border-radius:10px" onclick="clicked_forwarder(this)" id="{$data['forwarder_partner']}" data-fwdcode="{$data['billing_forwarder_details_ref']}">
                        <img src="{$data['forwarder_logo']}" alt="Forwarder TCL" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                    </div>
                </div>
            HTML;
        }
    ?>
    <!-- <div class="col-3 mb-2" style="cursor:pointer;">
        <div class="forwarder_tile d-flex justify-content-center align-items-center" style="height:120px;border:1px solid black;border-radius:10px" onclick="clicked_forwarder(this)">
            <i class="fas fa-plus" style="font-size:250%;"></i>
        </div>
    </div> -->
    </div>
</div>

<div class="container-fliud">
    <div class="row">
        <div class="col-3">
            <div class="card p-2">
                <label>TYPE OF TRANSACTION</label>
                <select class="form-control" id="type_of_transaction_select" name="type_transaction" onchange="load_details_of_charge(this.value)">
                    <option value="" selected disabled required>Select an Option</option>
                    <option>IMPORT SEA</option>
                    <option>IMPORT AIR</option>
                    <option>EXPORT SEA</option>
                    <option>EXPORT AIR</option>
                </select>
            </div>
        </div>
        <div class="col-3">
            <div class="card p-2 d-flex flex-column">
                <div>
                    <label>SHIPPING LINE</label>
                    <div class="input-group">
                        <!-- <div class="input-group-prepend">
                            <button type="button" class="btn btn-info" onclick="make_any(this)">ANY</button>
                        </div> -->
                        <input type="text" class="form-control" id="shipping_line_input" name="shipping_line" required autocomplete="off">
                    </div>
                </div>
                <div>
                    <label>ORIGIN PORT</label>
                    <div class="input-group">
                        <!-- <div class="input-group-prepend">
                            <button type="button" class="btn btn-info" onclick="make_any(this)">ANY</button>
                        </div> -->
                        <input type="text" class="form-control" id="origin_port_input" name="origin_port" required autocomplete="off">
                    </div>
                </div>
                <div>
                    <label>DESTINATION PORT</label>
                    <div class="input-group">
                        <!-- <div class="input-group-prepend">
                            <button type="button" class="btn btn-info" onclick="make_any(this)">ANY</button>
                        </div> -->
                        <input type="text" class="form-control" id="destination_port_input" name="destination_port" required autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
        <div class="card p-2 col-4 d-flex flex-column">
            <div>
                <label>DETAIL OF CHARGE</label>
                <select class="form-control" name="detail_of_charge_set" id="select_details_of_charge">
                    <option value="" selected disabled> </option>
                </select>
            </div>

            <label>ADD NEW CHARGE</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <input type="checkbox" id="new_charge_ck" onchange="toggle_detail_of_charge()">
                    </span>
                </div>
                <input type="text" id="new_charge_input" class="form-control" name="detail_of_charge_new" disabled>
            </div>
            <div>
                <label>BASIS OF NEW CHARGE</label>
                <select class="form-control" name="basis_new" id="new_basis_select" disabled>
                    <option value="" disabled selected> </option>
                    <?php 
                        $sql = "SELECT distinct basis from m_billing_information";
                        $stmt = $conn -> query($sql);
                        while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                            echo <<<HTML
                                <option>{$data['basis']}</option>
                            HTML;
                        }
                    ?>
                </select>
            </div>
            <div>
                <label>CHARGE GROUP</label>
                <select class="form-control" name="charge_group_new" id="new_charge_group_select" disabled>
                    <option value="" disabled selected> </option>
                    <option>LOCAL CHARGES</option>
                    <option>ACCESSORIAL</option>
                    <option>REIMBURSEMENT</option>
                </select>
            </div>
            <div>
                <label>CURRENCY</label>
                <select class="form-control" name="currency_new" id="new_currency_select" disabled>
                    <option value="" disabled selected> </option>
                    <option>PHP</option>
                    <option>USD</option>
                </select>
            </div>
        </div>
        <div class="col-2 d-flex justify-content-center align-items-start">
            <button type="button" class="btn btn-block btn-info" onclick="generate_form()" id="generate_form_button">GENERATE FORM</button>
        </div>
    </div>
</div>

<div class="container-fluid card p-3">
    <div class="d-flex align-items-stretch h-100"> <!-- Use d-flex and align-items-stretch -->
        <div class="col-6">
            <div id="MainEditContainer">
            </div>
            <!-- Your content here -->
            <form id="GeneratedFormSubmit">
            </form>
        </div>
        <div class="col-6 d-flex justify-content-center align-items-center"> <!-- Center the canvas -->
            <canvas id="myChart" style="max-width:100%;height:auto;"></canvas> <!-- Responsive canvas -->
        </div>
    </div>
</div>
<script>
  const ctx = document.getElementById('myChart');
  const myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: [],
      datasets: [{
        label: 'CHARGE RATES',
        data: [],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
<script>
    let forwarder_selected = "";
    let forwarder_code_selected = "";
    function clicked_forwarder(initiator) {
        forwarders = document.querySelectorAll('.forwarder_tile');
        forwarders.forEach(forwarder => {
            if (forwarder !== initiator) {
                forwarder.style.border = "1px solid black";
            } else {
                forwarder.style.border = "4px solid black";
                forwarder_selected = initiator.id;
                forwarder_code_selected = initiator.getAttribute('data-fwdcode');
            }
        });
        console.log(forwarder_selected)
        console.log(forwarder_code_selected)
    }

    function load_details_of_charge(type) {
        if (type.includes("IMPORT")) {
            document.getElementById('destination_port_input').disabled = true;
            document.getElementById('destination_port_input').value = "";
            document.getElementById('origin_port_input').disabled = false;
        } else if (type.includes("EXPORT")) {
            document.getElementById('origin_port_input').disabled = true;
            document.getElementById('origin_port_input').value = "";
            document.getElementById('destination_port_input').disabled = false;
        }
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
        if (!input.disabled) {
            // Set the value to 'ANY'
            input.value = 'ANY';
        }
    }
    function toggle_detail_of_charge() {
        const checkbox = document.getElementById('new_charge_ck');
        const input_new = document.getElementById('new_charge_input');
        const input_set = document.getElementById('select_details_of_charge');
        const basis_new = document.getElementById('new_basis_select');
        const charge_group_new = document.getElementById('new_charge_group_select');
        const currency_new = document.getElementById('new_currency_select');
        input_new.disabled = !checkbox.checked;
        basis_new.disabled = !checkbox.checked;
        charge_group_new.disabled = !checkbox.checked;
        currency_new.disabled = !checkbox.checked;
        input_set.disabled = checkbox.checked;
    }
    function generate_form() {
        data = {
            'type_of_transaction' : document.getElementById('type_of_transaction_select').value,
            'forwarder' : forwarder_selected,
            'billing_forwarder_details_ref' : forwarder_code_selected,
            'shipping_line' : document.getElementById('shipping_line_input').value,
            'origin_port' : document.getElementById('origin_port_input').value,
            'destination_port' : document.getElementById('destination_port_input').value,
            'billing_details_ref' : document.getElementById('select_details_of_charge').value,
            'wants_new' : document.getElementById('new_charge_ck').checked,
            'new_charge' : document.getElementById('new_charge_input').value,
            'new_basis' : document.getElementById('new_basis_select').value,
            'new_charge_group' : document.getElementById('new_charge_group_select').value,
            'new_currency' : document.getElementById('new_currency_select').value,
        };
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/billing_monitoring_computation/billing_edit_generate_form.php',
            data: data,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (!response.exited) {
                    document.getElementById('MainEditContainer').innerHTML = response.info;
                    document.getElementById('GeneratedFormSubmit').innerHTML = response.inner_html;
                    myChart.data.labels = response.labels;
                    myChart.data.datasets[0].data = response.dataset;
                    myChart.update();

                    if (response.new_generated) {
                        document.getElementById('select_details_of_charge').disabled = true;
                        document.getElementById('new_charge_ck').disabled = true;
                        document.getElementById('new_charge_input').disabled = true;
                        document.getElementById('generate_form_button').disabled = true;

                        document.getElementById('new_basis_select').disabled = true;
                        document.getElementById('new_charge_group_select').disabled = true;
                        document.getElementById('new_currency_select').disabled = true;
                    }
                } else {
                    Toast.fire({
		                icon: "warning",
		                title: "Parameters deemed incomplete",
	                })
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
                alert("An error occurred while processing your request. Please try again later.");
            }
        });
    }

    $('#GeneratedFormSubmit').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        console.log(formData);
        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/billing_monitoring_computation/save_billing_compute.php', // Replace with your server endpoint
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.notification) {
                    Toast.fire({
		                icon: response.notification.icon,
		                title: response.notification.text,
	                })
                }
            },
        });
    });
</script>