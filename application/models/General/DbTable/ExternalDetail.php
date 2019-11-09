<?php
//error_reporting(0);

class App_Model_General_DbTable_ExternalDetail extends Zend_Db_Table_Abstract//model class for schemesetup module
{
	private $lobjDbAdpt;
	private $id_sp;
	protected  $_name="tbl_external_connection_detail";
	protected  $_primary="idExtDetail";
	
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
	
	public function getDataByStd($externalid,$nmaccess,$groupid=null,$idstd=null){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where('idExternal = ?',$externalid)
		->where('nm_access = ?',$nmaccess);
		if ($groupid!=null) $select->where('IdCourseTaggingGroup=?',$groupid);
		if ($idstd!=null) $select->where('IdStudentRegistration = ?',$idstd);
		// echo $select;
		$row = $db->fetchRow($select);
		// echo var_dump($row);exit;
		return $row;
	}
	
	public function getDataByLect($externalid,$nmaccess,$groupid=null,$idlect=null){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where('idExternal = ?',$externalid)
		->where('nm_access = ?',$nmaccess);
		if ($groupid!=null) $select->where('IdCourseTaggingGroup=?',$groupid);
		if ($idlect!=null) $select->where('IdLecturer = ?',$idlect);
		
		// echo $select;
		$row = $db->fetchRow($select);
		// echo var_dump($row);exit;
		return $row;
	}
}
?>