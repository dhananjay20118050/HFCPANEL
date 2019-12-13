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

    public function start($userId, $trnrefno){

        $this->closeIESessions();

        $sql = "SELECT * FROM hfccustdata where TRNREFNO IN (SELECT TRNREFNO FROM bot_aps_tracking WHERE status IN ('N','E','P') AND TRNREFNO = '$trnrefno') ORDER BY id";
        
        $rows = $this->db->query($sql)->result_array();

        if(count($rows) > 0){

            $row = $rows[0];
            $capabilities = DesiredCapabilities::internetExplorer();
            $driver = RemoteWebDriver::create('http://localhost:5555/wd/hub', $capabilities, 5000);

            $localIP = getHostByName(getHostName());

            $sql = "UPDATE bot_aps_tracking SET status = 'P', ip_address = '".$localIP."', start_time = '".date("Y-m-d H:i:s")."' WHERE TRNREFNO = '".$row['TRNREFNO']."'";
            $qparent = $this->db->query($sql);

            try {
                $this->loginCust($driver, $row);
            } catch (Exception $e) {
                $this->addException($e, $row['TRNREFNO'], $userId, "Finacle Login", $driver);
            }

            try {
                $this->fincoreProcess($driver, $row);
            } catch (Exception $e) {
                $this->addException($e, $row['TRNREFNO'], $userId, "Cust ID Search", $driver);
            }

            try {

                $this->addNewCustId($driver, $row);

                $sql = "UPDATE bot_aps_tracking SET status = 'Y', end_time = '".date("Y-m-d H:i:s")."' WHERE TRNREFNO = '".$row['TRNREFNO']."'";
                $qparent = $this->db->query($sql);

            } catch (Exception $e) {
                $this->addException($e, $row['TRNREFNO'], $userId, "Cust ID Creation", $driver);
            }

             $sql = "UPDATE bot_aps_tracking SET status = 'Y', end_time = '".date("Y-m-d H:i:s")."' WHERE TRNREFNO = '".$row['TRNREFNO']."'";
            $qparent = $this->db->query($sql);

            $driver->quit();
            $this->closeIESessions();
                
        }
    }

    public function loginCust($driver, $row){

        $url = "https://ijprsunt7-04-ld18.icicibankltd.com:8212/SSO/ui/SSOLogin.jsp";
        $driver->get($url);
        $driver->findElement(WebDriverBy::id("moreInfoContainer"))->click();
        $driver->findElement(WebDriverBy::id("overridelink"))->click();

        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("usertxt"))
        );

        $driver->findElement(WebDriverBy::id("usertxt"))->sendKeys($this->getUserName());
        $driver->findElement(WebDriverBy::id("passtxt"))->sendKeys($this->getPassword());

        $driver->findElement(WebDriverBy::id("Submit"))->click();

    }

    public function fincoreProcess($driver, $row){

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("appSelect"))
        );

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("appSelect")));
        $element->selectByValue('CoreServer');

        $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();
