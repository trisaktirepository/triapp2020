<?php
class Application_Form_EmailTemplateDetail extends Zend_Form
{
	protected $language;
	
	public function init()
	{

		$this->setMethod('post');
		$this->setAttrib('id','form_email_template_detail');
		
		/*** hidden element ***/
		$element = new Zend_Form_Element_Hidden('etd_language');
		$element->setValue($this->academicID);
		$this->addElement($element);

		$this->addElement('text','etd_subject', array(
			'label'=>'Subject',
			'required'=>'true'));
		
		$this->addElement('textarea','etd_body', array(
			'label'=>'Code',
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
	
	public function setLanguage($language){
		$this->language = $language;
	}
}
?>