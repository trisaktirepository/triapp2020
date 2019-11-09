<?php 

class App_Model_Record_DbTable_Studentsemesterstatus extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_studentsemesterstatus';
	protected $_primary = "idstudentsemsterstatus";

			
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
	

	public function getRegisteredBlock($registrationId){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                   ->from(array('s' => $this->_name))   
                   ->join(array('b'=>'tbl_landscapeblock'),'b.idblock=s.IdBlock',array('blockname','semester_level'=>'semester'))      
                   ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=s.IdSemesterMain',array('SemesterMainName','SemesterMainCode'))       
                   ->where('s.IdStudentRegistration = ?',$registrationId) 
                   ->order("s.idstudentsemsterstatus")
                   ->group('s.IdBlock'); 
                  
        $result = $db->fetchAll($sql);
        return $result;
	}
	
	
	public function getRegisteredSemester($registrationId){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                   ->from(array('s' => $this->_name))  
                   ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=s.IdSemesterMain',array('SemesterMainName','SemesterMainCode'))  
                   ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=s.studentsemesterstatus',array('semester_status'=>'DefinitionDesc')) 
                   ->joinLeft(array('u'=>'tbl_user'),'u.iduser=s.UpdUser',array('lName','mName','fName'))    
                   ->where('s.IdStudentRegistration = ?',$registrationId)
                   ->order("s.idstudentsemsterstatus")
                   ->group('s.IdSemesterMain'); 
                  
        $result = $db->fetchAll($sql);
        return $result;
	}
	
	public function getMaxLevel($registrationId){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$sql = $db->select()
		->from(array('s' => $this->_name),array('MaxLevel'=>'MAX(Level)'))
		->where('s.IdStudentRegistration = ?',$registrationId);
	
		$result = $db->fetchRow($sql);
		return $result['MaxLevel'];
	}
	
	public function getMaxLevelPrevious($registrationId,$idsemester){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$sql = $db->select()
		->from(array('s' => $this->_name),array('MaxLevel'=>'MAX(Level)'))
		->where('s.IdStudentRegistration = ?',$registrationId)
		->where('s.IdSemesterMain<>?',$idsemester);
	
		$result = $db->fetchRow($sql);
		return $result['MaxLevel'];
	}
	
	public function getRegisteredSemesterWithPembaikan($registrationId){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                   ->from(array('s' => $this->_name))  
                   ->joinLeft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=s.IdSemesterMain',array('SemesterMainName','SemesterMainCode'))  
                   ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=s.studentsemesterstatus',array('semester_status'=>'DefinitionDesc')) 
                   ->joinLeft(array('u'=>'tbl_user'),'u.iduser=s.UpdUser',array('lName','mName','fName'))    
                   ->where('s.IdStudentRegistration = ?',$registrationId)
				   ->where('sm.SemesterFunctionType IN (0,1)')
                   ->order("s.idstudentsemsterstatus")
                   ->group('s.IdSemesterMain'); 
                  
        $result = $db->fetchAll($sql);
        return $result;
	}
	public function getRegisteredSemesterKHS($registrationId){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$sql = $db->select()
		->from(array('s' => $this->_name))
		->joinLeft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=s.IdSemesterMain',array('SemesterMainName','SemesterMainCode'))
		->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=s.studentsemesterstatus',array('semester_status'=>'DefinitionDesc'))
		->joinLeft(array('u'=>'tbl_user'),'u.iduser=s.UpdUser',array('lName','mName','fName'))
		->where('s.IdStudentRegistration = ?',$registrationId)
		//->where('sm.SemesterFunctionType IN (0,1)')
		->order("s.idstudentsemsterstatus")
		->group('s.IdSemesterMain');
	
		$result = $db->fetchAll($sql);
		return $result;
	}
	
	public function getData($idstudentsemsterstatus=0){
		
		if($idstudentsemsterstatus!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name))					
					->where('s.idstudentsemsterstatus = ?',$idstudentsemsterstatus);
					
			$stmt = $db->query($select);						
			$row = $stmt->fetch();		
		}else{
			$row = $this->fetchAll();
			$row=$row->toArray();
		}
		
		if(!$row){
			throw new Exception("There is No Information Found");
		}
		return $row;
	}
	
	
	public function getSemesterInfo($idstudentsemsterstatus=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                   ->from(array('s' => $this->_name))  
                   ->joinleft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=s.IdSemesterMain',array('SemesterMainName','SemesterMainCode'))  
                   ->where('s.idstudentsemsterstatus = ?',$idstudentsemsterstatus);                   
                  
        $result = $db->fetchRow($sql);
        return $result;
	}
	
	public function getRegisteredSemesterBlock($registrationId,$idSemester=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                   ->from(array('s' => $this->_name))   
                   ->joinleft(array('b'=>'tbl_landscapeblock'),'b.idblock=s.IdBlock',array('blockname','semester_level'=>'semester'))      
                   ->joinleft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=s.IdSemesterMain',array('SemesterMainName','SemesterMainCode'))       
                   ->where('s.IdStudentRegistration = ?',$registrationId)                  
                   ->order("s.idstudentsemsterstatus");
                  
        if(isset($idSemester)){
        	 $sql->where('s.IdSemesterMain = ?',$idSemester) ;
        }          
        
		$result = $db->fetchAll($sql);
        
        //print_r($result);
        return $result;
	}
	
	public function getRegisteredSemesterBlockKHS($registrationId,$idSemester=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                   ->from(array('s' => $this->_name))   
                   ->joinleft(array('b'=>'tbl_landscapeblock'),'b.idblock=s.IdBlock',array('blockname','semester_level'=>'semester'))      
                   ->joinleft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=s.IdSemesterMain',array('SemesterMainName','SemesterMainCode'))       
                   ->where('s.IdStudentRegistration = ?',$registrationId)                  
                   //->where('sm.SemesterFunctionType IN (0,1)')                  
                   ->order("s.idstudentsemsterstatus");
                  
        if(isset($idSemester)){
        	 $sql->where('s.IdSemesterMain = ?',$idSemester) ;
        }          
        //echo $sql;
		$result = $db->fetchAll($sql);
        
        //print_r($result);
        return $result;
	}
	public function getRegisteredSemesterBlockWithPembaikan($registrationId,$idSemester=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$sql = $db->select()
		->from(array('s' => $this->_name))
		->joinleft(array('b'=>'tbl_landscapeblock'),'b.idblock=s.IdBlock',array('blockname','semester_level'=>'semester'))
		->joinleft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=s.IdSemesterMain',array('SemesterMainName','SemesterMainCode'))
		->where('s.IdStudentRegistration = ?',$registrationId)
		->where('sm.SemesterFunctionType IN (0,1)')
		->order("s.idstudentsemsterstatus");
	
		if(isset($idSemester)){
			$sql->where('s.IdSemesterMain = ?',$idSemester) ;
		}
		//echo $sql;
		$result = $db->fetchAll($sql);
	
		//print_r($result);
		return $result;
	}
	
	public function getCountableRegisteredSemester($registrationId,$semid=0){
		
		//utk dapatakan jumlah semester yg sudah pernah daftar, countable dan status completed atau register
		//jika dulu pernah daftar then quit/tangguh maka ia tidak dikira
		//jika semester tu not countable cth semster pembaikan maka ia tidak dikira naik level
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                   ->from(array('s' => $this->_name))  
                   ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=s.IdSemesterMain',array('SemesterMainName','SemesterMainCode'))                    
                   ->where('s.IdStudentRegistration = ?',$registrationId)
                   ->where('sm.IsCountable = 1')
                   ->where('s.IdSemesterMain <> ?',$semid)
                   ->where('(s.studentsemesterstatus = 130 OR s.studentsemesterstatus = 229)') //130:Register 229:Completed
                   ->order("s.idstudentsemsterstatus");                  
                  
        $result = $db->fetchAll($sql);
        return $result;
	}
	
	public function isSemRegistered($regId,$semId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
                   ->from(array('s' => $this->_name))  
                   ->where('s.IdStudentRegistration = ?',$regId) 
                   ->where('s.IdSemesterMain = ?',$semId);
        $result = $db->fetchrow($sql);
        return $result;                    
	}	
	
	public function getRegisteredSemesterByLevel($registrationId,$level){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                   ->from(array('s' => $this->_name))  
                   ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=s.IdSemesterMain',array('SemesterMainName','SemesterMainCode')) 
                   ->where('s.IdStudentRegistration = ?',$registrationId)
                   ->where('s.Level =?',$level);
                  
        $result = $db->fetchRow($sql);
        return $result;
	}	
	
	public function getCurrentRegSem($IdStudentRegistration){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
                   ->from(array('sss' => $this->_name),array()) 
                   ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=sss.IdSemesterMain',array('IdSemesterMaster','SemesterMainName','SemesterMainCode','SemesterMainStartDate','SemesterMainEndDate')) 
                   ->where('SemesterMainStartDate  <=  CURDATE()')
                   ->where('SemesterMainEndDate    >=  CURDATE()')
                   ->where('sss.IdStudentRegistration = ?',$IdStudentRegistration);
		 
        $result = $db->fetchRow($sql);
        return $result;                    
	}		
}

?>