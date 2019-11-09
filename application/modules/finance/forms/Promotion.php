<?php
//require_once '../application/modules/setup/models/DbTable/Master.php';
//require_once '../application/modules/setup/models/DbTable/Market.php';
//require_once '../application/modules/setup/models/DbTable/Department.php';
//require_once '../application/modules/setup/models/DbTable/Faculty.php';
//require_once '../application/modules/setup/models/DbTable/Award.php';

class Finance_Form_Promotion extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','program_form');
		
		
		/*----------------------Master Program ID------------------------*/
		$this->addElement('text','program_master_id', array(
			'label'=>'Code',
			'required'=>'true'));
		
		$masterDB = new Setup_Model_DbTable_Promotion();
		$master_data = $masterDB->getProgram();
//		
//		$this->program_master_id->addMultiOption(0,"-- Select Program --");
//		foreach ($master_data as $list){
//			$this->program_master_id->addMultiOption($list['master_id'],$list['master_desc']);
//		}
		
			
		$this->addElement('text','program_duration', array(
			'label'=>'Description',
			'required'=>'true'));

		$this->addElement('submit', 'submit', array(
		    'label' => 'Update'
		));

	}
}
?>