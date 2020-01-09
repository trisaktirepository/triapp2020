<?php 
class icampus_Function_Application_Agreementletter extends Zend_View_Helper_Abstract{

		
		public function generateAgreementLetter($txnId){
				
			    $translate = Zend_Registry::get('Zend_Translate');
			
				//get applicant info		
				$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		    	$applicant = $applicantDB->getAllProfile($txnId);
		    	
		    	//get transaction info
		    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
				$txnData = $applicantTxnDB->getTransaction($txnId);
				
				
				//getapplicantprogram
				$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
				$programDb = new GeneralSetup_Model_DbTable_Program();
				
				if($txnData['at_appl_type']==2 || $txnData['at_appl_type']==4 || $txnData['at_appl_type']==5 || $txnData['at_appl_type']==6 || $txnData['at_appl_type']==7 || $txnData['at_appl_type']==3){
					$program = $appProgramDB->getProgramFaculty($txnId,$txnData['at_appl_type']);
					$branch=$program[0]['IdBranchOffer'];
					$programid=$program[0]['program_id'];
					//program data
					$programData = $programDb->fngetProgramData($program[0]['program_id']);
					
					//get assessment data
					$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
					$ass_data = $assessmentDb->getData($txnId);
		
					$assessmentData = array(
										'nomor' => $ass_data['asd_nomor'],
										'decree_date' => $ass_data['asd_decree_date'],
										'rank' => $ass_data['aar_rating_rector'],
										'registration_start_date' => $ass_data['aar_reg_start_date'],
										'registration_end_date' => $ass_data['aar_reg_end_date'],
										'payment_start_date' => $ass_data['aar_payment_start_date'],
										'payment_end_date' => $ass_data['aar_payment_end_date'],
									);
				}else{
					$program = $appProgramDB->getUsmOfferProgram($txnId);
					$branch=$program['IdBranchOffer'];
					$programid=$program['program_id'];
					//get assessment data
					$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
					$ass_data = $assessmentDb->getData($txnId);
					
					$assessmentData = array(
										'nomor' => $ass_data['aaud_nomor'],
										'decree_date' => $ass_data['aaud_decree_date'],
										'rank' => $ass_data['aau_rector_ranking'],
										'registration_start_date' => $ass_data['aaud_reg_start_date'],
										'registration_end_date' => $ass_data['aaud_reg_end_date'],
										'payment_start_date' => $ass_data['aaud_payment_start_date'],
										'payment_end_date' => $ass_data['aaud_payment_end_date'],
									);
								
					//program data
					$programData = $programDb->fngetProgramData($program['program_id']);
				}
				
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
				$rank_digit = 3;
				if($assessmentData['rank']==1){
					$rank_digit = 1;
					$rank = "1 (Satu)";
					$biaya =$programData['Estimate_Fee_R1']!=null?number_format($programData['Estimate_Fee_R1'], 2, '.', ','):""; 
				}else
				if($assessmentData['rank']==2){
					$rank_digit = 2;
					$rank = "2 (Dua)"; 
					$biaya =$programData['Estimate_Fee_R2']!=null?number_format($programData['Estimate_Fee_R2'], 2, '.', ','):"";
				}else
				if($assessmentData['rank']==3){
					$rank_digit = 3;
					$rank = "3 (Tiga)"; 
					$biaya =$programData['Estimate_Fee_R3']!=null?number_format($programData['Estimate_Fee_R3'], 2, '.', ','):"";
				}else{
					$rank = "3 (Tiga)"; 
					$biaya =$programData['Estimate_Fee_R3']!=null?number_format($programData['Estimate_Fee_R3'], 2, '.', ','):"";
				}
				
				
				
				
				//faculty data
				$collegeMasterDb = new GeneralSetup_Model_DbTable_Collegemaster();
				$facultyData = $collegeMasterDb->fngetCollegemasterData($programData['IdCollege']);
				
				//get applicant parents info
		    	$familyDB =  new App_Model_Application_DbTable_ApplicantFamily();
		    	$father = $familyDB->getData($applicant["appl_id"],20); //father's    	
		    	
		    	//get next intake
		    	$intakeDb = new GeneralSetup_Model_DbTable_Intake();
		    	$intakeData = $intakeDb->fngetIntakeDetails($txnData['at_intake']);
		    	
				//Nomor
				$nomor=$assessmentData['nomor'];
				
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
				if( isset($applicant["appl_postcode"]) && trim($applicant["appl_postcode"])!=""){
					$address = $address .$applicant["appl_postcode"]."<br />";
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
						'$[PARENTJOB]'=>$father["afj_title"],
				        '$[ADDRESS]' =>$address,
						'$ADDRESS1]'=>$applicant["appl_address1"],
						'$ADDRESS2]'=>$applicant["appl_address2"],
						'$[CITY]'=>$applicant["CityName"],
						'$[POSTCODE]'=>$applicant["appl_postcode"],
						'$[STATE]'=>$applicant["StateName"],				
				    	'$[ACADEMIC_YEAR]'=>$txnData['ay_code'],
						'$[PERIOD]'=>$txnData['ap_desc'],
						'$[FACULTY]'=>$programData["IdCollege"],
						'$[FACULTY_NAME]'=>($facultyData['ArabicName']!=null?$facultyData['ArabicName']." ":"-"),
						'$[FACULTY_SHORTNAME]'=>($facultyData['ShortName']!=null?$facultyData['ShortName']." ":"-"),
						'$[FACULTY_ADDRESS1]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"-"),
						'$[FACULTY_ADDRESS2]'=>($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
						'$[FACULTY_ADDRESS]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"").($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
						'$[FACULTY_PHONE]'=>($facultyData['Phone1']!=null?$facultyData['Phone1']." ":"").($facultyData['Phone2']!=null?", ".$facultyData['Phone2']." ":""),
						'$[FACULTY_FAX]'=>($facultyData['Fax']!=null?$facultyData['Fax']." ":""),
						'$[PROGRAME]'=>$programData["ArabicName"],
						'$[RANK]' => $rank,
				        '$[PRINT_DATE]'=>date('j M Y'),
						'$[REGISTRATION_DATE_START]'=> date ( 'j F Y' , strtotime ( $assessmentData['registration_start_date'] ) ),
						'$[REGISTRATION_DATE_END]'=> date ( 'j F Y' , strtotime ( $assessmentData['registration_end_date'] ) ),
						'$[LEARNING_DURATION]' => $learning_duration,
						'$[ESTIMASI_BIAYA]' => $biaya,
						'$[RECTOR_DATE]' => date('j M Y',strtotime($assessmentData['decree_date']))
				);
				
				
				
				require_once 'dompdf_config.inc.php';
				
				$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
				$autoloader->pushAutoloader('DOMPDF_autoload');
				
				$html_template_path = DOCUMENT_PATH."/template/AgreementLetter.html";
				
				$html = file_get_contents($html_template_path);
				
				//replace variable
				foreach ($fieldValues as $key=>$value){
					$html = str_replace($key,$value,$html);	
				}
				
				
				//payment data
				$paymentMainDb = new Studentfinance_Model_DbTable_PaymentMain();
				$payment = $paymentMainDb->getApplicantPaymentInfo($txnData['at_pes_id']);
				
				//get fee structure
				//TODO:check local or foreign
				$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
				if(!$this->islocalNationality($txnId)){
					//315 is foreigner in lookup db
					$fee_structure = $feeStructureDb->getApplicantFeeStructure($intakeData[0]['IdIntake'],$programid,315,$branch);
					$biaya = $biaya*2;
					$biaya = number_format($biaya, 2, '.', ',');
				}else{
					//default to local
					$fee_structure = $feeStructureDb->getApplicantFeeStructure($intakeData[0]['IdIntake'],$programid,314,$branch);
					$biaya = number_format($biaya, 2, '.', ',');
				}
				
				//$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
				//$fee_structure = $feeStructureDb->getApplicantFeeStructure($txnData['at_intake'], $programData['IdProgram']);
				
				//get selected payment plan
				$paymentplanDb = new Studentfinance_Model_DbTable_FeeStructurePlan();
				$payment_plan = $paymentplanDb->getBillingPlan($fee_structure['fs_id'],$payment[0]['billing_no']);
				
				//inject plan detail (installment)
				$paymentPlanDetailDb = new Studentfinance_Model_DbTable_FeeStructurePlanDetail();
				$payment_plan['installment_detail'] = array();
				for($i=1;$i<=$payment_plan['fsp_bil_installment']; $i++){
					$payment_plan['installment_detail'][$i] = $paymentPlanDetailDb->getPlanData($fee_structure['fs_id'], $payment_plan['fsp_id'], $i, 1, $programData['IdProgram'], $assessmentData['rank']);
					
				}
				
				//registration date
				global $reg_date;
				$reg_date = array(
								'REGISTRATION_DATE_START'=> $assessmentData['registration_start_date'],
								'REGISTRATION_DATE_END'=> $assessmentData['registration_end_date']
							);
				
				//date payment
				$start = $assessmentData['registration_start_date'];
				$end = $assessmentData['registration_end_date'];
				
				foreach ($payment_plan['installment_detail'] as $key=>$installment){
					$payment_plan['payment_date'][$key]['start'] = $start;
					$payment_plan['payment_date'][$key]['end'] = $end;
		
					$end = date ( 'F Y' , strtotime ( '+1 month' , strtotime ( $end) ) );
				}
				
				$end = $assessmentData['registration_end_date'];
						
				global $fee;
				$fee = $payment_plan;
				
				global $program_fee_structure;
				$program_fee_structure = $fee_structure;
				
				
				//program data
				global $program;
				$program = $programData;
		
				//footer variable
				global $pes;
				$pes = $txnData["at_pes_id"];
				/*echo $html;
				exit;*/
				
				$dompdf = new DOMPDF();
				$dompdf->load_html($html);
				$dompdf->set_paper('a4', 'potrait');
				@$dompdf->render();
				
				
				//$dompdf->stream($txnData["at_pes_id"]."_agreement_letter.pdf");
				//exit;
				$pdf = @$dompdf->output();
				
				
				//$location_path
				$location_path = "applicant/".date("mY")."/".$txnId;
				
				//output_directory_path
				$output_directory_path = DOCUMENT_PATH."/".$location_path;
				
				//create directory to locate file			
				if (!is_dir($output_directory_path)) {
			    	mkdir($output_directory_path, 0775);
				}
				
				//output filename 
				$output_filename = $txnData["at_pes_id"]."_agreement_letter.pdf";
				
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
				$fileexist = $documentDB->getDataArray($txnId, 50);
				
				$doc["ad_filepath"]=$location_path;
				$doc["ad_filename"]=$output_filename;
				$doc["ad_appl_id"]=$txnId;
				$doc["ad_type"]=50;
				
				if($fileexist){
		
					$documentDB->updateDocument($doc,$txnId,50);
				}else{
		
					$doc['ad_createddt'] = date('Y-m-d');
					$documentDB->addData($doc);
				}
		
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