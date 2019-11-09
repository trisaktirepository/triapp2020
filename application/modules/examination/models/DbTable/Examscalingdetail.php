<?php

class Examination_Model_DbTable_Examscalingdetail extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_exam_scaling_setup_detail';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnaddscalesetpdetail($larrformData) { //Function for adding the user details to the table
		$this->insert($larrformData);
	}

	public function fndeletedetailsetup($lintIdexamscale) {
		$where = $this->lobjDbAdpt->quoteInto('IdExamScaling = ?', $lintIdexamscale);
		$this->lobjDbAdpt->delete('tbl_exam_scaling_setup_detail', $where);
	}

}