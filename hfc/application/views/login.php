<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html ng-app="bpoApps">
<head>
  <title>Dashboard Starter UI, by Keen IO</title>
  <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no' />

  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/lib/angularmodules/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/keen-dashboards.css" />
</head>
<body class="application loginscreen">

  <div class="container">
  <div class="row">
    <div class="auth-box">
	<div class="chart-wrapper mainspace">
	<a href="#"><img src="<?php echo base_url();?>assets/img/vara-logo.png"></a>
    
            <div ng-view="" class="page" >
    
            </div>
  

 </div>
    </div>
  </div>
</div>

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



  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/app.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/constants.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/services.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/scripts/controllers.js"></script>


</body>
</html>
