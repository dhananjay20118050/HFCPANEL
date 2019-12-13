
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
    }else if(tab == "#t4"){
        setDT4();
    }else if(tab == "#t5"){
        setDT5();
    }else if(tab == "#t6"){
        setDT6();
    }else if(tab == "#t8"){
        setDT8();
    }else if(tab == "#t9"){
        setDT9();
    }else if(tab == "#t10"){
        setDT10();
    }else if(tab == "#t11"){
        setDT11();
    }else if(tab == "#t12"){
        setDT12();
    }else if(tab == "#t13"){
        setDT13();
    }else if(tab == "#t14"){
        setDT14();
    }else if(tab == "#t15"){
        setDT15();
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
        order : [[ 3, "desc" ]],
        search: { "caseInsensitive": false },
        ajax: { "url" : "include/sel/dt-1.php" },
        columns: [  
                    { data: "id" },
                    { data: "TRNREFNO" },
                    { data: "start_time" },
                    { data: "end_time" },
                    {   
                        mRender: function ( file_name, type, data ) {
                        return '<a href="'+SELENIUM_API+'start/1/0/'+data['TRNREFNO']+'" target="_blank"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Start"/></a><a href="#" class="info" data-value="No Info." id="info-'+data['TRNREFNO']+'"><span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" title="Info"/></a>';
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
    //<a href="#" id="start-q"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Start Queue"/></a>
            //$("#filters").html('<a href="#" id="refresh"><span class="glyphicon glyphicon-refresh" data-toggle="tooltip" title="Refresh"/></a><img src="css/img/loading.gif" class="none" id="loading-img">');
            afterDT1();
        },
        drawCallback: function( settings ) {
            //afterDT1();
        },
        "language": {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [4] },{ "searchable": true, "targets": [0,1,2,3] }]

    } );

    function afterDT1(){

        function startAutomation(){

            $("#start-q").addClass("disabled");
            $("#loading-img").removeClass("none");

            var table = $('#dt-1').DataTable();
            var apps = table.column(1).data().toArray();
            var len = apps.length;
            var count = 0;

            function singlestart(){
                $("#row_"+apps[count]).addClass("running disabled");
                $.ajax({
                    type: 'get',
                    url: SELENIUM_API+'start/1/'+userData.userId+'/'+apps[count],
                    async: true,
                    success: function(response) {
                        if(response.status == 'success'){
                            toastr["success"](response.message);
                        }else{
                            toastr["error"](response.message);
                        }
                        $("#info-"+apps[count]).attr("data-value",response.result);
                        $("#row_"+apps[count]).removeClass("running disabled");
                        if(count < len - 1){
                            count++;
                            singlestart();
                        }
                    }
                });
            }
            singlestart();
            $("#loading-img").addClass("none");
            $("#start-q").removeClass("disabled");
        }

         $(".info").click(function(){
            var val = $(this).data("value");
            console.log(val);
            alert(val);
        });

        /*$("#start-q").click(function(){
            startAutomation();
        });

         $("#refresh").click(function(){
            $(this).addClass("disabled");
            $("#loading-img").removeClass("none");
            $.ajax({
                 type: 'get',
                 url: SERVER_API+'refreshAPSTable/',
                 success: function(response) {
                    toastr["success"]("Refreshed.");
                    $(this).removeClass("disabled");
                    $("#loading-img").addClass("none");
                    selTab("#t1");
                 }
            });
        });*/
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
        destroy: true,
        paging: true,
        order : [[ 6, "desc" ]],
        search: { "caseInsensitive": false },
        ajax:{
                "url" : "include/sel/dt-2.php",
                "data" : { 'date' : date }
            },
        columns: [
                    { data: "txtAppFormNo" },
                    { data: "apsNo" },
                    { data: "batchId" },
                    { data: "start_user" },
                    { data: "resume_user" },
                    { data: "start_time" },
                    { data: "end_time" },
                    {  
                        mRender: function ( file_name, type, data ) {
                            var str = '';
                            if(data['status'] == 'R'){
                                $("#row_"+data['txtAppFormNo']).addClass("rejected");
                                return '<a href="#" class="view"><span class="glyphicon glyphicon-thumbs-down" data-toggle="tooltip" title="View Remarks"/></a>';
                            }
                            return str;
                        }
                    }
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
        "columnDefs": [{ "searchable": false, "targets": [5,7] },{ "searchable": true, "targets": [0,1,2,3,4,6] }]

    } );
}

