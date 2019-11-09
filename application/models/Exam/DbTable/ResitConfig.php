<?php 

class App_Model_Exam_DbTable_ResitConfig extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_examination_resit_configuration';
	protected $_primary = "IdExaminationResitConfiguration";
	public function getData($id=0){
		$id = (int)$id;
	
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
	
		if(!$row){
			throw new Exception("There is No Configuration Info");
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
	
	
	function getConfig($iduniv,$idprogram,$idmajoring,$idbranch,$idsubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
		->from(array('a'=>$this->_name))
		->where('a.IdUniversity = ?',$iduniv) 
		->where('a.IdProgram = ?',$idprogram)
		->where('a.IdMajoring = ?',$idmajoring)
		->where('a.IdBranch = ?',$idbranch)
		->where('a.IdSubject = ?',$idsubject);
		$row=$db->fetchRow($select);
		if (!$row) {
			$select = $db->select()
			->from(array('a'=>$this->_name))
			->where('a.IdUniversity = ?',$iduniv)
			->where('a.IdProgram = ?',$idprogram)
			->where('a.IdMajoring = ?',$idmajoring)
			->where('a.IdBranch = ?',$idbranch);
			$row=$db->fetchRow($select);
			if (!$row) {
				$select = $db->select()
				->from(array('a'=>$this->_name))
				->where('a.IdUniversity = ?',$iduniv)
				->where('a.IdProgram = ?',$idprogram)
				->where('a.IdMajoring = ?',$idmajoring);
				
				$row=$db->fetchRow($select);
				if (!$row) {
					$select = $db->select()
					->from(array('a'=>$this->_name))
					->where('a.IdUniversity = ?',$iduniv)
					->where('a.IdProgram = ?',$idprogram); 
				
					$row=$db->fetchRow($select);
					if (!$row) {
						$select = $db->select()
						->from(array('a'=>$this->_name))
						->where('a.IdUniversity = ?',$iduniv);
					
						$row=$db->fetchRow($select);
					}
				}
			}
		}
		
	
		return $row;
	
	}

	 
}

?>