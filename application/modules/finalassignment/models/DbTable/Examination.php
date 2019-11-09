<?php 
class Finalassignment_Model_DbTable_Examination extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_Examination';
	protected $_primary='IdTAExamination';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		$postData['dt_entry'] = date('Y-m-d H:i:sa'); 	
		return $this->insert($postData);
	}
	
	public function updateData($postData, $id){
		$this->update($postData, $this->_primary .' = '. (int) $id);
	}
	
	public function updateDataByStudent($postData, $idregis,$idta){
		$this->update($postData, 'IdStudentRegistration='.$idregis.' and IdTAApplication='.$idta);
	}
		 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function getData($id){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAExamination = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getDataByStudent($idreg,$idta){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->joinLeft(array('def'=>'tbl_definationms'),'p.IdPosition=def.IdDefinition',array('RoleAs'=>'def.BahasaIndonesia'))
		->where('p.IdTAApplication = ?', $idta)
		 
		->where('p.IdStudentRegistration = ?', $idreg)
		->order('p.dt_approval');
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getDataByStudentList($idreg,$idta){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->joinLeft(array('def'=>'tbl_definationms'),'p.IdPosition=def.IdDefinition',array('RoleAs'=>'def.BahasaIndonesia'))
		->where('p.IdTAApplication = ?', $idta)
		->where('p.IdStudentRegistration = ?', $idreg);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	 
	
}

?>