<?php


class App_Model_Record_DbTable_StudentRegSubjects extends Zend_Db_Table_Abstract {
	
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_studentregsubjects';
	protected $_primary = "IdStudentRegSubjects";
	
	
	public function getActiveRegisteredCourse($idSemesterMain,$IdStudentRegistration){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		 $sql = $db->select()
                        ->from(array('sr' => 'tbl_studentregistration'), array('registrationId'))  
                        ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')
                        ->join(array('ct'=>'tbl_course_tagging_group'),'ct.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array('coor'=>'IdLecturer'))
                        ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName','subjectMainDefaultLanguage','BahasaIndonesia','CreditHours','SubCode'))   
                        ->joinLeft(array('cgs'=>'course_group_schedule'),'idGroup=srs.IdCourseTaggingGroup')                  
                        ->where('sr.IdStudentRegistration = ?', $IdStudentRegistration)
                        ->where('srs.IdSemesterMain = ?',$idSemesterMain)
                        ->where('srs.Active=1')
                        ->where('srs.subjectlandscapetype != 2');
                        
                        
                        
       $sql .= 					"ORDER BY CASE cgs.sc_day 
                                 WHEN 'Monday' THEN 1
                                 WHEN 'Tuesday' THEN 2
                                 WHEN 'Wednesday' THEN 3
                                 WHEN 'Thursday' THEN 4
                                 WHEN 'Friday' THEN 5
                                 WHEN 'Saturday' THEN 6
                                 WHEN 'Sunday' THEN 7
                                 ELSE 8
                                 END ";
                          
         
        $result = $db->fetchAll($sql);
        return $result;
	}
	
	public function getSemesterSubjectRegistered($idSemesterMain,$IdStudentRegistration,$active=1, $subjectType=array(1,3)){
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$sql = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName','subjectMainDefaultLanguage','BahasaIndonesia','CreditHours','SubCode'))
		->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
		->where('srs.IdSemesterMain = ?',$idSemesterMain)
		->where('srs.Active=?',$active)
		->where('srs.subjectlandscapetype in (?)', $subjectType);
		//echo $sql;exit;	
		$result = $db->fetchAll($sql);
		return $result;
	
	}
	
	
	public function getTotalCreditHoursActiveRegisteredCourse($idSemesterMain,$IdStudentRegistration){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                        ->from(array('srs' => 'tbl_studentregsubjects'),array())   
                        ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject', array('total'=>new Zend_Db_Expr('SUM(CreditHours)')))                       
                        ->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
                        ->where('srs.IdSemesterMain = ?',$idSemesterMain)
                        ->where('srs.Active=1')
                        ->where('srs.subjectlandscapetype != 2');
                                                         
        $result = $db->fetchRow($sql);
        return $result["total"];
	}
	
	public function getSubject($idSemesterMain,$IdStudentRegistration,$idSubject){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                        ->from(array('srs' => 'tbl_studentregsubjects'),array())                                            
                        ->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
                        ->where('srs.IdSemesterMain = ?',$idSemesterMain)
                        ->where('srs.IdSubject = ?',$idSubject);
         
        $result = $db->fetchRow($sql);
        
	}
	
	
	public function isRegister($IdStudentRegistration,$idSubject,$idSemester){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                        ->from(array('srs' => 'tbl_studentregsubjects'))                                            
                        ->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)                        
                        ->where('srs.IdSubject = ?',$idSubject)
                        ->where('srs.IdSemesterMain = ?',$idSemester);
         
        return $result = $db->fetchRow($sql);
        
	}
	
	public function isBlock7Registered($IdStudentRegistration,$idlandscape){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$sql = $db->select()
		->from(array('srs' => 'tbl_studentregsubjects'))
		->join(array('sub'=>'tbl_landscapeblocksubject'),'sub.subjectid=srs.IdSubject')
		->join(array('bl'=>'tbl_landscapeblock'),'bl.idblock=sub.blockid')
		->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
		->where('sub.IdLandscape = ?',$idlandscape)
		->where('bl.block = "7"')
		->where('srs.IdSubject<>5842');// exclude skripsi
		 
		$row=$db->fetchRow($sql);
		if ($row) return true; else return false;
	
	}
	
	 
	
	public function addData($data){	

		if (!$this->isRegister($data['IdStudentRegistration'], $data['IdSubject'], $data['IdSemesterMain'])) {
			$this->insert($data);
	        $this->addStudentMapping($data);
		}
	}
	
	
	//to list subject yg pernah diregister and exam status completed and subject tu offer pada semester pembaikan (atau semester yg dipilih)
	public function getSubjectRegisterPreviousSemester($IdStudentRegistration,$landscape_id,$semester_id,$landscape_type,$subject_code){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 	
		//semester based
		if($landscape_type==43){
			
			 $sql = $db->select()
                        ->from(array('srs' => 'tbl_studentregsubjects'))    
                        ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=srs.IdSubject',array('BahasaIndonesia','SubCode','CreditHours'))
                        ->join(array('ls'=>'tbl_landscapesubject'),'ls.IdSubject=srs.IdSubject',array())
                        ->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('SubjectType'=>'DefinitionDesc')) 
                        ->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=srs.IdSubject',array())
                        ->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)    
                        ->where("srs.exam_status = 'C' ") //status exam mesti C=completed
                        ->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
                        ->where('ls.IdLandscape = ?',$landscape_id)    
                        ->order('s.SubCode') 
                        ->group('srs.IdSubject');
                        