sleep(20);
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CoreServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CoreServer"));
        $driver->switchTo()->frame($frame);
        sleep(10);
        $frame = $driver->findElement(WebDriverBy::id("FINW"));
        $driver->switchTo()->frame($frame);

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
        sleep(2);

        $handles = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handles));
        $driver->switchTo()->defaultContent();
        sleep(5);
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("GetCustIdCriteria"))
        );

        $frame = $driver->findElement(WebDriverBy::name("GetCustIdCriteria"));
        $driver->switchTo()->frame($frame);

        $driver->findElement(WebDriverBy::id("docNo"))->sendKeys($row['PANGIR1']);
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

            $sql = "UPDATE hfccustdata SET cifid_1 = '".$custId."',is_existing_cust = '1'  WHERE TRNREFNO = '".$row['TRNREFNO']."'";
            $qparent = $this->db->query($sql);

           // echo 'Account Found. Cust Id Exists;
        }else{
           // echo 'Account Not Found.';
        }
      
        $allWindows = $driver->getWindowHandles();

        $driver->switchTo()->window(end($allWindows))->close();

        $driver->switchTo()->window($allWindows[0]);

        $driver->switchTo()->defaultContent();

    }


    public function addNewCustId($driver, $row){


        $driver->wait(120,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );

        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);

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
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("view2"))
        );

        $driver->findElement(WebDriverBy::id("view2"))->click();


        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("subview20"))
        );

        $driver->findElement(WebDriverBy::id("subview20"))->click(); 
        

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


        $exp = explode(' ',$row['NAME']);

        $tnamecount = count($exp);

        if($tnamecount == 3){
            $salutaion = $exp[0];
            $firstname = $exp[1];
            $middlename = '';
            $lastname = $exp[2];

        }
        elseif($tnamecount == 4){
            $salutaion = $exp[0];
            $firstname = $exp[1];
            $middlename = $exp[2];
            $lastname = $exp[3];

        }elseif($tnamecount == 5){

            $salutaion = $exp[0];
            $firstname = $exp[1].' '.$exp[2];
            $middlename = $exp[3];
            $lastname = $exp[4];

        }

        if($salutaion =="MR" || $salutaion =="MR."){
            $gender = 'M';
        }else{
            $gender = 'F';
        }

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountModBO.Gender")));
        $element->selectByValue($gender);

        $element = $driver->findElement(WebDriverBy::name("AccountModBO.Salutation_code"));
        $element->sendKeys($salutaion);
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
        $element->sendKeys($row['NAME']);
        
        $element = $driver->findElement(WebDriverBy::name("3_AccountBO.Cust_DOB"));
        $element->sendKeys($row['DOB']);

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountModBO.CustomerNREFlg")));
        $element->selectByValue('N');

        $element = $driver->findElement(WebDriverBy::name("Cat_AccountBO.Constitution_Code"));
        $element->sendKeys('RTL-INDIVIDUAL');

        $element = $driver->findElement(WebDriverBy::name("AccountModBO.Tds_tbl"));
        $element->sendKeys('TDSI');
        $element->sendKeys(array(WebDriverKeys::TAB));
 
        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountModBO.PurgeFlag")));
        $element->selectByValue('N');

        $driver->findElement(WebDriverBy::id("rownative2"))->click();

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountModBO.Introd_Status")));
        $element->selectByValue('PAN');

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountModBO.IntroducerSalutation")));
        $element->selectByValue($salutaion);

        $element = $driver->findElement(WebDriverBy::name("AccountBO.IntroducerName"));
        $element->sendKeys($row['NAME']);

        $driver->switchTo()->defaultContent();
   
        /*$driver->wait(60,1000)->until(
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
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("buttonFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("buttonFrm"));
        $driver->switchTo()->frame($frame);
      
         $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("saveBut"))
        );
        $driver->findElement(WebDriverBy::id("saveBut"))->click(); 
        

        $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();
 
        $driver->switchTo()->defaultContent();

        $driver->wait(60,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);*/
       

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

        $handle = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handle));
        sleep(5);
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("moreInfoContainer"))
        );

        $driver->manage()->timeouts()->implicitlyWait = 10;
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::linkText("More information"))
        );
        $driver->findElement(WebDriverBy::linkText("More information"))->click();

        $driver->findElement(WebDriverBy::linkText("More information"))->click();

        $driver->findElement(WebDriverBy::id("overridelink"))->click();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.Address.PreferredFormat"))
        );

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.Address.PreferredFormat")));
        $element->selectByValue('FREE_TEXT_FORMAT');

        $driver->manage()->timeouts()->implicitlyWait = 10;

        $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
        $driver->switchTo()->alert()->accept();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.Address.addressCategory"))
        );

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.Address.addressCategory")));
        $element->selectByValue('Mailing');
      
        $element = $driver->findElement(WebDriverBy::name("AccountBO.Address.address_Line1"));
        $element->sendKeys($row['ADD1']);

        $element = $driver->findElement(WebDriverBy::name("AccountBO.Address.address_Line2"));
        $element->sendKeys($row['ADD2'].$row['ADD3']);

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

        $driver->findElement(WebDriverBy::name("Save"))->click();

        $driver->switchTo()->window($handle[0]);

        //PHONE

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

        $driver->findElement(WebDriverBy::id("fnttpagePhone"))->click();

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("Add Phone and E-mail"))
        );

        $driver->findElement(WebDriverBy::name("Add Phone and E-mail"))->click();


        $handle = $driver->getWindowHandles();
        $driver->switchTo()->window(end($handle));
        

        //$driver->wait(120,1000)->until(
           // WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.PhoneEmail.PhoneOrEmail"))
        //);

        //$element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneOrEmail")));
        //$element->selectByValue('PHONE');

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.PhoneEmail.PhoneEmailType"))
        );

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneEmailType")));
        $element->selectByValue('CELLPH');

        $element = $driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneNo.cntrycode"));
        $element->sendKeys('91');
        $element = $driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneNo.localcode"));
        $element->sendKeys($row['MOBILENO']);

        $driver->findElement(WebDriverBy::name("Save"))->click();

        //Email Add

        $driver->switchTo()->window($handle[0]);
         
        $driver->switchTo()->defaultContent();

        $driver->wait(120,1000)->until(
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
        

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.PhoneEmail.PhoneOrEmail"))
        );

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneOrEmail")));
        $element->selectByValue('EMAIL');

        $driver->wait(120,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AccountBO.PhoneEmail.PhoneEmailType1"))
        );

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.PhoneEmailType1")));
        $element->selectByValue('REGEML');

        $element = $driver->findElement(WebDriverBy::name("AccountBO.PhoneEmail.Email"));
        $element->sendKeys($row['EMAILID']);
       
        $driver->findElement(WebDriverBy::name("Save"))->click();
  
        $driver->switchTo()->window($handle[0]);
         
        $driver->switchTo()->defaultContent();

        $driver->wait(120,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );

        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);
        
        $driver->wait(120,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CRMServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CRMServer"));
        $driver->switchTo()->frame($frame);

        $driver->wait(120,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("DataAreaFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("DataAreaFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(120,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tempFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("tempFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(120,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tabContentFrm"))
        );
        $frame = $driver->findElement(WebDriverBy::name("tabContentFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(120,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("userArea"))
        );
        $frame = $driver->findElement(WebDriverBy::id("userArea"));
        $driver->switchTo()->frame($frame);

        $frame = $driver->findElement(WebDriverBy::name("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(120,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("formDispFrame"))
        );

        $frame = $driver->findElement(WebDriverBy::id("formDispFrame"));
        $driver->switchTo()->frame($frame);

        //IDENTIFICATION DETAILS

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("tab_tpageCont5"))
        );

        $driver->findElement(WebDriverBy::id("tab_tpageCont5"))->click();


        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("AddIdentificationDetails"))
        );
        $driver->findElement(WebDriverBy::name("AddIdentificationDetails"))->click();

        $handle = $driver->getWindowHandles();

        $driver->switchTo()->window(end($handle));

    
        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("EntityDocumentBO.DocTypeCode"))
        );

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("EntityDocumentBO.DocTypeCode")));
        $element->selectByValue('IDENTIFICATION');

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("EntityDocumentBO.DocCode")));
        $element->selectByValue('PANGAR');

        $element = $driver->findElement(WebDriverBy::name("EntityDocumentBO.ReferenceNumber"));
        $element->sendKeys($row['PANGIR1']);

        $driver->findElement(WebDriverBy::name("SAVE"))->click();

        //CURRENCY TAB

        $handle = $driver->getWindowHandles();

        $driver->switchTo()->window($handle[0]);
         
        $driver->switchTo()->defaultContent();

        $driver->wait(120,1000)->until(
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

        $frame = $driver->findElement(WebDriverBy::name("IFrmtab0"));
        $driver->switchTo()->frame($frame);

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("formDispFrame"))
        );

        $frame = $driver->findElement(WebDriverBy::id("formDispFrame"));
        $driver->switchTo()->frame($frame);

        $driver->findElement(WebDriverBy::id("tab_tpageCont6"))->click();
        $driver->findElement(WebDriverBy::name("ADD_CURRENCYDET"))->click();

        $handle = $driver->getWindowHandles();

        $driver->switchTo()->window(end($handle));        

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("PsychographicBO.MiscellaneousInfo.strText10")));
        $element->selectByValue('INR');

        $driver->findElement(WebDriverBy::name("SAVE"))->click();

        //Demographic

        $handle = $driver->getWindowHandles();

        $driver->switchTo()->window($handle[0]);

        $driver->switchTo()->defaultContent();

        $driver->wait(120,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);
        
        $driver->wait(120,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CRMServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CRMServer"));
        $driver->switchTo()->frame($frame);

        $driver->wait(120,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("DataAreaFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("DataAreaFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(120,1000)->until(
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

        $driver->wait(120,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);
        
        $driver->wait(120,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CRMServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CRMServer"));
        $driver->switchTo()->frame($frame);

        $driver->wait(120,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("DataAreaFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("DataAreaFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(120,1000)->until(
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

        $driver->wait(120,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("loginFrame"))
        );
        $frame = $driver->findElement(WebDriverBy::name("loginFrame"));
        $driver->switchTo()->frame($frame);
        
        $driver->wait(120,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("CRMServer"))
        );

        $frame = $driver->findElement(WebDriverBy::name("CRMServer"));
        $driver->switchTo()->frame($frame);

        $driver->wait(120,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("DataAreaFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("DataAreaFrm"));
        $driver->switchTo()->frame($frame);

        $driver->wait(120,1000)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("tempFrm"))
        );

        $frame = $driver->findElement(WebDriverBy::name("tempFrm"));
        $driver->switchTo()->frame($frame);

        $frame = $driver->findElement(WebDriverBy::name("buttonFrm"));
        $driver->switchTo()->frame($frame);

        $driver->findElement(WebDriverBy::id("saveBut"))->click();

        $driver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
        $custdata = $driver->switchTo()->alert()->getText();
        $custexp = explode(': ',$custdata);
        $custId = end($custexp);

        $this->saveCustID($custId,$row['TRNREFNO']);
        $driver->switchTo()->alert()->accept();
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
    }

    public function saveCustID($custid, $trnrefno){
       
        $sql = "UPDATE hfccustdata SET cifid_1 = $custid where TRNREFNO = '$trnrefno'";
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
        exit();
    }

    public function getUserName(){
        $localIP = getHostByName(getHostName());
        $sql = "SELECT username FROM bot_ip_logins WHERE ip_address = '$localIP'";
        $rows = $this->db->query($sql)->result_array();
        if(count($rows) > 0){
           // return $rows[0]['username'];
            return 'HE000049';
        }else{
            return 'HE000049';
        }
    }

    public function getPassword(){
        $localIP = getHostByName(getHostName());
        $sql = "SELECT password FROM bot_ip_logins WHERE ip_address = '$localIP'";
        $rows = $this->db->query($sql)->result_array();
        if(count($rows) > 0){
            return 'vikas@123';
        }else{
            return 'vikas@123';
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

}
?>