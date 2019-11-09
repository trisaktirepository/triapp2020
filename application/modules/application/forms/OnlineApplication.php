<?php
class Application_Form_OnlineApplication extends Zend_Form
{
	
	public function init()
	{
	
?>

<script>
	$(function() {
		$( "#datepicker" ).datepicker();
	});
	</script>
	
<?

		$this->setMethod('post');
		$this->setAttrib('id','onlineapplication_form');

		/*----------------------Master Programme ID------------------------*/
		$this->addElement('select','ID_PROG', array(
			'label'=>'Market Programme',
			'required'=>'true'));
		
		$masterDB = new App_Model_Record_DbTable_MainProgram();
		$master_data = $masterDB->getData();
		
		$this->ID_PROG->addMultiOption(0,"-- Select Programme --");
		foreach ($master_data as $list){
			$this->ID_PROG->addMultiOption($list['id'],$list['name']);
		}
		
		/*----------------------Date------------------------------------*/
		$this->addElement('text','datepicker', array(
			'label'=>'Date',
			'required'=>'true'));
		
		/*----------------------Venue------------------------------------*/
		$this->addElement('select','VENUE', array(
			'label'=>'Venue',
			'required'=>'true'));
		
		$branchDB = new App_Model_General_DbTable_Branch();
		$branch_data = $branchDB->getData();
		
		$this->VENUE->addMultiOption(0,"-- Select Venue --");
		foreach ($branch_data as $list){
			$this->VENUE->addMultiOption($list['id'],$list['name']);
		}
			
		
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