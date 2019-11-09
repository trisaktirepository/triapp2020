<?php
class Application_Form_PlacementTestProgramComponent extends Zend_Form
{
	
	public function init()
	{
	
		$this->setMethod('post');
		$this->setAttrib('id','placementTestProgramComponent');

		$this->addElement('select','apps_comp_code', array(
			'label'=>'Component',
			'required'=>true));
		
		$this->apps_comp_code->addMultiOption(null,"Select");
		$componentDb = new App_Model_Application_DbTable_PlacementTestComponent();
		foreach ($componentDb->getData() as $list){
			$this->apps_comp_code->addMultiOption($list['ac_comp_code'],$list['ac_comp_name']);
		}
		
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'application', 'controller'=>'placement-test-location','action'=>'index'),'default',true) . "'; return false;"
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