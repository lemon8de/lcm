<!-- Modal -->
<div class="modal fade" id="edit_import_data_sea_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="ImportDataModalForm" method="POST">
                <div class="modal-body container" id="EditImportModalBody">
                    <!-- Your form elements go here -->
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#ImportDataModalForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        // Serialize the form data
        var formData = $(this).serialize();
        // Send the AJAX request
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_import_data.php', // Replace with your server endpoint
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