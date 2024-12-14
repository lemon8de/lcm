<?php
    require '../php_static/session_lookup.php';
    $directory = " / ADMIN / Account Management";
    $bar_whois_active = "account_management";
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
                    <div class="card container-fluid">
                        <table class="table table-head-fixed table-hover mb-2">
                            <thead class="text-nowrap">
                                <tr style="border-bottom:1px solid black">
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th class="text-center">EDIT GROUP</th>
                                    <th class="text-center">Account Management</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="accountsBody">
                                <?php 
                                    $sql = "SELECT * from m_user_accounts";
                                    $stmt = $conn -> prepare($sql);
                                    $stmt -> execute();

                                    $site_role_options = "";
                                    $selected = "";
                                    $editing_privileges_options = "";
                                    $s_roles = ["ADMIN", "GUEST", "EDITOR"];
                                    $e_roles = ["all", "none"];
                                    while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                                        if ($data['site_role'] == "ADMIN") {
                                            $checked_admin = " checked disabled";
                                        } else {
                                            $checked_admin = "";
                                        }
                                        if ($_SESSION['username'] !== $data['username']) {
                                            $delete = <<<HTML
                                                <td>
                                                    <i class="fas fa-times" style="font-size:150%;color:#6c757d;cursor:pointer;" onclick="delete_user(this)"></i>
                                                </td>
                                            HTML;
                                        } else {
                                            $delete = "";
                                        }
                                        $edit_groups = ['GUEST ONLY', 'INCOMING', 'OUTGOING', 'BILLING', 'ALL'];
                                        $edit_group_options = "";
                                        foreach ($edit_groups as $edit_group) {
                                            if ($data['editing_privileges'] == $edit_group) {
                                                $selected = "selected";
                                            } else {
                                                $selected = "";
                                            }
                                            $edit_group_options .= <<<HTML
                                                <option {$selected}>{$edit_group}</option>
                                            HTML;
                                        }
                                        echo <<<HTML
                                            <tr id="user-{$data['id']}">
                                                <td>
                                                    <input type="text" name="username" class="form-control" onkeyup="update_user(this)" value="{$data['username']}">
                                                </td>
                                                <td>
                                                    <input type="text" name="password" class="form-control" onkeyup="update_user(this)" value="{$data['password']}">
                                                </td>
                                                <td class="text-center">
                                                    <select class="form-control" onchange="update_user(this)" name="editing_privileges">
                                                        {$edit_group_options}
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox" onchange="update_user(this)" name="is_admin"{$checked_admin}>
                                                </td>
                                                {$delete}
                                            </tr>
                                        HTML;
                                    }
                                ?>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-info mb-2" onclick="add_user()"><i class="fas fa-plus"></i>&nbsp;Add user</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php include '../php_static/web/footer.php'?>
        </div>
    </body>
</html>

<script>
    function update_user(element) {
        element.classList.add('is-warning');
        var tr = $(element).closest('tr');

        // Get all <select> and <input> elements inside the nearest <tr>
        var inputsAndSelects = tr.find('input, select');
        
        // Create an object to hold the data
        var dataDictionary = {
            id: tr.attr('id') // Include the <tr> id in the data dictionary
        };

        // Populate the data dictionary
        inputsAndSelects.each(function() {
            var key = $(this).attr('name') || $(this).attr('id'); // Use name or id as the key
            var value;

            // Check if the element is a checkbox
            if ($(this).is(':checkbox')) {
                value = $(this).is(':checked'); // Get the checked state of the checkbox
            } else {
                value = $(this).val(); // Get the value of the input/select
            }

            // Only add to the dictionary if the key is defined
            if (key) {
                dataDictionary[key] = value;
            }
        });
        // Log the data dictionary
        console.log(dataDictionary);
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_user_details.php',
            data: dataDictionary,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                element.classList.remove('is-warning');
                element.classList.add('is-valid');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
                alert("An error occurred while processing your request. Please try again later.");
            }
        });
    }

    function delete_user(element) {
        var tr = $(element).closest('tr');
        var dataDictionary = {
            id: tr.attr('id')
        };
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_user_delete.php',
            data: dataDictionary,
            dataType: 'json',
            success: function(response) {
                window.location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
                alert("An error occurred while processing your request. Please try again later.");
            }
        });
    }

    function add_user() {
        $.ajax({
            type: 'POST', // or 'GET' depending on your needs
            url: '../php_api/update_user_add.php',
            dataType: 'json',
            success: function(response) {
                console.log(response);
                document.getElementById('accountsBody').insertAdjacentHTML('afterend', response.inner_html);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
                alert("An error occurred while processing your request. Please try again later.");
            }
        });
    }
</script>