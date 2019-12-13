<?php

    require_once('../import.php');
   
	$sql = "SELECT * FROM coreusers WHERE (empID = '".$_POST['user']."' OR emailId = '".$_POST['user']."') AND passWord = '".$_POST['pass']."'";
    $result = mysqli_query($conn,$sql);
    $rows = mysqli_num_rows($result);
    $rowsdata = mysqli_fetch_assoc($result);

    if($rows > 0){
        $res['result'] = $rowsdata;
    }else{
        $res['result'] = '';
    }

    echo json_encode($res);

?>