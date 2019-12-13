
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

function selTab(tab){
   
    if(tab == "#t2"){
        setDT2();
    }else if(tab == "#t3"){
        setDT3();
    }else if(tab=="#t5"){
        setDT5();
    }else{
        setDT1();
    }
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
       $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
    });
}

function setDT1(){
    $("#dt-1").DataTable( { 
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        order : [[ 2, "desc" ]],
        search: { "caseInsensitive": false },
        ajax: { "url" : "include/datatable/dt-1.php" },
        columns: [  
                    { data: "TRNREFNO" },
                    { data: "APPLNO" },
                    { data: "upload_datetime" },
                    { data: "fullName" },
                    { data: "ipaddress" },
                    {   
                        mRender: function ( file_name, type, data ) {
                            return '<a href="#" onclick="startAction('+data['userid']+','+data['TRNREFNO']+')"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Start"/></a><a href="#" class="info" data-value="No Info." onclick="showInfo('+data['TRNREFNO']+')"><span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" title="Info"/></a>';
                        }
                    }
                ],
        dom: '<"toolbar"lB<"#filters">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        "scrollX": true,
        "scrollY": "400px",
        "scrollCollapse": true,
        initComplete: function () {
            $('.buttons-csv').html('<span class="glyphicon glyphicon-download-alt" data-toggle="tooltip" title="Download"/>');

            $("#filters").html('<a href="#" id="start-q" disabled><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Start Queue"/></a><img src="css/img/loading.gif" class="none" id="loading-img">');
        },
        drawCallback: function( settings ) {
        },
        "language": {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [5] },{ "searchable": true, "targets": [0,1,2,3,4] }]

    } );

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
        destroy: true,
        paging: true,
        order : [[ 9, "desc" ]],
        search: { "caseInsensitive": false },
        ajax:{
                "url" : "include/datatable/dt-2.php",
                "data" : { 'date' : date }
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
                    { data: "start_time" },
                    { data: "end_time" },
                    { data: "finnacleuser" },
                    { data: "upload_fullName" },
                    { data: "upload_datetime" }
                  //  {
                        //mRender: function ( file_name, type, data ) {
                          //  return '<a href="#" class="view"><span class="glyphicon glyphicon-info" data-toggle="tooltip" title="View Remarks"/></a>';
                       // }
                    //}
                ],

        dom: '<"toolbar"lB<"#filters2">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        "scrollX": true,
        "scrollY": "400px",
        "scrollCollapse": true,
        initComplete: function () {
            $('.buttons-csv').html('<span class="glyphicon glyphicon-download-alt" data-toggle="tooltip" title="Download"/>');
            $("#filters2").html('<div class="form-group"><input type="date" id="date-filter" class="form-control"></div>');
            afterDT2();
            if(date != ""){
                $("#date-filter").val(date);
            }
        },
        drawCallback: function( settings ) {
            afterDT2();
        },
        "language": {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [2,4,6] },{ "searchable": true, "targets": [0,1,3,5,7,8,9,10,11,12] }]

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
        destroy: true,
        paging: true,
        order : [[ 1, "desc" ]],
        search: { "caseInsensitive": false },
        ajax: { "url" : "include/datatable/dt-3.php" },
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
        scrollY: "400px",
        scrollCollapse: true,
        initComplete: function () {
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

function setDT5(){
     var date = $("#date-filter").val();
    if(date == undefined){
        /* set date for complete tab */
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        date = yyyy+'-'+mm+'-'+dd;
    }

    $("#dt-5").DataTable( {
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        order : [[1, "desc" ]],
        search: { "caseInsensitive": false },
        ajax: { "url" : "include/datatable/dt-5.php" },
        columns: [
                    { data: "custid" },                    
                    { data: "applno" },
                    { data: "TRNREFNO" },
                    { data: "upload_datetime" },
                    {   
                        mRender: function ( file_name, type, data ) {
                            return '<a href="#" onclick="accountcreate('+data['userid']+','+data['TRNREFNO']+')"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Start"/></a><a href="#" class="info" data-value="No Info." onclick="showInfo('+data['TRNREFNO']+')"><span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" title="Info"/></a>';
                        }
                    }
                ],
        dom: '<"toolbar"lB<"#filters3">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        scrollX: true,
        scrollY: "400px",
        scrollCollapse: true,
        initComplete: function () {
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

$('#upload_csv').on("submit", function(e){
    e.preventDefault(); //form will not submitted
    $("#input-file").addClass("disabled");
    $("#upload").addClass("disabled");
    $.ajax({
       url:"include/upload/upload.php",
        method:"POST",  
        data:new FormData(this),
        dataType: 'json',
        contentType:false,          // The content type used when sending data to the server.  
        cache:false,                // To unable request pages to be cached  
        processData:false,          // To send DOMDocument or non processed data file it is set to false  
        success: function(data){
            toastr[data.status](data.msg);
            $("#input-file").removeClass("disabled");
            $("#upload").removeClass("disabled");
            $("#input-file").val("");
        }
    })
});

function accountcreate(userid,trnrefno){

    $("#row_"+trnrefno).addClass("running disabled");
    $("#wait").show();
    $("#wait-loading-img").removeClass("none");
    $.ajax({
        url: SELENIUM_API+'finalfun/'+userid+'/'+trnrefno,
        type:"GET",
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

function startAction(userid,trnrefno){

    $("#row_"+trnrefno).addClass("running disabled");
    $("#wait").show();
    $("#wait-loading-img").removeClass("none");
    $.ajax({
        url: SELENIUM_API+'start/'+userid+'/'+trnrefno,
        type:"GET",
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
    toastr["info"]("TRN REFERENCE NO: "+trnrefno+" is ready for automation.");
}