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

    $table = 'bot_error_logs a';
    $pk = 'a.TRNREFNO';
    $arr = ['a.TRNREFNO as TRNREFNO','CONVERT(a.exception_dtl USING utf8) as exception_dtl', 'a.datetime as datetime', 'a.error_section as error_section', 'e.fullName as fullName'];

    foreach ($arr as $value) {
    	$field_arr[] = Field::inst($value);
    }

    if(isset($_GET['date'])){
        if($_GET['date'] != ""){
            $date = getWorkingOursByDate($_GET['date']);
        }else{
            $date = getWorkingOursByDate(date("Y-m-d"));
        }
    }else{
        $date = getWorkingOursByDate(date("Y-m-d"));
    }

    //print_r($date);exit;

    Editor::inst($db, $table, array($pk))
    ->fields($field_arr)
    ->leftJoin('bot_aps_tracking c', 'c.TRNREFNO', '=', 'a.TRNREFNO')
    ->leftJoin('coreusers e', 'e.userId', '=', 'a.userId')
  // ->where( 'a.datetime', $date['start'], ">" )
  // ->where( 'a.datetime', $date['end'], "<" )
    ->process($_GET)
    ->json();

?>