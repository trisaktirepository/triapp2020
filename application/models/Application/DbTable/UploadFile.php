<?php

class App_Model_Application_DbTable_UploadFile extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'appl_upload_file';
	protected $_primary = "auf_id";
	
	public function getData($id=0){
		
		 
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.auf_id = '.$id);
	        $row = $db->fetchRow($select);				
		 
		return $row;
	}
	
	public function getUploadData($appl_id=0){
		
		$appl_id = (int)$appl_id;
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.auf_appl_id = '.$appl_id)
						->order("auf_id ASC");	
	        $row = $db->fetchAll($select);				
		
	    	        
//		if(!$row){
//			throw new Exception("There is No Applicant");
//		}
		
		return $row;
	}
	public function getUploadDataNew($appl_id=0){
	
		$appl_id = (int)$appl_id;
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->join(array('def'=>'sis_setup_detl'),'def.ssd_id=a.auf_file_type',array('code'=>'def.ssd_name_bahasa','document_name'=>'def.ssd_name_bahasa'))
		->where('a.auf_appl_id = '.$appl_id)
		->order("auf_id ASC");
		$row = $db->fetchAll($select);
	
	
		//		if(!$row){
		//			throw new Exception("There is No Applicant");
		//		}
	
		return $row;
	}
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('a'=>$this->_name));
		
		return $selectData;
	}
	
	public function addData($data){
		$this->insert($data);
 		$id = $this->getAdapter()->lastInsertId();
		return $id;
	}
	
	public function updateData($data,$id){
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	
	public function getFile($appl_id=0,$type){
		
		$appl_id = (int)$appl_id;
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.auf_appl_id = '.$appl_id)
						->where('a.auf_file_type = '.$type);	
	        $row = $db->fetchRow($select);				
		
	    	        
//		if(!$row){
//			throw new Exception("There is No Applicant");
//		}
		
		return $row;
	}
	
	public function getTxnFile($txnId){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("auf_appl_id='".$txnId."'");
		 $row = $db->fetchRow($select);	
		 return $row;
	}
	
	
	public function getFileArray($appl_id=0,$type){
		
		$appl_id = (int)$appl_id;
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.auf_appl_id = '.$appl_id)
						->where('a.auf_file_type = '.$type);	
	        $row = $db->fetchAll($select);				
		
		return $row;
	}
	
	public function getFileByID($appl_id,$fileid){
		
		$appl_id = (int)$appl_id;
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.auf_appl_id = '.$appl_id)
						->where('a.auf_id = '.(int)$fileid);	
			
	        $row = $db->fetchRow($select);				
		
	    	        
//		if(!$row){
//			throw new Exception("There is No Applicant");
//		}
		
		return $row;
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
	
	
	
}

