<?php
class SystemSetup_Form_User extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','user_form');
		
		$this->addElement('text','fullname', array(
			'label'=>'Fullname',
			'required'=>'true'));
		
		$this->addElement('text','username', array(
			'label'=>'User Name',
			'required'=>'true'));
		
		$this->addElement('text','staff_id', array(
			'label'=>'Staff ID'));
		
		$this->addElement('select','role_id', array(
			'label'=>'User Role',
			'required'=>'true'));
			
		
			
		$roleDB = new SystemSetup_Model_DbTable_Role();
		$roles = $roleDB->getData();
		
		$this->role_id->addMultiOption(null,"-- Select Role --");
		foreach ($roles as $role){
			$this->role_id->addMultiOption($role['id'],$role['name']);
		}

		//button
		/*$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'system-setup', 'controller'=>'user','action'=>'index')) . "'; return false;"
        ));
        
        $this->addDisplayGroup(array('save','cancel'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));*/
		
	}
}
?>