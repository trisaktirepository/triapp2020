<?php
class Application_Form_SearchRaportPssb extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAction('/application/selection-highschool-report/print-raport-pssb');
		$this->setAttrib('id','search_form')
			 ->addAttribs(array(
	        		'target'   => 'mywindow',
	        		'onSubmit' => "window.open('','mywindow', 'width=600,height=400,resizable=yes,scrollbars=yes');"
	      	 ));
		
				
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
  
		$this->addElement('select','faculty', array(
			'label'=>'Faculty',
		    'required'=>true
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