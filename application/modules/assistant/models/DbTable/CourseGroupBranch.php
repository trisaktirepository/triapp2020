<?php 

class Assistant_Model_DbTable_CourseGroupBranch extends Zend_Db_Table_Abstract {
	
	protected $_name = 'course_group_branch_assistant';
	protected $_primary = "id";
	
  public function getGroupBranchData($group_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('cb'=>$this->_name))
					  ->join(array('b'=>'tbl_branchofficevenue'), 'b.IdBranch = cb.branch_id')
					  ->where('group_id = ?',$group_id);
							  
		 $row = $db->fetchAll($select);	
		 
		 return $row;
	}
	
	 public function updateCourseGroupBranch($larrformData,$id){
	 	$where = 'id = '.$id;
	 	$this->update($larrformData,$where);
	 }
	 
	 public function insertCourseGroupBranch($larrformData){
	 	$this->insert($larrformData);
	 }
	 
	 public function deleteCourseGroupBranch($id){
	 	$this->delete($id);
	 }
}