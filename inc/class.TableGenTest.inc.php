<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mowlwurf
 * Date: 5/1/12
 * Time: 12:44 PM
 * To change this template use File | Settings | File Templates.
 */

require_once('../simpletest/autorun.php');
require_once('class.TableGen.inc.php');

Class TableGenTest extends UnitTestCase
{
     public function testGenerateBoard()
     {
         $oTableGen = new TableGen(1);
         $this->assertNotNull($oTableGen);
         $this->assertIsA($oTableGen,'TableGen');
         $this->assertTrue($oTableGen->saveBoard());
         $aBoardMatrix = $oTableGen->getBoardMatrix();
         $this->assertNotNull($aBoardMatrix);
         $this->assertIsA($aBoardMatrix,'Array');
     }
}