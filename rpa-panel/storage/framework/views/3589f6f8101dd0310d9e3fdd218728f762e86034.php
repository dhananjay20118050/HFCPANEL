<?php $__env->startSection('title'); ?>
Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>HFC RPA Process</h1>
    </div>

    <div class="section-body">
 <div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="my-view" id="my-view" class="container">
                <div class="panel with-nav-tabs panel-success">
                    <div class="tab" role="tabpanel">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a href="#t1" role="tab" data-toggle="tab" class="dt-tabs nav-link active">Pending</a></li>
                            <li class="nav-item"><a href="#t2" role="tab" data-toggle="tab" class="dt-tabs nav-link">Completed</a></li>
                            <li class="nav-item"><a href="#t3" role="tab" data-toggle="tab" class="dt-tabs nav-link">Error Logs</a></li>
                            <li class="nav-item"><a href="#t4" role="tab" data-toggle="tab" class="dt-tabs nav-link">Upload Data</a></li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="t1">
                                <table id="dt-1" class="data-table table table-striped dataTable no-footer" width="100%" cellspacing="0"><thead><tr><th>TRN REF NO</th><th>APP NO</th><th>DATE</th><th>UPLOAD USER</th><th>IP Address</th><th>ACTIONS</th></tr></thead></table>


                            </div>
                            <div class="tab-pane fade" id="t2">
                                <table id="dt-2" class="table table-bordered datatable dataTable" width="100%" cellspacing="0"><thead><tr><th>APPLNO</th><th>TRNREFNO</th><th>EXIST. CUST.</th><th>CUST. ID</th><th>EXIST. JH1</th><th>JH1. ID</th><th>EXIST. JH2</th><th>JH2. ID</th><th>ACCT. NO</th><th>Processed</th><th>START DATE</th><th>END DATE</th><th>FINACLE ID</th><th>UPLOAD USER</th><th>UPLOAD DATE</th></tr></thead></table>
                            </div>
                            <div class="tab-pane fade" id="t3">
                                <table id="dt-3" class="table table-bordered datatable dataTable" width="100%" cellspacing="0"><thead><tr><th>TRNREFNO NO</th><th>DATE TIME</th><th>USER</th><th>SECTION</th><th>EXCEPTION</th></tr></thead></table>
                            </div>

                            <div class="tab-pane fade" id="t4">
                                <div class="card-header-action">
                                        <div class="col-md-3"></div> 
                                        <form id="upload_csv" method="post" enctype="multipart/form-data">
                                              <?php echo csrf_field(); ?>
                                              <?php echo method_field('PUT'); ?> 
                                            <div class="col-md-4">  
                                                <input type="file" name="csv_file" id="csv_file" accept=".csv" style="margin-top:15px;" />
                                            </div>  
                                            <div class="col-md-5">  
                                                <input type="submit" name="upload" id="upload" value="Upload" style="margin-top:10px;" class="btn btn-info" />
                                            </div>  
                                            <div style="clear:both"></div>
                                        </form>
                        </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        
	</div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('assets/js/toastr.min.js')); ?>"></script>
<script type="text/javascript">
	$(document).ready(function(){

		toastr.options = {
    	    "closeButton": true, // true/false
    	    "debug": false, // true/false
    	    "newestOnTop": false, // true/false
    	    "progressBar": false, // true/false
    	    "positionClass": "toast-bottom-right", // toast-top-right / toast-top-left / toast-bottom-right / toast-bottom-left
    	    "preventDuplicates": false,
    	    "onclick": null,
    	    "showDuration": "5000", // in milliseconds
    	    "hideDuration": "1000", // in milliseconds
    	    "timeOut": "5000", // in milliseconds
    	    "extendedTimeOut": "1000", // in milliseconds
    	    "showEasing": "swing",
    	    "hideEasing": "linear",
    	    "showMethod": "fadeIn",
    	    "hideMethod": "fadeOut"
	   }	
    
       $(".dt-tabs").click(function(){               
        var tab = $(this).attr("href");
        selTab(tab);

       });

        selTab('#t1');

});

function selTab(tab){     
    if(tab == "#t2"){
        setDT2();        
    }else if(tab == "#t3"){
        setDT3();        
    }else if(tab=="#t4"){
        setDT4();
    }else if(tab=="#t1"){
        setDT1();        
        $(tab).addClass('show');
    }
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
       $($.fn.dataTable.tables(false)).DataTable()
          .columns.adjust();
    });
}

