<?php

class App_Model_Exam_DbTable_ResitFee extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_resit';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Student Info");
		}			
		return $row->toArray();
	}
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	
	function getInfo($idprogram,$idsubject=null,$idComponent=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
					   ->from(array('a'=>$this->_name))
					   ->where('a.program_id = ?',$idprogram)
					   ->where('a.subject_id=?',$idsubject)
					   //->where('a.IdComponentType = ?',$idComponent)
					->where('effective_date <=CURDATE()');
		
		
		$rowSet = $db->fetchRow($select);
		if (!$rowSet) {
			$select = $db->select()
			->from(array('a'=>$this->_name))
			->where('a.program_id = ?',$idprogram)
			->where('a.subject_id=?',$idsubject)
			//->where('a.IdComponentType is null')
			->where('effective_date <=CURDATE()');
			$rowSet = $db->fetchRow($select);
			if (!$rowSet) {
				$select = $db->select()
				->from(array('a'=>$this->_name))
				->where('a.program_id = ?',$idprogram)
				->where('a.subject_id is null or a.subject_id=0')
				//->where('a.IdComponentType is null')
				->where('effective_date <=CURDATE()');
				$rowSet = $db->fetchRow($select);
			}
		}
		return $rowSet;
		
	}
	

    
}

