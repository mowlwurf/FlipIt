<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mowlwurf
 * Date: 5/1/12
 * Time: 10:04 AM
 * To change this template use File | Settings | File Templates.
 */

require_once('../simpletest/autorun.php');

class AllTests extends TestSuite {

    function AllTests() {
        $this->TestSuite('All tests');
        $this->addFile('inc/class.DatabaseTest.inc.php');
        $this->addFile('inc/class.TableGenTest.inc.php');
        $this->addFile('inc/class.TableHandlerTest.inc.php');
    }
}