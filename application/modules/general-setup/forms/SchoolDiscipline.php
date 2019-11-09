<?php

class GeneralSetup_Form_SchoolDiscipline extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','form_sd');
		
		
		$this->addElement('text','smd_code', 
			array(
				'label'=>'Discipline Code',
				'required'=>'true',
				'maxlength'=>'3'
			)
		);
		
		$this->addElement('text','smd_desc', 
			array(
				'label'=>'Discipline Name',
				'required'=>'true'
			)
		);
		
		$this->addElement('select','smd_school_type', 
			array(
				'label'=>'School Type',
				'required'=>'true'
			)
		);
		$schoolTypeDb = new App_Model_General_DbTable_SchoolType();
		$listData = $schoolTypeDb->getData();
		$this->smd_school_type->addMultiOption(null,"Please Select");
		foreach ($listData as $list){
			$this->smd_school_type->addMultiOption($list['st_id'],$list['st_name']." (".$list['st_code'].")");
		}

			
		
	
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'highschool-discipline','action'=>'index'),'default',true) . "'; return false;"
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