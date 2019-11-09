<?php

class App_Model_Exam_DbTable_ExamDefer extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e015_exam_defer';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Info");
		}			
		return $row->toArray();
	}
	
	
	/* ===========================================================================================
	   Function    : To Search List Student apply for exam Defer 
	   Created By  : Mardhiyati Ipin
	   Created On  : 09 September 2011
	   =========================================================================================== */
	
	public function search($condition=NULL){
				
		 $db = Zend_Db_Table::getDefaultAdapter();
		
		 $select= $db ->select()
    	             ->from(array('exam_defer'=>$this->_name),array('exam_defer.scr_id','exam_defer.defer_status','exam_defer.createddt','exam_defer.approvedby','exam_defer.approveddt'))
    	             ->joinLeft(array('st'=>'r015_student'),'st.id=exam_defer.student_id',array('stud_program_id'=>'st.program_id','ic_no'=>'st.ic_no','fullname'=>'st.fullname','matric_no'=>'st.matric_no'))
    	             ->join(array('scr'=>'r024_student_course_registration'),'scr.id=exam_defer.scr_id')
    	             ->joinLeft(array('course'=>'r010_course'),'course.id=scr.course_id',array('course_name'=>'name','course_code'=>'code'))
    	             ->joinLeft(array('semester'=>'r001_semester'),'exam_defer.semester_id=semester.id',array('semester_name'=>'name','semester_year'=>'year'))
    	             ->join(array('p'=>'r006_program'),'p.id=st.program_id',array('program_id'=>'p.id','program_code'=>'p.code'))
				     ->join(array('mp'=>'r005_program_main'),'mp.id=p.program_main_id',array('main_name'=>'name'))	
				     ->group(array('x'=>'exam_defer.student_id','y'=>'exam_defer.semester_id'))						   	   
				     ->order("exam_defer.defer_status,exam_defer.semester_id ASC");
				     
				     
		if($condition!=null){
		    if($condition['id']!=""){
				$select->where("st.ic_no = " .$condition['id']);
			}
			
			if($condition['name']!=""){
				$select->where("st.fullname like '%" .$condition['name']."%'");
			}
			
			if($condition['matric_no']!=0){
				$select->where("st.matric_no like '%" .$condition['matric_no']."%'");
			}
			
			if($condition['program_id']!=0){
				$select->where("st.program_id like '%" .$condition['program_id']."%'");
			}
		}
		
		 $row = $db->fetchAll($select);
		
		return $row;
	}
	
	
	public function getPaginateData(){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
		
		 $select= $db ->select()
    	             ->from(array('exam_defer'=>$this->_name),array('exam_defer.scr_id','exam_defer.defer_status','exam_defer.createddt','exam_defer.approvedby','exam_defer.approveddt'))
    	             ->joinLeft(array('st'=>'r015_student'),'st.id=exam_defer.student_id',array('stud_program_id'=>'st.program_id','ic_no'=>'st.ic_no','fullname'=>'st.fullname','matric_no'=>'st.matric_no'))
    	             ->join(array('scr'=>'r024_student_course_registration'),'scr.id=exam_defer.scr_id')
    	             ->joinLeft(array('course'=>'r010_course'),'course.id=scr.course_id',array('course_name'=>'name','course_code'=>'code'))
    	             ->joinLeft(array('semester'=>'r001_semester'),'exam_defer.semester_id=semester.id',array('semester_name'=>'name','semester_year'=>'year'))
    	             ->join(array('p'=>'r006_program'),'p.id=st.program_id',array('program_id'=>'p.id','program_code'=>'p.code'))
				     ->join(array('mp'=>'r005_program_main'),'mp.id=p.program_main_id',array('main_name'=>'name'))
				     ->group(array('x'=>'exam_defer.student_id','y'=>'exam_defer.semester_id'))		
				   	 ->order("exam_defer.defer_status ASC");
    	             
		return $select;	 
	}
	
	/* ===========================================================================================
	   Function    : To list Exam Defer Application From Student -> Display Current Semester Only
	   Created By  : Mardhiyati Ipin
	   Created On  : 09 September 2011
	   =========================================================================================== */
	
	public function getlist($condition=NULL){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	 $select= $db ->select()
    	             ->from(array('exam_defer'=>$this->_name))
    	             ->join(array('scr'=>'r024_student_course_registration'),'scr.id=exam_defer.scr_id',array('scr_id'=>'scr.id'))
    	             ->joinLeft(array('course'=>'r010_course'),'course.id=scr.course_id',array('course_name'=>'name','course_code'=>'code'));
    	             
    	    if($condition!=NULL){
    	    	if($condition["student_id"]!=0){
    	    		$select->where('exam_defer.student_id   = ?',$condition["student_id"]);
    	    	}
    	    	
    	    	if($condition["semester_id"]!=0){
    	    		$select->where('exam_defer.semester_id   = ?',$condition["semester_id"]);
    	    	}
    	    	
    	    	if($condition["scr_id"]!=0){
    	    		$select->where('exam_defer.scr_id   = ?',$condition["scr_id"]);
    	    	}
    	    }
    	 
    	    //echo $select;
    	    
   			$stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	     
	        return $row;
	}
	
	
	public function getRowExamDefer($condition=NULL){
		$db = Zend_Db_Table::getDefaultAdapter();
		    
		$select = $db->select()
	    	          ->from(array('exam_defer'=>$this->_name));
	    	         	    	            
	    	if($condition!=NULL){
    	    	if($condition["student_id"]!=0){
    	    		$select->where('exam_defer.student_id   = ?',$condition["student_id"]);
    	    	}
    	    	
    	    	if($condition["semester_id"]!=0){
    	    		$select->where('exam_defer.semester_id   = ?',$condition["semester_id"]);
    	    	}
    	    	
    	    	if($condition["scr_id"]!=0){
    	    		$select->where('exam_defer.scr_id   = ?',$condition["scr_id"]);
    	    	}
    	    }
	              
	    $stmt = $db->query($select);
	    $row = $stmt->fetch();
	     
	    return $row;
	}
	
	public function addData($data){				
		$this->insert($data);
	}
	
	public function updateData($data,$id){		
		$this->update($data,'id = ' . (int)$id);
	}
	
	public function deleteData($id){		
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
}
?>