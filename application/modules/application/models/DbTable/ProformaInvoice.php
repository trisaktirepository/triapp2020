<?php

class Application_Model_DbTable_ProformaInvoice extends Zend_Db_Table {

	protected $_name = 'applicant_proforma_invoice';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
  	}

	public function getData($id=""){
		
		$db = $this->lobjDbAdpt;
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->order("id desc");
					  
		if($id)	{			
			 $select->where("id ='".$id."'");
			 $row = $db->fetchRow($select);				 
		}else{
			 $row = $db->fetchAll($select);			
		}	 
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}
	
	public function getDataDecree($nomor,$type="PSSB"){
		
		$db = $this->lobjDbAdpt;
		
		if($type=="PSSB"){
			//get transaction from nomor
			$arr_txn = $this->getDecreeApplicantList($nomor,$type);
			
			$txn = array();
			for($i=0; $i<sizeof($arr_txn); $i++){
				$txn[] = $arr_txn[$i]['at_trans_id'];
			}
			
			$txn = implode(",", $txn);
	
			//get proforma based on transaction
			$select = $db ->select()
						  ->from($this->_name)
						  ->where("txn_id in (".$txn.")")
						  ->order("id asc");
	
			$row = $db->fetchAll($select);
		}else	
		if($type=="USM"){
			//get transaction from nomor
			$arr_txn = $this->getDecreeApplicantList($nomor,$type);
			
			$txn = array();
			for($i=0; $i<sizeof($arr_txn); $i++){
				$txn[] = $arr_txn[$i]['at_trans_id'];
			}
			
			$txn = implode(",", $txn);
	
			//get proforma based on transaction
			$select = $db ->select()
						  ->from($this->_name)
						  ->where("txn_id in (".$txn.")")
						  ->order("id asc");

			$row = $db->fetchAll($select);
		}		
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}
	
	public function getDataDate($date){
		
		$db = $this->lobjDbAdpt;
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("offer_date = '".date('Y-m-d',strtotime($date))."'")
					  ->order("id asc");		  
		
		$row = $db->fetchAll($select);			
			 
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}
	
	public function getDataRange($from,$to){
		
		$db = $this->lobjDbAdpt;
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where('offer_date between '.$from.' and '. $to)
					  ->order("id asc");
					  
		
		$row = $db->fetchAll($select);			
			 
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}
	
	public function getTxnData($txnId=0){
		
		$db = $this->lobjDbAdpt;
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->order("id ASC");
					  
		if($txnId!=0)	{			
			 $select->where("txn_id ='".$txnId."'");				 
		}	 
		
		$row = $db->fetchAll($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}
	
	public function getTxnBillingData($txnId=0,$billing=''){
		
		$db = $this->lobjDbAdpt;
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->order("id desc");
					  
		if($txnId!=0 && $billing!='')	{			
			 $select->where("txn_id ='".$txnId."'");
			 $select->where("billing_no ='".$billing."'");
			 
			 $row = $db->fetchRow($select);				 
		}	 
		
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
	
	public function generateProformaInvoice($txnId,$decree_date){
		
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txnId);
    	
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txnId);
		
		//get assessment data
		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
		$assessmentData = $assessmentDb->getData($txnId);
		
    	//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();		
		$program = $appProgramDB->getProgramFaculty($txnId);
		
		//program data
		$programDb = new GeneralSetup_Model_DbTable_Program();
		$programData = $programDb->fngetProgramData($program[0]['program_id']);
		
		//faculty data
		$collegeMasterDb = new GeneralSetup_Model_DbTable_Collegemaster();
		$facultyData = $collegeMasterDb->fngetCollegemasterData($program[0]['faculty_id']);
		    	
    	//get intake data
    	$intakeDb = new App_Model_Record_DbTable_Intake();
    	$intakeData = $intakeDb->getData($txnData['at_intake']);
    	
		//get fee structure
		//check local or foreign student
		$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
		if($applicant['appl_nationality']!=96){
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program[0]["program_id"],$program[0]['IdBranchOffer'],315);
		}else{
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program[0]["program_id"],$program[0]['IdBranchOffer']);
		}
		
		//fee structure plan
		$feeStructurePlanDb = new Studentfinance_Model_DbTable_FeeStructurePlan();
		$paymentPlanData = $feeStructurePlanDb->getStructureData($feeStructureData['fs_id']);
		$feeStructureData['payment_plan'] = $paymentPlanData;
		
		//fee structure program
		$feeStructureProgramDb = new Studentfinance_Model_DbTable_FeeStructureProgram();
		$feeStructureProgramData = $feeStructureProgramDb->getStructureData($feeStructureData['fs_id'],$program[0]["program_id"],$program[0]['IdBranchOffer']);
		
		//fee structure plan detail
		$fspdDb = new Studentfinance_Model_DbTable_FeeStructurePlanDetail();
		
		
		$name = "";
		$name .= $name!=""?" ":"".$applicant['appl_fname']!=null?$applicant['appl_fname']:"";
		$name .= $name!=""?" ":"".$applicant['appl_mname']!=null?$applicant['appl_mname']:"";
		$name .= $name!=""?" ":"".$applicant['appl_lname']!=null?$applicant['appl_lname']:"";
		
		$address = "Universitas Trisakti";
		 
		$arr_profoma = array();
		
		/*
		 * paket A
		 */
		$paket_a_plan = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[0]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		
		
		$arr_profoma[0] = array(
			'billing_no' => '01'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket A Lunas',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_a_plan as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[0]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[0]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[0]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[0]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[0]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[0]['amount_total'] = $total_amount;
		
		
		
		/*
		 * paket B
		 */
		
		//CICILAN 1
		$paket_b_plan_cicilan1 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$arr_profoma[1] = array(
			'billing_no' => '11'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 1',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan1 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[1]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[1]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[1]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[1]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[1]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[1]['amount_total'] = $total_amount;
		
		//CICILAN 2
		$paket_b_plan_cicilan2 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 2, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$arr_profoma[2] = array(
			'billing_no' => '12'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 2',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan2 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[2]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[2]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[2]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[2]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[2]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[2]['amount_total'] = $total_amount;
		
		//CICILAN 3
		$paket_b_plan_cicilan3 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 3, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		
		$arr_profoma[3] = array(
			'billing_no' => '13'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 3',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan3 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[3]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[3]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[3]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[3]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[3]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[3]['amount_total'] = $total_amount;
		
		//CICILAN 4
		$paket_b_plan_cicilan4 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 4, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		
		$arr_profoma[4] = array(
			'billing_no' => '14'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 4',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan4 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[4]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[4]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[4]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[4]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[4]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[4]['amount_total'] = $total_amount;
		
		//CICILAN 5
		$paket_b_plan_cicilan5 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 5, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		
		$arr_profoma[5] = array(
			'billing_no' => '15'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 5',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan5 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[5]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[5]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[5]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[5]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[5]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[5]['amount_total'] = $total_amount;
		
		//CICILAN 6
		$paket_b_plan_cicilan6 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 6, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		
		$arr_profoma[6] = array(
			'billing_no' => '16'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 6',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan6 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[6]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[6]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[6]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[6]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[6]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[6]['amount_total'] = $total_amount;
		
		
		
		/**
		 * insert into db
		 */
		$this->lobjDbAdpt->beginTransaction();
		
		try{
			
			foreach ($arr_profoma as $data){
				
				//check for already insert data
				$currentData = $this->getTxnBillingData($data['txn_id'],$data['billing_no']);
				if( $currentData!=null ){
					$this->update($data, 'id ='.$currentData['id']);
				}else{
					$this->insert($data);
				}
			}
			
			$query = $this->lobjDbAdpt->commit();
			$status = true;
			
		}catch(Exception $e){
			$status = false;
			
			$this->lobjDbAdpt->rollBack();
			
			$error_result = Array();
			$message = $e->getMessage();
			$code = $e->getCode();
			$error_result[0] = $message;
			$error_result[1] = $code;
			
		}
		
		return $status;
	}
	
	public function regenerateProformaInvoice($txnId){
		
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txnId);
    	
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txnId);
		
		//get assessment data
		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
		$assessmentData = $assessmentDb->getData($txnId);
		
    	//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();		
		$program = $appProgramDB->getProgramFaculty($txnId,$txnData['at_appl_type']);
		
		//program data
		$programDb = new GeneralSetup_Model_DbTable_Program();
		$programData = $programDb->fngetProgramData($program[0]['program_id']);
		
		//faculty data
		$collegeMasterDb = new GeneralSetup_Model_DbTable_Collegemaster();
		$facultyData = $collegeMasterDb->fngetCollegemasterData($program[0]['faculty_id']);
		    	
    	//get intake data
    	$intakeDb = new App_Model_Record_DbTable_Intake();
    	$intakeData = $intakeDb->getData($txnData['at_intake']);
    	
		//get fee structure
		//check local or foreign student
    	$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
    	if($applicant['appl_nationality']!=96){
    		$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program[0]["program_id"],$program[0]['IdBranchOffer'],315);
    	}else{
    		$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program[0]["program_id"],$program[0]['IdBranchOffer']);
    	}
    	
		
		//fee structure plan
		$feeStructurePlanDb = new Studentfinance_Model_DbTable_FeeStructurePlan();
		$paymentPlanData = $feeStructurePlanDb->getStructureData($feeStructureData['fs_id']);
		$feeStructureData['payment_plan'] = $paymentPlanData;
		
		//fee structure program
		$feeStructureProgramDb = new Studentfinance_Model_DbTable_FeeStructureProgram();
		$feeStructureProgramData = $feeStructureProgramDb->getStructureData($feeStructureData['fs_id'],$program[0]["program_id"],$program[0]['IdBranchOffer']);
		
		//fee structure plan detail
		$fspdDb = new Studentfinance_Model_DbTable_FeeStructurePlanDetail();
		
		
		$name = "";
		$name .= $name!=""?" ":"".$applicant['appl_fname']!=null?$applicant['appl_fname']:"";
		$name .= $name!=""?" ":"".$applicant['appl_mname']!=null?$applicant['appl_mname']:"";
		$name .= $name!=""?" ":"".$applicant['appl_lname']!=null?$applicant['appl_lname']:"";
		
		$address = "Universitas Trisakti";
		 
		$arr_profoma = array();
		
		/*
		 * paket A
		 */
		$paket_a_plan = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[0]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);

		$arr_profoma[0] = array(
			'billing_no' => '01'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket A Lunas',
			'due_date' => '',
			'offer_date' => $assessmentData['asd_decree_date']
		);
		
		
		$total_amount = 0;
		foreach ($paket_a_plan as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[0]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[0]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[0]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[0]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[0]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[0]['amount_total'] = $total_amount;
		
		
		
		/*
		 * paket B
		 */
		
		//CICILAN 1
		$paket_b_plan_cicilan1 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$arr_profoma[1] = array(
			'billing_no' => '11'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 1',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' => $assessmentData['asd_decree_date']
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan1 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[1]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[1]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[1]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[1]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[1]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[1]['amount_total'] = $total_amount;
		
		//CICILAN 2
		$paket_b_plan_cicilan2 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 2, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$arr_profoma[2] = array(
			'billing_no' => '12'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 2',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' => $assessmentData['asd_decree_date']
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan2 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[2]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[2]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[2]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[2]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[2]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[2]['amount_total'] = $total_amount;
		
		//CICILAN 3
		$paket_b_plan_cicilan3 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 3, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		
		$arr_profoma[3] = array(
			'billing_no' => '13'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 3',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' => $assessmentData['asd_decree_date']
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan3 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[3]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[3]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[3]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[3]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[3]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[3]['amount_total'] = $total_amount;
		
		//CICILAN 4
		$paket_b_plan_cicilan4 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 4, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		
		$arr_profoma[4] = array(
			'billing_no' => '14'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 4',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' => $assessmentData['asd_decree_date']
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan4 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[4]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[4]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[4]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[4]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[4]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[4]['amount_total'] = $total_amount;
		
		//CICILAN 5
		$paket_b_plan_cicilan5 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 5, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		
		$arr_profoma[5] = array(
			'billing_no' => '15'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 5',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' => $assessmentData['asd_decree_date']
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan5 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[5]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[5]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[5]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[5]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[5]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[5]['amount_total'] = $total_amount;
		
		//CICILAN 6
		$paket_b_plan_cicilan6 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 6, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		
		$arr_profoma[6] = array(
			'billing_no' => '16'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 6',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' => $assessmentData['asd_decree_date']
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan6 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[6]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[6]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[6]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[6]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[6]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[6]['amount_total'] = $total_amount;
		
		
		/**
		 * insert into db
		 */
		$this->lobjDbAdpt->beginTransaction();
		
		try{
					
			foreach ($arr_profoma as $key=>$data){
				
				//check for already insert data
				$currentData = $this->getTxnBillingData($data['txn_id'],$data['billing_no']);
				if( $currentData!=null ){
					$this->update($data, 'id ='.$currentData['id']);
				}else{
					
					//generate key
					$data['register_no'] = $this->generateAlphanumeric(9);
									
					$this->insert($data);
				}
			}
			
			$query = $this->lobjDbAdpt->commit();
			$status = true;
			
		}catch(Exception $e){
			$status = false;
			
			$this->lobjDbAdpt->rollBack();
			
			$error_result = Array();
			$message = $e->getMessage();
			$code = $e->getCode();
			$error_result[0] = $message;
			$error_result[1] = $code;
			
		}
		
		return $status;
	}
	
	public function generateUSMProformaInvoice($txnId,$decree_date,$ap_id){
			
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txnId);
    	
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txnId);
		
		//get assessment data
		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
		$assessmentData = $assessmentDb->getData($txnId);
		
		
    	//getapplicantprogram
		//$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();		
		//$program = $appProgramDB->getProgramFaculty($txnId);
		
		//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$program = $appProgramDB->getUsmOfferProgram($txnId);     
		
		//program data
		$programDb = new GeneralSetup_Model_DbTable_Program();
		$programData = $programDb->fngetProgramData($program['program_id']);
		
		//faculty data
		$collegeMasterDb = new GeneralSetup_Model_DbTable_Collegemaster();
		$facultyData = $collegeMasterDb->fngetCollegemasterData($program['faculty_id']);
		    	
    	//get intake data
    	$intakeDb = new App_Model_Record_DbTable_Intake();
    	$intakeData = $intakeDb->getData($txnData['at_intake']);
    	
		//get fee structure
		//check local or foreign student
		$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
		if($applicant['appl_nationality']!=96){
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program["program_id"],315);
		}else{
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program["program_id"]);
		}
		
		
		//fee structure plan
		$feeStructurePlanDb = new Studentfinance_Model_DbTable_FeeStructurePlan();
		$paymentPlanData = $feeStructurePlanDb->getStructureData($feeStructureData['fs_id']);
		$feeStructureData['payment_plan'] = $paymentPlanData;
		
		
		
		//fee structure program
		$feeStructureProgramDb = new Studentfinance_Model_DbTable_FeeStructureProgram();
		$feeStructureProgramData = $feeStructureProgramDb->getStructureData($feeStructureData['fs_id'],$program["program_id"]);
		
		//fee structure plan detail
		$fspdDb = new Studentfinance_Model_DbTable_FeeStructurePlanDetail();
		
		
		$name = "";
		$name .= $name!=""?" ":"".$applicant['appl_fname']!=null?$applicant['appl_fname']:"";
		$name .= $name!=""?" ":"".$applicant['appl_mname']!=null?$applicant['appl_mname']:"";
		$name .= $name!=""?" ":"".$applicant['appl_lname']!=null?$applicant['appl_lname']:"";
		
		$address = "Universitas Trisakti";
		 
		$arr_profoma = array();
		
		/*
		 * paket A
		 */
		
		$paket_a_plan = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[0]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		
		$arr_profoma[0] = array(
			'billing_no' => '01'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket A Lunas',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_a_plan as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[0]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[0]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[0]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[0]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[0]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[0]['amount_total'] = $total_amount;
		
		
		
		/*
		 * paket B
		 */
		
		//CICILAN 1
		$paket_b_plan_cicilan1 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		$arr_profoma[1] = array(
			'billing_no' => '11'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 1',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan1 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[1]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[1]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[1]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[1]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[1]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[1]['amount_total'] = $total_amount;
		
		//CICILAN 2
		$paket_b_plan_cicilan2 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 2, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		$arr_profoma[2] = array(
			'billing_no' => '12'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 2',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan2 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[2]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[2]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[2]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[2]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[2]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[2]['amount_total'] = $total_amount;
		
		//CICILAN 3
		$paket_b_plan_cicilan3 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 3, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		$arr_profoma[3] = array(
			'billing_no' => '13'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 3',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan3 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[3]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[3]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[3]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[3]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[3]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[3]['amount_total'] = $total_amount;
		
		//CICILAN 4
		$paket_b_plan_cicilan4 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 4, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		$arr_profoma[4] = array(
			'billing_no' => '14'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 4',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan4 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[4]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[4]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[4]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[4]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[4]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[4]['amount_total'] = $total_amount;
		
		//CICILAN 5
		$paket_b_plan_cicilan5 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 5, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		$arr_profoma[5] = array(
			'billing_no' => '15'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 5',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan5 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[5]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[5]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[5]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[5]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[5]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[5]['amount_total'] = $total_amount;
		
		//CICILAN 6
		$paket_b_plan_cicilan6 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 6, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		$arr_profoma[6] = array(
			'billing_no' => '16'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 6',
			'register_no' => $this->generateAlphanumeric(9),
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan6 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[6]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[6]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[6]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[6]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[6]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[6]['amount_total'] = $total_amount;
		
		
		
		/**
		 * insert into db
		 */
		$this->lobjDbAdpt->beginTransaction();
		
		try{
			
			foreach ($arr_profoma as $data){
				
				//check for already insert data
				$currentData = $this->getTxnBillingData($data['txn_id'],$data['billing_no']);
				if( $currentData!=null ){
					$this->update($data, 'id ='.$currentData['id']);
				}else{
					$this->insert($data);
				}
			}
			
			$query = $this->lobjDbAdpt->commit();
			$status = true;
			
		}catch(Exception $e){
			$status = false;
			
			$this->lobjDbAdpt->rollBack();
			
			$error_result = Array();
			$message = $e->getMessage();
			$code = $e->getCode();
			$error_result[0] = $message;
			$error_result[1] = $code;
			
		}
		
		return $status;
	}
	
	public function regenerateUSMProformaInvoice($txnId){
		
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txnId);
    	
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txnId);
		
		
		//get assessment data
		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
		$assessmentData = $assessmentDb->getData($txnId);
		$decree_date = $assessmentData['aaud_decree_date'];
		
		
		
    	//getapplicantprogram
		//$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();		
		//$program = $appProgramDB->getProgramFaculty($txnId);
		
		//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$program = $appProgramDB->getUsmOfferProgram($txnId);     
		
		//program data
		$programDb = new GeneralSetup_Model_DbTable_Program();
		$programData = $programDb->fngetProgramData($program['program_id']);
		
		//faculty data
		$collegeMasterDb = new GeneralSetup_Model_DbTable_Collegemaster();
		$facultyData = $collegeMasterDb->fngetCollegemasterData($program['faculty_id']);
		    	
    	//get intake data
    	$intakeDb = new App_Model_Record_DbTable_Intake();
    	$intakeData = $intakeDb->getData($txnData['at_intake']);
    	
		//get fee structure
		//check local or foreign student
		$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
		if($applicant['appl_nationality']!=96){
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program["program_id"],315);
		}else{
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program["program_id"]);
		}

		if(!$feeStructureData){
			 return false;
			//throw new Exception("No Fee Structure for given program ".$program['program_name_indonesia']);
		}
		
		
		//fee structure plan
		$feeStructurePlanDb = new Studentfinance_Model_DbTable_FeeStructurePlan();
		$paymentPlanData = $feeStructurePlanDb->getStructureData($feeStructureData['fs_id']);
		$feeStructureData['payment_plan'] = $paymentPlanData;
		
		//fee structure program
		$feeStructureProgramDb = new Studentfinance_Model_DbTable_FeeStructureProgram();
		$feeStructureProgramData = $feeStructureProgramDb->getStructureData($feeStructureData['fs_id'],$program["program_id"]);
		
		//fee structure plan detail
		$fspdDb = new Studentfinance_Model_DbTable_FeeStructurePlanDetail();
		
		
		$name = "";
		$name .= $name!=""?" ":"".$applicant['appl_fname']!=null?$applicant['appl_fname']:"";
		$name .= $name!=""?" ":"".$applicant['appl_mname']!=null?$applicant['appl_mname']:"";
		$name .= $name!=""?" ":"".$applicant['appl_lname']!=null?$applicant['appl_lname']:"";
		
		$address = "Universitas Trisakti";
		 
		$arr_profoma = array();
		
		/*
		 * paket A
		 */
		
		$paket_a_plan = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[0]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		$arr_profoma[0] = array(
			'billing_no' => '01'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket A Lunas',
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_a_plan as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[0]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[0]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[0]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[0]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[0]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[0]['amount_total'] = $total_amount;
		
		
		
		/*
		 * paket B
		 */
		
		//CICILAN 1
		$paket_b_plan_cicilan1 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		$arr_profoma[1] = array(
			'billing_no' => '11'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 1',
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan1 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[1]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[1]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[1]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[1]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[1]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[1]['amount_total'] = $total_amount;
		
		//CICILAN 2
		$paket_b_plan_cicilan2 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 2, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		$arr_profoma[2] = array(
			'billing_no' => '12'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 2',
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan2 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[2]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[2]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[2]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[2]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[2]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[2]['amount_total'] = $total_amount;
		
		//CICILAN 3
		$paket_b_plan_cicilan3 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 3, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		$arr_profoma[3] = array(
			'billing_no' => '13'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 3',
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan3 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[3]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[3]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[3]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[3]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[3]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[3]['amount_total'] = $total_amount;
		
		//CICILAN 4
		$paket_b_plan_cicilan4 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 4, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		$arr_profoma[4] = array(
			'billing_no' => '14'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 4',
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan4 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[4]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[4]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[4]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[4]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[4]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[4]['amount_total'] = $total_amount;
		
		//CICILAN 5
		$paket_b_plan_cicilan5 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 5, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		$arr_profoma[5] = array(
			'billing_no' => '15'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 5',
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan5 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[5]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[5]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[5]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[5]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[5]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[5]['amount_total'] = $total_amount;
		
		//CICILAN 6
		$paket_b_plan_cicilan6 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 6, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		$arr_profoma[6] = array(
			'billing_no' => '16'.$txnData['at_pes_id'],
			'payee_id' => $txnData['at_pes_id'],
			'appl_id' => $txnData['at_appl_id'],
			'txn_id' => $txnData['at_trans_id'],
			'name' => $name,
			'address' => $address,
			'ref1' => $facultyData['CollegeCode']."-".$facultyData['ShortName'],
			'ref2' => '',
			'ref3' => $programData['ProgramCode']."-".$programData['ShortName'],
			'ref4' => substr($intakeData['IntakeId'], 0,4),
			'ref5' => 'Paket B Cicilan 6',
			'due_date' => '',
			'offer_date' =>$decree_date
		);
		
		$total_amount = 0;
		foreach ($paket_b_plan_cicilan6 as $key=>$item){
			$total_amount += $item['total_amount'];
			
			if($item['fi_id'] == 1){//SP
				$arr_profoma[6]['amount1'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 2){//BPP-POKOK
				$arr_profoma[6]['amount2'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 3){//BPP-SKS
				$arr_profoma[6]['amount3'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 4){//PRAKTIKUM
				$arr_profoma[6]['amount4'] = $item['total_amount'];
			}else
			if($item['fi_id'] == 5){//LAIN-LAIN
				$arr_profoma[6]['amount5'] = $item['total_amount'];
			}
		}
		
		$arr_profoma[6]['amount_total'] = $total_amount;
		
		
		/**
		 * insert into db
		 */
		$this->lobjDbAdpt->beginTransaction();
		
		try{
					
			foreach ($arr_profoma as $key=>$data){
				
				
				//check for already insert data
				$currentData = $this->getTxnBillingData($data['txn_id'],$data['billing_no']);
				if( $currentData!=null ){
					$this->update($data, 'id ='.$currentData['id']);
				}else{
					
					//generate key
					$data['register_no'] = $this->generateAlphanumeric(9);
					
			    	
					$this->insert($data);
				}
			}
			
			$query = $this->lobjDbAdpt->commit();
			$status = true;
			
		}catch(Exception $e){
			$status = false;
			
			$this->lobjDbAdpt->rollBack();
			
			$error_result = Array();
			$message = $e->getMessage();
			$code = $e->getCode();
			$error_result[0] = $message;
			$error_result[1] = $code;
			

		}
		
		return $status;
	}
	
	
	/**
	 * 
	 * Generate Alphanumeric
	 * @param int $length
	 * @param unknown_type $alphanumeric
	 */
	private function generateAlphanumeric($length, $alphanumeric=true){
		
		if($alphanumeric){
			$character_array = array_merge(range('A', 'Z'), range(0, 9));
		}else{
			$character_array = array_merge(range(0, 9));
		}
		
		
		$string = "";
		    for($i = 0; $i < $length; $i++) {
		        $string .= $character_array[rand(0, (count($character_array) - 1))];
		    }
	    
	    return $string;
	}
	
	public function getDecreeApplicantList($nomor, $type="PSSB"){
		
		$db = $this->lobjDbAdpt;
		
		if($type=="PSSB"){
			//get applicant
			$bil_applicant = $db->select()
							->distinct()
							->from(array('aa'=>'applicant_assessment'),array())
							->join(array('asd'=>'applicant_selection_detl'), 'aa.aar_rector_selectionid = asd.asd_id',array())
							->join(array('at'=>'applicant_transaction'),'aa.aar_trans_id = at.at_trans_id', array('at.at_trans_id','at.at_pes_id') )
							->join(array('ap'=>'applicant_profile'),'ap.appl_id = at.at_appl_id')
							->where("asd.asd_nomor = '".$nomor."'")
							->where("at.at_status not in ('APPLY','CLOSE','PROCESS','REJECT')")
						    ->where("at.at_selection_status = 3");
			
			$row = $db->fetchAll($bil_applicant);
			
		}else	
		if($type=="USM"){
			
			//get student
			$selectUSMNomor = $db->select()
							->distinct()
							->from(array('aau'=>'applicant_assessment_usm'),array())
							->join(array('aaud'=>'applicant_assessment_usm_detl'), 'aau.aau_rector_selectionid = aaud.aaud_id',array())
							->join(array('at'=>'applicant_transaction'),'aau.aau_trans_id = at.at_trans_id', array('at.at_trans_id','at.at_pes_id') )
							->join(array('ap'=>'applicant_profile'),'ap.appl_id = at.at_appl_id')
							->where('aaud.aaud_nomor = ?',$nomor)
							->where("at.at_status not in ('APPLY','CLOSE','PROCESS','REJECT')")
						    ->where("at.at_selection_status = 3");
			
			$row = $db->fetchAll($selectUSMNomor);
						
		}		
		
		if($row){
			
			for($i=0; $i<sizeof($row);$i++){
				$applicant = $row[$i];
				
				$select = $db->select()
						->from(array('api'=>'applicant_proforma_invoice'))
						->where('api.payee_id =?',$applicant['at_pes_id']);
						
				if($db->fetchRow($select)){
					$row[$i]['proforma_status'] = true;
				}else{
					$row[$i]['proforma_status'] = false;
				}		
				
			}
			return $row;
		}else{
			return null;
		}
		
	}
}