function afterDT2(){
    $(".view").click(function(){
        var app = $(this).closest('td').siblings(":first").text();
        $.ajax({
            url:SELENIUM_API+"getRejectedData/"+app,
            type: 'get',
            success: function(data){
                $("#v-application").val(data.txtAppFormNo);
                $("#v-remarks").val(data.remarks);
                $("#v-ip-address").val(data.ip_address);
                $("#v-datetime").val(data.datetime);
                $("#v-user").val(data.fullName);
                $("#reject-view-modal").modal('show');
            }
        })
    });
    $("#reject-view-cancel").click(function(){
        $("#reject-view-modal").modal('hide');
    });
    $("#date-filter").change(function(){
        setDT2();
    });
}

function setDT3(){

    $("#dt-3").DataTable( {
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        order : [[ 5, "desc" ]],
        search: { "caseInsensitive": false },
        ajax: { "url" : "include/sel/dt-3.php" },
        columns: [
                    { data: "txtAppFormNo" },
                    { data: "apsNo" },
                    { data: "batchId" },
                    { data: "start_user" },
                    { data: "resume_user" },
                    { data: "start_time" },
                    { data: "end_time" },
                    {   
                        mRender: function ( file_name, type, data ) {
                            return '<a href="#" class="reject"><span class="glyphicon glyphicon-remove" data-toggle="tooltip" title="Remove"/></a>';
                        }
                    }
                ],
        dom: '<"toolbar"lB<"#filters">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        scrollX: true,
        scrollY: "400px",
        scrollCollapse: true,
        initComplete: function () {
            $('.buttons-csv').html('<span class="glyphicon glyphicon-download-alt" data-toggle="tooltip" title="Download"/>');
            $("#filters").html('<a href="#" id="start-q"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Start Queue"/></a><img src="css/img/loading.gif" class="none" id="loading-img">');
            afterDT3();
        },
        drawCallback: function( settings ) {
            afterDT3();
        },
        language: { "lengthMenu": "Show _MENU_" },
        columnDefs: [
            { "searchable": false, "targets": [6,7] },
            { "searchable": true, "targets": [0,1,2,3,4,5] }
        ]

    } );

    function afterDT3(){
        $(".glyphicon-play").click(function(){
            var id = $(this).closest('td').parent()[0].id;
            $("#"+id).addClass("running disabled");
        });
        $(".reject").click(function(){
            var app = $(this).closest('td').siblings(":first").text()
            $("#reject-modal").modal('show');
            $("#application").val(app);
        });
        $("#reject-cancel").click(function(){
            $("#remarks").val("");
            $("#reject-modal").modal('hide');
        });
    }
}

$("#reject-save").click(function(){
    var remarks = $("#remarks").val();
    var app = $("#application").val();
    if(remarks != ''){
        $.ajax({
            url:SELENIUM_API+"rejectApplication/",
            type: 'post',
            data: { "app" : app, "remarks" : remarks, "userId" : userData.userId},
            success: function(data){
                toastr["success"]("Rejected.");
                $("#reject-modal").modal('hide');
                $("#remarks").val("");
                setDT3()
            }
        })
    }
});

