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
        $this->oDBController = new DbController();
        //$this->oLog          = new LogDoc();
        include('cnf/config.inc.php');
        $this->_readConfig($aConfig);
        $this->_setStartPoint();
    }

    private function _setStartPoint()
    {
        $this->sStartPoint = '0/0';
        $this->_setPlayerField($this->sStartPoint);
    }

    private function _getPlayerFields()
    {
        $this->oDBController->getConnection($this->dbUser,$this->dbPassword,$this->dbName,$this->dbServer);
        $this->oDBController->query('SELECT `fields_player1` FROM '.$this->active_table);
        $aJson = $this->oDBController->getResult(); // todo SELECT one statment in dbhandler
        $aActualPlayerFields=unserialize($aJson[0]['fields_player1']);
        $this->oDBController->clearCache();
        return $aActualPlayerFields;
    }

    private function _setPlayerField($sField)
    {
        if($sField = false || trim($sField) === '' || strpos($sField,'/') === false)
        {
            return false;
        }
        $aActualPlayerFields=array();
        $aNewFieldCoords    =explode('/',$sField);
        $aActualPlayerFields=$this->_getPlayerFields();
        if(!is_array($aActualPlayerFields))
        {
            $aActualPlayerFields = array('0' => array('0'=>$aNewFieldCoords[0],'1'=>$aNewFieldCoords[1]));
        }
        else
        {
            $aActualPlayerFields[] = array('0'=>$aNewFieldCoords[0],'1'=>$aNewFieldCoords[1]);
        }
        $this->oDBController->query('UPDATE '.$this->active_table.' SET `fields_player1` = \''.serialize($aActualPlayerFields).'\'');
        //$this->oLog->log(__FILE__,__FUNCTION__,'process-10 (must be coordinatesys)',print_r($aActualPlayerFields,true));
    }

    private function _readConfig($aConfig)
    {
        if(!is_array($aConfig))
        {
            return false;
        }
        foreach($aConfig as $sKey => $sValue)
        {
            $this->$sKey = $sValue;
        }
        return true;
    }
}