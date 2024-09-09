<!-- <form action="../php_api/update_mm.php" method="POST"> -->
<form id="MMForm">
<div class="container" id="MMDetailsContent">
    <div class="row">
        <div class="col-12 text-center">
            <span class="text-muted">Make a selection to view its data.</span>
        </div>
    </div>
</div>
</form>

<script>
$(document).ready(function() {
    $('#MMForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_mm.php', // Replace with your server endpoint
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
});
</script>