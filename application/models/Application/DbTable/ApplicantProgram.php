<?php 
class App_Model_Application_DbTable_ApplicantProgram extends Zend_Db_Table_Abstract
{
    protected $_name = 'applicant_program';
	protected $_primary = "ap_id";
	
	
	public function getPlacementProgram($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	      $select = $db ->select()
					->from(array('ap'=>$this->_name))					
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ArabicName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
					//->joinLeft(array('d'=>'tbl_departmentmaster'),'p.idFacucltDepartment=d.IdDepartment')
					->joinLeft(array('prb'=>'appl_program_branch'),'prb.IdProgramBranch=ap.IdProgramBranch',array('kelas'=>'prb.GroupName'))
					->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('faculty'=>'c.ArabicName'))
					->where("ap.ap_at_trans_id  = '".$transaction_id."'")				
					->order("ap.ap_preference Asc");
			//echo $select;		
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
		
	}
	
	
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
	
	public function deleteTransactionData($transaction_id){
		$this->delete('ap_at_trans_id =' . (int)$transaction_id);
	}
	
	
	public function deleteDataProgram($appl_id,$ptest_code){		
	  $this->delete("app_at_trans_id  = '".$appl_id."' AND app_ptest_code = '".$ptest_code."'");
	  
		//echo $sql ="DELETE FROM `trisakti_app`.`applicant_ptest_program` WHERE `applicant_ptest_program`.`app_id` = '$appl_id' AND app_ptest_code = '$ptest_code'";
	}
	
	
	public function getProcedure($transid,$program1,$program2,$scheduleid=1,$testcode=null){
		//echo 'ini program:'.$program1;
	/*	$program1 = "0220";
		$program2 = "0210";
		$location = "1";*/
		$db = Zend_Db_Table::getDefaultAdapter();
			
		//echo "CALL pr_ptest_roomseatno('".$program1."','".$program2."',$scheduleid,@vRoomId, @vSitNo)";
		if ($testcode!=null) {
			//get seat no based on testcode other than S1
			$room=$this->getSeatNo($transid, $program1, $program2, $scheduleid, $testcode);
			return array('0'=>array('roomid'=>$room));
			
		} else {
	     	$stmt = $db->query("CALL pr_ptest_roomseatno($transid,'".$program1."','".$program2."',$scheduleid,@vRoomId, @vSitNo)");	
	
			$stmt;
			
			$select = $db->query("SELECT @vRoomId as roomid,@vSitNo as sitno");	 			
			$row = $select->fetchAll();
			//print_r($row);
			if($row[0]["roomid"]==0){
				$error="no sit no";
				//echo $error;exit;
			}						
			return $row;
		}


	}
	
	
	
	public function getComponentSchedule($transaction_id){
		
		/*
		 * select distinct(ac_comp_name),aps_test_date, ac_start_time, al_location_name
  from applicant_program, appl_placement_weightage,appl_placement_detl,appl_component,
appl_placement_schedule,appl_location 
where ap_at_trans_id = 1
and ap_ptest_prog_id  = apw_app_id
and apw_apd_id =apd_id
and apd_comp_code = ac_comp_code
and aps_placement_code = apd_placement_code
and al_id = aps_location_id
		 */
		
	$db = Zend_Db_Table::getDefaultAdapter();
		
	      $select = $db ->select()
	                ->distinct('c.ac_comp_name_bahasa')
					->from(array('ap'=>$this->_name),array())					
					->joinLeft(array('w'=>'appl_placement_weightage'),'ap.ap_ptest_prog_id  = w.apw_app_id')
					->joinLeft(array('d'=>'appl_placement_detl'),'w.apw_apd_id = d.apd_id')					
					->joinLeft(array('c'=>'appl_component'),'d.apd_comp_code = c.ac_comp_code',array('ac_comp_name'=>'c.ac_comp_name'))					
					->joinLeft(array('s'=>'appl_placement_schedule'),'s.aps_placement_code = d.apd_placement_code',array('aps_test_date'=>'s.aps_test_date','aps_start_time'=>'s.aps_start_time'))
					->joinLeft(array('l'=>'appl_location'),'l.al_id = s.aps_location_id',array('al_location_name'=>'l.al_location_name'))
					->where("ap.ap_at_trans_id  = '".$transaction_id."'")					;		
					
					
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
	}
		
