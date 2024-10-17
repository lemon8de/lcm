<?php 
    $sql = "SELECT 
        COUNT(*) AS total, 
        SUM(CASE WHEN e.actual_received_at_falp IS NOT NULL THEN 1 ELSE 0 END) AS received, 
        SUM(CASE WHEN e.actual_received_at_falp IS NULL THEN 1 ELSE 0 END) AS pending 
    FROM 
        m_shipment_sea_details AS a 
    LEFT JOIN 
        m_vessel_details AS b ON a.shipment_details_ref = b.shipment_details_ref 
    LEFT JOIN 
        m_delivery_plan AS c ON a.shipment_details_ref = c.shipment_details_ref 
    LEFT JOIN 
        m_mmsystem AS d ON a.shipment_details_ref = d.shipment_details_ref 
    LEFT JOIN 
        m_completion_details AS e ON a.shipment_details_ref = e.shipment_details_ref 
    WHERE 
        a.confirm_departure = '1';";
    $stmt = $conn -> prepare($sql);
    $stmt -> execute();
    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $total = $data['total'];
        $received = $data['received'];
        $pending = $data['pending'];
    }
?>
<div class="container mt-2">
    <div class="d-flex align-items-center mb-3">
        <div class="d-flex w-50 m-1 p-2 align-items-center" style="background-color:#ffffff;box-shadow:0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2);background-clip:border-box;border-radius:.25rem;">
            <div class="col-5 form-check">
                <input class="form-check-input" id="show_active_only_box" type="checkbox" checked onchange="historical_mode.call(this)">
                <label class="form-check-label">Show Active Only<br><span class="text-muted small">&nbsp;Disable for Historical Logs</span></label>
            </div>
            <div class="col-4">
                <form id="ExportForm">
                    <button type="submit" class="btn btn-block btn-primary">Export Data</button>
                </form>
            </div>
        </div>
        <div class="d-flex" id="SummationContainer">
            <!-- <div class="ml-1">
                <div class="bg-info pl-4 pr-4" style="border-radius:.350rem;padding:0rem .350rem">
                    <h4 style="font-weight:700;line-height:1.5;"><?php echo $total; ?><span style="font-size:75%;font-weight:500;">&nbsp;Total</span></h4>
                </div>
            </div>
            <div class="ml-1">
                <div class="bg-success pl-4 pr-4" style="border-radius:.350rem;padding:0rem .350rem">
                    <h4 style="font-weight:700;line-height:1.5;"><?php echo $received; ?><span style="font-size:75%;font-weight:500;">&nbsp;Received</span></h4>
                </div>
            </div> -->
            <div class="ml-1">
                <div class="bg-warning pl-4 pr-4" style="border-radius:.350rem;padding:0rem .350rem">
                    <h4 style="font-weight:700;line-height:1.5;"><?php echo $pending; ?><span style="font-size:75%;font-weight:500;">&nbsp;Active</span></h4>
                </div>
            </div>
        </div>
    </div>
    <form id="ActiveReportSearchForm">
    <div class="row mb-3" id="ActiveReportSearchBars" style="display:none;">
        <div class="col-3">
            <select class="form-control" id="active_month" name="month" onchange="search_active_report()">
                <option value="" selected disabled>Select Month</option>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
        </div>
        <div class="col-2">
            <select class="form-control" id="active_year" name="year" onchange="search_active_report()">
                <?php
                    $current_year = date("Y");
                    $end_year = $current_year - 10;
                    for ($year = $current_year; $year >= $end_year; $year--) {
                        echo <<<HTML
                            <option value="{$year}">{$year}</option>
                        HTML;
                    }
                ?>
            </select>
        </div>
    </div>
    </form>
</div>

<div class="card p-2 m-2 container-fluid" style="max-height: 70vh; overflow-y: auto;" id="ImportDataMain">
    <?php include '../php_static/content_tables/active_sea_report.php';?>
</div>

<script>
    function historical_mode() {
        //this is a cheeky way lol, so the active table will autoreload and will load again when you
        //check the box
        //this makes this page different as import and polytainer pages do not have
        //preloaded content, this will make life hard.
        //though we can argue that it is optimized that way
        // IDK IDKDIDKD IDKD IDK
        if (this.checked) {
            document.getElementById("ActiveReportSearchBars").style.display = 'none';
            location.reload();
            //do an ajax request here of empty search parameters to reload it
            //probably recall the function or just copy it here
        } else {
            document.getElementById("ActiveReportSearchBars").style.display = 'flex';
            document.getElementById("ActiveReportContent").innerHTML = '';
        }
    }
    function search_active_report() {
        var formData = $('#ActiveReportSearchForm').serialize();
        $.ajax({
            type: 'GET',
            url: '../php_api/refine_active_report.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (!response.exited) {
                    document.getElementById("ActiveReportContent").innerHTML = response.inner_html;
                    document.getElementById("SummationContainer").innerHTML = response.inner_html_summation;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
                alert("An error occurred while processing your request. Please try again later.");
            }
        });
    }

    $('#ExportForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        //gather data, if show active only, month and year
        $.ajax({
            url: '../php_api/export_active.php',
            type: 'POST',
            data: {
                'show_active_only' : document.getElementById('show_active_only_box').checked,
                'month' : document.getElementById('active_month').value,
                'year' : document.getElementById('active_year').value,
            },
            xhrFields: {
                responseType: 'blob' // Set the response type to blob
            },

            success: function(data) {
                // Create a link element
                var link = document.createElement('a');
                var url = window.URL.createObjectURL(data);
                link.href = url;
                // Create a new Date object
                const currentDate = new Date();
                const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // 01-12
                const year = currentDate.getFullYear(); // 4-digit year
                // Format as MM/YYYY
                const formattedDate = `${month}/${year}`;
                link.download = 'LCM-ACTIVE[' + formattedDate +  '].csv'; // Set the file name
                document.body.appendChild(link);
                link.click(); // Simulate click to download
                document.body.removeChild(link); // Remove the link
            },
            error: function() {
                alert('Error exporting data.');
            }
        });
    });
</script>