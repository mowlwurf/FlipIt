<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mowlwurf
 * Date: 5/1/12
 * Time: 10:09 AM
 * To change this template use File | Settings | File Templates.
 */
require_once('../simpletest/autorun.php');
require_once('class.DbController.php');

Class DatabaseTest extends UnitTestCase
{
    public function testGetConfig()
    {
        $this->assertNull($aConfig);
        $this->assertTrue(file_exists('cnf/config.inc.php'));
        include('cnf/config.inc.php');
        $this->assertNotNull($aConfig);
        $this->assertIsA($aConfig,'Array');
    }

    public function testGetDatabaseConnection()
    {
        $this->assertNull($aConfig);
        $oDBController = new DbController();
        include('cnf/config.inc.php');
        $this->assertIsA($aConfig,'Array');
        $this->assertTrue($oDBController->getConnection($aConfig['dbUser'],$aConfig['dbPassword'],$aConfig['dbName'],$aConfig['dbServer']));
    }

}