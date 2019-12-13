<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Node;
use App\Process;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverSelectInterface;

class CC_model extends Model{

    protected $appId = 1;
    protected $db;

    public function __construct()
    {
        $this->db = Process::setDB($this->appId);
    }
    
    public function downloadImages($request){

        $pid = 1;
        $path = $request->file('input-file')->getRealPath();
        $file = file($path);
        $appNos = array();
        foreach ($file as $row){
            $appNos[] = (explode(',',$row))[0];
        }

        $userIp = request()->ip();
        $node = Node::where('ip', $userIp)->firstOrFail();
        $host = 'http://'.$userIp.':'.$node->port.'/wd/hub';

        $driver = Process::getDriver($pid, $host);
        $config = Process::find($pid);

        $driver->get($config->url);
        $driver->findElement(WebDriverBy::id("username"))->sendKeys($config->username);
        $driver->findElement(WebDriverBy::id("password"))->sendKeys($config->password);
        $driver->findElement(WebDriverBy::id('SUBMIT'))->click();
        $driver->wait()->until(WebDriverExpectedCondition::urlIs("https://idisburse.icicibank.com:449/istreams/home")
        );
        Process::waitForAjax($driver);
        sleep(2);
        $element = WebDriverBy::id('externalVariable');
        $driver->wait(20,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated($element)
        );
        $select = new WebDriverSelect($driver->findElement($element));
        $select->selectByValue('APPLICATIONNO');
        
        $sp = config('constants.separator');
        $oldPath = $config->downloadDir.$sp;
        $newPath = $config->downloadDir.$sp.date('d-m-Y').$sp;
        $logFile = $config->downloadDir.$sp.date('d-m-Y').$sp.'log.txt';
        $log = "";

        foreach ($appNos as $appNo) {

            $appNo = trim($appNo);

            $driver->findElement(WebDriverBy::id('searchQuery'))->clear();
            $driver->findElement(WebDriverBy::id('searchQuery'))->sendKeys($appNo);
            $driver->findElement(WebDriverBy::id('searchQuery_Button'))->click();

            Process::waitForAjax($driver);
            sleep(3);
            $element = WebDriverBy::xpath('//*[@id="processInstanceTableBody"]/tr');
            $rows = $driver->findElements($element);

            $foundAt = count($rows);

            if($foundAt == 0){
                $log = $appNo.' : Folder not found.'.PHP_EOL;
                file_put_contents($logFile, $log , FILE_APPEND | LOCK_EX);
                continue;
            }else{
                $driver->findElement(WebDriverBy::xpath('//*[@id="processInstanceTableBody"]/tr['.$foundAt.']/td[1]/a'))->click();
            }

            $firstWindow = $driver->getWindowHandle();
            $handles = $driver->getWindowHandles();
            $lastHandle = end($handles);
            $driver->switchTo()->window($lastHandle);
            Process::waitForAjax($driver);
            sleep(3);

            $element = $driver->findElement(WebDriverBy::id('APPLICATIONNO'));

            if($element->getAttribute('value') != $appNo){
                $log = $appNo.' : Folder not found.'.PHP_EOL;
                file_put_contents($logFile, $log , FILE_APPEND | LOCK_EX);
                continue;
            }

            $frame = $driver->findElement(WebDriverBy::id('documentForm'));
            $driver->switchTo()->frame($frame);
            sleep(1);

            $frame = $driver->findElement(WebDriverBy::name('frametop'));
            $driver->switchTo()->frame($frame);

            $element = WebDriverBy::name('docList');
            $driver->wait(20,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated($element)
            );
            $select = new WebDriverSelect($driver->findElement($element));

            try {
                $filename = "Application Form.tif";
                $select->selectByVisibleText($filename);
            } catch (\Throwable $th) {
                $filename = "Application_Form.TIF";
                $select->selectByVisibleText($filename);
            }

            $prefix = explode('.', $filename);
            $driver->switchTo()->defaultContent();

            $frame = $driver->findElement(WebDriverBy::id('documentForm'));
            $driver->switchTo()->frame($frame);

            $element = WebDriverBy::name('framebottom');
            $driver->wait(20,1000)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated($element)
            );
            $frame = $driver->findElement($element);
            $driver->switchTo()->frame($frame);
            
            Process::waitForAjax($driver);
            sleep(3);

            $element = $driver->findElement(WebDriverBy::xpath('/html/body/form[1]/script[1]'));
            $text = $element->getAttribute("innerHTML");
            
            $text = (explode('function Download()', $text))[1];

            $text = str_replace("dForm.DocumentName.value = 'Application_Form'", "dForm.DocumentName.value = '".$appNo."'", $text);

            $text = 'function Download2()'.$text.';Download2();';

            //echo $text.PHP_EOL;

            $driver->executeScript($text);
            Process::waitForAjax($driver);

            $path = $oldPath.$appNo.'.TIF';
            while (!file_exists($path)) {
                //wait for it...
            }

            if (!file_exists($newPath)) {
                mkdir($newPath, 0777, true);
            }
            rename($path, $newPath.$appNo.".TIF");
            $log .= $appNo.' : Done.'.PHP_EOL;
            file_put_contents($logFile, $log , FILE_APPEND | LOCK_EX);

            //$driver->close();
            $driver->switchTo()->window($firstWindow);
        }
        $driver->quit();
        return $log;
    }

}