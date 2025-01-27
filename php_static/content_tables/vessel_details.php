<form id="VesselForm">
<div class="container" id="VesselDetailsContent">
    <div class="row">
        <div class="col-12 text-center">
            <span class="text-muted">Make a selection to view its data.</span>
        </div>
    </div>
</div>
</form>

<script>
$(document).ready(function() {
    $('#VesselForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_vessel_details.php', // Replace with your server endpoint
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.notification) {
                    Toast.fire({
		                icon: response.notification.icon,
		                title: response.notification.text,
	                })
                }
                get_container_details_api(null);
                search_documentation(false);
            },
        });
    });
});

function edit_find_similar() {
        $.ajax({
            url: '../php_api/search_similar_vessels.php',
            type: 'GET',
            data: {
                'vessel_name' : document.getElementById('VesselDetailsEditName').value,
            },
            dataType: 'json',
            success: function (response) {
                if (response.exists) {
                    document.getElementById('VesselDetailsEditToolTipInfo').style.display = 'block';
                    document.getElementById('VesselDetailsEditETA').value = response.eta_mnl;
                    document.getElementById('VesselDetailsEditATA').value = response.ata_mnl;
                    document.getElementById('VesselDetailsEditATB').value = response.atb;
                    document.getElementById('VesselDetailsEditToolTipInfo').innerHTML = response.info_html;
                } else {
                    document.getElementById('VesselDetailsEditToolTipInfo').style.display = 'none';
                    document.getElementById('VesselDetailsEditETA').value = '';
                    document.getElementById('VesselDetailsEditATA').value = '';
                    document.getElementById('VesselDetailsEditATB').value = '';
                }
            }
        });
    }
</script>