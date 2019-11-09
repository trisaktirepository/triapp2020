<?php 
class Finalassignment_Model_DbTable_Extend extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_EXTEND';
	protected $_primary='IdTAExtend';

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
	
	public function updateDataByStudent($postData, $idresigtration,$idtaapplication){
		$where='IdStudentRegistration='.$idresigtration.' and IdTAApplication='.$idtaapplication;
		$this->update($postData, $where);
	}
		 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function getData($id){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAExtend = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getDataByStudent($idregstudent,$idtaapplication){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('app'=>'tbl_TA_Approval'),'p.IdTAApplication=app.IdTAApplication')
		->where('p.IdStudentRegistration = ?', $idregstudent)
		->where('p.IdTAApplication=?',$idtaapplication)
		->where('app.Sequence="1"')
		->where('app.StaffAs="Pembimbing" or app.StaffAs="Promotor" ');
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function isExtend($idregstudent,$idtaapplication){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdStudentRegistration = ?', $idregstudent)
		->where('p.IdTAApplication=?',$idtaapplication)
		->where('p.Approved_by is not null');
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function fnGetExtendType() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Extend Cause"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	
	
}

?>