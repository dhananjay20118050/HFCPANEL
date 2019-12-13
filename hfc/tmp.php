<?php

require 'application/libraries/selenium/vendor/autoload.php';

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverSelectInterface;




$driver = RemoteWebDriver::createBySessionID('0ea834ea-794f-4755-af2d-462fc1be1f2a','http://localhost:5555/wd/hub');

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

$driver->findElement(WebDriverBy::id("tab_tpageCont3"))->click();

$element = new WebDriverSelect($driver->findElement(WebDriverBy::name("AccountBO.Address.preferredAddress")));
$element->selectByValue('Mailing');
sleep(5);
$driver->findElement(WebDriverBy::name("Add Address Details"))->click();

$handle = $driver->getWindowHandles();

print_r($handle);
$driver->switchTo()->window(end($handle));

$driver->switchTo()->defaultContent();
$driver->switchTo()->defaultContent();

$driver->wait(60,1000)->until(
    WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::linkText("More information"))
);
$driver->findElement(WebDriverBy::linkText("More information"))->click();


?>