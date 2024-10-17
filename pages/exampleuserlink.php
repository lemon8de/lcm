<?php
    require '../php_static/session_lookup.php';
    $directory = " / ADMIN / Account Management";
    $bar_whois_active = "exampleuserlink";
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
                        <table id="" class="table table-head-fixed table-hover mb-4">
                            <thead class="text-nowrap">
                                <tr style="border-bottom:1px solid black">
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Site Role</th>
                                    <th>Editing Privileges</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                        $site_role_options = "";
                                        foreach ($s_roles as $role) {
                                            if ($data['site_role'] == $role) {
                                                $selected = "selected";
                                            } else {
                                                $selected = null;
                                            }
                                            $site_role_options .= <<<HTML
                                                <option {$selected}>{$role}</option>
                                            HTML;
                                        }

                                        $editing_privileges_options = "";
                                        foreach ($e_roles as $e_role) {
                                            if ($data['editing_privileges'] == $e_role || !isset($data['editing_privileges'])) {
                                                $selected = "selected";
                                            } else {
                                                $selected = null;
                                            }
                                            $editing_privileges_options .= <<<HTML
                                                <option {$selected}>$e_role</option>
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
                                                <td>
                                                    <select class="form-control" name="site_role" onchange="update_user(this)">
                                                        {$site_role_options}
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="editing_privileges" onchange="update_user(this)">
                                                        {$editing_privileges_options}
                                                    </select>
                                                </td>
                                                <td>
                                                    <button class="btn btn-block btn-danger">Delete</button>
                                                </td>
                                            </tr>
                                        HTML;
                                    }
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <button class="btn btn-info"><i class="fas fa-plus"></i>&nbsp;Add user</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php include '../php_static/web/footer.php'?>
        </div>
    </body>
</html>

<script>
    function update_user(element) {
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
            var value = $(this).val(); // Get the value of the input/select

            // Only add to the dictionary if the key is defined
            if (key) {
                dataDictionary[key] = value;
            }
        });
        // Log the data dictionary
        console.log(dataDictionary);
    }
</script>