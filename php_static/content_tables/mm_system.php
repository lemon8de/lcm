<!-- <form action="../php_api/update_mm.php" method="POST"> -->
<form id="MMForm">
<table id="" class="table table-head-fixed table-hover mb-4">
    <thead>
        <tr>
            <th colspan="5" style="border: 1px solid black; text-align: center;">(6) MM System Details</th>
        </tr>
        <tr style="border-bottom:1px solid black">
            <th>CONTAINER STATUS</th>
            <th>DATE RETURN / REUSED</th>
            <th>NO OF DAYS AT PORT</th>
            <th>NO OF DAYS AT FALP</th>
            <th>ACTION</th>

        </tr>
    </thead>
    <tbody id="MMDetailsContent">
        <th colspan="5" class="text-muted text-center">Make a selection to view its data</th>
    </tbody>
</table>
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