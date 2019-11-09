<?php 
class Finalassignment_Model_DbTable_ExaminationComposition extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_Examiner_composition';
	protected $_primary='IdComposition';

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
	
	 
		 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function getData($id){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdComposition = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getDataByIdFlow($id){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('def'=>'tbl_definationms'),'p.IdPosition=def.IdDefinition',array('PositionName'=>'def.BahasaIndonesia'))
		->where('p.IdFlow = ?', $id)
		->order('p.Sequence ASC');
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function getPositionName(){
	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Examiner Position"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	
	 
	
}

?>