<?php
include_once('templates/header.php');
include_once('include/import.php');
$date = getWorkingOursByDate(date("Y-m-d"));
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="my-view" id="my-view" class="container">
                <div class="panel with-nav-tabs panel-success">
                    <div class="panel-heading">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#t1" data-toggle="tab" class="dt-tabs">Pending</a></li>
                            <!--<li><a href="#t5" data-toggle="tab" class="dt-tabs">Create Account</a></li>-->
                            <li><a href="#t2" data-toggle="tab" class="dt-tabs">Completed</a></li>
                            <li><a href="#t3" data-toggle="tab" class="dt-tabs">Error Logs</a></li>
                            <li><a href="#t4" data-toggle="tab" class="dt-tabs">Upload Data</a></li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="t1">
                                <table id="dt-1" class="table table-bordered datatable dataTable" width="100%" cellspacing="0"><thead><tr><th>TRN REF NO</th><th>APP NO</th><th>DATE</th><th>UPLOAD USER</th><th>IP Address</th><th>ACTIONS</th></tr></thead></table>
                            </div>
                            <div class="tab-pane fade" id="t2">
                                <table id="dt-2" class="table table-bordered datatable dataTable" width="100%" cellspacing="0"><thead><tr><th>APPLNO</th><th>TRNREFNO</th><th>EXIST. CUST.</th><th>CUST. ID</th><th>EXIST. JH1</th><th>JH1. ID</th><th>EXIST. JH2</th><th>JH2. ID</th><th>ACCT. NO</th><th>Processed</th><th>START DATE</th><th>END DATE</th><th>FINACLE ID</th><th>UPLOAD USER</th><th>UPLOAD DATE</th></tr></thead></table>
                            </div>
                            <div class="tab-pane fade" id="t3">
                                <table id="dt-3" class="table table-bordered datatable dataTable" width="100%" cellspacing="0"><thead><tr><th>TRNREFNO NO</th><th>DATE TIME</th><th>USER</th><th>SECTION</th><th>EXCEPTION</th></tr></thead></table>
                            </div>
                            <div class="tab-pane fade" id="t4">
                                <div class="row" style="width: 400px; text-align:center; margin: auto; padding-top: 80px;" align="center">
                                    <form id="upload_csv" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>UPLOAD CSV FILE:</label>
                                        </div>
                                        <div class="form-group">
                                            <input type="file" name="input-file" id="input-file" class="form-control" style="margin-top:15px;">
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" name="upload" id="upload" value="Upload" style="margin-top:10px;" class="form-control btn btn-primary" />
                                        </div>
                                        <div style="clear:both"></div>
                                    </form>
                                </div>
                            </div>

                             <!--<div class="tab-pane fade" id="t5">
                                <table id="dt-5" class="table table-bordered datatable dataTable" width="100%" cellspacing="0"><thead><tr><th>CUST ID</th><th>APPLNO</th><th>TRNREFNO NO</th><th>DATE TIME</th><th>ACTIONS</th></tr></thead></table>
                            </div>-->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once('templates/login-popup.php');
include_once('templates/footer.php');
