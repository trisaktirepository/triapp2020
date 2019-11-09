<?php
//require_once 'Zend/Controller/Action.php';

class LanguageController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {       	
          // action body
        $oLang = new App_Model_Exam_DbTable_Lang();
        $list = $oLang->getData();    
               
        //test
        $view = new Zend_View();    
        $view->a = "test" ;   
        $view->list = $list;
             
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR .'languages';        
        $view->setScriptPath($path);
        echo $view->render('ar_YE.php');		
      }

}

?>