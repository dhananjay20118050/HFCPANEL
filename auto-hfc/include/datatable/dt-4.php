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
    $pk = 'a.txtAppFormNo';
    $arr = ['a.txtAppFormNo as txtAppFormNo','CONVERT(a.exception_dtl USING utf8) as exception_dtl', 'a.datetime as datetime', 'd.process_dtl_desc as process_dtl_desc', 'd.process_dtl_id as process_dtl_id'];

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
    //->leftJoin('hfccustdata b', 'b.TRNREFNO', '=', 'a.txtAppFormNo')
    ->leftJoin('bot_aps_tracking c', 'c.TRNREFNO', '=', 'a.txtAppFormNo')
    ->leftJoin('bot_process_dtl d', 'd.process_dtl_id', '=', 'a.process_dtl_id')
   // ->leftJoin('coreusers e', 'e.userId', '=', 'a.userId')
  // ->where( 'a.datetime', $date['start'], ">" )
  // ->where( 'a.datetime', $date['end'], "<" )
    ->process($_GET)
    ->json();

?>