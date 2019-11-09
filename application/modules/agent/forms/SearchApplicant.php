<?php 
class Agent_Form_SearchApplicant extends Zend_Form {

	
	public function init(){
		
		$auth = Zend_Auth::getInstance(); 
		  
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		$this->setName('search_applicant');
		$this->setMethod('post');
		$this->setAttrib('id','search_form');;

		
		$this->addElement('hidden','agent_id', array(
			'value'=>$auth->getIdentity()->id
		   
		));
		
		//intake
		$this->addElement('select','intake_id', array(
			'label'=>'Intake',
			'onchange'=>'getPeriod(this)',
		    'required'=>true
		));
		
		$intakeDb = new App_Model_Record_DbTable_Intake();
		$intakeList = $intakeDb->getData();		
    			
		$this->intake_id->addMultiOption(null,"-- Select Intake --");
		foreach ($intakeList as $list){
			$this->intake_id->addMultiOption($list['IdIntake'],$list['IntakeDefaultLanguage']);
		}
		
		
		//period
		$this->addElement('select','period_id', array(
			'label'=>'Period',
		    'required'=>false	
		
		));
		
		$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period_data = $periodDB->getData();	
    			
		$this->period_id->addMultiOption(null,"-- Select Period --");
		foreach ($period_data as $list){
			$this->period_id->addMultiOption($list['ap_id'],$list['ap_desc']);
		}
		
		//load previous period
		$this->addElement('checkbox','load_previous_period', array(
			'label'=>'Include Previous Period',
			"checked" => "checked"
		));
		
		//name
		$this->addElement('text','name', array(
			'label'=>'Applicant Name'
		));
		
		//pes no
		$this->addElement('text','pes_no', array(
			'label'=>'PES No.'
		));
		
		//application type
		$this->addElement('select','application_type', array(
			'label'=>'Application Type'
		));
		
		$this->application_type->addMultiOptions(array('0'=>'All','1'=>'USM','2'=>'PSSB'));
		
		//application status
		$this->addElement('select','application_status', array(
			'label'=>'Application Status'
		));
		$app_status = array(
						'ALL'		=> 'All',
						'APPLY'		=> 'incomplete_app',
						'CLOSE'		=> 'complete_app',
						'PROCESS'	=> 'process_app',
						'OFFER'		=> 'offer',
						'REJECT'	=> 'reject'
					  );
		
		$this->application_status->addMultiOptions($app_status);
		
		
		$this->addElement('select','entry_type', array(
			'label'=>'entry_type'
		   
		));
		
		$this->entry_type->setMultiOptions(array(""=>"All","1"=>"online_entry","2"=>"manual_entry"))		
		->setValue("")->setSeparator('&nbsp;');
		

	$this->addElement('submit', 'save', array(
          'label'=>'Submit',		  
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