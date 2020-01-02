<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
	protected function _initAutoload() {
		
		$moduleLoader = new Zend_Application_Module_Autoloader(array (
				'namespace'	=> 'App_'	,
				'basePath'	=> APPLICATION_PATH));
		
		return $moduleLoader;
	}
	
	
	protected function _initViewHelpers() {
		
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		 
		 		
		$view->doctype ('XHTML1_TRANSITIONAL');
		$view->headMeta()->appendHttpEquiv ('Content-Type','text/html;charset=utf-8');
		$view->headMeta()->appendHttpEquiv ('Cache-control','no-cache');
		$view->headMeta()->appendHttpEquiv ('Pragma','no-cache');
		$view->headTitle()->setSeparator (' - ');
		$view->headTitle(APPLICATION_ENTERPRISE_SHORT ." - ". APPLICATION_TITLE_SHORT);
		$view->headLink(array('rel' => 'icon',
                                  'href' => '/images/favicon.gif'),
                                  'PREPEND');
		
		$view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');	

		//$utility = new Common_Helper_Utility();
		//$view->registerHelper($utility, 'utility');
		Zend_Controller_Action_HelperBroker::addPrefix('Common_Helper');
		//Zend_Controller_Action_HelperBroker::addHelper($utility);
		
	}
	
	protected function setconstants($constants){
        foreach ($constants as $key=>$value){
            if(!defined($key)){
                define($key, $value);
            }
        }
	}
	
	protected function _initTranslate(){
		$registry = Zend_Registry::getInstance();	
		
		 // Create Session block and save the locale
        $session = new Zend_Session_Namespace('session');  
       
       
		$locale = new Zend_Locale('id_ID');		
		$file = APPLICATION_PATH . DIRECTORY_SEPARATOR .'languages'. DIRECTORY_SEPARATOR . "id_ID.php";
			
						
		$translate = new Zend_Translate('array',
            $file, $locale,
            array(
            'disableNotices' => true,    // This is a very good idea!
            'logUntranslated' => false,  // Change this if you debug
            )
        );
        
		        
        $registry->set('Zend_Locale', $locale);
        $registry->set('Zend_Translate', $translate);
             
      
        
        return $registry;
	}
	
	protected function _initPlugin(){
		$fc = Zend_Controller_Front::getInstance();
        $fc->registerPlugin(new icampus_Plugin_LangSelector());        
       
	}
	
	
	protected function _initConfig()
    {
    
        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);
       
    }
    
	protected function _initLoadAclIni ()
	{
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/acl.ini');
		Zend_Registry::set('acl', $config);
		
	}
	
	protected function _initAuth() {
		/*
         * ACL
         */
		
		//All Module & Controller
		$acl = array();
    	$front = Zend_Controller_Front::getInstance();
    	
		foreach ($front->getControllerDirectory() as $module => $path) {
				//echo  $path;
                foreach (scandir($path) as $file) {

                        if (strstr($file, "Controller.php") !== false) {

                                include_once $path . DIRECTORY_SEPARATOR . $file;

                                foreach (get_declared_classes() as $class) {

                                        if (is_subclass_of($class, 'Zend_Controller_Action')) {

                                                $controller = strtolower(substr($class, 0, strpos($class, "Controller")));
                                                $actions = array();
                                               
                                                foreach (get_class_methods($class) as $action) {

                                                        if (strstr($action, "Action") !== false) {
                                                        		//echo $action;
                                                        		$action = str_replace("Action", "", $action);
                                                        		//echo var_dump($action);exit;
                                                        		$actions[] = $action;
                                                        		//echo $action;echo var_dump($actions);exit;
                                                        }
                                                       // 
                                                }
                                            //   echo var_dump($actions);echo "==";
                                        }
                                         
                                }
                               
                                $acl[$module][$controller] = $actions;
                               
                        }
                       
                       // echo $file;echo '<br>';
                }
                //echo '------------';
               // $coun++;
               // echo var_dump($acl);  
               //exit;
               
                
   		}
   		 //echo 'ok';
   		//exit;

        $acl = new icampus_Plugin_Acl($acl);
        
		/*
		 * AUTH
		 */
      
		$auth = Zend_Auth::getInstance();
        $fc = Zend_Controller_Front::getInstance();
        
        //$fc->registerPlugin(new icampus_Plugin_Auth($auth,null));
        $fc->registerPlugin(new icampus_Plugin_Auth( $auth, $acl ));
         
	}
	
	protected function _initACLLayout(){
		$auth = Zend_Auth::getInstance();
		
		if ($auth->hasIdentity()) {
			$front = Zend_Controller_Front::getInstance(); 
        	$front->registerPlugin(new icampus_Plugin_Layout()); 
			$front->registerPlugin(new icampus_Plugin_Survey());
			$front->registerPlugin(new icampus_Plugin_Studentfinance());
		}
	}
	

	protected function _initDomPdf(){
		//set_include_path(APPLICATION_PATH . "/../../library/dompdf/" . PATH_SEPARATOR . get_include_path());
		set_include_path("/var/www/html/triapp/library/dompdf/" . PATH_SEPARATOR . get_include_path());
		//set_include_path("/var/www/html/triapp_trisakti/library/dompdf/" . PATH_SEPARATOR . get_include_path());
		
	}
   
    
	protected function _initPhpqrcode(){
		set_include_path("/var/www/html/triapp/library/phpqrcode/" . PATH_SEPARATOR . get_include_path());
	
	}
	
}
 
include('common_functions.php');