function setDT4(){
    var date = $("#date-filter4").val();
    if(date == undefined){
        /* set date for complete tab */
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        date = yyyy+'-'+mm+'-'+dd;
    }
    $("#dt-4").DataTable( {
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        order : [[ 3, "desc" ]],
        search: { "caseInsensitive": false },
        ajax:{
                "url" : "include/sel/dt-4.php",
                "data" : { 'date' : date }
            },
        columns: [
                    { data: "txtAppFormNo" },
                    { data: "apsNo" },
                    { data: "exception_dtl" },
                    { data: "datetime" },
                    { data: "process_dtl_desc" },
                    { data: "fullName" }
                ],
        dom: '<"toolbar"lB<"#filters4">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        scrollX: true,
        scrollY: "400px",
        scrollCollapse: true,
        initComplete: function () {
            $('.buttons-csv').html('<span class="glyphicon glyphicon-download-alt" data-toggle="tooltip" title="Download"/>');
            $("#filters4").html('<div class="form-group"><input type="date" id="date-filter4" class="form-control"></div>');
            afterDT4();
            if(date != ""){
                $("#date-filter4").val(date);
            }
        },
        drawCallback: function( settings ) {
            afterDT4();
        },
        language: {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": true, "targets": [0,1,2,3,4,5] }]

    } );
}

function afterDT4(){
    $("#date-filter4").change(function(){
        setDT4();
    });
}

function setDT5(){

    var process_dtl = [ 'Login','Sourcing Details','Applicant Personal','Applicant Other Details','Address','Work Detail','Income Expense','Bank','References','Asset & Loan Details','Other Charges','Change Stage','Pre-sanction','Download Cibil'];

    $("#dt-5").DataTable( {
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        order : [[ 5, "desc" ]],
        search: { "caseInsensitive": false },
        ajax: { "url" : "include/sel/dt-5.php" },
        columns: [
                    { data: "txtAppFormNo" },
                    { data: "apsNo" },
                    { data: "batchId" },
                    { data: "start_user" },
                    { data: "resume_user" },
                    { data: "datetime" },
                    {
                        mRender: function ( file_name, type, data ) {
                            var str = '<select id="process" class="form-control selProcess">';
                            $.each(process_dtl, function( index, value ) {
                                if(data['last_process_entry'] == index){
                                    str = str + '<option value="'+index+'" selected>'+value+'</option>';
                                }else{
                                    str = str + '<option value="'+index+'">'+value+'</option>';
                                }
                            });
                            str = str + '</select>';
                            return str;
                        }
                    },
                    {
                        mRender: function ( file_name, type, data ) {
                            var str = '<select id="end-process" class="form-control selEndProcess">';
                            var len = process_dtl.length - 1;
                            $.each(process_dtl, function( index, value ) {
                                if(index == len){
                                    str = str + '<option value="'+index+'" selected>'+value+'</option>';
                                }else{
                                    str = str + '<option value="'+index+'">'+value+'</option>';
                                }
                            });
                            str = str + '</select>';
                            return str;
                        }
                    },
                    {   
                        mRender: function ( file_name, type, data ) {
                            var process_id = parseInt(data['last_process_entry']) + 1;
                            var complete = "";
                            if(data['status'] == 'Y'){
                                complete = "green-color";
                            }
                            if(data['status'] == 'P'){
                                complete = "red-color";
                            }
                            var actions = '';
                            if(process_id <= 2){
                                actions += '<a href="'+SELENIUM_API+'start/1/'+userData.userId+'/'+data['txtAppFormNo']+'" target="_blank" class="'+complete+'"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Start" class="'+complete+'"/></a>';
                            }else{
                                actions += '<a href="'+SELENIUM_API+'resume/1/'+userData.userId+'/'+data['txtAppFormNo']+'/'+process_id+'" target="_blank" class="'+complete+'"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Resume"/></a>';
                            }
                            return actions += '<a href="'+SERVER_PATH+'/#/ALPL/pldataEntry?process=2&appref='+data['txtAppFormNo']+'&appserial=0" target="_blank"><span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="Resume"/></a>';
                        }
                    }
                ],
        dom: '<"toolbar"lB<"#filters5">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        scrollX: true,
        scrollY: "400px",
        scrollCollapse: true,
        initComplete: function () {
            $('.buttons-csv').html('<span class="glyphicon glyphicon-download-alt" data-toggle="tooltip" title="Download"/>');
            $('#filters5').html('<a href="#" id="refresh5"><span class="glyphicon glyphicon-refresh" data-toggle="tooltip" title="Refresh"/></a><img src="css/img/loading.gif" class="none" id="loading-img5">');
            afterDT5();
        },
        drawCallback: function( settings ) {
            afterDT5();
        },
        language: {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [6,7,8] },{ "searchable": true, "targets": [0,1,2,3,4,5] }]

    } );
}

