<section id="ContainerBreakdownSwitch" style="display:none;">
<div class="row mb-2">
    <div class="col-sm-12">
        <button type="button" class="btn btn-tool" onclick="tableswitchingbutton()">
            <span class="text-danger"><i class="fas fa-arrow-left"></i><strong>&nbsp;Go Back to Import Data</strong></span>
        </button>
    </div>
</div>
<table class="table table-head-fixed table-hover mb-4">
    <thead>
        <tr>
            <th>Container</th>
            <th>Received Date</th>
        </tr>
    </thead>
    <tbody id="ContainerBreakdownContent">
    </tbody>
</table>
</section>

<script>
    function tableswitchingbutton() {
        //show and hide stuff
        document.getElementById('ImportDataMain').style.display = 'block';
        document.getElementById('ContainerBreakdownSwitch').style.display = 'none';
    }
</script>