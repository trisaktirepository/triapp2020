<?php 
class Finalassignment_Model_DbTable_DocRequisiteDetail extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_syaratberkas_detail';
	protected $_primary='IdSyaratBerkasDetail';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		 
		return $this->insert($postData);
	}
	
	public function updateData($postData, $id){
	
		 
		$this->update($postData, $this->_primary .' = '. (int) $id);
	}
	 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function getData($id){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=p.IdSyarat')
		->where('p.IdSyaratBerkasDetail = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getDataByIdFlow($idflow){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=p.IdSyarat',array('RequisiteName'=>'def.BahasaIndonesia'))
		->where('p.IdTAFlow = ?', $idflow);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fnGetRequisiteList() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Requisite"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	
}

?>