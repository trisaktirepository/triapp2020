<?php
class Examination_Model_DbTable_StudentAnswerScheme extends Zend_Db_Table { 

	protected $_name = 'student_ansscheme';
	protected $_primary = 'sas_id';
	
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
	
	
	public function checkExist($IdMarksDistributionMaster,$IdMarksDistributionDetails,$set_code){
		
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					 ->from($this->_name)
					 ->where("sas_IdMarksDistributionMaster = ?",$IdMarksDistributionMaster)
					 ->where("sas_set_code = ?",$set_code);
					
		if($IdMarksDistributionDetails!=''){
			 $select->where("sas_IdMarksDistributionDetails = ?",$IdMarksDistributionDetails);
		}else{
			 $select->where("sas_IdMarksDistributionDetails = 0");
		}			
		$result = $db->fetchRow($select);
	
		if(isset($result['sas_id'])){
			return 1;
		}else{
			return null;
		}
	}
	
	
	public function checkDuplicate($idSemester,$idProgram,$idSubject,$set_code){
		
		/*
		 * 		SELECT * 
		FROM `student_ansscheme` 
		WHERE 
		sas_set_code = '040'
		AND 
		`sas_IdMarksDistributionMaster` IN
		(
		SELECT IdMarksDistributionMaster
		FROM `tbl_marksdistributionmaster`
		WHERE `IdProgram` =3
		AND `IdCourse` =2435
		AND `semester` LIKE '1'
		)
		 */
		
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		$select_component = $db->select()
					 		   ->from('tbl_marksdistributionmaster',array('IdMarksDistributionMaster'))
					           ->where('semester = ?',$idSemester)
					           ->where('IdProgram = ?',$idProgram)
					           ->where('IdCourse = ?',$idSubject);
		
		$select = $db->select()
					 ->from($this->_name)
					 ->where("sas_set_code = ?",$set_code)
					 ->where("sas_IdMarksDistributionMaster IN (?)",$select_component);
	
		$result = $db->fetchRow($select);
	
		
		if(isset($result['sas_id'])){
			return 1;
		}else{
			return null;
		}			
		
	}
	
	public function getAnswerSchemeData($idSemester,$idProgram,$idSubject,$set_code){
		
			
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		$select_component = $db->select()
					 		   ->from('tbl_marksdistributionmaster',array('IdMarksDistributionMaster'))
					           ->where('semester = ?',$idSemester)
					           ->where('IdProgram = ?',$idProgram)
					           ->where('IdCourse = ?',$idSubject);
		
		$select = $db->select()
					 ->from($this->_name)
					 ->where("sas_set_code = ?",$set_code)
					 ->where("sas_IdMarksDistributionMaster IN (?)",$select_component);
	
		$result = $db->fetchRow($select);
	
		return $result;		
		
	}
	
	
	public function getData($IdMarksDistributionMaster,$IdMarksDistributionDetails){
		
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					 ->from($this->_name)
					 ->where("sas_IdMarksDistributionMaster = ?",$IdMarksDistributionMaster);					
					
		if($IdMarksDistributionDetails!=''){
			 $select->where("sas_IdMarksDistributionDetails = ?",$IdMarksDistributionDetails);
		}else{
			 $select->where("sas_IdMarksDistributionDetails = 0");
		}			
		$result = $db->fetchAll($select);
	
		return $result;
	}
	
	
	//to check student answer either correct or false
	public function getAnswer($IdMarksDistributionMaster,$IdMarksDistributionDetails,$set_code,$quest_no,$student_answer){
		
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					 ->from(array('sas'=>$this->_name))
					 ->join(array('sasd'=>'student_ansscheme_detl'),'sasd.sad_anscheme_id=sas.sas_id')
					 ->where("sas_IdMarksDistributionMaster = ?",$IdMarksDistributionMaster)
					 ->where("sas_set_code = ?",$set_code)
					 ->where("sasd.sad_ques_no = ?",$quest_no);
					
		if($IdMarksDistributionDetails!=''){
			 $select->where("sas_IdMarksDistributionDetails = ?",$IdMarksDistributionDetails);
		}else{
			 $select->where("sas_IdMarksDistributionDetails = 0");
		}		

		
		$result = $db->fetchRow($select);
	
		if($result["sad_ques_ans"]==$student_answer){
			return 1; //correct
		}else{
			return 0; //false
		}
	}
	
	
	//to check student answer either correct or false
	public function getStudentAnswer($IdMarksDistributionMaster,$IdMarksDistributionDetails,$set_code,$quest_no,$student_answer){
		
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					 ->from(array('sas'=>$this->_name))
					 ->join(array('sasd'=>'student_ansscheme_detl'),'sasd.sad_anscheme_id=sas.sas_id')
					 ->where("sas_IdMarksDistributionMaster = ?",$IdMarksDistributionMaster)
					 ->where("sas_set_code = ?",$set_code)
					 ->where("sasd.sad_ques_no = ?",$quest_no);
					
		if($IdMarksDistributionDetails!=''){
			 $select->where("sas_IdMarksDistributionDetails = ?",$IdMarksDistributionDetails);
		}else{
			 $select->where("sas_IdMarksDistributionDetails = 0");
		}		

		//echo $select;
		
		$result = $db->fetchRow($select);
	
		if($result["sad_ques_ans"]==$student_answer){
			return 1; //correct
		}else{
			return 0; //false
		}
	}
	
	
	
	
}
?>