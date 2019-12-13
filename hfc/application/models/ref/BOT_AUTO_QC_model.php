<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverSelectInterface;
use Facebook\WebDriver\WebDriverWindow;

class BOT_AUTO_QC_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        $this->load->model('BOT_Model', 'bot');
        $this->load->model('BOT_CAM_Model', 'bot_cam');
        //$this->load->model('BOT_QC_Model', 'bot_qc');
    }

    public function startAutoQC($apps){

        $host = 'http://localhost:4445/wd/hub';
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 50000);

        $this->bot_cam->login($driver);

        $apps = explode(',', $apps);
        $apps = array_unique($apps);

        foreach ($apps as $app) {

            $this->updateEntry($app);

            $sql = "SELECT a.selBranchLevel, a.selBranch, a.hidCustomerID, a.apsNo, a.txtAppFormNo FROM hfccustdata a WHERE a.txtAppFormNo = '$app'";

            $rows = $this->db->query($sql)->result_array();

            $this->bot_cam->selectBranch($driver, $rows[0]['selBranchLevel'], $rows[0]['selBranch']);

            $apsStatus = $this->checkApsStatus($driver, $rows);

            if($apsStatus == 'DOC'){
                $this->getQCData($driver, $rows);
                $this->getDifference($app);
            }
            // $this->getQCData($driver, $rows);
            // $this->getDifference($app);

            $sql = "UPDATE bot_aps_qc_tracking SET end_time = '".date("Y-m-d H:i:s")."' WHERE txtAppFormNo = '".$app."'";
            $this->db->query($sql);
            $sql = "UPDATE bot_aps_tracking SET is_auto_qc_done = 1 WHERE txtAppFormNo = '".$app."'";
            $this->db->query($sql);

        }
        $driver->quit();
        return 1;
    }

    public function updateEntry($app){
        $sql = "SELECT COUNT(*) AS TOTAL FROM bot_aps_qc_tracking WHERE txtAppFormNo = '$app'";
        $rows = $this->db->query($sql)->result_array();
        if($rows[0]['TOTAL'] <= 0){
            $localIP = getHostByName(getHostName());
            $sql = "INSERT INTO bot_aps_qc_tracking (`txtAppFormNo`,`start_time`,`ip_address`) VALUES('$app','".date("Y-m-d H:i:s")."','".$localIP."')";
            $qparent = $this->db->query($sql);
        }else{
            $sql = "UPDATE bot_aps_qc_tracking SET start_time = '".date("Y-m-d H:i:s")."' WHERE txtAppFormNo = '".$app."'";
            $this->db->query($sql);
        }
        $sql = "SELECT COUNT(*) AS TOTAL FROM hfccustdata_ver WHERE txtAppFormNo = '$app'";
        $rows = $this->db->query($sql)->result_array();
        if($rows[0]['TOTAL'] <= 0){
            $sql = "INSERT INTO hfccustdata_ver (`txtAppFormNo`) VALUES('$app')";
            $qparent = $this->db->query($sql);
        }
    }

    public function checkApsStatus($driver, $rows){
        $caseSearchURL = "https://aps.icicibank.com/ICICIWeb/CaseIDSearch.los?category=PERSONAL&selBranch=".$rows[0]['selBranch'];
        $driver->get($caseSearchURL);

        $selector = WebDriverBy::name("txtAppId");
        $element = $driver->findElement($selector);
        $element->sendKeys($rows[0]['apsNo']);

        $selector = WebDriverBy::name("btnSearch");
        $element = $driver->findElement($selector);
        $element->click();

        $selector = WebDriverBy::name("caseIdAF");
        $driver->wait(20,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated($selector)
        );

        $selector = WebDriverBy::xpath("/html/body/form/table[4]/tbody/tr[2]/td[2]");
        if (count($driver->findElements($selector)) === 0) {
            $apsStatus = '';
        }else{
            $selector = WebDriverBy::xpath("/html/body/form/table[4]/tbody/tr[2]/td[4]");
            $element = $driver->findElement($selector);
            $apsStatus = trim($element->getText());
        }
        $sql = "UPDATE bot_aps_qc_tracking SET aps_status = '$apsStatus' WHERE txtAppFormNo = '".$rows[0]['txtAppFormNo']."'";
        $this->db->query($sql);
        return $apsStatus;
    }

    public function getQCData($driver, $rows){

        /* Sourcing Details Starts */
        $driver->get("https://aps.icicibank.com/ICICIWeb/Activity.los?activity=BDE&currentActivity=BDE&txtApplicationNo=".$rows[0]['apsNo']."&category=PERSONAL&mode=E&inBranchID=".$rows[0]['selBranch']);
        $this->getData(2, $driver, $rows);
        /* Sourcing Details Ends */

        /* Applicant Personal Starts */
        $driver->get("https://aps.icicibank.com/ICICIWeb/PersonalInfo.los?displayFlag=P&showLowerTab=T&pageName=PersonalInfo.los&tabKey=APPLICANT&currentActivity=BDE&activity=BDE&hidCustomerID=".$rows[0]['hidCustomerID']."&ComingFrom=APPLIST&hidGCDCustomerID=");
        $this->getData(3, $driver, $rows);
        /* Applicant Personal Ends */

        /* Applicant Other Details Starts */
        $driver->get("https://aps.icicibank.com/ICICIWeb/PersonalInfo.los?Action=OtherPersonalDetails&CustId=".$rows[0]['hidCustomerID']."&ApplicantType=P&GCDCustId=&currentActivity=BDE");
        $this->getData(4, $driver, $rows);
        /* Applicant Other Details Ends */


        /* Applicant Address Starts */
        $driver->get("https://aps.icicibank.com/ICICIWeb/Address.los?displayFlag=P&showLowerTab=T&pageName=Address.los&tabKey=APPLICANT&currentActivity=BDE&activity=BDE&hidCustomerID=".$rows[0]['hidCustomerID']."&ComingFrom=APPLIST&hidGCDCustomerID=");
        $this->getData(5, $driver, $rows);
        /* Applicant Address Ends */

        /* Applicant Word Details Starts */
        $driver->get("https://aps.icicibank.com/ICICIWeb/WorkDetail.los?displayFlag=P&showLowerTab=T&pageName=WorkDetail.los&tabKey=APPLICANT&currentActivity=BDE&activity=BDE&hidCustomerID=".$rows[0]['hidCustomerID']."&ComingFrom=APPLIST&hidGCDCustomerID=");
        $this->getData(6, $driver, $rows);
        /* Applicant Word Details Ends */

        /* Applicant Income Expense Starts */
        $driver->get("https://aps.icicibank.com/ICICIWeb/IncomeExpense.los?displayFlag=P&showLowerTab=T&pageName=IncomeExpense.los&tabKey=APPLICANT&currentActivity=BDE&activity=BDE&hidCustomerID=".$rows[0]['hidCustomerID']."&ComingFrom=APPLIST&hidGCDCustomerID=");
        $this->getData(7, $driver, $rows);
        /* Applicant Income Expense Ends */

        /* Applicant Bank Details Starts */
        $driver->get("https://aps.icicibank.com/ICICIWeb/PersonalBank.los?displayFlag=P&showLowerTab=T&pageName=PersonalBank.los&tabKey=APPLICANT&currentActivity=BDE&activity=BDE&hidCustomerID=".$rows[0]['hidCustomerID']."&ComingFrom=APPLIST&hidGCDCustomerID=");
        $this->getData(8, $driver, $rows);
        /* Applicant Bank Details Ends */

        /* Applicant References Starts */
        $driver->get("https://aps.icicibank.com/ICICIWeb/CustRelationRefDetail.los?activity=BDE&currentActivity=BDE&txtApplicationNo=".$rows[0]['apsNo']."&category=CUSTRELATIONREF&mode=E&inBranchID=".$rows[0]['selBranch']);
        $this->getData(9, $driver, $rows);
        /* Applicant References Ends */

        /* Applicant Asset & Loan Details Starts */
        $driver->get("https://aps.icicibank.com/ICICIWeb/FinancialCombo.los?activity=BDE&currentActivity=BDE&txtApplicationNo=".$rows[0]['apsNo']."&category=FINANCIALCOMBO&mode=E&inBranchID=".$rows[0]['selBranch']);
        $this->getData(10, $driver, $rows);
        /* Applicant Asset & Loan Details Ends */

        /* Applicant Presanctions Starts */
        $driver->get("https://aps.icicibank.com/ICICIWeb/Activity.los?activity=DOC&currentActivity=DOC&txtApplicationNo=".$rows[0]['apsNo']."&category=PERSONAL&mode=E&inBranchID=".$rows[0]['selBranch']);
        $this->getData(13, $driver, $rows);
        /* Applicant Presanctions Ends */

    }

    public function getData($process_dtl_id, $driver, $rows){

        $data = array();
        $loop = 0;

        $sql = "SELECT * FROM bot_sequence_dtl a WHERE a.process_dtl_id = $process_dtl_id AND isDel != 1 ORDER BY a.process_dtl_id, a.seq_no";
        $result = $this->db->query($sql)->result();

        foreach ($result as $key => $control) {

            /* custum code starts */
            if($control->selector_id == "selAddressType" || $control->selector_id == "selBankName" || $control->selector_id == "txtReferenceName"){
                try {
                    $driver->executeScript("return updateFunc('$loop')");
                    $loop++;
                } catch (Exception $e) {
                    break;
                }
            }
            if($control->selector_id == "selIncomeSalHead"){
                try {
                    $driver->executeScript("return updateFuncIncome('0')");
                } catch (Exception $e) {
                    break;
                }
            }
            if($control->selector_id == "selDocID"){
                try {
                    $driver->executeScript("return selRowNew('$loop')");
                    $loop++;
                } catch (Exception $e) {
                    break;
                }
            }
            if($control->selector_id == "txtRCUDate" || $control->selector_id == "selRCUID" || $control->selector_id == "selRCUStatus"){
                continue;
            }
            /* custum code ends */

            $selector = $this->bot->getSelector($control);

            switch ($control->control_id) {
                case 1:
                case 12:
                    //input box
                    $element = $driver->findElement($selector);
                    $value = $element->getAttribute('value');
                    break;
                case 3:
                    //select dropdown
                    $element = $driver->findElement($selector);
                    $select = new WebDriverSelect($element);
                    $option = $select->getFirstSelectedOption();
                    $value = $option->getAttribute('value');
                    break;

                default:
                    $value = '';
                    break;
            }
            if($control->model != ''){
                $data[$control->model] = trim($value);
            }
        }
        $str = '';
        foreach ($data as $key => $v) {
            $str = $str .= "$key = '$v', ";
        }
        if($str != ''){
            $str = rtrim($str,', ');
            $sql = "UPDATE hfccustdata_ver SET $str WHERE txtAppFormNo = '".$rows[0]['txtAppFormNo']."'";
            $this->db->query($sql);
        }
    }

    public function getDifference($app){

        $log = '';

        $sql = "SELECT * FROM hfccustdata_ver a WHERE a.txtAppFormNo = '$app'";
        $rows_ver = $this->db->query($sql)->result_array();

        $sql = "SELECT * FROM hfccustdata a WHERE a.txtAppFormNo = '$app'";
        $rows = $this->db->query($sql)->result_array();

        $total_errors = 0;

        $sql = "SELECT model FROM bot_sequence_dtl where isVer = 1 and isDel != 1";
        $model_vers = $this->db->query($sql)->result_array();

        foreach ($model_vers as $key => $value) {
            if($rows[0][$value['model']] != ''){
                if(strtoupper($rows_ver[0][$value['model']]) != strtoupper(trim($rows[0][$value['model']]))){
                    $total_errors++;
                    $log .= "<br>".$value['model']." : $".$rows_ver[0][$value['model']]."$ = $".$rows[0][$value['model']]."$ [Not Matched]<br>";
                }
            }
        }

        $sql = "UPDATE bot_aps_qc_tracking SET total_errors = '$total_errors' WHERE txtAppFormNo = '$app'";
        $this->db->query($sql);
        return $log;
    }

    public function showQCExceptions($app){

        $sql = "SELECT * FROM hfccustdata_ver a WHERE a.txtAppFormNo = '$app'";
        $rows_ver = $this->db->query($sql)->result_array();

        $sql = "SELECT * FROM hfccustdata a WHERE a.txtAppFormNo = '$app'";
        $rows = $this->db->query($sql)->result_array();

        $sql = "SELECT a.model, a.selector_desc, b.process_dtl_desc, b.process_dtl_id FROM bot_sequence_dtl a left join bot_process_dtl b on b.process_dtl_id = a.process_dtl_id where a.isVer = 1 and a.isDel != 1";
        $model_vers = $this->db->query($sql)->result_array();
        $sections = array();

        foreach ($model_vers as $key => $value) {
            if($rows[0][$value['model']] != ''){
                if(strtoupper($rows_ver[0][$value['model']]) != strtoupper(trim($rows[0][$value['model']]))){
                    $data[$key]['model'] = $value['model'];
                    $data[$key]['selector_desc'] = $value['selector_desc'];
                    $data[$key]['process_dtl_desc'] = $value['process_dtl_desc'];
                    $data[$key]['process_dtl_id'] = $value['process_dtl_id'];
                    $data[$key]['aps_val'] = $rows_ver[0][$value['model']];
                    $data[$key]['bde_val'] = $rows[0][$value['model']];
                    $sections[$value['process_dtl_id']] = $value['process_dtl_desc'];
                }
            }
        }

        $sections = array_unique($sections);
        $content = '';

        $tabs = '';
        $tmp = 1;
        foreach ($sections as $key => $sec) {
            $class = ($tmp == 1)?" active":"";
            $tabs .= '<li class="'.$class.'"><a href="#qc-exp-tab_'.$key.'" data-toggle="tab" class="dt-tabs"> '.$sec.' </a></li>';

            $class = ($tmp == 1)?" fade active in":"";
            $tmp = 0;

            $content .= '<div class="tab-pane fade'.$class.'" id="qc-exp-tab_'.$key.'">
                            <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>Field</th>
                                    <th>Description</th>
                                    <th>BDE Value</th>
                                    <th>APS Value</th>
                                  </tr>
                                </thead>
                                <tbody>';

            foreach ($data as $value) {
                if($value['process_dtl_id'] == $key){
                    $content .= '<tr><td>'.$value['model'].'</td><td>'.$value['selector_desc'].'</td><td>'.$value['bde_val'].'</td><td>'.$value['aps_val'].'</td></tr>';
                }
            }

            $content .= '</tbody></table></div></div>';
        }

        $str = '<div class="col-md-3 col-sm-3 col-xs-3">
                    <ul class="nav nav-tabs tabs-left">'.$tabs.'</ul>
                </div>
                <div class="col-md-9 col-sm-9 col-xs-9">
                    <div class="tab-content">'.$content.'</div>
                </div>';
        return $str;
    }
}
?>