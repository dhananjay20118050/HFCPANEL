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
    $arr = ['a.ip_address as ipaddress','c.fullName as fullName','b.APPLNO as APPLNO','a.TRNREFNO as TRNREFNO','a.upload_user as upload_user','a.upload_datetime as upload_datetime','c.userid as userid'];

    foreach ($arr as $value) {
        $field_arr[] = Field::inst($value);
    }

    Editor::inst($db, $table, array($pk))
    ->fields($field_arr)
    ->leftjoin( 'hfccustdata b', 'b.TRNREFNO', '=', 'a.TRNREFNO' )
    ->leftjoin( 'coreusers c', 'a.upload_user', '=', 'c.userid' )
    ->where( function ( $q ) {
      $q->where( 'a.status', "('N','E','P')", 'IN', false );
    })
    ->distinct(true)
    ->process($_GET)
    ->json();

?>