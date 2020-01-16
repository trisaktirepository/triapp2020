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
	public function moveToInvoiceBasedOnPaket($noform,$paket){
	
		$db = $this->lobjDbAdpt;
		if ($paket=="A") $paket="0"; else $paket="1";
		
		$dbInvoice=new Studentfinance_Model_DbTable_InvoiceMain();
		$dbInvoiceDet=new Studentfinance_Model_DbTable_InvoiceDetail();
		
		$select = $db ->select()
		->from('proforma_invoice_va')
		->where('LEFT(bill_number,1)=?',$paket)
		->where('no_fomulir=?',$noform);
			
		$invoices=$db->fetchAll($select);
		foreach ($invoices as $value) {
			$idpro=$value['id'];
			unset($value['id']);
			$inv=$dbInvoice->isIn($value['bill_number']);
			if (!$inv) {
				$id=$dbInvoice->insert($value);
			} else $id=$inv['id'];
				//get detail
				$select = $db ->select()
				->from('proforma_invoice_detail')
				->where('invoice_main_id=?',$idpro);
				$detail=$db->fetchAll($select);
				//echo $id;echo var_dump($detail);exit;
				foreach ($detail as $det) {
					unset($det['id']);
					$det['invoice_main_id']=$id;
					if (!$dbInvoiceDet->isIn($id, $det['fi_id'])) $dbInvoiceDet->insertData($det);
				}
			
		}
	}
	public function generateProformaInvoiceEcollection($txnId){
	
		$dbInvoiceVa=new Application_Model_DbTable_ProformaInvoiceVa();
		$dbInvoiceDetailVa=new Application_Model_DbTable_ProformaInvoiceDetail();
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant = $applicantDB->getAllProfile($txnId);
			
		//get transaction info
		$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txnId);
	
		//get assessment data
		if ($txnData['at_appl_type']=="1") {
			$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
			$assessmentData=$assessmentDb->getData($txnId);
		} else {
			$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
			$assessmentData = $assessmentDb->getData($txnId);
		}
	
		//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$program = $appProgramDB->getProgramFaculty($txnId,null,$txnData['at_appl_type']);
	
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
	
		if($this->islocalNationality($txnId)){
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program[0]["program_id"],314,$program[0]['IdBranchOffer']);
		}else{
			//315 is foreigner in lookup db
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program[0]["program_id"],null,315);
		}
	
		//echo var_dump($feeStructureData);exit;
		//fee structure plan
		$feeStructurePlanDb = new Studentfinance_Model_DbTable_FeeStructurePlan();
		$paymentPlanData = $feeStructurePlanDb->getStructureData($feeStructureData['fs_id']);
		$feeStructureData['payment_plan'] = $paymentPlanData;
	
		//fee structure program
		$feeStructureProgramDb = new Studentfinance_Model_DbTable_FeeStructureProgram();
		$feeStructureProgramData = $feeStructureProgramDb->getStructureData($feeStructureData['fs_id'],$program[0]["program_id"],$program[0]['IdBranchOffer']);
		//echo var_dump($feeStructureProgramData);exit;
		//fee structure plan detail
		$fspdDb = new Studentfinance_Model_DbTable_FeeStructurePlanDetail();
	
		$arr_profoma = array();
		$dbProformaDetail=new Application_Model_DbTable_ProformaInvoiceDetail();
		$reg_date = array(
				'REGISTRATION_DATE_START'=> $assessmentData['aar_reg_start_date'],
				'REGISTRATION_DATE_END'=> $assessmentData['aar_reg_end_date']
		);
			
		$end=date('Y-m-d H:s:i',strtotime($reg_date['REGISTRATION_DATE_END'].' 23:59:00'));
	
			
	
		/*
		 * paket A
		*/
		$paket_a_plan = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[0]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
	
		$arr_profoma[0] = array(
				'billing_no' => '01'.$txnData['at_pes_id'],
				'no_fomulir' => $txnData['at_pes_id'],
				'appl_id' => $txnData['at_appl_id'],
				'college_id' => $programData['IdCollege'],
				'program_code'=>$programData['ProgramCode'],
				'bill_description' => 'Paket A Lunas',
				'academic_year' => $txnData['at_academic_year'],
				'fs_id'=>$feeStructureData['fs_id'],
				'expired_dt'=>$end,
				//'offer_date' => $assessmentData['asd_decree_date']
		);
	
	
		$total_amount = 0;
		foreach ($paket_a_plan as $key=>$item){
			$total_amount += $item['total_amount'];
			$arr_profoma[0]['detail'][]=array('fi_id'=>$item['fi_id'],'description'=>$item['fi_name_bahasa'],'amount'=>$item['total_amount']);
		}
		$arr_profoma[0]['amount_total'] = $total_amount;
	 
		if ($paymentPlanData[1]['fsp_id']!=null) {
			/*
			 * paket B
			*/
			$feeStrucPlan=$feeStructurePlanDb->getBillingPlanByPackage($feeStructureData['fs_id'], 'Paket B');
			//echo var_dump($feeStrucPlan);exit;
			if ($feeStrucPlan) {
				$fspbillinstallment=$feeStrucPlan['fsp_bil_installment'];
				$end=$end=date('Y-m-d H:s:i',strtotime($reg_date['REGISTRATION_DATE_END'].' 23:59:00'));
				for ($i=1;$i<=$fspbillinstallment;$i++){
	
					$paket_b_plan_cicilan = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], $i, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
					$arr_profoma[$i] = array(
							'billing_no' => $feeStrucPlan['fsp_billing_no'].$i.$txnData['at_pes_id'],
							'no_fomulir' => $txnData['at_pes_id'],
							'appl_id' => $txnData['at_appl_id'],
							'college_id' => $programData['IdCollege'],
							'program_code'=>$programData['ProgramCode'],
							'bill_description' => 'Paket B Cicilan '.$i,
							'academic_year' => $txnData['at_academic_year'],
							'fs_id'=>$feeStructureData['fs_id'],
							'expired_dt'=> $end
							//'offer_date' => $assessmentData['asd_decree_date']
					);
					$end=date ( 'Y-m-d' , strtotime ( '+1 month' , strtotime ( $end) ) );
						
					$total_amount = 0;
					foreach ($paket_b_plan_cicilan as $key=>$item){
						$total_amount += $item['total_amount'];
						if ($item['total_amount']>0)
							$arr_profoma[$i]['detail'][]=array('fi_id'=>$item['fi_id'],'description'=>$item['fi_name_bahasa'],'amount'=>$item['total_amount']);
					}
					if ($total_amount>0)
						$arr_profoma[$i]['amount_total'] = $total_amount;
					else unset($arr_profoma[$i]);
				}
			}
	
		}
		//echo var_dump($arr_profoma);exit;
		/**
		 * insert into db
		 */
		$this->lobjDbAdpt->beginTransaction();
		echo var_dump($arr_profoma);exit;
		try{
	
			foreach ($arr_profoma as $key=>$data){
				$inv_data = array(
						'bill_number' => $data['billing_no'],
						'no_fomulir' => $data['no_fomulir'],
						'appl_id' => $data['appl_id'],
						'IdStudentRegistration' => null,
						'academic_year' => $data['academic_year'],
						'bill_amount' => $data['amount_total'],
						'bill_paid' => 0.00,
						'bill_balance' => $data['amount_total'],
						'bill_description' => $data['bill_description'],
						'college_id' => $data['college_id'],
						'program_code' => $data['program_code'],
						'creator' => '1',
						'fs_id' => $data['fs_id'],
						'status' => 'A',
						'va_expired_dt'=>$data['expired_dt'],
						'date_create' => date('Y-m-d H:i:s')
				);
				//echo var_dump($inv_data);exit;
				$row=$dbInvoiceVa->isIn($inv_data['bill_number'],$inv_data['appl_id']);
				if ($row) {
					$idinvoice=$row['id'];
					$dbInvoiceVa->updateData($inv_data,$idinvoice );
				}
				else $idinvoice=$dbInvoiceVa->addData($inv_data);
				$detail=$data['detail'];
				foreach ($detail as $det) {
					$detail_inv=array('invoice_main_id'=>$idinvoice,
							'fi_id'=>$det['fi_id'],
							'fee_item_description'=>$det['description'],
							'amount'=>$det['amount']
					);
					$row=$dbInvoiceDetailVa->isIn($idinvoice, $det['fi_id']);
					if ($row) $dbInvoiceDetailVa->updateData($detail_inv, $row['id']);
					else $dbInvoiceDetailVa->addData($detail_inv);
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
		//	exit;
		return $status;
	}
	private function islocalNationality($txn_id){
		//get profile
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
		->from(array('at'=>'applicant_transaction'))
		->join(array('ap'=>'applicant_profile'),'ap.appl_id = at.at_appl_id')
		->where("at_trans_id = ".$txn_id);
	
		$row = $db->fetchRow($select);
	
		//nationality
		if( isset($row['appl_nationality']) ){
				
			if($row['appl_nationality']==96){
				return true;
			}else{
				return false;
			}
		}else{
			//default to local if null data
			return true;
		}
	}
}