<?php 

class App_Model_Application_DbTable_ApplicantProfilePropose extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_profile_propose';
	protected $_primary = "id";
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, 'appl_id = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	
	
	
	
	public function uniqueEmail($email){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("appl_email = ?", $email);

		$row = $db->fetchRow($select);	
		 
		if($row){
		 	return false;
		}else{
			return true;	
		}
	}
	

	
	public function verifyData($username,$password){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("appl_email = ?", $username)
					  ->where("appl_password = ?", $password);
					  
		 $row = $db->fetchRow($select);	
		 return $row;
	}
	
	public function getForgotPasswordData($email,$dob){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("appl_email = ?", $email)
					  ->where("appl_dob = ?", $dob);
					  
		 $row = $db->fetchRow($select);	
		 
		 if($row){
		 	return $row;
		 }else{
		 	return null;	
		 }
		 
	}
	
	public function getData($id=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name);
					  
		if($id!=null)	{			
			 $select->where("appl_id ='".$id."'");
			 $row = $db->fetchRow($select);	
			 
		}	else{			
			$row = $db->fetchAll($select);	
		}	  
		
		 return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db ->select()
					  ->from(array('ap'=>$this->_name))
					  ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id')
					  ->joinleft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id',(array('education'=>'ae.ae_discipline_code')))
					  ->joinleft(array('apt'=>'applicant_ptest'),'apt.apt_at_trans_id=at.at_trans_id',(array('fee'=>'apt.apt_fee_amt','bill_no'=>'apt.apt_bill_no','currency'=>'apt.apt_currency','schedule_id'=>'apt.apt_aps_id')));
						
		return $select;
	}
	
	
	public function getProfile ($id=""){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db ->select()
					  ->from(array('ap'=>$this->_name))
					  ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id',array('admission_type'=>'at_appl_type'))
					  ->joinleft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id',(array('education'=>'ae.ae_discipline_code')))
					  ->joinleft(array('sd'=>'school_discipline'),'sd.smd_code=ae.ae_discipline_code',array('discipline'=>'sd.smd_desc'))
					  ->joinleft(array('apt'=>'applicant_ptest'),'apt.apt_at_trans_id=at.at_trans_id',(array('fee'=>'apt.apt_fee_amt','bill_no'=>'apt.apt_bill_no','currency'=>'apt.apt_currency','schedule_id'=>'apt.apt_aps_id')))
					  ->where("at.at_status = 'APPLY'")
					  ->where("ap.appl_id ='".$id."'");
					  //echo $select."<hr>";
		$row = $db->fetchRow($select);	
		return $row;
	}
	
	public function getProfileAll ($id=""){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('ap'=>$this->_name))
		->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id',array('admission_type'=>'at_appl_type'))
		->joinleft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id',(array('education'=>'ae.ae_discipline_code')))
		->joinleft(array('sd'=>'school_discipline'),'sd.smd_code=ae.ae_discipline_code',array('discipline'=>'sd.smd_desc'))
		->joinleft(array('apt'=>'applicant_ptest'),'apt.apt_at_trans_id=at.at_trans_id',(array('fee'=>'apt.apt_fee_amt','bill_no'=>'apt.apt_bill_no','currency'=>'apt.apt_currency','schedule_id'=>'apt.apt_aps_id')))
		 
		->where("ap.appl_id ='".$id."'");
		//echo $select."<hr>";
		$row = $db->fetchRow($select);
		return $row;
	}

	public function getTransProfile ($id="",$transid=""){
	 
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db ->select()
					  ->from(array('ap'=>$this->_name))
					  ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id AND at.at_trans_id='.$transid,array('admission_type'=>'at_appl_type'))
					  ->joinleft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id',(array('education'=>'ae.ae_discipline_code')))
					  ->joinleft(array('sd'=>'school_discipline'),'sd.smd_code=ae.ae_discipline_code',array('discipline'=>'sd.smd_desc'))
					  ->joinleft(array('apt'=>'applicant_ptest'),'apt.apt_at_trans_id=at.at_trans_id',(array('fee'=>'apt.apt_fee_amt','bill_no'=>'apt.apt_bill_no','currency'=>'apt.apt_currency','schedule_id'=>'apt.apt_aps_id')))
					 // ->where("at.at_status = 'APPLY'")
					  ->where("ap.appl_id ='".$id."'");
		// echo $select;			  
		// die;
        $row = $db->fetchRow($select);	
		return $row;
	}	
	
	public function verify($transaction_id,$billing_no,$pin_no){
		
		    $db = Zend_Db_Table::getDefaultAdapter();
		
	 $select = $db ->select()
					  ->from(array('ap'=>$this->_name))	
					  ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id')				 
					  ->joinleft(array('apt'=>'applicant_ptest'),'apt.apt_at_trans_id=at.at_trans_id')	
					  ->joinLeft(array('apb'=>'appl_pin_to_bank'),'apb.billing_no=apt.apt_bill_no')				  
					  ->where("at.at_trans_id ='".$transaction_id."'")
					  ->where("apt.apt_bill_no = '".$billing_no."'")
				      ->where("apb.REGISTER_NO = '".$pin_no."'");	//	entry yg belum pakai								

        $row = $db->fetchRow($select);
		return $row;
	}
	public function getProfileByTransaction($id=""){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->_db->select()
		->from(array('ap'=>$this->_name))
		->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id',array('admission_type'=>'at_appl_type','at_pes_id','at_intake'))
		->joinleft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id',(array('education'=>'ae.ae_discipline_code')))
		->where("at.at_trans_id ='".$id."'");
		$row = $this->_db->fetchRow($select);
		return $row;
	}
	
	public function viewkartu($transaction_id){
		
		    $db = Zend_Db_Table::getDefaultAdapter();
		
			 $select = $db ->select()
					  ->from(array('ap'=>$this->_name))	
					  ->join(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id')				 
					  ->joinleft(array('apt'=>'applicant_ptest'),'apt.apt_at_trans_id=at.at_trans_id')	
					  ->joinLeft(array('apb'=>'appl_pin_to_bank'),'apb.billing_no=apt.apt_bill_no')				  
					  ->where("at.at_trans_id ='".$transaction_id."'");	//	entry yg belum pakai								
       //echo $select;exit;
        $row = $db->fetchRow($select);
		return $row;
	}	
	
	public function getAllProfile ($id=""){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db ->select()
					  ->from(array('ap'=>$this->_name))
					  ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id',array('admission_type'=>'at_appl_type','applicantID'=>'at_pes_id'))
					  ->joinleft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id',(array('education'=>'ae.ae_discipline_code')))
					  ->joinleft(array('apt'=>'applicant_ptest'),'apt.apt_at_trans_id=at.at_trans_id',(array('fee'=>'apt.apt_fee_amt','bill_no'=>'apt.apt_bill_no','currency'=>'apt.apt_currency','schedule_id'=>'apt.apt_aps_id')))	
					  ->joinleft(array('p'=>'tbl_city'),'p.idCity=ap.appl_city',array('CityName'=>'p.CityName'))
					  ->joinleft(array('s'=>'tbl_state'),'s.idState=ap.appl_state',array('StateName'=>'s.StateName'))					  			
					  ->where("at.at_trans_id ='".$id."'");
		$row = $db->fetchRow($select);	
		return $row;
	}
	
	public function getPaginateDatabyProgram($condition=null){
		
		 $select = $this->_db->select()
					   ->from(array('ap'=>$this->_name))
					   ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id',array('transaction_id'=>'at.at_trans_id','applicantID'=>'at.at_pes_id','submit_date'=>'at.at_submit_date','status'=>'at.at_status'))
					   ->joinleft(array('apr'=>'applicant_program'),'apr.ap_at_trans_id=at.at_trans_id')
					   ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=apr.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
					   ->joinLeft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id')
					   ->joinLeft(array('sm'=>'school_master'),'sm.sm_id=ae.ae_institution',array('school'=>'sm.sm_name'));
					   
					   if($condition!=null){
					   		if($condition["program_code"]!=''){
					   			$select->where("apr.ap_prog_code ='".$condition["program_code"]."'");
					   		}
					  		if($condition["admission_type"]!=''){
					   			$select->where("at.at_appl_type ='".$condition["admission_type"]."'");
					   		}
					   		if(isset($condition["status"]) && $condition["status"]!=''){
								$select->where("at.at_status  = '".$condition["status"]."'");	
							}
					   }
					   
		//echo $select;			  
		return $select;
	}
	
	public function getDatabyProgram($condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db ->select()
					   ->from(array('ap'=>$this->_name))
					   ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id',array('transaction_id'=>'at.at_trans_id','applicantID'=>'at.at_pes_id','submit_date'=>'at.at_submit_date','status'=>'at.at_status'))
					   ->joinleft(array('apr'=>'applicant_program'),'apr.ap_at_trans_id=at.at_trans_id')
					   ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=apr.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
					   ->joinLeft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id')
					   ->joinLeft(array('sm'=>'tbl_schoolmaster'),'sm.idSchool=ae.ae_institution',array('school'=>'sm.SchoolName'));
					   
					   if($condition!=null){
					   		if(isset($condition["program_code"]) && $condition["program_code"]!=''){
					   			$select->where("apr.ap_prog_code ='".$condition["program_code"]."'");
					   		}
					   		if(isset($condition["admission_type"]) && $condition["admission_type"]!=''){
					   			$select->where("at.at_appl_type ='".$condition["admission_type"]."'");
					   		}
					  		if(isset($condition["transaction_id"]) && $condition["transaction_id"]!=''){
					   			$select->where("at.at_trans_id ='".$condition["transaction_id"]."'");
					  		}
					  		
					   		if(isset($condition["academic_year"]) && $condition["academic_year"]!=''){
					   			$select->where("at.at_academic_year ='".$condition["academic_year"]."'");
					  		}
					  		
					  		if(isset($condition["status"]) && $condition["status"]!=''){
								$select->where("at.at_status  = '".$condition["status"]."'");
					  		}
					   		if(isset($condition["period"]) && $condition["period"]!=''){	
					   			$period = explode('/',$condition["period"]);
					   							   		
								$select->where("MONTH(at.at_submit_date) = '".$period[0]."'");
								$select->where("YEAR(at.at_submit_date) = '".$period[1]."'");
					  		}
					   }
					   
		
		// echo $select;
		 
		if(isset($condition["transaction_id"]) && $condition["transaction_id"]!=''){
			$row = $db->fetchRow($select);
		}else{		   
			$row = $db->fetchAll($select);		
		}		  
		return $row;
	}
	
	
	public function getDeanSelection($condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db ->select()
					   ->from(array('ap'=>$this->_name))
					   ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id',array('transaction_id'=>'at.at_trans_id','applicantID'=>'at.at_pes_id','submit_date'=>'at.at_submit_date','status'=>'at.at_status'))
					   ->joinleft(array('apr'=>'applicant_program'),'apr.ap_at_trans_id=at.at_trans_id')
					   ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=apr.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
					   ->joinLeft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id')
					   ->joinLeft(array('sm'=>'tbl_schoolmaster'),'sm.idSchool=ae.ae_institution',array('school'=>'sm.SchoolName'))
					   ->where("at.at_selection_status = 0");
					  
					  
					   
					   if($condition!=null){
					   	
					   		if(isset($condition["faculty_id"]) && $condition["faculty_id"]!=''){
					   			$select->where("p.IdCollege ='".$condition["faculty_id"]."'");
					   		}
					   		if(isset($condition["program_code"]) && $condition["program_code"]!=''){
					   			$select->where("apr.ap_prog_code ='".$condition["program_code"]."'");
					   		}
					   		if(isset($condition["admission_type"]) && $condition["admission_type"]!=''){
					   			$select->where("at.at_appl_type ='".$condition["admission_type"]."'");
					   		}
					  		if(isset($condition["transaction_id"]) && $condition["transaction_id"]!=''){
					   			$select->where("at.at_trans_id ='".$condition["transaction_id"]."'");
					  		}
					  		
					   		if(isset($condition["academic_year"]) && $condition["academic_year"]!=''){
					   			$select->where("at.at_academic_year ='".$condition["academic_year"]."'");
					  		}
					  		
					  		if(isset($condition["status"]) && $condition["status"]!=''){
								$select->where("at.at_status  = '".$condition["status"]."'");
					  		}					   	
					   		if(isset($condition["period"]) && $condition["period"]!=''){	
					   			$select->where("at_period = '".$condition["period"]."'");
					  		}
					  		
					  	   
					   }
					   
		
		
		 
		if(isset($condition["transaction_id"]) && $condition["transaction_id"]!=''){
			$row = $db->fetchRow($select);
		}else{		   
			$row = $db->fetchAll($select);		
		}		  
		return $row;
	}
	
	
	public function getRectorSelection($condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db ->select()
					   ->from(array('ap'=>$this->_name))
					   ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id',array('transaction_id'=>'at.at_trans_id','applicantID'=>'at.at_pes_id','submit_date'=>'at.at_submit_date'))
					   ->joinleft(array('apr'=>'applicant_program'),'apr.ap_at_trans_id=at.at_trans_id')
					   ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=apr.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
					   ->joinLeft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id')
					   ->joinLeft(array('sm'=>'tbl_schoolmaster'),'sm.idSchool=ae.ae_institution',array('school'=>'sm.SchoolName'))
					   ->where("at.at_selection_status = 1");
					   
					   if($condition!=null){
					  		if(isset($condition["faculty_id"]) && $condition["faculty_id"]!=''){
					   			$select->where("p.IdCollege ='".$condition["faculty_id"]."'");
					   		}
					   		if(isset($condition["program_code"]) && $condition["program_code"]!=''){
					   			$select->where("apr.ap_prog_code ='".$condition["program_code"]."'");
					   		}
					   		if(isset($condition["admission_type"]) && $condition["admission_type"]!=''){
					   			$select->where("at.at_appl_type ='".$condition["admission_type"]."'");
					   		}
					  		if(isset($condition["transaction_id"]) && $condition["transaction_id"]!=''){
					   			$select->where("at.at_trans_id ='".$condition["transaction_id"]."'");
					  		}					  		
					   		if(isset($condition["academic_year"]) && $condition["academic_year"]!=''){
					   			$select->where("at.at_academic_year ='".$condition["academic_year"]."'");
					  		}
					  		
					  		if(isset($condition["status"]) && $condition["status"]!=''){
								$select->where("at.at_status  = '".$condition["status"]."'");
					  		}					   	
					  	 	if(isset($condition["period"]) && $condition["period"]!=''){	
					   			$select->where("at_period = '".$condition["period"]."'");
					  		}
					  		
					   }
					   
					   
		//echo $select; 
		if(isset($condition["transaction_id"]) && $condition["transaction_id"]!=''){
			$row = $db->fetchRow($select);
		}else{		   
			$row = $db->fetchAll($select);		
		}		  
		return $row;
	}
	
	
	public function getApprovalSelection($condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db ->select()
					   ->from(array('ap'=>$this->_name))
					   ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id',array('transaction_id'=>'at.at_trans_id','applicantID'=>'at.at_pes_id','submit_date'=>'at.at_submit_date'))
					   ->joinleft(array('apr'=>'applicant_program'),'apr.ap_at_trans_id=at.at_trans_id')
					   ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=apr.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
					   ->joinLeft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id')
					   ->joinLeft(array('sm'=>'tbl_schoolmaster'),'sm.idSchool=ae.ae_institution',array('school'=>'sm.SchoolName'))
					   ->where("at.at_selection_status = 2");
					   
					   if($condition!=null){
					   		if(isset($condition["faculty_id"]) && $condition["faculty_id"]!=''){
					   			$select->where("p.IdCollege ='".$condition["faculty_id"]."'");
					   		}
					   		if(isset($condition["program_code"]) && $condition["program_code"]!=''){
					   			$select->where("apr.ap_prog_code ='".$condition["program_code"]."'");
					   		}
					   		if(isset($condition["admission_type"]) && $condition["admission_type"]!=''){
					   			$select->where("at.at_appl_type ='".$condition["admission_type"]."'");
					   		}
					  		if(isset($condition["transaction_id"]) && $condition["transaction_id"]!=''){
					   			$select->where("at.at_trans_id ='".$condition["transaction_id"]."'");
					  		}
					  		
					   		if(isset($condition["academic_year"]) && $condition["academic_year"]!=''){
					   			$select->where("at.at_academic_year ='".$condition["academic_year"]."'");
					  		}
					  		
					  		if(isset($condition["status"]) && $condition["status"]!=''){
								$select->where("at.at_status  = '".$condition["status"]."'");
					  		}
					   		
					       if(isset($condition["period"]) && $condition["period"]!=''){	
					   			$select->where("at_period = '".$condition["period"]."'");
					  		}
					  		
					  	   
					   }
					   
		
		
	  //echo $select;
		if(isset($condition["transaction_id"]) && $condition["transaction_id"]!=''){
			$row = $db->fetchRow($select);
		}else{		   
			$row = $db->fetchAll($select);		
		}		  
		return $row;
	}
	
	
	public function getResultSelection($condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db ->select()
					   ->from(array('ap'=>$this->_name))
					   ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id',array('transaction_id'=>'at.at_trans_id','applicantID'=>'at.at_pes_id','submit_date'=>'at.at_submit_date'))
					   ->joinleft(array('apr'=>'applicant_program'),'apr.ap_at_trans_id=at.at_trans_id')
					   ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=apr.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
					   ->joinLeft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id')
					   ->joinLeft(array('sm'=>'tbl_schoolmaster'),'sm.idSchool=ae.ae_institution',array('school'=>'sm.SchoolName'))
					   ->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('faculty'=>'c.ArabicName'))
					   ->where("at.at_selection_status = 3");
					   
					   if($condition!=null){
					   		if(isset($condition["program_code"]) && $condition["program_code"]!=''){
					   			$select->where("apr.ap_prog_code ='".$condition["program_code"]."'");
					   		}
					   		if(isset($condition["admission_type"]) && $condition["admission_type"]!=''){
					   			$select->where("at.at_appl_type ='".$condition["admission_type"]."'");
					   		}		
					   		if(isset($condition["academic_year"]) && $condition["academic_year"]!=''){
					   			$select->where("at.at_academic_year ='".$condition["academic_year"]."'");
					  		}					  		
					  		if(isset($condition["status"]) && $condition["status"]!=''){
								$select->where("at.at_status  = '".$condition["status"]."'");
					  		}
					   		/*if(isset($condition["period"]) && $condition["period"]!=''){	
					   			$period = explode('/',$condition["period"]);
					   							   		
								$select->where("MONTH(at.at_submit_date) = '".$period[0]."'");
								$select->where("YEAR(at.at_submit_date) = '".$period[1]."'");
					  		}*/
					  		if(isset($condition["period"]) && $condition["period"]!=''){	
					   			$select->where("at_period = '".$condition["period"]."'");
					  		}
					  		
					  	   
					   }
			   
		$row = $db->fetchAll($select);	  
		return $row;
	}
	
	public function getAgentPaginateData($condition=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db ->select()
					  ->from(array('ap'=>$this->_name))
					  ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id')
					  ->joinleft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id',(array('education'=>'ae.ae_discipline_code')))
					  ->joinleft(array('apt'=>'applicant_ptest'),'apt.apt_at_trans_id=at.at_trans_id',(array('fee'=>'apt.apt_fee_amt','bill_no'=>'apt.apt_bill_no','currency'=>'apt.apt_currency','schedule_id'=>'apt.apt_aps_id')))
					    ->order("at.at_create_date DESC");
					  
	  					if($condition!=null){
					   		if(isset($condition["agent_id"]) && $condition["agent_id"]!=''){
					   			$select->where("at.agent_id ='".$condition["agent_id"]."'");
					   		}
	  						if(isset($condition["entry_type"]) && $condition["entry_type"]!=''){
					   			$select->where("at.entry_type ='".$condition["entry_type"]."'");
					   		}
					   		
					  	   
					   }			 
						
		return $select;
	}
	
	public function getAgentData($condition=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db ->select()
					  ->from(array('ap'=>$this->_name))
					  ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id')
					  ->joinleft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id',(array('education'=>'ae.ae_discipline_code')))
					  ->joinleft(array('apt'=>'applicant_ptest'),'apt.apt_at_trans_id=at.at_trans_id',(array('fee'=>'apt.apt_fee_amt','bill_no'=>'apt.apt_bill_no','currency'=>'apt.apt_currency','schedule_id'=>'apt.apt_aps_id')))
					  ->order("at.at_create_date DESC");
					  
					  
	  					if($condition!=null){
					   		if(isset($condition["agent_id"]) && $condition["agent_id"]!=''){
					   			$select->where("at.agent_id ='".$condition["agent_id"]."'");
					   		}
	  						if(isset($condition["entry_type"]) && $condition["entry_type"]!=''){
					   			$select->where("at.entry_type ='".$condition["entry_type"]."'");
					   		}
					   		
					   }			 
						
		return $row = $db->fetchAll($select);	
	}
	
	
	public function getStatusSelection($condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db ->select()
					   ->from(array('ap'=>$this->_name))
					   ->joinleft(array('at'=>'applicant_transaction'),'at.at_appl_id=ap.appl_id',array('transaction_id'=>'at.at_trans_id','applicantID'=>'at.at_pes_id','submit_date'=>'at.at_submit_date','selection_status'=>'at.at_selection_status','status'=>'at.at_status'))
					   ->joinleft(array('apr'=>'applicant_program'),'apr.ap_at_trans_id=at.at_trans_id')
					   ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=apr.ap_prog_code',array('program_id'=>'p.IdProgram','program_name'=>'p.ProgramName','program_name_indonesia'=>'p.ArabicName','program_code'=>'p.ProgramCode'))
					   ->joinLeft(array('ae'=>'applicant_education'),'ae.ae_appl_id=ap.appl_id')
					   ->joinLeft(array('sm'=>'tbl_schoolmaster'),'sm.idSchool=ae.ae_institution',array('school'=>'sm.SchoolName'))
					   ->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('faculty'=>'c.ArabicName'))
					   ->order("at.at_pes_id DESC");
					  
					   
					   if($condition!=null){
					   		if(isset($condition["faculty"]) && $condition["faculty"]!=''){
					   			$select->where("p.IdCollege ='".$condition["faculty"]."'");
					   		}
					   		if(isset($condition["program_code"]) && $condition["program_code"]!=''){
					   			$select->where("apr.ap_prog_code ='".$condition["program_code"]."'");
					   		}
					   		if(isset($condition["admission_type"]) && $condition["admission_type"]!=''){
					   			$select->where("at.at_appl_type ='".$condition["admission_type"]."'");
					   		}		
					   		if(isset($condition["academic_year"]) && $condition["academic_year"]!=''){
					   			$select->where("at.at_academic_year ='".$condition["academic_year"]."'");
					  		}					  		
					  		
					  		if(isset($condition["period"]) && $condition["period"]!=''){	
					   			$select->where("at_period = '".$condition["period"]."'");
					  		}					  		
					  		if(isset($condition["selection_status"]) && $condition["selection_status"]!=''){
								$select->where("at.at_selection_status  = '".$condition["selection_status"]."'");
					  		}
					   	
					  		
					  	   
					   }
		//echo $select;	   
		$row = $db->fetchAll($select);	  
		return $row;
	}
	
	
	
	public function getSameEmail($email){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("appl_email = ?", $email);

		$row = $db->fetchAll($select);	
		
		return $row;
	}
	
	public function getDataVerify($id=""){
        
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
        ->from($this->_name);
        
		if($id)	{			
            $select->where("verifyKey ='".$id."'");
            $row = $db->fetchRow($select);	
            
		}	  
		
        return $row;
	}
	
}
?>