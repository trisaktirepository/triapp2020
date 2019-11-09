<?php
class Application_Form_PlacementTestHead extends Zend_Form
{
	
	public function init()
	{

		$this->setMethod('post');
		$this->setAttrib('id','form_placement_test');

		$this->addElement('text','aph_placement_name', array(
			'label'=>'Placement Test Name',
			'required'=>'true'));
		
		$this->addElement('text','aph_placement_code', array(
			'label'=>'Code',
			'required'=>'true'));
		
		$this->addElement('select','aph_academic_year', array(
			'label'=>'Academic year',
			'required'=>'true'
		));
		$this->aph_academic_year->addMultiOption(null,"Select");
		$academicYearDb = new App_Model_Record_DbTable_AcademicYear();
		foreach ($academicYearDb->getData() as $list){
			$this->aph_academic_year->addMultiOption($list['ay_id'],$list['ay_code']);
		}
		
		$this->addElement('select','aph_batch', array(
			'label'=>'Batch',
			'required'=>'true'
		));
		$this->aph_batch->addMultiOption(null,"Select");
		$academicYearDb = new App_Model_Record_DbTable_AcademicPeriod();
		foreach ($academicYearDb->getData() as $list){
			$this->aph_batch->addMultiOption($list['ap_id'],$list['ap_code']);
		}
		
		
		$this->addElement('radio','aph_fees_program', array(
			'label'=>'Fees By Program'
		));
		$this->aph_fees_program->setMultiOptions(array('1'=>' Yes', '0'=>' No'))->setValue("0")->setSeparator('');
		
		$this->addElement('radio','aph_fees_location', array(
			'label'=>'Fees By Location'
		));
		$this->aph_fees_location->setMultiOptions(array('1'=>' Yes', '0'=>' No'))->setValue("0")->setSeparator('');
		
		$this->addElement('text','aph_start_date', array(
			'label'=>'Start Date',
			'id'=>'start',
			'class'=>'datepicker',
			'placeholder'=>'dd-mm-yyyy',
			'required'=>'true'));
		
		$this->addElement('text','aph_end_date', array(
			'label'=>'End Date',
			'id'=>'end',
			'class'=>'datepicker',
			'placeholder'=>'dd-mm-yyyy',
			'required'=>'true'));
		
		$this->addElement('text','aph_effective_date', array(
			'label'=>'Effective Date',
			'class'=>'datepicker',
			'placeholder'=>'dd-mm-yyyy',
			'required'=>'true'));
			
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'application', 'controller'=>'placement-test','action'=>'index'),'default',true) . "'; return false;"
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