function setDT1(){

	    $('#dt-1').DataTable({
        processing: true,         
        serverSide: true,
        ajax: "<?php echo e(route('admin.process.hfc.showhfc',1)); ?>",        
        columns: [
 			{ data: "TRNREFNO" },
            { data: "APPLNO" },
            { data: "upload_datetime" },
            { data: "fullName" },
            { data: "ipaddress" },
            {   
                mRender: function ( file_name, type, data ) {

                return '<a href="#" onclick="startAction('+data['userid']+','+data['TRNREFNO']+')"><i class="fa fa-play" aria-hidden="true"></i></a> <a href="#" class="info" data-value="No Info." onclick="showInfo('+data['TRNREFNO']+')"><i class="fa fa-info" aria-hidden="true"></i></a>';
                 }
             },
            //{data: 'action', name: 'action', orderable: false, searchable: false},
        ],

        dom: '<"toolbar"lB<"#filters">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        "scrollX": true,
        "scrollY": "400px",
        "bDestroy": true,
        "scrollCollapse": true,
        initComplete: function () {
            $('.buttons-csv').html('<i class="fa fa-download" aria-hidden="true"></i>');

            $("#filters").html('<a href="#" id="start-q"><i class="fa fa-play" aria-hidden="true"></i></a><img src="../../../assets/img/loading.gif" class="none" id="loading-img">');

            afterDT1();
        },
        drawCallback: function( settings ) {

        },
        "language": {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [5] },{ "searchable": true, "targets": [0,1,2,3,4] }]


    });

    function afterDT1(){

        function startAutomation(){
            $("#wait").show();
            $("#wait-loading-img").removeClass("none");

            var table = $('#dt-1').DataTable();
            var apps = table.column(1).data().toArray();
            var userId = table.row(0).data()['userid'];
            console.log(userId);
            var len = apps.length;
            var count = 0;

            function singlestart(){

                $("#row_"+apps[count]).addClass("running disabled");

                $.ajax({
                    url:'<?php echo e(url('/api/process/hfc/start')); ?>',   
                    type:"POST",
                    data:{'userid':userId,'trnrefno':apps[count]},
                    async: true,
                    success: function(response) {
                        if(response.status == 'success'){
                            toastr["success"](response.message);
                        }else{
                            toastr["error"](response.message);
                        }
                        $("#row_"+apps[count]).removeClass("running disabled");
                        if(count < len - 1){
                            count++;
                            singlestart();
                        }
                        $("#wait").hide();
                        $("#wait-loading-img").addClass("none");
                    },error: function(request,status,errorThrown) {
                        toastr["error"]("Something went wrong!");
                        $("#row_"+apps[count]).removeClass("running disabled");
                        console.log(errorThrown);
                        console.log(JSON.parse(request.responseText));
                        $("#wait").hide();
                        $("#wait-loading-img").addClass("none");
                    }
                });
            }

            singlestart();
            
        }

        $("#start-q").click(function(){
            alert('Bulk Run');
            startAutomation();
        });
    }
 }


function setDT2(){

    var date = $("#date-filter").val();
    if(date == undefined){
        /* set date for complete tab */
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        date = yyyy+'-'+mm+'-'+dd;
    }

        $("#dt-2").DataTable( {
        processing: true,
        serverSide: true,         
        paging: true,
        order : [[ 8, "desc" ]],
        search: { "caseInsensitive": false },        
        ajax: {
            url:  "<?php echo e(route('admin.process.hfc.showhfc1',1)); ?>"
        },

        columns: [
                    { data: "appno" },
                    { data: "TRNREFNO" },
                    {   
                        mRender: function ( file_name, type, data ) {
                            if(data['is_existing_cust_1'] == 1){
                                return 'YES';
                            }else{
                                return 'NO';
                            }
                        }
                    },
                    { data: "cifid_1" },
                    {   
                        mRender: function ( file_name, type, data ) {
                            if(data['is_existing_cust_2'] == 1){
                                return 'YES';
                            }else{
                                return 'NO';
                            }
                        }
                    },
                    { data: "cifid_2" },
                    {   
                        mRender: function ( file_name, type, data ) {
                            if(data['is_existing_cust_3'] == 1){
                                return 'YES';
                            }else{
                                return 'NO';
                            }
                        }
                    },
                    { data: "cifid_3" },
                    { data: "accountno" },
                    { data: "processed" },
                    { data: "start_time" },
                    { data: "end_time" },
                    { data: "finnacleuser" },
                    { data: "upload_fullName" },
                    { data: "upload_datetime" }
                 
                ],

        dom: '<"toolbar"lB<"#filters2">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        "scrollX": true,
        "bDestroy": true,
        "scrollY": "400px",
        "scrollCollapse": true,
        initComplete: function () {
            $('.buttons-csv').html('<i class="fa fa-download" aria-hidden="true"></i>');

            $("#filters2").html('<div class="form-group"><input type="date" id="date-filter" class="form-control"></div>').css({'float':'left','padding-left':'5px'});
            //afterDT2();
            if(date != ""){
                $("#date-filter").val(date);
            }
        },
        drawCallback: function( settings ) {
            //afterDT2();
        },
        "language": {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [2,4,6] },{ "searchable": true, "targets": [0,1,3,5,7,8,9,10,11,12,13,14] }]

    } );

}

function afterDT2(){
    $("#date-filter").change(function(){
        setDT2();
    });
}

