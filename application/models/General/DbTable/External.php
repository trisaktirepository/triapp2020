<?php
//error_reporting(0);

class App_Model_General_DbTable_External extends Zend_Db_Table_Abstract//model class for schemesetup module
{
	private $lobjDbAdpt;
	private $id_sp;
	protected  $_name="tbl_external_connection";
	protected  $_primary="idExternal";
	
	public function init(){
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$this->id_sp = '113b4d06-34ca-4a6e-a29a-9af372933c8f';
	}
	
	public function addData($data){
		$id = $this->insert($data);
		return $id;
	}
	
	public function updateData($data,$id){
		// echo var_dump($data);echo $id;exit;
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function getDataByCode($code=null){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name));
		if ($code!=null) {
			
			$select->where('extenal_code = ?',$code);
			$row = $db->fetchRow($select);
		}
		else $row = $db->fetchAll($select);
		// echo $select;
		
		// echo var_dump($row);exit;
		return $row;
	}
	
	public function getData($id){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where('a.idExternal=?',$id);
		$row = $db->fetchRow($select);
		 
		return $row;
	}
}
?>