<div class="card card-gray-dark card-outline">
    <div class="card-header">
        <h3 class="card-title">Import Data + Update from Forwarder's File</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="container">
            <div class="row mb-3">
                <div class="col-3">
                    <button class="btn btn-warning btn-block btn-file" onclick="fileexplorer()">
                        <form id="file_form" enctype="multipart/form-data" action="../php_api/import_sea_shipment.php" method="POST">
                            <span><i class="fas fa-upload mr-2"></i>Import Forwarder's File</span><input type="file" id="import_sea" name="import_sea_shipment_file" onchange="submit()" accept=".csv" style="opacity:0; display:none;">
                        </form>
                    </button>
                </div>
                <div class="col-3">
                    <a href="../php_api/download_forwarder_sea_template.php">
                        <button class="btn btn-info btn-block btn-file">
                            <span><i class="fas fa-download mr-2"></i> Download Template </span>
                        </button>
                    </a>
                </div>
            </div>
            <?php include '../forms/add_shipment_sea_details_form.php';?>
        </div>
    </div>
</div>

<div class="card card-gray-dark card-outline">
    <div class="card-header collapsed">
        <h3 class="card-title">Shipment Documentation</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>
    <div class="card-body table-responsive">
        <!-- <div class="container"> -->
            <?php include '../php_static/content_tables/incoming_sea_confirm.php';?>
        <!-- </div> -->
    </div>
</div>

<div class="card card-gray-dark card-outline">
    <div class="card-header collapsed">
        <h3 class="card-title">Tabbed Details Idea</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>
    <div class="card-body table-responsive">
        <div class="row">
            <div class="col-2">
                <div class="row">
                    <div class="col-12">
                    <input class="form-control" id="ContainerInput" placeholder="Search By Container" onkeyup="debounce(searchContainer, 150)">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" style="max-height:400px;overflow-y:auto;">
                        <table class="table table-head-fixed text-nowrap table-hover mb-4">
                            <thead>
                                <tr>
                                    <th>CONTAINER</th>
                                </tr>
                            </thead>
                            <tbody id="ContainerSearch">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <div class="col-10">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        <li class="pt-2 px-3"><h3 class="card-title" id="TabTitle">Container</h3></li>
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#ShipmentTabDiv" role="tab">Shipment</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#VesselTabDiv" role="tab">Vessel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#DeliveryTabDiv" role="tab">Delivery</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#CompletionTabDiv" role="tab">Completion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#PolytainerTabDiv" role="tab">Polytainer</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#MMTabDiv" role="tab">MM</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#HistoryTabDiv" role="tab">History</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade active show" id="ShipmentTabDiv" role="tabpanel">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin malesuada lacus ullamcorper dui molestie, sit amet congue quam finibus. Etiam ultricies nunc non magna feugiat commodo. Etiam odio magna, mollis auctor felis vitae, ullamcorper ornare ligula. Proin pellentesque tincidunt nisi, vitae ullamcorper felis aliquam id. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin id orci eu lectus blandit suscipit. Phasellus porta, ante et varius ornare, sem enim sollicitudin eros, at commodo leo est vitae lacus. Etiam ut porta sem. Proin porttitor porta nisl, id tempor risus rhoncus quis. In in quam a nibh cursus pulvinar non consequat neque. Mauris lacus elit, condimentum ac condimentum at, semper vitae lectus. Cras lacinia erat eget sapien porta consectetur.
                        </div>
                        <div class="tab-pane fade" id="VesselTabDiv" role="tabpanel">
                            Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam.
                        </div>
                        <div class="tab-pane fade" id="DeliveryTabDiv" role="tabpanel">
                            <?php include '../php_static/content_tables/delivery_plan.php';?>
                        </div>
                        <div class="tab-pane fade" id="CompletionTabDiv" role="tabpanel">
                            <?php include '../php_static/content_tables/completion_details.php';?>
                        </div>
                        <div class="tab-pane fade" id="PolytainerTabDiv" role="tabpanel">
                            <?php include '../php_static/content_tables/polytainer_details.php';?>
                        </div>
                        <div class="tab-pane fade" id="MMTabDiv" role="tabpanel">
                            <?php include '../php_static/content_tables/mm_system.php';?>
                        </div>
                        <div class="tab-pane fade" id="HistoryTabDiv" role="tabpanel">
                            <div style="max-height: 300px; overflow-y: auto;">
                                <?php include '../php_static/content_tables/history.php';?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        
    </div>
</div>

<div class="card card-gray-dark card-outline">
    <div class="card-header collapsed">
        <h3 class="card-title">View Data from Forwarder's File</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>
    <div class="card-body table-responsive">
        <!-- <div class="container"> -->
            <?php include '../php_static/content_tables/incoming_sea_view.php';?>
        <!-- </div> -->
    </div>
</div>
<script>

    function debounce(method, delay) {
        clearTimeout(method._tId);
        method._tId = setTimeout(function() {
            method();
        }, delay);
    }

    function searchContainer() {
        $.ajax({
            url: '../php_api/search_container.php',
            type: 'GET',
            data: {
                'container' : document.getElementById('ContainerInput').value,
            },
            dataType: 'json',
            success: function (response) {
                document.getElementById('ContainerSearch').innerHTML = response.inner_html;
            }
        });
    }

    function fileexplorer() {
        document.getElementById("import_sea").click();
    }

    //function loaddata() {
    function loaddata(row) {
        //console.log(this.value);
        var value = row.getAttribute('data-value');
        var shipment_details_ref = value;
        document.getElementById('TabTitle').innerHTML = row.getAttribute('id');

        $.ajax({
            url: '../php_api/detailsdump.php',
            type: 'GET',
            data: {
                'shipment_details_ref' : shipment_details_ref,
            },
            dataType: 'json',
            success: function (response) {
                if (response.delivery_plan) {
                    document.getElementById('DeliveryPlanContent').innerHTML = response.delivery_plan;
                    console.log(response)
                } else {
                    document.getElementById('DeliveryPlanContent').innerHTML = "<tr><td colspan='4' class='text-muted text-center'>NO DATA</td></tr>";
                }
                if (response.completion_details) {
                    document.getElementById('CompletionContent').innerHTML = response.completion_details;
                    console.log(response)
                } else {
                    document.getElementById('CompletionContent').innerHTML = "<tr><td colspan='3' class='text-muted text-center'>NO DATA</td></tr>";
                }
                if (response.polytainer_details) {
                    document.getElementById('PolytainerDetailsContent').innerHTML = response.polytainer_details;
                    console.log(response)
                } else {
                    document.getElementById('PolytainerDetailsContent').innerHTML = "<tr><td colspan='4' class='text-muted text-center'>NO DATA</td></tr>";
                }
                if (response.mmsystem_details) {
                    document.getElementById('MMDetailsContent').innerHTML = response.mmsystem_details;
                    console.log(response)
                } else {
                    document.getElementById('MMDetailsContent').innerHTML = "<tr><td colspan='5' class='text-muted text-center'>NO DATA</td></tr>";
                }
                if (response.history) {
                    document.getElementById('HistoryContent').innerHTML = response.history;
                    console.log(response)
                } else {
                    document.getElementById('HistoryContent').innerHTML = "<tr><td colspan='4' class='text-muted text-center'>NO DATA</td></tr>";
                }
            }
        });
    }
</script>