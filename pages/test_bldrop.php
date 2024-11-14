<?php
    require '../php_static/session_lookup.php';
    $directory = " / TESTING / BL DROP TEST";
    $bar_whois_active = "bl_drop";
    require '../php_static/block_urlcreep.php';
    require '../php_api/db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php include '../php_static/web/link-rels.php'?>
        <?php include '../php_static/web/scripts-rels.php'?>
    </head>
    <body class="sidebar-mini layout-fixed">
        <div class="wrapper">
            <?php include '../php_static/web/nav-bar.php'?>
            <?php include '../php_static/web/sidebar.php'?>
            <div class="content-wrapper">
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-3 d-flex">
                                <div class="card div m-2 p-2 flex-fill">
                                    <label>BL NUMBER</label>
                                    <input type="text" class="form-control" id="billing_bl" style="font-family:monospace;">
                                </div>
                            </div>
                            <div class="col-1 d-flex flex-fill justify-content-center align-items-center">
                                <i class="fas fa-arrow-right" style="font-size:180%;cursor:pointer;" onclick="q_container()"></i>
                            </div>
                            <div class="col-3 d-flex">
                                <div class="card div m-2 flex-fill">
                                    <ul id="blnumber_list" style="font-family:monospace;">
                                    </ul>
                                    <style>
                                        #blnumber_list {
                                            list-style-type: none; /* Remove default bullets */
                                            padding-left: 1em;
                                        }

                                        #blnumber_list li {
                                            position: relative; /* Position relative for the pseudo-element */
                                        }
                                        #blnumber_list li::before {
                                            content: 'X '; /* Use "X" as the bullet */
                                            color: red; /* Change color if desired */
                                        }
                                    </style>
                                </div>
                            </div>
                            <div class="col d-flex align-items-center">
                                <button class="btn btn-info" onclick="generate_billing()">Generate Charges</button>
                            </div>
                        </div>
                        <div class="container-fluid" id="billing_main_container"></div>
                    </div>
                </div>
            </div>
            <?php include '../php_static/web/footer.php'?>
        </div>
    </body>
    <?php include '../php_static/web/notification_handler.php';?>
</html>

<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     alert("Testing environment");
    // });

    function debounce(method, delay) {
        clearTimeout(method._tId);
        method._tId = setTimeout(function() {
            method();
        }, delay);
    }

    function seek_bl_number() {
        console.log('seeking: ' + document.getElementById('billing_bl').value);
        $.ajax({
            type: 'GET', // or 'GET' depending on your needs
            url: '../php_api/billing_seek_blnumber.php',
            data: {
                'bl_number' : document.getElementById('billing_bl').value,
            },
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
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
                alert("An error occurred while processing your request. Please try again later.");
            }
        });
    }

    function q_container() {
        const list = document.getElementById('blnumber_list');
        const inputValue = document.getElementById('billing_bl').value.trim(); // Get and trim the input value

        // Check if the input is empty or only whitespace
        if (!inputValue) {
            alert("Please enter a valid BL number."); // Alert the user
            return; // Exit the function
        }

        // Check for duplicates
        const existingItems = Array.from(list.getElementsByTagName('li'));
        const isDuplicate = existingItems.some(item => item.textContent === inputValue);

        if (isDuplicate) {
            alert("This BL number already exists in the list."); // Alert the user
            return; // Exit the function
        }

        // Create a new list item
        const newItem = document.createElement('li');
        newItem.textContent = inputValue; // Set the text content to the input value

        // Add click event to delete the item
        newItem.onclick = function() {
            list.removeChild(newItem); // Remove the item from the list
        };
        newItem.style.cursor = "pointer";

        // Append the new item to the list
        list.appendChild(newItem);
        document.getElementById('billing_bl').value = null;
    }

    document.getElementById('billing_bl').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') { // Check if the pressed key is Enter
            q_container(); // Call the function
            event.preventDefault(); // Prevent the default action (optional)
        }
    });

    function generate_billing() {
        // Capture the list using JavaScript
        const list = document.getElementById('blnumber_list');
        const items = list.getElementsByTagName('li');
        const bl_list = Array.from(items).map(item => item.textContent);
        console.log(bl_list);

        // Send the AJAX request
        $.ajax({
            type: 'GET', // or 'GET' depending on your needs
            url: '../php_api/billing_seek_blnumber.php',
            data: {
                'bl_numbers' :bl_list,
            },
            dataType: 'json',
            success: function(response) {
                document.getElementById('billing_main_container').innerHTML = "";
                if (response.import_sea) {
                    document.getElementById('billing_main_container').innerHTML = response.import_sea;
                }
                if (response.import_air) {
                    document.getElementById('billing_main_container').innerHTML += response.import_air;
                }
                if (response.export_sea) {
                    document.getElementById('billing_main_container').innerHTML += response.export_sea;
                }
                if (response.export_air) {
                    document.getElementById('billing_main_container').innerHTML += response.export_air;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
                alert("An error occurred while processing your request. Please try again later.");
            }
        });
    }
    
    function export_button(id) {
        // Get the table element
        const table = document.getElementById(id);
        const rows = Array.from(table.rows);
        const csvContent = rows.map(row => {
            const cells = Array.from(row.cells).map(cell => {
                // Wrap cell content in double quotes
                const cellText = cell.innerText.replace(",", ''); // Escape double quotes
                return `"${cellText}"`;
            });
            return cells.join(',');
        }).join('\n');

        // Create a new Date object
        const currentDate = new Date();
        const year = currentDate.getFullYear(); // 4-digit year
        const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // 01-12
        const day = String(currentDate.getDate()).padStart(2, '0'); // 01-31
        // Format as YYYY-MM-DD
        const formattedDate = `${year}-${month}-${day}`;

        // Create a blob and a link to download the CSV
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'LCM-BILLING_PREP-IMPORT-SEA[' + formattedDate + '].csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>