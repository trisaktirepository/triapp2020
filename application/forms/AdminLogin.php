<?php
class App_Form_AdminLogin extends Zend_Form {
	
	public function init(){
		$this->setName('login');
		$this->setMethod('post');
		$this->setAttrib('id','login_form');;
		$this->setMethod('post');
		
		$username = new Zend_Form_Element_Text('username');
		$username	->	setLabel('Admin Username')
					-> 	setRequired(true);
					
		$password = new Zend_Form_Element_Password('password');
		$password 	->	setLabel('Password')
					->	setRequired(true);
					
		$stusername = new Zend_Form_Element_Hidden('stusername');
		$stusername	->	setRequired(true);					
		
		/*$signin = new Zend_Form_Element_Submit('signin');
		$signin		-> 	setLabel('Sign In')
					->  setIgnore(true)
					->	setAttrib('id','signinbutton');*/

		$this->addElements(array($username, $password,$stusername));
					
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Login',
          'decorators'=>array('ViewHelper')
        ));
        
        
        
        $this->addDisplayGroup(array('save','fpwd'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));
		
	}
}
?>