<div class="container">
    <div class="row mb-3">
        <div class="col-3">
            <button class="btn btn-primary btn-block btn-file" onclick="fileexplorer()">
                <span><i class="fas fa-plus mr-2"></i>Add Forwarder</span>
            </button>
        </div>
    </div>
</div>

<div class="card p-2 m-2 container-fluid" style="max-height: 80vh; overflow-y:auto;">
<table class="table table-head-fixed text-nowrap table-hover">
    <thead>
        <tr style="border-bottom:1px solid black">
            <!-- <th><input type="checkbox" onchange="checkall(this)"></th> -->
            <th>PARTNER</th>
            <th>BUSINESS NAME</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $sql = "SELECT billing_forwarder_details_ref, forwarder_partner, business_name from m_billing_forwarder";
            $stmt = $conn -> query($sql);
            while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                echo <<<HTML
                    <tr>
                        <td>{$data['forwarder_partner']}</td>
                        <td>{$data['business_name']}</td>
                        <td><i id="{$data['billing_forwarder_details_ref']}" class="fas fa-times" onclick="delete_forwarder(this)" style="cursor:pointer;"></i></td>
                    </tr>
                HTML;
            }
        ?>
    </tbody>
</table>
</div>

<script>
    function delete_forwarder(element) {
        const form = document.createElement('form');
        form.action = '../php_api/delete_billing_forwarder.php'; // Set the action URL
        form.method = 'POST'; // Set the method

        const inputName = document.createElement('input');
        inputName.type = 'hidden';
        inputName.name = 'billing_forwarder_details_ref';
        inputName.value = element.id;
        form.appendChild(inputName);
        // Append the form to the body
        document.body.appendChild(form);

        form.submit();
    }
</script>