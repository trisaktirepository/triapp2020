<?php
class App_Form_Loginpin extends Zend_Form {
	
	public function init(){
		
		$this->setName('login');
		$this->setMethod('post');
		
		$this->setAttrib('id','login_form');
		
		$username = new Zend_Form_Element_Text('billingno');
		$username	->	setLabel('billingno')
					-> 	setRequired(true);
					
		$password = new Zend_Form_Element_Password('pinno');
		$password 	->	setLabel('pinno')
					->	setRequired(true);
		
		$signin = new Zend_Form_Element_Submit('signin');
		$signin		-> 	setLabel('submit')
					->  setIgnore(true)
					->	setAttrib('id','signinbutton');
					
		$this->addElements(array($username, $password, $signin));
		$this->setMethod('post');
		
	}
}
?>