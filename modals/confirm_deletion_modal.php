<!-- Modal -->
<div class="modal fade" id="confirm_deletion_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body container" id="">
                <div class="row">
                    <div class="col-8 mx-auto text-center">
                        <h4 class="badge badge-danger" style="font-size:150%;">CONFIRM THE DELETION</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="container mt-5 mb-5">
                    <table class="table table-head-fixed text-nowrap table-hover">
                            <thead>
                                <tr style="border-bottom:1px solid black">
                                    <th>BL NUMBER</th>
                                    <th>CONTAINER</th>
                                    <th>STATUS</th>
                                    <th>CONFIRMED</th>
                                </tr>
                            </thead>
                            <tbody id="ConfirmDeletionContent">
                                <tr>
                                    <td>DATA</td>
                                    <td>DATA</td>
                                    <td>DATA</td>
                                    <td>DATA</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="row">
                    <div class="col-3">
                        <button type="button" class="btn btn-block btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                    <div class="col-6"></div>
                    <div class="col-3">
                        <button type="button" id="confirm_deletion_button" class="btn btn-block btn-danger" disabled>Delete (5)</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirm_deletion_modal_start_timer() {
        let timer = 5;
        const confirmBtn = document.getElementById("confirm_deletion_button");
    
        // Update button text
        confirmBtn.textContent = `Delete (${timer})`;
        confirmBtn.disabled = true; // Disable the button initially

        const interval = setInterval(function () {
            timer--;
            confirmBtn.textContent = `Delete (${timer})`;

            // When the timer reaches zero
            if (timer <= 0) {
                clearInterval(interval);
                confirmBtn.disabled = false; // Enable the button
                confirmBtn.textContent = "Delete"; // Reset button text
            }
        }, 1000);
    }

    //idk something with onclick tag on that modal button destroys it :)
    // Get the button element
    const button = document.getElementById("confirm_deletion_button");
    // Function to be executed when the button is clicked
    function handleClick() {
        //deletion ajax
        $.ajax({
            url: '../php_api/sea_delete_departure.php',
            type: 'POST',
            data: {
                'bl_numbers' : selectedIds,
            },
            dataType: 'json',
            success: function (response) {
                if (response.notification) {
                    Toast.fire({
		                icon: response.notification.icon,
		                title: response.notification.text,
	                });
                }
                //this block is retarded and disgusting
                //only one case
                //if you delete everything from a filter
                //the first load will be empty, because you queried with a filter, where you deleted
                //everything from
                //so this next one, staggered because they actually race each other, redoes the query, but this time
                // the emptied filter is not selected anymore, well because it don't exist anymore
                search_documentation();
                setTimeout(() => {
                    search_documentation();
                }, 500); // 2000 milliseconds = 2 seconds
            }
        });
        ck_bl_status = true; //makes the select all button work again

        $('#confirm_deletion_modal').modal('hide');
    }
    // Add the onclick event listener to the button
    button.addEventListener("click", handleClick);
</script>