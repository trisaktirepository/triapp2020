<?php
class GeneralSetup_Model_DbTable_LandscapeBlockSubject extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_landscapeblocksubject';
   
	public function addData($data)
    {    	     
        $id = $this->insert($data);        
        return $id;
    }
    
	public function updateData($data,$id){
		 $this->update($data,'IdLandscapeblocksubject 	 = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete('IdLandscapeblocksubject 	 =' . (int)$id);
	}
    
	public function getLandscapeCourse($idLandscape){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $lstrSelect = $db->select()
		 				 ->from(array("bs"=>$this->_name))		
		 				  ->join(array("s"=>"tbl_subjectmaster"),'bs.subjectid=s.IdSubject ',array('s.IdSubject','BahasaIndonesia','SubCode','CreditHours'))
		 				 ->where("bs.IdLandscape = ?",$idLandscape)
		 				 ->order("s.BahasaIndonesia");
    	  $larrResult = $db->fetchAll($lstrSelect);
		 
		  return $larrResult;
    }
    public function getLandscapeCoursePerCategory($idLandscape,$category){
    	 
    	$db = Zend_Db_Table::getDefaultAdapter();
    	 
    	$lstrSelect = $db->select()
    	->from(array("bs"=>$this->_name),array('IdSubject'=>'subjectid'))
    	->join(array("s"=>"tbl_subjectmaster"),'bs.subjectid=s.IdSubject ',array('s.IdSubject','BahasaIndonesia','SubCode','CreditHours'))
    	->where("bs.IdLandscape = ?",$idLandscape)
    	->where("bs.coursetypeid=?",$category)
    	->order("s.BahasaIndonesia")
    	->group("s.IdSubject");
    	$larrResult = $db->fetchAll($lstrSelect);
    		
    	return $larrResult;
    }
    
	public function getPaginateLandscapeCourse($idLandscape,$keywords=null){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $lstrSelect = $db->select()
		 				 ->from(array("bs"=>$this->_name))		
		 				 ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=bs.subjectid ',array('SubjectName','BahasaIndonesia','SubCode','CreditHours'))
		 				 ->where("bs.IdLandscape = ?",$idLandscape)
		 				 ->order("s.BahasaIndonesia");

		  if($keywords){
			$lstrSelect->where("s.SubCode LIKE '%".$keywords."%'");
			$lstrSelect->whereor("s.BahasaIndonesia LIKE '%".$keywords."%'");
			$lstrSelect->whereor("s.SubjectName LIKE '%".$keywords."%'");
		  }	

			
		  $larrResult = $db->fetchAll($lstrSelect);
		  return $larrResult;
    }
    
    public function getBlockCourse($idLandscape,$idBlock,$compulsory=null){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $lstrSelect = $db->select()
		 				 ->from(array("bs"=>$this->_name))		
		 				 ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=bs.subjectid ',array('BahasaIndonesia','SubCode','CreditHours','IdSubject'))
		 				 ->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=bs.coursetypeid',array('DefinitionDesc'))
		 				 ->joinLeft(array("pr"=>"tbl_programrequirement"),'pr.IdProgramReq=bs.IdProgramReq',array())		 							
		 				 ->where("bs.IdLandscape = ?",$idLandscape)
		 				 ->where("bs.parentId = 0")
		 				 ->where("bs.blockid = ?",$idBlock);
		 				 
	    	if($compulsory){ //Compulosry
				$lstrSelect->where("pr.Compulsory = ?",$compulsory);	
			}			
						
		  $larrResult = $db->fetchAll($lstrSelect);
		  return $larrResult;
    }
    public function getBlockCourseChild($parentid,$compulsory=null){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $lstrSelect = $db->select()
		 				 ->from(array("bs"=>$this->_name))		
		 				 ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=bs.subjectid ',array('BahasaIndonesia','SubCode','CreditHours','IdSubject'))
		 				 ->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=bs.coursetypeid',array('DefinitionDesc'))
		 				 ->joinLeft(array("pr"=>"tbl_programrequirement"),'pr.IdProgramReq=bs.IdProgramReq',array())		 							
		 				 ->where("bs.parentId = ?",$parentid);
		 				 
	    	if($compulsory){ //Compulosry
				$lstrSelect->where("pr.Compulsory = ?",$compulsory);	
			}			
						
		  $larrResult = $db->fetchAll($lstrSelect);
		  return $larrResult;
    }    
    
    /*
     * This function to get list of courses to be registered by new student in first semester.
     */
    
	public function getLandscapeBlockCourseList($idLandscape,$idBlock,$idSemester){
    	
	 
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  //get Course Offer for particular semester tbl_subjectsoffered
    	  $select_course_offer = $db->select()
		 				 			->from(array("so"=>'tbl_subjectsoffered'),array('IdSubject'))	
		 				 			->where("IdSemester = ?",$idSemester);
    	  
    	  
    	  $select = $db->select()
		 				 ->from(array("bs"=>$this->_name))		
		 				 ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=bs.subjectid ',array('BahasaIndonesia','SubCode','CreditHours'))
		 				 ->joinLeft(array("lb"=>"tbl_landscapeblock"),'lb.idblock=bs.blockid',array('BlockLevel'=>'block'))
		 				 ->where("bs.IdLandscape = ?",$idLandscape)
		 				 ->where("bs.blockid = ?",$idBlock)
		 				 ->where("bs.subjectid IN (?)",$select_course_offer)
		 				 ->order("s.BahasaIndonesia");

				
		  $row = $db->fetchAll($select);
		  return $row;
    }
    
public function getBlockCourseInfo($idLandscape,$idBlock,$subjectId){
    	
    	 $db = Zend_Db_Table::getDefaultAdapter();
    	 
    	   $select = $db->select()
		 				 ->from(array("bs"=>$this->_name))		
		 				 ->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=bs.coursetypeid',array('DefinitionDesc'))
		 				 ->where("bs.IdLandscape = ?",$idLandscape)
		 				 ->where("bs.blockid = ?",$idBlock)
		 				 ->where("bs.subjectid = ?",$subjectId);
		 				 
		  $row = $db->fetchRow($select);
		  return $row;
    }

    
	public function getBlockSemCourseList($idLandscape,$idSemester){
    	
	 
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  //get Course Offer for particular semester tbl_subjectsoffered
    	  $select_course_offer = $db->select()
		 				 			->from(array("so"=>'tbl_subjectsoffered'),array('IdSubject'))	
		 				 			->where("IdSemester = ?",$idSemester);
    	  
    	  
    	  $select = $db->select()
		 				 ->from(array("lbs"=>"tbl_landscapeblocksemester"))	
		 				 ->where("lbs.IdLandscape = ?",$idLandscape)
		 				 ->where("lbs.semesterid = ?",$idSemester);
		 				 
		  $blocks = $db->fetchAll($select);
		  
		  foreach($blocks as $block){
		  	
		  	 $sel_course = $db->select()
		 				 	  ->from(array("lbs"=>"tbl_landscapeblocksubject"))	
		 				 	  ->where("lbs.IdLandscape = ?",$idLandscape)		 				 	 
		 				 	  ->where("lbs.blockid  = ?",$block["blockid"]);
		 				 	  
		 	 $courses = $db->fetchAll($sel_course);
		 	 
		  }
		  
    }
    
	
    
	public function getCoursebyBlock($idLandscape,$blockLevel,$idSemester){
    	
	 
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  //get Course Offer for particular semester tbl_subjectsoffered
    	  $select_course_offer = $db->select()
		 				 			->from(array("so"=>'tbl_subjectsoffered'),array('IdSubject'))	
		 				 			->where("IdSemester = ?",$idSemester);    	  
    	  
    	  $select = $db->select()		 				
		 				 ->from(array("lb"=>"tbl_landscapeblock"))		
		 				 ->where("lb.idlandscape = ?",$idLandscape)
		 				 ->where("lb.block = ?",$blockLevel)		 				 
		 				 ->order("lb.block asc");
		 				 	 				 
		  $block = $db->fetchRow($select);
		  		  
		  //print_r($block);
		 
		   $sel_course = $db->select()
		 				 	  ->from(array("lbs"=>"tbl_landscapeblocksubject"))	
		 				 	  ->joinLeft(array("lb"=>"tbl_landscapeblock"),'lb.idblock=lbs.blockid',array('blockname','block_level'=>'block'))	
		 				 	  ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=lbs.subjectid',array('SubjectName','BahasaIndonesia','SubCode','CreditHours'))
		 				 	  ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = lbs.coursetypeid',array('DefinitionDesc'))
		 				 	  ->where("lbs.IdLandscape = ?",$idLandscape)		 				 	 
		 				 	  ->where("lbs.blockid  = ?",$block["idblock"])
		 				 	  ->where("lbs.subjectid IN (?)",$select_course_offer);
		 				 	  
		   $courses = $db->fetchAll($sel_course);

		   return $courses;
		 
    }
    
	public function getMultiLandscapeCourse($Landscapes,$formdata=null){
	    	
	    	  $db = Zend_Db_Table::getDefaultAdapter();
	    	
	    	  $lstrSelect = $db->select()
	    	  				->distinct()
			 				 ->from(array("ls"=>$this->_name),array())	
			 				 ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.subjectid ',array('IdSubject','SubjectName'=>'BahasaIndonesia','SubCode','CreditHours','key'=>'IdSubject','name'=>'BahasaIndonesia'))
			 				 ->join(array('c'=>'tbl_collegemaster'),'c.IdCollege=s.IdFaculty',array('facultyName'=>'ArabicName'))			 				
			 				 ->group("ls.subjectid")
			 				 ->order("s.SubCode");
			 				 
			 foreach ($Landscapes as $landscape) {
			 	$lstrSelect->orwhere("(ls.IdLandscape = ?",$landscape);
			 	$lstrSelect->where("ls.type != 2)");
			 }	
			 
			 /*if(isset($formdata["IdSemester"]) && $formdata["IdSemester"]!=''){
			 	$lstrSelect->join(array('lb'=>'tbl_landscapeblocksemester'),'lb.IdLandscape=ls.IdLandscape');
			 	$lstrSelect->where("lb.semesterid = ?",$formdata["IdSemester"]);
			 }*/
			 			 
	 		 if(isset($formdata["subject_code"]) && $formdata["subject_code"]!=''){			 
			 	$lstrSelect->where("s.SubCode LIKE '%".$formdata["subject_code"]."%'");
			 }
		
			 if(isset($formdata["subject_name"]) && $formdata["subject_name"]!=''){			 
			 	$lstrSelect->where("s.SubjectName LIKE '%".$formdata["subject_name"]."%'");
			 	$lstrSelect->orwhere("s.BahasaIndonesia LIKE '%".$formdata["subject_name"]."%'");
			 }
			 		
			//echo $lstrSelect;
			$rows = $db->fetchAll($lstrSelect);
			return $rows;
	    }    
	    
   public function getBlockId($idLandscape,$subjectid){
    	
    	 $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	 $sel_course = $db->select()
		 				 	  ->from(array("lbs"=>"tbl_landscapeblocksubject"))	
		 				 	  ->join(array("b"=>'tbl_landscapeblock'),'b.idblock=lbs.blockid',array('level'=>'block'))
		 				 	  ->where("lbs.IdLandscape = ?",$idLandscape)		 				 	 
		 				 	  ->where("lbs.subjectid  = ?",$subjectid);
		 				 	  
		   $courses = $db->fetchRow($sel_course);

		   return $courses;
    }
    
    
 	public function getLandscapeSubjectInfo($idLandscape,$subjectid){
    	
    	 $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	 $sel_course = $db->select()
		 				 	  ->from(array("lbs"=>"tbl_landscapeblocksubject"))			 				 	  		 				 	 
		 				 	  ->where("lbs.IdLandscape = ?",$idLandscape)		 				 	 
		 				 	  ->where("lbs.subjectid  = ?",$subjectid);

		 				 	 
	    $courses = $db->fetchRow($sel_course);
 
	    return $courses;
    }
    
	public function getChild($subjectId,$IdBlock,$idLandscape){
    	
    	 $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	 $sel_course = $db->select()
		 				 	  ->from(array("lbs"=>"tbl_landscapeblocksubject"),array('IdLandscapeblocksubject'))			 				 	 
		 				 	  ->where("lbs.subjectid = ?",$subjectId)
		 				 	  ->where("lbs.blockid = ?",$IdBlock)
		 				 	  ->where("lbs.IdLandscape = ?",$idLandscape);
		 				 	  
	    $course = $db->fetchRow($sel_course);
	    
	    if($course){
	    	$sel_child = $db->select()
		 				 	  ->from(array("lbs"=>"tbl_landscapeblocksubject"))	
		 				 	  ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=lbs.subjectid',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category'))
		 				 	  ->where("lbs.parentId = ?",$course['IdLandscapeblocksubject']);
	    	
	    	$childs = $db->fetchAll($sel_child);
	    }
		
	    return $childs;
    }	
	    
    public function getChildByParentId($parentid){
    	
    	$db = Zend_Db_Table::getDefaultAdapter();
    	  	    
	    $sel_child = $db->select()
		 				 	  ->from(array("lbs"=>"tbl_landscapeblocksubject"))	
		 				 	  ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=lbs.subjectid',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category'))
		 				 	  ->where("lbs.parentId = ?",$parentid);
		
 		$childs = $db->fetchAll($sel_child);
	    return $childs;
    }	
    
     public function getData($id){
    	
    	$db = Zend_Db_Table::getDefaultAdapter();
    	  	    
	    $sel = $db->select()
		 				 	  ->from(array("lbs"=>"tbl_landscapeblocksubject"))	
		 				 	  ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=lbs.subjectid',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category'))
		 				 	  ->where("lbs.IdLandscapeblocksubject = ?",$id);
		
 		$row = $db->fetchRow($sel);
	    return $row;
    }	
    
    
     public function getOtherChildByParentId($parentid,$subjectId,$IdStudentRegistration){
    	
    	$db = Zend_Db_Table::getDefaultAdapter();
    	
    	/* SELECT * FROM `tbl_studentregsubjects` WHERE `IdStudentRegistration`='2720' and `IdSubject` IN 
    	 * (SELECT `lbs`.`subjectid` FROM `tbl_landscapeblocksubject` AS `lbs` WHERE (lbs.parentId = '1') AND (lbs.subjectid != '3898'))
    	 */
    	  	    
	    $select1 = $db->select()
 				 	    ->from(array("lbs"=>"tbl_landscapeblocksubject"),array('subjectid'))			 				 	  
 				 	    ->where("lbs.parentId = ?",$parentid)
 				 	    ->where('lbs.subjectid != ?',$subjectId);
 				 	    
 	    $select2 = $db->select()
 				 	    ->from(array("srs"=>"tbl_studentregsubjects"),array('IdStudentRegSubjects'))			 				 	  
 				 	    ->where("srs.IdStudentRegistration = ?",$IdStudentRegistration)
 				 	    ->where('srs.IdSubject IN (?)',$select1);
		
 		$result = $db->fetchAll($select2);
	    return $result;
    }	
	
    public function getLandscapeCourseResult($idLandscape){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $lstrSelect = $db->select()
		 				 ->from(array("bs"=>$this->_name))		
		 				 ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=bs.subjectid ',array('BahasaIndonesia','SubCode','CreditHours'))
		 				 ->where("bs.IdLandscape = ?",$idLandscape)
		 				 ->order("s.BahasaIndonesia");
		  $larrResult = $db->fetchAll($lstrSelect);
		  return $larrResult;
    }    
   

}
?>