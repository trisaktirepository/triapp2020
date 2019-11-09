
<?php 

class Application_Form_OfferLetterItem extends Zend_Form
{
	protected $programID;
	
	public function init()
	{
		$programDB = new App_Model_Record_DbTable_Program();
		$programList = $programDB->getData();
		
		$courseTypeDB = new App_Model_Application_DbTable_EducationLevel();
		
		$conditionDB = new App_Model_Application_DbTable_Condition();
		
		
		$this->setMethod('post');
		$this->setAttrib('id','detailForm');
		$this->setAttrib('action',$this->getView()->url(array('module'=>'application', 'controller'=>'entry-requirement','action'=>'adddetail', 'id'=>$this->programID)) );			
		
		
		
		/*** Description Requirement ***/
		$this->addElement('textarea','desc', array(
		'label'=>'Description',
		'required'=>'true'));

		/*** Course Type ***/
		
		
		$this->addElement('select','entry_id', array(
			'label'=>'Education Level',
			'required'=>'true'));
		
		$this->entry_id->addMultiOption(0,"-- Select Award Level --");
		foreach ($courseTypeDB->getProgram() as $list){
			$this->entry_id->addMultiOption($list['sc001_name'],$list['sc001_name']);
		}
		
		
		
//		$element = new Zend_Form_Element_Select('entry_id');
//		$element	->setLabel('Education Level')
//					->setRequired('true')
//					->addMultiOption(null,"--Select Award Level--");
//		foreach ($courseTypeDB->getProgram() as $list){
//			$element->addMultiOption($list['sc001_name'],$list['sc001_name']);
//		}				
//		$this->addElement($element);
		
		$this->addElement('text','item', array(
		'label'=>'Item',
		'required'=>'true'));
		
		
		/*** Condition Type ***/
		$element = new Zend_Form_Element_Select('condition');
		$element	->setLabel('Condition')
					->setRequired('true')
					->addMultiOption(null,"--Select--");
		foreach ($conditionDB->getData() as $list){
			$element->addMultiOption($list['symbol'],$list['condition_name']);
		}				
		$this->addElement($element);
		
		/*** Min Credit ***/
		$this->addElement('text','status', array(
		'label'=>'Result/Grade',
		'required'=>'true'));
		
		$this->addElement('checkbox','compulsory', array(
			'label'=>'Compulsory?',
			'required'=>'true'));

		
		
		//button
		/*$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'setup', 'controller'=>'program-requirement','action'=>'view', 'program-id'=>$this->programID)) . "'; return false;"
        ));
        
        $this->addDisplayGroup(array('save','cancel'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));*/
	}
	
	public function setProgramID($programID){
		$this->programID = $programID; 
	}
}
?>