			 if(isset($subject_code) && $subject_code!=''){
			  	$sql->where("s.SubCode LIKE '%".$subject_code."%'");
			  }
		}
		

       	//landscape based	
		if($landscape_type==44){
			
			 //untuk semester pembaikan display yang bujang dan anak sahaja.bapak x perlu
			 
			 $sql = $db->select()
                        ->from(array('srs' => 'tbl_studentregsubjects'))    
                        ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=srs.IdSubject',array('BahasaIndonesia','SubCode','CreditHours'))
                        ->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=srs.IdSubject',array())
                        ->join(array('ls'=>'tbl_landscapeblocksubject'),'ls.subjectid=srs.IdSubject',array())
                        ->join(array('lb'=>'tbl_landscapeblock'),'lb.idblock=ls.blockid',array('block_level'=>'block'))
                        ->join(array("d"=>"tbl_definationms"),'d.idDefinition=ls.coursetypeid',array('SubjectType'=>'DefinitionDesc')) 
                        ->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)    
                        ->where("srs.exam_status = 'C' ")  //status exam mesti C=completed
                        ->where('(ls.type = 3 OR ls.type = 1)') //bujang atau anak  sahaja                  
                        ->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
                        ->where('ls.IdLandscape = ?',$landscape_id)   
                        ->order('s.SubCode')                    
                        ->group('srs.IdSubject');   

		 	  if(isset($subject_code) && $subject_code!=''){
			  	$sql->where("s.SubCode LIKE '%".$subject_code."%'");
			  }
        }
       				
        $result =  $db->fetchAll($sql);
        $subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
         

		foreach($result as $key=>$row){
		  				
			    //setkan child=='' sebab guna jquery loop yg sama kalo x nanti display ada bugs
		 	     $result[$key]['child'] = '';  
			    
				//get subject status already taken/registered or not
	         	$subject_registered = $subjectRegDB->isRegister($IdStudentRegistration,$row["IdSubject"],$semester_id);	
	         	
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
	
	public function getSubjectRegisterAllPreviousSemester($IdStudentRegistration,$landscape_id,$semester_id,$landscape_type,$subject_code){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	    //semester based
		if($landscape_type==43){
				
			$sql = $db->select()
			 
			->from(array('srs' => 'tbl_studentregsubjects') )
			->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=srs.IdSubject',array('BahasaIndonesia','SubCode','CreditHours'))
			->join(array('ls'=>'tbl_landscapesubject'),'ls.IdSubject=srs.IdSubject',array())
			->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('SubjectType'=>'DefinitionDesc'))
			->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=srs.IdSubject',array())
			->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
			->where("srs.exam_status = 'C' ") //status exam mesti C=completed
			->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
			->where('ls.IdLandscape = ?',$landscape_id)
			->order('s.SubCode')
			->group('srs.IdSubject');
			if ($landscape_id==32||$landscape_id==34||$landscape_id==36) 
				$sql->where('srs.grade_point>0');
			if(isset($subject_code) && $subject_code!=''){
				$sql->where("s.SubCode LIKE '%".$subject_code."%'");
			}
		}
	
	
		//landscape based
		if($landscape_type==44){
				
			//untuk semester pembaikan display yang bujang dan anak sahaja.bapak x perlu
	
			$sql = $db->select()
			->from(array('srs' => 'tbl_studentregsubjects'))
			->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=srs.IdSubject',array('BahasaIndonesia','SubCode','CreditHours'))
			->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=srs.IdSubject',array())
			->join(array('ls'=>'tbl_landscapeblocksubject'),'ls.subjectid=srs.IdSubject',array())
			->join(array('lb'=>'tbl_landscapeblock'),'lb.idblock=ls.blockid',array('block_level'=>'block'))
			->join(array("d"=>"tbl_definationms"),'d.idDefinition=ls.coursetypeid',array('SubjectType'=>'DefinitionDesc'))
			->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
			->where("srs.exam_status = 'C' ")  //status exam mesti C=completed
			->where('(ls.type = 3 OR ls.type = 1)') //bujang atau anak  sahaja
			->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
			->where('ls.IdLandscape = ?',$landscape_id)
			->order('s.SubCode')
			->group('srs.IdSubject');
	
			if(isset($subject_code) && $subject_code!=''){
				$sql->where("s.SubCode LIKE '%".$subject_code."%'");
			}
		}
		 
		$result =  $db->fetchAll($sql);
		$subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
			
	
		foreach($result as $key=>$row){
	
			//setkan child=='' sebab guna jquery loop yg sama kalo x nanti display ada bugs
			$result[$key]['child'] = '';
			 
			//get subject status already taken/registered or not
			$subject_registered = $subjectRegDB->isRegister($IdStudentRegistration,$row["IdSubject"],$semester_id);
			 
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
	public function getSubjectRegisterCurrentSemester($IdStudentRegistration,$landscape_id,$semester_id,$landscape_type,$subject_code,$gradepointmin=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		if ($gradepointmin==null) $gradepointmin=0;
		//get reguler semester
		$dbx=new App_Model_General_DbTable_Semestermaster();
		$sem = $dbx->getRegulerSemester($semester_id);
		$semidreguler=$sem['IdSemesterMaster'];
		//echo $semidreguler;exit;
		//semester based
		if($landscape_type==43){
				
			$sql = $db->select()
			->from(array('srs' => 'tbl_studentregsubjects'))
			->join(array('ct'=>'tbl_course_tagging_group'),'ct.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array('GroupCode'))
			->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=srs.IdSubject',array('BahasaIndonesia','SubCode','CreditHours'))
			->join(array('ls'=>'tbl_landscapesubject'),'ls.IdSubject=srs.IdSubject',array())
			->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('SubjectType'=>'DefinitionDesc'))
			->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=srs.IdSubject',array())
			->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
			->where("srs.exam_status = 'C' ")
			->where("srs.grade_point>=?",$gradepointmin) //status exam mesti C=completed
			->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
			->where('srs.IdSemesterMain = ?',$semidreguler) // current semester saja
			->where('ls.IdLandscape = ?',$landscape_id)
			->order('s.SubCode')
			->group('srs.IdSubject');
			
			if(isset($subject_code) && $subject_code!=''){
				$sql->where("s.SubCode LIKE '%".$subject_code."%'");
			}
		}
	
	
		//landscape based
		if($landscape_type==44){
				
			//untuk semester pembaikan display yang bujang dan anak sahaja.bapak x perlu
	
			$sql = $db->select()
			->from(array('srs' => 'tbl_studentregsubjects'))
			->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=srs.IdSubject',array('BahasaIndonesia','SubCode','CreditHours'))
			->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=srs.IdSubject',array())
			->join(array('ls'=>'tbl_landscapeblocksubject'),'ls.subjectid=srs.IdSubject',array())
			->join(array('lb'=>'tbl_landscapeblock'),'lb.idblock=ls.blockid',array('block_level'=>'block'))
			->join(array("d"=>"tbl_definationms"),'d.idDefinition=ls.coursetypeid',array('SubjectType'=>'DefinitionDesc'))
			->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
			->where("srs.exam_status = 'C' ")  //status exam mesti C=completed
			->where("srs.grade_point>=?",$gradepointmin)
			->where('(ls.type = 3 OR ls.type = 1)') //bujang atau anak  sahaja
			->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
			->where('srs.IdSemesterMain = ?',$semidreguler) // current semester saja
			->where('ls.IdLandscape = ?',$landscape_id)
			->order('s.SubCode')
			->group('srs.IdSubject');
			
			if(isset($subject_code) && $subject_code!=''){
				$sql->where("s.SubCode LIKE '%".$subject_code."%'");
			}
		}
		//echo $sql;exit;		
		$result =  $db->fetchAll($sql);
	
	
		foreach($result as $key=>$row){
	
			//setkan child=='' sebab guna jquery loop yg sama kalo x nanti display ada bugs
			$result[$key]['child'] = '';
			 
			//get subject status already taken/registered or not
			$subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
			$subject_registered = $subjectRegDB->isRegister($IdStudentRegistration,$row["IdSubject"],$semester_id);
			 
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
	
	function drop($sregid){
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
        $sql = $db->select()
               ->from($this->_name)
               ->where('ApprovedBy = 0')
               ->where('IdStudentRegSubjects = ?', (int)$sregid);
        $data = $db->fetchRow($sql);
        
        if ($data) {
        	$idstd=$data['IdStudentRegistration'];
        	$iddrop=$auth->getIdentity()->registration_id;
        	$idadmin=$auth->getIdentity()->adminid;
        	$idadminname=$auth->getIdentity()->adminlogin;
        	if ($idstd==$iddrop) {
        	 
	       		$db->delete($this->_name,"IdStudentRegSubjects =".$sregid);
	       		if ($this->hasGroupMinor($data['IdCourseTaggingGroup'])) $this->dropMinor($data);//$this->dropStudentMapping($data);
        		//check invoice
	       		$sql = $db->select()
	       				->from('invoice_main')
	       				->where('IdStudentRegistration=?',$iddrop)
	       				->where('idactivity in (39,40,42)')
	       				->where('status_va<>"P"')
	       				->where('semester=?',$data['IdSemesterMain']);
	       		$invoice=$db->fetchRow($sql);
	       		if ($invoice) $db->delete('invoice_main',"id =".$invoice['id']);
	       		else {
	       			$sql = $db->select()
	       			->from('invoice_main')
	       			->where('IdStudentRegistration=?',$iddrop)
	       			->where('idactivity in (39,40,42)')
	       			->where('status_va="P"')
	       			->where('semester=?',$data['IdSemesterMain']);
	       			$invoice=$db->fetchRow($sql);
	       			if ($invoice) {
	       				$sql = $db->select()
	       				->from('payment_main')
	       				->where('IdStudentRegistration=?',$iddrop)
	       				->where('no_billing=?',$invoice['bill_number']);
	       				$payment=$db->fetchRow($sql);
	       				
	       				$advancePaymentDb = new Studentfinance_Model_DbTable_AdvancePayment();
	       				$idsubject=$data['IdSubject'];
	       				//get payment
	       				$sql = $db->select()
	       				->from('tbl_subjectmaster')
	       				->where('IdSubject=?',$idsubject);
	       				$sksrec=$db->fetchRow($sql);
	       				$sks=$sksrec['CreditHours'];
	       				//advantage
	       				$adv_amount = $sks*300000;
	       				$data = array(
	       						'advpy_appl_id' => $invoice['appl_id'],
	       						'advpy_acad_year_id' => $invoice['academic_year'],
	       						'advpy_sem_id' => $invoice['semester'],
	       						'advpy_prog_code' => $invoice['program_code'],
	       						'advpy_fomulir' => $invoice['no_fomulir'],
	       						'advpy_invoice_no' => $invoice['bill_number'],
	       						'advpy_invoice_id' => $invoice['id'],
	       						'advpy_payment_id' => $payment['id'],
	       						'advpy_description' => 'Excess Payment for invoice no:'.$invoice['bill_number'],
	       						'advpy_amount' => $adv_amount,
	       						'advpy_total_paid' => 0,
	       						'advpy_total_balance' => $adv_amount,
	       						'advpy_status' => 'A'
	       				
	       				);
	       				$advancePaymentDb->insert($data);
	       				
	       				//$amt_paid = $tot_amount - $adv_amount;
	       				$paid = $invoice['bill_paid'] - $adv_amount;
	       				$amount = $invoice['bill_amount'] - $adv_amount; 
	       				
	       				$data = array(
	       						'bill_paid' => $paid,
	       						'bill_amount' => $amount
	       				);
	       				$db->update('invoice_main', 'id = '.$invoice['id']);
	       			}
	       		}
        	
        	}
        	$data['id_userdrop']=$iddrop;
        	$data['id_admin']=$idadmin;
        	$data['admin_login']=$idadminname;
	        $db->insert('tbl_studentregsubjects_del',$data);
        }
    }
	
	public function getSubjectOfferedPackage($IdStudentRegistration,$landscape_id,$semester_id,$landscape_type,$subject_code,$level=null,$lastblock,$program=null,$idbranch=null,$intake=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		//check for configure package
		  
		if ($idbranch!=null && $intake!=null) {
			$sql=$db->select()
			->from(array('a'=>'course_register_package'))
			->join(array('b'=>'course_register_package_scheme'),'a.idpackage=b.idpackage')
			->where('IdProgram=?',$program)
			->where('IdBranch=?',$idbranch);
			
			$row=$db->fetchRow($sql);
			//echo var_dump($row); exit;
			if ($row) {
				if($landscape_type==43){
					$sql = $db->select()
					->from(array("s"=>"tbl_subjectmaster"),array('BahasaIndonesia','SubCode','CreditHours','IdSubject'))
					->join(array('ls'=>'course_register_package_scheme'),'ls.IdSubject=s.IdSubject',array()) 
					->join(array('lss'=>'tbl_landscapesubject'),'lss.IdSubject=s.IdSubject',array('IdLandscapeSub'))
					->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=lss.SubjectType',array('SubjectType'=>'DefinitionDesc'))
					->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=s.IdSubject',array())
					->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
					->where('ls.idpackage = ?',$row['idpackage'])
					->where('ls.idsemester=?',$semester_id)
					->where('ls.idintake=?',$intake)
					->order('s.SubCode')
					->group('s.IdSubject');
				} else {
					$sql = $db->select()
					->from(array("s"=>"tbl_subjectmaster"),array('BahasaIndonesia','SubCode','CreditHours','IdSubject'))
					->join(array('ls'=>'course_register_package_scheme'),'ls.IdSubject=s.IdSubject',array())
					 ->join(array('lss'=>'tbl_landscapeblocksubject'),'lss.subjectid=s.IdSubject',array('blockid','IdLandscapeblocksubject'))
	                 ->join(array('bl'=>'tbl_landscapeblock'),'bl.idblock=lss.blockid')
	                 ->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=lss.SubjectType',array('SubjectType'=>'DefinitionDesc'))
					->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=s.IdSubject',array())
					->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
					->where('ls.idpackage = ?',$row['idpackage'])
					->where('ls.idsemester=?',$semester_id)
					->where('ls.idintake=?',$intake)
					->order('s.SubCode')
					->group('s.IdSubject');
				}
				
			}	else {
				//semester based
				//echo $level; echo $landscape_type;exit;
				if($landscape_type==43){
				
					$sql = $db->select()
					->from(array("s"=>"tbl_subjectmaster"),array('BahasaIndonesia','SubCode','CreditHours','IdSubject'))
					->join(array('ls'=>'tbl_landscapesubject'),'ls.IdSubject=s.IdSubject',array('IdLandscapeSub'))
					->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('SubjectType'=>'DefinitionDesc'))
					->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=s.IdSubject',array())
					->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
					->where('ls.IdLandscape = ?',$landscape_id)
					->where('ls.IdSemester=?',$level)
					->order('s.SubCode')
					->group('s.IdSubject');
						
					if(isset($subject_code) && $subject_code!=''){
						$sql->where("s.SubCode LIKE '%".$subject_code."%'");
					}
				}
				
				 
				if($landscape_type==44){
					//
						
					$sql = $db->select()
					->from(array("s"=>"tbl_subjectmaster"),array('BahasaIndonesia','SubCode','CreditHours','IdSubject'))
					->join(array('ls'=>'tbl_landscapeblocksubject'),'ls.subjectid=s.IdSubject',array('blockid','IdLandscapeblocksubject'))
					->join(array('bl'=>'tbl_landscapeblock'),'bl.idblock=ls.blockid')
					->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=ls.coursetypeid',array('SubjectType'=>'DefinitionDesc'))
					->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=s.IdSubject',array())
					->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
					->where('ls.IdLandscape = ?',$landscape_id)
					->order('ls.blockid')
					->order('s.SubCode')
					->group('s.IdSubject');
					
					if ($program==11 && $lastblock==7) {
						//just for medicine program for studnet in last block can not take lower block
						$sql->where("bl.block=7");
					} else if ($program==11 ) {
						$sql->where("bl.block<=?",$lastblock);
					}
				
					if(isset($subject_code) && $subject_code!=''){
						$sql->where("s.SubCode LIKE '%".$subject_code."%'");
					}
						
				
					$result =  $db->fetchAll($sql);
				}
			}
			
			if ($program!=11 ) {
				///echo $sql;exit;
				$result =  $db->fetchAll($sql);
				//get course ulang
				$sql = $db->select()->distinct()
				->from(array("s"=>"tbl_subjectmaster"),array('BahasaIndonesia','SubCode','CreditHours','IdSubject'))
				->join(array('ls'=>'tbl_studentregsubjects'),'ls.IdSubject=s.IdSubject',array())
				->join(array('lss'=>'tbl_landscapesubject'),'lss.IdSubject=s.IdSubject',array('IdLandscapeSub'))
				->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=lss.SubjectType',array('SubjectType'=>'DefinitionDesc'))
				->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=s.IdSubject',array())
				->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
				->where('ls.idsemestermain <>?',$semester_id)
				->where('ls.IdStudentRegistration=?',$IdStudentRegistration)
				->where('ls.grade_point < 3')
				->order('s.SubCode')
				->group('s.IdSubject');
				$resultulang =  $db->fetchAll($sql);
				
				$result=array_merge($result,$resultulang);
			}
		
		}
		
		 
	   
        if(count($result)>0){
		foreach($result as $key=>$row){
		  				
			    //setkan child=='' sebab guna jquery loop yg sama kalo x nanti display ada bugs
		 	     $result[$key]['child'] = '';  
			    
				//get subject status already taken/registered or not
	         	$subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
	         	$subject_registered = $subjectRegDB->isRegister($IdStudentRegistration,$row["IdSubject"],$semester_id);	
	         	
	         	if(is_array($subject_registered)){
	         		$result[$key]['register_status']="Registered";
	         		$result[$key]['register']=1;
	         	} else{
	         		$result[$key]['register_status']="Not Registered";
	         		$result[$key]['register']=2;
	         	}  
		  }
        }
		  
        return $result;
	}
	
	
	public function getSubjectOfferedReg($IdStudentRegistration,$landscape_id,$semester_id,$landscape_type,$subject_code,$level=null,$lastblock,$program=null,$idbranch=null,$intake=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		//check for configure package
		 
			//semester based
			if($landscape_type==43){
	
				$sql = $db->select()
				->from(array("s"=>"tbl_subjectmaster"),array('BahasaIndonesia','SubCode','CreditHours','IdSubject'))
				->join(array('ls'=>'tbl_landscapesubject'),'ls.IdSubject=s.IdSubject',array('IdLandscapeSub'))
				->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('SubjectType'=>'DefinitionDesc'))
				->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=s.IdSubject',array())
				->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
				->where('ls.IdLandscape = ?',$landscape_id)
				->order('s.SubCode')
				->group('s.IdSubject');
				 
				if(isset($subject_code) && $subject_code!=''){
					$sql->where("s.SubCode LIKE '%".$subject_code."%'");
				}
			}
				
				
			if($landscape_type==44){
				//
					
				$sql = $db->select()
				->from(array("s"=>"tbl_subjectmaster"),array('BahasaIndonesia','SubCode','CreditHours','IdSubject'))
						->join(array('ls'=>'tbl_landscapeblocksubject'),'ls.subjectid=s.IdSubject',array('blockid','IdLandscapeblocksubject'))
						->join(array('bl'=>'tbl_landscapeblock'),'bl.idblock=ls.blockid')
						->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=ls.coursetypeid',array('SubjectType'=>'DefinitionDesc'))
								->join(array("so"=>'tbl_subjectsoffered'),'so.IdSubject=s.IdSubject',array())
								->where('so.IdSemester = ?',$semester_id) //offer pada semester ini
										->where('ls.IdLandscape = ?',$landscape_id)
										->order('ls.blockid')
										->order('s.SubCode')
										->group('s.IdSubject');
				if ($program==11 && $lastblock==7) {
										//just for medicine program for studnet in last block can not take lower block
						$sql->where("bl.block=7");
				}
			 
				if(isset($subject_code) && $subject_code!=''){
					$sql->where("s.SubCode LIKE '%".$subject_code."%'");
				}
		 
				
			
		}
		$result =  $db->fetchAll($sql);
	
					if(count($result)>0){
					foreach($result as $key=>$row){
	
							//setkan child=='' sebab guna jquery loop yg sama kalo x nanti display ada bugs
							$result[$key]['child'] = '';
							 
							//get subject status already taken/registered or not
							$subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
					$subject_registered = $subjectRegDB->isRegister($IdStudentRegistration,$row["IdSubject"],$semester_id);
				 
				if(is_array($subject_registered)){
				$result[$key]['register_status']="Registered";
				$result[$key]['register']=1;
							} else{
							$result[$key]['register_status']="Not Registered";
									$result[$key]['register']=2;
							}
							}
							}
	
							return $result;
							}
	

	
	public function getRegSubjectBySemId($IdStudentRegistration,$IdSemester,$landscape){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
		 $sql = $db->select()
                        ->from(array('ct'=>'tbl_course_tagging_group'))
                        ->join(array('srs' => 'tbl_studentregsubjects'),'ct.IdCourseTaggingGroup=srs.IdCourseTaggingGroup')    
                        ->join(array("s"=>"tbl_subjectmaster"),'s.IdSubject=srs.IdSubject',array('BahasaIndonesia','SubCode','CreditHours'))
                        ->where('srs.IdSemesterMain = ?',$IdSemester)
                        ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
                        ->where('srs.subjectlandscapetype !=2');
                        
                        
         if($landscape["LandscapeType"]==43){         	
         	 $sql->join(array('ls'=>'tbl_landscapesubject'),'ls.IdSubject=s.IdSubject',array());
         	 $sql->join(array('pr'=>'tbl_programrequirement'),'pr.IdProgramReq=ls.IdProgramReq',array());
         	 $sql->join(array("d"=>"tbl_definationms"),'d.idDefinition=pr.SubjectType',array('SubjectType'=>'DefinitionDesc'));
         	  
         	 $sql->where('ls.IdLandscape= ?',$landscape['IdLandscape']);        	
         
         }    
         
         if($landscape["LandscapeType"]==44){         	
         	 $sql->join(array('ls'=>'tbl_landscapeblocksubject'),'ls.subjectid=s.IdSubject',array());         
         	 $sql->join(array('pr'=>'tbl_programrequirement'),'pr.IdProgramReq=ls.IdProgramReq',array());
         	 $sql->join(array("d"=>"tbl_definationms"),'d.idDefinition=pr.SubjectType',array('SubjectType'=>'DefinitionDesc'));
         	  
         	 $sql->where('ls.IdLandscape= ?',$landscape['IdLandscape']); 	
         } 
         
       
         //echo $sql;
		 $result =  $db->fetchAll($sql);
		 return $result;
	}

	public function isPass($IdStudentRegistration,$idSubject,$grade=null,$type=null,$total_credit=null,$passtotal_credit=null,$cgpa=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		if(($grade==null)){
			$sql = $db->select()
			->from(array('srs' => 'tbl_studentregsubjects'))
			->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
			->where('srs.IdSubject = ?',$idSubject)
			->where('srs.exam_status = ?','C')
			->where('srs.grade_status = ?','Pass');
			$result = $db->fetchRow($sql);
			if(!empty($result)){
				return true;
			}else{
				return false;
			}
		}
	}
	
	public function tookOnline($idsemester,$IdStudentRegistration,$idSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		if(($grade==null)){
			$sql = $db->select()
			->from(array('srs' => 'tbl_studentregsubjects'))
			->join(array('ct'=>'tbl_course_tagging_group'),'srs.IdCourseTaggingGroup=ct.IdCourseTaggingGroup')
			->join(array('sc'=>'course_group_schedule'),'sc.idGroup=ct.IdCourseTaggingGroup')
			->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
			->where('srs.IdSubject = ?',$idSubject)
			->where('srs.IdSemesterMain <> ?',$idsemester)
			->where('sc.learning_mode="1"');
			$result = $db->fetchRow($sql);
			if(!empty($result)){
				return true;
			}else{
				return false;
			}
		}
	}
	
	public function isLuring($idsemester,$IdStudentRegistration,$idSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		if(($grade==null)){
			$sql = $db->select()
			->from(array('srs' => 'tbl_studentregsubjects'))
			->join(array('ct'=>'tbl_course_tagging_group'),'srs.IdCourseTaggingGroup=ct.IdCourseTaggingGroup')
			->join(array('sc'=>'course_group_schedule'),'sc.idGroup=ct.IdCourseTaggingGroup')
			->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
			->where('srs.IdSubject = ?',$idSubject)
			->where('srs.IdSemesterMain = ?',$idsemester)
			->where('sc.learning_mode="0"');
			$result = $db->fetchRow($sql);
			if(!empty($result)){
				return true;
			}else{
				return false;
			}
		}
	}
	
	public function isCompleted($IdStudentRegistration,$idSubject,$grade=null,$type=null,$total_credit=null,$passtotal_credit=null,$cgpa=null,$currentsem=null,$cheklate=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		if(($grade==null)){ 
			 
			$sql = $db->select()
	                        ->from(array('srs' => 'tbl_studentregsubjects'))                                            
	                        ->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)                        
	                        ->where('srs.IdSubject = ?',$idSubject)
	         //               ->where('srs.exam_status = ?','C')
	         ;
	                        //->where('srs.grade_status = ?','Pass');
	          $result = $db->fetchRow($sql);
			 if(!empty($result)){
	         	return true;
	         }else{
	         	//cek for location prerequisite
	         	
	         	return false;
	         }
	         
		}else{
			
			if($type==2){ //Total Credit Hour
				
				if($total_credit >= $grade){
					return true;
				}else{
					return false;
				}
				
			}else if($type==4){ //Major
				//get landscape
				$sql = $db->select()
				->from(array('l' => 'tbl_landscape'))
				->join(array('sr'=>'tbl_studentregistration'),'sr.idlandscape=l.idlandscape')
				->where('sr.IdStudentRegistration=?',$IdStudentRegistration);
				$landscape=$db->fetchRow($sql);
				if ($landscape['LandscapeType']=="43") {
					//semester
					$sql = $db->select()
					->from(array('l' => 'tbl_landscapesubject'))
					->where('l.IdLandscape=?',$landscape['IdLandscape'])
					->where('l.SubjectType=273');
					$subject=$db->fetchAll($sql);
					if ($subject) {
						foreach ($subject as $value) {
							$subjects[]=$value['IdSubject'];
						} 
						$subjects=implode(',', $subjects);
					} else $subjects='0';
					
					
				} else if ($landscape['LandscapeType']=="44") {
					//blok
					$sql = $db->select()
					->from(array('l' => 'tbl_landscapeblocksubject'))
					->where('l.IdLandscape=?',$landscape['IdLandscape'])
					->where('l.SubjectType=273');
					$subject=$db->fetchAll($sql);
					if ($subject) {
						foreach ($subject as $value) {
							$subjects[]=$value['IdSubject'];
						} 
						$subjects=implode(',', $subjects);
					} else $subjects='0';
				}
				//get total major
				$sql = $db->select()
				->from(array('srs' => 'tbl_studentregsubjects'),array('Mayor'=>'count(distinct IdSubject)'))
				->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
				
				//->where('srs.exam_status = ?','C')
				;
				if ($subjects!='0') $sql->where('srs.IdSubject in ('.$subjects.')');
				//echo $sql;exit;
				//->where('srs.grade_status = ?','Pass');
				$result = $db->fetchRow($sql);
				if($result['Mayor'] >= $grade){
					return true;
				}else{
					return false;
				}
				
			} else if($type==5){ //Minor
			//get landscape
				$sql = $db->select()
				->from(array('l' => 'tbl_landscape'))
				->join(array('sr'=>'tbl_studentregistration'),'sr.idlandscape=l.idlandscape')
				->where('sr.IdStudentRegistration=?',$IdStudentRegistration);
				$landscape=$db->fetchRow($sql);
				if ($landscape['LandscapeType']=="43") {
					//semester
					$sql = $db->select()
					->from(array('l' => 'tbl_landscapesubject'))
					->where('l.IdLandscape=?',$landscape['IdLandscape'])
					->where('l.SubjectType=1129');
					$subject=$db->fetchAll($sql);
					if ($subject) {
						foreach ($subject as $value) {
							$subjects[]=$value['IdSubject'];
						}
						$subjects=implode(',', $subjects);
					}else $subjects="0";
					
				} else if ($landscape['LandscapeType']=="44") {
					//blok
					$sql = $db->select()
					->from(array('l' => 'tbl_landscapeblocksubject'))
					->where('l.IdLandscape=?',$landscape['IdLandscape'])
					->where('l.SubjectType=1129');
					$subject=$db->fetchAll($sql);
					if ($subject) { 
						foreach ($subject as $value) {
							$subjects[]=$value['IdSubject'];
						}
						$subjects=implode(',', $subjects);
					}else $subjects="0";
				}
				//get total major
				$sql = $db->select()
				->from(array('srs' => 'tbl_studentregsubjects'),array('Mayor'=>'count(distinct IdSubject)'))
				->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
				
				//->where('srs.exam_status = ?','C')
				;
				if ($subjects!="0") $sql->where('srs.IdSubject in ('.$subjects.')');
				//->where('srs.grade_status = ?','Pass');
				$result = $db->fetchRow($sql);
				if($result['Mayor'] >= $grade){
					return true;
				}else{
					return false;
				}
				
			} else if($type==6){
				//passed cummulative credit hours
				if($passtotal_credit >= $grade*1){
					return true;
				}else{
					return false;
				} 
			}else if($type==7){
				//minimum CGPA
				if($cgpa >= $grade){
					return true;
				}else{
					return false;
				} 
			}else { //Pass With Grade
				
				//ni sepatutnya check kat setup tapi urgent hardcode sini dulu
				$gpoint["C"]=2.00;
				$gpoint["C+"]=2.50;
				$gpoint["B-"]=2.75;
				$gpoint["B"]=3.00;
				$gpoint["B+"]=3.50;
				$gpoint["A"]=4.00;
				$gpoint["A-"]=3.75;
				$gpoint["D"]=1.00;
				$gpoint["E"]=0.00;
				//-----------------------make it based on setup---------
				
				
				//------------------------------------------------------
				if(isset($gpoint[$grade])){
					
					$sql = $db->select()
			                        ->from(array('srs' => 'tbl_studentregsubjects'))                                            
			                        ->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)                        
			                        ->where('srs.IdSubject = ?',$idSubject)
			                        ->where('srs.exam_status = ?','C')
			                        ->where('srs.grade_point >= ?',$gpoint[$grade]);
			         $result = $db->fetchRow($sql);
			         
			         if(!empty($result)){
			         	return true;
			         }else{
			         	//check for late grade entry 
			         	if ($cheklate=="1") {
			         		$dbSemester=new App_Model_General_DbTable_Semestermaster();
			         		$sempre=$dbSemester->getPreviousSemester($currentsem);
			         		$sql = $db->select()
			         		->from(array('srs' => 'tbl_studentregsubjects'))
			         		->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
			         		->where('srs.IdSubject = ?',$idSubject)
			         		->where('srs.exam_status is null')
			         		->where('srs.grade_name is null');
			         		$result = $db->fetchRow($sql);
			         		if(!empty($result)){
			         			return true;
			         		}else return false;
			         	}
			         	return false;
			         }
				}else{
					return false;
				}			
			}
		}
	}

	
	public function isCoRequisite($IdStudentRegistration,$idSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		 
			$sql = $db->select()
			->from(array('srs' => 'tbl_studentregsubjects'))
			->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
			->where('srs.IdSubject = ?',$idSubject);
			
			$result = $db->fetchRow($sql);
			if(!empty($result)){
				return true;
			}else{
				return false;
			}
	
		 
	}
    public function addStudentMapping($data)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
        $Tagging = new Zend_Db_Table('tbl_course_group_student_mapping');
        $add = array(
            'IdCourseTaggingGroup' => $data['IdCourseTaggingGroup'],
            'IdStudent' => $data['IdStudentRegistration']
        );
        $where = 'IdCourseTaggingGroup ='.$data['IdCourseTaggingGroup'].' and  IdStudent = '.$data['IdStudentRegistration'];
        $db->delete('tbl_course_group_student_mapping',$where);
        $Tagging->insert($add);
    }
    
    public function dropStudentMapping($data)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $delete = array(
            'IdCourseTaggingGroup = ?' => $data['IdCourseTaggingGroup'],
            'IdStudent = ?' => $data['IdStudentRegistration']
        );
        $where = 'IdCourseTaggingGroup ='.$data['IdCourseTaggingGroup'].' and  IdStudent = '.$data['IdStudentRegistration'];
        //$Tagging = new Zend_Db_Table('tbl_course_group_student_mapping');
       $db->delete('tbl_course_group_student_mapping',$where);
    }
    
    public function dropMinor($data)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$sql = $db->select()
    	->from(array('sb' => 'tbl_course_tagging_group_minor'))
    	->join(array('sm'=>'course_group_student_minor'),'sb.IdCourseTaggingGroupMinor=sm.IdCourseTaggingGroupMinor')
    	->where('sb.group_id=?',$data['IdCourseTaggingGroup'])
    	->where('sm.IdStudentRegistration=?',$data['IdStudentRegistration']);
    	$row = $db->fetchRow($sql);
    	if ($row) $db->delete('course_group_student_minor','IdStudentMinor='.$row['IdStudentMinor']);
    	 
    	
    }
    
    public function hasGroupMinor($idcoursetagginggroup)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$sql = $db->select()
    	->from(array('sb' => 'tbl_course_tagging_group_minor'))
    	->where('sb.group_id=?',$idcoursetagginggroup);
    	$row = $db->fetchRow($sql);
    	if ($row) return true; else return false;
    	 
    }
    
    
    public function getSubjectmaster($id) {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$sql = $db->select()
    	->from(array('sb' => 'tbl_subjectmaster'))
    	->where('IdSubject=?',$id);
    	$row = $db->fetchRow($sql);
    	return $row;
    }
    
    public function saveCurrentCH($idreg,$ch) {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$sql = $db->select()
    	->from(array('sb' => 'tbl_temp_credithours'))
    	->where('IdStudentRegistration=?',$idreg);
    	$row = $db->fetchRow($sql);
    	if ($row) {
    		$db->update('tbl_temp_credithours',array('credithours'=>$ch),$idreg);
    	} else {
    		$data=array('credithours'=>$ch,
    					'IdStudentRegistration'=>$idreg
    		
    				);
    		 
    		$db->insert('tbl_temp_credithours',$data);
    	}
    }
    public function getCurrentCH($idreg) {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$sql = $db->select()
    	->from(array('sb' => 'tbl_temp_credithours'))
    	->where('IdStudentRegistration=?',$idreg);
    	$row = $db->fetchRow($sql);
    	if ($row) return $row['credithours']; else return 0;
    }
    
    public function getUnInvoiceRegisteredSubject($idStudentRegistration,$semester_id, $type=null){
    
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	$select =$db->select()
    	->from(array('srs'=>'tbl_studentregsubjects'))
    	->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject = srs.IdSubject')
    	->where('srs.IdStudentRegistration = ?',$idStudentRegistration)
    	->where('srs.invoice < 100')
    	->where('srs.IdSemesterMain = ?',$semester_id);
    		
    	if($type){
    		$select->where('srs.subjectlandscapetype in ('.$type.')');
    	}
    
    	$row = $db->fetchAll($select);
    
    	return $row;
    
    }
    
    public function getUnInvoiceRepeatRegisteredSubject($idStudentRegistration,$semester_id, $type=null){
    
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	$select =$db->select()
    	->from(array('srs'=>'tbl_studentregsubjects'))
    	->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject = srs.IdSubject')
    	->where('srs.IdStudentRegistration = ?',$idStudentRegistration)
    	->where('srs.invoice < 100')
    	->where('srs.IdSemesterMain = ?',$semester_id);
    		
    	if($type){
    		$select->where('srs.subjectlandscapetype in ('.$type.')');
    	}
    
    	$row = $db->fetchAll($select);
    
    	foreach ($row as $key=>$value) {
    		//remove new subjects
    		$select =$db->select()
    		->from(array('srs'=>'tbl_studentregsubjects'))
    		->where('srs.IdStudentRegistration = ?',$value['IdStudentRegistration'])
    		->where('srs.IdSubject = ?',$value['IdSubject'])
    		->where('srs.IdSemesterMain != ?',$value['IdSemesterMain']);
    		$ulang=$db->fetchRow($select);
    		if (!$ulang)
    			unset($row[$key]);
    	}
    	//echo var_dump($row);exit;
    	return $row;
    
    }
    
    public function getRegisteredSubjectByProgram($idprogram,$semester_id){
    
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	$select =$db->select()
    	->from(array('srs'=>'tbl_studentregsubjects'),array())
    	->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject = srs.IdSubject',array('IdSubject','SubCode','SubjectName'=>'BahasaIndonesia'))
    	->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration',array())
    	->where('sr.IdProgram = ?',$idprogram)
    	->where('srs.IdSemesterMain = ?',$semester_id)
    	->group('s.IdSubject');
    		
    	$row = $db->fetchAll($select);
    
    	return $row;
    
    }
}


?>