function afterDT5(){

    $("#refresh5").click(function(){
        $("#refresh5").addClass("disabled");
        $("#loading-img5").removeClass("none");
        $.ajax({
             type: 'get',
             url: SERVER_API+'refreshAPSTable/',
             success: function(response) {
                toastr["success"]("Refreshed.");
                $("#refresh5").removeClass("disabled");
                $("#loading-img5").addClass("none");
             }
        });
    });

    $(".selProcess").change(function(){

        var process_id = parseInt($(this).val()) + 1;
        var txtAppFormNo = $(this).parent().siblings(":first").text();
        var end_process_id = parseInt($(this).closest('td').next('td').find('select:first').val()) + 1;

        if(process_id <= end_process_id){
            if(process_id <= 2){
                var newUrl = SELENIUM_API+'start/1/'+userData.userId+'/'+txtAppFormNo;
            }else{
                var newUrl = SELENIUM_API+'resume/1/'+userData.userId+'/'+txtAppFormNo+'/'+process_id+'/'+end_process_id;
            }
            $(this).closest('td').next('td').next('td').find('a:first').attr("href", newUrl);
        }else{
            toastr["error"]("Invalid Selection!");
        }

    });

    $(".selEndProcess").change(function(){

        var end_process_id = parseInt($(this).val()) + 1;
        var txtAppFormNo = $(this).parent().siblings(":first").text();
        var process_id = parseInt($(this).closest('td').prev('td').find('select:first').val()) + 1;

        if(process_id <= end_process_id){
            if(process_id <= 2){
                var newUrl = SELENIUM_API+'start/1/'+userData.userId+'/'+txtAppFormNo;
            }else{
                var newUrl = SELENIUM_API+'resume/1/'+userData.userId+'/'+txtAppFormNo+'/'+process_id+'/'+end_process_id;
            }
            $(this).closest('td').next('td').find('a:first').attr("href", newUrl);
        }else{
            toastr["error"]("Invalid Selection!");
        }

    });
}

function setDT6(){

    $("#dt-6").DataTable( {
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        order : [[ 4, "desc" ]],
        search: { "caseInsensitive": false },
        ajax: { "url" : "include/sel/dt-6.php" },
        columns: [
                    { data: "txtAppFormNo" },
                    { data: "apsNo" },
                    { data: "batchId" },
                    { data: "cibilRefNo" },
                    { data: "end_time" },
                    {   
                        mRender: function ( file_name, type, data ) {
                            return '';
                        }
                    }
                ],
        dom: '<"toolbar"lB<"#filters6"><"#cibil-div">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        "scrollX": true,
        "scrollY": "400px",
        "scrollCollapse": true,
        initComplete: function () {
            $('.buttons-csv').html('<span class="glyphicon glyphicon-download-alt" data-toggle="tooltip" title="Download"/>');
            $("#filters6").html('<a href="#" id="save-ref"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Save Reference"/></a><img src="css/img/loading.gif" class="none" id="loading-img6">');
            afterDT6();
        },
        drawCallback: function( settings ) {
            //afterDT1();
        },
        "language": {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [5] },{ "searchable": true, "targets": [0,1,2,3,4] }]

    } );

    function afterDT6(){
        $("#save-ref").click(function(){
            saveCibilRef();
        });
        function saveCibilRef(){
            var table = $('#dt-6').DataTable();
            var apps = table.column(1).data().toArray();
            if(apps.length > 0){
                $("#loading-img6").removeClass("none");
                $("#save-ref").addClass("disabled");
                $.ajax({
                    type: 'get',
                    url: SELENIUM_API+'saveCibilRef/'+apps,
                    async: true,
                    dataType: 'json',
                    success: function(response) {
                        toastr["success"]('Cibil Downloaded.');
                        $("#loading-img6").addClass("none");
                        $("#save-ref").removeClass("disabled");
                        selTab("#t6");
                    }
                });
            }
        }
    }
}

