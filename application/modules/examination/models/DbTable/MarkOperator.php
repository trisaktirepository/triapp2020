<?php
class Examination_Model_DbTable_MarkOperator extends Zend_Db_Table { //Model Class for Users Details
	
	protected $_name = 'tbl_mark_operator';
	protected $_primary = 'idMarkOperator';
	
	protected $lobjDbAdpt;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($data){
		$id = $this->insert($data);
		return $id;
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function updateData($data,$id){
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function getData($id) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select=$lobjDbAdpt->select()
		->from(array('mo'=>$this->_name))
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=mo.Entrier',array('stm.FrontSalutation','stm.FullName','stm.BackSalutation'))
		->joinLeft(array('stm2'=>'tbl_staffmaster'),'stm2.IdStaff=mo.Verifier',array('VerFront'=>'stm2.FrontSalutation','VerFullName'=>'stm2.FullName','VerBack'=>'stm2.BackSalutation'))
		
		->where('mo.IdMarkOperator=?',$id);
		$row=$lobjDbAdpt->fetchRow($select);
		return $row;
		
	}
	
	public function getDataByGroupComp($idGroup,$idMark=null,$idItem=null) {
		
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select=$lobjDbAdpt->select()
		->from(array('mo'=>$this->_name))
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=mo.Entrier',array('stm.FrontSalutation','stm.FullName','stm.BackSalutation'))
		->joinLeft(array('stm2'=>'tbl_staffmaster'),'stm2.IdStaff=mo.Verifier',array('VerFront'=>'stm2.FrontSalutation','VerFullName'=>'stm2.FullName','VerBack'=>'stm2.BackSalutation'))
		->where('mo.IdCourseTaggingGroup=?',$idGroup);
		
		if ($idMark!=null) $select->where('mo.idMarksDistributionMaster=?',$idMark);
		if ($idItem!=null) $select->where('mo.idMarksDistributionDetail=?',$idItem);
		
		$row=$lobjDbAdpt->fetchRow($select);
		return $row;
	
	}
}
?>