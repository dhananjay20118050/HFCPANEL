<?php

require_once '../import.php';

use
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\Mjoin,
    DataTables\Editor\Options,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate;

    $table = 'bot_aps_tracking a';
    $pk = 'a.TRNREFNO';
    $arr = ['b.APPLNO as appno','a.TRNREFNO as TRNREFNO','b.is_existing_cust_1 as is_existing_cust_1', 'b.cifid_1 as cifid_1','b.is_existing_cust_2 as is_existing_cust_2', 'b.cifid_2 as cifid_2','b.is_existing_cust_3 as is_existing_cust_3', 'b.cifid_3 as cifid_3','b.AccountNo as accountno','a.is_processed as processed','a.start_time as start_time', 'a.end_time as end_time','e.username as finnacleuser', 'a.upload_datetime as upload_datetime','c.fullName as fullName','d.fullName as upload_fullName'];

    foreach ($arr as $value) {
    	$field_arr[] = Field::inst($value);
    }

    if(isset($_POST['date'])){
        if($_POST['date'] != ""){
            $date = getWorkingOursByDate($_POST['date']);
        }else{
            $date = getWorkingOursByDate(date("Y-m-d"));
        }
    }else{
        $date = getWorkingOursByDate(date("Y-m-d"));
    }

    Editor::inst($db, $table, array($pk))
    ->fields($field_arr)
    ->leftJoin( 'hfccustdata b', 'b.TRNREFNO', '=', 'a.TRNREFNO' )
    ->leftJoin( 'coreusers c', 'c.userId', '=', 'a.userid' )
    ->leftJoin( 'coreusers d', 'd.userId', '=', 'a.upload_user' )
    ->leftJoin( 'bot_ip_logins e', 'e.id', '=', 'a.userid' )
    ->where( function ( $q ) {
      $q->where( 'a.status', "('Y')", 'IN', false );
    })
    // ->where( 'a.end_time', $date['start'], ">" )
    // ->where( 'a.end_time', $date['end'], "<" )
    ->distinct(true)
    ->process($_POST)
    ->json();

?>