public function getComponentSchedulebytype($transaction_id,$com_type=1,$schedule_id=""){
		
		/*
		 * select distinct(ac_comp_name),aps_test_date, ac_start_time, al_location_name
  from applicant_program, appl_placement_weightage,appl_placement_detl,appl_component,
appl_placement_schedule,appl_location 
where ap_at_trans_id = 1
and ap_ptest_prog_id  = apw_app_id
and apw_apd_id =apd_id
and apd_comp_code = ac_comp_code
and aps_placement_code = apd_placement_code
and al_id = aps_location_id
		 */
		if($com_type==1){
			$where = "c.ac_test_type  = '".$com_type."'";
		}else{
			$where = "c.ac_test_type  <> 1";
		}
	$db = Zend_Db_Table::getDefaultAdapter();
		
	      $select = $db ->select()
	                ->distinct('c.ac_comp_name')
					->from(array('ap'=>$this->_name),array())					
					->joinLeft(array('w'=>'appl_placement_weightage'),'ap.ap_ptest_prog_id  = w.apw_app_id',array())
					->joinLeft(array('d'=>'appl_placement_detl'),'w.apw_apd_id = d.apd_id',array())					
					->joinLeft(array('c'=>'appl_component'),'d.apd_comp_code = c.ac_comp_code',array('ac_comp_name'=>'c.ac_comp_name','ac_comp_name_bahasa'=>'c.ac_comp_name_bahasa','ac_start_time'=>'c.ac_start_time'))					
					->joinLeft(array('s'=>'appl_placement_schedule'),'s.aps_placement_code = d.apd_placement_code',array('aps_test_date'=>'s.aps_test_date','aps_start_time'=>'s.aps_start_time'))
					->joinLeft(array('l'=>'appl_location'),'l.al_id = s.aps_location_id',array('al_location_name'=>'l.al_location_name'),array())
					->where("ap.ap_at_trans_id  = '".$transaction_id."' and s.aps_id = '".$schedule_id."'" )
					->where($where);	
	/*$select = $db ->select()
	                ->distinct('c.ac_comp_name')
					->from(array('ap'=>'applicant_ptest'),array())
					->joinLeft(array('w'=>'appl_placement_weightage'))					
					->joinLeft(array('w'=>'appl_placement_weightage'),'ap.ap_ptest_prog_id  = w.apw_app_id')
					->joinLeft(array('d'=>'appl_placement_detl'),'w.apw_apd_id = d.apd_id')					
					->joinLeft(array('c'=>'appl_component'),'d.apd_comp_code = c.ac_comp_code',array('ac_comp_name'=>'c.ac_comp_name'))					
					->joinLeft(array('s'=>'appl_placement_schedule'),'s.aps_placement_code = d.apd_placement_code',array('aps_test_date'=>'s.aps_test_date','aps_start_time'=>'s.aps_start_time'))
					->joinLeft(array('l'=>'appl_location'),'l.al_id = s.aps_location_id',array('al_location_name'=>'l.al_location_name'))
					->where("ap.ap_at_trans_id  = '".$transaction_id."'")
					->where($where);*/	
	 	//echo $select;exit;
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
        
	}
	public function IsIn($transid,$pref){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('ap'=>$this->_name))
		->where('ap.ap_at_trans_id=?',$transid)
		->where('ap.ap_preference=?',$pref);
		 
		$row = $db->fetchRow($select);
		return $row;
	}
	
	//for high school only dah default type=2 
	public function getApplicantProgram($condition=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	      $select = $db ->select()
					->from(array('ap'=>$this->_name))						
					->joinLeft(array('at'=>'applicant_transaction'),'at.at_trans_id=ap.ap_at_trans_id')	
					->joinLeft(array('af'=> 'applicant_profile'),'af.appl_id=at.at_appl_id')				
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
					->where('at.at_appl_type=2');//high school

					
					if($condition!=null){
						if(isset($condition["program_code"]) && $condition["program_code"]!=''){
							$select->where("ap.ap_prog_code  = '".$condition["program_code"]."'");	
						}
						
						if(isset($condition["academic_year"]) && $condition["academic_year"]!=''){
							$select->where("at.at_academic_year  = '".$condition["academic_year"]."'");	
						}
						
						if(isset($condition["status"]) && $condition["status"]!=''){
							$select->where("at.at_status  = '".$condition["status"]."'");	
						}
						
						if(isset($condition["nationality"]) && $condition["nationality"]!=''){
							if($condition["nationality"]==1){
								$select->where("af.appl_nationality  = '".$condition["nationality"]."'");	
							}else{			
								$select->where("af.appl_nationality  != 1");	
							}
						}else{			
							$select->where("af.appl_nationality  != 1");	
						}
	
					}
		
		$stmt = $db->query($select);
		$row = $stmt->fetchAll();
		
				        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
		
	}
	
	
	public function getApplicantProgramByID($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	      $select = $db ->select()
					->from(array('ap'=>$this->_name))						
					->joinLeft(array('at'=>'applicant_transaction'),'at.at_trans_id=ap.ap_at_trans_id')	
					->joinLeft(array('af'=> 'applicant_profile'),'af.appl_id=at.at_appl_id')				
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
					->where("at.at_trans_id='".$transaction_id."'");//high school
		
		$select->order('ap.ap_preference asc');
					
		$stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
	}
	
	public function getProgramFaculty($transaction_id,$appl_type){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	      $select = $db ->select()
					->from(array('ap'=>$this->_name))					
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
					->joinLeft(array('d'=>'appl_program_branch'),'ap.IdBranchProgramOffer=d.IdProgramBranch')
					->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('faculty'=>'c.CollegeName','faculty2'=>'c.ArabicName','faculty_id'=>'c.IdCollege'))
					->where("ap.ap_at_trans_id  = '".$transaction_id."'")				
					->order("ap.ap_preference Asc");
	      if(isset($appl_type) && ($appl_type==1 || $appl_type==4 || $appl_type==7 || $appl_type==5)){
	      	$select->where("ap.ap_usm_status  = 1");
	      }
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
		
	}
	
	public function getTotalStudent($program_code,$periode,$status){
						
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$selectData = $db->select()
						->from(array('ap'=>$this->_name))						
						->joinLeft(array('at'=>'applicant_transaction'),'at.at_trans_id=ap.ap_at_trans_id')
						->where("at_status='".$status."'")
						->where("at_period= '". $periode."'")
						->where("ap_prog_code = '".$program_code."'");
						
		$stmt = $db->query($selectData);
        $row = $stmt->fetchAll();		
		return count($row);	
	}
	
	
	public function getTotalStudentByFaculty($faculty_id,$periode,$status){
						
		$db = Zend_Db_Table::getDefaultAdapter();
			
		 $selectData = $db->select()
						->from(array('ap'=>$this->_name))						
						->joinLeft(array('at'=>'applicant_transaction'),'at.at_trans_id=ap.ap_at_trans_id')
						->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
						->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('faculty'=>'c.CollegeName'))
						->where("at.at_status='".$status."'")
						->where("at.at_period= '". $periode."'")
						->where("c.IdCollege = '".$faculty_id."'");
						
		$stmt = $db->query($selectData);
        $row = $stmt->fetchAll();		
		return count($row);	
	}
	
	public function getProgram($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	      $select = $db ->select()
					->from(array('ap'=>$this->_name))					
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
				
					->where("ap.ap_at_trans_id  = '".$transaction_id."'")				
					->order("ap.ap_preference Asc");
					
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
	}
	
	
	public function getData($id=""){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from(array('ap'=>$this->_name))
					  ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code');
					  
		if($id)	{			
			 $select->where("ap_id ='".$id."'");
			 $row = $db->fetchRow($select);	
			 
		}	else{			
			$row = $db->fetchAll($select);	
		}	  
		
		 return $row;
	}
	
	public function getUsmOfferProgram($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	      $select = $db ->select()
					->from(array('ap'=>$this->_name))					
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))				
					->joinLeft(array('d'=>'appl_program_branch'),'ap.IdBranchProgramOffer=d.IdProgramBranch')
					->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('faculty'=>'c.CollegeName','faculty2'=>'c.ArabicName', 'faculty_id'=>'IdCollege'))
					->where("ap.ap_at_trans_id  = '".$transaction_id."'")
					->where("ap.ap_usm_status  = 1")				
					->order("ap.ap_preference Asc");
				
         $row = $db->fetchRow($select);	
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
		
	}
	
	public function getProgramPreference($transaction_id,$preference){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	      $select = $db ->select()
					->from(array('ap'=>$this->_name))					
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
				
					->where("ap.ap_at_trans_id  = '".$transaction_id."'")	
					->where("ap.ap_preference  = '".$preference."'")				
					->order("ap.ap_preference Asc");
					
         $row = $db->fetchRow($select);	
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
	}
	
	public function getProgramAssessment($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	      $select = $db ->select()
					->from(array('ap'=>$this->_name))					
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
				    ->joinLeft(array('aau'=>'applicant_assessment_usm'),'aau.aau_ap_id=ap.ap_id',array('aau.aau_rector_ranking','aau.aau_rector_status'))
					->where("ap.ap_at_trans_id  = '".$transaction_id."'")	
					->order("ap.ap_preference Asc");
					
         $row = $db->fetchAll($select);	
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
	}
	
	public function updateProgramPtest($data,$txn_id,$program_code,$preference){	
		
		 $this->update($data,"ap_at_trans_id ='".$txn_id."' AND ap_prog_code = '".$program_code."' AND ap_preference='".$preference."'");
	}
	
