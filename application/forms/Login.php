<?php
class App_Form_Login extends Zend_Form {
	
	public function init(){
		$this->setName('login');
		$this->setMethod('post');
		$this->setAttrib('id','login_form');;
		
		/*$mytype = new Zend_Form_Element_Radio('mytype');
		$mytype	->	setLabel('login as')
				-> 	setRequired(true)
				->  setMultiOptions(array('1'=>' Usakti', '2'=>' Agent'))
				->  setValue("");*/
		
		$mytype = new Zend_Form_Element_Hidden('mytype');
		$mytype	->  setValue("2");
				
		$username = new Zend_Form_Element_Text('username');
		$username	->	setLabel($this->getView()->translate('username'))
					-> 	setRequired(true);
					
		$password = new Zend_Form_Element_Password('password');
		$password 	->	setLabel($this->getView()->translate('password'))
					->	setRequired(true);
		
		$signin = new Zend_Form_Element_Submit('signin');
		$signin		-> 	setLabel($this->getView()->translate('login'))
					->  setIgnore(true)
					->	setAttrib('id','signinbutton');
					
		$this->addElements(array($username, $password, $mytype, $signin));
		$this->setMethod('post');
		
	}
}
?>