<?php 

class Servqual_Form_SurveyResult extends Zend_Form
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
		
		//semester
		$this->addElement('select','IdSemester', array(
				'label'=>$this->getView()->translate('Semester Name'),
				'required'=>false
		));
		
		$programDb = new GeneralSetup_Model_DbTable_Semestermaster();
		
		$this->IdSemester->addMultiOption(null,"-- All --");
		foreach($programDb->getCountableSemester()  as $program){
			$this->IdSemester->addMultiOption($program["key"],$program["value"]);
		}
		//survey
		
		$this->addElement('select','IdSurvey', array(
				'label'=>$this->getView()->translate('Survey Name'),
				'required'=>false
		));
		
		$programDb = new Servqual_Model_DbTable_Survey();
		
		$this->IdSurvey->addMultiOption(null,"-- All --");
		foreach($programDb->getData()  as $program){
			$this->IdSurvey->addMultiOption($program["IdSurvey"],$program["SurveyName"].'-'.$program['surveytype']);
		}
		
		//button
		
		$this->addElement('submit', 'Calculate', array(
          'label'=>$this->getView()->translate('Calculate'),
          'decorators'=>array('ViewHelper')
        ));
        
        
        $this->addDisplayGroup(array('Calculate'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));
        	    
		
        		
	}
	
	
}
?>