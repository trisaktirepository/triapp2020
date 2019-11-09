<?php
class App_Model_General_DbTable_Subjectmaster extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_subjectmaster';

	public function fnGetSubjectMasterList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("sm"=>"tbl_subjectmaster"),array('sm.*',"CONCAT_WS(' - ',IFNULL(sm.SubjectName,''),IFNULL(sm.SubCode,'')) AS SubjectName","CONCAT_WS(' - ',IFNULL(sm.BahasaIndonesia,''),IFNULL(sm.SubCode,'')) AS BahasaIndonesia"))
		 				 ->where("sm.Active = 1")
		 				 ->order("sm.BahasaIndonesia");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function fngetSubjectDetails() { //Function to get the user details
       $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
       
       $locale =  Zend_Registry::get('Zend_Locale');
		
		if($locale!="en_US" && $locale!="en_GB"){
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("sm"=>"tbl_subjectmaster"),array("sm.*"))
       								->joinleft(array("dm"=>"tbl_departmentmaster"),'sm.IdDepartment = dm.IdDepartment',array("dm.*"))
       								->joinleft(array("cm"=>"tbl_collegemaster"),'dm.IdCollege = cm.IdCollege',array("cm.ArabicName as BranchName","cm.CollegeType"))
       								->joinleft(array("cm2"=>"tbl_collegemaster"),'cm.Idhead =cm2.IdCollege',array("cm2.CollegeName"))
       								->joinleft(array("ct"=>"tbl_coursetype"),'ct.IdCourseType =sm.CourseType',array("ct.Bahasaindonesia AS CourseName"))
       								->where('sm.Active = 1')

       								->group("sm.IdSubject")
       								->order("sm.SubjectName");
		}else{
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("sm"=>"tbl_subjectmaster"),array("sm.*"))
       								->joinleft(array("dm"=>"tbl_departmentmaster"),'sm.IdDepartment = dm.IdDepartment',array("dm.*"))
       								->joinleft(array("cm"=>"tbl_collegemaster"),'dm.IdCollege = cm.IdCollege',array("cm.CollegeName as BranchName","cm.CollegeType"))
       								->joinleft(array("cm2"=>"tbl_collegemaster"),'cm.Idhead =cm2.IdCollege',array("cm2.CollegeName"))
       								->joinleft(array("ct"=>"tbl_coursetype"),'ct.IdCourseType =sm.CourseType',array("ct.CourseType AS CourseName"))
       								->where('sm.Active = 1')

       								->group("sm.IdSubject")
       								->order("sm.SubjectName");	
		}
		
        //echo $lstrSelect;

		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
     }
	public function fngetUserSubjectDetails($IdCollege) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();

		$locale =  Zend_Registry::get('Zend_Locale');
		
		if($locale!="en_US" && $locale!="en_GB"){
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("sm"=>"tbl_subjectmaster"),array("sm.*"))
       								->joinleft(array("dm"=>"tbl_departmentmaster"),'sm.IdDepartment = dm.IdDepartment',array("dm.*"))
       								->joinleft(array("cm"=>"tbl_collegemaster"),'dm.IdCollege = cm.IdCollege',array("cm.ArabicName as BranchName","cm.CollegeType"))
       								->joinleft(array("cm2"=>"tbl_collegemaster"),'cm.Idhead =cm2.IdCollege',array("cm2.CollegeName"))
       								->joinleft(array("ct"=>"tbl_coursetype"),'ct.IdCourseType =sm.CourseType',array("ct.Bahasaindonesia AS CourseName"))
       								 ->where('dm.IdCollege = ?',$IdCollege)
       								->where('sm.Active = 1')
       								//->where('cm2.CollegeType = 0')
       								->group("sm.IdSubject")
       								->order("sm.SubjectName");
       								//->where("dm.DepartmentType  = 0");
		}else{
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("sm"=>"tbl_subjectmaster"),array("sm.*"))
       								->joinleft(array("dm"=>"tbl_departmentmaster"),'sm.IdDepartment = dm.IdDepartment',array("dm.*"))
       								->joinleft(array("cm"=>"tbl_collegemaster"),'dm.IdCollege = cm.IdCollege',array("cm.CollegeName as BranchName","cm.CollegeType"))
       								->joinleft(array("cm2"=>"tbl_collegemaster"),'cm.Idhead =cm2.IdCollege',array("cm2.CollegeName"))
       								->joinleft(array("ct"=>"tbl_coursetype"),'ct.IdCourseType =sm.CourseType',array("ct.CourseType AS CourseName"))
       								 ->where('dm.IdCollege = ?',$IdCollege)
       								->where('sm.Active = 1')
       								//->where('cm2.CollegeType = 0')
       								->group("sm.IdSubject")
       								->order("sm.SubjectName");
       								//->where("dm.DepartmentType  = 0");
		}
		
       					//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
     }
	public function fnSearchSubject($post = array()) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
       
       	$locale =  Zend_Registry::get('Zend_Locale');
		
		if($locale!="en_US" && $locale!="en_GB"){
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("sm"=>"tbl_subjectmaster"),array("sm.*"))
       								->joinleft(array("dm"=>"tbl_program"),'sm.IdDepartment = dm.IdProgram',array("dm.*"))
       								->joinleft(array("cm"=>"tbl_collegemaster"),'dm.IdCollege = cm.IdCollege',array("cm.ArabicName as BranchName","cm.CollegeType"))
       								->joinleft(array("cm2"=>"tbl_collegemaster"),'cm.Idhead =cm2.IdCollege',array("cm2.CollegeName"))
       								->joinleft(array("ct"=>"tbl_coursetype"),'ct.IdCourseType =sm.CourseType',array("ct.Bahasaindonesia AS CourseName"));
		}else{
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("sm"=>"tbl_subjectmaster"),array("sm.*"))
       								->joinleft(array("dm"=>"tbl_program"),'sm.IdDepartment = dm.IdProgram',array("dm.*"))
       								->joinleft(array("cm"=>"tbl_collegemaster"),'dm.IdCollege = cm.IdCollege',array("cm.CollegeName as BranchName","cm.CollegeType"))
       								->joinleft(array("cm2"=>"tbl_collegemaster"),'cm.Idhead =cm2.IdCollege',array("cm2.CollegeName"))
       								->joinleft(array("ct"=>"tbl_coursetype"),'ct.IdCourseType =sm.CourseType',array("ct.CourseType AS CourseName"));
		}
	   	

		if(isset($post['field5']) && !empty($post['field5']) ){
				$lstrSelect = $lstrSelect->where("dm.IdCollege = ?",$post['field5']);

		}
		if(isset($post['field8']) && !empty($post['field8']) ){
				$lstrSelect = $lstrSelect->where("cm.Idhead = ?",$post['field8']);

		}
		if(isset($post['field20']) && !empty($post['field20']) ){
				$lstrSelect = $lstrSelect->where("sm.IdDepartment = ?",$post['field20']);

		}
		
		if(isset($post['field28']) && !empty($post['field28']) ){
				$lstrSelect = $lstrSelect->where("sm.subjectMainDefaultLanguage LIKE ?", '%'.$post['field28'].'%');

		}
       							//	->where("cm.IdCollege= ?",$post['field5'])
       								//->where("cm.Idhead= ?",$post['field5'])
       								//->where('sm.IdDepartment like "%" ? "%"',$post['field20'])
       	 if (isset($post['field4']) && !empty($post['field4']))	$lstrSelect		->where('sm.BahasaIndonesia like "%" ? "%"',$post['field4']);
       	 if (isset($post['field2']) && !empty($post['field2']))    	$lstrSelect	->where('sm.SubjectName like "%" ? "%"',$post['field2']);
       	 if (isset($post['field3']) && !empty($post['field3'])) $lstrSelect->where('sm.SubCode like "%" ? "%"',$post['field3']);
       		$lstrSelect	->where("sm.Active = ".$post["field7"])
       				->group("sm.IdSubject")
       			   ->order("sm.SubjectName");
       					//echo $lstrSelect;die();
		//echo $lstrSelect;exit;
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	 }

	public function fnSearchUserSubject($post = array(),$IdCollege) { //Function to get the user details
       $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
      
       $locale =  Zend_Registry::get('Zend_Locale');
		
		if($locale!="en_US" && $locale!="en_GB"){
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("sm"=>"tbl_subjectmaster"),array("sm.*"))
       								->joinleft(array("dm"=>"tbl_program"),'sm.IdDepartment = dm.IdProgram',array("dm.*"))
       								->joinleft(array("cm"=>"tbl_collegemaster"),'dm.IdCollege = cm.IdCollege',array("cm.ArabicName as BranchName","cm.CollegeType"))
       								->joinleft(array("cm2"=>"tbl_collegemaster"),'cm.Idhead =cm2.IdCollege',array("cm2.CollegeName"))
       								->joinleft(array("ct"=>"tbl_coursetype"),'ct.IdCourseType =sm.CourseType',array("ct.Bahasaindonesia AS CourseName"));
		}else{
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("sm"=>"tbl_subjectmaster"),array("sm.*"))
       								->joinleft(array("dm"=>"tbl_program"),'sm.IdDepartment = dm.IdProgram',array("dm.*"))
       								->joinleft(array("cm"=>"tbl_collegemaster"),'dm.IdCollege = cm.IdCollege',array("cm.CollegeName as BranchName","cm.CollegeType"))
       								->joinleft(array("cm2"=>"tbl_collegemaster"),'cm.Idhead =cm2.IdCollege',array("cm2.CollegeName"))
       								->joinleft(array("ct"=>"tbl_coursetype"),'ct.IdCourseType =sm.CourseType',array("ct.CourseType AS CourseName"));
		}
	   

		if(isset($post['field5']) && !empty($post['field5']) ){
				$lstrSelect = $lstrSelect->where("dm.IdCollege = ?",$post['field5']);

		}
		if(isset($post['field8']) && !empty($post['field8']) ){
				$lstrSelect = $lstrSelect->where("cm.Idhead = ?",$post['field8']);

		}
		if(isset($post['field20']) && !empty($post['field20']) ){
				$lstrSelect = $lstrSelect->where("sm.IdDepartment = ?",$post['field20']);

		}
		
		
		if(isset($post['field28']) && !empty($post['field28']) ){
				$lstrSelect = $lstrSelect->where("sm.subjectMainDefaultLanguage LIKE ?", '%'.$post['field28'].'%');

		}
		
       							    //->where("cm.IdCollege= ?",$post['field5'])
       								//->where("cm.Idhead= ?",$post['field5'])
       								//->where('sm.IdDepartment like "%" ? "%"',$post['field20'])
        if (isset($post['field4']) && !empty($post['field4']))	$lstrSelect->where('sm.BahasaIndonesia like "%" ? "%"',$post['field4']);
       	if (isset($post['field2']) && !empty($post['field2']))  $lstrSelect->where('sm.SubjectName like "%" ? "%"',$post['field2']);
       	if (isset($post['field3']) && !empty($post['field3'])) $lstrSelect->where('sm.SubCode like "%" ? "%"',$post['field3']);
       		$lstrSelect	->where("sm.Active = ".$post["field7"])
       				->group("sm.IdSubject")
       			   ->order("sm.SubjectName");
       					//echo $lstrSelect;die();
       //	echo $lstrSelect;exit;
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		
		return $larrResult;
	 }
    /* public function fnSearchSubject($post = array()) { //Function for searching the user details
    	$db = Zend_Db_Table::getDefaultAdapter();
		$field7 = "a.Active = ".$post["field7"];
		$select = $this->select()
			   ->setIntegrityCheck(false)
			   ->join(array('a' => 'tbl_subjectmaster'),array('IdSubject'))
			   ->join(array("dm"=>"tbl_departmentmaster"),'dm.IdDepartment = a.IdDepartment')
			   ->join(array("cm"=>"tbl_collegemaster"),'cm.IdCollege = dm.IdCollege')
			   //->join(array("um"=>"tbl_universitymaster"),'um.IdUniversity = cm.AffiliatedTo')
			   ->where('cm.AffiliatedTo like "%" ? "%"',$post['field1'])
			   ->where('cm.IdCollege like "%" ? "%"',$post['field5'])
			   ->where('a.SubjectName like "%" ? "%"',$post['field2'])
			   ->where('a.SubCode like  "%" ? "%"',$post['field3'])
			   ->where($field7);
		$result = $this->fetchAll($select);
		return $result->toArray();
	}*/


