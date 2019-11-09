<?php
class AdmissionRecord_Bootstrap extends Zend_Application_Module_Bootstrap 
{

	protected function _initAutoload() {
		$autoloader = new Zend_Application_Module_Autoloader(array (
				'namespace'	=> 'AdmissionRecord_'	,
				'basePath'	=> APPLICATION_PATH
		));
		
		return $autoloader;
	}
}
?>