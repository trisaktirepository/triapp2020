<?php
class App_Model_Record_DbTable_LandscapeBlockSubject extends Zend_Db_Table_Abstract
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
		 				 ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=bs.subjectid ',array('BahasaIndonesia','SubCode','CreditHours'))
		 				 ->where("bs.IdLandscape = ?",$idLandscape)
		 				 ->order("s.BahasaIndonesia");
		 
		  return $lstrSelect;
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
		 				 ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=bs.subjectid ',array('BahasaIndonesia','SubCode','CreditHours'))
		 				 ->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=bs.coursetypeid',array('DefinitionDesc'))
		 				 ->joinLeft(array("pr"=>"tbl_programrequirement"),'pr.IdProgramReq=bs.IdProgramReq',array())		 							
		 				 ->where("bs.IdLandscape = ?",$idLandscape)
		 				 ->where("bs.blockid = ?",$idBlock);
		 				 
	    	if($compulsory){ //Compulosry
				$lstrSelect->where("pr.Compulsory = ?",$compulsory);	
			}			
						
		  $larrResult = $db->fetchAll($lstrSelect);
		  return $larrResult;
    }
    public function getBlock($idLandscape,$idsubject){
    	 
    	$db = Zend_Db_Table::getDefaultAdapter();
    	 
    	$lstrSelect = $db->select()
    	->from(array("bs"=>$this->_name))
    	->join(array('bl'=>'tbl_landscapeblock'),'bl.idblock=bs.blockid')
    	->where("bs.IdLandscape = ?",$idLandscape)
    	->where("bs.subjectid = ?",$idsubject);
     
    	$larrResult = $db->fetchRow($lstrSelect);
    	if ($larrResult) return $larrResult['bl.block']; else return 0;
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
		  
		  print_r($courses);
		 
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
    
	public function getMultiLandscapeCourse($Landscapes){
	    	
	    	  $db = Zend_Db_Table::getDefaultAdapter();
	    	  
	    	  $lstrSelect = $db->select()
	    	  				->distinct()
			 				 ->from(array("ls"=>$this->_name),array())		
			 				 ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.subjectid ',array('IdSubject','SubjectName'=>'BahasaIndonesia','SubCode','CreditHours'))
			 				 ->join(array('c'=>'tbl_collegemaster'),'c.IdCollege=s.IdFaculty',array('facultyName'=>'ArabicName'))
			 				 ->group("ls.subjectid")
			 				 ->order("s.BahasaIndonesia");
			 				 
			 foreach ($Landscapes as $landscape) {
			 	$lstrSelect->orwhere("ls.IdLandscape = ?",$landscape);
			 }	
			 
			 $rows = $db->fetchAll($lstrSelect);
			return $rows;
	}

	    
	public function getLandscapeCourseByLevel($idLandscape,$level,$registration_id=null,$idSemester=null){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
    	
    	  $select = $db->select()
 				 	  ->from(array("bs"=>"tbl_landscapeblocksemester"))	
 				 	  ->join(array('lb'=>'tbl_landscapeblock'),'lb.idblock=bs.blockid',array('block_level'=>'block'))
 				 	  ->join(array("lbs"=>"tbl_landscapeblocksubject"),'lbs.`blockid`=bs.`blockid`',array('IdSubject'=>'subjectid','IdLandscapeSub'=>'blockid','parentId','IdLandscapeblocksubject','type'))	
 				 	  ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=lbs.subjectid',array('SubjectName','BahasaIndonesia','SubCode','CreditHours'))
 				 	  ->join(array('pr'=>'tbl_programrequirement'),'pr.IdProgramReq=lbs.IdProgramReq',array('Compulsory'))
 				 	  ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = lbs.coursetypeid',array('SubjectType'=>'DefinitionDesc'))
 				 	  ->join(array('so'=>'tbl_subjectsoffered'),'so.IdSubject=lbs.subjectid',array())
 				 	  ->where("bs.IdLandscape = ?",$idLandscape)
 				 	  ->where("bs.semesterid = ?",$level)
 				 	  ->where("parentId = 0")
 				 	  ->group('lbs.subjectid'); //level semester sebenarnya				 	  
		 				 
		  $larrResult = $db->fetchAll($select);
		  
		  foreach($larrResult as $index=>$row){
		  	
		  	     //get child if bapak (sekiranya bujang mmg xde anak)nak bg cepat xyah loop skip terus
		  	     if($row["type"]==2){
			  		 $select_child = $db->select()
			 				 	  	    ->from(array("lbs"=>"tbl_landscapeblocksubject"),array('IdSubject'=>'subjectid','IdLandscapeSub'=>'blockid','parentId','IdLandscapeblocksubject'))	
			 				 	  	    ->join(array('lb'=>'tbl_landscapeblock'),'lb.idblock=lbs.blockid',array('block_level'=>'block'))
			 				 	  	    ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=lbs.subjectid',array('SubjectName','BahasaIndonesia','SubCode','CreditHours'))
			 				 	  	    ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = lbs.coursetypeid',array('SubjectType'=>'DefinitionDesc'))
			 				 	  	    ->join(array('so'=>'tbl_subjectsoffered'),'so.IdSubject=lbs.subjectid',array())
			 				 	  	    ->where('parentId = ?',$row["IdLandscapeblocksubject"])
			 				 	  	    ->group('IdSubject');
			 				 	  	    
	 				$childResult = $db->fetchAll($select_child);
	 				
	 				
	 				foreach($childResult as $key=>$child_row){
	 					
	 					//cek already register or not	 					
			         	$subject_registered = $subjectRegDB->isRegister($registration_id,$child_row["IdSubject"],$idSemester);	
			         	
			         	if(is_array($subject_registered)){				         	
			         		$childResult[$key]['child_register_status']="Registered";
			         		$childResult[$key]['child_register']=1;
			         	} else{
			         		$childResult[$key]['child_register_status']="Not Registered";
			         		$childResult[$key]['child_register']=2;
			         	}   
	 					
	 				}//end foreach childresult
	 				
	 				$larrResult[$index]['child'] = $childResult; 
	 				
		  	   }//end if rowtype=2
		  }//end foreach larrresult
		  
		  
		  return $larrResult;
    }
    
    
	public function getBlockLandscapeCourse($idLandscape,$subject_code=null,$level,$idSemester,$IdStudentRegistration){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $select2 = $db->select()
		 				 ->from(array("lbs"=>$this->_name),array('subjectid'))
		 				 ->join(array('bs'=>'tbl_landscapeblocksemester'),'lbs.`blockid`=bs.`blockid`',array())
		 				 ->where("bs.IdLandscape  = ?",$idLandscape)
		 				 ->where("bs.semesterid = ?",$level); //is actually level of semester
    	  
    	  $select = $db->select()
		 				 ->from(array("ls"=>$this->_name))		
		 				 ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.subjectid',array('BahasaIndonesia','SubCode','CreditHours'))
 						 ->join(array("d"=>"tbl_definationms"),'d.idDefinition=ls.coursetypeid',array('SubjectType'=>'DefinitionDesc'))
		 				 ->where("ls.IdLandscape = ?",$idLandscape)	
		 				 ->where("ls.subjectid NOT IN (?)",$select2)	 				
		 				 ->order("s.SubCode");
		 				 
		  if(isset($subject_code) && $subject_code!=''){
		  	$select->where("s.SubCode LIKE '%".$subject_code."%'");
		  }

		  $result = $db->fetchAll($select);
		  
		
		  
		 foreach($result as $key=>$row){
		  	
		 			//get status subject offer or not?
		         	$subjectOfferDb = new App_Model_Record_DbTable_SubjectsOffered();
		         	$subject_offer = $subjectOfferDb->getSubjectsOfferBySemester($idSemester,$row["subjectid"]);
		         	
		         	if(is_array($subject_offer)){
		         		$result[$key]['offer_status']='Offered';		         		
		         	}else{
		         		$result[$key]['offer_status']='Not Offered';		         		
		         	}
		         	
		         	//get subject status already taken at previous semester or not
		         	$subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
		         	$subject_registered = $subjectRegDB->isRegister($IdStudentRegistration,$row["subjectid"]);	
		         	
		         	if(is_array($subject_registered)){
		         		$result[$key]['register_status']="Registered";
		         	} else{
		         		$result[$key]['register_status']="";
		         	}     
		  }
		  
		
		 
		  return $result;
    }
    
    /* Nak list semua subject dalam landscape yg dioofer pada semester yg dipilih. Kalo subkecy tu dah register pada semester yg dipilih jgn allow register lagi*/
	public function getLandscapeCourseList($idLandscape,$course_code,$level,$semester_id,$student_id){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  $subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
    	  
    	 $select = $db->select()
 				 	  ->from(array("bs"=>"tbl_landscapeblocksemester"))	
 				 	  ->join(array('lb'=>'tbl_landscapeblock'),'lb.idblock=bs.blockid',array('block_level'=>'block'))
 				 	  ->join(array("lbs"=>"tbl_landscapeblocksubject"),'lbs.`blockid`=bs.`blockid`',array('IdSubject'=>'subjectid','IdLandscapeSub'=>'blockid','parentId','IdLandscapeblocksubject','type'))	
 				 	  ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=lbs.subjectid',array('SubjectName','BahasaIndonesia','SubCode','CreditHours'))
 				 	 // ->join(array('pr'=>'tbl_programrequirement'),'pr.IdProgramReq=lbs.IdProgramReq',array('Compulsory'))
 				 	  ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = lbs.coursetypeid',array('SubjectType'=>'DefinitionDesc'))
 				 	  ->join(array('so'=>'tbl_subjectsoffered'),'so.IdSubject=lbs.subjectid',array())
 				 	  ->where("bs.IdLandscape = ?",$idLandscape) 				 	
 				 	  ->where("parentId = 0")
 				 	  ->group('lbs.subjectid'); //level semester sebenarnya				 	  
		 				 
		  $larrResult = $db->fetchAll($select);
		 
		  foreach($larrResult as $index=>$row){
		  	
		  		//get BAPAK subject status already taken/registered or not	         	
	         	$subject_registered = $subjectRegDB->isRegister($student_id,$row["IdSubject"],$semester_id);	
	         	
	         	if(is_array($subject_registered)){
	         		$larrResult[$index]['register_status']="Registered";
	         		$larrResult[$index]['register']=1;	 
	         	} else{
	         		$larrResult[$index]['register_status']="Not Registered";
	         		$larrResult[$index]['register']=2;
	         	}        
		  	
		  	     //get child if bapak (sekiranya bujang mmg xde anak)nak bg cepat xyah query skip terus
		  	     if($row["type"]==2){
			  		 $select_child = $db->select()
			 				 	  	    ->from(array("lbs"=>"tbl_landscapeblocksubject"),array('IdSubject'=>'subjectid','IdLandscapeSub'=>'blockid','parentId','IdLandscapeblocksubject'))	
			 				 	  	    ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=lbs.subjectid',array('SubjectName','BahasaIndonesia','SubCode','CreditHours'))
			 				 	  	    ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = lbs.coursetypeid',array('SubjectType'=>'DefinitionDesc'))
			 				 	  	    ->join(array('so'=>'tbl_subjectsoffered'),'so.IdSubject=lbs.subjectid',array())
			 				 	  	    ->where('parentId = ?',$row["IdLandscapeblocksubject"])
			 				 	  	    ->group('IdSubject');
			 				 	  	    
	 				$childResult = $db->fetchAll($select_child);
	 				
	 				
	 				foreach($childResult as $key=>$child_row){
	 					
	 					//cek already register or not	 					
			         	$subject_registered = $subjectRegDB->isRegister($student_id,$child_row["IdSubject"],$semester_id);	
			         	
			         	if(is_array($subject_registered)){				         	
			         		$childResult[$key]['child_register_status']="Registered";
			         		$childResult[$key]['child_register']=1;
			         	} else{
			         		$childResult[$key]['child_register_status']="Not Registered";
			         		$childResult[$key]['child_register']=2;
			         	}   
	 					
	 				}//end foreach childresult
	 				
	 				$larrResult[$index]['child'] = $childResult; 
	 				
		  	   }else{
		  	   		$larrResult[$index]['child'] = ''; 
		  	   }//end if rowtype=2
		  }//end foreach larrresult
		  
		  return $larrResult;
    }
    
    
    //allow student to search course from block landscape where semester level is < from current semester level and subject offered.
    //TAPI jika subject tu pernah di register sebelum ni (ikut logik kalo block mmg wajib xde yg x wajib) allow anak sahaja utk diregister(repeat).
    //jika x pernah so bila BAPAK diclick/register auto anak ikut
    
	public function getPreviousLevelCourseList($idLandscape,$subject_code,$level,$semester_id,$student_id){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
    	  
    	 $select = $db->select()
 				 	  ->from(array("bs"=>"tbl_landscapeblocksemester"))	
 				 	  ->join(array('lb'=>'tbl_landscapeblock'),'lb.idblock=bs.blockid',array('block_level'=>'block'))
 				 	  ->join(array("lbs"=>"tbl_landscapeblocksubject"),'lbs.`blockid`=bs.`blockid`',array('IdSubject'=>'subjectid','IdLandscapeSub'=>'blockid','parentId','IdLandscapeblocksubject','type'))	
 				 	  ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=lbs.subjectid',array('SubjectName','BahasaIndonesia','SubCode','CreditHours'))
 				      ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = lbs.coursetypeid',array('SubjectType'=>'DefinitionDesc'))
 				 	  ->join(array('so'=>'tbl_subjectsoffered'),'so.IdSubject=lbs.subjectid',array())
 				 	  ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdSubject=lbs.subjectid',array('IdStudentRegSubjects')) 				 	
 				 	  ->where("bs.IdLandscape = ?",$idLandscape) 
 				 	  ->where("bs.semesterid < ?",$level) //actually level of semester				 	
 				 	  ->where("parentId = 0")
 				 	  ->order('lb.block')
 				 	  ->order('s.SubCode')
 				 	  ->group('lbs.subjectid'); 		 	  

		  if(isset($subject_code) && $subject_code!=''){
		  	$select->where("s.SubCode LIKE '%".$subject_code."%'");
		  }
		  
		  $larrResult = $db->fetchAll($select);
		 
		  foreach($larrResult as $index=>$row){
		  	
		  	
		  		if(isset($row["IdStudentRegSubjects"]) && $row["IdStudentRegSubjects"]!=''){
		  			//jika pernah registerd previous semester
		  			//so bapak xleh click hanya anak aje leh click utk register = add dalam row studentregsubject tbale
		  			$larrResult[$index]['register_status']="Closed";
	         		$larrResult[$index]['register']=1;	 
		  		}else{
		  			//jika x pernah cek dah register lom dalam selected semester		  		      	
		         	$subject_registered = $subjectRegDB->isRegister($student_id,$row["IdSubject"],$semester_id);	
		         	
		         	if(is_array($subject_registered)){
		         		$larrResult[$index]['register_status']="Registered";
		         		$larrResult[$index]['register']=1;	 
		         	} else{
		         		$larrResult[$index]['register_status']="Open";
		         		$larrResult[$index]['register']=2;
		         	}  
		  		}
		  		
		  		      
		  	
		  	     //get child if bapak (sekiranya bujang mmg xde anak)nak bg cepat xyah query skip terus
		  	     if($row["type"]==2){
			  		 $select_child = $db->select()
			 				 	  	    ->from(array("lbs"=>"tbl_landscapeblocksubject"),array('IdSubject'=>'subjectid','IdLandscapeSub'=>'blockid','parentId','IdLandscapeblocksubject'))	
			 				 	  	    ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=lbs.subjectid',array('SubjectName','BahasaIndonesia','SubCode','CreditHours'))
			 				 	  	    ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = lbs.coursetypeid',array('SubjectType'=>'DefinitionDesc'))
			 				 	  	    ->join(array('so'=>'tbl_subjectsoffered'),'so.IdSubject=lbs.subjectid',array())
			 				 	  	    ->where('parentId = ?',$row["IdLandscapeblocksubject"])
			 				 	  	    ->group('IdSubject');
			 				 	  	    
	 				$childResult = $db->fetchAll($select_child);
	 				
	 				
	 				foreach($childResult as $key=>$child_row){
	 					
	 					//cek already register or not	 					
			         	$subject_registered = $subjectRegDB->isRegister($student_id,$child_row["IdSubject"],$semester_id);	
			         	
			         	if(is_array($subject_registered)){				         	
			         		$childResult[$key]['child_register_status']="Registered";
			         		$childResult[$key]['child_register']=1;
			         	} else{
			         		$childResult[$key]['child_register_status']="Open";
			         		$childResult[$key]['child_register']=2;
			         	}   
	 					
	 				}//end foreach childresult
	 				
	 				$larrResult[$index]['child'] = $childResult; 
	 				
		  	   }else{
		  	   		$larrResult[$index]['child'] = ''; 
		  	   }//end if rowtype=2
		  }//end foreach larrresult
		  
		  return $larrResult;
    }
    
    
    public function getChildbyParentId($idLandscapeBlockSubject){
    	
    	 $db = Zend_Db_Table::getDefaultAdapter();
    	 
    	 $select_child = $db->select()
			 				->from(array("lbs"=>"tbl_landscapeblocksubject"),array('IdSubject'=>'subjectid','IdLandscapeSub'=>'blockid','parentId','IdLandscapeblocksubject'))
			 				->join(array('lb'=>'tbl_landscapeblock'),'lb.idblock=lbs.blockid',array('block_level'=>'block'))
			 				->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=lbs.subjectid',array('SubjectName','BahasaIndonesia','SubCode','CreditHours'))
			 				->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = lbs.coursetypeid',array('SubjectType'=>'DefinitionDesc'))
			 				->where('idLandscapeBlockSubject = ?',$idLandscapeBlockSubject)
			 				->orwhere('parentId = ?',$idLandscapeBlockSubject);
			 				
		 return $childResult = $db->fetchAll($select_child);
			 		
    }
    
    public function getCoursePrerequisite($idLandscape,$idLandscapeSub){
    
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select 	= $db->select()
    	->from(array("sp" =>"tbl_subjectprerequisites"))
    	->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=sp.IdRequiredSubject',array('BahasaIndonesia','SubCode'))
    	->where("sp.IdLandscape= ?",(int)$idLandscape)
    	->where("sp.IdLandscapeblocksubject= ?",(int)$idLandscapeSub);
    	// ->where("sp.IdSubject= ?",$idSubject);
    
    	//echo $select;
    	return $result = $db->fetchAll($select);
    
    }

}
?>