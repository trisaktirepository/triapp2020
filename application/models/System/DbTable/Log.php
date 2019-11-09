<?php

class App_Model_System_DbTable_Log extends Zend_Log
{
    const ACCESS = 1;
    const ACTION = 2;

    public function __construct($user_id, $logTypeID = self::ACCESS, $priority=Zend_Log::INFO)
    {        
        $application = new Zend_Application(APPLICATION_ENV,APPLICATION_PATH . '/configs/application.ini');

        // Initialize and retrieve DB resource
        $bootstrap = $application->getBootstrap();
        $bootstrap->bootstrap('db');
        $db = $bootstrap->getResource('db');

        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        
        $dbTableName = 'sys001_log';
        
        $columnMapping = array(
        	'user_id' => 'user_id',
         	'level' => 'priority',
        	'hostname' => 'hostname',
         	'ip' => 'ip',
         	'message' => 'message'
         );
         
        //register writer
        $dbWriter = new Zend_Log_Writer_Db($db, $dbTableName,$columnMapping);
        
        //register logger
        parent::__construct($dbWriter);
        $this->setEventItem('user_id', $user_id);
        $this->setEventItem('hostname', gethostbyaddr($_SERVER['REMOTE_ADDR']));
        $this->setEventItem('ip', $_SERVER['REMOTE_ADDR']);
    }
}