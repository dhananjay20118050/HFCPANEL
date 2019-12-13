//demogramphic

        /*$driver->switchTo()->window($handle[0]);
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
        $element = $driver->findElement(WebDriverBy::cssSelector("a[href='javascript:void populateCifEntityDetails('Main','Account',AccountId,ScreenName,viewnameScreenID);']"));
        $driver->action()->moveToElement($element)->perform();
        $element->click();

        sleep(3);
        $driver->switchTo()->window(end($allWindows))->close();
        $driver->switchTo()->window($allWindows[0]);

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

        $script = 'document.getElementById("ie5submenu3").style.visibility = "visible";';
        $driver->executeScript($script);

        $driver->findElement(WebDriverBy::id("suboptions7"))->click();

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


         $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("DemographicModBO.Nationality"))
        );

        //if(!empty(var)){

        $element = $driver->findElement(WebDriverBy::name("DemographicModBO.Nationality"))->clear();
        $element->sendKeys('INDIAN');

        $driver->wait(60,1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name("Cat_DemographicModBO.Nationality"))
        );

        $element = $driver->findElement(WebDriverBy::name("Cat_DemographicModBO.Nationality"))->clear();
        $element->sendKeys('INDIAN');

        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("DemographicModBO.Marital_Status")));
        $element->selectByValue('OTHER');


        $driver->findElement(WebDriverBy::id("tab_tpageEDet"))->click();
        $element = new WebDriverSelect($driver->findElement(WebDriverBy::name("DemographicModBO.Employment_Status")));
        $element->selectByValue('Other');


        $driver->findElement(WebDriverBy::id("tab_tpageIExp"))->click();
        $element = $driver->findElement(WebDriverBy::name("3_DemographicBO.Annual_Salary_Income"))->clear();
        $element->sendKeys('500000');


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
        $driver->switchTo()->alert()->accept();*/