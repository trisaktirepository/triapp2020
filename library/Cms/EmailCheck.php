<?php
class Cms_EmailCheck extends Zend_Validate_Abstract {
    const NOT_MATCH = 'notMatch';
	
    protected $_messageTemplates = array(
        self::NOT_MATCH => "Email Already Exist",
    );

    public function isValid($value, $context = null) {
      if(App_Model_Applicant::checkemail($value,Zend_Controller_Front::getInstance()->getRequest()->getPost())){
    		return true;
      }else{
        $this->_error(self::NOT_MATCH);
        return false;
      }

    }

}

?>