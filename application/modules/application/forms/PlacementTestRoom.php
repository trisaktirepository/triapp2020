<?php
class Application_Form_PlacementTestRoom extends Zend_Form
{
	
	public function init()
	{
	
		$this->setMethod('post');
		$this->setAttrib('id','placementTestRoom');

		$this->addElement('text','av_room_name', array(
			'label'=>'Name',
			'required'=>true));
		
		$this->addElement('text','av_room_name_short', array(
			'label'=>'Name Short',
			'required'=>true));
		
		$this->addElement('text','av_room_code', array(
			'label'=>'Code',
			'required'=>true));
		
		$this->addElement('text','av_building', array(
			'label'=>'Building'));
		
		$this->addElement('text','av_tutorial_capacity', array(
			'label'=>'Tutorial Capacity'));
		
		$this->addElement('text','av_exam_capacity', array(
			'label'=>'Exam Capacity',
			'required' => true
		));
		$this->av_exam_capacity->addValidator('digits', true);
		
		$this->addElement('multiselect','av_test_type', array(
			'label'=>'Test Type'
		));
		
		$rooTestTypeDb = new App_Model_Application_DbTable_PlacementTestRoomTestType();
		foreach ($rooTestTypeDb->getData() as $list){
			$this->av_test_type->addMultiOption($list['artt_id'],$list['artt_name']);
		}
		
		$this->addElement('text','av_seq', array(
			'label'=>'Room Filling Prioriy'));
		
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