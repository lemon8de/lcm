<form id="AddSeaShipmentForm">
    <div class="row mb-1">
        <h3>Shipment Details</h3>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>BL NUMBER</label>
            <input type="text" class="form-control" name="bl_number" required>
        </div>
        <div class="col-6">
            <label>COMMERCIAL INVOICE</label>
            <input type="text" class="form-control" name="commercial_invoice" required>
        </div>
        <div class="col-3">
            <label>COMMODITY</label>
            <select name="commodity" class="form-control" required>
                <option value="" disabled selected>Select Commodity</option>
                <?php 
                    $sql = "SELECT method, value, display_name from list_commodity where method = 'sea' order by display_name asc";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo <<<HTML
                            <option value="{$row['value']}">{$row['display_name']}</option>
                        HTML;
                    }
                ?>
            </select>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>SHIPPING LINES</label>
            <input type="text" class="form-control" name="shipping_lines" required>
        </div>
        <div class="col-3">
            <label>FORWARDER'NAME</label>
            <input type="text" class="form-control" name="forwarder_name" required>
        </div>
        <div class="col-3">
            <label>ORIGIN PORT</label>
            <input type="text" class="form-control" name="origin_port" required>
        </div>
        <div class="col-3">
            <label>DESTINATION PORT</label>
            <input type="text" class="form-control" name="destination_port" required>
        </div>
    </div>
    <div class="row mb-4" id="TypeOfShipmentDropOff">
        <div class="col-3">
            <label>SHIPMENT STATUS</label>
            <select name="shipment_status" class="form-control" required>
                <option value="" disabled selected>Select Status</option>
                <option>Waiting for Validated Manifest</option>
                <option>Waiting for PEZA Endorsement</option>
                <option>Documents at the Manifest</option>
                <option>Gate Pass Available</option>
            </select>
        </div>
        <div class="col-3">
            <label>TSAD NUMBER</label>
            <input type="text" class="form-control" name="tsad_number" required>
        </div>
        <div class="col-3">
            <label>TYPE OF SHIPMENT</label>
            <select class="form-control" name="type_of_shipment" required onchange="assess_shipment(this)">
                <option>LCL</option>
                <option>FCL</option>
            </select>
        </div>
    </div>
    <div class="row mb-1" id="AddContainerButtonDropOff">
        <div class="col-3">
            <h3>Container Details</h3>
        </div>
    </div>
    <div id="MoreContainerLogs">
        <div class="row mb-2">
            <div class="col-3">
                <label>CONTAINER</label>
                <input type="text" class="form-control containers" required pattern=".{11}">
            </div>
            <div class="col-3">
                <label>CONTAINER SIZE / CBM</label>
                <input type="text" class="form-control container_sizes" required>
            </div>
        </div>
    </div>

    <div class="row mb-1">
        <div class="col-3">
            <h3>Vessel Details</h3>
        </div>
        <div class="col-9" id="VesselDetailsToolTip" style="display:none;">
            <div class="alert alert-info" id="ToolTipInfo">
            </div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label>VESSEL NAME</label>
            <input type="text" id="VesselDetailsName" class="form-control" name="vessel_name" required onkeyup="debounce(find_similar, 150)">
        </div>
        <div class="col-3">
            <label>ETA MNL (YYY/MM/DD)</label>
            <input type="date" id="VesselDetailsETA" class="form-control" name="eta_mnl">
        </div>
        <div class="col-3">
            <label>ATA MNL (YYYY/MM/DD)</label>
            <input type="date" id="VesselDetailsATA" class="form-control" name="ata_mnl">
        </div>
        <div class="col-3">
            <label>ATB(YYYY/MM/DD)</label>
            <input type="date" id="VesselDetailsATB" class="form-control" name="atb">
        </div>
    </div>
    <div class="row">
        <div class="col-4 mx-auto mt-3">
            <button type="submit" class="btn bg-primary btn-block">Create Shipment</button>
        </div>
    </div>
</form>

