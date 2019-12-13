<?php 

require_once '../import.php';

$files = $_FILES;
$response = array('status' => 0, 'msg' => 'Please Select File.');

if(!empty($files["input-file"]["name"])){

    $output = '';
    $ext = explode(".", $files["input-file"]["name"]);

    if($ext[1] == 'csv'){
        $filename = $ext[0];
        $filename = substr($filename, 3,8);
        $pattern = '/(\d{2}+)(\d{2}+)(\d{4}+)/i';
        $replacement = '$1-$2-$3';
        $newfile = preg_replace($pattern, $replacement, $filename);
        //echo $newfile;exit;
        $file_data = fopen($_FILES["input-file"]["tmp_name"], 'r');
        $appCount = 0;
        while($row = fgetcsv($file_data)){
          if($row[0] == "TRNREFNO"){continue;}
            
            if(!empty($row[0]) && !empty($row[1]) && !empty($row[3]) && !empty($row[4]) && !empty($row[5]) && !empty($row[6]) && !empty($row[9]) && !empty($row[10]) && !empty($row[11]) && !empty($row[12]) && !empty($row[13]) && !empty($row[14]) && !empty($row[17]) && !empty($row[18]) && !empty($row[19]) && !empty($row[20]) && !empty($row[21]) && !empty($row[22]) && !empty($row[23]) && !empty($row[25]) && !empty($row[26]) && !empty($row[27]) && !empty($row[28]) && !empty($row[31]) && !empty($row[39]) && !empty($row[47]) && !empty($row[48]) && !empty($row[49]) && !empty($row[50]) && !empty($row[51]) && !empty($newfile))
            { 
            if(is_numeric($row[0])){
               $a = validate_data($row[0]);
            }

           
            $b = validate_data($row[1]);

            if(is_numeric($row[3]))  {
               $d = validate_data($row[3]);
            }
            if(is_numeric($row[4]))  {
               $e = validate_data($row[4]);
            }
            $f =$row[5];

            $g =validate_data($row[6]);

            $h =validate_data($row[7]);
            $i =validate_data($row[8]);

            $j =validate_data($row[9]);
            $k =validate_data($row[10]);
            if(is_numeric($row[11]))  {
               $l = validate_data($row[11]);
            }
            $m =validate_data($row[12]);
            $n =validate_data($row[13]);
            $o =validate_data($row[14]);
            $r =validate_data($row[17]);
            $s =validate_data($row[18]);
            $t =validate_data($row[19]);

           if(is_numeric($row[20]))  {
               $u = validate_data($row[20]);
            }

            $v =validate_data($row[21]);

                      
             $w =validate_data($row[22]);
         
            
            $x =validate_data($row[23]);

            $dobexp = explode('/', $x);
            $day = validate_data($dobexp[1]);
            $month = validate_data($dobexp[0]);
            $year = validate_data($dobexp[2]);

            if(strlen($day) == 1){
                $day = '0'.$day;
            }
            if(strlen($month) == 1){
                $month = '0'.$month;
            }


            $dob = $day.'/'.$month.'/'.$year;


            $z =validate_data($row[25]);
            $a1 =validate_data($row[26]);
            $b1 =validate_data($row[27]);
           if(is_numeric($row[28]))  {
               $c1 = validate_data($row[28]);
            }
            $f1 =validate_data($row[31]);
            $n1 =validate_data($row[39]);
            $v1 =validate_data($row[47]);
            $w1 =validate_data($row[48]);

            if (filter_var($row[49], FILTER_VALIDATE_EMAIL)) {
                $x1 =validate_data($row[49]);
            }
            
            $mob =validate_data($row[50]);

            if (strlen($mob)==10 && is_numeric($mob)){
                $y1 = $mob;
            }
          
            $z1 =validate_data($row[51]);

            $c =$row[2];
            $p =$row[15];
            $q =$row[16];
            $y =$row[24];
            $d1 =$row[29];
            $e1 =$row[30];
            $g1 =$row[32];
            $h1 =$row[33];
            $i1 =$row[34];
            $j1 =$row[35];
            $k1 =$row[36];
            $l1 =$row[37];
            $m1 =$row[38];
            $o1 =$row[40];
            $p1 =$row[41];
            $q1 =$row[42];
            $r1 =$row[43];
            $s1 =$row[44];
            $t1 =$row[45];
            $u1 =$row[46];

            $sql = "INSERT INTO `hfccustdata`(`TRNREFNO`, `BRCODE`, `SBCODE`, `BNKSRL`, `APPLNO`, `NAME`, `ADD1`, `ADD2`, `ADD3`, `CITY`, `STATE`, `PIN`, `TYPE`, `TENURE`, `CATE`, `FOLIO`, `EMPCODE`, `STATUS`, `AMOUNT`, ` PAYMODE`, `INSTNO`, `INSTDT`, `PANGIR1`, `DOB`, `NGNAME`, `BANKAC`, `BANKNM`, `BCITY`, `MICR`, `GNAME`, `GPAN`, `ACTYPE`, `RTGSCOD`, `NNAME`, `NADD1`, `NADD2`, `NADD3`, `NCITY`, `NPIN`, `ENCL`, `TELNO`, `JH1NAME`, `JH2NAME`, `JH1PAN`, `JH2PAN`, `JH1RELATION`, `JH2RELATION`, `HLDINGPATT`, `SUBTYPE`, `EMAILID`, `MOBILENO`, `IFSC`,filename) VALUES ('".$a."','".$b."','".$c."','".$d."','".$e."','".$f."','".$g."','".$h."','".$i."','".$j."','".$k."','".$l."','".$m."','".$n."','".$o."','".$p."','".$q."','".$r."','".$s."','".$t."','".$u."','".$v."','".$w."','".$dob."','".$y."','".$z."','".$a1."','".$b1."','".$c1."','".$d1."','".$e1."','".$f1."','".$g1."','".$h1."','".$i1."','".$j1."','".$k1."','".$l1."','".$m1."','".$n1."','".$o1."','".$p1."','".$q1."','".$r1."','".$s1."','".$t1."','".$u1."','".$v1."','".$w1."','".$x1."','".$y1."','".$z1."','".$newfile."')";
            $result = mysqli_query($conn,$sql);
            if($result){
            $ipadd=getHostByName(getHostName());;
            $sql = "INSERT INTO `bot_aps_tracking` (`TRNREFNO`,`status`,`last_process_entry`,`ip_address`,`upload_user`, `upload_datetime`) VALUES ('".$a."', 'N', 0,'".$ipadd."',1,'".date('Y-m-d H:i:s')."')";
            $result = mysqli_query($conn,$sql);
            $appCount++;
            }
        }   
        
        }
        fclose($file_data);
        $response = array('status' => 'success', 'msg' => $appCount.' Applications uploaded.');
    }else{
        $response = array('status' => 'error', 'msg' => 'Please upload .csv file.');
    }
}else{
    $response = array('status' => 'error', 'msg' => 'Please Select File.');
}

echo json_encode($response);

?>