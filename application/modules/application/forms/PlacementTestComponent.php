<?php
class Application_Form_PlacementTestComponent extends Zend_Form
{
	
	public function init()
	{
	
		$this->setMethod('post');
		$this->setAttrib('id','placementTestComponent');

		
		$this->addElement('text','ac_comp_code', array(
			'label'=>'Component Code',
			'required'=>true));
		
		$this->addElement('text','ac_comp_name', array(
			'label'=>'Component Name',
			'required'=>true));
		
		$this->addElement('text','ac_comp_name_bahasa', array(
			'label'=>'Component Name (Indonesia)',
			'required'=>true));
		
		$this->addElement('text','ac_short_name', array(
			'label'=>'Short Name',
			'required'=>true));
		
		
		$this->addElement('select','ac_test_type', array(
			'label'=>'Component Type',
			'required'=>'true'));
		
		$this->ac_test_type->addMultiOption(null,"Select");
		$placementTestTypeDb = new App_Model_Application_DbTable_PlacementTestType();
		foreach ($placementTestTypeDb->getData() as $list){
			$this->ac_test_type->addMultiOption($list['act_id'],$list['act_name']);
		}
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'application', 'controller'=>'placement-test-component','action'=>'index'),'default',true) . "'; return false;"
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