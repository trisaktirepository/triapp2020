<?php
//error_reporting(0);

class App_Model_General_DbTable_ExternalPermission extends Zend_Db_Table_Abstract//model class for schemesetup module
{
	private $lobjDbAdpt;
	private $id_sp;
	protected  $_name="tbl_external_permission";
	protected  $_primary="id";
	
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
	
	public function getDataByCode($code){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'tbl_external_connection'),'a.IdExternal=b.IdExternal')
		->where('extenal_code = ?',$code);
	
		// echo $select;
		$row = $db->fetchRow($select);
		// echo var_dump($row);exit;
		return $row;
	}
	public function getDataById($externalid){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'tbl_external_connection'),'a.IdExternal=b.IdExternal')
		->where('a.IdExternal = ?',$externalid);
	
		// echo $select;
		$row = $db->fetchRow($select);
		// echo var_dump($row);exit;
		return $row;
	}
	
	public function isIn($externalid,$idProgram,$idSubject,$groupid=null){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'tbl_external_connection'),'a.IdExternal=b.IdExternal')
		->where('a.IdExternal = ?',$externalid)
		->where('a.IdProgram=?',$idProgram)
		->where('a.IdSubject=?',$idSubject);
		
		if ($groupid!=null) $select->where('a.group_id=?',$groupid);
	
		//echo $select;
		$row = $db->fetchRow($select);
		//echo var_dump($row);exit;
		return $row;
	}
	
	public function getDataBySubject($idProgram,$idSubject,$groupid=null){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'tbl_external_connection'),'a.IdExternal=b.IdExternal') 
		->where('a.IdProgram=?',$idProgram)
		->where('a.IdSubject=?',$idSubject);
	
		if ($groupid!=null) $select->where('a.group_id=?',$groupid);
	
		//echo $select;
		$row = $db->fetchAll($select);
		//echo var_dump($row);exit;
		return $row;
	}
}
?>