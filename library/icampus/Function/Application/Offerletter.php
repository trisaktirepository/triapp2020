<?php 
class icampus_Function_Application_Offerletter extends Zend_View_Helper_Abstract{
	
	public function generateOfferLetter($txnId){
		$translate = Zend_Registry::get('Zend_Translate');
		
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txnId);
    	
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txnId);
		if ($txnData['at_appl_type']=="1") $type='Jalur Seleksi USM';
		else if ($txnData['at_appl_type']=="2") $type='Jalur Seleksi PSSB';
		else if ($txnData['at_appl_type']=="3") $type='Jalur Mahasiswa Pindahan';
		else if ($txnData['at_appl_type']=="4") $type='Jalur Seleksi Undangan';
		else if ($txnData['at_appl_type']=="5") $type='Jalur Seleksi Beasiswa';
		else if ($txnData['at_appl_type']=="6") $type='Jalur Seleksi Portofolio';
		else if ($txnData['at_appl_type']=="7") $type='Jalur Seleksi Nilai UTBK';
		
		//get assessment data
		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
		$assessmentData = $assessmentDb->getData($txnId);
				
		
		//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$program = $appProgramDB->getProgramFaculty($txnId);
    	$branch=$program[0]['IdBranchOffer'];	
		//program data
		$programDb = new GeneralSetup_Model_DbTable_Program();
		$programData = $programDb->fngetProgramData($program[0]['program_id']);
		
		//award type
		$award = "";
		
		if($programData['Award'] == 36){
			$award = "D3";
		}else
		if($programData['Award'] == 363){
			$award = "D4";
		}else{
			$award = "S1";
		}
		
		
		$learning_duration = $award." = ".$programData['OptimalDuration']." Semester";
		
		
		//rank
		if($assessmentData['aar_rating_rector']==1){
			$rank = "1 (Satu)";
			$biaya =$programData['Estimate_Fee_R1']!=null?$programData['Estimate_Fee_R1']:""; 
		}else
		if($assessmentData['aar_rating_rector']==2){
			$rank = "2 (Dua)"; 
			$biaya =$programData['Estimate_Fee_R2']!=null?$programData['Estimate_Fee_R2']:"";
		}else
		if($assessmentData['aar_rating_rector']==3){
			$rank = "3 (Tiga)"; 
			$biaya =$programData['Estimate_Fee_R3']!=null?$programData['Estimate_Fee_R3']:"";
		}
		
		
		
		
		//faculty data
		$collegeMasterDb = new GeneralSetup_Model_DbTable_Collegemaster();
		$facultyData = $collegeMasterDb->fngetCollegemasterData($program[0]['faculty_id']);
		
		//get applicant parents info
    	$familyDB =  new App_Model_Application_DbTable_ApplicantFamily();
    	$father = $familyDB->fetchdata($applicant["appl_id"],20); //father's    	
    	
    	//get next intake
    	$intakeDb = new GeneralSetup_Model_DbTable_Intake();
    	$intakeData = $intakeDb->fngetIntakeDetails($txnData['at_intake']);
    	    	
		//get fee structure
		$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
		
		if(!$this->islocalNationality($txnId)){
			//315 is foreigner in lookup db
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData[0]['IdIntake'],$program[0]["program_id"],315,$branch);	
			$biaya = $biaya*2;
			$biaya = number_format($biaya, 2, '.', ',');
		}else{
			//default to local
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData[0]['IdIntake'],$program[0]["program_id"],314,$branch);
			$biaya = number_format($biaya, 2, '.', ',');
		}
				
		//fee structure plan
		$feeStructurePlanDb = new Studentfinance_Model_DbTable_FeeStructurePlan();
		$paymentPlanData = $feeStructurePlanDb->getStructureData($feeStructureData['fs_id']);
		$feeStructureData['payment_plan'] = $paymentPlanData;
		
		//fee structure program
		$feeStructureProgramDb = new Studentfinance_Model_DbTable_FeeStructureProgram();
		$feeStructureProgramData = $feeStructureProgramDb->getStructureData($feeStructureData['fs_id'],$program[0]["program_id"],$branch);
		
		//fee structure plan detail
		$fspdDb = new Studentfinance_Model_DbTable_FeeStructurePlanDetail();
		
		foreach ($feeStructureData['payment_plan'] as $key=>$plan){
			
			for($installment=1; $installment<=$plan['fsp_bil_installment']; $installment++){
				$feeStructureData['payment_plan'][$key]['plan_detail'][$installment] = $fspdDb->getPlanData($plan['fsp_structure_id'], $plan['fsp_id'], $installment, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);	
			}
		}
		
		
		/*
		 * paket A
		 */
		//$paket_a_plan = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[0]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		
		
		/*
		 * paket B
		 */
		/*
		$paket_b_plan_cicilan1 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$paket_b_plan_cicilan2 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 2, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$paket_b_plan_cicilan3 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 3, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$paket_b_plan_cicilan4 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 4, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$paket_b_plan_cicilan5 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 5, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$paket_b_plan_cicilan6 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 6, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		*/
		
		/*echo "<pre>";
		print_r($paket_b_plan_cicilan1);
		echo "<pre>";
		exit;*/
		  
		//create image
		//$this->createImage();
		
		
    	
		//$nomor = '010/AK.4.02/PSSB-BAA/Usakti/WR.I/I-3/2012';
		$nomor=$assessmentData['asd_nomor'];
		
		$address = "";
		if( isset($applicant["appl_address1"]) && $applicant["appl_address1"]!=""){
			$address = $address . $applicant["appl_address1"]."<br />";
		}
		if( isset($applicant["appl_address2"]) && $applicant["appl_address2"]!=""){
			$address = $address . $applicant["appl_address2"]."<br />";
		}
		if( isset($applicant["CityName"]) && $applicant["CityName"]!=""){
			$address = $address . $applicant["CityName"]."<br />";
		}
		if( isset($applicant["appl_postcode"]) && $applicant["appl_postcode"]!=""){
			$address = $address . $applicant["appl_postcode"]."<br />";
		}
		if( isset($applicant["StateName"]) && $applicant["StateName"]!=""){
			$address = $address . $applicant["StateName"]."<br />";
		}
		
		$fieldValues = array(
				'$[NO_PES]'=>$txnData["at_pes_id"],
		        '$[NOMOR]'=>$nomor,
				'$[LAMPIRAN]'=>"-",
		        '$[TITLE_TEMPLATE]'=>$translate->_("Pemberitahuan diterima sebagai calon Mahasiswa di Universitas Trisakti"),
		        '$[APPLICANT_NAME]'=>$applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
				'$[PARENTNAME]'=>$father["af_name"],
		        '$[ADDRESS]' =>$address,
				'$ADDRESS1]'=>$applicant["appl_address1"],
				'$ADDRESS2]'=>$applicant["appl_address2"],
				'$[CITY]'=>$applicant["CityName"],
				'$[POSTCODE]'=>$applicant["appl_postcode"],
				'$[STATE]'=>$applicant["StateName"],				
		    	'$[ACADEMIC_YEAR]'=>$txnData['ay_code'],
				'$[PERIOD]'=>$txnData['ap_desc'],
				'$[FACULTY]'=>$program[0]["faculty2"],
				'$[FACULTY_NAME]'=>($facultyData['ArabicName']!=null?$facultyData['ArabicName']." ":"-"),
				'$[FACULTY_SHORTNAME]'=>($facultyData['ShortName']!=null?$facultyData['ShortName']." ":"-"),
				'$[FACULTY_ADDRESS1]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"-"),
				'$[FACULTY_ADDRESS2]'=>($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
				'$[FACULTY_ADDRESS]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"").($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
				'$[FACULTY_PHONE]'=>($facultyData['Phone1']!=null?$facultyData['Phone1']." ":"").($facultyData['Phone2']!=null?", ".$facultyData['Phone2']." ":""),
				'$[FACULTY_FAX]'=>($facultyData['Fax']!=null?$facultyData['Fax']." ":""),
				'$[PROGRAME]'=>$program[0]["program_name_indonesia"],
				'$[KELAS]'=>$program[0]["GroupName"],
				'$[RANK]' => $rank,
		        '$[PRINT_DATE]'=>date('j M Y'),
				'$[REGISTRATION_DATE_START]'=> date ( 'j F Y' , strtotime ( $assessmentData['aar_reg_start_date'] ) ),
				'$[REGISTRATION_DATE_END]'=> date ( 'j F Y' , strtotime ( $assessmentData['aar_reg_end_date'] ) ),
				//'$[PAKET_A_DATE_PAYMENT]'=> date ( 'j F Y' , strtotime ( $assessmentData['aar_payment_start_date'] ) ),
				//'$[PAKET_A_SP]' => number_format($paket_a_plan[0]['total_amount'], 2, '.', ','),
				//'$[PAKET_A_BPP_POKOK]' => number_format($paket_a_plan[1]['total_amount'], 2, '.', ','),
				//'$[PAKET_A_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				//'$[PAKET_A_BPP_SKS_VALUE]' => number_format($paket_a_plan[2]['fsi_amount'], 2, '.', ','),
				//'$[PAKET_A_BPP_SKS_AMOUNT]' => number_format($paket_a_plan[2]['total_amount'], 2, '.', ','),
				//'$[PAKET_A_PRAKTIKUM]' => number_format($paket_a_plan[3]['total_amount'], 2, '.', ','),
				//'$[PAKET_A_TOTAL]' => number_format($paket_a_plan[0]['total_amount'] + $paket_a_plan[1]['total_amount'] + $paket_a_plan[2]['total_amount'] + $paket_a_plan[3]['total_amount'], 2, '.', ',') ,

				//'$[PAKET_B_C1_DATE_PAYMENT]'=>date ( 'j F Y' , strtotime ( $assessmentData['aar_payment_start_date'] ) ),
				//'$[PAKET_B_C1_SP]' => number_format($paket_b_plan_cicilan1[0]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C1_BPP_POKOK]' => number_format($paket_b_plan_cicilan1[1]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C1_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				//'$[PAKET_B_C1_BPP_SKS_VALUE]' => number_format($paket_b_plan_cicilan1[2]['fsi_amount'], 2, '.', ','),
				//'$[PAKET_B_C1_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan1[2]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C1_PRAKTIKUM]' => number_format($paket_b_plan_cicilan1[3]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C1_TOTAL]' => number_format($paket_b_plan_cicilan1[0]['total_amount'] + $paket_b_plan_cicilan1[1]['total_amount'] + $paket_b_plan_cicilan1[2]['total_amount'] + $paket_b_plan_cicilan1[3]['total_amount'], 2, '.', ',') ,
		
				//'$[PAKET_B_C2_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+1 month' , strtotime ( $assessmentData['aar_reg_end_date'] ) ) ),
				//'$[PAKET_B_C2_SP]' => number_format($paket_b_plan_cicilan2[0]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C2_BPP_POKOK]' => number_format($paket_b_plan_cicilan2[1]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C2_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				//'$[PAKET_B_C2_BPP_SKS_VALUE]' => number_format($paket_b_plan_cicilan2[2]['fsi_amount'], 2, '.', ','),
				//'$[PAKET_B_C2_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan2[2]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C2_PRAKTIKUM]' => number_format($paket_b_plan_cicilan2[3]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C2_TOTAL]' => number_format($paket_b_plan_cicilan2[0]['total_amount'] + $paket_b_plan_cicilan2[1]['total_amount'] + $paket_b_plan_cicilan2[2]['total_amount'] + $paket_b_plan_cicilan2[3]['total_amount'], 2, '.', ',') ,
		
				/*'$[PAKET_B_C3_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+2 month' , strtotime ( $assessmentData['aar_reg_end_date'] ) ) ),
				'$[PAKET_B_C3_SP]' => number_format($paket_b_plan_cicilan3[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_BPP_POKOK]' => number_format($paket_b_plan_cicilan3[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C3_SKS_VALUE]' => number_format($paket_b_plan_cicilan3[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C3_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan3[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_PRAKTIKUM]' => number_format($paket_b_plan_cicilan3[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_TOTAL]' => number_format($paket_b_plan_cicilan3[0]['total_amount'] + $paket_b_plan_cicilan3[1]['total_amount'] + $paket_b_plan_cicilan3[2]['total_amount'] + $paket_b_plan_cicilan3[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C4_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+3 month' , strtotime ( $assessmentData['aar_reg_end_date'] ) ) ),
				'$[PAKET_B_C4_SP]' => number_format($paket_b_plan_cicilan4[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_BPP_POKOK]' => number_format($paket_b_plan_cicilan4[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C4_SKS_VALUE]' => number_format($paket_b_plan_cicilan4[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C4_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan4[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_PRAKTIKUM]' => number_format($paket_b_plan_cicilan4[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_TOTAL]' => number_format($paket_b_plan_cicilan4[0]['total_amount'] + $paket_b_plan_cicilan4[1]['total_amount'] + $paket_b_plan_cicilan4[2]['total_amount'] + $paket_b_plan_cicilan4[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C5_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+4 month' , strtotime ( $assessmentData['aar_reg_end_date'] ) ) ),
				'$[PAKET_B_C5_SP]' => number_format($paket_b_plan_cicilan5[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_BPP_POKOK]' => number_format($paket_b_plan_cicilan5[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C5_SKS_VALUE]' => number_format($paket_b_plan_cicilan5[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C5_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan5[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_PRAKTIKUM]' => number_format($paket_b_plan_cicilan5[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_TOTAL]' => number_format($paket_b_plan_cicilan5[0]['total_amount'] + $paket_b_plan_cicilan5[1]['total_amount'] + $paket_b_plan_cicilan5[2]['total_amount'] + $paket_b_plan_cicilan5[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C6_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+5 month' , strtotime ( $assessmentData['aar_reg_end_date'] ) ) ),
				'$[PAKET_B_C6_SP]' => number_format($paket_b_plan_cicilan6[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_BPP_POKOK]' => number_format($paket_b_plan_cicilan6[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C6_SKS_VALUE]' => number_format($paket_b_plan_cicilan6[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C6_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan6[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_PRAKTIKUM]' => number_format($paket_b_plan_cicilan6[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_TOTAL]' => number_format($paket_b_plan_cicilan6[0]['total_amount'] + $paket_b_plan_cicilan6[1]['total_amount'] + $paket_b_plan_cicilan6[2]['total_amount'] + $paket_b_plan_cicilan6[3]['total_amount'], 2, '.', ',') ,
					
				'$[BALANCE_INSTALLMENT_PAKET_B]' => number_format( ( $paket_a_plan[0]['total_amount'] + $paket_a_plan[1]['total_amount'] + $paket_a_plan[2]['total_amount'] + $paket_a_plan[3]['total_amount'] ) - ( $paket_b_plan_cicilan1[0]['total_amount'] + $paket_b_plan_cicilan1[1]['total_amount'] + $paket_b_plan_cicilan1[2]['total_amount'] + $paket_b_plan_cicilan1[3]['total_amount'] ), 2, '.', ','),
				*/
				'$[SELECTION_TYPE]'=>$type,
				'$[LEARNING_DURATION]' => $learning_duration,
				'$[ESTIMASI_BIAYA]' => $biaya,
				'$[RECTOR_DATE]' => date('j M Y',strtotime($assessmentData['asd_decree_date']))
		);
		
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		$html_template_path = DOCUMENT_PATH."/template/OfferLetter.html";
		
		$html = file_get_contents($html_template_path);
		
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}
		
		//program data
		global $program;
		$program = $feeStructureProgramData;
		
		//registration date
		global $reg_date;
		$reg_date = array(
						'REGISTRATION_DATE_START'=> $assessmentData['aar_reg_start_date'],
						'REGISTRATION_DATE_END'=> $assessmentData['aar_reg_end_date']
					);

		//date payment
		foreach($feeStructureData['payment_plan'] as $key=>$plan){
			$start = $assessmentData['aar_reg_start_date'];
			$end = $assessmentData['aar_reg_end_date'];
			
			foreach ($plan['plan_detail'] as $key2=>$installment){
				$reg_date['date_payment'][$key][$key2]['start'] = $start;
				$reg_date['date_payment'][$key][$key2]['end'] = $end;

				$end = date ( 'F Y' , strtotime ( '+1 month' , strtotime ( $end) ) );
			}
			
			$end = $assessmentData['aar_reg_end_date'];
		}			
		
		
		//fee data
		global $fees;
		$fees = $feeStructureData['payment_plan'];
		
		//footer variable
		global $pes;
		$pes = $txnData["at_pes_id"];
		//echo $html;
		//exit;
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		
		
		//$dompdf->stream($txnData["at_pes_id"]."_offer_letter.pdf");
		$pdf = $dompdf->output();
		//exit;
		
		//$location_path
		$location_path = "applicant/".date("mY")."/".$txnId;
		
		//output_directory_path
		$output_directory_path = DOCUMENT_PATH."/".$location_path;
		
		//create directory to locate file			
		if (!is_dir($output_directory_path)) {
	    	mkdir($output_directory_path, 0775);
		}
		
		//output filename 
		$output_filename = $txnData["at_pes_id"]."_offer_letter.pdf";
		
		//to rename output file			
		$output_file_path = $output_directory_path."/".$output_filename;

		file_put_contents($output_file_path, $pdf);
		
		//update file info
		/*$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
		$doc["ad_filepath"]=$location_path;
		$doc["ad_filename"]=$output_filename;
		$documentDB->updateDocument($doc,$txnId,45);*/
		
		//update file info
		$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
		$fileexist = $documentDB->getDataArray($txnId, 45);
		
		$doc["ad_filepath"]=$location_path;
		$doc["ad_filename"]=$output_filename;
		$doc["ad_appl_id"]=$txnId;
		$doc["ad_type"]=45;
		$doc["ad_createddt"]=date("Y-m-d");
		
		
		if($fileexist){
			$documentDB->updateDocument($doc,$txnId,45);
		}else{
			$documentDB->addData($doc);
		}

		//regenerate performa invoice
		$proformaInvoiceDb = new Application_Model_DbTable_ProformaInvoice();
		$proformaInvoiceDb->regenerateProformaInvoice($txnId);
	}
	
	public function generateUsmOfferLetter($txnId){
		
		$translate = Zend_Registry::get('Zend_Translate');
		
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txnId);
    	
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txnId);
		if ($txnData['at_appl_type']=="1") $type='Jalur Seleksi USM';
		else if ($txnData['at_appl_type']=="2") $type='Jalur Seleksi PSSB';
		else if ($txnData['at_appl_type']=="3") $type='Jalur Mahasiswa Pindahan';
		else if ($txnData['at_appl_type']=="4") $type='Jalur Seleksi Undangan';
		else if ($txnData['at_appl_type']=="5") $type='Jalur Seleksi Beasiswa';
		else if ($txnData['at_appl_type']=="6") $type='Jalur Seleksi Portofolio';
		else if ($txnData['at_appl_type']=="7") $type='Jalur Seleksi Nilai UTBK';
		
		//get assessment data
		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
		$assessmentData = $assessmentDb->getData($txnId);	

		//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$program = $appProgramDB->getUsmOfferProgram($txnId);     	
		$branch=$program['IdBranchOffer'];
		//program data
		$programDb = new GeneralSetup_Model_DbTable_Program();
		$programData = $programDb->fngetProgramData($program['program_id']);
		
		
		//award type
		$award = "";
		
		if($programData['Award'] == 36){
			$award = "D3";
		}else
		if($programData['Award'] == 363){
			$award = "D4";
		}else{
			$award = "S1";
		}
		
		$learning_duration = $award." = ".$programData['OptimalDuration']." Semester";
		
		
		//rank
		if($assessmentData['aau_rector_ranking']==1){
			$rank = "1 (Satu)";
			//$biaya =$programData['Estimate_Fee_R1']!=null?number_format($programData['Estimate_Fee_R1'], 2, '.', ','):""; 
			$biaya =$programData['Estimate_Fee_R1']!=null?$programData['Estimate_Fee_R1']:""; 			
		}else
		if($assessmentData['aau_rector_ranking']==2){
			$rank = "2 (Dua)"; 
			$biaya =$programData['Estimate_Fee_R2']!=null?$programData['Estimate_Fee_R2']:"";
		}else
		if($assessmentData['aau_rector_ranking']==3){
			$rank = "3 (Tiga)"; 
			$biaya =$programData['Estimate_Fee_R3']!=null?$programData['Estimate_Fee_R3']:"";
		}
		
		
		
		
		//faculty data
		$collegeMasterDb = new GeneralSetup_Model_DbTable_Collegemaster();
		$facultyData = $collegeMasterDb->fngetCollegemasterData($program['faculty_id']);
		
		//get applicant parents info
    	$familyDB =  new App_Model_Application_DbTable_ApplicantFamily();
    	$father = $familyDB->fetchdata($applicant["appl_id"],20); //father's    	
    	
    	//get next intake
    	$intakeDb = new GeneralSetup_Model_DbTable_Intake();
    	$intakeData = $intakeDb->fngetIntakeDetails($txnData['at_intake']);
    	
    	
		//get fee structure
		$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
		if(!$this->islocalNationality($txnId)){
			//315 is foreigner in lookup db
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData[0]['IdIntake'],$program["program_id"],315,$branch);
			$biaya = $biaya*2;
			$biaya = number_format($biaya, 2, '.', ',');
		}else{
			//default to local
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData[0]['IdIntake'],$program["program_id"],314,$branch);
			$biaya = number_format($biaya, 2, '.', ',');
		}
		
		//fee structure plan
		$feeStructurePlanDb = new Studentfinance_Model_DbTable_FeeStructurePlan();
		$paymentPlanData = $feeStructurePlanDb->getStructureData($feeStructureData['fs_id']);
		$feeStructureData['payment_plan'] = $paymentPlanData;
		
		//fee structure program
		$feeStructureProgramDb = new Studentfinance_Model_DbTable_FeeStructureProgram();
		$feeStructureProgramData = $feeStructureProgramDb->getStructureData($feeStructureData['fs_id'],$program["program_id"],$branch);
		
    	
		//fee structure plan detail
		$fspdDb = new Studentfinance_Model_DbTable_FeeStructurePlanDetail();
		
		foreach ($feeStructureData['payment_plan'] as $key=>$plan){
			
			for($installment=1; $installment<=$plan['fsp_bil_installment']; $installment++){
				$feeStructureData['payment_plan'][$key]['plan_detail'][$installment] = $fspdDb->getPlanData($plan['fsp_structure_id'], $plan['fsp_id'], $installment, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
			}
		}
		
		/*
		 * paket A
		 */
		//$paket_a_plan = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[0]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		
		/*
		 * paket B
		 */
		//$paket_b_plan_cicilan1 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		//$paket_b_plan_cicilan2 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 2, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		//$paket_b_plan_cicilan3 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 3, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		//$paket_b_plan_cicilan4 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 4, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		//$paket_b_plan_cicilan5 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 5, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		//$paket_b_plan_cicilan6 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 6, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		/*echo "<pre>";
		print_r($paket_b_plan_cicilan1);
		echo "<pre>";
		exit;*/
		  
		//create image
		//$this->createImage();
		
		
    	
		//$nomor = '010/AK.4.02/PSSB-BAA/Usakti/WR.I/I-3/2012';
		$nomor=$assessmentData['aaud_nomor'];
		
		$address = "";
		if( isset($applicant["appl_address1"]) && $applicant["appl_address1"]!=""){
			$address = $address . $applicant["appl_address1"]."<br />";
		}
		if( isset($applicant["appl_address2"]) && $applicant["appl_address2"]!=""){
			$address = $address . $applicant["appl_address2"]."<br />";
		}
		if( isset($applicant["CityName"]) && $applicant["CityName"]!=""){
			$address = $address . $applicant["CityName"]."<br />";
		}
		if( isset($applicant["appl_postcode"]) && $applicant["appl_postcode"]!=""){
			$address = $address . $applicant["appl_postcode"]."<br />";
		}
		if( isset($applicant["StateName"]) && $applicant["StateName"]!=""){
			$address = $address . $applicant["StateName"]."<br />";
		}
		
		$fieldValues = array(
				'$[NO_PES]'=>$txnData["at_pes_id"],
		        '$[NOMOR]'=>$nomor,
				'$[LAMPIRAN]'=>"-",
		        '$[TITLE_TEMPLATE]'=>$translate->_("Pemberitahuan diterima sebagai calon Mahasiswa di Universitas Trisakti"),
		        '$[APPLICANT_NAME]'=>$applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
				'$[PARENTNAME]'=>$father["af_name"],
		        '$[ADDRESS]' =>$address,
				'$ADDRESS1]'=>$applicant["appl_address1"],
				'$ADDRESS2]'=>$applicant["appl_address2"],
				'$[CITY]'=>$applicant["CityName"],
				'$[POSTCODE]'=>$applicant["appl_postcode"],
				'$[STATE]'=>$applicant["StateName"],				
		    	'$[ACADEMIC_YEAR]'=>$txnData['ay_code'],
				'$[PERIOD]'=>$txnData['ap_desc'],
				'$[FACULTY]'=>$program["faculty2"],
				'$[FACULTY_NAME]'=>($facultyData['ArabicName']!=null?$facultyData['ArabicName']." ":"-"),
				'$[FACULTY_SHORTNAME]'=>($facultyData['ShortName']!=null?$facultyData['ShortName']." ":"-"),
				'$[FACULTY_ADDRESS1]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"-"),
				'$[FACULTY_ADDRESS2]'=>($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
				'$[FACULTY_ADDRESS]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"").($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
				'$[FACULTY_PHONE]'=>($facultyData['Phone1']!=null?$facultyData['Phone1']." ":"").($facultyData['Phone2']!=null?", ".$facultyData['Phone2']." ":""),
				'$[FACULTY_FAX]'=>($facultyData['Fax']!=null?$facultyData['Fax']." ":""),
				'$[PROGRAME]'=>$program["program_name_indonesia"],
				'$[KELAS]'=>$program["GroupName"],
				'$[RANK]' => $rank,
		        '$[PRINT_DATE]'=>date('j M Y'),
				'$[REGISTRATION_DATE_START]'=> date ( 'j F Y' , strtotime ( $assessmentData['aaud_reg_start_date'] ) ),
				'$[REGISTRATION_DATE_END]'=> date ( 'j F Y' , strtotime ( $assessmentData['aaud_reg_end_date'] ) ),
				/*'$[PAKET_A_DATE_PAYMENT]'=> date ( 'j F Y' , strtotime ( $assessmentData['aaud_payment_start_date'] ) ),
				'$[PAKET_A_SP]' => number_format($paket_a_plan[0]['total_amount'], 2, '.', ','),
				'$[PAKET_A_BPP_POKOK]' => number_format($paket_a_plan[1]['total_amount'], 2, '.', ','),
				'$[PAKET_A_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_A_BPP_SKS_VALUE]' => number_format($paket_a_plan[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_A_BPP_SKS_AMOUNT]' => number_format($paket_a_plan[2]['total_amount'], 2, '.', ','),
				'$[PAKET_A_PRAKTIKUM]' => number_format($paket_a_plan[3]['total_amount'], 2, '.', ','),
				'$[PAKET_A_TOTAL]' => number_format($paket_a_plan[0]['total_amount'] + $paket_a_plan[1]['total_amount'] + $paket_a_plan[2]['total_amount'] + $paket_a_plan[3]['total_amount'], 2, '.', ',') ,

				'$[PAKET_B_C1_DATE_PAYMENT]'=>date ( 'j F Y' , strtotime ( $assessmentData['aaud_payment_start_date'] ) ),
				'$[PAKET_B_C1_SP]' => number_format($paket_b_plan_cicilan1[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C1_BPP_POKOK]' => number_format($paket_b_plan_cicilan1[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C1_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C1_BPP_SKS_VALUE]' => number_format($paket_b_plan_cicilan1[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C1_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan1[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C1_PRAKTIKUM]' => number_format($paket_b_plan_cicilan1[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C1_TOTAL]' => number_format($paket_b_plan_cicilan1[0]['total_amount'] + $paket_b_plan_cicilan1[1]['total_amount'] + $paket_b_plan_cicilan1[2]['total_amount'] + $paket_b_plan_cicilan1[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C2_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+1 month' , strtotime ( $assessmentData['aaud_reg_start_date'] ) ) ),
				'$[PAKET_B_C2_SP]' => number_format($paket_b_plan_cicilan2[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C2_BPP_POKOK]' => number_format($paket_b_plan_cicilan2[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C2_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C2_BPP_SKS_VALUE]' => number_format($paket_b_plan_cicilan2[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C2_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan2[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C2_PRAKTIKUM]' => number_format($paket_b_plan_cicilan2[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C2_TOTAL]' => number_format($paket_b_plan_cicilan2[0]['total_amount'] + $paket_b_plan_cicilan2[1]['total_amount'] + $paket_b_plan_cicilan2[2]['total_amount'] + $paket_b_plan_cicilan2[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C3_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+2 month' , strtotime ( $assessmentData['aaud_reg_end_date'] ) ) ),
				'$[PAKET_B_C3_SP]' => number_format($paket_b_plan_cicilan3[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_BPP_POKOK]' => number_format($paket_b_plan_cicilan3[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C3_SKS_VALUE]' => number_format($paket_b_plan_cicilan3[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C3_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan3[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_PRAKTIKUM]' => number_format($paket_b_plan_cicilan3[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_TOTAL]' => number_format($paket_b_plan_cicilan3[0]['total_amount'] + $paket_b_plan_cicilan3[1]['total_amount'] + $paket_b_plan_cicilan3[2]['total_amount'] + $paket_b_plan_cicilan3[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C4_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+3 month' , strtotime ( $assessmentData['aaud_reg_end_date'] ) ) ),
				'$[PAKET_B_C4_SP]' => number_format($paket_b_plan_cicilan4[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_BPP_POKOK]' => number_format($paket_b_plan_cicilan4[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C4_SKS_VALUE]' => number_format($paket_b_plan_cicilan4[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C4_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan4[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_PRAKTIKUM]' => number_format($paket_b_plan_cicilan4[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_TOTAL]' => number_format($paket_b_plan_cicilan4[0]['total_amount'] + $paket_b_plan_cicilan4[1]['total_amount'] + $paket_b_plan_cicilan4[2]['total_amount'] + $paket_b_plan_cicilan4[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C5_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+4 month' , strtotime ( $assessmentData['aaud_reg_end_date'] ) ) ),
				'$[PAKET_B_C5_SP]' => number_format($paket_b_plan_cicilan5[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_BPP_POKOK]' => number_format($paket_b_plan_cicilan5[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C5_SKS_VALUE]' => number_format($paket_b_plan_cicilan5[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C5_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan5[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_PRAKTIKUM]' => number_format($paket_b_plan_cicilan5[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_TOTAL]' => number_format($paket_b_plan_cicilan5[0]['total_amount'] + $paket_b_plan_cicilan5[1]['total_amount'] + $paket_b_plan_cicilan5[2]['total_amount'] + $paket_b_plan_cicilan5[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C6_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+5 month' , strtotime ( $assessmentData['aaud_reg_end_date'] ) ) ),
				'$[PAKET_B_C6_SP]' => number_format($paket_b_plan_cicilan6[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_BPP_POKOK]' => number_format($paket_b_plan_cicilan6[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C6_SKS_VALUE]' => number_format($paket_b_plan_cicilan6[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C6_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan6[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_PRAKTIKUM]' => number_format($paket_b_plan_cicilan6[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_TOTAL]' => number_format($paket_b_plan_cicilan6[0]['total_amount'] + $paket_b_plan_cicilan6[1]['total_amount'] + $paket_b_plan_cicilan6[2]['total_amount'] + $paket_b_plan_cicilan6[3]['total_amount'], 2, '.', ',') ,
		
				'$[BALANCE_INSTALLMENT_PAKET_B]' => number_format( ( $paket_a_plan[0]['total_amount'] + $paket_a_plan[1]['total_amount'] + $paket_a_plan[2]['total_amount'] + $paket_a_plan[3]['total_amount'] ) - ( $paket_b_plan_cicilan1[0]['total_amount'] + $paket_b_plan_cicilan1[1]['total_amount'] + $paket_b_plan_cicilan1[2]['total_amount'] + $paket_b_plan_cicilan1[3]['total_amount'] ), 2, '.', ','),
				*/
				'$[SELECTION_TYPE]'=>$type,
				'$[LEARNING_DURATION]' => $learning_duration,
				'$[ESTIMASI_BIAYA]' => $biaya,
				'$[RECTOR_DATE]' => date('j M Y',strtotime($assessmentData['aaud_decree_date']))
		);
		
	
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		$html_template_path = DOCUMENT_PATH."/template/OfferLetterUSM.html";
		
		$html = file_get_contents($html_template_path);
		
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}
		
		//program data
		global $program;
		$program = $feeStructureProgramData;
		
		//registration date
		global $reg_date;
		$reg_date = array(
						'REGISTRATION_DATE_START'=> $assessmentData['aaud_reg_start_date'],
						'REGISTRATION_DATE_END'=> $assessmentData['aaud_reg_end_date']
					);

		//date payment
		foreach($feeStructureData['payment_plan'] as $key=>$plan){
			$start = $assessmentData['aaud_reg_start_date'];
			$end = $assessmentData['aaud_reg_end_date'];
			
			foreach ($plan['plan_detail'] as $key2=>$installment){
				$reg_date['date_payment'][$key][$key2]['start'] = $start;
				$reg_date['date_payment'][$key][$key2]['end'] = $end;

				$end = date ( 'F Y' , strtotime ( '+1 month' , strtotime ( $end) ) );
			}
			
			$end = $assessmentData['aaud_reg_end_date'];
		}	
		
		
		//fee data
		global $fees;
		$fees = $feeStructureData['payment_plan'];
		
		//footer variable
		global $pes;
		$pes = $txnData["at_pes_id"];
		//echo $html;
		//exit;
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		
		//$dompdf->stream($txnData["at_pes_id"]."_offer_letter.pdf");
		$pdf = $dompdf->output();
		//exit;
		
		//$location_path
		$location_path = "applicant/".date("mY")."/".$txnId;
		
		//output_directory_path
		$output_directory_path = DOCUMENT_PATH."/".$location_path;
		
		//create directory to locate file			
		if (!is_dir($output_directory_path)) {
	    	mkdir($output_directory_path, 0775, true);
		}
		
		//output filename 
		$output_filename = $txnData["at_pes_id"]."_offer_letter.pdf";
		
		//to rename output file			
		$output_file_path = $output_directory_path."/".$output_filename;

		file_put_contents($output_file_path, $pdf);
		
		//update file info
		/*$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
		$doc["ad_filepath"]=$location_path;
		$doc["ad_filename"]=$output_filename;
		$documentDB->updateDocument($doc,$txnId,45);*/
		
		//update file info
		$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
		$fileexist = $documentDB->getDataArray($txnId, 45);
		
		$doc["ad_filepath"]=$location_path;
		$doc["ad_filename"]=$output_filename;
		$doc["ad_appl_id"]=$txnId;
		$doc["ad_type"]=45;
		$doc["ad_createddt"]=date("Y-m-d");
		
		if($fileexist){
			$documentDB->updateDocument($doc,$txnId,45);
		}else{
			$documentDB->addData($doc);
		} 
		
		//regenerate performa invoice
		$proformaInvoiceDb = new Application_Model_DbTable_ProformaInvoice();
		$proformaInvoiceDb->regenerateUSMProformaInvoice($txnId);

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
?>