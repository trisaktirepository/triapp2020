<?php
class Examination_Model_DbTable_StudentDetailsMarkEntry extends Zend_Db_Table { //Model Class for Users Details
	
	protected $_name = 'tbl_student_detail_marks_entry';
	protected $_primary = 'IdStudentMarksEntryDetail';

	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
    public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function updateData($data,$id){
		 $this->update($data, 'IdStudentMarksEntryDetail = '. (int)$id);
	}
	
	public function getItemMark($IdStudentMarksEntry,$IdMarksDistributionDetails) {
		
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$sql =  $lobjDbAdpt->select()->from(array("sdme" =>"tbl_student_detail_marks_entry"))																		
							    		->where("sdme.IdStudentMarksEntry = ?",$IdStudentMarksEntry)
							    		->where("sdme.ComponentDetail = ?",$IdMarksDistributionDetails);

		$result = $lobjDbAdpt->fetchRow($sql);
		
		return $result;
	}
	
	public function checkMarkEntry($IdStudentMarksEntry,$IdMarksDistributionDetails){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			
		//check ada x mark entry sebelum ni
	 	$select_mark = $db->select()
	 	 				  ->from(array('sdme'=>$this->_name))
	 	 				  ->where('sdme.IdStudentMarksEntry = ?',$IdStudentMarksEntry)
	 	 				  ->where('sdme.ComponentDetail = ?',$IdMarksDistributionDetails);                                                                                      	 	 				 
	 	 				  
	 	return $entry_list = $db->fetchRow($select_mark);	
	}
	
	
	public function getListComponentMark($IdStudentMarksEntry) {
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$sql =  $db->select()->from(array("sdme" =>$this->_name))								
							    ->where("sdme.IdStudentMarksEntry = ?",$IdStudentMarksEntry);

		$result = $db->fetchAll($sql);
		
		return $result;
	}
	
	public function getComponentItemMark($IdStudentMarksEntry,$id){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
		
	 	 $select_mark = $db->select()
	 	 				  ->from(array('sdme'=>$this->_name))
	 	 				  ->where('sdme.IdStudentMarksEntry = ?',$IdStudentMarksEntry)
	 	 				  ->where('sdme.ComponentDetail = ?',$id);
	 	 				
	 			  
	     return $entry_list = $db->fetchRow($select_mark);	
	}
	public function getData($id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select_mark = $db->select()
		->from(array('sdme'=>$this->_name))
		->where('sdme.IdStudentMarksEntryDetail = ?',$id);
	
			
		return $entry_list = $db->fetchRow($select_mark);
	}
	
}
?>