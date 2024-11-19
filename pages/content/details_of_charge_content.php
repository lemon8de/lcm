<div class="card p-2 m-2 container-fluid" style="max-height: 80vh; overflow-y:auto;">
<table class="table table-head-fixed text-nowrap table-hover">
    <thead>
        <tr style="border-bottom:1px solid black">
            <th></th>
            <th>TYPE</th>
            <th>GROUP</th>
            <th>DETAILS</th>
            <th>BASIS</th>
            <th>CURRENCY</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $sql = "SELECT type_of_transaction, charge_group, details_of_charge, basis, currency, billing_details_ref, (case when type_of_transaction = 'IMPORT SEA' then 1 when type_of_transaction = 'IMPORT AIR' then 2 when type_of_transaction = 'EXPORT SEA' then 3 when type_of_transaction = 'EXPORT AIR' then 4 else 0 end) as type_order, (case when charge_group = 'LOCAL CHARGES' then 1 when charge_group = 'ACCESSORIAL' then 2 when charge_group = 'REIMBURSEMENT' then 3 else 0 end) as charge_order from m_billing_information order by type_order asc, charge_order asc";

            $stmt = $conn -> query($sql);
            while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                echo <<<HTML
                <tr>
                    <td><i id="{$data['billing_details_ref']}" class="fas fa-times" onclick="delete_charge(this)" style="cursor:pointer;"></i></td>
                    <td>{$data['type_of_transaction']}</td>
                    <td>{$data['charge_group']}</td>
                    <td>{$data['details_of_charge']}</td>
                    <td>{$data['basis']}</td>
                    <td>{$data['currency']}</td>
                </tr>
                HTML;
            }
        ?>
    </tbody>
</table>
</div>

<script>
    function delete_charge(element) {
        const form = document.createElement('form');
        form.action = '../php_api/delete_details_of_charge.php'; // Set the action URL
        form.method = 'POST'; // Set the method

        const inputName = document.createElement('input');
        inputName.type = 'hidden';
        inputName.name = 'billing_details_ref';
        inputName.value = element.id;
        form.appendChild(inputName);
        // Append the form to the body
        document.body.appendChild(form);

        form.submit();
    }
</script>