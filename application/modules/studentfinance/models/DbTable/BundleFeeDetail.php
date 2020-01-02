<?php
class Studentfinance_Model_DbTable_BundleFeeDetail extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'fee_budle_detail';
	protected $_primary='idfeebundledetail';
	protected $lobjDbAdpt;
	
	public function init(){
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($data) {
		return $this->lobjDbAdpt->insert($this->_name,$data);
	}
	
	public function updatData($data,$where){
		return $this->lobjDbAdpt->update($this->_name,$data,$where);
	}
	
	public function deleteData($where){
		return $this->lobjDbAdpt->delete($this->_name,$where);
	}
	 
	public function isIn($budleid,$idfee) {
		$select=$this->lobjDbAdpt->select()
		->from($this->_name)
		->where('idfeebundle=?',$budleid)
		->where('fee_item=?',$idfee);
		$row=$this->lobjDbAdpt->fetchRow($select);
		return $row;
	}
	
	public function getDataByBudle($budleid) {
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'fee_item'),'a.fee_item=b.fi_id')
		->where('idfeebundle=?',$budleid) ;
		$row=$this->lobjDbAdpt->fetchAll($select);
		return $row;
	}
}