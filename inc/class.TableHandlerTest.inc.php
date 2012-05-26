<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mowlwurf
 * Date: 5/1/12
 * Time: 1:13 PM
 * To change this template use File | Settings | File Templates.
 */

require_once('../simpletest/autorun.php');
require_once('class.TableHandler.inc.php');

Class TableHandlerTest extends UnitTestCase
{
    public function testHandlerUsability()
    {
        $oTableHandler = new TableHandler();
        $this->assertNotNull($oTableHandler->setStartView());
        $this->assertNotNull($oTableHandler);
        $this->assertIsA($oTableHandler,'TableHandler');
        $aAvailableColors = $oTableHandler->getColorSwitcher();
        $this->assertNotNull($aAvailableColors);
        $this->assertNotNull($aAvailableColors[1]);
        $this->assertIsA($aAvailableColors,'Array');
        $aColidedTabs = $oTableHandler->getColidingTabs($aAvailableColors[0]);
        print_r($aColidedTabs);
        $this->assertIsA($aColidedTabs[0],'Array');
    }
}