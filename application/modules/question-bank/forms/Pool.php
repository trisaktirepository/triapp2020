<?php
class QuestionBank_Form_Pool extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','university_form');

		$this->addElement('text','name', array(
			'label'=>'Question Pool Name',
			'id'=>'name',
			'required'=>'true'));
			
		$this->addElement('textarea','desc', array(
			'label'=>'Description'));
		
		$this->addElement('radio', 'status', array(
            'label'      => 'Is Active?',
            'required'   => true,
            'multioptions'   => array(
                            '0' => 'No',
                            '1' => 'Yes',
                            ),
        ));
        $this->status->setSeparator('')->setValue('1');
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'question-bank', 'controller'=>'pool','action'=>'index'),'default',true) . "'; return false;"
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