<?php
class Application_Form_SelectionSearch extends Zend_Form
{
	protected $_facultyid;
	
	public function setFacultyid($facultyid) {
		$this->_facultyid = $facultyid;
	}
	
	public function init()
	{
		$this->setMethod('post');		
		$this->setAttrib('id','search_form');
				
  
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
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
    	$period_data = $periodDB->getData($this->_facultyid);	
    			
		$this->period->addMultiOption(0,"-- Select Period --");
		foreach ($period_data as $list){
			$this->period->addMultiOption($list['ap_id'],$list['ap_desc']);
		}
		
		$this->addElement('select','faculty', array(
			'label'=>'Faculty',
		    'onChange'=>"getProgramme(this,$('#programme'))"
		
		));
		
		$collegeDB = new App_Model_General_DbTable_Collegemaster();
		$college_data = $collegeDB->getData();		
    			
		$this->faculty->addMultiOption(0,"-- Select Faculty --");
		foreach ($college_data as $list){
			if($locale=="id_ID"){
				$college_name = $list["ArabicName"];
			}elseif($locale=="en_US"){
				$college_name = $list["CollegeName"];
			}
			$this->faculty->addMultiOption($list['IdCollege'],strtoupper($college_name));
		}
		
		
		
		$this->addElement('select','programme', array(
			'label'=>'Programme'		
		
		));
		
		$programDB = new App_Model_Record_DbTable_Program();
    	$program_data = $programDB->searchProgramByFaculty($this->_facultyid);    	

			
		$this->programme->addMultiOption(0,"-- Select Program --");
		$this->programme->addMultiOption('',"All");
		foreach ($program_data as $list){
			
			if($locale=="id_ID"){
				$program_name = $list["ArabicName"];
			}elseif($locale=="en_US"){
				$program_name = $list["ProgramName"];
			}
			
			
			$this->programme->addMultiOption($list['ProgramCode'],$program_name);
		}
		
		
		
		
		
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