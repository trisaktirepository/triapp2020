<?php


class App_Model_Record_DbTable_CreditTransfer extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r033_credit_transfer';
	protected $_primary = "id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Credit Transfer");
		}
		return $row->toArray();
	}
			  
	
	public function getList($student_id){
		 $db = Zend_Db_Table::getDefaultAdapter();
		 $select  = $db->select()
	                      ->from(array('cdt' => $this->_name))
	                      ->where('cdt.student_id = '.$student_id)
	                      ->join(array('c'=> 'r010_course'),'c.id=cdt.course_id', array('course_name'=>'name','course_code'=>'code','course_credit_hour'=>'credit_hour'));
	                      //->join(array('ct'=>'r009_course_type'),'ct.id = alc.course_type_id',array('course_type'=>'name'))
	                     // ->order('alc.level');
	              
        $result = $db->fetchAll($select);         
        	
        return $result;
	}
	
	
	public function getRow($student_id,$course){
		 $db = Zend_Db_Table::getDefaultAdapter();
		 $select  = $db->select()
	                      ->from(array('cdt' => $this->_name))
	                      ->where('cdt.student_id = '.$student_id.' and cdt.course_id = '.$course)
	                      ->join(array('c'=> 'r010_course'),'c.id=cdt.course_id', array('course_name'=>'name','course_code'=>'code','course_credit_hour'=>'credit_hour'));
	                      //->join(array('ct'=>'r009_course_type'),'ct.id = alc.course_type_id',array('course_type'=>'name'))
	                     // ->order('alc.level');
	              
        $result = $db->fetchRow($select);         
        	
        return $result;
	}
	
	public function checkTransfer($student_id,$course,$semester){
		 $db = Zend_Db_Table::getDefaultAdapter();
		 $select  = $db->select()
	                      ->from(array('cdt' => $this->_name))
	                      ->where('cdt.student_id = '.$student_id.' and cdt.course_id = '.$course.' and cdt.semester_id = '.$semester);
	              
        $result = $db->fetchRow($select);         
        	
        return $result;
	}
	
	public function addData($postData){
		$data = array(
				'student_id' => $postData['student_id'],
				'course_id' => $postData['course_id'],
				'semester_id' => $postData['semester_id'],
				'status' => $postData['status'],
				'date' => date('Y-m-d H:i:a')
				);
			
		$this->insert($data);
	}
	
	
	public function getPaginateDataAdmin($condition=NULL){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				->distinct()
	            ->from(array('s'=>$this->_name),array('student_id'=>'s.student_id'))
				->join(array('st'=>'r015_student'),'st.id=s.student_id',array('stud_program_id'=>'st.program_id','ic_no'=>'st.ic_no','fullname'=>'st.fullname','matric_no'=>'st.matric_no'))
				->join(array('p'=>'r006_program'),'p.id=st.program_id',array('program_id'=>'p.id','program_code'=>'p.code'))
				->join(array('mp'=>'r005_program_main'),'mp.id=p.program_main_id',array('main_name'=>'name'));
		
					
		if($condition!=null){
		    if(isset($condition['id']) && $condition['id']!=""){
				$select->where("s.id = " .$condition['id']);
			}
			
			if(isset($condition['name'])){
				$select->where("s.fullname like '%" .$condition['name']."%'");
			}
			
			if($condition['matric_no']!=0){
				$select->where("s.matric_no like '%" .$condition['matric_no']."%'");
			}
			
			if($condition['program_id']!=0){
				$select->where("s.program_id like '%" .$condition['program_id']."%'");
			}
		}
		
		
		return $select;	 
	}
	
	public function searchAdmin($condition){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('s'=>$this->_name))
				->join(array('st'=>'r015_student'),'st.id=s.student_id',array('stud_program_id'=>'st.program_id'))
				->join(array('p'=>'r006_program'),'p.id=st.stud_program_id',array('program_id'=>'p.id','program_code'=>'p.code'))
				->join(array('mp'=>'r005_program_main'),'mp.id=p.program_main_id',array('main_name'=>'name'));

		if($condition!=null){
		    if($condition['id']!=""){
				$select->where("s.id = " .$condition['id']);
			}
			
			if($condition['name']!=""){
				$select->where("s.fullname like '%" .$condition['name']."%'");
			}
			
			if($condition['matric_no']!=0){
				$select->where("s.matric_no like '%" .$condition['matric_no']."%'");
			}
			
			if($condition['program_id']!=0){
				$select->where("s.program_id like '%" .$condition['program_id']."%'");
			}
		}
		
		 if($condition['id']!=""){	
		 	$row = $db->fetchRow($select);
		 }else{
			$row = $db->fetchAll($select);
		 }
		
		return $row;
	}
	
	public function updateData($postData,$student_id,$semester_id,$courseID){
		$data = array(
				'status' => $postData["status"],
				'date_approved' => date('Y-m-d H:i:a')
				);
			
		$this->update($data, 'student_id = '. (int)$student_id.' and course_id = '.(int)$courseID.' and semester_id = '.(int)$semester_id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}

