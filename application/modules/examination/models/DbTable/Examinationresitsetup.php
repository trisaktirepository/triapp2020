<?php
class Examination_Model_DbTable_Examinationresitsetup extends Zend_Db_Table_Abstract { //Model Class for Users Details
	protected $_name = 'tbl_examination_resit_configuration';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function Addresitconfig($data){
		return $this->insert($data);
	}

	public function getresitconfig($IdUniversity){
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_resit_configuration'), array('a.*'))
		->where('a.IdUniversity =?',$IdUniversity);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}

	public function updateresitconfig($larrformData,$IdExaminationResitConfiguration){
		$where ='IdExaminationResitConfiguration = '.$IdExaminationResitConfiguration;
		$this->update($larrformData,$where);

	}


}
?>