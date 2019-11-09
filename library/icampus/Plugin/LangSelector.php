<?php

class icampus_Plugin_LangSelector extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $registry = Zend_Registry::getInstance();
        // Get our translate object from registry.
        $translate = $registry->get('Zend_Translate');
        $currLocale = $translate->getLocale();
        
         // Create Session block and save the locale
         $session = new Zend_Session_Namespace('session');  
	     $session->lang;	
        
         $lang = $request->getParam('lang','');
   
        // Register all your "approved" locales below.
        switch($lang) {
            case "ar":
                $langLocale = 'ar_YE'; break;  
            case "en":
                $langLocale = 'en_US'; break;
            case "bm":
                $langLocale = 'ms_MY'; break;   
            case "id":
                $langLocale = 'id_ID'; break;    
            default:
                /**
                 * Get a previously set locale from session or set
                 * the current application wide locale (set in
                 * Bootstrap)if not.
                 */
                $langLocale = isset($session->lang) ? $session->lang : $currLocale;
              
        }
        
         //set new session
        $session->lang = $langLocale;
        
    	switch($session->lang){
			case "ar_YE": 
							$locale = new Zend_Locale('ar_YE');
	       				    $file = APPLICATION_PATH . DIRECTORY_SEPARATOR .'languages'. DIRECTORY_SEPARATOR . "ar_YE.php";
			
							break;
			case "en_US":  
							$locale = new Zend_Locale('en_US');		
							$file = APPLICATION_PATH . DIRECTORY_SEPARATOR .'languages'. DIRECTORY_SEPARATOR . "en_US.php";
			
							break;
							
			case "ms_MY":  
							$locale = new Zend_Locale('ms_MY');		
							$file = APPLICATION_PATH . DIRECTORY_SEPARATOR .'languages'. DIRECTORY_SEPARATOR . "ms_MY.php";
			
							break;	
			case "id_ID":  
							$locale = new Zend_Locale('id_ID');		
							$file = APPLICATION_PATH . DIRECTORY_SEPARATOR .'languages'. DIRECTORY_SEPARATOR . "id_ID.php";
			
							break;							
			
		}

		$translate = new Zend_Translate('array',
            $file, $locale,
            array(
            'disableNotices' => true,    // This is a very good idea!
            'logUntranslated' => false,  // Change this if you debug
            )
        );
      
        $newLocale = new Zend_Locale();
        $newLocale->setLocale($langLocale);
        $registry->set('Zend_Locale', $newLocale);
		
        $translate->setLocale($langLocale);
        // Save the modified translate back to registry
        $registry->set('Zend_Translate', $translate);
    }
}