<!-- Modal -->
<div class="modal fade" id="add_forwarder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body container" id="">
                <form action="../php_api/add_forwarder.php" method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="textInput1">FORWARDER PARTNER</label>
                            <input type="text" class="form-control" name="forwarder_partner" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="textInput2">BUSINESS NAME</label>
                            <input type="text" class="form-control" name="business_name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fileInput">File Input</label>
                        <input type="file" class="form-control-file" name="forwarder_logo" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>