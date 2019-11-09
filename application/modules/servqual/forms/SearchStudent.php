<?php 

class Servqual_Form_SearchStudent extends Zend_Form
{
		
		
	public function init()
	{
						
		$this->setMethod('post');
		$this->setAttrib('id','myform');
						       
		
		
		//Program
		$this->addElement('select','IdProgram', array(
			'label'=>$this->getView()->translate('Program Name'),
		    'required'=>false
		));
		
		$programDb = new Registration_Model_DbTable_Program();
		
		$this->IdProgram->addMultiOption(null,"-- All --");		
		foreach($programDb->getData()  as $program){
			$this->IdProgram->addMultiOption($program["IdProgram"],$program["ProgramCode"].' - '.$program["ArabicName"]);
		}
				
		
		//Intake
		$this->addElement('select','IdIntake', array(
			'label'=>$this->getView()->translate('Intake'),
		    'required'=>false
		));
		
		$intakeDB = new App_Model_Record_DbTable_Intake();
		
		$this->IdIntake->addMultiOption(null,"-- All --");		
		foreach($intakeDB->fngetlatestintake() as $intake){
			$this->IdIntake->addMultiOption($intake["key"],$intake["value"]);
		}
				
	
		//Applicant Name
		$this->addElement('text','applicant_name', array(
			'label'=>$this->getView()->translate('Applicant Name')
		));
		
		//Student ID
		$this->addElement('text','student_id', array(
			'label'=>$this->getView()->translate('Student ID')
		));
		
		$this->addElement('text','at_pes_id', array(
			'label'=>$this->getView()->translate('No Formulir')
		));		
		
		
		//Status
		$this->addElement('select','profile_status', array(
			'label'=>$this->getView()->translate('Profile Status')
		));
		
		$maintenanceModelObj =  new GeneralSetup_Model_DbTable_Maintenance();
		$status = $maintenanceModelObj->fnfetchProfileStatus("a.idDefType = '20'");
		
		$this->profile_status->addMultiOption(null,"-- All --");		
		foreach($status as $s){
			$this->profile_status->addMultiOption($s["key"],$s["value"]);
		}
		
		
		//button
		$this->addElement('submit', 'Search', array(
          'label'=>$this->getView()->translate('Search'),
          'decorators'=>array('ViewHelper')
        ));
        
        
        $this->addDisplayGroup(array('Search'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));
        	    
		
        		
	}
	
	
}
?>