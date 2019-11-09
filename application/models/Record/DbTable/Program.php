<?php 
class App_Model_Record_DbTable_Program extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_program';
	protected $_primary = "IdProgram";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db ->select()
						->from(array('program'=>$this->_name))
						->where('program.'.$this->_primary.' = ' .$id);

	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db ->select()
						->from(array('program'=>$this->_name))
						->order('ArabicName ASC');

			//echo $select;
									
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		return $row;
	}
	
	public function getProgrambyCode($code=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		
		
		if($code!=0){

	        $select = $db ->select()
						->from(array('program'=>$this->_name))
						->where("program.ProgramCode='".$code."'");

	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db ->select()
						->from(array('program'=>$this->_name))
						->order('ArabicName ASC');

			//echo $select;
									
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		return $row;
	}
	
	public function getPaginateData($name="",$code=""){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
						->from(array('program'=>$this->_name))
						->join(array('main_program'=>'r005_program_main'),'program.program_main_id = main_program.id',array('main_name'=>'name'))
						->join(array('market'=>'r004_market'),'market.id = program.market_id',array('market'=>'name'))
						->joinLeft(array('faculty'=>'g005_faculty'),'faculty.id = program.faculty_id',array('faculty'=>'name'))
						->joinLeft(array('department'=>'g008_department'),'department.id = program.department_id',array('department'=>'name'))
						->join(array('award'=>'r003_award'),'award.id = program.award_id',array('award'=>'name'))
						->order(array('program.status desc','program.award_id desc'));
						
		if($code!=""){
			$selectData->where("program.code like '%".$code."%'");
		}

		if($name!=""){
			$selectData->where("main_program.name like '%".$name."%'");
		}
		return $selectData;
	}
	
	public function getDataFaculty($facID){
		$db = Zend_Db_Table::getDefaultAdapter();
		if($facID!=0){
		$selectData = $db ->select()
						->from(array('program'=>$this->_name))
						->where('program.faculty_id = ' .$facID)
						->joinLeft(array('main_program'=>'r005_program_main'),'program.program_main_id = main_program.id',array('main_name'=>'name'))
						->joinLeft(array('market'=>'r004_market'),'market.id = program.market_id',array('market'=>'name'))
						->join(array('faculty'=>'g005_faculty'),'faculty.id = program.faculty_id',array('faculty'=>'name'))
						->joinLeft(array('department'=>'g008_department'),'department.id = program.department_id',array('department'=>'name'))
						->join(array('award'=>'r003_award'),'award.id = program.award_id',array('award'=>'name'))
						->order(array('program.status desc','program.award_id desc'));
						
		}else {
			$selectData = $db ->select()
						->from(array('program'=>$this->_name))
						//->joinLeft(array('main_program'=>'r005_program_main'),'program.program_main_id = main_program.id',array('main_name'=>'name'))
						//->joinLeft(array('market'=>'r004_market'),'market.id = program.market_id',array('market'=>'name'))
						->joinLeft(array('faculty'=>'g005_faculty'),'faculty.id = program.faculty_id',array('faculty'=>'name'))
						//->joinLeft(array('department'=>'g008_department'),'department.id = program.department_id',array('department'=>'name'))
						->joinleft(array('award'=>'r003_award'),'award.id = program.award_id',array('award'=>'name'))
						->order(array('program.status desc','program.award_id desc'));
						
		}
						
		return $selectData;
	}
	
	public function addData($postData){
		$data = array(
		        'program_main_id' => $postData['program_main_id'],
				'code' => $postData['code'],
				'market_id' => $postData['market_id'],
				'faculty_id' => $postData['faculty_id'],
				'award_id' => $postData['award_id'],
				'duration' => $postData['duration'],
				'status' => $postData['status'],
				'department_id' => $postData['department_id'],
				'min_student' => $postData['min_student'],
				'synopsis' => $postData['synopsis'],
				'first_intake_id' => $postData['first_intake_id']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
		        'program_main_id' => $postData['program_main_id'],
				'code' => $postData['code'],
				'market_id' => $postData['market_id'],
				'faculty_id' => $postData['faculty_id'],
				'award_id' => $postData['award_id'],
				'duration' => $postData['duration'],
				'status' => $postData['status'],
				'department_id' => $postData['department_id'],
				'min_student' => $postData['min_student'],
				'synopsis' => $postData['synopsis'],
				'first_intake_id' => $postData['first_intake_id']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete('id =' . (int)$id);
	}
	
	public function getProgramList()	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
						->from(array('program'=>$this->_name))
						->join(array('main_program'=>'r005_program_main'),'program.program_main_id = main_program.id',array('main_name'=>'name'))
						->join(array('market'=>'r004_market'),'market.id = program.market_id',array('market'=>'name'))
						->join(array('faculty'=>'g005_faculty'),'faculty.id = program.faculty_id',array('faculty'=>'name'))
						->joinLeft(array('department'=>'g008_department'),'department.id = program.department_id',array('department'=>'name'))
						->join(array('award'=>'r003_award'),'award.id = program.award_id',array('award'=>'name'));
			                     
	  
        $result = $db->fetchAll($select);  
        $list = array("Please Select..");
		foreach ($result as $value) {
			$list[$value['program_main_id']] = $value['main_name'];
		}
        return $list;
	    
	}
	
	public function getActiveProgram()	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
						->from(array('program'=>$this->_name))
						->where('program.active = 1')
						->order('program.ArabicName');
						//->join(array('main_program'=>'r005_program_main'),'program.program_main_id = main_program.id',array('main_name'=>'name'))
						//->join(array('market'=>'r004_market'),'market.id = program.market_id',array('market'=>'name'))
						//->joinLeft(array('faculty'=>'g005_faculty'),'faculty.id = program.faculty_id',array('faculty'=>'name'))
						//->joinLeft(array('department'=>'g008_department'),'department.id = program.department_id',array('department'=>'name'))
						//->join(array('award'=>'r003_award'),'award.id = program.award_id',array('award'=>'name'));
			                     
	  
        $result = $db->fetchAll($select);  
        
		
        return $result;
	    
	}
	
	//EXAM
	public function selectProgram(){   

	  $db = Zend_Db_Table::getDefaultAdapter();
	  
	  
	  //get session user id
	  $auth = Zend_Auth::getInstance(); 
	  $user_id = $auth->getIdentity()->id; 
	  
	   $selectIn =  $db ->select()
    	             ->from('e010_exam_user',array('program_id'=>'program_id'))
    	             ->where('user_id = ?',$user_id);
    	
       $select = $db ->select()
						->from(array('program'=>$this->_name))
						->join(array('main_program'=>'r005_program_main'),'program.program_main_id = main_program.id',array('main_name'=>'name'))
						->join(array('market'=>'r004_market'),'market.id = program.market_id',array('market'=>'name'))
						->join(array('faculty'=>'g005_faculty'),'faculty.id = program.faculty_id',array('faculty'=>'name'))
						->joinLeft(array('department'=>'g008_department'),'department.id = program.department_id',array('department'=>'name'))
						->join(array('award'=>'r003_award'),'award.id = program.award_id',array('award'=>'name'))
						->where('program.id IN (?)', $selectIn);
						
					
    	$rowSet = $db->fetchAll($select);   
    	
    	return $rowSet;
    }
    
    
    
    
    public function selectSemProgram($semester_id){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $select = $db ->select()
						->from(array('program'=>$this->_name))
						->join(array('main_program'=>'r005_program_main'),'program.program_main_id = main_program.id',array('main_name'=>'name'))
						->join(array('market'=>'r004_market'),'market.id = program.market_id',array('market'=>'name'))
						->join(array('faculty'=>'g005_faculty'),'faculty.id = program.faculty_id',array('faculty'=>'name'))
						->joinLeft(array('department'=>'g008_department'),'department.id = program.department_id',array('department'=>'name'))
						->join(array('award'=>'r003_award'),'award.id = program.award_id',array('award'=>'name'))
						->join(array('semprog' => 'r002_semester_program'),'semprog.program_id = program.id')
						->where('semprog.semester_id = ?',$semester_id);
	
						
    	$rowSet = $db->fetchAll($select);    	
    	return $rowSet;
    }
    
    public function getExamUser($user_id,$type){
    	
    	$db = Zend_Db_Table::getDefaultAdapter();
    	  
    		
    	$select = $db ->select()
						->from(array('program'=>$this->_name))
						->join(array('main_program'=>'r005_program_main'),'program.program_main_id = main_program.id',array('main_name'=>'name'))
						->join(array('market'=>'r004_market'),'market.id = program.market_id',array('market'=>'name'))
						->join(array('faculty'=>'g005_faculty'),'faculty.id = program.faculty_id',array('faculty'=>'name'))
						->joinLeft(array('department'=>'g008_department'),'department.id = program.department_id',array('department'=>'name'))
						->join(array('award'=>'r003_award'),'award.id = program.award_id',array('award'=>'name'));
			
		              
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	     
	        return $row;
    }
    
    //online application
    public function getProgramOffered($award_id)	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
						->from(array('program'=>$this->_name))
						->where('program.status = 1 and program.award_id = '.$award_id)
						->join(array('main_program'=>'r005_program_main'),'program.program_main_id = main_program.id',array('main_name'=>'name'))
						->join(array('market'=>'r004_market'),'market.id = program.market_id',array('market'=>'name'))
						->joinLeft(array('faculty'=>'g005_faculty'),'faculty.id = program.faculty_id',array('faculty'=>'name'))
						->joinLeft(array('department'=>'g008_department'),'department.id = program.department_id',array('department'=>'name'))
						->join(array('award'=>'r003_award'),'award.id = program.award_id',array('award'=>'name'))
						->order(array('main_program.name'));
			                     
        $stmt = $db->query($select);
	    $result = $stmt->fetchAll();
	    
	    $count = $db->query($select)->rowCount();
	    
        return array($result,$count);
	}
	
	
