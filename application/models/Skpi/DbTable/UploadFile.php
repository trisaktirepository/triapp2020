<?php

class App_Model_Skpi_DbTable_UploadFile extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_SKPI_upload_file';
	protected $_primary = "auf_id";
	
	public function getData($id=0){
		
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.ID = '.$id)
						->join(array('p'=>'r003_award'),'p.id = a.ARD_AWARD',array('award_name'=>'name'));
						
	        $row = $db->fetchRow($select);				
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->join(array('p'=>'r003_award'),'p.id = a.ARD_AWARD',array('award_name'=>'name'));
						
			$row = $db->fetchAll($select);
		}
		
//		if(!$row){
//			throw new Exception("There is No Applicant");
//		}
		
		return $row;
	}
	
	public function getUploadData($appl_id=0){
		
		$appl_id = (int)$appl_id;
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.auf_idStudentRegistration = '.$appl_id)
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
						->where('a.auf_idStudentRegistration = '.$appl_id)
						->where('a.auf_file_type = '.$type);	
	        $row = $db->fetchRow($select);				
		
	    
		
		return $row;
	}
	public function getFileItems($appl_id=0,$items,$type){
	
		$appl_id = (int)$appl_id;
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where('a.auf_idStudentRegistration = '.$appl_id)
		->where('a.auf_Items=?',$items)
		->where('a.auf_file_type = '.$type);
		$row = $db->fetchRow($select);
	
		 
	
		return $row;
	}
	
	public function getTxnFile($txnId){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("auf_idStudentRegistration='".$txnId."'");
		 $row = $db->fetchRow($select);	
		 return $row;
	}
	
	
	public function getFileArray($appl_id=0,$type){
		
		$appl_id = (int)$appl_id;
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.auf_idStudentRegistration = '.$appl_id)
						->where('a.auf_file_type = '.$type);	
	        $row = $db->fetchAll($select);				
		
		return $row;
	}
	
	public function getFileByID($appl_id,$fileid){
		
		$appl_id = (int)$appl_id;
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.auf_idStudentRegistration = '.$appl_id)
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

