<?php
class Exam_Form_Examiner extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','exam_user_form');

		$this->addElement('text','name', array(
			'label'=>'Full Name',
			'required'=>'true'));
		
		$this->addElement('text','email', array(
			'label'=>'Email',
			'required'=>'true',
			'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
            )
        ));
		
		//BRANCH
		$branchDB = new App_Model_General_DbTable_Branch();
		$branch_data = $branchDB->getData();
		
		$element = new Zend_Form_Element_Select('branch_id');
		$element	->setLabel('Branch')
					->addMultiOption(0,"USTY HQ");
		foreach ($branch_data as $list){
			$element->addMultiOption($list['id'],$list['name']);
		}		
 
		$this->addElement($element);
		
			
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'exam', 'controller'=>'user-setup','action'=>'user'),'default',true) . "'; return false;"
        ));
        
        $this->addDisplayGroup(array('save','cancel'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));
		
	}
}
?>