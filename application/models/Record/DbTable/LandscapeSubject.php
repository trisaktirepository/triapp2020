<?php
class App_Model_Record_DbTable_LandscapeSubject extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_landscapesubject';
    protected $_primary = 'IdLandscapeSub';
   
    public function getLandscapeCourseByLevel($idLandscape,$level){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	   $select = $db->select()
		 				 ->from(array("ls"=>$this->_name))		
		 				 ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.IdSubject',array('BahasaIndonesia','SubCode','CreditHours'))
 						 ->join(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('SubjectType'=>'DefinitionDesc'))
		 				 ->where("ls.IdLandscape = ?",$idLandscape)
		 				 ->where("ls.IdSemester = ?",$level)
		 				 ->order("s.SubCode");
		// echo $select;exit;
		  $larrResult = $db->fetchAll($select);
		  return $larrResult;
    }
    
 	public function getLandscapeCourse($idLandscape,$subject_code=null,$level,$idSemester,$IdStudentRegistration){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $select2 = $db->select()
		 				 ->from(array("ls"=>$this->_name),array('ls.IdSubject'))
		 				 ->where("ls.IdLandscape = ?",$idLandscape)
		 				 ->where("ls.IdSemester = ?",$level); //is actually level of semester
    	  
    	  $select = $db->select()
		 				 ->from(array("ls"=>$this->_name))		
		 				 ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.IdSubject',array('BahasaIndonesia','SubCode','CreditHours'))
 						 ->join(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('SubjectType'=>'DefinitionDesc'))
		 				 ->where("ls.IdLandscape = ?",$idLandscape)	
		 				 ->where("ls.IdSubject NOT IN (?)",$select2)	 				
		 				 ->order("s.SubCode");
		 				 
		  if(isset($subject_code) && $subject_code!=''){
		  	$select->where("s.SubCode LIKE '%".$subject_code."%'");
		  }

		  $result = $db->fetchAll($select);
		  
		
		  
		 foreach($result as $key=>$row){
		  	
			  	//get status subject offer or not?
	         	$subjectOfferDb = new App_Model_Record_DbTable_SubjectsOffered();
	         	$subject_offer = $subjectOfferDb->getSubjectsOfferBySemester($idSemester,$row["IdSubject"]);
			  	
	         	if(is_array($subject_offer)){
	         		$result[$key]['offer_status']="Offered";	         		
	         	}else{
	         		unset($result[$key]);
	         		//$result[$key]['offer_status']="Not Offered";	         		
	         	}		  	
	         	
	         	//get subject status already taken/registered or not
	         	$subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
	         	$subject_registered = $subjectRegDB->isRegister($IdStudentRegistration,$row["IdSubject"],$idSemester);	
	         	
	         	if(is_array($subject_registered)){
	         		$result[$key]['register_status']="Registered";
	         		$result[$key]['register']=1;	 
	         	} else{
	         		$result[$key]['register_status']="Not Registered";
	         		$result[$key]['register']=2;
	         	}        	
		  }
		  
		
		 
		  return $result;
    }
    
    
    //allow student to search course from landscape where semester level is < from current semester level and subject offered.
	public function getPreviousLevelCourseList($idLandscape,$subject_code=null,$level,$idSemester,$IdStudentRegistration){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();    	  
    	   
    	  $select = $db->select()
		 				 ->from(array("ls"=>$this->_name))		
		 				 ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.IdSubject',array('BahasaIndonesia','SubCode','CreditHours'))
 						 ->join(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('SubjectType'=>'DefinitionDesc'))
 						 ->join(array('so'=>'tbl_subjectsoffered'),'so.IdSubject=ls.IdSubject',array())
		 				 ->where("ls.IdLandscape = ?",$idLandscape)	
		 				 ->where("ls.IdSemester < ?",$level)			 				 				
		 				 ->order("ls.IdSemester") //is actually semester level
		 				 ->order("s.SubCode")
		 				 ->group('ls.IdSubject');
		 				 
		  if(isset($subject_code) && $subject_code!=''){
		  	$select->where("s.SubCode LIKE '%".$subject_code."%'");
		  }

		  $result = $db->fetchAll($select);		  		
		  
		 foreach($result as $key=>$row){
		  		         	
		 	     //setkan xde anak jika macam dentistry ada anak so kene enhance coding ni kalo xbuat mcm ni nanti jquery x appear ada bugs
		 	     $result[$key]['child'] = ''; 
		 	     
	         	//get subject status already taken/registered or not
	         	$subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
	         	$subject_registered = $subjectRegDB->isRegister($IdStudentRegistration,$row["IdSubject"],$idSemester);	
	         	
	         	if(is_array($subject_registered)){
	         		$result[$key]['register_status']="Registered";
	         		$result[$key]['register']=1;	 
	         	} else{
	         		$result[$key]['register_status']="Not Registered";
	         		$result[$key]['register']=2;
	         	}        	
		  }
		  
		
		 
		  return $result;
    }
    
    public function getSubjectCreditHours($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $sql = $db->select()
               ->from('tbl_subjectmaster')
               ->where('idSubject = ?', (int)$id);
        
        $row = $db->fetchRow($sql);
        
        //print_r($row);
        $creditHours = $row['CreditHours'];
        
        return $creditHours;
    }
    
    public function getCoursePrerequisite($idLandscape,$idLandscapeSub){
		
        $db = Zend_Db_Table::getDefaultAdapter();
        $select 	= $db->select()
                         ->from(array("sp" =>"tbl_subjectprerequisites"))
                         ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=sp.IdRequiredSubject',array('BahasaIndonesia','SubCode'))				
                         ->where("sp.IdLandscape= ?",(int)$idLandscape)
                         ->where("sp.IdLandscapeSub= ?",(int)$idLandscapeSub);
                        // ->where("sp.IdSubject= ?",$idSubject);
        
        //echo $select;
        return $result = $db->fetchAll($select);
		
	}
	
	public function getCoursePrerequisiteOr($idSubjectPrereq){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$select 	= $db->select()
		->from(array("sp" =>"tbl_subjectprerequisites_Or"))
		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=sp.IdRequiredSubject',array('BahasaIndonesia','SubCode'))
		->where("sp.IdSubjectPrerequisites= ?",(int)$idSubjectPrereq);
		 
		return $result = $db->fetchAll($select);
	
	}
    
}