$('#upload_csv').on("submit", function(e){
    e.preventDefault(); //form will not submitted
    $("#input-file").addClass("disabled");
    $("#upload").addClass("disabled");
    $.ajax({
       // url:SELENIUM_API+"uploadCSV/",
       url:"include/imports/upload.php",
        method:"POST",  
        data:new FormData(this),
        contentType:false,          // The content type used when sending data to the server.  
        cache:false,                // To unable request pages to be cached  
        processData:false,          // To send DOMDocument or non processed data file it is set to false  
        success: function(data){  
            if(data == 2){
               toastr["error"]("Invalid File.");
            }
            else if(data == 3){
               toastr["error"]("Please Select File.");
            }
            else{
                toastr["success"]("Download Completed.");
            }
            $("#input-file").removeClass("disabled");
            $("#upload").removeClass("disabled");
            $("#input-file").val("");
        }
    })
});

function setDT8(){

    $("#dt-8").DataTable( {
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        order : [[ 3, "desc" ]],
        search: { "caseInsensitive": false },
        ajax: { "url" : "include/sel/dt-8.php" },
        columns: [  
                    { data: "id" },
                    { data: "txtAppFormNo" },
                    { data: "batchId" },
                    { data: "updated_date" },
                    { data: "start_time" },
                    { data: "end_time" },
                    {   
                        mRender: function ( file_name, type, data ) {
                        return '<a href="'+SELENIUM_API+'start/1/'+userData.userId+'/'+data['txtAppFormNo']+'" target="_blank"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Start"/></a><a href=SELENIUM_API+"delete/1/'+data['txtAppFormNo']+'" target="_blank"><span class="glyphicon glyphicon-remove" data-toggle="tooltip" title="Remove"/></a><a href="#" class="info" data-value="No Info." id="info-'+data['txtAppFormNo']+'"><span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" title="Info"/></a>';
                        }
                    }
                ],
        dom: '<"toolbar"lB<"#filters8">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        "scrollX": true,
        "scrollY": "400px",
        "scrollCollapse": true,
        initComplete: function () {
            $('.buttons-csv').html('<span class="glyphicon glyphicon-download-alt" data-toggle="tooltip" title="Download"/>');
            $("#filters8").html('<a href="#" id="start-q8"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Start Queue"/></a><a href="#" id="refresh8"><span class="glyphicon glyphicon-refresh" data-toggle="tooltip" title="Refresh"/></a><img src="css/img/loading.gif" class="none" id="loading-img8">');
            afterDT8();
        },
        drawCallback: function( settings ) {
            //afterDT1();
        },
        "language": {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [6] },{ "searchable": true, "targets": [0,1,2,3,4,5] }]


    } );

    function afterDT8(){

        function startAutomation8(){

            $("#start-q8").addClass("disabled");
            $("#loading-img8").removeClass("none");

            var table = $('#dt-8').DataTable();
            var apps = table.column(1).data().toArray();
            var len = apps.length;
            var count = 0;

            function singlestart8(){
                $("#row_"+apps[count]).addClass("running disabled");
                $.ajax({
                    type: 'get',
                    url: SELENIUM_API+'start/1/'+userData.userId+'/'+apps[count],
                    async: true,
                    success: function(response) {
                        if(response.status == 'success'){
                            toastr["success"](response.message);
                        }else{
                            toastr["error"](response.message);
                        }
                        $("#info-"+apps[count]).attr("data-value",response.result);
                        $("#row_"+apps[count]).removeClass("running disabled");
                        if(count < len - 1){
                            count++;
                            singlestart8();
                        }
                    }
                });
            }
            singlestart8();
            $("#loading-img8").addClass("none");
            $("#start-q8").removeClass("disabled");
        }

        $("#start-q8").click(function(){
            startAutomation8();
        });

        $("#refresh8").click(function(){
            $(this).addClass("disabled");
            $("#loading-img8").removeClass("none");
            $.ajax({
                 type: 'get',
                 url: SERVER_API+'refreshAPSTable/',
                 success: function(response) {
                    toastr["success"]("Refreshed.");
                    $(this).removeClass("disabled");
                    $("#loading-img8").addClass("none");
                    selTab("#t8");
                 }
            });
        });
        $(".info").click(function(){
            var val = $(this).data("value");
            console.log(val);
            alert(val);
        });
    }
}


