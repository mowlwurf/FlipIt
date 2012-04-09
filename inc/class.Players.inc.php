<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mowlwurf
 * Date: 4/9/12
 * Time: 12:17 PM
 * To change this template use File | Settings | File Templates.
 */

Class Players{

    private $iPoints      = Null;
    private $sStartPoint  = false;
    private $aPlayerField = array();

    function getPoints()
    {
        return $this->iPoints;
    }

    function __construct()
    {
        $this->_setStartPoint();
    }

    private function _setStartPoint()
    {
        $this->sStartPoint = '0/0';
        $this->_setPlayerField();
    }

    private function _setPlayerField($mFields = false)
    {
        if($mFields = false || trim($mFields) === '')
        {
            return false;
        }
    }

}