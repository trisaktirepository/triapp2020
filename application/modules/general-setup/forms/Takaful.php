<?php
class GeneralSetup_Form_Takaful extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','takaful_form');
		
		$this->addElement('text','name', array(
			'label'=>'Name',
			'required'=>'true'));
		
		$this->addElement('text','shortName', array(
			'label'=>'Short Name',
			'required'=>'true'));
		
		$this->addElement('radio', 'idClienttype', array(
		    'label'=>'Client Type',
		    'multiOptions'=>array(
		        '2'=> 'Takaful Operator',
		        '3'=> 'Company / Insurance Agency',
		      ),
  		));
		
  
		
//		$this->addElement('radio','gender', array('label'=>'Gender:'));
//		$this->clienttype->addMultiOption('2','Takaful Operator');
//		$this->clienttype->addMultiOption('2','Takaful Operator');
//        $gender->setLabel('Gender:')
//            ->addMultiOptions(array(
//                    'male' => 'Male',
//                    'female' => 'Female' 
//                        ))
//            ->setSeparator('');   
		
		$this->addElement('text','contactperson', array(
			'label'=>'Contact Person',
			'required'=>'true'));
		
		$this->addElement('text','email', array(
			'label'=>'Email Address',
			'required'=>'true'));
		
		$this->addElement('text','contactno', array(
			'label'=>'Contact Number',
			'required'=>false));
		
		$this->addElement('text','loginid', array(
			'label'=>'Login ID',
			'required'=>'true'));
	
		$this->addElement('text','password', array(
			'label'=>'Password',
			'required'=>'true'));
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'takaful','action'=>'index')) . "'; return false;"
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