
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html ng-app="bpoApps">
<head>
  <title>BPO Apps Console</title>
  <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no' />

  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/lib/angularmodules/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/keen-dashboards.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/style.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/lib/angularmodules/angular-material.css" />
 
   
  
    <style>
        body {
            padding: 15px;
        }

        .select2 > .select2-choice.ui-select-match {
            /* Because of the inclusion of Bootstrap */
            height: 29px;
        }

        .selectize-control > .selectize-dropdown {
            top: 36px;
        }
        /* Some additional styling to demonstrate that append-to-body helps achieve the proper z-index layering. */
        .select-box {
          background: #fff;
          position: relative;
          z-index: 1;
        }
        .alert-info.positioned {
          margin-top: 1em;
          position: relative;
          z-index: 10000; /* The select2 dropdown has a z-index of 9999 */
        }
    </style>
</head>
<body class="application" >

  <div class="navbar navbar-inverse navbar-fixed-top" role="navigation" ng-if="isSession()">
    <div class="container-fluid">
      <div class="navbar-header" >
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">
        
          <img src="<?php echo base_url();?>/assets/img/vara-logo.png">
        </a>
		<ul class="nav navbar-left" >
                <li>
                  <a href="#" class="user-profile">
				  <img src="{{WEB_URL+'storage/profile/'+SESS_USER.filename}}" alt="">{{SESS_USER.fullName}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo WEBSITE_INSTANCE; ?></strong>
                  </a>
                </li>
			</ul>
		
      </div>
      
	  <ul class="nav navbar-right">
                <li class="" >
                  <a href="#/login?logout=1" class="user-profile" data-toggle="dropdown" aria-expanded="false">Logout
                  </a>
                </li>
			</ul>
    </div>
             
      <nav class="navbar-collapse collapse graybg" ng-controller="menubarController" >

          <div class="container-fluid" >
          
            <ul class="nav navbar-nav" >
<!--              <li class="active"><a href>Home</a></li>-->
              <li class="dropdown" ng-repeat="menu in menulist.result" >
                <a class="dropdown-toggle" data-toggle="dropdown" href>{{menu.name}}
                <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li ng-repeat="childmenu in menu.child" style="width:100%"><a href="#/{{childmenu.url}}">{{childmenu.name}}</a></li>
                </ul>
              </li>
              
              
            </ul>
          </div>
        </nav>

      
  </div>









     <div ng-view="" class="page" >

    </div>

<!--<script type="text/javascript" src="<?php echo base_url();?>assets/js/image.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/gallerly.js"></script>
   <script type="text/javascript" src="<?php echo base_url();?>assets/js/main.js"></script>
-->  <script type="text/javascript" src="<?php echo base_url();?>assets/lib/holderjs/holder.js"></script>
  <script type="text/javascript" >
    Holder.add_theme("white", { background:"#fff", foreground:"#a7a7a7", size:10 });
  </script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/lib/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/lib/bootstrap/dist/js/bootstrap.min.js"></script>

  <script type="text/javascript" src="<?php echo base_url();?>assets/lib/holderjs/holder.js"></script>
  <script>
    Holder.add_theme("white", { background:"#fff", foreground:"#a7a7a7", size:10 });
  </script>

  <script type="text/javascript" src="<?php echo base_url();?>assets/lib/keen-js/dist/keen.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/js/meta.js"></script>
 
 

	<link rel="stylesheet" href="<?php echo base_url();?>assets/lib/angularmodules/angular-toastr.css" />
	<link rel="stylesheet" href="<?php echo base_url();?>assets/lib/angularmodules/ngProgress.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/lib/angularmodules/ng-table.min.css" />
	<link rel="stylesheet" href="<?php echo base_url();?>assets/lib/angularmodules/font-awesome.min.css" />
	
<!--	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/lib/angularmodules/bootstrap.css">-->
	<link rel="stylesheet" href="<?php echo base_url();?>assets/scripts/vControls/vcRadio/vcRadio.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>assets/scripts/vControls/vcCheckBox/vcCheckBox.css" />    
    <link rel="stylesheet" href="<?php echo base_url();?>assets/scripts/vControls/vcDDL/select.css" />
 

    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">    
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.css">

	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/autoScroll.css" /> 

    
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.css">



	<script src="<?php echo base_url();?>assets/lib/angularmodules/angular.js"></script>
	<script src="<?php echo base_url();?>assets/lib/angularmodules/mask.min.js"></script>

	<script src="<?php echo base_url();?>assets/lib/js-xlsx/shim.js"></script>
    <script src="<?php echo base_url();?>assets/lib/js-xlsx/jszip.js"></script>
    <script src="<?php echo base_url();?>assets/lib/js-xlsx/xlsx.js"></script>
    <!-- uncomment the next line here and in xlsxworker.js for ODS support -->
    <script src="<?php echo base_url();?>assets/lib/js-xlsx/ods.js"></script>


	<script src="<?php echo base_url();?>assets/lib/angularmodules/angular.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/angular-animate.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/angular-aria.min.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/angular-filter.min.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/angular-resource.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/angular-sanitize.min.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/angular-toastr.tpls.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/angular-touch.min.js"></script>
    
    <script src="<?php echo base_url();?>assets/lib/angularmodules/angular-route.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/loading-bar.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/ngprogress.min.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/ng-table.min.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/ngToast.min.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/ui-bootstrap-tpls-2.1.4.min.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/ui-bootstrap-tpls.min.js"></script>
    <script src="<?php echo base_url();?>assets/lib/angularmodules/angular-material.js"></script>
    
	 <script src="<?php echo base_url();?>assets/lib/ng-file-upload-bower-12.2.12/ng-file-upload-shim.min.js"></script>
     <script src="<?php echo base_url();?>assets/lib/ng-file-upload-bower-12.2.12/ng-file-upload.min.js"></script>
	<script src="<?php echo base_url();?>assets/lib/checklist-model-0.10.0/checklist-model.js"></script>
    <script src="<?php echo base_url();?>assets/lib/ng-table-to-csv.min.js"></script>

	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/lib/canvasviewer/assets/lib/json-formatter/dist/json-formatter.min.css">
	<!--endbower -->
	<link rel="stylesheet" href="<?php echo base_url();?>assets/lib/canvasviewer/src/CanvasViewer.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/lib/canvasviewer/assets/css/style.css">
	<!-- bower:js -->
	<script src="<?php echo base_url();?>assets/lib/canvasviewer/assets/lib/angular/angular.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url();?>assets/lib/canvasviewer/assets/lib/libtiff/tiff.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url();?>assets/lib/canvasviewer/assets/lib/pdfjs/pdf.compat.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url();?>assets/lib/canvasviewer/assets/lib/pdfjs/pdf.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url();?>assets/lib/canvasviewer/assets/lib/json-formatter/dist/json-formatter.min.js" type="text/javascript" charset="utf-8"></script>
	<!-- endbower -->
	<script src="<?php echo base_url();?>assets/lib/canvasviewer/src/FormatReader.js" type="text/javascript" charset="utf-8"></script>	
	<script src="<?php echo base_url();?>assets/lib/canvasviewer/src/CanvasViewer.js" type="text/javascript" charset="utf-8"></script>	
	

  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/config.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/app.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/constants.js"></script>




<!-- <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/vControls/vcRadio/vcRadio.js"></script>
 <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/vControls/vcCheckBox/vcCheckBox.js"></script>
  -->
 
   <script type="text/javascript" src="<?php echo base_url();?>assets/lib/maskdt/ngMask.js"></script>
   <script type="text/javascript" src="<?php echo base_url();?>assets/lib/maskdt/ngMask.min.js"></script>
   
   <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/vControls/vcRadio/vcRadio.js"></script>
   <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/vControls/vcDDL/select.js"></script>
   <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/vControls/vcDDL/vcDDL.js"></script>
   <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/vControls/vcSelect/vcSelect.js"></script>
   <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/vControls/vcText/vcText.js"></script>
   <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/vControls/vcCheckBox/vcCheckBox.js"></script>
   <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/vControls/vcCalendar/vcCalendar.js"></script>
 <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/vControls/vcFile/vcFile.js"></script>
   <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/vControls/vcMaskedDate/vcMasked.js"></script>

  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/Core/services.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/Core/controllers.js"></script>

  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/CC/services.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/CC/controllers.js"></script>

  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/ALPL/services.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/ALPL/controllers.js"></script>


</body>
</html>