<script>
    function assess_shipment(selectElement) {
        if (selectElement.value == "FCL") {
            //make div
            newDiv = document.createElement('div');
            newDiv.className = 'col-3'; // Set the class for the div
            //make label
            newLabel = document.createElement('label');
            newLabel.textContent = "CONTAINER SIZE";
            //make select
            select = document.createElement('select');
            select.className = 'form-control';
            select.name = 'container_size_suffix';
            select.required = true;
            //create options
            options = ['x20', 'x40'];
            options.forEach(optionText => {
                option = document.createElement('option');
                option.value = optionText; // Set the value of the option
                option.textContent = optionText; // Set the display text of the option
                select.appendChild(option); // Append the option to the select
            });
            //assemble
            container = document.getElementById('TypeOfShipmentDropOff');
            newDiv.appendChild(newLabel);
            newDiv.appendChild(select);
            container.appendChild(newDiv);

            //make add new button
            addButton = document.createElement('button');
            addButton.className = "btn bg-info btn-block";
            addButton.type = "button";
            addButton.textContent = "Add";
            addButton.onclick = function() {add_another_container(); };
            //make remove button
            removeButton = document.createElement('button');
            removeButton.className = "btn bg-danger btn-block";
            removeButton.type = "button";
            removeButton.textContent = "Remove";
            removeButton.onclick = function() {remove_container(); };
            //div for button
            newDiv = document.createElement('div');
            newDiv.className = 'col-3'; // Set the class for the div
            //assemble
            container = document.getElementById('AddContainerButtonDropOff');
            newDiv.appendChild(addButton);
            container.appendChild(newDiv);
            newDiv = document.createElement('div');
            newDiv.className = 'col-3'; // Set the class for the div
            newDiv.appendChild(removeButton);
            container.appendChild(newDiv);
        } else {
            location.reload();
        }
    }

    function add_another_container() {
        // Create the row container
        const rowDiv = document.createElement('div');
        rowDiv.className = 'row mb-2';
        // Create the "CONTAINER" section
        const containerDiv = document.createElement('div');
        containerDiv.className = 'col-3';
        const containerLabel = document.createElement('label');
        containerLabel.textContent = 'CONTAINER';
        const containerInput = document.createElement('input');
        containerInput.type = 'text';
        containerInput.className = 'form-control containers';
        containerInput.required = true;
        containerInput.pattern = ".{11}";
        containerDiv.appendChild(containerLabel);
        containerDiv.appendChild(containerInput);
        // Create the "CONTAINER SIZE / CBM" section
        const sizeDiv = document.createElement('div');
        sizeDiv.className = 'col-3';
        const sizeLabel = document.createElement('label');
        sizeLabel.textContent = 'CONTAINER SIZE / CBM';
        const sizeInput = document.createElement('input');
        sizeInput.type = 'text';
        sizeInput.className = 'form-control container_sizes';
        sizeInput.required = true;
        sizeDiv.appendChild(sizeLabel);
        sizeDiv.appendChild(sizeInput);
        // Append both sections to the row container
        rowDiv.appendChild(containerDiv);
        rowDiv.appendChild(sizeDiv);
        // Assuming you have a parent container to append this to
        const parentContainer = document.getElementById('MoreContainerLogs'); // Replace with your actual parent container ID
        parentContainer.appendChild(rowDiv);
    }

    function remove_container() {
        // Select the parent container
        const parentContainer = document.getElementById('MoreContainerLogs');

        // Remove the last child
        if (parentContainer.children.length > 1) {
            parentContainer.removeChild(parentContainer.lastChild);
        }
    }

    function debounce(method, delay) {
        clearTimeout(method._tId);
        method._tId = setTimeout(function() {
            method();
        }, delay);
    }
    function find_similar() {
        $.ajax({
            url: '../php_api/search_similar_vessels.php',
            type: 'GET',
            data: {
                'vessel_name' : document.getElementById('VesselDetailsName').value,
            },
            dataType: 'json',
            success: function (response) {
                if (response.exists) {
                    document.getElementById('VesselDetailsETA').value = response.eta_mnl;
                    document.getElementById('VesselDetailsATA').value = response.ata_mnl;
                    document.getElementById('VesselDetailsATB').value = response.atb;
                    document.getElementById('VesselDetailsToolTip').style.display = 'block';
                    document.getElementById('ToolTipInfo').innerHTML = response.info_html;
                } else {
                    document.getElementById('VesselDetailsToolTip').style.display = 'none';
                    document.getElementById('VesselDetailsETA').value = '';
                    document.getElementById('VesselDetailsATA').value = '';
                    document.getElementById('VesselDetailsATB').value = '';
                }
            }
        });
    }
    
    $('#AddSeaShipmentForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();

        // Create an array to hold the values
        var containerSizesValues = [];
        var containersValues = [];

        //get the dynamic container and container_sizes
        $('.container_sizes').each(function() {
            // Get the value of the current input
            var value = $(this).val();
            // Add the value to the list if it's not empty
            if (value) {
                containerSizesValues.push(value);
            }
        });
        $('.containers').each(function() {
            // Get the value of the current input
            var value = $(this).val();
            // Add the value to the list if it's not empty
            if (value) {
                containersValues.push(value);
            }
        });
        // Convert arrays to query string format
        var containerSizesParam = $.param({ container_sizes: containerSizesValues });
        var containersParam = $.param({ containers: containersValues });
        // Append the new parameters to the existing formData
        formData += '&' + containerSizesParam + '&' + containersParam;

        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/add_shipment_sea_details.php',
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
        });
    });
</script>