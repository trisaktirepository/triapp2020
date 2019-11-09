<?php 
class Common_Helper_Utility extends Zend_Controller_Action_Helper_Abstract
{
    protected $_count = 0;
    public $plugginLoader;
    
    public function utility(){
    	$this->plugginLoader = new Zend_Loader_PluginLoader();	
    }
    
    public function formatdate($strdate)
    {
        
        
		$reg = Zend_Registry::getInstance();
	    $tranlate = $reg->get("Zend_Translate");
	    $locale = $tranlate->getLocale();
	    
	    //$locale = new Zend_Locale('id_ID');
		$date = new Zend_Date(strtotime($strdate), false, $locale);
		print $date->toString("dd MMMM yyyy h:m a");
  
        
    }
}
?>