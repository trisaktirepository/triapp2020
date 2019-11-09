<?php
class GeneralSetup_Model_DbTable_Landscapesubject extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_landscapesubject';
    private $lobjDbAdpt;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

    public function fnaddLandscapesubject($formData) {
			if($formData['LandscapeType'] == 42 || $formData['LandscapeType'] == 44){
				$formData ['LandscapeCreditHoursgrid'] = 0;
			}


    		 $count = count($formData['IdSubjectgrid']);
    		 for($i = 0;$i<$count;$i++) {

    			$data = array('IdProgram' =>$formData['IdProgram'],
    					  	  'IdLandscape' => $formData ['IdLandscape'],
    					      'IdSubject' => $formData ['IdSubjectgrid'][$i],
    						  'IdSemester' => $formData ['IdSemestergrid'][$i],
						  	  'SubjectType' =>  $formData ['LandscapeSubjectTypegrid'][$i],
    			              'Active' =>  $formData ['Active'],
    					  	  'UpdDate'  =>	$formData ['UpdDate'],
    					  	  'UpdUser'	=> 	$formData ['UpdUser']);

    		if($formData['LandscapeType'] == 42 || $formData['LandscapeType'] == 44){
				$data ['CreditHours'] = 0;
			}else {
    				$data['CreditHours'] = $formData ['LandscapeCreditHoursgrid'][$i];
    			}

			 $this->insert($data);
    		 }
			$lobjdb = Zend_Db_Table::getDefaultAdapter();
			return $lobjdb->lastInsertId();
	}


    public function fnaddLandscapesubjectLevel($formData,$resultLandscape) {
					$count = count($formData['IdSubjectgrid']);
    		 for($i = 0;$i<$count;$i++) {
    			$data = array('IdProgram' =>$formData['IdProgram'],
    					  	  'IdLandscape' => $resultLandscape,
    			      		  'CreditHours' => $formData ['LandscapeCreditHoursgrid'][$i],
    					      'IdSubject' => $formData ['IdSubjectgrid'][$i],
    						  'IdSemester' => $formData ['IdSemestergrid'][$i],
    			              'Compulsory' => $formData ['Compulsory'][$i],
						  	  'SubjectType' =>  $formData ['LandscapeSubjectTypegrid'][$i],
    				          'IDProgramMajoring'=>$formData ['IdProgramMajoringgrid'][$i],
    			              'Active' =>  $formData ['Active'],
    					  	  'UpdDate'  =>	$formData ['UpdDate'],
    					  	  'UpdUser'	=> 	$formData ['UpdUser']);

			 $this->insert($data);
    		 }
			$lobjdb = Zend_Db_Table::getDefaultAdapter();
			return $lobjdb->lastInsertId();
	}


	/**
      * Function to get the landscape SUBJECTS
      * @author: Vipul
      */
	public function getlandscapesubjects($pid,$final_lid){
     	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_landscapesubject"))
		 				 ->where("a.IdProgram = ?",$pid)
		 				 ->where("a.IdLandscape = ?",$final_lid);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
     }
     public function getlandscapesubjectsPerCategory($final_lid,$category){
     	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
     	$lstrSelect = $lobjDbAdpt->select()
     	->from(array("a"=>"tbl_landscapesubject"))
     	//->where("a.IdProgram = ?",$pid)
     	->where("a.IdLandscape = ?",$final_lid)
     	->where("a.SubjectType=?",$category)
     	->group('a.IdSubject');
     	$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
     	return $larrResult;
     }

	/**
	 * Function to ADD Landscape subjectLevel
	 * @author: vipul
	 */
	 public function fninsertLandscapesubjectLevel($formData) {
			    			$data = array('IdProgram' => $formData['IdProgram'],
    					  	  'IdLandscape' => $formData['IdLandscape'],
    			      		  'CreditHours' => $formData ['CreditHours'],
    					      'IdSubject' => $formData ['IdSubject'],
    						  'IdSemester' => $formData ['IdSemester'],
    			              'Compulsory' => $formData ['Compulsory'],
						  	  'SubjectType' =>  $formData ['SubjectType'],
    				          'IDProgramMajoring'=>$formData ['IDProgramMajoring'],
    			              'Active' =>  $formData ['Active'],
    					  	  'UpdDate'  =>	$formData ['UpdDate'],
    					  	  'UpdUser'	=> 	$formData ['UpdUser']);

			$this->insert($data);
			$lobjdb = Zend_Db_Table::getDefaultAdapter();
			return $lobjdb->lastInsertId();
	}
	
	
	/* start yatie*/
	
	//add
	public function addData($data) {			    			
			$id=$this->insert($data);
			return $id;
	}
	
	public function getCommonCourse($program_id,$landscape_id,$compulsory=null){		
				
     	$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		 				 ->from(array("ls"=>"tbl_landscapesubject"))
		 				 ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.IdSubject',array('BahasaIndonesia','SubCode'))
		 				 ->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('DefinitionDesc'))
		 				 ->joinLeft(array("pr"=>"tbl_programrequirement"),'pr.IdProgramReq=ls.IdProgramReq',array())
		 				 ->where("ls.IdProgram = ?",$program_id)
		 				 ->where("ls.IdLandscape = ?",$landscape_id)		 				 
		 				 ->where("IDProgramMajoring = 0")
		 				 ->order("ls.IdSemester")
		 				 ->order("pr.IdProgramReq")
		 				 ->order("ls.IdLandscapeSub");
		 				 
		if($compulsory){ //Compulosry
			$select->where("pr.Compulsory = ?",$compulsory);	
		}
		
		$row = $db->fetchAll($select);
		return $row;
     }
          
	
     
     //getMajoringCourse
	public function getMajoringCourse($program_id,$landscape_id,$majoring_id,$compulsory=null){
		
     	$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		 				 ->from(array("ls"=>"tbl_landscapesubject"))
		 				 ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.IdSubject',array('BahasaIndonesia','SubCode'))
		 				 ->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('DefinitionDesc'))
		 				 ->joinLeft(array("pr"=>"tbl_programrequirement"),'pr.IdProgramReq=ls.IdProgramReq',array())
		 				 ->where("ls.IdProgram = ?",$program_id)
		 				 ->where("ls.IdLandscape = ?",$landscape_id)
		 				 ->where("IDProgramMajoring = ?",$majoring_id)
		 				 ->order("ls.IdSemester")
		 				 ->order("pr.IdProgramReq")
		 				 ->order("ls.IdLandscapeSub");
		 				 
		if($compulsory){ //Compulosry
			$select->where("pr.Compulsory = ?",$compulsory);	
		}
		
		$row = $db->fetchAll($select);
		return $row;
     }
     
     
     public function getCourseByProgramReq($program_id,$landscape_id,$subjectType){
     	
     	$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		 				 ->from(array("ls"=>"tbl_landscapesubject"))
		 				 ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.IdSubject',array('BahasaIndonesia','SubCode'))
		 				// ->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('DefinitionDesc'))
		 				 ->joinLeft(array("m"=>"tbl_programmajoring"),'m.IDProgramMajoring=ls.IDProgramMajoring',array('BahasaDescription','IdMajor'))
		 				 ->where("ls.IdProgram = ?",$program_id)
		 				 ->where("ls.IdLandscape = ?",$landscape_id)
		 				 ->where("ls.SubjectType = ?",$subjectType);
		$row = $db->fetchAll($select);
		return $row;
     }
     
	public function deleteData($id){		
	  $this->delete('IdLandscapeSub = ' . (int)$id);
	}
	
	public function getLandscapeCourse($idLandscape){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $lstrSelect = $db->select()
		 				 ->from(array("ls"=>$this->_name))		
		 				 ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.IdSubject ',array('s.IdSubject','BahasaIndonesia','SubCode','CreditHours'))
		 				 ->where("ls.IdLandscape = ?",$idLandscape)
		 				 ->group("ls.IdSubject")
		 				 ->order("s.BahasaIndonesia");
    	  $larrResult = $db->fetchAll($lstrSelect);
		  return $larrResult;
    }
    
    
	public function getPaginateLandscapeCourse($idLandscape,$keywords=null){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $lstrSelect = $db->select()
		 				 ->from(array("ls"=>$this->_name))		
		 				 ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.IdSubject ',array('SubjectName','BahasaIndonesia','SubCode','CreditHours'))
		 				 ->where("ls.IdLandscape = ?",$idLandscape)
		 				 ->group("ls.IdSubject")
		 				 ->order("s.BahasaIndonesia");

		  if($keywords){
			$lstrSelect->where("s.SubCode LIKE '%".$keywords."%'");
			$lstrSelect->whereor("s.BahasaIndonesia LIKE '%".$keywords."%'");
			$lstrSelect->whereor("s.SubjectName LIKE '%".$keywords."%'");
		  }	

			
		  $larrResult = $db->fetchAll($lstrSelect);
		  return $larrResult;
    }
    
    
	public function getLandscapeCourseList($idLandscape,$idSemester,$semester_level,$idProgramMajoring=0){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  //get Course Offer for particular semester tbl_subjectsoffered
    	  $select_course_offer = $db->select()
		 				 			->from(array("so"=>'tbl_subjectsoffered'),array('IdSubject'))	
		 				 			->where("IdSemester = ?",$idSemester);
    	  
    	  $lstrSelect = $db->select()
		 				 ->from(array("ls"=>$this->_name))		
		 				 ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.IdSubject ',array('BahasaIndonesia','SubCode','CreditHours'))
		 				 ->where("ls.IdLandscape = ?",$idLandscape)
		 				 ->where("ls.IdSemester = ?",$semester_level)
		 				 //->where("ls.IdSubject IN (?)",$select_course_offer)
		 				 ->group("s.IdSubject")
		 				 ->order("s.BahasaIndonesia");
		 				 
		  if(isset($idProgramMajoring)){
		  		$lstrSelect->where("ls.IDProgramMajoring = ?",$idProgramMajoring);
		  }
		 //echo $lstrSelect;
		  $larrResult = $db->fetchAll($lstrSelect);
		  return $larrResult;
    }
    
	public function getPrerequisiteCourseList($idLandscape,$idSubject,$idLandscapeSub,$idProgramMajoring=null){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	   $select 	= $db->select()
							 ->from(array("sp" =>"tbl_subjectprerequisites"),array('IdRequiredSubject'))
		            		 ->where("sp.IdLandscape= ?",$idLandscape)
		            		 ->where("sp.IdLandscapeSub= ?",$idLandscapeSub);		            		
    	   
    	    	    	  
    	  $lstrSelect = $db->select()
		 				 ->from(array("ls"=>$this->_name))		
		 				 ->joinLeft(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.IdSubject ',array('BahasaIndonesia','SubCode','CreditHours'))
		 				 ->where("ls.IdLandscape = ?",$idLandscape)		 				
		 				 ->where("ls.IdSubject != ?",$idSubject)	
		 				 ->where("ls.IdSubject NOT IN (?)",$select)		 				
		 				 ->group("ls.IdSubject")
		 				 ->order("s.SubCode");

		  if($idProgramMajoring!=0 && $idProgramMajoring!=''){
		  	 $lstrSelect->where("(ls.IDProgramMajoring = ?",$idProgramMajoring);
		 	 $lstrSelect->orwhere("ls.IDProgramMajoring = '0')");
		  }else{
		  	 $lstrSelect->where("ls.IDProgramMajoring = '0'");
		  }
		  
		  $larrResult = $db->fetchAll($lstrSelect);
		  return $larrResult;
    }
    
	public function getProgramMajoring($idLandscapeSub){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();    	 
    	  
    	  $select = $db->select()
		 			   ->from(array("ls"=>'tbl_landscapesubject'))
		 			   ->joinLeft(array('pm'=>'tbl_programmajoring'),'pm.IDProgramMajoring = ls.IDProgramMajoring',array('BahasaDescription'))
		 			   ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject = ls.IdSubject')
		 			   ->where("ls.IdLandscapeSub = ?",$idLandscapeSub);
		             	             			
    	  $row = $db->fetchRow($select);
    	       	 
		  return $row;
    }
    
	public function getCourseInfo($idLandscapeSubject){
    	
    	 $db = Zend_Db_Table::getDefaultAdapter();
    	 
    	  $select = $db->select()
		 				 ->from(array("ls"=>$this->_name))		
		 				 ->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('DefinitionDesc'))
		 				 ->where("ls.IdLandscapeSub = ?",$idLandscapeSubject);
		 			 
		  $row = $db->fetchRow($select);
		  return $row;
    }
    
	public function getMultiLandscapeCourse($Landscapes,$search=""){
		    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	
    	  $lstrSelect = $db->select()
    	  				->distinct()
		 				 ->from(array("ls"=>$this->_name),array())		
		 				 ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=ls.IdSubject ',array('IdSubject','SubjectName'=>'BahasaIndonesia','SubCode','CreditHours','key'=>'IdSubject','name'=>'BahasaIndonesia'))
		 				 ->join(array('c'=>'tbl_collegemaster'),'c.IdCollege=s.IdFaculty',array('facultyName'=>'ArabicName'))
		 				 ->group("ls.IdSubject")
		 				 ->order("s.SubCode");
		 				 
		 				 
		 foreach ($Landscapes as $landscape) {
		 	$lstrSelect->orwhere("ls.IdLandscape = ?",$landscape);
		 }		 
		 		 
		 if(isset($search["subject_code"]) && $search["subject_code"]!=''){			 
			 	$lstrSelect->where("s.SubCode LIKE '%".$search["subject_code"]."%'");
		 }
	 	 
	     if(isset($search["subject_name"]) && $search["subject_name"]!=''){			 
			 	$lstrSelect->where("s.SubjectName LIKE '%".$search["subject_name"]."%'");
			 	$lstrSelect->orwhere("s.BahasaIndonesia LIKE '%".$search["subject_name"]."%'");
		 }
			 
 		/* if(isset($search["IdSemester"])&& $search["IdSemester"]!=""){			 
		 	$lstrSelect->where("ls.IdSemester = ?",$search["IdSemester"]);
		 }*/
		
		
		 $rows = $db->fetchAll($lstrSelect);
		return $rows;
    }
    
 
	public function getInfo($idLandscape,$idSubject){
    	
    	 $db = Zend_Db_Table::getDefaultAdapter();
    	 
    	  $select = $db->select()
		 				 ->from(array("ls"=>$this->_name))			 				
		 				 ->where("ls.IdLandscape = ?",$idLandscape)
		 				 ->where("ls.IdSubject = ?",$idSubject);
		 			 
		  $row = $db->fetchRow($select);
		  return $row;
    }
	
   
}
?>