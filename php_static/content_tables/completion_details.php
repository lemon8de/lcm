<!-- <form action="../php_api/update_completion.php" method="POST"> -->
<form id="CompletionForm">
<table id="" class="table table-head-fixed table-hover mb-4">
    <thead>
        <tr>
            <th colspan="3" style="border: 1px solid black; text-align: center;">(4) Completion Details</th>
        </tr>
        <tr style="border-bottom:1px solid black">
            <th>DATE PORT OUT</th>
            <th>ACTUAL RECEIVED AT FALP</th>
            <th>ACTION</th>
        </tr>
    </thead>
    <tbody id="CompletionContent">
        <th colspan="3" class="text-muted text-center">Make a selection to view its data</th>
    </tbody>
</table>
</form>

<script>
$(document).ready(function() {
    $('#CompletionForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_completion.php', // Replace with your server endpoint
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
</script>