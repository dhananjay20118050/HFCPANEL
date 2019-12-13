<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Traits\GridTrait;
use DataTables;
use Illuminate\Support\Facades\Input;
use App\Process;
use Redirect,Response;

class HFCController extends Controller
{
    protected $db;
    public function __construct()
    {
        $this->db = Process::setDB(3);
    }
    use GridTrait;
    public function showTab(Request $request){      
        if ($request->ajax()) {

            $data = $this->db->table('bot_aps_tracking As a')
            ->leftJoin('hfccustdata as b', 'b.TRNREFNO', '=', 'a.TRNREFNO')
            ->leftjoin( 'coreusers as c', 'a.upload_user', '=', 'c.userid' )
            ->select('a.ip_address as ipaddress','c.fullName as fullName','b.APPLNO as APPLNO','a.TRNREFNO as TRNREFNO','a.upload_user as upload_user','a.upload_datetime as upload_datetime','c.userid as userid' )
            ->whereIn('a.status',['N','E','P'])
            ->get();
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '<div class="dt-actions"><a href="#" class="btn btn-icon btn-primary btn-sm"><i class="far fa-edit"></i></a><a href="#" class="btn btn-icon btn-danger btn-sm"><i class="fa fa-trash"></i></a></div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true); 
    }

    return view('admin.process.hfc.index');
}

public function getWorkingOursByDate($date = ""){

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

public function showTab1(Request $request){
    
     if ($request->ajax()) {
        /*if(isset($_POST['date'])){

            if($_POST['date'] != ""){$date = $this->getWorkingOursByDate($_POST['date']);}
            else{$date = $this->getWorkingOursByDate(date("Y-m-d"));
            }
          }

         else {$date = $this->getWorkingOursByDate(date("Y-m-d"));}*/

            $data = $this->db->table('bot_aps_tracking As a')
            ->leftJoin('hfccustdata as b', 'b.TRNREFNO', '=', 'a.TRNREFNO')
            ->leftjoin( 'coreusers as c', 'a.upload_user', '=', 'c.userid' )
            ->leftJoin( 'coreusers as d', 'd.userId', '=', 'a.upload_user' )
            ->leftJoin( 'bot_ip_logins as e', 'e.id', '=', 'a.userid' )
            ->select('b.APPLNO as appno','a.TRNREFNO as TRNREFNO','b.is_existing_cust_1 as is_existing_cust_1', 'b.cifid_1 as cifid_1','b.is_existing_cust_2 as is_existing_cust_2', 'b.cifid_2 as cifid_2','b.is_existing_cust_3 as is_existing_cust_3', 'b.cifid_3 as cifid_3','b.AccountNo as accountno','a.is_processed as processed','a.start_time as start_time', 'a.end_time as end_time','e.username as finnacleuser', 'a.upload_datetime as upload_datetime','d.fullName as upload_fullName')
           ->whereIn('a.status',['Y'])
          // ->where( 'a.end_time', ">",$date['start'])
           //->where( 'a.end_time', "<", $date['end'])
            ->get();
            return Datatables::of($data)
            ->addIndexColumn()
            
            ->rawColumns(['action'])
            ->make(true); 
    }

    
}


public function showTab2(Request $request){

        if ($request->ajax()) {
            $data = $this->db->table('bot_error_logs AS a')
            ->leftJoin('bot_aps_tracking AS c', 'c.TRNREFNO', '=', 'a.TRNREFNO')
            ->leftjoin( 'coreusers AS e', 'e.userId', '=', 'a.userId')
            ->select('a.TRNREFNO as TRNREFNO','a.exception_dtl as exception_dtl', 'a.datetime as datetime', 'a.error_section as error_section', 'e.fullName as fullName')
            ->get();
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '<div class="dt-actions"><a href="#" class="btn btn-icon btn-primary btn-sm"><i class="far fa-edit"></i></a><a href="#" class="btn btn-icon btn-danger btn-sm"><i class="fa fa-trash"></i></a></div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true); 
    }

    
}

public function uploaddata(Request $request)
{
$files = $_FILES;
$response = array('status' => 0, 'msg' => 'Please Select File.');

if(!empty($files["csv_file"]["name"])){

    $output = '';
    $ext = explode(".", $files["csv_file"]["name"]);

    if($ext[1] == 'csv'){
        $filename = $ext[0];
        $filename = substr($filename, 3,8);
        $pattern = '/(\d{2}+)(\d{2}+)(\d{4}+)/i';
        $replacement = '$1-$2-$3';
        $newfile = preg_replace($pattern, $replacement, $filename);
        
        $file_data = fopen($_FILES["csv_file"]["tmp_name"], 'r');
        $appCount = 0;
        while($row = fgetcsv($file_data)){
          if($row[0] == "TRNREFNO"){continue;}
            
            if(!empty($row[0]) && !empty($row[1]) && !empty($row[3]) && !empty($row[4]) && !empty($row[5]) && !empty($row[6]) && !empty($row[9]) && !empty($row[10]) && !empty($row[11]) && !empty($row[12]) && !empty($row[13]) && !empty($row[14]) && !empty($row[17]) && !empty($row[18]) && !empty($row[19]) && !empty($row[20]) && !empty($row[21]) && !empty($row[22]) && !empty($row[23]) && !empty($row[25]) && !empty($row[26]) && !empty($row[27]) && !empty($row[28]) && !empty($row[31]) && !empty($row[39]) && !empty($row[47]) && !empty($row[48]) && !empty($row[49]) && !empty($row[50]) && !empty($row[51]) && !empty($newfile))
            { 
            if(is_numeric($row[0])){
               $a = $this->validate_data($row[0]);
            }

           
            $b = $this->validate_data($row[1]);

            if(is_numeric($row[3]))  {
               $d = $this->validate_data($row[3]);
            }
            if(is_numeric($row[4]))  {
               $e = $this->validate_data($row[4]);
            }
            $f =$row[5];

            $g = $this->validate_data($row[6]);

            $h =$this->validate_data($row[7]);
            $i =$this->validate_data($row[8]);

            $j =$this->validate_data($row[9]);
            $k =$this->validate_data($row[10]);
            if(is_numeric($row[11]))  {
               $l = $this->validate_data($row[11]);
            }
            $m =$this->validate_data($row[12]);
            $n =$this->validate_data($row[13]);
            $o =$this->validate_data($row[14]);
            $r =$this->validate_data($row[17]);
            $s =$this->validate_data($row[18]);
            $t =$this->validate_data($row[19]);

           if(is_numeric($row[20]))  {
               $u = $this->validate_data($row[20]);
            }

            $v =$this->validate_data($row[21]);

                      
             $w =$this->validate_data($row[22]);
         
            
            $x =$this->validate_data($row[23]);

            $dobexp = explode('/', $x);
            $day = $this->validate_data($dobexp[1]);
            $month = $this->validate_data($dobexp[0]);
            $year = $this->validate_data($dobexp[2]);

            if(strlen($day) == 1){
                $day = '0'.$day;
            }
            if(strlen($month) == 1){
                $month = '0'.$month;
            }


            $dob = $day.'/'.$month.'/'.$year;


            $z =$this->validate_data($row[25]);
            $a1 =$this->validate_data($row[26]);
            $b1 =$this->validate_data($row[27]);
           if(is_numeric($row[28]))  {
               $c1 = $this->validate_data($row[28]);
            }
            $f1 =$this->validate_data($row[31]);
            $n1 =$this->validate_data($row[39]);
            $v1 =$this->validate_data($row[47]);
            $w1 =$this->validate_data($row[48]);

            if (filter_var($row[49], FILTER_VALIDATE_EMAIL)) {
                $x1 =$this->validate_data($row[49]);
            }
            
            $mob =$this->validate_data($row[50]);

            if (strlen($mob)==10 && is_numeric($mob)){
                $y1 = $mob;
            }
          
            $z1 =$this->validate_data($row[51]);

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
            try{
            $result = $this->db->table('hfccustdata')->insert(
                ['TRNREFNO' => $a, 
                'BRCODE' => $b,
                'SBCODE'=> $c,
                'BNKSRL'=> $d,
                'APPLNO'=> $e,
                'NAME'=> $f,
                'ADD1'=> $g,
                'ADD2'=> $h,
                'ADD3'=> $i,
                'CITY'=> $j,
                'STATE'=> $k,
                'PIN'=> $l,
                'TYPE'=> $m,
                'TENURE'=> $n,
                'CATE'=> $o,
                'FOLIO'=> $p,
                'EMPCODE'=> $q,
                'STATUS'=> $r,
                'AMOUNT'=> $s,
                'PAYMODE'=> $t,
                'INSTNO'=> $u,
                'INSTDT'=> $v,
                'PANGIR1'=> $w,
                'DOB'=> $dob,
                'NGNAME'=> $y,
                'BANKAC'=> $z,
                'BANKNM'=> $a1,
                'BCITY'=> $b1,
                'MICR'=> $c1,
                'GNAME'=> $d1,
                'GPAN'=> $e1,
                'ACTYPE'=> $f1,
                'RTGSCOD'=> $g1,
                'NNAME'=> $h1,
                'NADD1'=> $i1,
                'NADD2'=> $j1,
                'NADD3'=> $k1,
                'NCITY'=> $l1,
                'NPIN'=> $m1,
                'ENCL'=> $n1,
                'TELNO'=> $o1,
                'JH1NAME'=> $p1,
                'JH2NAME'=> $q1,
                'JH1PAN'=> $r1,
                'JH2PAN'=> $s1,
                'JH1RELATION'=> $t1,
                'JH2RELATION'=> $u1,
                'HLDINGPATT'=> $v1,
                'SUBTYPE'=> $w1,
                'EMAILID'=> $x1,
                'MOBILENO'=> $y1,
                 'IFSC'=> $z1,
                 'filename'=> $newfile

            ]
            );
                if($result){

                    $ipadd=getHostByName(getHostName());;
                    $result = $this->db->table('bot_aps_tracking')->insert([
                    'TRNREFNO'=>$a,'status'=>'N','last_process_entry'=>'0','ip_address'=>$ipadd,'upload_user'=>'1','upload_datetime'=>date('Y-m-d H:i:s')]);
                        $appCount++;
                }
                
            $dd = false;
            }catch (QueryException $e) {
                $sqlState = $e->errorInfo[0];
                $errorCode  = $e->errorInfo[1];
                 if ($sqlState === "23000" && $errorCode === 1062) {
                    $dd = true;
                 }
                    
            }
            
        }   
        
        }
        fclose($file_data);
        if($dd){
            $response = array('status' => 'error', 'msg' =>'Duplicate Entry');
        }else{
            $response = array('status' => 'success', 'msg' => $appCount.' Applications uploaded.');
        }
    }else{
        $response = array('status' => 'error', 'msg' => 'Please upload .csv file.');
    }
}else{
    $response = array('status' => 'error', 'msg' => 'Please Select File.');
}

  return Response::json($response);

}

public function validate_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

}