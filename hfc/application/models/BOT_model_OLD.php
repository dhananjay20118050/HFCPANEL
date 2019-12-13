<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverAlert;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverSelectInterface;


class BOT_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function test()
    {
        return 'Working';
    }

    public function start($processId, $userId, $txtAppFormNo){

        $log = "Log for : ".$txtAppFormNo;
        $this->loadData();

        if($txtAppFormNo != '0'){
            $sql = "SELECT * FROM hfccustdata where TRNREFNO IN (SELECT TRNREFNO FROM bot_aps_tracking WHERE status IN ('N','E','P') AND TRNREFNO = '$txtAppFormNo') ORDER BY id";
        }else{
            $sql = "SELECT * FROM hfccustdata where TRNREFNO IN (SELECT TRNREFNO FROM bot_aps_tracking WHERE status = 'N') ORDER BY id";
        }
        $rows = $this->db->query($sql)->result_array();

        if(count($rows) > 0){

            foreach ($rows as $row) {

                $localIP = getHostByName(getHostName());
                $sql = "UPDATE bot_aps_tracking SET status = 'P', ip_address = '".$localIP."', start_time = '".date("Y-m-d H:i:s")."', start_userId = '".$userId."' WHERE TRNREFNO = '".$row['TRNREFNO']."'";

                $qparent = $this->db->query($sql);

                $sql = "SELECT process_dtl_id FROM bot_process_dtl a, bot_process_mst b WHERE a.process_id = b.process_id AND a.process_id = $processId ORDER BY a.process_dtl_id";

                $processes = $this->db->query($sql)->result_array();

                $capabilities = DesiredCapabilities::internetExplorer();
                //$capabilities = DesiredCapabilities::chrome();
              $driver = RemoteWebDriver::create($this->host, $capabilities, 5000); 

                foreach ($processes as $process) {

                    $process_dtl_id = $process['process_dtl_id'];

                    $sql = "UPDATE bot_aps_tracking SET status = 'P' WHERE TRNREFNO = '".$row['TRNREFNO']."'";
                    $qparent = $this->db->query($sql);

                    $sql = "SELECT * FROM bot_sequence_dtl a WHERE a.process_dtl_id = $process_dtl_id AND isDel != 1 ORDER BY a.process_dtl_id, a.seq_no";
                    $result = $this->db->query($sql)->result();
                   //echo "<PRE>";
                  // print_r($result);exit();

                    foreach ($result as $key => $control) {

                        $log .= $control->seq_id.'->';

                        $selector = $this->getSelector($control);
                        $value = $this->getValue($control, $row);

                        if($value == "skip"){
                            continue;               
                        }

                        $resp = $this->startSequence($driver, $control, $selector, $value, $row['TRNREFNO'], $result, $userId);
                        if($resp == 0){
                            return 0;
                        }

                    }
                    
                    $sql = "UPDATE bot_aps_tracking SET status = 'Y', end_time = '".date("Y-m-d H:i:s")."', last_process_entry = '".$process_dtl_id."' WHERE TRNREFNO = '".$row['TRNREFNO']."'";
                    $qparent = $this->db->query($sql);
                    
                }

                $driver->quit();
                $this->closeIESessions();
                
            }
        }
        return $log;
    }

    public function closeIESessions(){
        $wmiLocator = new COM("WbemScripting.SWbemLocator");
        $objWMIService = $wmiLocator->ConnectServer('.', "root/cimv2", '', '');
        $objWMIService->Security_->ImpersonationLevel = 3;
        $oReg = $objWMIService->Get("StdRegProv");
        $compsys = $objWMIService->ExecQuery("Select * from Win32_process where name = 'iexplore.exe' or name = 'IEDriverServer.exe'");
        foreach ( $compsys as $compsys_val)
        {
            $compsys_val->Terminate();
        }
    }

    function getSelector($control){
        switch ($control->selector_type) {
            case 'id':
                return WebDriverBy::id($control->selector_id);

            case 'name':
                return WebDriverBy::name($control->selector_id);

            case 'css':
                return WebDriverBy::cssSelector($control->selector_id);

            case 'linktext':
                return WebDriverBy::linkText($control->selector_id);

            case 'xpath':
                //echo $control->selector_id;exit;
                return WebDriverBy::xpath($control->selector_id);

            case 'class':
                return WebDriverBy::className($control->selector_id);
            
            default:
                return "";
        }
    }

    function getValue($control, $row){
        /* Assign value to a control */
        if($control->selector_value != ""){
            $value = $control->selector_value;
        }
        elseif($control->model != ""){
            $value = trim($row[trim($control->model)]);
            if($value == "" || $value == null){
                $value = "skip";
            }
        }
        else{
            $value = "";
        }

        if($control->parent_model != ""){
            if($row[$control->parent_model] == ""){
                $value = "skip";
            }
        }
        //echo $value.';';
        return $value;
    }

    function startSequence($driver, $control, $selector, $value, $txtAppFormNo, &$result, $userId){

        $c = 0;
        while ($c < 2) {

            try {

                
                if($control->isSleep != 0){
                    sleep($control->isSleep);
                }
               // if($control->seq_id == 14){echo $control->control_id;exit;}
                if($control->seq_id == 3){
                    $control->control_id = 14;
                }
                switch ($control->control_id) {
                    case 1:
                        //input box

                        $element = $driver->findElement($selector);
                        $element->clear();
                        $element->sendKeys($value);
                        $element->sendKeys(array(WebDriverKeys::TAB));
                        break;

                    case 2:
                        //button click
                        $driver->findElement($selector)->click();
                        $driver->manage()->timeouts()->implicitlyWait = 5;
                        break;

                    case 3:
                        //select dropdown
                        $element = new WebDriverSelect($driver->findElement($selector));
                        $element->selectByValue($value);
                        $driver->manage()->timeouts()->implicitlyWait = 5;
                        break;

                    case 4:
                        //link URL
                      //  $value = $this->url_decode($value, $txtAppFormNo);
                        $driver->manage()->timeouts()->implicitlyWait = 10;
                        $driver->get($value);
                        break;

                    case 5:
                        //wait for url
                        $driver->wait()->until(WebDriverExpectedCondition::urlIs($value));
                        break;

                    case 6:
                        //wait for alert
                       // $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
                        
                       // echo "sdfsad";exit;
                       // $element = $driver->switchTo()->alert();
                        //$driver->sendKeys(Keys.RETURN);
                       // $driver->manage()->timeouts()->implicitlyWait = 5;
                        //$element->dismiss();
                        break;

                    case 7:
                        //wait for implicit time
                        $driver->manage()->timeouts()->implicitlyWait = $value;
                        break;

                    case 8:
                        //wait for visibility of element
                        $driver->wait(20,1000)->until(
                            WebDriverExpectedCondition::visibilityOfElementLocated($selector)
                        );
                        break;

                    case 9:
                        //switch to frame
                        $frame = $driver->findElement($selector);
                        $driver->switchTo()->frame($frame);
                        break;

                    case 10:
                        //switch to default content
                        $driver->switchTo()->defaultContent();
                        break;

                    case 11:
                        //switch to window
                        $handle = $driver->getWindowHandles();
                        $driver->switchTo()->window(end($handle));
                        break;

                    case 12:
                        //radio click
                        $css = "input[$control->selector_type='$control->selector_id'][value='$value']";
                        $driver->findElement(WebDriverBy::cssSelector($css))->click();
                        break;

                    case 13:
                        //call function
                        $this->callFunctions($driver, $txtAppFormNo, $control->selector_value);
                        break;
                    case 14:
                        //call function
                        //$element = $driver->findElement($selector);
                        $driver->findElement($selector)->click();
                        $driver->manage()->timeouts()->implicitlyWait = 5;
                        echo $xpath = "//html/frameset/frame[2]/html/body/applet[@name='ClientApp']";exit;
                        
                       // $frame = $driver->findElement($selector);
                            //element = driver.find_element :xpath, '//input[@name="q"]'
                       // $ele = 
                        

                        //WebDriverBy::xpath($control->selector_id);

                        $driver->findElement(WebDriverBy::xpath($xpath))->click();

                        //$driver->switchTo()->frame('appletFrame');
                        //$driver->sendKeys(Keys.RETURN);
                        //$driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
                        //$element = $driver->switchTo()->alert();
                        //$element->dismiss();
                        //$element->sendKeys($value)->;
                        
                        break;
                    
                    default:
                        # code...
                        break;
                }

                break;

            } catch (Exception $e) {
                if($c == 1){
                    echo '<pre>';
                    print_r($e);
                    $error = $e->getResults();
                    $exception_dtl = $error['value']['localizedMessage'];
                    // $this->takeScreenshot("_error_", $driver, $control, $txtAppFormNo);

                    if($control->process_dtl_id <= 13){
                        $this->addException(get_class($e), $txtAppFormNo, $exception_dtl, $control->seq_id, $control->process_dtl_id, $userId);
                        $driver->quit();
                        $this->closeIESessions();
                        return 0;
                    }else{
                        $sql = "UPDATE bot_aps_tracking SET status = 'Y', last_process_entry = '".$process_dtl_id."' WHERE TRNREFNO = '".$txtAppFormNo."'";    
                        $qparent = $this->db->query($sql);
                        return 1;
                    }
                    
                }else{
                    $driver->manage()->timeouts()->implicitlyWait = 10;
                }
            }
            $c++;
        }
        return 1;
    }

    function takeScreenshot($prefix, $driver, $control, $txtAppFormNo){
        $path = APS_SCREENSHOTS.$txtAppFormNo.SEPARATOR;
        if(!is_dir($path)){
            mkdir($path, 0777, true);
        }
        $img = $prefix.$control->process_dtl_id.'_'.$control->seq_id.'_'.date('Y_m_d_H_i_s').'.png';
        $driver->takeScreenshot($path.$img);
    }

    public function saveCustID($driver, $appFormNo){
        $selector = WebDriverBy::name("hidCustomerID");
        $hidCustomerID = $driver->findElement($selector)->getAttribute('value');
        $sql = "UPDATE hfcformdata SET hidCustomerID = $hidCustomerID where txtAppFormNo = '$appFormNo'";
        $qparent = $this->db->query($sql);
    }

    public function addException($exception_class, $appFormNo, $exception_dtl, $seq_id, $process_dtl_id, $userId){

        $exception_dtl = $this->db->escape_str($exception_dtl);
        $exception_class = $this->db->escape_str($exception_class);
        $excp = explode('Build info:', $exception_dtl);

        $sql = "INSERT INTO bot_error_logs (exception_class, txtAppFormNo, exception_dtl, seq_id, userId, process_dtl_id) VALUES ('".$exception_class."','".$appFormNo."','".$excp[0]."', '".$seq_id."', '".$userId."','".$process_dtl_id."')"; 
        $qparent = $this->db->query($sql);

        $sql = "UPDATE bot_aps_tracking SET status = 'E', end_time = '".date("Y-m-d H:i:s")."' ,last_process_entry = '".($process_dtl_id - 1)."' WHERE txtAppFormNo = '".$appFormNo."'";    
        $qparent = $this->db->query($sql);

        $sql = "SELECT COUNT(*) AS TOTAL FROM bot_error_logs WHERE txtAppFormNo = '$appFormNo'";
        $rows = $this->db->query($sql)->result_array();

        if($rows[0]['TOTAL'] < 3){
            if($process_dtl_id > 2 ){
                $this->resume(1, $userId, $appFormNo, $process_dtl_id);
            }
        }
    }

    public function loadData(){
        $sql = "SELECT txtAppFormNo FROM hfcformdata WHERE txtAppFormNo NOT IN (SELECT txtAppFormNo FROM bot_aps_tracking)";

        $rows = $this->db->query($sql)->result_array();

        foreach ($rows as $row) {
            $sql = "INSERT INTO `bot_aps_tracking` (`txtAppFormNo`,`status`,`last_process_entry`) VALUES ('".$row['txtAppFormNo']."', 'N', 0)";
            $qparent = $this->db->query($sql);
        }
    }

    public function url_decode($url, $txtAppFormNo){
        $arr = array(
                "#txtApplicationNo#" => $txtAppFormNo,
               // "#selBranch#" => $branchId,
                "#apsNo#" => $this->getApsNo($txtAppFormNo),
                "#hidCustomerID#" => $this->getHidCustomerID($txtAppFormNo)
            );

        $url = str_replace(array_keys($arr), array_values($arr), $url);
        return $url;
    }

    public function getUserName(){
        $localIP = getHostByName(getHostName());
        $sql = "SELECT username FROM bot_ip_logins WHERE ip_address = '$localIP'";
        $rows = $this->db->query($sql)->result_array();
        if(count($rows) > 0){
            return $rows[0]['username'];
        }else{
            return 'VARAPL5';
        }
    }

    public function getPassword(){
        $localIP = getHostByName(getHostName());
        $sql = "SELECT password FROM bot_ip_logins WHERE ip_address = '$localIP'";
        $rows = $this->db->query($sql)->result_array();
        if(count($rows) > 0){
            return $rows[0]['password'];
        }else{
            return 'VARA@123';
        }
    }

    public function callFunctions($driver, $txtAppFormNo, $funName){
        switch ($funName) {
            case 'downloadCibil':
                //$this->downloadCibil($driver, $txtAppFormNo);
                break;
            
            default:
                break;
        }
    }

    public function isAlertPresent($driver){
        try {
            $driver->switchTo()->alert()->accept();
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function waitForAjax($driver)
    {
        $code = "return document.readyState";
        do {
        //wait for it
        } while ($driver->executeScript($code) != 'complete');
    }

    
    public function login($user, $pass){
        $sql = "SELECT * FROM coreusers WHERE (empID = '".$user."' OR emailId = '".$user."') AND passWord = '".$pass."'";
        $rows = $this->db->query($sql)->result_array();
        if(count($rows) > 0){
            return $rows[0];
        }else{
            return "";
        }
    }

    // public function exportReport($report, $dt_start, $dt_end){
    //     $dt_start = urldecode($dt_start);
    //     $dt_end = urldecode($dt_end);
    //     $sql = "";
    //     switch ($report) {
    //         case 1:
    //             $sql = "SELECT DISTINCT
    //                         t3.applicationNo,
    //                         t1.creationDate AS 'Inward Time',
    //                         t5.startDate AS 'Dataentry Start Time',
    //                         t5.endDate AS 'Dataentry End Time',
    //                         TIMEDIFF(t5.endDate, t5.startDate) AS 'Dataentry Time',
    //                         t6.start_time AS 'Automation Start Time',
    //                         t6.end_time AS 'Automation End Time',
    //                         TIMEDIFF(t6.end_time, t6.start_time) AS 'Automation Time'
    //                     FROM
    //                         pldataentry t3
    //                             LEFT JOIN
    //                         alpluserentry t5 ON t3.entryId = t5.entryId
    //                             LEFT JOIN
    //                         alplallocationentry t2 ON t5.allocationId = t2.allocationId
    //                             LEFT JOIN
    //                         alplapplications t1 ON t1.applicationId = t2.applicationId
    //                             LEFT JOIN
    //                         alplcustomremark t4 ON t1.applicationNo = t4.appNo
    //                             LEFT JOIN
    //                         bot_aps_tracking t6 ON t1.applicationNo = t6.txtAppFormNo
    //                     WHERE
    //                         t3.RejectionCatId = 0
    //                             AND t6.status IN ('Y')
    //                             AND t1.creationDate >= '".$dt_start."'
    //                             AND t1.creationDate <= '".$dt_end."'
    //                     ORDER BY t3.DataEntryId DESC";
    //             break;
    //         case 2:

    //             break;
            
    //         default:
    //             break;
    //     }
    //     $rows = $this->db->query($sql)->result_array();
    //     return $rows;
    // }


    public function uploadCSV($files){ 
        if(!empty($files["input-file"]["name"])){
            $output = '';
            $ext = explode(".", $files["input-file"]["name"]);
            if($ext[1] == 'csv'){
                $file_data = fopen($_FILES["input-file"]["tmp_name"], 'r');
                fgetcsv($file_data);
                $appNos = array();
                while($row = fgetcsv($file_data)){  
                    $appNos[] = $row[0];
                }
                fclose($file_data);
            }else{
                return 2;
            }
        }else{
            return 3;
        }
        return 4;  
    }



}
?>