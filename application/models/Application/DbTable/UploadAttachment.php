<?php


class App_Model_Application_DbTable_UploadAttachment extends Zend_Db_Table_Abstract {
	
	/**
	 * The default table name 
	 */
	protected $_name = 'a015_upload_cert';
	protected $_primary = "id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name))
					->where('s.app_id = '.$id)
					->order($this->_primary.' DESC');
							
			$row = $db->fetchAll($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}
		
//		if(!$row){
//			throw new Exception("There is No Student Upload File");
//		}
		return $row;
		
	}
	
	
	public function getDataUpload($app_id,$id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		echo $select = $db->select()
	            ->from(array('s' => $this->_name))					
				->join(array('app'=>'a001_applicant'),'app.ID=s.app_id', array('ARD_IC'=>'ARD_IC'))
				->where('s.app_id = '.$app_id.' and s.id = '.$id);
				
		$result = $db->fetchRow($select);	
		return $result;	 
	}
	
	
	public function addData($data){
		$db = Zend_Db_Table::getDefaultAdapter();
        
        $this->insert($data);
        $id = $db->lastInsertId();
        
        return $id;
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

	
}

?>