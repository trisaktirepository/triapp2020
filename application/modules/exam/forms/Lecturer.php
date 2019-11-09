<?php
class Exam_Form_Lecturer extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','exam_user_form');

		$this->addElement('text','salutation', array(
			'label'=>'Salutation',
			'required'=>'true'));
		
		$this->addElement('text','name', array(
			'label'=>'Full Name',
			'required'=>'true'));
		
		//id type
		$this->addElement('select','identity_type_id', array(
			'label'=>$this->getview()->translate('Identification Type'),
			'required'=>'true'));
		$this->identity_type_id->addMultiOption(0,"-- Select Identification Type --");
		$this->identity_type_id->addMultiOption('1',"Personal ID");
		$this->identity_type_id->addMultiOption('2',"Family ID");
		$this->identity_type_id->addMultiOption('3',"Passport");
		
		//id
		$this->addElement('text','identity_id', array(
		'label'=>$this->getview()->translate('Identification Number'),
		'required'=>'true'));
		
		//country of origin
		$this->addElement('select','country_origin', array(
			'label'=>'Country of Origin',
			'inArrayValidator'=>'false'
		
		));
		
		$countryDB = new App_Model_General_DbTable_Country();
		$country_data = $countryDB->getData();
		
		$this->country_origin->addMultiOption(0,"-- Select Country of Origin --");
		foreach ($country_data as $list){
			$this->country_origin->addMultiOption($list['id'],$list['name']);
		}
		
		
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
		
		
		//status
		$this->addElement('radio', 'status', array(
            'label'      => 'Status',
            'required'   => true,
			'saperator'	=>'&nbsp',
            'multioptions'   => array(
                            '0' => 'Inactive',
                            '1' => 'Active',
                            ),
        ));
        
        $this->status->setValue(1);
			
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'exam', 'controller'=>'lecturer','action'=>'index'),'default',true) . "'; return false;"
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