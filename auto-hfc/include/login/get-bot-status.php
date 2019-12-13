<?php

    require_once('../import.php');

    $res = array('status' => 'success', 'msg' => 'No data found.');
   
	$sql = "SELECT * FROM `bot_aps_tracking` WHERE `userid` = ".$_SESSION['userId']." and `status` = 'P'";
    $result = mysqli_query($conn,$sql);
    $rows = mysqli_num_rows($result);
    $rowsdata = mysqli_fetch_assoc($result);

    if($rows > 0){
        $res = array('status' => 'error', 'msg' => 'You have existing running bot. TRN Ref No: '.$rowsdata['TRNREFNO']);
    }

    echo json_encode($res);

?>