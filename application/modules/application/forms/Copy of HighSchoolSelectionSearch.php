<?php
class Application_Form_HighSchoolSelectionSearch extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAction('/application/selection-highschool-report/print-student-view');
		$this->setAttrib('id','search_form')
		//$this->setAttrib('target', 'mywindow');
		->addAttribs(array(
         'target'   => 'mywindow',
         'onSubmit' => "mywindow = window.open('about:blank','mywindow', 'width=600,height=400');"
  
     ));
		
		
		
		
		$this->addElement('select','academic_year', array(
			'label'=>'Academic Year',
		    'required'=>true
		));
		
		$academicDB = new App_Model_Record_DbTable_AcademicYear();
		$academic_year_data = $academicDB->getData();		
    			
		$this->academic_year->addMultiOption(0,"-- Select Academic Year --");
		foreach ($academic_year_data as $list){
			$this->academic_year->addMultiOption($list['ay_id'],$list['ay_code']);
		}
		
		
		
		
		$this->addElement('select','period', array(
			'label'=>'Period',
		    'required'=>true	
		
		));
		
		$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period_data = $periodDB->getData();	
    			
		$this->period->addMultiOption(0,"-- Select Period --");
		foreach ($period_data as $list){
			$this->period->addMultiOption($list['ap_id'],$list['ap_desc']);
		}
		
		
		
		/*
		$this->addElement('select','programme', array(
			'label'=>'Programme'		
		
		));
		
		$programDB = new App_Model_Record_DbTable_Program();
    	$condition["IdCollege"]=7; //for now set default in future patut login based on user/dean tagged to faculty
    	$program_data = $programDB->searchProgram($condition);
    	
    			
		$this->programme->addMultiOption('',"-- All --");
		foreach ($program_data as $list){
			$this->programme->addMultiOption($list['ProgramCode'],$list['ProgramName']);
		}
		
		*/
		
		
		
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
		  'onclick'=>"openList()",
          'decorators'=>array('ViewHelper')
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