/* 6.11.2012
	 * mardhiyati
	 * To get paginate Data
	 */
	public function searchPaginateProgram($data=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $selectData = $db ->select()
						->from(array('p'=>$this->_name))						
						->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('idFaculty'=>'c.IdCollege','faculty'=>'c.CollegeName'))
						->order("p.IdCollege asc");
						
		if($data!=null){				
			if($data['IdProgram']!=""){
				$selectData->where("p.IdProgram = ?",$data['IdProgram']);
			}
		}
		
		
		return $selectData;
	}
	
	
	
	
	
	public function searchProgram($data=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$auth = Zend_Auth::getInstance();
		$role = $auth->getIdentity()->role;
		
		 $selectData = $db ->select()
						->from(array('p'=>$this->_name))						
						->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('idFaculty'=>'c.IdCollege','faculty'=>'c.ArabicName'))
						->order('p.IdCollege asc');
						
		if($data!=null){				
			if(isset($data['IdProgram']) && $data['IdProgram']!=""){
				$selectData->where("p.IdProgram = ?",$data['IdProgram']);
			}
			if(isset($data['ProgramCode']) && $data['ProgramCode']!=""){
				$selectData->where("p.ProgramCode = ?",$data['ProgramCode']);
			}
			if(isset($data['IdCollege']) && $data['IdCollege']!=""){
				$selectData->where("c.IdCollege = ?",$data['IdCollege']);
			}
		}
		
		if(isset($data['IdCollege']) && $data['IdCollege']!=""){
			$result = $db->fetchAll($selectData);
		}else{
			$result = $db->fetchRow($selectData);
		}
	
		
		return $result;
	}
	
	public function searchAllProgram($data=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$auth = Zend_Auth::getInstance();
		$role = $auth->getIdentity()->role;
		
		 $selectData = $db ->select()
						->from(array('p'=>$this->_name))						
						->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('idFaculty'=>'c.IdCollege','faculty'=>'c.ArabicName'))
						->order('p.IdCollege asc');
						
		if($data!=null){
			if(isset($data['IdCollege']) && $data['IdCollege']!=""){
				$selectData->where("c.IdCollege = ?",$data['IdCollege']);
			}
		}
		
		$result = $db->fetchAll($selectData);
	
		
		return $result;
	}
	
	
	public function searchProgramByFaculty($faculty_id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
	  	$select = $db->select()
	                 ->from(array('p'=>'tbl_program'))	               
	                 ->order('p.ProgramName ASC');
	    
		if($faculty_id!=null){
			  $select->where('p.IdCollege = ?', $faculty_id);
		}	
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        return $row;
	}
	
	
	
	
}
?>