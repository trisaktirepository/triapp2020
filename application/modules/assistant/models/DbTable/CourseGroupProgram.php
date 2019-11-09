<?php 

class Assistant_Model_DbTable_CourseGroupProgram extends Zend_Db_Table_Abstract {
	
	protected $_name = 'course_group_program_assistant';
	protected $_primary = "id";
	
  public function getGroupData($group_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		$session = new Zend_Session_Namespace('sis');
		$select = $db ->select()
					  ->from(array('cgp'=>$this->_name))
					  ->join(array('p'=>'tbl_program'), 'p.IdProgram = cgp.program_id')
					  ->where('group_id = ?',$group_id);
		
		/* if($session->IdRole == 605 || $session->IdRole == 311 || $session->IdRole == 298){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$select->where("p.IdCollege=".$session->idCollege);
		}
		if($session->IdRole == 470 || $session->IdRole == 480){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$select->where("p.IdProgram=".$session->idDepartment);
		} */
						  
		 $row = $db->fetchAll($select);	
		 
		 return $row;
	}
	
	public function addData($data){
		$id = $this->insert($data);
		return $id;
	}
	
}