<?php

class Assistant_Model_DbTable_MarkdistributionDetailHeader extends Zend_Db_Table {

	protected $_name = 'tbl_markdistributiondetail_header';
	protected $_primary = '`IdMarkDistributionDetailHeader';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function addData($data){
		$this->lobjDbAdpt->insert($this->_name,$data);   
	   	return $lintgroupid = $this->lobjDbAdpt->lastInsertId();
	}
	
	public function updateData($data,$id){
		$this->lobjDbAdpt->update($this->_name,$data, IdMarkDistributionDetailHeader.' = "'. (int)$id.'"');
	}
	
	public function deleteData($id){
		//deleted row
		$sql = $this->lobjDbAdpt
		->select()
		->from(array('mdm'=>$this->_name))
		->join(array('nm'=>'tbl_markdistributiondetail'),'nm.IdMarkDetailHeader=mdm.IdMarkDistributionDetailHeader')
		->where('mdm.IdMarkDistributionDetailHeader =' . (int)$id);
		$result = $this->lobjDbAdpt->fetchRow($sql);
		//delete row
		if (!$result) $this->delete($this->_primary .' =' . (int)$id);
		 
	}
	
	public function fnGetMarksDistributionDetailHeaderByMaster($idMaster,$entrier=null) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$select = $lobjDbAdpt->select()
		->from(array("a" => $this->_name))
		->join(array('def'=>'tbl_definationms'),'def.Iddefinition=a.Calculation_mode',array('CalMode'=>'def.BahasaIndonesia'))
		
		->where('a.IdMarksDistributionMaster=?',$idMaster);
		if ($entrier!=null) $select->where('a.Entry_by=?',$entrier);
		
		  
		return $result = $lobjDbAdpt->fetchRow($select);
	}
	
	public function fnGetMarksDistributionDetailHeader($header) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	
		$select = $lobjDbAdpt->select()
		->from(array("a" => $this->_name))  
		->join(array('def'=>'tbl_definationms'),'def.Iddefinition=a.Calculation_mode',array('CalMode'=>'def.BahasaIndonesia'))
		->where('a.IdMarkDistributionDetailHeader=?',$header)
		;
		return $result = $lobjDbAdpt->fetchRow($select);
	}

	
	


}