function setDT9(){

    var process_dtl = [ 'Login','Sourcing Details','Applicant Personal','Applicant Personal Other Details','Address','Work Detail','Income Expense','Bank','References','Asset & Loan Details','Other Charges','Change Stage','Pre-sanction','Download Cibil'];

    $("#dt-9").DataTable( {
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        order : [[ 3, "desc" ]],
        search: { "caseInsensitive": false },
        ajax: { "url" : "include/sel/dt-9.php" },
        columns: [
                    { data: "txtAppFormNo" },
                    { data: "apsNo" },
                    { data: "batchId" },
                    { data: "datetime" },
                    {
                        mRender: function ( file_name, type, data ) {
                            var str = '<select id="process9" class="form-control selProcess">';
                            $.each(process_dtl, function( index, value ) {
                                if(data['last_process_entry'] == index){
                                    str = str + '<option value="'+index+'" selected>'+value+'</option>';
                                }else{
                                    str = str + '<option value="'+index+'">'+value+'</option>';
                                }
                            });
                            str = str + '</select>';
                            return str;
                        }
                    },
                    {
                        mRender: function ( file_name, type, data ) {
                            var str = '<select id="end-process9" class="form-control selEndProcess">';
                            var len = process_dtl.length - 1;
                            $.each(process_dtl, function( index, value ) {
                                if(index == len){
                                    str = str + '<option value="'+index+'" selected>'+value+'</option>';
                                }else{
                                    str = str + '<option value="'+index+'">'+value+'</option>';
                                }
                            });
                            str = str + '</select>';
                            return str;
                        }
                    },
                    {   
                        mRender: function ( file_name, type, data ) {
                            var process_id = parseInt(data['last_process_entry']) + 1;
                            var actions = '';
                            if(process_id <= 2){
                                actions += '<a href="'+SELENIUM_API+'start/1/'+userData.userId+'/'+data['txtAppFormNo']+'" target="_blank"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Start"/></a>';
                            }else{
                                actions += '<a href="'+SELENIUM_API+'resume/1/'+userData.userId+'/'+data['txtAppFormNo']+'/'+process_id+'" target="_blank"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Resume"/></a>';
                            }
                            return actions += '<a href="'+SERVER_PATH+'/#/ALPL/pldataEntry?process=2&appref='+data['txtAppFormNo']+'&appserial=0" target="_blank"><span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="Resume"/></a>';
                        }
                    }
                ],
        dom: '<"toolbar"lB<"#filters">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        scrollX: true,
        scrollY: "400px",
        scrollCollapse: true,
        initComplete: function () {
            $('.buttons-csv').html('<span class="glyphicon glyphicon-download-alt" data-toggle="tooltip" title="Download"/>');
            afterDT5();
        },
        drawCallback: function( settings ) {
            afterDT5();
        },
        language: {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [4,5,6] },{ "searchable": true, "targets": [0,1,2,3] }]

    } );
}

function setDT10(){
    $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii'});
    $("#report-submit").click(function(){
        var dtStart = $("#dt-start").val();
        var dtEnd = $("#dt-end").val();
        if(dtStart != "" && dtStart != ""){
            var report = $("#report-list").val();
            var url = SERVER_API+'exportReport/'+report+'/'+dtStart+'/'+dtEnd;
            window.open(url, '_blank');
        }else{
            toastr["error"]("Select dates.!");
        }

    });
}