/*public function fngetSubjectDetails() { //Function to get the user details
       $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
       								->from(array("sm"=>"tbl_subjectmaster"),array("sm.*"))
       								->join(array("dm"=>"tbl_departmentmaster"),'dm.IdDepartment = sm.IdDepartment',array("dm.*"))
       								->join(array("cm"=>"tbl_collegemaster"),'cm.IdCollege = dm.IdCollege',array("cm.CollegeName as CollegeName"))
       								->join(array("cm1"=>"tbl_collegemaster"),'cm1.IdCollege = dm.IdCollege',array("cm1.CollegeName as BranchName"))
       								->where("cm.CollegeType = 0")
       								->where("cm1.CollegeType = 1");
       								echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
			return $larrResult;
     }*/

	public function fnGetUniversityMasterList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("um"=>"tbl_universitymaster"),array("key"=>"um.IdUniversity","value"=>"um.Univ_Name"))
		 				 ->where("um.Active = 1")
		 				 ->order("um.Univ_Name");
			 				 
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnaddSubject($larrformData,$idUniversity,$CodeType)  { //Function for adding the user details to the table
			unset($larrformData['IdStaff']);
	   		unset($larrformData['FromDate']);
	   		unset($larrformData['ToDate']);

	   	 	unset($larrformData['Idcomponents']);
	   	 	unset($larrformData['CreditHour']);
	   		unset($larrformData['Idcomponentsgrid']);
			unset($larrformData['CreditHourgrid']);
//			echo "<pre>";
//			print_r();

	   	$idSubject =	$this->insert($larrformData);
		//if($CodeType == 1){
		//	$SemCode = $this->fnGenerateCode($idUniversity,$idSubject,$larrformData['ShortName'],$larrformData['IdDepartment']);
		//	$formData1['SubCode'] = $SemCode;
		//	$this->fnupdateSubject($idSubject,$formData1);
		//}
		return $idSubject;
	}
	function fnGenerateCode($idUniversity,$idSubject,$Shortname,$departmentId){
		    $page	=  "Subject";
			$db 	= 	Zend_Db_Table::getDefaultAdapter();
			$select =   $db->select()
					->  from('tbl_config')
					->	where('idUniversity  = ?',$idUniversity);
			$result = 	$db->fetchRow($select);
			$sepr	=	$result[$page.'Separator'];
			$str	=	$page."CodeField";
			$select =  $db->select()
						 		 -> from(array('a'=>'tbl_departmentmaster'))
						 		 -> join(array('b'=>'tbl_collegemaster'),'b.IdCollege=a.IdCollege','b.ShortName AS CShortName')
						 		 ->	where('a.IdDepartment  = ?',$departmentId);
			$resultCollage = $db->fetchRow($select);

			for($i=1;$i<=4;$i++){
				$check = $result[$str.$i];
				switch ($check){
					case 'Uniqueid':
					  $code		= $idSubject;
					  break;
					case 'ShortName':
					  $code	    = $Shortname;
					  break;
					case 'CollegeShortName':
					  $code		   = $resultCollage['CShortName'];
					  break;
					case 'DepartmentShortName':
					  $code		   = $resultCollage['ShortName'];
					  break;
					default:
					  break;
				}
				if($i == 1) $accCode 	 =  $code;
				else 		$accCode	.=	$sepr.$code;
			}
		return $accCode;
	}
	public function fnGetDepartmentList($idFaculty=""){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$locale =  Zend_Registry::get('Zend_Locale');
		
		if($locale!="en_US" && $locale!="en_GB"){
			$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_departmentmaster"),array("key"=>"a.IdDepartment","value"=>"a.ArabicName"))
		 				 ->where("a.Active = 1")
		 				 ->order("a.DepartmentName");
		}else{
			$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_departmentmaster"),array("key"=>"a.IdDepartment","value"=>"a.DepartmentName"))
		 				 ->where("a.Active = 1")
		 				 ->order("a.DepartmentName");
		}
		
        if($idFaculty!="") {
        	$lstrSelect->where("a.IdCollege = ?",$idFaculty);
        }
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fnGetUserDepartmentList($IdCollege){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_departmentmaster"),array("key"=>"a.IdDepartment","value"=>"a.DepartmentName"))
		 				 ->where('a.IdCollege = ?',$IdCollege)
		 				 ->where("a.Active = 1")
		 				 ->order("a.DepartmentName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fnGetCourseTypeList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_coursetype"),array("key"=>"a.IdCourseType","value"=>"a.CourseType"))
		 				 ->where("a.Active = 1")
		 				 ->order("a.CourseType");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetcollegeDepartmentList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_departmentmaster"),array('a.IdDepartment'))
		 				 ->where("a.Active = 1")
		 				 ->order("a.DepartmentName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetCollegeList($idCollege=0){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$locale =  Zend_Registry::get('Zend_Locale');
		
		if($locale!="en_US" && $locale!="en_GB"){
			$lstrSelect = $lobjDbAdpt->select()
			 				 ->from(array("a"=>"tbl_collegemaster"),array("key"=>"a.IdCollege","value"=>"a.ArabicName"))
			 				 ->where("a.CollegeType = 0")
			 				 ->where("a.Active = 1")
			 				 ->order("a.CollegeName");
		}else{
			$lstrSelect = $lobjDbAdpt->select()
			 				 ->from(array("a"=>"tbl_collegemaster"),array("key"=>"a.IdCollege","value"=>"a.CollegeName"))
			 				 ->where("a.CollegeType = 0")
			 				 ->where("a.Active = 1")
			 				 ->order("a.CollegeName");
		}
		
		if($idCollege!=0){
				$lstrSelect->where('a.IdCollege =?',$idCollege);
		}
		 				 
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

		public function fnGetSubjectList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_subjectmaster"),array("key"=>"a.IdSubject","value"=>"CONCAT_WS(' - ',IFNULL(a.BahasaIndonesia,''),IFNULL(a.SubCode,''))"))
						 ->where("a.Active = 1")
		 				 ->order("a.BahasaIndonesia");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetBranchList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$locale =  Zend_Registry::get('Zend_Locale');
		
		if($locale!="en_US" && $locale!="en_GB"){
			$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_collegemaster"),array("key"=>"a.IdCollege","value"=>"a.ArabicName"))
		 				 ->where("a.CollegeType = 1")
		 				 ->where("a.Active = 1")
		 				 ->order("a.CollegeName");
		}else{
			$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_collegemaster"),array("key"=>"a.IdCollege","value"=>"a.CollegeName"))
		 				 ->where("a.CollegeType = 1")
		 				 ->where("a.Active = 1")
		 				 ->order("a.CollegeName");	
		}
		
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

    public function fnviewSubject($lintisubject) { //Function for the view user
    	//echo $lintidepartment;die();
	 	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
						->from(array("a" => "tbl_subjectmaster"),array("a.*"))
						->join(array('b' => 'tbl_coursetype'),'a.CourseType = b.IdCourseType',array('b.CourseType AS CCourseType','b.MandatoryAmount','b.MandatoryCreditHrs'))
		            	->where("a.IdSubject= ?",$lintisubject);
		return $result = $lobjDbAdpt->fetchRow($select);
    }

    public function fnupdateSubject($lintIdDepartment,$larrformData) { //Function for updating the user
    	unset($larrformData['IdStaff']);
	   	unset($larrformData['FromDate']);
	   	unset($larrformData['ToDate']);
	   	unset($larrformData['Idcomponents']);
	   	 	unset($larrformData['CreditHour']);
	   		unset($larrformData['Idcomponentsgrid']);
			unset($larrformData['CreditHourgrid']);

		$where = 'IdSubject = '.$lintIdDepartment;
		$this->update($larrformData,$where);
    }


	public function fninsertChiefofSubjectList($larrformData,$IdSubject) {  // function to insert po details
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_subjectcoordinatorlist";
		$larrcordinatesubjects = array('IdSubject'=>$IdSubject,
									'IdStaff'=>$larrformData['IdStaff'],
									'FromDate'=>$larrformData['FromDate'],
									'ToDate'=>$larrformData['ToDate'],
									'Active'=>$larrformData['Active'],
									'UpdDate'=>$larrformData['UpdDate'],
									'UpdUser'=>$larrformData['UpdUser']
		);
		$db->insert($table,$larrcordinatesubjects);
	}


	public function fngetSubjectCoordinator($lintisubject) //fngetChiefofProgramList($lintIdProgram)
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrselectsql1 = $this->lobjDbAdpt->select()
									->from(array('cop'=>'tbl_subjectcoordinatorlist'),array('IdSubjectCoordinatorList'=>'MAX(cop.IdSubjectCoordinatorList)'))
									->where("cop.IdSubject = $lintisubject");
									//->where("reglst.IdStaff = ".$larrformdata['IdStaff']);
		$larrresultset1 = $this->lobjDbAdpt->fetchRow($lstrselectsql1);

		if(!empty($larrresultset1['IdSubjectCoordinatorList']))
		{
			$lstrselectsql = $this->lobjDbAdpt->select()
									->from(array('cop'=>'tbl_subjectcoordinatorlist'),array('IdStaff','FromDate','ToDate'))
									->where("cop.IdSubjectCoordinatorList = ".$larrresultset1['IdSubjectCoordinatorList']);
									//->where("reglst.IdStaff = ".$larrformdata['IdStaff']);
			$larrresultset = $this->lobjDbAdpt->fetchRow($lstrselectsql);

			return $larrresultset;
		}
		else
			return 0;

	}


	public function fnupdateChiefofSubjectList($larrformData,$lintisubject) //fnupdateChiefofProgramList($larrformdata,$lintIdProgram)
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrselectsql = $this->lobjDbAdpt->select()
						->from(array('cop'=>'tbl_subjectcoordinatorlist'),array('IdSubjectCoordinatorList'=>'MAX(cop.IdSubjectCoordinatorList)'))
						->where("cop.IdSubject = $lintisubject");
						//->where("reglst.IdStaff = ".$larrformdata['IdStaff']);
						
		$larrresultset = $this->lobjDbAdpt->fetchRow($lstrselectsql);
		
		
		//asd($lintisubject,true);
		//asd($larrresultset);
        //$larrreglst =  array();
		//if(!empty($larrresultset['IdChiefOfProgramList']))
                
        //if(!empty($larrresultset['IdSubjectCoordinatorList']))
		//{
	    	//$larrreglst['ToDate'] = $larrformdata['FromDate'];
            //$larrreglst['IdStaff'] = $larrformData['IdStaff'];
	    	//$lstrwhere = " IdSubject = ".$lintisubject;
                //asd($larrreglst,false);
                //asd($lstrwhere);
			//$this->lobjDbAdpt->update('tbl_subjectcoordinatorlist',$larrreglst,$lstrwhere);
		//}
		
		if($larrresultset['IdSubjectCoordinatorList']!=null){
			$larrreglst =  array();
			$larrreglst['ToDate'] = $larrformdata['FromDate'];
            $larrreglst['IdStaff'] = $larrformData['IdStaff'];
            
            $this->lobjDbAdpt->update('tbl_subjectcoordinatorlist',$larrreglst,$lstrwhere);
		}else{
			$this->fninsertChiefofSubjectList($larrformData, $lintisubject);
		}
	}

	public function fngetMandatoryfields($lintIdCourseType) { //Function for the view user
    	//echo $lintidepartment;die();
	 	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
						->from(array("a" => "tbl_coursetype"),array("a.MandatoryCreditHrs","a.MandatoryAmount"))
		            	->where("a.IdCourseType= ?",$lintIdCourseType);
		      // echo $select;die();
		return $result = $lobjDbAdpt->fetchRow($select);
    }

    public function fninsertSubjectComponentcredithours($larrformData,$IdSubject){


   		unset($larrformData['SubjectName']);
   		unset($larrformData['ShortName']);
   		unset($larrformData['UpdDate']);
   		unset($larrformData['UpdUser']);
   		unset($larrformData['BahasaIndonesia']);
   		unset($larrformData['SubCode']);
   		unset($larrformData['IdDepartment']);
   		unset($larrformData['CourseType']);
   		unset($larrformData['CreditHours']);
   		unset($larrformData['AmtPerHour']);
   		unset($larrformData['Active']);
   		unset($larrformData['IdStaff']);
   		unset($larrformData['FromDate']);
   		unset($larrformData['ToDate']);
   		unset($larrformData['ClassTimeTable']);
   		unset($larrformData['ExamTimeTable']);
   		unset($larrformData['ReligiousSubject']);
   		unset($larrformData['IdReligion']);
   		unset($larrformData['Idcomponents']);
   		unset($larrformData['CreditHour']);

    	$db = Zend_Db_Table::getDefaultAdapter();
		$tablecomp = "tbl_subcredithoursdistrbtn";
		$count=count($larrformData['Idcomponentsgrid']);

		for($i=0;$i<$count;$i++){
		$larrcomponents = array('IdSubject'=>$IdSubject,
								'CreditHour'=>$larrformData['CreditHourgrid'][$i],
								'Idcomponents'=>$larrformData['Idcomponentsgrid'][$i],
							);
		$db->insert($tablecomp,$larrcomponents);
		}

    }

    public function fngetSubjectComponentlist($lintSubjectId){
    	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
							->from(array('sch'=>'tbl_subcredithoursdistrbtn'),array("sch.*"))
							->join(array('dfm' => 'tbl_definationms'),'sch.Idcomponents = dfm.idDefinition',array('dfm.idDefinition','dfm.DefinitionCode'))
							->where("sch.IdSubject= ?",$lintSubjectId);
		$larrResult = $lobjDbAdpt->fetchAll($select);
		return $larrResult;
    }

    public function fndeleteSubjectComponentcredithours($lintisubject){

			$db = Zend_Db_Table::getDefaultAdapter();
			$tablecompdel = "tbl_subcredithoursdistrbtn";
	    	$where = $db->quoteInto('IdSubject = ?', $lintisubject);
			$db->delete($tablecompdel, $where);

    }

	public function fngetsubjname($subjName) {

    	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
						->from(array("c"=>"tbl_subjectmaster"),array("c.*"))
		            	->where("c.SubjectName= ?",$subjName);
		return $result = $lobjDbAdpt->fetchRow($select);
    }


	public function fngetsubjcode($subjcode) {

    	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
						->from(array("c"=>"tbl_subjectmaster"),array("c.*"))
		            	->where("c.SubCode = ?",$subjcode);
		return $result = $lobjDbAdpt->fetchAll($select);
    }
    public function fngetsubjcodeRow($subjcode) {
    
    	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
    	$select 	= $lobjDbAdpt->select()
    	->from(array("c"=>"tbl_subjectmaster"),array("c.*"))
    	->where("c.SubCode = ?",$subjcode);
    	//echo $subjcode.'0';
    	return $result = $lobjDbAdpt->fetchRow($select);
    }

    public function fnGetCoursetList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_subjectmaster"),array("key"=>"a.IdSubject","value"=>"CONCAT_WS(' - ',IFNULL(a.SubjectName,''),IFNULL(a.SubCode,''))"))
						 ->where("a.Active = 1");

		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


        public function getUserName($id){
            $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_user"),array("a.loginName"))
						 ->where("a.iduser =?",$id);

		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
        }
        
         /**
	 *Function fetch all courses(and count) from subject master table
	 *@author Vipul
	 */
	 public function totalSubjects() {
                $db = Zend_Db_Table::getDefaultAdapter();
                $sql = $db->select()->from(array('a' => 'tbl_subjectmaster',array('a.IdSubject')));
                $result = $db->fetchAll($sql);
                $totalVal = count($result);
                return $totalVal;
	 }
         
         
         public function fnGetLandscapeSubjectList($id){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_landscapesubject"),array(""))
                                                 ->joinLeft(array("dm"=>"tbl_subjectmaster"),'a.IdSubject = dm.IdSubject',array("key"=>"dm.IdSubject","value"=>"CONCAT_WS(' - ',IFNULL(dm.SubjectName,''),IFNULL(dm.SubCode,''))"))
						  ->where("a.IdLandscape =?",$id)
		 				 ->order("dm.SubjectName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


        public function getSemesterid($semcode){
            $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
            $lstrSelect = $lobjDbAdpt->select()
		 	->from(array("a"=>"tbl_semestermaster"),array("a.IdSemesterMaster"))
			->where("a.SemesterMainCode =?",$semcode);
            $larrResult = $lobjDbAdpt->fetchRow($lstrSelect);

            if(empty($larrResult)){
                $Select = $lobjDbAdpt->select()
		 	->from(array("a"=>"tbl_semester"),array("a.IdSemester"))
			->where("a.SemesterCode =?",$semcode);
                $larrResult = $lobjDbAdpt->fetchRow($Select);
            }
            return $larrResult;
        }
         
public function getSubjectList($program_id,$landscape_id){
        	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();		
				
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_subjectmaster"),array("key"=>"a.IdSubject","value"=>"CONCAT_WS(' - ',IFNULL(a.BahasaIndonesia,''),IFNULL(a.SubCode,''))",'CreditHours'))
						 ->where("a.Active = 1")						
		 				 ->order("a.SubjectName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
         
        }
        
 		public function getCourse(){
        	
		$db = Zend_Db_Table::getDefaultAdapter();		
				
		$lstrSelect = $db->select()
		 				 ->from(array("a"=>"tbl_subjectmaster"))
						 ->where("a.Active = 1")						
		 				 ->order("a.SubjectName")
		 				 ->limit(10,0);
		$result = $db->fetchAll($lstrSelect);
		return $result;
         
        }
        
	public function getData($id=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select =$db->select()
	 				 ->from(array('s'=>$this->_name))	 				 
	 				 ->where("s.IdSubject = ?",$id);
		$row = $db->fetchRow($select);
		return $row;
	}
	
	public function getMySubjectList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_subjectmaster"))
						 ->where("a.Active = 1")		 				
		 				 ->order("a.SubCode");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
        
	}
	
public function getSubjectByCollegeId($formdata=null){
		
		$session = new Zend_Session_Namespace('sis');
				
		  
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select =$db->select()
	 				 ->from(array('s'=>$this->_name))
	 				 ->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=s.IdFaculty',array('facultyName'=>'ArabicName'))
	 				 ->order("c.CollegeCode")
	 				 ->order("s.SubCode");
	 	
	 				 
		if($session->IdRole == 311 || $session->IdRole == 298){ 			
			$select->where("s.IdFaculty =?",$session->idCollege);
		}else if($formdata["IdCollege"] != null){		
		 	$select->where("s.IdFaculty  = ?",$formdata["IdCollege"]);		 
		}
		
		if($formdata["SubjectCode"] != null){
			$select->where("s.SubCode  LIKE '%".$formdata["SubjectCode"]."%'");	
		}

		//echo $select;
		$row = $db->fetchAll($select);
		return $row;
	}
	
	
	public function fngetcourseComponentlist($lintSubjectId){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('sch'=>'tbl_subcredithoursdistrbtn'),array("sch.*"))
		->joinLeft(array('assesitem' => 'tbl_examination_assessment_item'),'sch.IdcomponentItem = assesitem.IdExaminationAssessmentType',array('Description as compitem'))
		->joinLeft(array('assestype' => 'tbl_examination_assessment_type'),'sch.IdComponents = assestype.IdExaminationAssessmentType',array('Description as comptype'))
		->where("sch.IdSubject= ?",$lintSubjectId);
		$larrResult = $lobjDbAdpt->fetchAll($select);
		return $larrResult;
	}
	

}