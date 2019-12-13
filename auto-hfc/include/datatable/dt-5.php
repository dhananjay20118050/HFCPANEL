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
    $arr = ['b.cifid_1 as custid','b.APPLNO as applno','a.TRNREFNO as TRNREFNO','a.upload_datetime as upload_datetime','a.userid as userid'];

    foreach ($arr as $value) {
        $field_arr[] = Field::inst($value);
    }

    Editor::inst($db, $table, array($pk))
    ->fields($field_arr)
    ->leftjoin( 'hfccustdata b', 'b.TRNREFNO', '=', 'a.TRNREFNO' )
    ->where( function ( $q ) {
      $q->where( 'a.status', "('EE','Y','PP')", 'IN', false );
    })
    ->distinct(true)
    ->process($_GET)
    ->json();

?>