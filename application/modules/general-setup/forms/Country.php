<?php
class GeneralSetup_Form_Country extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','country_form');

		$this->addElement('text','name', array(
			'label'=>'Country Name',
			'id'=>'title',
			'required'=>'true'));
		
		$this->addElement('text','code', array(
			'label'=>'ISO country code'));
		
		$this->addElement('text','iso3', array(
			'label'=>'ISO3 country code'));
		
		$this->addElement('radio', 'arab_continent', array(
            'label'      => 'Is Arab Country?',
            'required'   => true,
            'multioptions'   => array(
                            '0' => 'No',
                            '1' => 'Yes',
                            ),
        ));
		

		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'country','action'=>'index')) . "'; return false;"
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