<?php
class Cms_PriorityCheck extends Zend_Validate_Abstract {
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => "For Program Priority Already Exists",
    );

    public function isValid($value, $context = null) {
    	if(Application_Model_DbTable_Programchecklist::checkpriority($value,Zend_Controller_Front::getInstance()->getRequest()->getPost())){
      	return true;
      }else{
        $this->_error(self::NOT_MATCH);
        return false;
      }

    }

}

?>