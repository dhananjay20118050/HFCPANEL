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
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverContextClickAction;

class BOT_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }
    public function dbquery($userId, $trnrefno)
    {
       $sql = "SELECT a.*,b.is_pan_checked FROM hfccustdata a LEFT JOIN bot_aps_tracking b on a.TRNREFNO = b.TRNREFNO WHERE b.status in ('N','E','P') AND a.TRNREFNO = '$trnrefno' ORDER BY id";
        $rows = $this->db->query($sql)->result_array();
        return $rows;
    }
    public function logout($driver){

        $driver->switchTo()->defaultContent();
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);

        $selector = WebDriverBy::xpath('/html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a');

        $driver->wait(60, 1000)->until(WebDriverExpectedCondition::visibilityOfElementLocated($selector));

        $driver->findElement($selector)->click(); 

        $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();


    }   
    public function start($userId, $trnrefno){
        try {
            $this->closeIESessions();
        }catch (Exception $e) {
            
        }

        $rows = $this->dbquery($userId, $trnrefno);

        if(count($rows) > 0){

            $row = $rows[0];
            $capabilities = DesiredCapabilities::internetExplorer();
            $driver = RemoteWebDriver::create('http://localhost:5555/wd/hub', $capabilities, 5000);

            $localIP = getHostByName(getHostName());

            $sql = "UPDATE bot_aps_tracking SET status = 'P', ip_address = '".$localIP."', start_time = '".date("Y-m-d H:i:s")."' WHERE TRNREFNO = '".$row['TRNREFNO']."'";
            $qparent = $this->db->query($sql);

                try {
                    $this->loginCust($driver, $row,$userId);
                } catch (Exception $e) {
                    $this->addException($e, $row['TRNREFNO'], $userId, "Finacle Login for CustID Creation", $driver);
                    
                }

            if($row['is_pan_checked'] != 1){

                try {
                    $this->startfincoreProcess($driver,$userId,$row);
                } catch (Exception $e) {
                    $this->addException($e, $row['TRNREFNO'], $userId, "Cust ID Search", $driver);
                    
                }

                $sql = "UPDATE bot_aps_tracking SET is_pan_checked = 1, status = 'P', end_time = '".date("Y-m-d H:i:s")."' WHERE TRNREFNO = '".$row['TRNREFNO']."'";
                $qparent = $this->db->query($sql);

            }
            
            $rows = $this->dbquery($userId, $trnrefno);
            if(count($rows) > 0){
                $row = $rows[0];
            }
            try {
                $this->startCRMProcess($driver, $userId, $row);
            } catch (Exception $e) {
                $this->addException($e, $row['TRNREFNO'], $userId, "Cust ID Creation Error", $driver);
            }

            $rows = $this->dbquery($userId, $trnrefno);
            if(count($rows) > 0){
                $row = $rows[0];
            }

            try {
                $this->accountcreate($driver, $userId, $row);
            } catch (Exception $e) {
                $this->addException($e, $row['TRNREFNO'], $userId, "Account Creation", $driver,'finalexception');
            }
            
            $sql = "UPDATE bot_aps_tracking SET status = 'Y', end_time = '".date("Y-m-d H:i:s")."' WHERE TRNREFNO = '".$row['TRNREFNO']."'";
            $qparent = $this->db->query($sql);

            try {
            $this->logout($driver);
            } catch (Exception $e) {
                $this->addException($e, $row['TRNREFNO'], $userId, "Log Out Error", $driver);
            }
            
            $driver->quit();
            try {
                $this->closeIESessions();
            } catch (Exception $e) {
                
            }
                
        }
         
    }

    public function loginCust($driver,$row,$userid){

        $url = "https://ijprsunt7-04-ld18.icicibankltd.com:8212/SSO/ui/SSOLogin.jsp";
        $driver->get($url);
        $driver->findElement(WebDriverBy::id("moreInfoContainer"))->click();
        $driver->findElement(WebDriverBy::id("overridelink"))->click();

        $driver->switchTo()->defaultContent();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("usertxt"))
        );

        $driver->findElement(WebDriverBy::id("usertxt"))->sendKeys($this->getUserName());
        $driver->findElement(WebDriverBy::id("passtxt"))->sendKeys($this->getPassword());
        $driver->findElement(WebDriverBy::id("Submit"))->click();

        $sql = "UPDATE bot_aps_tracking SET userid = $userid WHERE TRNREFNO = '".$row['TRNREFNO']."'";
        $qparent = $this->db->query($sql);
    }
    public function startfincoreProcess($driver,$userId,$row){

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("appSelect"))
        );

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("appSelect")));
        $element->selectByValue('CoreServer');

        $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CoreServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CoreServer"));
        $driver->switchTo()->frame($frame);

        sleep(10);
        $frame = $driver->findElement(WebDriverBy::id("FINW"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("menutree"))
        );

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("menuName"))
        );

        $element = $driver->findElement(WebDriverBy::id("menuName"));
        $element->sendKeys("HOAACTD");
        $driver->executeScript('document.getElementById("menuName").value = "HOAACTD";');
        $element->sendKeys(array(WebDriverKeys::TAB));
        $driver->findElement(WebDriverBy::id("gotomenu"))->click();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("sLnk2"))
        );

        $driver->findElement(WebDriverBy::id("sLnk2"))->click();
        sleep(2);
        $driver->findElement(WebDriverBy::id("sLnk2"))->click();
       
        $handles = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handles));

        $this->checkPanStatus($driver, $row['PANGIR1'], $row['TRNREFNO'], 1);

        if(!empty($row['JH1PAN'])){
            $this->checkPanStatus($driver, $row['JH1PAN'], $row['TRNREFNO'],2);
        }

        if(!empty($row['JH2PAN'])){
            $this->checkPanStatus($driver,$row['JH2PAN'],$row['TRNREFNO'],3);
        }

        $allWindows = $driver->getWindowHandles();
        $driver->switchTo()->window(end($allWindows))->close();
        $driver->switchTo()->window($allWindows[0]);   
    }
    public function checkPanStatus($driver, $pan, $trnrefno, $cifid){

        $driver->switchTo()->defaultContent();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath("html/frameset/frame"))
        );

        $frame = $driver->findElement(WebDriverBy::xpath("html/frameset/frame"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("docNo"))
        );

        $driver->findElement(WebDriverBy::id("docNo"))->clear();
        $driver->findElement(WebDriverBy::id("docNo"))->sendKeys($pan);
        $driver->findElement(WebDriverBy::id("Ok"))->click();

        $driver->switchTo()->defaultContent();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("GetCustIdResults"))
        );

        $frame = $driver->findElement(WebDriverBy::name("GetCustIdResults"));
        $driver->switchTo()->frame($frame);

        sleep(2);

        $results = $driver->findElements(WebDriverBy::xpath("/html/body/form/span/table/tbody/tr/td/table/tbody/tr/td/table[2]/tbody/tr[2]/td[1]/font/a"));

        if(count($results) > 0){
            $custId = $results[0]->getText();

            $sql = "UPDATE hfccustdata SET cifid_$cifid = '".$custId."',is_existing_cust_$cifid ='1' WHERE TRNREFNO = '".$trnrefno."'";
            $qparent = $this->db->query($sql);
        }
    }
    public function startCRMProcess($driver,$userId,$row){
        $i=0;
        $j=0;
        if(empty($row['cifid_1'])){
            $i++;
            try {
                $this->addNewCustId($driver, $row,'cifid_1',$row['DOB'],$row['NAME'],$row['PANGIR1'],$i,$userId);
            } catch (Exception $e) {
                $this->addException($e, $row['TRNREFNO'], $userId, "ADDNEW CIFID", $driver);
            }
            
        }
        if((is_null($row['cifid_2']) || empty($row['cifid_2'])) && !empty($row['JH1PAN'])){
            $i++;
            try {
                $this->addNewCustId($driver, $row,'cifid_2','01/01/1900',$row['JH1NAME'],$row['JH1PAN'],$i,$userId);
            } catch (Exception $e) {
                $this->addException($e, $row['TRNREFNO'], $userId, "ADDNEW2 CIFID", $driver);
            }
           
        }
        if((is_null($row['cifid_3']) || empty($row['cifid_3'])) && !empty($row['JH2PAN'])){
            $i++;
            try {
                $this->addNewCustId($driver, $row,'cifid_3','01/01/1900',$row['JH2NAME'],$row['JH2PAN'],$i,$userId);
            } catch (Exception $e) {
                $this->addException($e, $row['TRNREFNO'], $userId, "ADDNEW3 CIFID", $driver);
            }
           
        }

        if(!empty($row['cifid_1']) && $row['is_existing_cust_1'] == 1 && $row['edit_cifid_1'] != 1){
            $j++;
            try {
                $this->editcust($driver,$row,'cifid_1',$row['DOB'],$row['NAME'],$row['PANGIR1'],$userId,$j);
            }
            catch (Exception $e) {
               $this->addException($e, $row['TRNREFNO'], $userId, "EDIT CIFID1", $driver);
           }
        }
        if(!empty($row['cifid_2']) && $row['is_existing_cust_2'] == 1 && $row['edit_cifid_2'] != 1){
            $j++;
           try {
               $this->editcust($driver, $row,'cifid_2','01/01/1900',$row['JH1NAME'],$row['JH1PAN'],$userId,$j);
            }
            catch (Exception $e) {
                 $this->addException($e, $row['TRNREFNO'], $userId, "EDIT CIFID2", $driver);
            }
        }
        if(!empty($row['cifid_3']) && $row['is_existing_cust_3'] == 1 && $row['edit_cifid_3'] != 1){
            $j++;
              try {
                 $this->editcust($driver, $row,'cifid_3','01/01/1900',$row['JH2NAME'],$row['JH2PAN'],$userId,$j);
            }
            catch (Exception $e) {
                 $this->addException($e, $row['TRNREFNO'], $userId, "EDIT CIFID3", $driver);
            }
        }
    }
    public function addNewCustId($driver, $row, $cfid, $dob, $name, $pan,$num,$userid){
       if($num == '1'){
            $driver->switchTo()->defaultContent();
            $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
            );

            $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
            $driver->switchTo()->frame($frame);

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("appSelect"))
            );

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("appSelect")));
            $element->selectByValue('CRMServer');

            try {
                $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
                $driver->switchTo()->alert()->accept();
            } catch (Exception $e) {
                
            }

            $frame = $driver->findElement(WebDriverBy::name("CRMServer"));
            $driver->switchTo()->frame($frame);

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("ScreensTOCFrm"))
            );

            $frame = $driver->findElement(WebDriverBy::name("ScreensTOCFrm"));
            $driver->switchTo()->frame($frame);

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("Functionmain"))
            );

            $frame = $driver->findElement(WebDriverBy::name("Functionmain"));
            $driver->switchTo()->frame($frame);


            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("screen1"))
            );

            $driver->findElement(WebDriverBy::id("screen1"))->click();


            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("1504"))
            );

            $frame = $driver->findElement(WebDriverBy::name("1504"));
            $driver->switchTo()->frame($frame);


            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("view2"))
            );

            $driver->findElement(WebDriverBy::id("view2"))->click();


            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("subview20"))
            );
            $driver->findElement(WebDriverBy::id("subview20"))->click(); 
        }

        $driver->switchTo()->defaultContent();

        try {
            $this->switchframeCust($driver);
        } catch (Exception $e) {
            $this->addException($e, $row['TRNREFNO'], $userid, "Frame Not Found", $driver);
        }
        
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("IFrmtab0"))
        );

        $frame = $driver->findElement(WebDriverBy::name("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("formDispFrame"))
        );

        $frame = $driver->findElement(WebDriverBy::id("formDispFrame"));
        $driver->switchTo()->frame($frame);

        try {
            $this->generaltabentries($driver, $row, $cfid, $dob, $name, $pan);
        } catch (Exception $e) {
            $this->addException($e, $row['TRNREFNO'], $userid, "General tab Entry Error", $driver);
        }

        //Demographic

        $handle = $driver->getWindowHandles();

        $driver->switchTo()->window($handle[0]);

        $driver->switchTo()->defaultContent();

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);
        
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CRMServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CRMServer"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("DataAreaFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("DataAreaFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tempFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("tempFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tabViewFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("tabViewFrm"));
        $driver->switchTo()->frame($frame);


        //DEMOGRAPHIC TAB
        $driver->findElement(WebDriverBy::id("tab1"))->click();
        $driver->switchTo()->defaultContent();

        try {
            $this->switchframeCust($driver);
        } catch (Exception $e) {
            $this->addException($e, $row['TRNREFNO'], $userid, "Frame Not Found", $driver);
        }

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("IFrmtab1"))
        );

        $frame = $driver->findElement(WebDriverBy::name("IFrmtab1"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("formDispFrame"))
        );

        $frame = $driver->findElement(WebDriverBy::id("formDispFrame"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("DemographicModBO.Nationality"))
        );

        $element = $driver->findElement(WebDriverBy::name("DemographicModBO.Nationality"));
        $element->sendKeys('INDIAN');

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("Cat_DemographicModBO.Nationality"))
        );

        $element = $driver->findElement(WebDriverBy::name("Cat_DemographicModBO.Nationality"));
        $element->sendKeys('INDIAN');

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("DemographicModBO.Marital_Status")));
        $element->selectByValue('OTHER');

        $driver->findElement(WebDriverBy::id("tab_tpageEDet"))->click();
        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("DemographicModBO.Employment_Status")));
        $element->selectByValue('Other');


        $driver->findElement(WebDriverBy::id("tab_tpageIExp"))->click();
        $element = $driver->findElement(WebDriverBy::name("3_DemographicBO.Annual_Salary_Income"));
        $element->sendKeys('500000');

        $driver->switchTo()->defaultContent();

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);
        
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CRMServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CRMServer"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("DataAreaFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("DataAreaFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tempFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("tempFrm"));
        $driver->switchTo()->frame($frame);

        $frame = $driver->findElement(WebDriverBy::name("buttonFrm"));
        $driver->switchTo()->frame($frame);

        $driver->findElement(WebDriverBy::id("submitBut"))->click();

        $driver->wait(120,1000)->until(WebDriverExpectedCondition::alertIsPresent());

        $alert = $driver->switchTo()->alert();
        $custdata = $alert->getText();
        $custexp = explode(': ',$custdata);
        $custId = end($custexp);

        $this->saveCustID($custId,$row['TRNREFNO'],$cfid);
        $alert->accept();

        $handle = $driver->getWindowHandles();

        $driver->switchTo()->window(end($handle));

        $driver->switchTo()->defaultContent();

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tempFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("tempFrm"));
        $driver->switchTo()->frame($frame);


        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("buttonFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("buttonFrm"));
        $driver->switchTo()->frame($frame);

        $driver->findElement(WebDriverBy::id("saveBut"))->click();

        $driver->wait(120,1000)->until(WebDriverExpectedCondition::alertIsPresent());

        $driver->switchTo()->alert()->accept();

        sleep(5);
        $handle = $driver->getWindowHandles();
        $driver->switchTo()->window($handle[0]);
        $driver->switchTo()->defaultContent();

    }

    public function switchframeCust($driver){

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);

        
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CRMServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CRMServer"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("DataAreaFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("DataAreaFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tempFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("tempFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tabContentFrm"))
        );
        $frame = $driver->findElement(WebDriverBy::name("tabContentFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("userArea"))
        );
        $frame = $driver->findElement(WebDriverBy::id("userArea"));
        $driver->switchTo()->frame($frame);

    }
    public function generaltabentries($driver, $row, $cfid, $dob, $name, $pan){
       
        if(!empty($name)){
            $exp = explode(' ',trim($name));

            $tnamecount = count($exp);

            if($tnamecount == 3){
                $salutaion = trim($exp[0]);
                $firstname = trim($exp[1]);
                $middlename = '';
                $lastname = trim($exp[2]);

            }
            elseif($tnamecount == 4){
                $salutaion = trim($exp[0]);
                $firstname = trim($exp[1]);
                $middlename = trim($exp[2]);
                $lastname = trim($exp[3]);

            }elseif($tnamecount == 5){

                $salutaion = trim($exp[0]);
                $firstname = trim($exp[1]).' '.trim($exp[2]);
                $middlename = trim($exp[3]);
                $lastname = trim($exp[4]);

            }

            if($salutaion =="MR" || $salutaion =="MR."){
                $salutaion = "MR";
                $gender = 'M';
            }else{
                $salutaion = "MRS";
                $gender = 'F';
            }

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountModBO.Gender"))
            );

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountModBO.Gender")));
            $element->selectByValue($gender);

            $element = $driver->findElement(WebDriverBy::name("AccountModBO.Salutation_code"));
            $element->sendKeys($salutaion);
            sleep(1);
            $element->sendKeys(array(WebDriverKeys::TAB));

            $element = $driver->findElement(WebDriverBy::name("AccountBO.Cust_First_Name"));
            $element->sendKeys($firstname);

            $element = $driver->findElement(WebDriverBy::name("AccountBO.Cust_Middle_Name"));
            $element->sendKeys($middlename);

            $element = $driver->findElement(WebDriverBy::name("AccountBO.Cust_Last_Name"));
            $element->sendKeys($lastname);

            $element = $driver->findElement(WebDriverBy::name("AccountBO.short_name"));
            $element->sendKeys($firstname);

            $element = $driver->findElement(WebDriverBy::name("AccountBO.Name"));
            $element->sendKeys($name);

            if(!empty($dob)){
                $element = $driver->findElement(WebDriverBy::name("3_AccountBO.Cust_DOB"));
                $element->sendKeys($dob);
            }
            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountModBO.CustomerNREFlg")));
            $element->selectByValue('N');

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("Cat_AccountBO.Constitution_Code"))
            );

            $element = $driver->findElement(WebDriverBy::name("Cat_AccountBO.Constitution_Code"));
            $element->sendKeys('RTL-INDIVIDUAL');

            $element = $driver->findElement(WebDriverBy::name("AccountModBO.Tds_tbl"));
            $element->sendKeys('TDSI');
            sleep(1);
            $element->sendKeys(array(WebDriverKeys::TAB));
     
            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountModBO.PurgeFlag")));
            $element->selectByValue('N');

            $driver->findElement(WebDriverBy::id("rownative2"))->click();

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountModBO.Introd_Status")));
            $element->selectByValue('PAN');

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountModBO.IntroducerSalutation")));
            $element->selectByValue($salutaion);

            $element = $driver->findElement(WebDriverBy::name("AccountBO.IntroducerName"));
            $element->sendKeys($name);

        }
        $driver->switchTo()->defaultContent();    

        //Address Details
        try {
            $this->switchframeCust($driver);
        } catch (Exception $e) {
            $this->addException($e, $row['TRNREFNO'], $userid, "Frame Not Found", $driver);
        }

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("IFrmtab0"))
        );

        $frame = $driver->findElement(WebDriverBy::name("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("formDispFrame"))
        );

        $frame = $driver->findElement(WebDriverBy::id("formDispFrame"));
        $driver->switchTo()->frame($frame);
        sleep(5);
        $driver->findElement(WebDriverBy::id("tab_tpageCont3"))->click();

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.Address.preferredAddress")));
        $element->selectByValue('Mailing');
        sleep(5);
        $driver->findElement(WebDriverBy::name("Add Address Details"))->click();
        sleep(2);
        $handle = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handle));
        sleep(2);
        $driver->switchTo()->defaultContent();
        $driver->switchTo()->defaultContent();  

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::linkText("More information"))
        );
        $driver->findElement(WebDriverBy::linkText("More information"))->click();

        $driver->findElement(WebDriverBy::linkText("More information"))->click();

        $driver->findElement(WebDriverBy::id("overridelink"))->click();
        sleep(2);
        $driver->switchTo()->defaultContent();
 
        if(!empty($row['ADD1'])){

            $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.Address.PreferredFormat"))
            );

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.Address.PreferredFormat")));
            $element->selectByValue('FREE_TEXT_FORMAT');

            $driver->manage()->timeouts()->implicitlyWait = 5;

            $driver->wait(10)->until(WebDriverExpectedCondition::alertIsPresent());
            $driver->switchTo()->alert()->accept();

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.Address.addressCategory"))
            );
            sleep(3);
            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.Address.addressCategory")));
     
            $element->selectByValue('Mailing');

            $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.Address.address_Line1"))
             );
            $element = $driver->findElement(WebDriverBy::name("AccountBO.Address.address_Line1"));
            $element->sendKeys($row['ADD1']);
        }
        if(!empty($row['ADD2']) && !empty($row['ADD3'])){
            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.Address.address_Line2"))
            );
            $element = $driver->findElement(WebDriverBy::name("AccountBO.Address.address_Line2"));
            $element->sendKeys($row['ADD2'].' '.$row['ADD3']);

            $element = $driver->findElement(WebDriverBy::name("Cat_AccountBO.Address.city"));
            $element->sendKeys($row['CITY']);

            $element = $driver->findElement(WebDriverBy::name("Cat_AccountBO.Address.state"));
            $element->sendKeys($row['STATE']);

            $element = $driver->findElement(WebDriverBy::name("AccountBO.Address.country"));
            $element->sendKeys('IN');

            $element = $driver->findElement(WebDriverBy::name("Cat_AccountBO.Address.country"));
            $element->sendKeys('INDIA');

            $element = $driver->findElement(WebDriverBy::name("AccountBO.Address.zip"));
            $element->sendKeys($row['PIN']);

            $element = $driver->findElement(WebDriverBy::name("3_AccountBO.Address.Start_Date"))->clear();
            $element->sendKeys('04/07/2019');

        }
        $driver->findElement(WebDriverBy::name("Save"))->click();
        $driver->switchTo()->window($handle[0]);

        //PHONE

        $driver->switchTo()->defaultContent();

        try {
            $this->switchframeCust($driver);
        } catch (Exception $e) {
            $this->addException($e, $row['TRNREFNO'], $userid, "Frame Not Found", $driver);
        }

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("IFrmtab0"))
        );

        $frame = $driver->findElement(WebDriverBy::name("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("formDispFrame"))
        );

        $frame = $driver->findElement(WebDriverBy::id("formDispFrame"));
        $driver->switchTo()->frame($frame);

        $driver->findElement(WebDriverBy::id("fnttpagePhone"))->click();

        
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.PhoneEmail.PhoneEmailType"))
        );
        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneEmailType")));
        $element->selectByValue('CELLPH');

        
        $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.PhoneEmail.PhoneEmailType1"))
            );

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneEmailType1")));
        $element->selectByValue('REGEML');

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("Add Phone and E-mail"))
        );
        $driver->findElement(WebDriverBy::name("Add Phone and E-mail"))->click();
        sleep(2);
        $handle = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handle));
        sleep(3);
        $driver->switchTo()->defaultContent();
         if(!empty($row['MOBILENO'])){

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.PhoneEmail.PhoneEmailType"))
            );

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneEmailType")));
            $element->selectByValue('CELLPH');

            $element = $driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneNo.cntrycode"));
            $element->sendKeys('91');
            $element = $driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneNo.localcode"));
            $element->sendKeys($row['MOBILENO']);  
        }
        
        $driver->findElement(WebDriverBy::name("Save"))->click();
        
        //Email Add
        $driver->switchTo()->window($handle[0]);
        $driver->switchTo()->defaultContent();

        try {
            $this->switchframeCust($driver);
        } catch (Exception $e) {
            $this->addException($e, $row['TRNREFNO'], $userid, "Start CUST ID Creation", $driver);
        }

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("IFrmtab0"))
        );

        $frame = $driver->findElement(WebDriverBy::name("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("formDispFrame"))
        );

        $frame = $driver->findElement(WebDriverBy::id("formDispFrame"));
        $driver->switchTo()->frame($frame);

       //start mail
        $driver->findElement(WebDriverBy::name("Add Phone and E-mail"))->click();
        $handle = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handle));
        sleep(5);
        $driver->switchTo()->defaultContent();
        $driver->switchTo()->defaultContent();
        if(!empty($row['EMAILID'])){
            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.PhoneEmail.PhoneOrEmail"))
            );

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneOrEmail")));
            $element->selectByValue('EMAIL');

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.PhoneEmail.PhoneEmailType1"))
            );

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneEmailType1")));
            $element->selectByValue('REGEML');

             $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.PhoneEmail.Email"))
            );

            $element = $driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.Email"));
            $element->sendKeys($row['EMAILID']);
            
        }        
        $driver->findElement(WebDriverBy::name("Save"))->click();
        sleep(2);
        $driver->switchTo()->window($handle[0]);
        $driver->switchTo()->defaultContent();

        try {
            $this->switchframeCust($driver);
        } catch (Exception $e) {
            $this->addException($e, $row['TRNREFNO'], $userid, "Start CUST ID Creation", $driver);
        }

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("IFrmtab0"))
        );

        $frame = $driver->findElement(WebDriverBy::name("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("formDispFrame"))
        );

        $frame = $driver->findElement(WebDriverBy::id("formDispFrame"));
        $driver->switchTo()->frame($frame);

        //IDENTIFICATION DETAILS

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("tab_tpageCont5"))
        );

        $driver->findElement(WebDriverBy::id("tab_tpageCont5"))->click();

        if(!empty($pan)){
            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AddIdentificationDetails"))
            );
            $driver->findElement(WebDriverBy::name("AddIdentificationDetails"))->click();

            $handle = $driver->getWindowHandles();

            $driver->switchTo()->window(end($handle));

            sleep(4);

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("EntityDocumentBO.DocTypeCode"))
            );

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("EntityDocumentBO.DocTypeCode")));
            $element->selectByValue('IDENTIFICATION');

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("EntityDocumentBO.DocCode")));
            $element->selectByValue('PANGAR');

            $element = $driver->findElement(WebDriverBy::name("EntityDocumentBO.ReferenceNumber"));
            $element->sendKeys($pan);
        }

        $driver->findElement(WebDriverBy::name("SAVE"))->click();

        sleep(2);
        //CURRENCY TAB

        $handle = $driver->getWindowHandles();

        $driver->switchTo()->window($handle[0]);
         
        $driver->switchTo()->defaultContent();

        try {
            $this->switchframeCust($driver);
        } catch (Exception $e) {
            $this->addException($e, $row['TRNREFNO'], $userid, "Frame Not Found", $driver);
        }

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("IFrmtab0"))
        );

        $frame = $driver->findElement(WebDriverBy::name("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("formDispFrame"))
        );

        $frame = $driver->findElement(WebDriverBy::id("formDispFrame"));
        $driver->switchTo()->frame($frame);

        $css = "input[name='radio1']";
        $driver->findElement(WebDriverBy::cssSelector($css))->click();        
        sleep(2);
        $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();

        $driver->findElement(WebDriverBy::id("tab_tpageCont6"))->click();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("ADD_CURRENCYDET"))
        );

        $driver->findElement(WebDriverBy::name("ADD_CURRENCYDET"))->click();
        sleep(1);
        $handle = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handle));    
        sleep(2);
        $driver->switchTo()->defaultContent(); 

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("PsychographicBO.MiscellaneousInfo.strText10"))
        );
  
        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("PsychographicBO.MiscellaneousInfo.strText10")));
        $element->selectByValue('INR');

        $driver->findElement(WebDriverBy::name("SAVE"))->click();
    }
    public function editcust($driver,$row,$cfid, $dob, $name, $pan,$userid,$j){
        if($j==1){
            $driver->switchTo()->defaultContent();
            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
            );

            $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
            $driver->switchTo()->frame($frame);

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("appSelect"))
            );

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("appSelect")));
            $element->selectByValue('CRMServer');

            $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
            $driver->switchTo()->alert()->accept();

            $frame = $driver->findElement(WebDriverBy::name("CRMServer"));
            $driver->switchTo()->frame($frame);

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("ScreensTOCFrm"))
            );

            $frame = $driver->findElement(WebDriverBy::name("ScreensTOCFrm"));
            $driver->switchTo()->frame($frame);

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("Functionmain"))
            );

            $frame = $driver->findElement(WebDriverBy::name("Functionmain"));
            $driver->switchTo()->frame($frame);


            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("screen1"))
            );

            $driver->findElement(WebDriverBy::id("screen1"))->click();


            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("1504"))
            );

            $frame = $driver->findElement(WebDriverBy::name("1504"));
            $driver->switchTo()->frame($frame);

             $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("view0"))
            );

            $driver->findElement(WebDriverBy::id("view0"))->click();

        }

        $driver->switchTo()->defaultContent();
        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);

        
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CRMServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CRMServer"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("DataAreaFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("DataAreaFrm"));
        $driver->switchTo()->frame($frame);

        $frame = $driver->findElement(WebDriverBy::xpath("html/frameset/frame"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tempFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("tempFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tabContentFrm"))
        );
        $frame = $driver->findElement(WebDriverBy::name("tabContentFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("userArea"))
        );
        $frame = $driver->findElement(WebDriverBy::id("userArea"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("IFrmtab0"))
        );
        $frame = $driver->findElement(WebDriverBy::id("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("FilterForm"))
        );
        $frame = $driver->findElement(WebDriverBy::id("FilterForm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("userArea"))
        );
        $frame = $driver->findElement(WebDriverBy::id("userArea"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("FilterParam1"))
        );
        $element = $driver->findElement(WebDriverBy::name("FilterParam1"))->clear();
        $element->sendKeys($row[$cfid]);

        $driver->switchTo()->defaultContent();

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);

        
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CRMServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CRMServer"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("DataAreaFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("DataAreaFrm"));
        $driver->switchTo()->frame($frame);

        $frame = $driver->findElement(WebDriverBy::xpath("html/frameset/frame"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tempFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("tempFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tabContentFrm"))
        );
        $frame = $driver->findElement(WebDriverBy::name("tabContentFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("userArea"))
        );
        $frame = $driver->findElement(WebDriverBy::id("userArea"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("IFrmtab0"))
        );
        $frame = $driver->findElement(WebDriverBy::id("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("FilterForm"))
        );
        $frame = $driver->findElement(WebDriverBy::id("FilterForm"));
        $driver->switchTo()->frame($frame);

        $driver->findElement(WebDriverBy::name("submitBut"))->click();

        $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();

        $driver->switchTo()->defaultContent();

        try {
            $this->swithtoframeedit($driver);
        }
        catch (Exception $e) {
           $this->addException($e, $row['TRNREFNO'], $userid, "EDIT $cfid",$driver);
        }

       /* $element = $driver->findElement(WebDriverBy::cssSelector("a[href='javascript:void populateCifEntityDetails('Main','Account',AccountId,ScreenName,viewnameScreenID);']"));
     
        $driver->action()->moveToElement($element)->perform();

        $element->click();
        sleep(2);
        $allWindows = $driver->getWindowHandles();
        $driver->switchTo()->window(end($allWindows));
        $driverclose = $driver->switchTo()->window(end($allWindows));
        $driver->switchTo()->window(end($allWindows));
        sleep(2);

        $driverclose->close();

        $driver->switchTo()->window($allWindows[0]);

        $driver->switchTo()->defaultContent();

        $this->swithtoframeedit($driver);*/

        
        $selector = WebDriverBy::xpath('//*[@id="RecordSet"]/tbody/tr[3]/td[1]');
        $driver->wait(60, 1000)->until(WebDriverExpectedCondition::visibilityOfElementLocated($selector));
        $driver->findElement($selector)->click();

        $script = 'document.getElementById("ie5submenu3").style.visibility = "visible";';
        $driver->executeScript($script);

        $driver->findElement(WebDriverBy::id("suboptions5"))->click();
        
        /*$alertchk = $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
        $alert = $driver->switchTo()->alert();
        $custdata = $alert->getText();
        $custexp = explode(' ',$custdata);
        $txt = array_shift($custexp);

        if($txt == 'has modified this entity and it is in Draft status.'){
            $alert = $driver->switchTo()->alert();
            $driver->switchTo()->alert()->accept();
        }*/
        sleep(2);
        $allWindows = $driver->getWindowHandles();
        $driver->switchTo()->window(end($allWindows));
        $driver->switchTo()->defaultContent();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::linkText("More information"))
        );
        $driver->findElement(WebDriverBy::linkText("More information"))->click();

        $driver->findElement(WebDriverBy::id("overridelink"))->click();

        $driver->switchTo()->defaultContent();

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tempFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("tempFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tabContentFrm"))
        );
        $frame = $driver->findElement(WebDriverBy::name("tabContentFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("userArea"))
        );
        $frame = $driver->findElement(WebDriverBy::id("userArea"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("IFrmtab0"))
        );

        $frame = $driver->findElement(WebDriverBy::name("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("formDispFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::id("formDispFrame"));
        $driver->switchTo()->frame($frame);

        try {
            $this->editgeneralentry($driver, $row, $cfid, $dob, $name, $pan,$userid);
        }
        catch (Exception $e) {
           $this->addException($e, $row['TRNREFNO'], $userid, "EDIT $cfid",$driver);
       }

    }
    public function swithtoframeedit($driver){
        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);

        
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CRMServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CRMServer"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("DataAreaFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("DataAreaFrm"));
        $driver->switchTo()->frame($frame);

        $frame = $driver->findElement(WebDriverBy::xpath("html/frameset/frame"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("tempFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::id("tempFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tabContentFrm"))
        );
        $frame = $driver->findElement(WebDriverBy::name("tabContentFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("userArea"))
        );
        $frame = $driver->findElement(WebDriverBy::id("userArea"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("IFrmtab0"))
        );
        $frame = $driver->findElement(WebDriverBy::id("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("primaryUserArea"))
        );
        $frame = $driver->findElement(WebDriverBy::name("primaryUserArea"));
        $driver->switchTo()->frame($frame);
    }
    public function swithtoframeeditnext($driver){

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tempFrm"))
        );
        $frame = $driver->findElement(WebDriverBy::name("tempFrm"));
        $driver->switchTo()->frame($frame);
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tabContentFrm"))
        );
        $frame = $driver->findElement(WebDriverBy::name("tabContentFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("userArea"))
        );
        $frame = $driver->findElement(WebDriverBy::id("userArea"));
        $driver->switchTo()->frame($frame);

        $frame = $driver->findElement(WebDriverBy::name("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("formDispFrame"))
        );

        $frame = $driver->findElement(WebDriverBy::id("formDispFrame"));
        $driver->switchTo()->frame($frame);
    }
    public function editgeneralentry($driver, $row, $cfid, $dob, $name, $pan){

        if(!empty($name)){
            $exp = explode(' ',trim($name));

            $tnamecount = count($exp);

            if($tnamecount == 3){
                $salutaion = trim($exp[0]);
                $firstname = trim($exp[1]);
                $middlename = '';
                $lastname = trim($exp[2]);

            }
            elseif($tnamecount == 4){
                $salutaion = trim($exp[0]);
                $firstname = trim($exp[1]);
                $middlename = trim($exp[2]);
                $lastname = trim($exp[3]);

            }elseif($tnamecount == 5){

                $salutaion = trim($exp[0]);
                $firstname = trim($exp[1]).' '.trim($exp[2]);
                $middlename = trim($exp[3]);
                $lastname = trim($exp[4]);

            }

            if($salutaion =="MR" || $salutaion =="MR."){
                $salutaion = "MR";
                $gender = 'M';
            }else{
                $salutaion = "MRS";
                $gender = 'F';
            }

            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.Gender"))
            );

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.Gender")));
            $element->selectByValue($gender);

            $element = $driver->findElement(WebDriverBy::name("AccountBO.Salutation_code"))->clear();

            $element->sendKeys($salutaion);
            sleep(1);
            $element->sendKeys(array(WebDriverKeys::TAB));

            $element = $driver->findElement(WebDriverBy::name("AccountBO.Cust_First_Name"))->clear();
            $element->sendKeys($firstname);

            $element = $driver->findElement(WebDriverBy::name("AccountBO.Cust_Middle_Name"))->clear();
            $element->sendKeys($middlename);

            $element = $driver->findElement(WebDriverBy::name("AccountBO.Cust_Last_Name"))->clear();
            $element->sendKeys($lastname);

            $element = $driver->findElement(WebDriverBy::name("AccountBO.short_name"))->clear();
            $element->sendKeys($firstname);

            if(!empty($dob)){
                $element = $driver->findElement(WebDriverBy::name("3_AccountBO.Cust_DOB"))->clear();
                $element->sendKeys($dob);
            }

            $driver->findElement(WebDriverBy::id("rownative2"))->click();

            $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.IntroducerSalutation")));
            $element->selectByValue($salutaion);

            $element = $driver->findElement(WebDriverBy::name("AccountBO.IntroducerName"))->clear();
            $element->sendKeys($name);
     
        }
        
        //Address Details
        $driver->switchTo()->defaultContent(); 

        try {
            $this->swithtoframeeditnext($driver);
        }
        catch (Exception $e) {
           $this->addException($e, $row['TRNREFNO'], $userid, "EDIT $cfid",$driver);
        }

        $driver->findElement(WebDriverBy::id("tab_tpageCont3"))->click();
        $selector = WebDriverBy::xpath('//*[@id="RecordSet"]/tbody/tr[3]/td[7]/input');
        $driver->wait(60, 1000)->until(WebDriverExpectedCondition::visibilityOfElementLocated($selector));
        $driver->findElement($selector)->click();
        sleep(3);
        $handle = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handle));
        $driver->switchTo()->defaultContent(); 
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::linkText("More information"))
        );
        $driver->findElement(WebDriverBy::linkText("More information"))->click();
        $driver->findElement(WebDriverBy::id("overridelink"))->click();


        if(!empty($row['ADD1'])){

            $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.Address.address_Line1"))
             );
            $element = $driver->findElement(WebDriverBy::name("AccountBO.Address.address_Line1"))->clear();
            $element->sendKeys($row['ADD1']);
        }

        if(!empty($row['ADD2']) && !empty($row['ADD3'])){
            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.Address.address_Line2"))
            );
            $element = $driver->findElement(WebDriverBy::name("AccountBO.Address.address_Line2"))->clear();
            $element->sendKeys($row['ADD2'].' '.$row['ADD3']);

            /*$element = $driver->findElement(WebDriverBy::name("Cat_AccountBO.Address.city"))->clear();
            $element->sendKeys($row['CITY']);

            $element = $driver->findElement(WebDriverBy::name("Cat_AccountBO.Address.state"))->clear();
            $element->sendKeys($row['STATE']);

            $element = $driver->findElement(WebDriverBy::name("AccountBO.Address.zip"))->clear();
            $element->sendKeys($row['PIN']);*/

        }

        $driver->findElement(WebDriverBy::name("Save"))->click();

        $driver->switchTo()->window($handle[1]);

        //PHONE

        $driver->switchTo()->defaultContent();
        try {
            $this->swithtoframeeditnext($driver);
        }
        catch (Exception $e) {
           $this->addException($e, $row['TRNREFNO'], $userid, "EDIT $cfid",$driver);
        }
        $driver->findElement(WebDriverBy::id("fnttpagePhone"))->click();

        $selector = WebDriverBy::xpath('//*[@id="PhoneEmailRecordSet"]/tbody/tr[3]/td[7]/input');
        $driver->wait(20, 1000)->until(WebDriverExpectedCondition::visibilityOfElementLocated($selector));
        $driver->findElement($selector)->click();
        sleep(2);
        $handle = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handle));

         if(!empty($row['MOBILENO'])){
            sleep(3);
            $element = $driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneNo.cntrycode"))->clear();
            $element->sendKeys('91');
            $element = $driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneNo.localcode"))->clear();
            $element->sendKeys($row['MOBILENO']);  
        }
        
        $driver->findElement(WebDriverBy::name("Save"))->click();
        
        //Email Add
        $driver->switchTo()->window($handle[1]);
        $driver->switchTo()->defaultContent();
        try {
            $this->swithtoframeeditnext($driver);
        }
        catch (Exception $e) {
           $this->addException($e, $row['TRNREFNO'], $userid, "EDIT $cfid",$driver);
        }

        $selector = WebDriverBy::xpath('//*[@id="PhoneEmailRecordSet"]/tbody/tr[4]/td[7]/input');
        $driver->wait(20, 1000)->until(WebDriverExpectedCondition::visibilityOfElementLocated($selector));
        $driver->findElement($selector)->click();

        sleep(2);
        $handle = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handle));
        
        if(!empty($row['EMAILID'])){
             sleep(5);
             $driver->switchTo()->defaultContent();
             $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.PhoneEmail.Email"))
            );

            $element = $driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.Email"))->clear();
            $element->sendKeys($row['EMAILID']);
            
        }        
        sleep(2);
        $driver->findElement(WebDriverBy::name("Save"))->click();
        $driver->switchTo()->window($handle[1]);
        $driver->switchTo()->defaultContent();
        try {
            $this->swithtoframeeditnext($driver);
        }
        catch (Exception $e) {
           $this->addException($e, $row['TRNREFNO'], $userid, "EDIT $cfid",$driver);
        }

        //IDENTIFICATION DETAILS
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("tab_tpageCont5"))
        );

        $driver->findElement(WebDriverBy::id("tab_tpageCont5"))->click();
        sleep(2);
        /*if(!empty($pan)){
            $selector = WebDriverBy::xpath('//*[@id="EDocRecordSet"]/tbody/tr[3]/td[8]/input');
            $driver->wait(20, 1000)->until(WebDriverExpectedCondition::visibilityOfElementLocated($selector));
            $driver->findElement($selector)->click();

            sleep(2);
            $handle = $driver->getWindowHandles();
            $driver->switchTo()->window(end($handle));
            
            $element = $driver->findElement(WebDriverBy::name("EntityDocumentBO.ReferenceNumber"))->clear();
            $element->sendKeys($pan);
        }*/

        $driver->findElement(WebDriverBy::name("SAVE"))->click();

        //CURRENCY TAB

        $handle = $driver->getWindowHandles();

        $driver->switchTo()->window($handle[1]);
         
        $driver->switchTo()->defaultContent();

        try {
            $this->swithtoframeeditnext($driver);
        }
        catch (Exception $e) {
           $this->addException($e, $row['TRNREFNO'], $userid, "EDIT $cfid",$driver);
        }

        $driver->findElement(WebDriverBy::id("tab_tpageCont6"))->click();

        $selector = WebDriverBy::xpath('//*[@id="CurrencyDetRecordSet"]/tbody/tr[3]/td[7]/input');
        $driver->wait(20, 1000)->until(WebDriverExpectedCondition::visibilityOfElementLocated($selector));
        $driver->findElement($selector)->click();

        sleep(2);
        $handle = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handle));
        //$driver->switchTo()->defaultContent(); 
        sleep(2);
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("PsychographicBO.MiscellaneousInfo.strText10"))
        );
  

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("PsychographicBO.MiscellaneousInfo.strText10")));
        $element->selectByValue('INR');

        $driver->findElement(WebDriverBy::name("SAVE"))->click();

        sleep(2);
        $driver->switchTo()->window($handle[1]);
        $driver->switchTo()->defaultContent();
        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tempFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("tempFrm"));
        $driver->switchTo()->frame($frame);

        $frame = $driver->findElement(WebDriverBy::name("buttonFrm"));
        $driver->switchTo()->frame($frame);

        $driver->findElement(WebDriverBy::id("saveBut"))->click();

        $driver->wait(120,1000)->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();

        $this->editCustID($trnrefno,$cfid);

    }
    public function accountcreate($driver,$userId,$row){
        if(!empty($row['cifid_1'])){
            try {
                $this->accountcreation($driver, $row,'cifid_1');
            } catch (Exception $e) {
                $this->addException($e, $row['TRNREFNO'], $userId, "Primary Account Creation", $driver);
            }
        }
    }

    public function accountcreation($driver,$row,$userId){

        $driver->switchTo()->defaultContent();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
         );

        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("appSelect"))
        );

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("appSelect")));
        $element->selectByValue('CoreServer');

        $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CoreServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CoreServer"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("FINW"))
        );

        $frame = $driver->findElement(WebDriverBy::id("FINW"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("menutree"))
        );
        
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("menuName"))
        );

        $element = $driver->findElement(WebDriverBy::id("menuName"));
        $element->sendKeys("HOAACTD");
        $driver->executeScript('document.getElementById("menuName").value = "HOAACTD";');
        $element->sendKeys(array(WebDriverKeys::TAB));
        $driver->findElement(WebDriverBy::id("gotomenu"))->click();
        sleep(5);
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tdacop.cifId"))
        );

        $driver->findElement(WebDriverBy::name("tdacop.cifId"))->sendKeys($row['cifid_1']);
       // $driver->findElement(WebDriverBy::name("tdacop.cifId"))->sendKeys('661040053');

        $Ctype=$row['TYPE'];

        $x =$row['DOB'];
        $dobexp = explode('/', $x);

        $day = $dobexp[0];
        $month =$dobexp[1];
        $year = $dobexp[2];

        $from = new DateTime($year.'-'.$month.'-'.$day);
        $to   = new DateTime('today');
        $age=$from->diff($to)->y;
        
        $status=$row['STATUS'];

        if(($Ctype=='C') && ($age < '60') && ($status=='R')){
            $schmcode='CEDEP';
            $ledgercode='15050';
        }
        elseif(($Ctype=='C') && ($age >= '60') && ($status=='R')){
            $schmcode='SRCED';
            $ledgercode='15050';
        }
        elseif(($Ctype=='Y') && ($age < '60') && ($status=='R')){
            $schmcode='FEYRL';
            $ledgercode='12050';
        }
        elseif(($Ctype=='Y') && ($age >= '60') && ($status=='R')){
            $schmcode='FEYRS';
            $ledgercode='12050';
        }
        elseif(($Ctype=='Q') && ($age < '60') && ($status=='R')){
            $schmcode='FEQTR';
            $ledgercode='12050';
        }
        elseif(($Ctype=='Q') && ($age >= '60') && ($status=='R')){
            $schmcode='FEQTS';
            $ledgercode='12050';
        }
        elseif(($Ctype=='M') && ($age < '60') && ($status=='R')){
            $schmcode='FEMNT';
            $ledgercode='12050';
        }
        elseif(($Ctype=='M') && ($age >= '60') && ($status=='R')){
            $schmcode='FEMNS';
            $ledgercode='12050';
        }
        elseif(($Ctype=='C') && ($age < '60') && ($status=='NRE')){
            $schmcode='NRCED';
            $ledgercode='15050';
        }
         elseif(($Ctype=='C') && ($age >= '60') && ($status=='NRE')){
            $schmcode='NRECS';
            $ledgercode='15050';
        }
        elseif(($Ctype=='Y') && ($age < '60') && ($status=='NRE')){
            $schmcode='NREYL';
            $ledgercode='12050';
        }
        elseif(($Ctype=='Y') && ($age >= '60') && ($status=='NRE')){
            $schmcode='NREYS';
            $ledgercode='12050';
        }
        elseif(($Ctype=='Q') && ($age < '60') && ($status=='NRE')){
            $schmcode='NREQT';
            $ledgercode='12050';
        }
        elseif(($Ctype=='Q') && ($age >= '60') && ($status=='NRE')){
            $schmcode='NREQS';
            $ledgercode='12050';
        }
        elseif(($Ctype=='M') && ($age < '60') && ($status=='NRE')){
            $schmcode='NREMT';
            $ledgercode='12050';
        }
        elseif(($Ctype=='M') && ($age >= '60') && ($status=='NRE')){
            $schmcode='NREMS';
            $ledgercode='12050';
        }

        if(!empty($row['NAME'])){
            $exp = explode(' ',trim($row['NAME']));

            $tnamecount = count($exp);

            if($tnamecount == 3){
                $salutaion = trim($exp[0]);
                $firstname = trim($exp[1]);
                $middlename = '';
                $lastname = trim($exp[2]);

            }
            elseif($tnamecount == 4){
                $salutaion = trim($exp[0]);
                $firstname = trim($exp[1]);
                $middlename = trim($exp[2]);
                $lastname = trim($exp[3]);

            }elseif($tnamecount == 5){

                $salutaion = trim($exp[0]);
                $firstname = trim($exp[1]).' '.trim($exp[2]);
                $middlename = trim($exp[3]);
                $lastname = trim($exp[4]);

            }
        }


        $driver->findElement(WebDriverBy::name("tdacop.schmCode"))->sendKeys($schmcode);
        $driver->findElement(WebDriverBy::name("tdacop.glSubHeadCode"))->sendKeys($ledgercode);
        $driver->findElement(WebDriverBy::id("Accept"))->click();
        sleep(3);

        //GENERAL TAB DETAILS
        $driver->findElement(WebDriverBy::name("tdgen.acctName"))->clear();
        $driver->findElement(WebDriverBy::name("tdgen.acctName"))->sendKeys($row['NAME']);
        $driver->findElement(WebDriverBy::name("tdgen.acctShortName"))->clear();
        $driver->findElement(WebDriverBy::name("tdgen.acctShortName"))->sendKeys($firstname);
        $driver->findElement(WebDriverBy::name("tdgen.acctOpenDate_ui"))->sendKeys('04/07/2019');

        $driver->findElement(WebDriverBy::name("tdgen.modeOfOperCode"))->sendKeys('SING');
        $driver->findElement(WebDriverBy::name("tdgen.locationCode"))->sendKeys('600');

        //Interest & Taxes
        $driver->findElement(WebDriverBy::id("tdint"))->click();
        
        sleep(3);

        //SCHEME TAB DETAILS

        $driver->findElement(WebDriverBy::id("tdsch"))->click();
        sleep(2);

        $driver->wait(120,1000)->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tdsch.valueDate_ui"))
        );
        // date should be taken from filename please change
        $driver->findElement(WebDriverBy::name("tdsch.valueDate_ui"))->clear()->sendKeys('04-07-2019');
        $driver->findElement(WebDriverBy::name("tdsch.depAmt"))->clear();
        $driver->findElement(WebDriverBy::name("tdsch.depAmt"))->sendKeys($row['AMOUNT']);
        $driver->findElement(WebDriverBy::name("tdsch.depPerdMths"))->clear();
        $driver->findElement(WebDriverBy::name("tdsch.depPerdMths"))->sendKeys($row['TENURE']);
        $driver->findElement(WebDriverBy::name("tdsch.repayAcct"))->sendKeys('10001RPAYAC01');

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("nomineeFlg"))
        );

        $css = "input[id='nomineeFlg'][value='N']";
        $driver->findElement(WebDriverBy::cssSelector($css))->click();
        $driver->findElement(WebDriverBy::cssSelector($css))->click();
        sleep(2);

        //FLOW TAB DETAILS
        $driver->findElement(WebDriverBy::id("tdflw"))->click();
        sleep(2);

        //RENEWAL & CLOSURE
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("tdren"))
        );
        $driver->findElement(WebDriverBy::id("tdren"))->click();
          sleep(2);
        
        $css = "input[name='tdren.autoClosureFlg'][value='Y']";
        $driver->findElement(WebDriverBy::cssSelector($css))->click();


        $driver->findElement(WebDriverBy::id("renewMths"))->clear();


        $driver->findElement(WebDriverBy::id("renewDays"))->clear();

        $driver->findElement(WebDriverBy::id("renewSchm"))->clear();
        $driver->findElement(WebDriverBy::id("renewGLSubHead"))->clear();
        $driver->findElement(WebDriverBy::id("renewIntTable"))->clear();
        $driver->findElement(WebDriverBy::id("renewCrncy"))->clear();
        
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("renewOption"))
        );

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::id("renewOption")));
        $element->selectByValue('');

    
        //NOMINATION DETAILS

         $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("nominationdetails"))
        );
         $driver->findElement(WebDriverBy::id("nominationdetails"))->click();
         sleep(2);
         //write all mandatory fields of nominations


        
        //RELATED PARTY
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("relatedpartydetails"))
        );
        $driver->findElement(WebDriverBy::id("relatedpartydetails"))->click();
        sleep(2);
        if(!empty($row['cifid_2']) && !empty($row['JH1PAN'])){
            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("relParty_NextRec"))
            );
            $driver->findElement(WebDriverBy::id("relParty_NextRec"))->click();
            sleep(2);
             //write all mandatory fields of joinholders
        }
        if(!empty($row['cifid_3']) && !empty($row['JH2PAN'])){
            $driver->wait(60,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("relParty_NextRec"))
            );
            $driver->findElement(WebDriverBy::id("relParty_NextRec"))->click();
            sleep(2);
             //write all mandatory fields of joinholders
        }

        //DOCUMENT DETAILS
        $driver->findElement(WebDriverBy::id("documentdetails"))->click();
        sleep(3);
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("documentdetails.docCode"))
        );
        $driver->findElement(WebDriverBy::name("documentdetails.docCode"))->sendKeys('KYC');

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("documentdetails.docScanFlg")));
        $element->selectByValue('Y');

        $driver->findElement(WebDriverBy::name("documentdetails.docFreeText1"))->sendKeys($row['BANKNM']);
        $driver->findElement(WebDriverBy::name("documentdetails.docFreeText2"))->sendKeys($row['BANKAC']);
        $driver->findElement(WebDriverBy::name("documentdetails.docFreeText3"))->sendKeys($row['IFSC']);

        // take from gaurav w1234
        $driver->findElement(WebDriverBy::name("documentdetails.docFreeText4"))->sendKeys('W1234');
        $driver->findElement(WebDriverBy::name("documentdetails.docFreeText5"))->sendKeys($row['TRNREFNO']);
        $driver->findElement(WebDriverBy::name("documentdetails.docFreeText6"))->sendKeys($row['PANGIR1']);
        $driver->findElement(WebDriverBy::name("documentdetails.docFreeText7"))->sendKeys('RECD DEMAT STATEMENT');
        
        //OTHER DETAILS
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("otherdetails"))
        );

        $driver->findElement(WebDriverBy::id("otherdetails"))->click();
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("otherdetails.srcDlrId"))
        );

        $driver->findElement(WebDriverBy::name("otherdetails.srcDlrId"))->sendKeys('**C020601');
        sleep(2);
        $driver->findElement(WebDriverBy::id("Submit"))->click();
        $driver->findElement(WebDriverBy::id("Submit"))->click();
        sleep(2);
        $handle = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handle));

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("accept"))
        );

        $driver->findElement(WebDriverBy::id("accept"))->click();
        sleep(3);
        $driver->switchTo()->window($handle[0]);
        $driver->switchTo()->defaultContent();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
         );

        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);

  
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CoreServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CoreServer"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("FINW"))
        );

        $frame = $driver->findElement(WebDriverBy::id("FINW"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("acctNum"))
        );

        $accnum = $driver->findElement(WebDriverBy::id("acctNum"))->getText();

        $this->saveAccountID($accnum,$row['TRNREFNO']);

        $driver->findElement(WebDriverBy::id("Ok"))->click();

    }

    public function saveAccountID($accnum, $trnrefno){
        $sql = "UPDATE hfccustdata SET AccountNo = $accnum where TRNREFNO = '$trnrefno'";
        $qparent = $this->db->query($sql);
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

    function takeScreenshot($prefix, $driver, $control, $trnrefno){
        $path = APS_SCREENSHOTS.$trnrefno.SEPARATOR;
        if(!is_dir($path)){
            mkdir($path, 0777, true);
        }
        $img = $prefix.$control->process_dtl_id.'_'.$control->seq_id.'_'.date('Y_m_d_H_i_s').'.png';
        $driver->takeScreenshot($path.$img);
        $this->logout($driver);
    }

    public function saveCustID($custid, $trnrefno,$cfid){
       
        $sql = "UPDATE hfccustdata SET $cfid = $custid where TRNREFNO = '$trnrefno'";
        $qparent = $this->db->query($sql);

    }
    public function editCustID($trnrefno,$cfid){
       
        $sql = "UPDATE hfccustdata SET edit_$cfid = '1' where TRNREFNO = '$trnrefno'";
        $qparent = $this->db->query($sql);

    }

    public function addException($e, $trnrefno, $userId, $error_section, $driver){

        echo '<pre>';
        print_r($e);

        $img = SCREENSHOTS.$trnrefno.'_'.date('Y_m_d_H_i_s').'.png';

        $error = $e->getResults();
        $exception_dtl = $error['value']['localizedMessage'];

        $exception_dtl = $this->db->escape_str($exception_dtl);
        $exception_class = get_class($e);
        $exception_class = $this->db->escape_str($exception_class);
        $excp = explode('Build info:', $exception_dtl);
        $excp = explode('For documentation', $excp[0]);

        $sql = "INSERT INTO bot_error_logs (exception_class, TRNREFNO, exception_dtl, userId, error_section, screenshot_path) VALUES ('".$exception_class."','".$trnrefno."','".$excp[0]."', '".$userId."', '".$error_section."','".$this->db->escape_str($img)."')";
        $qparent = $this->db->query($sql);

        
        $sql = "UPDATE bot_aps_tracking SET status = 'E', end_time = '".date("Y-m-d H:i:s")."' WHERE TRNREFNO = '".$trnrefno."'";
        $qparent = $this->db->query($sql);
        try {
            $driver->takeScreenshot($img);
        } catch (Exception $e) {
            
        }
        $this->logout($driver);
        exit();
    }

    public function getUserName(){
        $localIP = getHostByName(getHostName());
        $sql = "SELECT username FROM bot_ip_logins WHERE ip_address = '$localIP'";
        $rows = $this->db->query($sql)->result_array();
        if(count($rows) > 0){
            //return $rows[0]['username'];
            return 'HE000827';
        }else{
            return 'HE000827';
        }
    }

    public function getPassword(){
        $localIP = getHostByName(getHostName());
        $sql = "SELECT password FROM bot_ip_logins WHERE ip_address = '$localIP'";
        $rows = $this->db->query($sql)->result_array();
        if(count($rows) > 0){
            //return $rows[0]['password'];
            return 'User@123';
        }else{
            return 'User@123';
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

}
?>