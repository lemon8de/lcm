<!-- <form action="../php_api/update_polytainer.php" method="POST"> -->
<form id="PolytainerForm">
<table id="" class="table table-head-fixed table-hover mb-4">
    <thead>
        <tr>
            <th colspan="4" style="border: 1px solid black; text-align: center;">(5) Polytainer Details</th>
        </tr>
        <tr style="border-bottom:1px solid black">
            <th>POLYTAINER SIZE</th>
            <th>POLYTAINER QUANTITY</th>
            <th>ETD</th>
            <th>ACTION</th>

        </tr>
    </thead>
    <tbody id="PolytainerDetailsContent">
        <th colspan="4" class="text-muted text-center">Make a selection to view its data</th>
    </tbody>
</table>
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