<?php


class App_Model_Record_DbTable_UploadFile extends Zend_Db_Table_Abstract {
	
	/**
	 * The default table name 
	 */
	protected $_name = 'r031_upload_file';
	protected $_primary = "id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name))
					->where('s.id = '.$id)
					->order($this->_primary.' DESC');
							
			$row = $db->fetchRow($select);
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
	
	public function getDataPicture($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name))
					->where('s.student_id = '.$id)
					->order($this->_primary.' DESC');
							
			$row = $db->fetchRow($select);
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
	
	public function getPaginateData($condition=NULL){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	            ->from(array('s' => $this->_name))					
				->join(array('p'=>'r006_program'),'p.id=s.program_id', array('program_id'=>'p.id','program_code'=>'p.code'))
				->join(array('mp'=>'r005_program_main'),'mp.id=p.program_main_id',array('main_name'=>'name'));

		return $select;	 
	}
	
	
	public function addData($data){
		$db = Zend_Db_Table::getDefaultAdapter();
        
        $this->insert($data);
        $id = $db->lastInsertId();
        
        return $id;
	}
	
}

?>