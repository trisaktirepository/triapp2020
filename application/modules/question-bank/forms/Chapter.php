<?php
class QuestionBank_Form_Chapter extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','form1');
		
	/*----------------------Faculty------------------------------------*/
		$this->addElement('select','idPool', array(
			'label'=>'Question Pool',
			'required'=>'true'));
		
		$poolDB = new App_Model_Question_DbTable_Pool();
		$poolData = $poolDB->getData();
		
		$this->idPool->addMultiOption(0,"-- Please select --");
		foreach ($poolData as $list){
			$this->idPool->addMultiOption($list['id'],$list['name']);
		}
		
		$this->addElement('text','name', array(
			'label'=>'Name',
			'id'=>'name',
			'required'=>'true'));
			
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'question-bank', 'controller'=>'chapter','action'=>'index'),'default',true) . "'; return false;"
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