<?php /* Collection Payout Project */?>
<!DOCtype html>
<html>
<head>
    <title>VARA RPA | ICICI HFC</title>
    <!-- Ignite UI Required Combined CSS files -->
    <link rel="shortcut icon" type="image/png" href="css/img/vu-new.png"/>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/toastr.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="lib/datatables_v1/datatables.min.css">
    <link href="css/style.css" rel="stylesheet" />
    <script src="lib/modernizr-2.8.3.js"></script>
    <script src="lib/jquery.min.js"></script>
    <script src="lib/jquery-ui.min.js"></script>
    <script src="lib/bootstrap.min.js"></script>
    <script src="lib/toastr.min.js"></script>
    <link rel="stylesheet" href="lib/datetimepicker/bootstrap-datetimepicker.min.css">
    <script src="lib/datetimepicker/bootstrap-datetimepicker.min.js"></script>
</head>
<body>
<div class="navbar navbar-default" id="navbar">
    <div class="container" id="navbar-container">
    <div class="navbar-header">
        <div class="navbar-brand">
            <img src="css/img/vu-new.png" width="50">
            <small>VARA RPA - ICICI HFC</small>
            <small id="instance-type"></small>
        </div>
    </div>
    <div class="navbar-header pull-right" role="navigation">
        <div class="user-menu-bar">
            <span id="product-title"></span>
            <span id="cycledate-title"></span>
            <input type="hidden" id="hid-processId" value="">
            <input type="hidden" id="hid-product" value="">
            <div class="btn-group btn-input clearfix" style="display:inline-block;">
                <button class="btn username dropdown-toggle" type="button" data-toggle="dropdown">
                  <span id="username-title" data-bind="lable"></span><span class="caret" style="margin-left: 5px;"></span>
                </button>
                <ul class="dropdown-menu" id="user-menu" role="menu">
                    <li><a href="#" id="logout" class="select-usermenu">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
    </div>
</div>
