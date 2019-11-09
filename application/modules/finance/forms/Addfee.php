<?php
//require_once '../application/modules/setup/models/DbTable/Master.php';
//require_once '../application/modules/setup/models/DbTable/Market.php';
//require_once '../application/modules/setup/models/DbTable/Department.php';
//require_once '../application/modules/setup/models/DbTable/Faculty.php';
//require_once '../application/modules/setup/models/DbTable/Award.php';

class Finance_Form_Addfee extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','program_form');
		
		
		/*----------------------Master Program ID------------------------*/
			
		$masterDB = new Finance_Model_DbTable_Typefee();
		$master_data = $masterDB->getProgram();

		$this->addElement('text','txtitem', array(
			'label'=>'Item',
			'required'=>'true'));
			
		$this->addElement('text','txtdesc', array(
			'label'=>'Description',
			'required'=>'true'));
			
		$this->addElement('text','txtamt', array(
			'label'=>'Amount',
			'required'=>'true'));

		$this->addElement('submit', 'submit', array(
		    'label' => 'Update'
		));

	}
}
?>