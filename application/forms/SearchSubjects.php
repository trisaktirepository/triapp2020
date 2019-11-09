<?php
class App_Form_SearchSubjects extends Zend_Form {
	
	public function init(){
		
		$this->setName('serach');
		$this->setMethod('post');
		$this->setAttrib('id','form1');
		
		//Intake
		$this->addElement('select','IdSemester', array(
			'label'=>$this->getView()->translate('Semester'),
		    'required'=>true
		));
		
		
		$semesterDB = new App_Model_General_DbTable_Semestermaster();
		
		$this->IdSemester->addMultiOption(null,"-- Please Select --");		
		foreach($semesterDB->fnGetSemestermasterList() as $semester){
			$this->IdSemester->addMultiOption($semester["key"],$semester["value"]);
		}
	
			
		
		//button
		$this->addElement('submit', 'Search', array(
          'label'=>'Search',
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