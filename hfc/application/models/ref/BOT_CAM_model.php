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
//use thiagoalessio\TesseractOCR\TesseractOCR;

class BOT_CAM_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('BOT_Model', 'bot');
    }

    public function test()
    {
        return 'Working';
    }

    public function saveCamData($apps){

        $host = 'http://localhost:4445/wd/hub';
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 50000);

        $this->login($driver);

        $apps = explode(',', $apps);
        $apps = array_unique($apps);

        foreach ($apps as $app) {

            $this->updateEntry($app);

            $sql = "SELECT a.selBranchLevel, a.selBranch, a.hidCustomerID, a.apsNo, a.txtAppFormNo, a.selBankName_1, a.selBankName_2, a.txtCompnayName, a.txtPanNo, a.txtEmail_4, a.txtAccountNumber_1, a.selDistrict_1, a.txtZip_1, b.state, b.official_email_id FROM hfccustdata a LEFT JOIN pldataentry b ON b.applicationNo = a.txtAppFormNo WHERE a.txtAppFormNo = '$app'";

            $rows = $this->db->query($sql)->result_array();

            $this->selectBranch($driver, $rows[0]['selBranchLevel'], $rows[0]['selBranch']);

            if($rows[0]['selBankName_1'] == 'Icici Bank Ltd' || $rows[0]['selBankName_2'] == 'Icici Bank Ltd'){
                $this->fetchBankDetails($driver, $rows);
            }

            $batchId = $this->bot->getBatchId($app);
            $fncs = array('PL-CBA','PL-CCH','PL-CGA','PL-CVZ','PL-CKB');
            $caseType = 'FNCB';
            $batch_prefix = explode('_', $batchId);
            if(in_array($batch_prefix[0], $fncs)){
                //$this->saveUnderwritingDetails($driver, $rows);
                $caseType = 'FNCS';
            }
            $this->saveUnderwritingDetails($driver, $rows);
            $this->downloadCibilReport($driver, $rows);
            $this->saveScreenshots($driver, $rows, $caseType);

            $sql = "UPDATE pl_apscamdata SET end_time = '".date("Y-m-d H:i:s")."', isCamDataSaved = 1 WHERE txtAppFormNo = '".$app."'";
            $this->db->query($sql);

        }
        $driver->quit();
        return 1;
    }

    public function updateEntry($app){
        $sql = "SELECT COUNT(*) AS TOTAL FROM pl_apscamdata WHERE txtAppFormNo = '$app'";
        $rows = $this->db->query($sql)->result_array();
        if($rows[0]['TOTAL'] <= 0){
            $localIP = getHostByName(getHostName());
            $sql = "INSERT INTO pl_apscamdata (`txtAppFormNo`,`start_time`,`ip_address`) VALUES('$app','".date("Y-m-d H:i:s")."','".$localIP."')";
            $qparent = $this->db->query($sql);
        }
    }

    public function downloadCibilReport($driver, $rows){
        $driver->navigate()->to("https://aps.icicibank.com/ICICIWeb/CPCS.los?AppId=".$rows[0]['apsNo']."&Action=CIBIL");
        try {
            $driver->wait()->until(WebDriverExpectedCondition::alertIsPresent());
            $driver->switchTo()->alert()->accept();
        } catch (Exception $e) {
            
        }
        sleep(1);
        $driver->navigate()->to("https://aps.icicibank.com/ICICIWeb/CPCS.los?Action=CIBILREPORT&AppId=".$rows[0]['apsNo']);
        $selector = WebDriverBy::xpath("/html/body/table[4]/tbody/tr[2]/td[1]/a");
        $elements = $driver->findElements($selector);
        if(count($elements) > 0){
            $element = $driver->findElement($selector);
            $serialNo = trim($element->getText());
            $sql = "UPDATE hfccustdata SET cibilRefNo = '$serialNo' where apsNo = '".$rows[0]['apsNo']."'";
            $qparent = $this->db->query($sql);
            $this->bot->downloadCibilReport($rows[0]['txtAppFormNo'], $serialNo);
        }
    }

    public function login($driver){
        $driver->get("https://idisburse.icicibank.com:447/idecisions/ilogin");
        $driver->findElement(WebDriverBy::id("username"))->sendKeys("VARAPL5");
        $driver->findElement(WebDriverBy::id("password"))->sendKeys("VARA@123");
        $select = new WebDriverSelect($driver->findElement(WebDriverBy::id('apsOdUserType')));
        $select->selectByValue('V');
        $driver->findElement(WebDriverBy::id('SUBMIT'))->click();
        $driver->wait()->until(
          WebDriverExpectedCondition::urlIs("https://idisburse.icicibank.com:447/idecisions/ApsOdHome.jsp")
        );
        $driver->get("https://aps.icicibank.com/ICICIWeb/CheckLogin.los");
        $driver->findElement(WebDriverBy::name("userLoginView"))->sendKeys("VARAPL5");
        $driver->findElement(WebDriverBy::name("userPasswordView"))->sendKeys("VARA@123");
        $driver->findElement(WebDriverBy::cssSelector("input[type='button'][value='Login']"))->click();  
        $driver->wait()->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();
    }
    public function selectBranch($driver, $branchLevel, $branch){
        $driver->navigate()->to("https://aps.icicibank.com/ICICIWeb/ProductList.los");
        try {
            $driver->findElement(WebDriverBy::name("btnProductSaveDetails"))->click();
        } catch (Exception $e) {
            
        }
        $my_frame = $driver->findElement(WebDriverBy::name('banner'));
        $driver->switchTo()->frame($my_frame);
        $select = new WebDriverSelect($driver->findElement(WebDriverBy::name('selBranchLevel')));
        $select->selectByValue($branchLevel);
        $driver->wait()->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();
        sleep(1);
        $driver->wait()->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name('selBranch'))
        );
        sleep(1);
        $select = new WebDriverSelect($driver->findElement(WebDriverBy::name('selBranch')));
        $select->selectByValue($branch);
        try {
            $driver->wait()->until(WebDriverExpectedCondition::alertIsPresent());
            $driver->switchTo()->alert()->accept();
        } catch (Exception $e) {
            
        }
        $driver->switchTo()->defaultContent();
    }
    public function fetchBankDetails($driver, $rows){

        $driver->navigate()->to("https://aps.icicibank.com/ICICIWeb/Activity.los?activity=UND&currentActivity=UND&txtApplicationNo=".$rows[0]['apsNo']."&category=PERSONAL&mode=E&inBranchID=".$rows[0]['selBranch']);

        $driver->navigate()->to("https://aps.icicibank.com/ICICIWeb/PersonalBank.los?hidCustomerID=".$rows[0]['hidCustomerID']."&showLowerTab=T&pageName=PersonalBank.los&tabKey=APPLICANT&displayFlag=P&activity=BDE&currentActivity=UND&activityEditE&hidIsLink=Y&tabKey=PERSONALBANK&pageName=PersonalBank.los?hidCustomerID=".$rows[0]['hidCustomerID']."&showLowerTab=T&pageName=PersonalBank.los&tabKey=APPLICANT&displayFlag=P&activity=BDE&currentActivity=UND&activityEditE&hidIsLink=Y");

        $selector = WebDriverBy::xpath("/html/body/form/table[10]/tbody/tr");
        $banks = $driver->findElements($selector);
        $foundAt = 0;

        for ($i=2; $i <= count($banks); $i++) {
            $ele = $driver->findElement(WebDriverBy::xpath('/html/body/form/table[10]/tbody/tr['.$i.']/td[2]/a'));
            $bank_name = $ele->getText();
            if($bank_name == 'Icici Bank Ltd ' || $bank_name == 'Icici Bank Ltd&nbsp;'){
                $foundAt = $i;
                break;
            }
        }

        if($foundAt == 0){
            return;
        }else{
            $driver->findElement(WebDriverBy::xpath('/html/body/form/table[10]/tbody/tr['.$foundAt.']/td[2]/a'))->click();
            sleep(1);

            try {
                $driver->findElement(WebDriverBy::name('btnExtraField'))->click();
            } catch (Exception $e) {
                return;
            }

            $handle = $driver->getWindowHandle();
            $handles = $driver->getWindowHandles();
            $lastHandle = end($handles);
            $driver->switchTo()->window($lastHandle);

            $driver->findElement(WebDriverBy::name('btnDoc2'))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::name('btnDoc'))->click();
            sleep(1);

            $arr2 = array('bal_5','bal_15','bal_25','month_avg_bal','times_of_emi','month_bank_credits','times_of_emi_2','no_inward_cheq_returns','count_of_credit_trans','idtvsic');
            $arr = array();

            $sql = "DELETE FROM pl_apsbankdata WHERE txtAppFormNo = '".$rows[0]['txtAppFormNo']."'";
            $this->db->query($sql);

            for ($i=0; $i < 6; $i++) {
                $c = ($i != 0) ? $i : '';
                $arr['month'] = $driver->executeScript('return document.getElementsByName("selMonth'.$c.'")[0].value;');
                $arr['year'] = $driver->executeScript('return document.getElementsByName("selYear'.$c.'")[0].value;');
                for ($j=1; $j <= 10; $j++) {
                    $arr[$arr2[$j - 1]] = $driver->executeScript('return document.getElementsByName("txtMonth'.$c.$j.'")[0].value;');
                }
                $sql = "INSERT INTO `pl_apsbankdata`(`txtAppFormNo`,`month`,`year`,`bal_5`,`bal_15`,`bal_25`,`month_avg_bal`,`times_of_emi`,`month_bank_credits`,`times_of_emi_2`,`no_inward_cheq_returns`,`count_of_credit_trans`,`idtvsic`) VALUES ('".$rows[0]['txtAppFormNo']."','".$arr['month']."','".$arr['year']."','".$arr['bal_5']."','".$arr['bal_15']."','".$arr['bal_25']."','".$arr['month_avg_bal']."','".$arr['times_of_emi']."','".$arr['month_bank_credits']."','".$arr['times_of_emi_2']."','".$arr['no_inward_cheq_returns']."','".$arr['count_of_credit_trans']."','".$arr['idtvsic']."')";
                $this->db->query($sql);
            }
            
            $driver->executeScript("callPersonalBank2('CC')");
            sleep(1);
            $driver->close();
            $driver->switchTo()->window($handle);
            $driver->findElement(WebDriverBy::name('btnUpdate'))->click();
            sleep(1);
            $sql = "UPDATE pl_apscamdata SET isBankFetched = 1 WHERE txtAppFormNo = '".$rows[0]['txtAppFormNo']."'";
            $this->db->query($sql);
        }
    }

    public function saveUnderwritingDetails($driver, $rows){

        $arr2 = array('app_score' => '', 'cpcs' => '', 'de_dupe' => '', 'cibil_vintage' => '', 'app_id' => '', 'pq_offer' => '');

        $driver->navigate()->to("https://aps.icicibank.com/ICICIWeb/Activity.los?activity=UND&currentActivity=UND&txtApplicationNo=".$rows[0]['apsNo']."&category=PERSONAL&mode=E&inBranchID=".$rows[0]['selBranch']);            

        for ($j=33; $j <= 34; $j++) {
            $row_data = $driver->findElements(WebDriverBy::xpath('/html/body/form/table['.$j.']/tbody/tr[2]/td[1]/table/tbody/tr'));
            for ($i=1; $i <= count($row_data); $i++) {
                $tmp = $driver->findElement(WebDriverBy::xpath('/html/body/form/table['.$j.']/tbody/tr[2]/td[1]/table/tbody/tr['.$i.']/td[2]'))->getText();
                $tmp2 = trim($tmp);
                if (strpos($tmp2, 'Cibil Vintage =') !== false) {
                    $tmp3 = explode('=', $tmp2);
                    $arr2['cibil_vintage'] = trim($tmp3[1]);
                    break;
                }
            }
        }

        $handle = $driver->getWindowHandle();
        $driver->executeScript("callBREScoring()");
        
        $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();

        sleep(2);

        $handles = $driver->getWindowHandles();

        foreach($handles as $h) {
            if($handle != $h) {
                $driver->switchTo()->window($h);
                $driver->close();
            }
        }

        $driver->switchTo()->window($handle);
        $driver->executeScript("showReport('SR')");

        $handles = $driver->getWindowHandles();
        $lastHandle = end($handles);
        $driver->switchTo()->window($lastHandle);

        sleep(2);

        $this->bot->waitForAjax($driver);

        $driver->wait(20,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name('scoringResultAF'))
        );

        $tmp = $driver->findElement(WebDriverBy::xpath('/html/body/form/table/tbody/tr[2]/td[2]'))->getText();
        $arr2['app_score'] = trim($tmp);

        $driver->close();
        $driver->switchTo()->window($handle);

        $driver->executeScript("showReport('DUP')");
        $handles = $driver->getWindowHandles();
        $lastHandle = end($handles);
        $driver->switchTo()->window($lastHandle);
        $this->bot->waitForAjax($driver);

        $driver->wait(20,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name('dedupeAF'))
        );

        try {
            $tmp = $driver->findElement(WebDriverBy::xpath('/html/body/form/table[2]/tbody/tr[5]/td[2]/a'))->getText();
            $arr2['cpcs'] = trim($tmp);
        } catch (Exception $e) {
            $arr2['cpcs'] = "";
        }

        $driver->close();
        $driver->switchTo()->window($handle);

        $driver->executeScript("showReport('POS')");
        $handles = $driver->getWindowHandles();
        $lastHandle = end($handles);
        $driver->switchTo()->window($lastHandle);
        $this->bot->waitForAjax($driver);

        try {
            $tmp = $driver->findElement(WebDriverBy::xpath('/html/body/table[5]/tbody/tr/td[2]/a'))->getText();
            $arr2['de_dupe'] = trim($tmp);
        } catch (Exception $e) {
            $arr2['de_dupe'] = "0";
        }
        

        if($arr2['de_dupe'] != "0"){
            $driver->findElement(WebDriverBy::xpath('/html/body/table[5]/tbody/tr/td[2]/a'))->click();
            $curr_handle = $driver->getWindowHandle();
            $handles = $driver->getWindowHandles();
            $lastHandle = end($handles);
            $driver->switchTo()->window($lastHandle);
            $this->bot->waitForAjax($driver);

            $tmp = $driver->findElement(WebDriverBy::xpath('/html/body/table[3]/tbody/tr[7]/td[2]'))->getText();
            $accNo = trim($tmp);

            $tables = ((int) $arr2['de_dupe']) + 4 ;

            for ($i=5; $i <= $tables; $i++) {
                $tmp = $driver->findElement(WebDriverBy::xpath('/html/body/table['.$i.']/tbody/tr/td/table/tbody/tr[9]/td[4]'))->getText();
                $accNo_tmp = trim($tmp);
                if (strpos($accNo_tmp, $accNo) !== false) {
                    break;
                }
            }

            try {
                $tmp = $driver->findElement(WebDriverBy::xpath('/html/body/table['.$i.']/tbody/tr/td/table/tbody/tr[2]/td[2]'))->getText();
                $arr2['app_id'] = trim($tmp);
            } catch (Exception $e) {
                $arr2['app_id'] = '';
            }

            $driver->close();
            $driver->switchTo()->window($curr_handle);
        }

        $driver->close();
        $driver->switchTo()->window($handle);

        $driver->navigate()->to("https://aps.icicibank.com/ICICIWeb/RelationAccount.los?accountNo=".$rows[0]['txtAccountNumber_1']."&hidPageToInclude=A");
        $e = $driver->findElement(WebDriverBy::name('txtPqOffer'));
        $arr2['pq_offer'] = trim($e->getAttribute('value'));
        
        $sql = "UPDATE pl_apscamdata SET isUnderwritingFetched = 1, app_score = '".$arr2['app_score']."', cpcs = '".$arr2['cpcs']."', de_dupe = '".$arr2['de_dupe']."', cibil_vintage = '".$arr2['cibil_vintage']."', app_id = '".$arr2['app_id']."', pq_offer = '".$arr2['pq_offer']."' WHERE txtAppFormNo = '".$rows[0]['txtAppFormNo']."'";
        $this->db->query($sql);
    }

    public function saveScreenshots($driver, $rows, $caseType){
        $companyName = $rows[0]['txtCompnayName'];
        $arr2 = array('mca_incorp_date' => '', 'mca_agm_date' => '', 'mca_status' => 'N', 'pan_status' => 'N', 'domain_status' => 'N', 'domain_exp_date' => '');
        $wordlist = array('pvt','PVT','ltd','LTD');
        foreach ($wordlist as &$word) {
            $word = '/\b' . preg_quote($word, '/') . '\b/';
        }
        $companyName = preg_replace($wordlist, '', $companyName);
        $companyName = trim($companyName);
        $driver->navigate()->to("http://www.mca.gov.in/mcafoportal/viewCompanyMasterData.do");
        $driver->findElement(WebDriverBy::name('imgSearchIcon'))->click();
        $this->bot->waitForAjax($driver);
        sleep(1);
        $driver->findElement(WebDriverBy::name('searchcompanyname'))->sendKeys($companyName);
        $driver->findElement(WebDriverBy::name('findcindata'))->click();
        $this->bot->waitForAjax($driver);
        sleep(1);

        $row_data = $driver->findElements(WebDriverBy::xpath('//*[@id="cinlist"]/tbody/tr'));
        $arr2['mca_status'] = 'N';
        for ($i=1; $i <= count($row_data); $i++) {
            $e = $driver->findElement(WebDriverBy::xpath('//*[@id="cinlist"]/tbody/tr['.$i.']/td[1]/a'));
            $tmp = $e->getText();
            if (strpos($tmp, $companyName) !== false) {
                $arr2['mca_status'] = 'Y';
                $e->click();
                break;
            }
        }
        if($arr2['mca_status'] == 'Y'){
            $this->bot->waitForAjax($driver);
            sleep(1);
            $driver->executeScript("document.getElementsByName('displayCaptcha')[0].setAttribute('value', 'false')");
            $driver->findElement(WebDriverBy::id('companyLLPMasterData_0'))->click();
            $this->bot->waitForAjax($driver);
            sleep(1);

            $elements = $driver->findElements(WebDriverBy::xpath('//*[@id="resultTab1"]/tbody/tr'));
            for ($i=1; $i <= count($elements); $i++) {
                $tmp = $driver->findElement(WebDriverBy::xpath('//*[@id="resultTab1"]/tbody/tr['.$i.']/td[1]'))->getText();
                if (trim($tmp) == "Date of Incorporation") {
                    $tmp2 = $driver->findElement(WebDriverBy::xpath('//*[@id="resultTab1"]/tbody/tr['.$i.']/td[2]'))->getText();
                    $arr2['mca_incorp_date'] = trim($tmp2);
                }
                if (trim($tmp) == "Date of last AGM") {
                    $tmp2 = $driver->findElement(WebDriverBy::xpath('//*[@id="resultTab1"]/tbody/tr['.$i.']/td[2]'))->getText();
                    $arr2['mca_agm_date'] = trim($tmp2);
                }
            }

            $path = APS_CIBIL.date("d-m-Y").SEPARATOR.$caseType.SEPARATOR.'VERIFIED'.SEPARATOR;
            $img = $rows[0]['txtAppFormNo'].'_mca.png';
            if(!is_dir($path)){
                mkdir($path, 0777, true);
            }
            $driver->manage()->window()->maximize();
            $driver->executeScript('window.scrollTo(0,250);');
            $driver->takeScreenshot($path.$img);
        }

        // $panNo = $rows[0]['txtPanNo'];
        // $arr2['pan_status'] = 'N';

        // if($panNo != ""){
        //     try {
        //         $driver->navigate()->to("https://onlineservices.tin.egov-nsdl.com/etaxnew/tdsnontds.jsp");
        //         $driver->executeScript("sendRequest(286)");
        //         $this->bot->waitForAjax($driver);
        //         $driver->findElement(WebDriverBy::id('PanId'))->sendKeys($panNo);
        //         $select = new WebDriverSelect($driver->findElement(WebDriverBy::id('AssessYearId')));
        //         $select->selectByIndex(1);
        //         $driver->wait()->until(WebDriverExpectedCondition::alertIsPresent());
        //         $driver->switchTo()->alert()->accept();
        //         $driver->findElement(WebDriverBy::xpath('/html/body/form[2]/div[5]/fieldset/table/tbody/tr[3]/td[2]/input'))->sendKeys($rows[0]['selDistrict_1']);
        //         $driver->findElement(WebDriverBy::xpath('/html/body/form[2]/div[5]/fieldset/table/tbody/tr[4]/td[2]/input'))->sendKeys($rows[0]['txtZip_1']);
        //         $select = new WebDriverSelect($driver->findElement(WebDriverBy::xpath('/html/body/form[2]/div[5]/fieldset/table/tbody/tr[3]/td[4]/strong/select')));
        //         $select->selectByVisibleText($rows[0]['state']);

        //         $bankName = $rows[0]['selBankName_1'];

        //         $select = new WebDriverSelect($driver->findElement(WebDriverBy::id('NetBank_Name_c')));
        //         $elements = $select->getOptions();

        //         foreach ($elements as $e) {
        //             $tmp = $e->getText();
        //             if (strpos(strtolower($bankName), strtolower($tmp)) !== false) {
        //                 $select->selectByVisibleText($tmp);
        //             }
        //         }
        //         $img = $driver->findElement(WebDriverBy::id('Captcha'));
        //         $path = APS_SCREENSHOTS.'tmp'.SEPARATOR;
        //         if (!file_exists($path)) {
        //             mkdir($path, 0777, true);
        //         }
        //         $img_path = $this->TakeScreenshot($img, $driver);
        //         echo 'Path:'.$img_path.'<br>';
        //         system("convert -density 300 -colorspace gray -modulate 120 -contrast-stretch 10%x80% -modulate 140 -gaussian-blur 1 -contrast-stretch 5%x50% +repage -negate -gaussian-blur 4 -negate -modulate 130 ".$img_path." ".$img_path);
        //         exec("convert ".$img_path." -density 300 -resize 800x250 ".$img_path);
        //         echo 'Image ://'.(new TesseractOCR($img_path))->run().'//';


        //     } catch (Exception $e) {
        //         echo '<pre>';
        //         print_r($e);
        //     }
            
        // }

        if($rows[0]['official_email_id'] != ""){
            $tmp = explode('@', $rows[0]['official_email_id']);
            $domain = $tmp[1];
            $driver->navigate()->to("https://who.is/whois/".$domain);
            $this->bot->waitForAjax($driver);

            $elements_exp = $driver->findElements(WebDriverBy::xpath('/html/body/div[3]/div[2]/div[5]/div[1]/div[5]/div/div[1]/div[1]'));

            if(count($elements_exp) > 0){
                $element_exp = $driver->findElement(WebDriverBy::xpath('/html/body/div[3]/div[2]/div[5]/div[1]/div[5]/div/div[1]/div[1]'));
                $txt = $element_exp->getText();
                if($txt == "Expires On"){
                    $arr2['domain_status'] = 'Y';
                    $element_exp_dt = $driver->findElement(WebDriverBy::xpath('/html/body/div[3]/div[2]/div[5]/div[1]/div[5]/div/div[1]/div[2]'));
                    $arr2['domain_exp_date'] = $element_exp_dt->getText();

                    $path = APS_CIBIL.date("d-m-Y").SEPARATOR.$caseType.SEPARATOR.'VERIFIED'.SEPARATOR;
                    $img = $rows[0]['txtAppFormNo'].'_domain.png';
                    if(!is_dir($path)){
                        mkdir($path, 0777, true);
                    }
                    $driver->manage()->window()->maximize();
                    $driver->executeScript('window.scrollTo(0,1200);');
                    $driver->takeScreenshot($path.$img);
                }
            }
        }
        $sql = "UPDATE pl_apscamdata SET mca_incorp_date = '".$arr2['mca_incorp_date']."', mca_agm_date = '".$arr2['mca_agm_date']."', mca_status = '".$arr2['mca_status']."', pan_status = '".$arr2['pan_status']."', domain_status = '".$arr2['domain_status']."', domain_exp_date = '".$arr2['domain_exp_date']."' WHERE txtAppFormNo = '".$rows[0]['txtAppFormNo']."'";
        $this->db->query($sql);
    }

    public function TakeScreenshot($element=null, $driver) {
        // Change the Path to your own settings
        $path = APS_SCREENSHOTS.'tmp'.SEPARATOR;
        $screenshot = $path . time() . ".png";

        if( ! (bool) $element) {
            return $screenshot;
        }

        $element_screenshot = $path . time() . ".png"; // Change the path here as well
        
        $element_width = $element->getSize()->getWidth();
        $element_height = $element->getSize()->getHeight();
        
        $element_src_x = $element->getLocationOnScreenOnceScrolledIntoView()->getX();
        $element_src_y = $element->getLocationOnScreenOnceScrolledIntoView()->getY();

        // Change the driver instance
        $driver->takeScreenshot($screenshot);
        if(! file_exists($screenshot)) {
            throw new Exception('Could not save screenshot');
        }
        
        // Create image instances
        $src = imagecreatefrompng($screenshot);
        $dest = imagecreatetruecolor($element_width, $element_height);

        // Copy
        imagecopy($dest, $src, 0, 0, (int) ceil($element_src_x), (int) ceil($element_src_y), (int) ceil($element_width), (int) ceil($element_height));
        
        imagepng($dest, $element_screenshot);
        
        // unlink($screenshot); // unlink function might be restricted in mac os x.
        
        if( ! file_exists($element_screenshot)) {
            throw new Exception('Could not save element screenshot');
        }
        
        return $element_screenshot;
    }

}
?>