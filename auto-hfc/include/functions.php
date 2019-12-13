<?php

function getWorkingOursByDate($date = ""){

    if($date != ""){
        $current = strtotime(date("Y-m-d"));
        $date_t    = strtotime($date);
        $datediff = $date_t - $current;
        $difference = floor($datediff/(60*60*24));
        if($difference==0)
        {
            $hour = date('H', time());
            if($hour < 9){
                $mydate['start'] = date('Y-m-d', strtotime($date .' -1 day')).' 09:00:00';
                $mydate['end'] = date('Y-m-d', strtotime($date)).' 09:00:00';
            }
            else{
                $mydate['start'] = date('Y-m-d', strtotime($date)).' 09:00:00';
                $mydate['end'] = date('Y-m-d', strtotime($date .' +1 day')).' 09:00:00';
            }
        }else{
            $mydate['start'] = date('Y-m-d', strtotime($date)).' 09:00:00';
            $mydate['end'] = date('Y-m-d', strtotime($date .' +1 day')).' 09:00:00';
        }
    }else{
    	$mydate['start'] = '2018-01-01 00:00:00';
    	$mydate['end'] = '2020-01-01 00:00:00';
    }
    return $mydate;
}

function validate_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


?>