public function getProgramOffered($transaction_id,$appl_type=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	    $select = $db ->select()
					->from(array('ap'=>$this->_name))					
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
					->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('faculty'=>'c.CollegeName','faculty_indonesia'=>'c.ArabicName', 'faculty_id'=>'IdCollege'))
					->where("ap.ap_at_trans_id  = '".$transaction_id."'");			
		
				    //jika usm kene check program mana yg offer sebab usm ada 2 pilihan
				    if(isset($appl_type) && ($appl_type==1 || $appl_type==7 || $appl_type==4 || $appl_type==3 || $appl_type==5 || $appl_type==6)){
						$select->where("ap.ap_usm_status  = 1");	
					}
					
        $row = $db->fetchRow($select);
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
	}
	
	
	public function getChangeProgram($txnId){
						
		$db = Zend_Db_Table::getDefaultAdapter();
			
		 $select = $db->select()											
					  ->from(array('at'=>'applicant_transaction'))
					  ->where("at.at_trans_id= '".$txnId."'");
		
        $rows = $db->fetchRow($select);	
        
        
        $select_program = $db ->select()
		          			  ->from(array('ap'=>$this->_name))					
		                      ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code',array('IdProgram','ProgramName','ArabicName','ProgramCode'))
		                      ->where("ap.ap_at_trans_id ='".$txnId."'");
								
	    //if($rows["at_appl_type"]==1){
			$select_program->where("ap.ap_usm_status  = 1");	
		//}
		$program = $db->fetchRow($select_program);			
        
        return array($rows,$program);
		
	}
	
	
	
	public function getSeatNo($vAppTransId,$vProgramCode1,$vProgramCode2,$vScheduleId,$vTestCode) {
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		/*$vRoomType;
		$vRoomNameShort;
		$vExamnoFlag ;
		$vExamCapacity;
		$vExamno_f;
		$vExamno_l;
		$vExamNo;
		$vProgRoomType1;
		$vProgRoomType2;
		$cntRoomExist;
		$vApss_id ;
		$vlocation ;
		*/
		$select = $db ->select()
		->from(array('a'=>'tbl_program'))
		->where('ProgramCode=?',$vProgramCode1);
		$row=$db->fetchRow($select);
		$vProgRoomType1=$row['ptest_room_type'];
		 
		$select = $db ->select()
		->from(array('a'=>'tbl_program'))
		->where('ProgramCode=?',$vProgramCode2);
		$row=$db->fetchRow($select);
		if ($row)
			$vProgRoomType2=$row['ptest_room_type'];
		else $vProgRoomType2=0;
		
		$select = $db ->select()
		->from(array('a'=>'appl_room_assign'))
		->where('ara_program1=?',$vProgRoomType1)
		->where('ara_program2=?',$vProgRoomType2)
		->where('test_code=?',$vTestCode);
		$row=$db->fetchRow($select);
		//echo $select;exit;
		$vRoomType=$row['ara_room'];
		
		$select = $db ->select()
		->from(array('a'=>'appl_placement_schedule'))
		->join(array('b'=>'appl_placement_schedule_seatno'),'a.aps_id = b.apss_aps_id')
		->join(array('c'=>'appl_room'),'b.apss_room_id = c.av_id')
		->join(array('d'=>'appl_room_type'),'d.art_room_id = c.av_id')
		->where('a.aps_id=?',$vScheduleId)
		->where('art_test_type=?',$vRoomType)
		->where('aps_placement_code=?',$vTestCode)
		->where('apss_exam_apply<apss_exam_capasity');
		//echo $select;exit;
		$row=$db->fetchRow($select);
		 
		if (!$row) {
				
			$select = $db ->select()
				->from(array('a'=>'appl_placement_schedule'))
				->where('aps_id=?',$vScheduleId);
			$row=$db->fetchRow($select);
			if (!$row) {
					$vRoomId = 0;
					return 0;
			}
			$vlocation=$row['aps_location_id'];
			
					
			$smtin = $db ->select()
			->from(array('a'=>'appl_placement_schedule'),array())
			->join(array('b'=>'appl_placement_schedule_seatno'),'a.aps_id = b.apss_aps_id and b.apss_exam_apply=b.apss_exam_capasity',array('apss_room_id'))
			//->join(array('c'=>''))
			->where('aps_id=?',$vScheduleId);
			
			$select = $db ->select()
			->from(array('a'=>'appl_room'),array('av_id','av_room_name_short', 'av_exam_capacity'))
			->join(array('b'=>'appl_room_type'),'b.art_room_id = a.av_id')
			->where('av_location_code=?',$vlocation)
			->where('av_exam_capacity is not null')
			->where('av_status=1',$vTestCode)
			->where('art_test_type=?',$vRoomType)
			->where('av_id not in ('.$smtin.')')
			->order('av_seq');
			//echo $select;exit;
			$row=$db->fetchRow($select);
			if (!row) return 0;
			
			$vRoomId=$row['av_id'];
			$vRoomNameShort=$row['av_room_name_short'];
			$vExamCapacity=$row['av_exam_capacity'];
			if ($vExamCapacity==null) $vExamCapacity=0;
			$vExamnoFlag='F';
			$vExamno_f= 0;
			$vExamno_l=0;
			 
			$vExamNo = $vExamno_f + 1;
			$data=array('apss_room_id'=>$vRoomId,
					 	'apss_aps_id'=>$vScheduleId,
					 	'apss_exam_capasity'=>vExamCapacity,
						'apss_exam_apply'=>1,		
						'apss_examno_flag'=>'L', 
						'apss_examno_f'=>1, 
						'apss_examno_l'=>0
			);
			
			$db->insert('appl_placement_schedule_seatno',$data);
			$vSeatNo = $vRoomNameShort.'-'.substr(vExamNo+1000,1,3);
			
			$db->update('applicant_ptest',array('apt_sit_no'=>$vSeatNo,'apt_room_id'=>$vRoomId),'apt_at_trans_id='.$vAppTransId);
			 
			} else {
		
				$vApss_id=$row['apss_id'];
				$vRoomId=$row['apss_room_id'];
				$vRoomNameShort=$row['av_room_name_short'];
				$vExamCapacity=$row['apss_exam_capasity'];
				$vExamnoFlag=$row['apss_examno_flag']; 
				$vExamno_f=$row['apss_examno_f'];
				$vExamno_l=$row['apss_examno_l'];
				$apss_exam_apply=$row['apss_exam_apply']+1;
				if ($vExamnoFlag=='F') {
					$vExamNo = $vExamno_f + 1;
					$data=array('apss_examno_f'=>$vExamNo,'apss_exam_apply'=>$apss_exam_apply+1,'apss_examno_flag'=>'L');
					$db->update('appl_placement_schedule_seatno',$data,'apss_id='.$vApss_id);
				}
				else 
				{
					if ($vExamno_l==0) $vExamNo=$vExamCapacity;
					else $vExamNo=$vExamNo-1;
					$data=array('apss_examno_l'=>$vExamNo,'apss_exam_apply'=>$apss_exam_apply+1,'apss_examno_flag'=>'F');
					$db->update('appl_placement_schedule_seatno',$data,'apss_id='.$vApss_id);
					
				}
				$vSeatNo = $vRoomNameShort.'-'.substr($vExamNo+1000,1,3);
				$db->update('applicant_ptest',array('apt_sit_no'=>$vSeatNo,'apt_room_id'=>$vRoomId),'apt_at_trans_id ='.$vAppTransId);
				return $vRoomId;
			}
		  
	
	}
	
}
 
?>