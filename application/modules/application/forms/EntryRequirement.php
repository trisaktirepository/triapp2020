<?php
class Application_Form_EntryRequirement extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','entry_form');
		
		
		/*----------------------Master Program ID------------------------*/
		$this->addElement('select','id_program', array(
			'label'=>'Program',
			'required'=>'true'));
		
		$programDB = new App_Model_Record_DbTable_Program();
		$master_data = $programDB->getData();
		
		$this->id_program->addMultiOption(0,"-- Select Program --");
		foreach ($master_data as $list){
			$this->id_program->addMultiOption($list['id'],$list['program_name']);
		}
		
		/*$this->addElement('select','id_program', array(
			'label'=>'Program',
			'required'=>'true'));
		
		$masterDB = new App_Model_Record_DbTable_Program();
		$master_data = $masterDB->getData();
		
		$this->id_program->addMultiOption(0,"-- Select Program --");
		foreach ($master_data as $list){
			$this->id_program->addMultiOption($list['id'],$list['main_name']);
		}*/
		
		
		/*----------------------Market------------------------------------*/
		/*$this->addElement('select','market_id', array(
			'label'=>'Market',
			'required'=>'true'));
		
		$marketDB = new App_Model_Record_DbTable_Market();
		$market_data = $marketDB->getData();
		
		$this->market_id->addMultiOption(0,"-- Select Market --");
		foreach ($market_data as $list){
			$this->market_id->addMultiOption($list['id'],$list['name']);
		}*/
			
		
			
		$this->addElement('text','entry_name', array(
			'label'=>'Entry Name',
			'required'=>'true'));
		
		$this->addElement('checkbox','status', array(
			'label'=>'Active?',
			'required'=>'true'));

		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'application', 'controller'=>'entry-requirement','action'=>'index'),'default',true) . "'; return false;"
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