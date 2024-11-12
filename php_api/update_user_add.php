<?php
    require 'db_connection.php';
    $randomString = generateRandomString(5);
    $username = 'lcm-user-' . $randomString;
    $password = 'lcm-password-' . $randomString;
    $site_role = "GUEST";
    $editing_privileges = null;
    $stmt = $conn->prepare("INSERT INTO m_user_accounts (username, password, site_role, editing_privileges) VALUES (:username, :password, :site_role, :editing_privileges)");
    // Bind parameters
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':site_role', $site_role);
    $stmt->bindParam(':editing_privileges', $editing_privileges);

    // Execute the statement
    $stmt->execute();

    // Get the last inserted ID
    $id = $conn->lastInsertId();

    $inner_html = <<<HTML
        <tr id="user-{$id}">
            <td>
                <input type="text" name="username" class="form-control" onkeyup="update_user(this)" value="{$username}">
            </td>
            <td>
                <input type="text" name="password" class="form-control" onkeyup="update_user(this)" value="{$password}">
            </td>
            <td class="text-center">
                <input class="form-check-input" type="checkbox" onchange="update_user(this)" name="can_edit">
            </td>
            <td class="text-center">
                <input class="form-check-input" type="checkbox" onchange="update_user(this)" name="is_admin">
            </td>
            <td>
                <i class="fas fa-times" style="font-size:150%;color:#6c757d;cursor:pointer;" onclick="delete_user(this)"></i>
            </td>
        </tr>
    HTML;
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);

    function generateRandomString($length = 5) {
        // Define the characters to choose from
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
    
        // Generate the random string
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
    
        return $randomString;
    }