function setDT11(){

    $("#dt-11").DataTable( {
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        order : [[ 3, "desc" ]],
        search: { "caseInsensitive": false },
        ajax: { "url" : "include/sel/dt-11.php" },
        columns: [
                    { data: "txtAppFormNo" },
                    { data: "apsNo" },
                    { data: "batchId" },
                    { data: "end_time" },
                    {   
                        mRender: function ( file_name, type, data ) {
                            return '';
                        }
                    }
                ],
        dom: '<"toolbar"lB<"#filters11"><"#cam-div">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        "scrollX": true,
        "scrollY": "400px",
        "scrollCollapse": true,
        initComplete: function () {
            $('.buttons-csv').html('<span class="glyphicon glyphicon-download-alt" data-toggle="tooltip" title="Download"/>');
            $("#filters11").html('<a href="#" id="save-cam"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Save Reference"/></a><img src="css/img/loading.gif" class="none" id="loading-img11">');
            afterDT6();
        },
        drawCallback: function( settings ) {
            //afterDT1();
        },
        "language": {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [4] },{ "searchable": true, "targets": [0,1,2,3] }]

    } );

    function afterDT6(){
        $("#save-cam").click(function(){
            saveCamData();
        });
        function saveCamData(){
            var table = $('#dt-11').DataTable();
            var apps = table.column(0).data().toArray();
            if(apps.length > 0){
                $("#loading-img11").removeClass("none");
                $("#save-cam").addClass("disabled");
                $.ajax({
                    type: 'get',
                    url: SELENIUM_CAM_API+'saveCamData/'+apps,
                    async: true,
                    dataType: 'json',
                    success: function(response) {
                        toastr["success"]('CAM Detials Saved.');
                        $("#loading-img11").addClass("none");
                        $("#save-cam").removeClass("disabled");
                        selTab("#t11");
                    }
                });
            }
        }
    }
}

/*** Process - 12 Start QC Automation Starts ***/

function setDT12(){

    $("#dt-12").DataTable( {
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        order : [[ 5, "asc" ]],
        search: { "caseInsensitive": false },
        ajax: { "url" : "include/sel/dt-12.php" },
        columns: [
                    { data: "txtAppFormNo" },
                    { data: "apsNo" },
                    { data: "batchId" },
                    { data: "bde_user" },
                    { data: "auto_user" },
                    { data: "end_time" },
                    {
                        mRender: function ( file_name, type, data ) {
                            return '';
                        }
                    }
                ],
        dom: '<"toolbar"lB<"#filters12"><"#qc-div">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        "scrollX": true,
        "scrollY": "400px",
        "scrollCollapse": true,
        initComplete: function () {
            $('.buttons-csv').html('<span class="glyphicon glyphicon-download-alt" data-toggle="tooltip" title="Download"/>');
            $("#filters12").html('<a href="#" onclick="startQCAuto()" id="start-qc-auto"><span class="glyphicon glyphicon-play" data-toggle="tooltip" title="Start QC Automation"/></a><img src="css/img/loading.gif" class="none" id="loading-img12">');
        },
        "language": {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [6] },{ "searchable": true, "targets": [0,1,2,3,4,5] }]
    } );
}
function startQCAuto(){
    var table = $('#dt-12').DataTable();
    var apps = table.column(0).data().toArray();
    if(apps.length > 0){
        $("#loading-img12").removeClass("none");
        $("#start-qc-auto").addClass("disabled");
        $.ajax({
            type: 'get',
            url: SELENIUM_QC_AUTO_API+'startAutoQC/'+apps,
            async: true,
            dataType: 'json',
            success: function(response) {
                toastr["success"]('QC Automation Done.');
                $("#loading-img12").addClass("none");
                $("#start-oc-auto").removeClass("disabled");
                selTab("#t12");
            }
        });
    }
}
/*** Process - 12 Start QC Automation Ends ***/