function setDT3(){ 
     var date = $("#date-filter").val();
    if(date == undefined){
        /* set date for complete tab */
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        date = yyyy+'-'+mm+'-'+dd;
    }

    $("#dt-3").DataTable( {
        processing: true,
        serverSide: true,
        paging: true,
        order : [[ 1, "desc" ]],
        search: { "caseInsensitive": false },
        ajax: "<?php echo e(route('admin.process.hfc.showhfc2',1)); ?>",
        //ajax: { "url" : "include/datatable/dt-3.php" },
        columns: [
                    { data: "TRNREFNO" },                    
                    { data: "datetime" },
                    { data: "fullName" },
                    { data: "error_section" },
                    { data: "exception_dtl" }
                ],
        dom: '<"toolbar"lB<"#filters3">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        scrollX: true,
        "bDestroy": true,
        scrollY: "400px",
        scrollCollapse: true,
        initComplete: function () {
            $('.buttons-csv').html('<i class="fa fa-download" aria-hidden="true"></i>');
        },
        drawCallback: function( settings ) {
            //afterDT3();
        },
        language: { "lengthMenu": "Show _MENU_" },
        columnDefs: [
            { "searchable": false, "targets": [] }, { "searchable": true, "targets": [0,1,2,3,4] }
        ]

    } );

}

function setDT4(){ 
 $('#upload_csv').on("submit", function(e){
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
          });
        /*e.preventDefault();
        $.ajax({
            url: "<?php echo e(url('admin/process/uploads')); ?>",
            type:"POST",  
            data:new FormData(this),
            contentType:false,
            cache:false,
            processData:false,
            success: function(data){
                if(data == 2){
                toastr["error"]("Invalid File.");
                }
                else if(data == 3){
                toastr["error"]("Please Select File.");
                }
                else{
                    swal('Data Imported', 'Successfully!', 'success');
                }
                $("#input-file").removeClass("disabled");
                $("#upload").removeClass("disabled");
                $("#input-file").val("");
            }
        })*/
        e.preventDefault();

        var extension = $('#csv_file').val().split('.').pop().toLowerCase();
        if ($.inArray(extension, ['csv']) == -1) {
            //alert('Please Select Valid File... ');
            swal('Please Select Valid File... ');

        } else {
        var file_data = $('#csv_file').prop('files')[0];
        //var supplier_name = $('#supplier_name').val();
        var form_data = new FormData();
        form_data.append('csv_file', file_data);
        //form_data.append('supplier_name', supplier_name);

        $.ajax({
            //url:  "<?php echo e(route('admin.process.hfc.uploads',1)); ?>"
            url: "<?php echo e(url('admin/process/hfc/uploads')); ?>", // point to server-side PHP script
            data: form_data,
            type: 'POST',
            contentType: false, // The content type used when sending data to the server.
            cache: false, // To unable request pages to be cached
            processData: false,
            success: function(data) {
                console.log(data);
                if(data.status == 'success'){
                    swal(data.msg);
                }else if(data.status == 'error'){
                    swal(data.msg);
                }
                $("#csv_file").val("");
                $("#csv_file").removeClass("disabled");
            }
        });
    }
    });

}
// function accountcreate(userid,trnrefno){

//     $("#row_"+trnrefno).addClass("running disabled");
//     $("#wait").show();
//     $("#wait-loading-img").removeClass("none");
//     $.ajax({
//         url: SELENIUM_API+'finalfun/'+userid+'/'+trnrefno,
//         type:"GET",
//         dataType: 'json',
//         cache:false,
//         success: function(data){
//             toastr["success"]("Automation Completed.");
//             $("#row_"+trnrefno).removeClass("running disabled");
//             $("#wait").hide();
//             $("#wait-loading-img").addClass("none");
//             console.log(JSON.parse(data));
//         },
//         error: function(request,status,errorThrown) {
//             toastr["error"]("Something went wrong!");
//             $("#row_"+trnrefno).removeClass("running disabled");
//             $("#wait").hide();
//             $("#wait-loading-img").addClass("none");
//             console.log(errorThrown);
//             console.log(JSON.parse(request.responseText));
//         }
//     })

// }

function startAction(userid,trnrefno){

    $("#row_"+trnrefno).addClass("running disabled");
    $("#wait").show();
    $("#wait-loading-img").removeClass("none");    
    $.ajax({
        url:'<?php echo e(url('/api/process/hfc/start')); ?>',   
        type:"POST",
        data:{'userid':userid,'trnrefno':trnrefno},
        dataType: 'json',
        cache:false,
        success: function(data){
            toastr["success"]("Automation Completed.");
            $("#row_"+trnrefno).removeClass("running disabled");
            $("#wait").hide();
            $("#wait-loading-img").addClass("none");
            console.log(JSON.parse(data));
        },
        error: function(request,status,errorThrown) {
            toastr["error"]("Something went wrong!");
            $("#row_"+trnrefno).removeClass("running disabled");
            $("#wait").hide();
            $("#wait-loading-img").addClass("none");
            console.log(errorThrown);
            console.log(JSON.parse(request.responseText));
        }
    })

}

function showInfo(trnrefno){ 
    alert("Info Click");
    toastr["info"]("TRN REFERENCE NO: "+trnrefno+" is ready for automation.");
}
</script>
<style type="text/css">
    #dt-2_filter,#dt-1_filter,#dt-3_filter{
    float:right !important;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\rpa-panel\resources\views/admin/process/hfc/index.blade.php ENDPATH**/ ?>