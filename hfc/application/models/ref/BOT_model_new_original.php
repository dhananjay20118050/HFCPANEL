<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Chrome\ChromeDriver;

class BOT_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();      
    }

    public function test()
    {
        return 'Working';
    }

    public function start($processId, $txtAppFormNo){

        $log = "Log for : ".$txtAppFormNo;
        $this->loadData();

        if($txtAppFormNo != '0'){
            $sql = "SELECT * FROM hfccustdata where txtAppFormNo IN (SELECT txtAppFormNo FROM bot_aps_tracking WHERE status IN ('N','E','P') AND txtAppFormNo = '$txtAppFormNo') ORDER BY id";
        }else{
            $sql = "SELECT * FROM hfccustdata where txtAppFormNo IN (SELECT txtAppFormNo FROM bot_aps_tracking WHERE status = 'N') ORDER BY id";
        }
        $rows = $this->db->query($sql)->result_array();

        if(count($rows) > 0){

            foreach ($rows as $row) {

                $localIP = getHostByName(getHostName());
                $sql = "UPDATE bot_aps_tracking SET status = 'P', ip_address = '".$localIP."', start_time = '".date("Y-m-d H:i:s")."' WHERE txtAppFormNo = '".$row['txtAppFormNo']."'";

                $qparent = $this->db->query($sql);

                $sql = "SELECT process_dtl_id FROM bot_process_dtl a, bot_process_mst b WHERE a.process_id = b.process_id AND a.process_id = $processId ORDER BY a.process_dtl_id";

                $processes = $this->db->query($sql)->result_array();

                $capabilities = DesiredCapabilities::internetExplorer();
                // $capabilities = DesiredCapabilities::chrome();
                $driver = RemoteWebDriver::create($this->host, $capabilities, 5000);

                foreach ($processes as $process) {

                    $process_dtl_id = $process['process_dtl_id'];

                    $sql = "SELECT * FROM bot_sequence_dtl a WHERE a.process_dtl_id = $process_dtl_id AND isDel != 1 ORDER BY a.process_dtl_id, a.seq_no";

                    $result = $this->db->query($sql)->result();

                    foreach ($result as $key => $control) {

                        $log .= $control->seq_id.'->';

                        $selector = $this->getSelector($control);
                        $value = $this->getValue($control, $row);
                        if($value == "skip"){
                            continue;               
                        }

                        $resp = $this->startSequence($driver, $control, $selector, $value, $row['txtAppFormNo'], $row['selBranch'], $result);
                        if($resp == 0){
                            return 0;
                        }

                        if($control->seq_id == 19){

                            $apsNo = $this->getApsNo($txtAppFormNo);
                            if($apsNo == 0 || $apsNo == "0"){
                                $driver->get("https://aps.icicibank.com/ICICIWeb/Activity.los?activity=BDE&currentActivity=BDE&category=PERSONAL&hidProcessFlag=N&applicationStatus=N&hidCustomerID=N&selBranch=".$row['selBranch']);
                            }else{
                                $resp = $this->startExcistingApsProcess($driver, $apsNo, $row['txtAppFormNo'], $row['selBranch']);

                                /*  0 - Create New APS with adding 5100 to 5800
                                    1 - APS Edit Link of BDE   */

                                if($resp == 0){
                                    $newAppFormNo = $this->updateApplicationNo($row['txtAppFormNo'], $apsNo);
                                    $row['txtAppFormNo'] = $newAppFormNo;
                                    $driver->get("https://aps.icicibank.com/ICICIWeb/Activity.los?activity=BDE&currentActivity=BDE&category=PERSONAL&hidProcessFlag=N&applicationStatus=N&hidCustomerID=N&selBranch=".$row['selBranch']);
                                }else{
                                    $driver->get("https://aps.icicibank.com/ICICIWeb/Activity.los?activity=BDE&currentActivity=BDE&txtApplicationNo=".$row['apsNo']."&category=PERSONAL&mode=E&inBranchID=".$row['selBranch']);
                                }
                            }

                        }
                    }
                }

                $driver->quit();
                $sql = "UPDATE bot_aps_tracking SET status = 'Y', end_time = '".date("Y-m-d H:i:s")."' WHERE txtAppFormNo = '".$row['txtAppFormNo']."'";    
                $qparent = $this->db->query($sql);
            }
        }
        return $log;
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

        if($control->selector_id == "gstCustomer"){
            $value = trim($this->getHidCustomerID($row["txtAppFormNo"]));
        }

        if($control->parent_model != ""){
            if($row[$control->parent_model] == ""){
                $value = "skip";
            }
        }
        //echo $value.';';
        return $value;
    }

    function startSequence($driver, $control, $selector, $value, $txtAppFormNo, $branchId, &$result){

        $c = 0;
        while ($c < 2) {

            try {

                if($control->isSleep != 0){
                    sleep($control->isSleep);
                }
                // if($control->seq_id == 76){
                //  exit();
                // }
                // if($control->seq_id == 428){
                //  exit();
                // }

                switch ($control->control_id) {
                    case 1:
                        //input box

                        $element = $driver->findElement($selector);
                        $element->clear();
                        $original_value = $value;
                        if($control->isPopup == 1){
                            if (strpos($value, "(") !== false) {
                                $tmp_value = explode("(", $value);
                                $value = $tmp_value[0];
                            }
                        }
                        $element->sendKeys($value);
                        $element->sendKeys(array(WebDriverKeys::TAB));

                        if($control->isPopup != 1){

                            if($control->selector_id == "txtAppFormRecvdDate"){
                                $element->sendKeys($value);
                            }
                            if($control->selector_id == "txtApprovedAmount"){
                                $e = $driver->findElement(WebDriverBy::name("txtRequestedTerm"));
                                $e->clear();
                            }

                        }else{
                             //added bu sumit for presanction count
                            // if($control->selector_id == 'selDocID'){
                            //    $model=$control->model;
                            //    $this->countPresanction($driver, $value,$model);
                            // }
                            if($control->selector_id == "txtSelEmployerID" && $value == "OTHERS"){
								//sleep(5);
                                $element->sendKeys(array(WebDriverKeys::TAB));
								//sleep(5);
                            }
                            $i = 0;
                            do{
                                $this->checkIfAlertPresent($driver, $original_value);
                                $ver_value = $element->getAttribute('value');
                                if($ver_value == ""){
                                    $driver->executeScript("popupHandleDis.close();");
                                    $element->sendKeys($value);
                                    $element->sendKeys(array(WebDriverKeys::TAB));
                                }
                                $i++;
                            }while($ver_value == "" && $i < 5);
                        }
                        break;

                    case 2:
                        //button click
                        // $this->takeScreenshot("_click_before_", $driver, $control, $txtAppFormNo);

                        // if($control->selector_desc == "Applicant Save Button"){
                        //     $selector = WebDriverBy::name("btnUpdate");
                        //     $driver->findElement($selector)->click();
                        //     $driver->manage()->timeouts()->implicitlyWait = 5;
                        // }
                   
                        if($control->selector_id == "Applicant"){
                            $this->saveApsNo($driver, $txtAppFormNo);
                            //$this->checkForExistingCustId($driver, $txtAppFormNo);
                        }
                        $driver->findElement($selector)->click();
                        $driver->manage()->timeouts()->implicitlyWait = 5;

                        if($control->selector_id == "btnOtherCharges"){
                            $this->getOtherChargesWindow($driver, $selector);
                        }
                        // $this->takeScreenshot("_click_after_", $driver, $control, $txtAppFormNo);
                        break;

                    case 3:
                        //select dropdown
                        //if($control->selector_id == "selEmploymentType"){
                           // $e = $driver->findElement(WebDriverBy::name("txtSelEmployerID"));
                           // $e->clear();
                        //}
                        $element = new WebDriverSelect($driver->findElement($selector));
                        $element->selectByValue($value);
                        $driver->manage()->timeouts()->implicitlyWait = 5;
                        break;

                    case 4:
                        //link URL
                        if($control->selector_desc == "Other Details Link"){
                            $this->saveCustID($driver, $txtAppFormNo);
                        }
                        $value = $this->url_decode($value, $txtAppFormNo, $branchId);
                        $driver->manage()->timeouts()->implicitlyWait = 10;
                        $driver->get($value);
                        break;

                    case 5:
                        //wait for url
                        $driver->wait()->until(WebDriverExpectedCondition::urlIs($value));
                        break;

                    case 6:
                        //wait for alert
                        $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
                        $driver->switchTo()->alert()->accept();
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
                    
                    default:
                        # code...
                        break;
                }

                //validate data
                // if(in_array($control->control_id, [1,3]) && $control->model != "" && !in_array($control->selector_id, ["selBranchLevel", "selBranch"])){
                //  $this->saveForVarification($driver, $element, $control, $txtAppFormNo, $value);
                // }

                break;

            } catch (Exception $e) {
                if($c == 1){
                    echo '<pre>';
                    print_r($e);
                    $error = $e->getResults();
                    $exception_dtl = $error['value']['localizedMessage'];
                    // $this->takeScreenshot("_error_", $driver, $control, $txtAppFormNo);
                    if($control->process_dtl_id < 13){
                        $this->addException(get_class($e), $txtAppFormNo, $exception_dtl, $control->seq_id, $control->process_dtl_id);
                        $driver->quit();
                        return 0;
                    }else{
                        return 1;
                    }
                    
                }else{
                    $driver->manage()->timeouts()->implicitlyWait = 10;
                    if($control->selector_id == "selDueDate"){
                        $value = "05";
                    }
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

    // public function saveForVarification($driver, $element, $control, $txtAppFormNo, $value){

    //  $ver_value = "";
    //  switch ($control->control_id) {
    //      case 1:
    //          //input box
    //          $ver_value = $element->getAttribute('value');
    //          if($ver_value != $value){
    //              $element->clear();
    //              $element->sendKeys($value);
    //              $element->sendKeys(array(WebDriverKeys::TAB));
    //              $this->checkIfAlertPresent($driver, $value);
    //          }
    //          break;

    //      case 3:
    //          //select dropdown
    //          //$option = $element->getFirstSelectedOption();
    //          //$ver_value = $option->getAttribute('value');
    //          break;

    //      case 12:
    //          //radio click
    //          //$flag = $element->isSelected();
    //          break;
            
    //      default:
    //          # code...
    //          break;
    //  }

    //  if($ver_value != ""){
    //      $sql = "UPDATE hfccustdata_ver SET ".$control->model." = '$ver_value' where txtAppFormNo = '$txtAppFormNo'";
    //      $qparent = $this->db->query($sql);
    //  }
    // }

    public function countPresanction($driver, $value,$model){
        try{
            $arr=array($value);
            for($j=0;$j<count($arr);$j++){
                // echo $arr[$j];
            }
                    $selector = WebDriverBy::xpath("/html/body/form/table[9]/tbody/tr");
                    $rows = $driver->findElements($selector);
                    $foundAt=0;
                    for ($i=3; $i <= count($rows); $i++) {
                        $selector = WebDriverBy::xpath("/html/body/form/table[9]/tbody/tr[$i]/td[2]");
                        $e = $driver->findElement($selector);
                        // foreach ($elements as $key => $e) {
                            $text = $e->getText();
                            $getVal=array($text);
                            //echo $text;
                            //echo "~";
                    //         if($text=='PSL DOC ' || $text=='PSL DOC '){
                    //             $foundAt=$i;
                    //             $selector = WebDriverBy::xpath("/html/body/form/table[9]/tbody/tr[$i]/td[1]");
                    //             $elements = $driver->findElements($selector);
                    //             $s = WebDriverBy::xpath("/html/body/form/table[3]/tbody/tr[".$foundAt."]/td/input[@name='selDocIDList']");
                    //             $e = $driver->findElement($s);
                    //             $e->click();
                    // }   
                // }
            }
        
         // $c='x';
         //            for($x=0;$x<=count($getVal);$x++){
         //                echo $c;
         //                echo $getVal[$x];
         //                echo ' ';
                        
         //            }
                    $expected=array_map('trim',$arr);   //to be added
                    $actual=array_map('trim',$getVal);  //actually added
                    if(count($arr)!=count($rows)-3){
                        $missingDoc=array_diff($actual,$expected);  //missed out doc
                        // echo $missingDoc;
                        // for($missingDoc as $key => $v){

                        // }
                    }
                   // if(selDocIDList)
                    // $selector = WebDriverBy::name("B1");
                    // $element = $driver->findElement($selector)->click();
                    // $driver->switchTo()->window($handle);
                    // break;
                }
                catch (Exception $e){
                    // if($c == 1){
                    //     $driver->switchTo()->window($handle);
                    // }else{
                    //     $driver->manage()->timeouts()->implicitlyWait = 5;
                    // }
                }
    }

    public function checkIfAlertPresent($driver, $value) 
    {
        $handle = $driver->getWindowHandle();
        $handles = $driver->getWindowHandles();
        $lastHandle = end($handles);

        if($handle != $lastHandle){
            $c = 0;
            while ($c < 2) {
                try{
                    $driver->switchTo()->window($lastHandle);
                    $selector = WebDriverBy::xpath("/html/body/form/table[3]/tbody/tr");
                    $rows = $driver->findElements($selector);
                    $foundAt = 0;

                    for ($i=1; $i <= count($rows); $i++) { 
                        $selector = WebDriverBy::xpath("/html/body/form/table[3]/tbody/tr[$i]/td");
                        $elements = $driver->findElements($selector);
                        foreach ($elements as $key => $e) {
                            $text = $e->getText();
                            if($text == " ".$value){
                                $foundAt = $i;
                                if($value != "RESIDENCE PROOF"){
                                    break 2;
                                }
                            }
                            if($text == $value){
                                $foundAt = $i;
                                if($value != "RESIDENCE PROOF"){
                                    break 2;
                                }
                            }
                        }
                    }

                    if($foundAt != 0){
                        $s = WebDriverBy::xpath("/html/body/form/table[3]/tbody/tr[".$foundAt."]/td/input[@name='chkRecords']");
                        $e = $driver->findElement($s);
                        $e->click();
                    }   

                    $selector = WebDriverBy::name("B1");
                    $element = $driver->findElement($selector)->click();
                    $driver->switchTo()->window($handle);
                    break;
                }
                catch (Exception $e){
                    if($c == 1){
                        $driver->switchTo()->window($handle);
                    }else{
                        $driver->manage()->timeouts()->implicitlyWait = 5;
                    }
                }
                $c++;
            }
        }
    }

    public function saveApsNo($driver, $appFormNo){
        $selector = WebDriverBy::name("txtappidc");
        $apsNo = $driver->findElement($selector)->getAttribute('value');
        $sql = "UPDATE hfccustdata SET apsNo = $apsNo where txtAppFormNo = '$appFormNo'";
        $qparent = $this->db->query($sql);
        $sql = "UPDATE pldataentry SET apsNo = '$apsNo' where applicationNo = '$appFormNo'";
        $qparent = $this->db->query($sql);
    }

    public function saveCustID($driver, $appFormNo){
        $selector = WebDriverBy::name("hidCustomerID");
        $hidCustomerID = $driver->findElement($selector)->getAttribute('value');
        $sql = "UPDATE hfccustdata SET hidCustomerID = $hidCustomerID where txtAppFormNo = '$appFormNo'";
        $qparent = $this->db->query($sql);
    }

    public function addException($exception_class, $appFormNo, $exception_dtl, $seq_id, $process_dtl_id){

        $exception_dtl = $this->db->escape_str($exception_dtl);
        $exception_class = $this->db->escape_str($exception_class);
        $excp = explode('Build info:', $exception_dtl);

        $sql = "INSERT INTO bot_error_logs (exception_class, txtAppFormNo, exception_dtl, seq_id) VALUES ('".$exception_class."','".$appFormNo."','".$excp[0]."', '".$seq_id."')";  
        $qparent = $this->db->query($sql);

        $sql = "UPDATE bot_aps_tracking SET status = 'E', end_time = '".date("Y-m-d H:i:s")."' ,last_process_entry = '".($process_dtl_id - 1)."' WHERE txtAppFormNo = '".$appFormNo."'";    
        $qparent = $this->db->query($sql);

        $sql = "SELECT COUNT(*) AS TOTAL FROM bot_error_logs WHERE txtAppFormNo = '$appFormNo'";
        $rows = $this->db->query($sql)->result_array();

        if($rows[0]['TOTAL'] < 3){
            if($process_dtl_id > 2 ){
                $this->resume($appFormNo, 1, $process_dtl_id);
            }else{
                $this->start(1);
            }
        }
    }

    public function loadData(){
        $sql = "SELECT txtAppFormNo FROM hfccustdata WHERE txtAppFormNo NOT IN (SELECT txtAppFormNo FROM bot_aps_tracking)";

        $rows = $this->db->query($sql)->result_array();

        foreach ($rows as $row) {
            $sql = "INSERT INTO `bot_aps_tracking` (`txtAppFormNo`,`status`,`last_process_entry`) VALUES ('".$row['txtAppFormNo']."', 'N', 0)";
            $qparent = $this->db->query($sql);

            $sql = "INSERT INTO `hfccustdata_ver` (`txtAppFormNo`) VALUES ('".$row['txtAppFormNo']."')";
            $qparent = $this->db->query($sql);
        }
    }

    public function resume($appNo, $processId, $process_dtl_id, $end_process_dtl_id){

        $log = "";

        $sql = "SELECT * FROM hfccustdata WHERE txtAppFormNo = '$appNo' LIMIT 1";
        $rows = $this->db->query($sql)->result_array();

        if(count($rows) > 0){

            foreach ($rows as $row) {

                $localIP = getHostByName(getHostName());
                $sql = "UPDATE bot_aps_tracking SET status = 'P', ip_address = '".$localIP."' WHERE txtAppFormNo = '".$row['txtAppFormNo']."'";

                $qparent = $this->db->query($sql);

                if($end_process_dtl_id == 0){
                    $sql = "SELECT process_dtl_id FROM bot_process_dtl a, bot_process_mst b WHERE a.process_id = b.process_id AND a.process_id = $processId AND (a.process_dtl_id >= $process_dtl_id OR a.process_dtl_id = 1) ORDER BY a.process_dtl_id";
                }else{
                    $sql = "SELECT process_dtl_id FROM bot_process_dtl a, bot_process_mst b WHERE a.process_id = b.process_id AND a.process_id = $processId AND (a.process_dtl_id >= $process_dtl_id OR a.process_dtl_id = 1) AND a.process_dtl_id <= $end_process_dtl_id ORDER BY a.process_dtl_id";
                }

                $processes = $this->db->query($sql)->result_array();
                $capabilities = DesiredCapabilities::internetExplorer();
                // $capabilities = DesiredCapabilities::chrome();
                $driver = RemoteWebDriver::create($this->host, $capabilities, 5000);

                $apsURL = "https://aps.icicibank.com/ICICIWeb/Activity.los?activity=BDE&currentActivity=BDE&txtApplicationNo=".$row['apsNo']."&category=PERSONAL&mode=E&inBranchID=".$row['selBranch'];

                if(in_array($process_dtl_id, [3,4,5,6,7,8]) && $row['hidCustomerID'] != ""){
                    $appURL = "https://aps.icicibank.com/ICICIWeb/PersonalInfo.los?displayFlag=P&showLowerTab=T&pageName=PersonalInfo.los&tabKey=APPLICANT&currentActivity=BDE&activity=BDE&hidCustomerID=".$row['hidCustomerID']."&ComingFrom=APPLIST&hidGCDCustomerID=";
                }elseif($process_dtl_id == 11){
                    $appURL = "https://aps.icicibank.com/ICICIWeb/FinancialCombo.los?activity=BDE&currentActivity=BDE&txtApplicationNo=".$row['apsNo']."&category=FINANCIALCOMBO&mode=E&inBranchID=".$row['selBranch'];
                }else{
                    $appURL = "";
                }

                foreach ($processes as $process) {

                    $process_dtl_id = $process['process_dtl_id'];
                    $sql = "SELECT * FROM bot_sequence_dtl a WHERE a.process_dtl_id = $process_dtl_id AND isDel != 1 ORDER BY a.process_dtl_id, a.seq_no";
                    $result = $this->db->query($sql)->result();

                    foreach ($result as $key => $control) {

                        $log .= $control->seq_id.'->';

                        if($control->seq_id == 19){
                            $driver->get($apsURL);
                            if($appURL != ""){
                                $selector = WebDriverBy::linkText("Applicant");
                                $driver->findElement($selector)->click();
                                $driver->get($appURL);
                            }
                            continue;
                        }elseif($control->seq_id == 77){
                            if($appURL != "")
                                continue;
                        }
                        
                        $selector = $this->getSelector($control);
                        $value = $this->getValue($control, $row);

                        if($value == "skip"){
                            continue;
                        }

                        $res = $this->startSequence($driver, $control, $selector, $value, $row['txtAppFormNo'], $row['selBranch'],$result);
                        if($res == 0){
                            return 0;
                        }

                    }
                }

                $driver->quit();
                $sql = "UPDATE bot_aps_tracking SET status = 'Y', end_time = '".date("Y-m-d H:i:s")."' WHERE txtAppFormNo = '".$row['txtAppFormNo']."'";    
                $qparent = $this->db->query($sql);
            }
        }
        return 1;
    }

    public function url_decode($url, $txtAppFormNo, $branchId){
        $arr = array(
                "#txtApplicationNo#" => $txtAppFormNo,
                "#selBranch#" => $branchId,
                "#apsNo#" => $this->getApsNo($txtAppFormNo),
                "#hidCustomerID#" => $this->getHidCustomerID($txtAppFormNo)
            );

        $url = str_replace(array_keys($arr), array_values($arr), $url);
        return $url;
    }

    public function getHidCustomerID($txtAppFormNo){
        $sql = "SELECT hidCustomerID FROM hfccustdata WHERE txtAppFormNo = '$txtAppFormNo'";
        $rows = $this->db->query($sql)->result_array();
        return $rows[0]['hidCustomerID'];
    }

    public function getApsNo($txtAppFormNo){
        $sql = "SELECT apsNo FROM hfccustdata WHERE txtAppFormNo = '$txtAppFormNo'";
        $rows = $this->db->query($sql)->result_array();
        return $rows[0]['apsNo'];
    }

    public function getDob($txtAppFormNo){
        $sql = "SELECT txtDob FROM hfccustdata WHERE txtAppFormNo = '$txtAppFormNo'";
        $rows = $this->db->query($sql)->result_array();
        return $rows[0]['txtDob'];
    }

    public function getOtherChargesWindow($driver, $selector){
        $count = 0;
        do{
            sleep(1);
            $total = count($driver->getWindowHandles());
            $count++;
            if($total < 2){
                $driver->executeScript("popupHandleDis.close();");
                $driver->findElement($selector)->click();
            }
        }while($total < 2 && $count < 10);
    }

    public function callFunctions($driver, $txtAppFormNo, $funName){
        switch ($funName) {
            case 'downloadCibil':
                $this->downloadCibil($driver, $txtAppFormNo);
                break;
            
            default:
                break;
        }
    }

    public function downloadCibil($driver, $txtAppFormNo){

        $selector = WebDriverBy::xpath("/html/body/table[4]/tbody/tr[2]/td[1]/a");
        $element = $driver->findElement($selector);
        $serialNo = $element->getText();

        $sql = "UPDATE hfccustdata SET cibilRefNo = '$serialNo' where txtAppFormNo = '$txtAppFormNo'";
        $qparent = $this->db->query($sql);

        // $content = file_get_contents('http://203.27.235.149:98/GetRpt.aspx?srno='.trim($serialNo));
        // $getDob = $this->getDob($txtAppFormNo);
        // $getDob = str_replace('/', '', $getDob);
        // $path = APS_CIBIL.date("d-m-Y").'\\';
        // if (!file_exists($path)) {
        //     mkdir($path, 0777, true);
        // }
        // file_put_contents($path."CR-".$txtAppFormNo."-".$getDob.".pdf", $content);
    }

    public function downloadCibilReport($txtAppFormNo, $refNo){
        if($refNo != 0){
            $content = file_get_contents('http://203.27.235.149:98/GetRpt.aspx?srno='.trim($refNo));
            $getDob = $this->getDob($txtAppFormNo);
            $getDob = str_replace('/', '', $getDob);
            $path = APS_CIBIL.date("d-m-Y").'\\';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            file_put_contents($path."CR-".$txtAppFormNo."-".$getDob.".pdf", $content);
        }
    }

    public function startExcistingApsProcess($driver, $apsNo, $txtAppFormNo, $branchId){

        $caseSearchURL = "https://aps.icicibank.com/ICICIWeb/CaseIDSearch.los?category=PERSONAL&selBranch=".$branchId;
        $driver->get($caseSearchURL);

        $selector = WebDriverBy::name("txtAppId");
        $element = $driver->findElement($selector);
        $element->sendKeys($apsNo);

        $selector = WebDriverBy::name("btnSearch");
        $element = $driver->findElement($selector);
        $element->click();

        $selector = WebDriverBy::name("caseIdAF");
        $driver->wait(20,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated($selector)
        );

        $selector = WebDriverBy::xpath("/html/body/form/table[4]/tbody/tr[2]/td[2]");
        if (count($driver->findElements($selector)) === 0) {
            return;
        }else{
            $element = $driver->findElement($selector);
            $branchName = trim($element->getText());
            echo '<br>Branch:'.$branchName.'<br>';

            $allBranchName = $this->getAllBranches($branchId);

            if(!in_array($branchName, $allBranchName)){
                return 0;
            }else{
                $selector = WebDriverBy::xpath("/html/body/form/table[4]/tbody/tr[2]/td[4]");
                $element = $driver->findElement($selector);
                $status = trim($element->getText());
                echo '<br>Branch:'.$status.'<br>';

                if($status == "" || $status == "DOC"){

                    $caseSearchURL = "https://aps.icicibank.com/ICICIWeb/ProductList.los";
                    $driver->get($caseSearchURL);
                    
                    $selector = WebDriverBy::name("btnProductSaveDetails");
                    $element = $driver->findElement($selector);
                    $element->click();

                    $selector = WebDriverBy::name("contents");
                    $driver->wait(20,1000)->until(
                        WebDriverExpectedCondition::visibilityOfElementLocated($selector)
                    );
                    $frame = $driver->findElement($selector);
                    $driver->switchTo()->frame($frame);

                    $driver->findElement(WebDriverBy::linkText("Intray"))->click();
                    $driver->manage()->timeouts()->implicitlyWait = 4;
                    $driver->findElement(WebDriverBy::linkText("Unsecured Loan"))->click();
                    $driver->manage()->timeouts()->implicitlyWait = 4;
                    $driver->findElement(WebDriverBy::linkText("All"))->click();
                    $driver->manage()->timeouts()->implicitlyWait = 4;

                    // $selector = WebDriverBy::xpath('//*[@id="ntTree118d"]/table/tbody/tr/td[3]/a');
                    // $element = $driver->findElement($selector);
                    // $element->click();

                    $driver->switchTo()->defaultContent();

                    $selector = WebDriverBy::name("main");
                    $frame = $driver->findElement($selector);
                    $driver->switchTo()->frame($frame);

                    $selector = WebDriverBy::name("txtApplication");
                    $driver->wait(20,1000)->until(
                        WebDriverExpectedCondition::visibilityOfElementLocated($selector)
                    );
                    $element = $driver->findElement($selector);
                    $element->sendKeys($apsNo);
                    
                    $selector = WebDriverBy::name("selOffice");
                    $element = new WebDriverSelect($driver->findElement($selector));
                    $element->selectByValue($branchId);

                    $selector = WebDriverBy::name("btnSearch");
                    $element = $driver->findElement($selector);
                    $element->click();

                    $selector = WebDriverBy::xpath("/html/body/*[starts-with(name(), 'html')]/form/table[1]/tbody/tr[4]/td[7]");
                    $driver->wait(20,1000)->until(
                        WebDriverExpectedCondition::visibilityOfElementLocated($selector)
                    );

                    if($status == "DOC"){

                        for ($i=4; $i < 7; $i++) {

                            $selector = WebDriverBy::xpath("/html/body/*[starts-with(name(), 'html')]/form/table[1]/tbody/tr[$i]/td[7]");

                            $elements = $driver->findElements($selector);
                            
                            if (count($elements) == 0) {
                                echo 'No';
                                continue;
                            }else{

                                $element = $driver->findElement($selector);
                                $activity = trim($element->getText());
                                echo '<br>Activity:'.$activity.'<br>';
                                if ($activity == "Pre Sanc Doc") {

                                    //stage reversal process
                                    $stageReversalURL = "https://aps.icicibank.com/ICICIWeb/Activity.los?activity=STG&currentActivity=STG&txtApplicationNo=".$apsNo."&category=PERSONAL&mode=E&inBranchID=".$branchId;
                                    $driver->get($stageReversalURL);

                                    $selector = WebDriverBy::name("selActivityTo");
                                    $element = new WebDriverSelect($driver->findElement($selector));
                                    $element->selectByValue("BDE");

                                    $selector = WebDriverBy::name("txtReasonDesc");
                                    $element = $driver->findElement($selector);
                                    $element->sendKeys($txtAppFormNo);

                                    // $selector = WebDriverBy::name("btnSave");
                                    // $element = $driver->findElement($selector);
                                    // $element->click();
                                    return 1;

                                }else{
                                    return 0;
                                }
                            }
                        }
                        return 0;
                    }else{

                        for ($i=4; $i < 7; $i++) {

                            $selector = WebDriverBy::xpath("/html/body/*[starts-with(name(), 'html')]/form/table[1]/tbody/tr[$i]/td[9]");

                            if (count($driver->findElements($selector)) === 0) {
                                continue;
                            }else{
                                $element = $driver->findElement($selector);
                                $user = trim($element->getText());
                                echo '<br>User:'.$user.'<br>';
                                if (strpos($user, 'VARA') !== false) {
                                    return 1;
                                }else{
                                    return 0;
                                }
                            }
                        }
                        return 0;
                    }

                }else{
                    return 0;
                }
            }
        }
    }

    public function getAllBranches($branchId){
        $sql = "SELECT branch_code, branch_desc FROM plbranch_master WHERE branch_code = '$branchId'";
        $rows = $this->db->query($sql)->result_array();
        foreach ($rows as $key => $row) {
            $arr[] = $row['branch_desc'];
        }
        return $arr;
    }

    public function updateApplicationNo($oldAppFormNo, $oldApsNo){

        $prefix = substr($oldAppFormNo, 0, 4);
        $postfix = substr($oldAppFormNo, 4);

        if($prefix == "5100"){
            $newAppFormNo = "5800".$postfix;
        }else{
            $newAppFormNo = "5100".$postfix;
        }

        $sql = "UPDATE hfccustdata SET txtAppFormNo = '$newAppFormNo', oldApsNo = '$oldApsNo', oldAppNo = '$oldAppFormNo' where txtAppFormNo = '$oldAppFormNo'";
        $qparent = $this->db->query($sql);

        $sql = "UPDATE pldataentry SET applicationNo = '$newAppFormNo' where applicationNo = '$oldAppFormNo'";
        $qparent = $this->db->query($sql);

        $sql = "UPDATE bot_aps_tracking SET txtAppFormNo = '$newAppFormNo' where txtAppFormNo = '$oldAppFormNo'";
        $qparent = $this->db->query($sql);

        $sql = "UPDATE alplapplications SET applicationNo = '$newAppFormNo' where applicationNo = '$oldAppFormNo'";
        $qparent = $this->db->query($sql);

        return $newAppFormNo;

    }

    public function checkForExistingCustId($driver, $txtAppFormNo){

        $selector = WebDriverBy::xpath("/html/body/form/table[8]/tbody/tr[3]/td[2]/a");

        if (count($driver->findElements($selector)) === 0) {
            //continue;
        }else{

            $element = $driver->findElement($selector);
            $custDtl = $element->getAttribute('onclick');
            $custId = "";

            if (preg_match("/'([^']+)'/", $custDtl, $m)) {
                $url = $m[1];
                $parts = parse_url($url);
                parse_str($parts['query'], $query);
                $custId = $query['hidCustomerID'];
            }

            $sql = "UPDATE hfccustdata SET hidCustomerID = '$custId' where txtAppFormNo = '$txtAppFormNo'";
            $qparent = $this->db->query($sql);

            $appURL = "https://aps.icicibank.com/ICICIWeb/PersonalInfo.los?displayFlag=P&showLowerTab=T&pageName=PersonalInfo.los&tabKey=APPLICANT&currentActivity=BDE&activity=BDE&hidCustomerID=".$custId."&ComingFrom=APPLIST&hidGCDCustomerID=";

            $driver->get($appURL);
        }
    }

    public function downloadImages($files){
        if(!empty($files["input-file"]["name"])){
            $output = '';
            $ext = explode(".", $files["input-file"]["name"]);
            if($ext[1] == 'csv'){
                $this->startDownloadImages($files);
            }else{
                return 2;
            }
        }else{
            return 3;
        }
        return 4;  
    }

    public function startDownloadImages($files){

        $file_data = fopen($_FILES["input-file"]["tmp_name"], 'r');
        fgetcsv($file_data);
        $appNos = array();
        while($row = fgetcsv($file_data)){  
            $appNos[] = $row[0];
        }
        fclose($file_data);

        //$appNos = array('VJAP9216091','CHEP8972945','XXXXXX','7722500847');
        $appNames = array('Application_Form','Application Form');

        $host = 'http://localhost:4445/wd/hub';
    
        $capabilities = DesiredCapabilities::chrome();

        $driver = RemoteWebDriver::create($host, $capabilities, 50000);
        
        $driver->get("https://idisburse.icicibank.com:447/idecisions/ilogin");
        $driver->findElement(WebDriverBy::id("username"))->sendKeys("51014");
        $driver->findElement(WebDriverBy::id("password"))->sendKeys("PHANI@95");
        $select = new WebDriverSelect($driver->findElement(WebDriverBy::id('apsOdUserType')));
        $select->selectByValue('V');
        $driver->findElement(WebDriverBy::id('SUBMIT'))->click();
        $driver->wait()->until(WebDriverExpectedCondition::urlIs("https://idisburse.icicibank.com:447/idecisions/ApsOdHome.jsp")
        );
        $skipOD = 0;

        $log = "";

        foreach ($appNos as $appNo) {

            echo '---------------------------------<br>Application : '.$appNo.'<br>---------------------------------<br>';

            if($skipOD == 0){
                $frame = $driver->findElement(WebDriverBy::id('OD'));
                $driver->switchTo()->frame($frame);
            }

            $driver->findElement(WebDriverBy::id('application_form_search'))->clear();
            $driver->findElement(WebDriverBy::id('application_form_search'))->sendKeys($appNo);
            $driver->findElement(WebDriverBy::id('get'))->click();
            sleep(2);

            if($this->isAlertPresent($driver)){
                $log = $appNo.' : Folder not found.'.PHP_EOL;
                $myfile = file_put_contents(LOG_FILE_PATH, $log , FILE_APPEND | LOCK_EX);
                echo 'Folder not found.<br><br>';
                //$a->accept();
                $skipOD = 1;
                continue;
            }

            $rows = $driver->findElements(WebDriverBy::xpath('//*[@id="documents"]/tr'));
            $foundAt = 0;
            $count = 0;

            while($foundAt == 0 && $count < 5){
                for ($i=1; $i <= count($rows); $i++) {
                    $e = $driver->findElement(WebDriverBy::xpath('//*[@id="documents"]/tr['.$i.']/td[2]/a'));
                    $file_name = $e->getText();
                    echo 'File : '.$file_name.'<br>';
                    if(in_array($file_name, $appNames)){
                        $foundAt = $i;
                        break 2;
                    }
                }
                $driver->findElement(WebDriverBy::id("viewMore_DocumentList"))->click();
                sleep(2);
                $rows = $driver->findElements(WebDriverBy::xpath('//*[@id="documents"]/tr'));
                $count++;
            }

            if($foundAt == 0){
                $log = $appNo.' : File not found.'.PHP_EOL;
                $myfile = file_put_contents(LOG_FILE_PATH, $log , FILE_APPEND | LOCK_EX);
                echo 'File not found.<br><br>';
                $skipOD = 1;
                continue;
            }else{
                echo 'File found at : '.$foundAt.'<br><br>';
                $driver->findElement(WebDriverBy::xpath('//*[@id="documents"]/tr['.$foundAt.']/td[2]/a'))->click();
            }

            $firstWindow = $driver->getWindowHandle();
            $handles = $driver->getWindowHandles();
            $lastHandle = end($handles);
            $driver->switchTo()->window($lastHandle);

            $frame = $driver->findElement(WebDriverBy::name('frametop'));
            $driver->switchTo()->frame($frame);

            $selector = WebDriverBy::xpath('//*[@name="docList"]/option[1]');
            
            $tmp_count = 0;
            while ($tmp_count < 3) {
                try {
                    $driver->wait(20,1000)->until(
                        WebDriverExpectedCondition::visibilityOfElementLocated($selector)
                    );
                    $e = $driver->findElement($selector);
                    break;
                } catch (Exception $e) {
                    $tmp_count++;
                }
            }
            
            $filename = $e->getText();
            $prefix = explode('.', $filename);

            foreach (glob(DOWNLOAD_PATH.$prefix[0].'*.*') as $file) {
                unlink($file);
            }
            
            $driver->switchTo()->defaultContent();
            $frame = $driver->findElement(WebDriverBy::name('framebottom'));
            $driver->switchTo()->frame($frame);
            
            sleep(2);

            $driver->executeScript('Download();');

            $path = DOWNLOAD_PATH.$filename;

            while (!file_exists($path)) {
                //wait for it...
            }

            if (!file_exists(IMAGE_PATH)) {
                mkdir(IMAGE_PATH, 0777, true);
            }
            rename($path, IMAGE_PATH.$appNo.".tif");
            
            $driver->close();
            $driver->switchTo()->window($firstWindow);
            $skipOD = 0;
            $log = "";

            $log = $appNo.' : Done.'.PHP_EOL;
            $myfile = file_put_contents(LOG_FILE_PATH, $log , FILE_APPEND | LOCK_EX);

        }
        $driver->quit();
    }

    public function isAlertPresent($driver){
        try {
            $driver->switchTo()->alert()->accept();
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }


}
?>