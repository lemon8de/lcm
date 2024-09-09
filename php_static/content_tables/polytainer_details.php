<!-- <form action="../php_api/update_polytainer.php" method="POST"> -->
<form id="PolytainerForm">
<div class="container" id="PolytainerDetailsContent">
    <div class="row">
        <div class="col-12 text-center">
            <span class="text-muted">Make a selection to view its data.</span>
        </div>
    </div>
</div>
</form>

<script>
$(document).ready(function() {
    $('#PolytainerForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_polytainer.php', // Replace with your server endpoint
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