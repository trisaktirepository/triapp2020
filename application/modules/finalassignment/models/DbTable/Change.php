<?php 
class Finalassignment_Model_DbTable_Change extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_CHANGE';
	protected $_primary='IdTAChange';

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
		->where('p.IdTAChange = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getDataByStudent($idregistration,$idtaapplication){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('def'=>'tbl_definationms'),'p.ChangeCode=def.IdDefinition',array('reasoning'=>'def.BahasaIndonesia'))
		->where('p.IdStudentRegistration = ?', $idregistration)
		->where('p.IdTAApplication = ?', $idtaapplication);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		//echo $lstrSelect;exit;
		return $larrResult;
	}
	
	
	
	public function fnGetChangeType() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Change Cause"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	
	public function fnGetReasonType() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Reason Reject"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	
	
}

?>