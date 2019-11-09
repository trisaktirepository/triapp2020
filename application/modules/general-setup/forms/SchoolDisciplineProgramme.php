<?php

class GeneralSetup_Form_SchoolDisciplineProgramme extends Zend_Form
{
	protected $year;
	
	public function setYear($year){
		$this->year = $year;
	}
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','form_sds');
		
		$academicYear= new Zend_Form_Element_Select('apr_academic_year',array(
			'label' => 'Academic Year',
			'required' => true
		));
		
		$academicYearDb = new App_Model_Record_DbTable_AcademicYear();
		$programList = $academicYearDb->getData();
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		if ($locale=="en_US"){	
			foreach ($programList as $list){
				$academicYear->addMultiOption($list['ay_id'],$list['ay_code']);
			}	
		}else if ($locale=="id_ID"){
			foreach ($programList as $list){
				$academicYear->addMultiOption($list['ay_id'],$list['ay_code']);
			}
		}
		
		$academicYear->setValue($this->year);
		
		$this->addElement($academicYear);
		
		
		
		/*** PROGRAMME **/
		$programme= new Zend_Form_Element_Multiselect('apr_program_code',array(
			'label' => 'Program',
			'required' => true
		));
		
		$programDb = new App_Model_Record_DbTable_Program();
		$programList = $programDb->getData();
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		if ($locale=="en_US"){	
			foreach ($programList as $list){
				$programme->addMultiOption($list['ProgramCode'],$list['ProgramName']);
			}	
		}else if ($locale=="id_ID"){
			foreach ($programList as $list){
				$programme->addMultiOption($list['ProgramCode'],$list['ArabicName']);
			}
		}
		
		$this->addElement($programme);
		
	
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'highschool-discipline-subject','action'=>'index'),'default',true) . "'; return false;"
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