/*** Process - 13 Display Exception QC Starts ***/
function setDT13(){
    $("#dt-13").DataTable( {
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        order : [[ 6, "asc" ]],
        search: { "caseInsensitive": false },
        ajax:{  "url" : "include/sel/dt-13.php" },
        columns: [
                    { data: "txtAppFormNo" },
                    { data: "apsNo" },
                    { data: "batchId" },
                    { data: "bde_user" },
                    { data: "auto_user" },
                    { data: "auto_end_time" },
                    { data: "qc_start_time" },
                    { data: "qc_end_time" },
                    {
                        mRender: function ( file_name, type, data ) {
                            return data['total_errors']+'<a href="#" onclick="showQCExceptions(this)" class="error-link">view errors</a>';
                        }
                    },
                    {
                        mRender: function ( file_name, type, data ) {
                            return '<a href="#" onclick="qcSendToComplete(this)"><span class="glyphicon glyphicon-ok green-color" data-toggle="tooltip" title="Send To Complete"/></a><a href="#" onclick="qcSendToResume(this)"><span class="glyphicon glyphicon-remove red-dark" data-toggle="tooltip" title="Send To Resume"/></a>';
                        }
                    }
                ],
        dom: '<"toolbar"lB<"#filters13"><"#qc_complete-div">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        "scrollX": true,
        "scrollY": "400px",
        "scrollCollapse": true,
        initComplete: function () {
            $('.buttons-csv').html('<span class="glyphicon glyphicon-download-alt" data-toggle="tooltip" title="Download"/>');
        },
        "language": {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [8,9] },{ "searchable": true, "targets": [0,1,2,3,4,5,6,7] }]
    } );
}
function showQCExceptions(element){
    $("#qc-exception-modal").modal('show');
    var app = $(element).closest('td').siblings(":first").text();
    $("#qc-app-exp").html(app + ' &nbsp;<a href="'+SERVER_PATH+'/#/ALPL/pldataEntry?process=2&appref='+app+'&appserial=0" target="_blank"><span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="Open BDE"/></a>');
    $.ajax({
        url:SELENIUM_QC_AUTO_API+"showQCExceptions/"+app,
        type: 'get',
        dataType: 'json',
        success: function(data){
            $("#exception-tab-content").html(data.result);
        }
    })
}
/*** Process - 13 Display Exception QC Ends ***/

/*** Process - 14 Display Completed QC Starts ***/
function setDT14(){
    $("#dt-14").DataTable( {
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        order : [[ 7, "asc" ]],
        search: { "caseInsensitive": false },
        ajax:{  "url" : "include/sel/dt-14.php" },
        columns: [
                    { data: "txtAppFormNo" },
                    { data: "apsNo" },
                    { data: "batchId" },
                    { data: "bde_user" },
                    { data: "auto_user" },
                    { data: "auto_end_time" },
                    { data: "qc_start_time" },
                    { data: "qc_end_time" },
                    { data: "aps_status" },
                    {
                        mRender: function ( file_name, type, data ) {
                            return '';
                        }
                    }
                ],
        dom: '<"toolbar"lB<"#filters14"><"#qc_complete-div">f>rt<"bottom"ip><"clear">',
        buttons: ['csv'],
        lengthMenu: [[5, 10, 15, 20, 25, 50, 100, -1], [5, 10, 15, 20, 25, 50, 100, "All"]],
        pageLength: 10,
        "scrollX": true,
        "scrollY": "400px",
        "scrollCollapse": true,
        initComplete: function () {
            $('.buttons-csv').html('<span class="glyphicon glyphicon-download-alt" data-toggle="tooltip" title="Download"/>');
        },
        "language": {
            "lengthMenu": "Show _MENU_"
        },
        "columnDefs": [{ "searchable": false, "targets": [9] },{ "searchable": true, "targets": [0,1,2,3,4,5,6,7,8] }]
    } );
}
/*** Process - 14 Display Completed QC Ends ***/