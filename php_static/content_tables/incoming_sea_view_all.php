<div class="row mb-4">
    <select id="shipment_details_ref_select" class="form-control" required onchange="loaddata.call(this)">
        <option value="" disabled selected>Select BL  |  CONTAINER</option>
        <?php 
            $sql = "SELECT shipment_details_ref, bl_number, container from m_shipment_sea_details";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo <<<HTML
                    <option value="{$row['shipment_details_ref']}">{$row['bl_number']}  |  {$row['container']}</option>
                HTML;
            }
        ?>
    </select>
</div>

<script>
    function loaddata() {
        console.log(this.value);
    }
</script>

<?php 
echo <<<HTML
    <table id="" class="table table-head-fixed text-nowrap table-hover">
        <thead>
            <tr>
                <th colspan="3" style="border: 1px solid black; text-align: center;">(3) Delivery Plan</th>
            </tr>
            <tr style="border-bottom:1px solid black">
                <th>REQUIRED DELIVERY SCHEDULE</th>
                <th>DELIVERY PLAN</th>
                <th>TABS</th>
            </tr>
        </thead>
        <tbody id="">
HTML;

echo <<<HTML
        </tbody>
    </table>
HTML;

echo <<<HTML
    <table id="" class="table table-head-fixed text-nowrap table-hover">
        <thead>
            <tr>
                <th colspan="4" style="border: 1px solid black; text-align: center;">(4) Completion Details</th>
            </tr>
            <tr style="border-bottom:1px solid black">
                <th>DATE PORT OUT</th>
                <th>ACTUAL RECEIVED AT FALP</th>
                <th>NO. DAYS AT PORT</th>
                <th>NO. DAYA AT FALP</th>
            </tr>
        </thead>
        <tbody id="">
HTML;

echo <<<HTML
        </tbody>
    </table>
HTML;

echo <<<HTML
    <table id="" class="table table-head-fixed text-nowrap table-hover">
        <thead>
            <tr>
                <th colspan="3" style="border: 1px solid black; text-align: center;">(5) Polytainer Details</th>
            </tr>
            <tr style="border-bottom:1px solid black">
                <th>POLYTAINER SIZE</th>
                <th>POLYTAINER QUANTITY</th>
                <th>ETD</th>
            </tr>
        </thead>
        <tbody id="">
HTML;

echo <<<HTML
        </tbody>
    </table>
HTML;
?>

<div class="row mt-2 mb-2">
    <div class="col-12" style="text-align:center;">
        <h3>History</h3>
    </div>
</div>