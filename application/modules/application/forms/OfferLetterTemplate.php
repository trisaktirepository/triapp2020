<?php
class Application_Form_OfferLetterTemplate extends Zend_Form
{
	
	public function init()
	{
	
?>

<script>
	$(function() {
		$( "#DATE" ).datepicker();
	});
	</script>
	
<?

		$this->setMethod('post');
		$this->setAttrib('id','offerlettertemplate_form');
		
		$this->addElement('text','name', array(
		'label'=>'Template Name',
		'required'=>'true'));
		
		$this->addElement('text','name', array(
		'label'=>'Template Name',
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