<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\JavaScriptExecutor;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverSelectInterface;

class Process extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'description', 'url', 'username', 'password', 'browserId'
    ];
    
    

    public static function getDriver($pid, $host){

        $data = Process::find($pid);

        switch ($data->browserId) {

            case '1':

                $options = new ChromeOptions();
                $options->addArguments(['--start-maximized', '--disable-web-security', '--allow-running-insecure-content']);
                $options->setExperimentalOption('excludeSwitches', ['enable-automation']);
                $options->setExperimentalOption('useAutomationExtension', false);
                $prefs = array(
                    'profile.default_content_setting_values.automatic_downloads' => 1,
                    'credentials_enable_service' => false,
                    'profile.password_manager_enabled' => false,
                    'download.default_directory' => $data->downloadDir
                );
                $options->setExperimentalOption('prefs', $prefs);

                $capabilities = DesiredCapabilities::chrome();
                $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
                $driver = RemoteWebDriver::create($host, $capabilities);
                return $driver;

            case '2':

                $capabilities = DesiredCapabilities::InternetExplorer();
                $capabilities->setCapability("nativeEvents", false);
                $capabilities->setCapability("unexpectedAlertBehaviour", "accept");
                $capabilities->setCapability("ignoreProtectedModeSettings", true);
                $capabilities->setCapability("disable-popup-blocking", true);
                $capabilities->setCapability("enablePersistentHover", true);
                $capabilities->setCapability("ignoreZoomSetting", true);
                $capabilities->setCapability("acceptSslCerts", true);
                $driver = RemoteWebDriver::create($host, $capabilities);
                return $driver;

            case '3':
                return DesiredCapabilities::firefox();
            default:
                return false;
        }
    }

    public static function waitForAjax($driver)
    {
        $code = "return document.readyState";
        do {
        //wait for it
        } while ($driver->executeScript($code) != 'complete');
    }

    public static function setDB($pid){

        $conn = DB::table('apps')->find($pid);
        Config::set('database.connections.mysql_dynamic.host', $conn->db_host);
        Config::set('database.connections.mysql_dynamic.username', $conn->db_username);
        Config::set('database.connections.mysql_dynamic.password', $conn ->db_password);
        Config::set('database.connections.mysql_dynamic.database', $conn ->db_name);

        // //If you want to use query builder without having to specify the connection
        // Config::set('database.default', 'mysql_dynamic');
        // DB::reconnect('mysql_dynamic');
       // print_r($conn);exit;
        return DB::connection('mysql_dynamic');

    }

    public $timestamps = false;

}