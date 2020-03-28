<?php
class Studentfinance_Model_DbTable_InvoiceMain extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'invoice_main';
	protected $_primary = "id";
		
	public function updatedata($data,$key) {
		 
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$db->update($this->_name,$data, $key);
	}
	
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('im'=>$this->_name))
					->where("im.id = ?", (int)$id);

		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getDataByVa($va){
		if ($va=="") return false;
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('im'=>$this->_name))
		->where("im.va = ?", $va);
		 
		$row = $db->fetchRow($selectData);
		
		return $row;
	}
	
	public function getInvoiceData($billing_no){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('im'=>$this->_name))
					->where("im.bill_number = '".$billing_no."'");
					
		$row = $db->fetchRow($selectData);

		if(!$row){
			return null;
		}else{
			return $row;	
		}
		
	}
	

	public function getInvoiceDataPendaftaran($billing_no){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('im'=>$this->_name))
					->where("im.no_fomulir = '".$billing_no."' and bill_description like '%Pendaftaran%'");
					
		$row = $db->fetchRow($selectData);

		if(!$row){
			return null;
		}else{
			return $row;	
		}
		
	}
	public function getInvoiceByStd($stdid,$semester,$idactivity){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('im'=>$this->_name))
		->where("im.IdStudentRegistration = ?",$stdid)
		->where("im.semester=?",$semester)
		->where('im.IdActivity=?',$idactivity);
			
		$row = $db->fetchRow($selectData);
	
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
	
	public function getInvoiceDataByFormulir($noform,$addinfo=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('im'=>$this->_name))
		->where("im.no_fomulir = '".$noform."'");
		if ($addinfo!=null) $selectData->where('im.bill_description like "%'.$addinfo.'_%"');
		$row = $db->fetchRow($selectData);
	
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
	
	public function getIssuedInvoiceData($payee, $program_code=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('im'=>$this->_name))
					->where("im.no_fomulir = '".$payee."'");
					
		if($program_code!=null){
			$selectData->where('im.program_code =?',$program_code);
		}

		
		$row = $db->fetchAll($selectData);

		if(!$row){
			return null;
		}else{
			return $row;	
		}
		
	}
	
	/**
	 * 
	 * Return boolean if found any proforma invoice not in invoice main table
	 * @param String $billing_no
	 */
	public function getProformaInvoiceNotInInvoice($billing_no,$payer_id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//select invoice
		/*$select_invoice = $db->select()
					->from(array('im'=>$this->_name), array('im.bill_number'))
					->where("im.bill_number = ?", $billing_no);
					
		//select proforma invoice
		$select_proforma = $db->select()
					->from(array('api'=>'applicant_proforma_invoice'))
					->where("api.billing_no = '".$billing_no."'")
					->where("api.billing_no not in (".$select_invoice.")");
	
		if($payer_id!=null){
			$select_proforma->where('api.payee_id = ?',$payer_id);
		}*/
		
		$select = $db->select()
					->from(array('api'=>'applicant_proforma_invoice'))
					->joinLeft(array('im'=>$this->_name), 'api.billing_no = im.bill_number')
					->where("im.bill_number is null")
					->where('api.billing_no = ?', $billing_no);
		
		//echo $select;
		//exit;
		$row = $db->fetchRow($select);
		
		if(!$row){
			return false;
		}else{
			return true;	
		}
		
	}

	/*
	 * Overite Insert function
	 */
	
	public function insert(array $data){
		
		
		if( !isset($data['date_create'])  ){
			$data['date_create'] = date('Y-m-d H:i:s');
		}
					
		return parent::insert( $data );
	}
	
	public function generateApplicantInvoice($payer_id, $billing, $paid=null){
		
		try {
			
			//get proforma invoice
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('api'=>'applicant_proforma_invoice'))
					->where('api.payee_id = ?', $payer_id)
					->where('api.billing_no = ?', $billing);
			$row = $db->fetchAll($select);
			
			if($row!=null){
				//loop each invoice
				for($i=0; $i<sizeof($row); $i++){
					
					$proforma_invoice = $row[$i];
					
					//check not in invoice table 
					$select = $db->select()
						->from(array('api'=>'applicant_proforma_invoice'))
						->joinLeft(array('im'=>$this->_name), 'api.billing_no = im.bill_number')
						->where("im.bill_number is null")
						->where('api.billing_no = ?', $proforma_invoice['billing_no']);
						

					$row_invoice = $db->fetchRow($select);
					
					if($row_invoice){
						
						$date_invoice = null;
						if( isset($row_invoice['offer_date']) && $row_invoice['offer_date'] != '0000-00-00' ){
							$date_invoice = date('Y-m-d H:i:s', strtotime($row_invoice['offer_date']));
						}
						
						//get transaction data
						$transaction = $this->getTransaction($payer_id);
						
						//get selection rank
						$selection_rank = $this->getSelectionRank($transaction['at_trans_id'], $transaction['at_appl_type']);
						
						//get program data (code & college)
						$program = $this->getProgram($payer_id);
						
						//get fee structure
						$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
						if(!$this->islocalNationality($transaction['at_trans_id'])){
							//315 is foreigner in lookup db
							$fee_structure = $feeStructureDb->getApplicantFeeStructure($transaction['at_intake'],$program['IdProgram'],315);
								
						}else{
							//default to local
							$fee_structure = $feeStructureDb->getApplicantFeeStructure($transaction['at_intake'],$program['IdProgram']);
								
						}
						
						//get selected payment plan
						$paymentplanDb = new Studentfinance_Model_DbTable_FeeStructurePlan();
						$payment_plan = $paymentplanDb->getBillingPlan($fee_structure['fs_id'],$billing);
						
						//inject plan detail (installment)
						$paymentPlanDetailDb = new Studentfinance_Model_DbTable_FeeStructurePlanDetail();
						$payment_plan['installment_detail'] = array();
						for($i=1;$i<=$payment_plan['fsp_bil_installment']; $i++){
							$payment_plan['installment_detail'][$i] = $paymentPlanDetailDb->getPlanData($fee_structure['fs_id'], $payment_plan['fsp_id'], $i, 1, $program['IdProgram'], $selection_rank );
							
							
						}
						
						
						
						//loop each cicilan
						for($i=1;$i<=$payment_plan['fsp_bil_installment']; $i++){
							set_time_limit(0);
							
							//total amount for each cicilan
							$total_invoice_amount = 0;
							foreach ($payment_plan['installment_detail'][$i] as $cicilan){
								$total_invoice_amount = $total_invoice_amount + $cicilan['total_amount'];
							}
							
							//desc cicilan
							$paket_info = "";
							if($payment_plan['fsp_bil_installment']==1){
								$paket_info = "Lunas";
							}else
							if($payment_plan['fsp_bil_installment']>1){
								$paket_info = "Cicilan ".$i;
							}
							
							//insert main bill
							$data = array(
									'bill_number' => $payment_plan['fsp_billing_no'].$i.$payer_id,
									'appl_id' => $transaction['at_appl_id'],
									'no_fomulir' => $payer_id,
									'academic_year' => $transaction['at_academic_year'],
									//'semester' => '',
									'bill_amount' => $total_invoice_amount,
									'bill_description' => $program['ShortName']."-"."P".$selection_rank."-".$payment_plan['fsp_name']." ".$paket_info,
									'college_id' => $program['IdCollege'],
									'program_code' => $program['ProgramCode'],
									'creator' => '1',
									'fs_id' => $payment_plan['fsp_structure_id'],
									'fsp_id' => $payment_plan['fsp_id'],
									'status' => 'A',
									'date_create' => $date_invoice
							);
							$data['bill_balance'] = $total_invoice_amount;
							
							if(isset($paid) && $paid!=null){
								$data['bill_paid'] = $paid;
								$data['bill_balance'] = $total_invoice_amount - $paid;
							}
							
							$main_id = $this->insert($data);
							
							//insert bill detail
							$invoiceDetailDb = new Studentfinance_Model_DbTable_InvoiceDetail();
							foreach ($payment_plan['installment_detail'][$i] as $detail){
								
								$data_detail = array(
												'invoice_main_id' => $main_id,
												'fi_id' => $detail['fi_id'],
												'fee_item_description' => $detail['fi_name_bahasa'],
												'amount' => $detail['amount']
											);
								
								
								$invoiceDetailDb->insert($data_detail);
							}
						}
						
						
						
					}
				}
			}
			
		}catch (Exception $e) {
			
			echo $e->getMessage();
			
			echo "<pre>";
			print_r($e->getTrace());
			echo "</pre>";
			
			throw new Exception('Error in Invoice Main');
		}
	}
	
	public function generateApplicantInvoiceByPaket($payer_id, $paket, $paid=null){
	
		try {
			if ($paket=="A") $paket="0"; else $paket="1";
			//get proforma invoice
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('api'=>'applicant_proforma_invoice'))
			->where('api.payee_id = ?', $payer_id)
			->where('api.billing_no = ?', $billing);
			$row = $db->fetchAll($select);
				
			if($row!=null){
				//loop each invoice
				for($i=0; $i<sizeof($row); $i++){
						
					$proforma_invoice = $row[$i];
						
					//check not in invoice table
					$select = $db->select()
					->from(array('api'=>'applicant_proforma_invoice'))
					->joinLeft(array('im'=>$this->_name), 'api.billing_no = im.bill_number')
					->where("im.bill_number is null")
					->where('api.billing_no = ?', $proforma_invoice['billing_no']);
	
	
					$row_invoice = $db->fetchRow($select);
						
					if($row_invoice){
	
						$date_invoice = null;
						if( isset($row_invoice['offer_date']) && $row_invoice['offer_date'] != '0000-00-00' ){
							$date_invoice = date('Y-m-d H:i:s', strtotime($row_invoice['offer_date']));
						}
	
						//get transaction data
						$transaction = $this->getTransaction($payer_id);
	
						//get selection rank
						$selection_rank = $this->getSelectionRank($transaction['at_trans_id'], $transaction['at_appl_type']);
	
						//get program data (code & college)
						$program = $this->getProgram($payer_id);
	
						//get fee structure
						$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
						if(!$this->islocalNationality($transaction['at_trans_id'])){
							//315 is foreigner in lookup db
							$fee_structure = $feeStructureDb->getApplicantFeeStructure($transaction['at_intake'],$program['IdProgram'],315);
	
						}else{
							//default to local
							$fee_structure = $feeStructureDb->getApplicantFeeStructure($transaction['at_intake'],$program['IdProgram']);
	
						}
	
						//get selected payment plan
						$paymentplanDb = new Studentfinance_Model_DbTable_FeeStructurePlan();
						$payment_plan = $paymentplanDb->getBillingPlan($fee_structure['fs_id'],$billing);
	
						//inject plan detail (installment)
						$paymentPlanDetailDb = new Studentfinance_Model_DbTable_FeeStructurePlanDetail();
						$payment_plan['installment_detail'] = array();
						for($i=1;$i<=$payment_plan['fsp_bil_installment']; $i++){
							$payment_plan['installment_detail'][$i] = $paymentPlanDetailDb->getPlanData($fee_structure['fs_id'], $payment_plan['fsp_id'], $i, 1, $program['IdProgram'], $selection_rank );
								
								
						}
	
	
	
						//loop each cicilan
						for($i=1;$i<=$payment_plan['fsp_bil_installment']; $i++){
							set_time_limit(0);
								
							//total amount for each cicilan
							$total_invoice_amount = 0;
							foreach ($payment_plan['installment_detail'][$i] as $cicilan){
								$total_invoice_amount = $total_invoice_amount + $cicilan['total_amount'];
							}
								
							//desc cicilan
							$paket_info = "";
							if($payment_plan['fsp_bil_installment']==1){
								$paket_info = "Lunas";
							}else
							if($payment_plan['fsp_bil_installment']>1){
								$paket_info = "Cicilan ".$i;
							}
								
							//insert main bill
							$data = array(
									'bill_number' => $payment_plan['fsp_billing_no'].$i.$payer_id,
									'appl_id' => $transaction['at_appl_id'],
									'no_fomulir' => $payer_id,
									'academic_year' => $transaction['at_academic_year'],
									//'semester' => '',
									'bill_amount' => $total_invoice_amount,
									'bill_description' => $program['ShortName']."-"."P".$selection_rank."-".$payment_plan['fsp_name']." ".$paket_info,
									'college_id' => $program['IdCollege'],
									'program_code' => $program['ProgramCode'],
									'creator' => '1',
									'fs_id' => $payment_plan['fsp_structure_id'],
									'fsp_id' => $payment_plan['fsp_id'],
									'status' => 'A',
									'date_create' => $date_invoice
							);
							$data['bill_balance'] = $total_invoice_amount;
								
							if(isset($paid) && $paid!=null){
								$data['bill_paid'] = $paid;
								$data['bill_balance'] = $total_invoice_amount - $paid;
							}
								
							$main_id = $this->insert($data);
								
							//insert bill detail
							$invoiceDetailDb = new Studentfinance_Model_DbTable_InvoiceDetail();
							foreach ($payment_plan['installment_detail'][$i] as $detail){
	
								$data_detail = array(
										'invoice_main_id' => $main_id,
										'fi_id' => $detail['fi_id'],
										'fee_item_description' => $detail['fi_name_bahasa'],
										'amount' => $detail['amount']
								);
	
	
								$invoiceDetailDb->insert($data_detail);
							}
						}
	
	
	
					}
				}
			}
				
		}catch (Exception $e) {
				
			echo $e->getMessage();
				
			echo "<pre>";
			print_r($e->getTrace());
			echo "</pre>";
				
			throw new Exception('Error in Invoice Main');
		}
	}
	
	private function getTransaction($payer_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//get application type
		$selectData = $db->select()
					->from(array('at'=>'applicant_transaction'))
					->where("at.at_pes_id = ?", $payer_id);
			
		$txn_row = $db->fetchRow($selectData);			

		if(!$txn_row){
			return null;
		}else{
			return $txn_row;
		}
	}
	
	private function getProgram($payer_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//get application type
		$selectData = $db->select()
					->from(array('at'=>'applicant_transaction'))
					->where("at.at_pes_id = ?", $payer_id);
							
		$txn_row = $db->fetchRow($selectData);			

		if($txn_row){
			
			$selectData = $db->select()
					->from(array('at'=>'applicant_transaction'),array())
					->join(array('ap'=>'applicant_program'), 'ap.ap_at_trans_id = at.at_trans_id', array())
					->join(array('p'=>'tbl_program'),'p.ProgramCode = ap.ap_prog_code')
					->where("at.at_pes_id = ?", $payer_id);
			
			$row = $db->fetchRow($selectData);
				
			if($txn_row['at_appl_type']==1){
				$selectData->where("ap.ap_usm_status = 1");			
			}

			if(!$row){
				return null;
			}else{
				return $row;
			}
		}else{
			return null;
		}	
	}
	
	private function getSelectionRank($txn_id, $application_type){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($application_type == 1){
			$selectData = $db->select()
					->from(array('aau'=>'applicant_assessment_usm'), array('rank'=>'aau.aau_rector_ranking'))
					->where("aau.aau_trans_id = ?", $txn_id)
					->order('aau.aau_id desc');
		}else
		if($application_type == 2 ||$application_type == 7||$application_type == 4||$application_type == 5||$application_type == 6){
			$selectData = $db->select()
					->from(array('aa'=>'applicant_assessment'), array('rank'=>'aa.aar_rating_rector'))
					->where("aa.aar_trans_id = ?", $txn_id)
					->order('aa.aar_id desc');
		}
		
		$row = $db->fetchRow($selectData);			

		if(!$row){
			return 3;
		}else{
			return $row['rank'];
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
	
	public function getApplicantInvoice($payer){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
		->from(array('im'=>'invoice_main'))
		->join(array('pi'=>'applicant_proforma_invoice'),'im.bill_number = pi.billing_no',array())
		->where("pi.payee_id ='".$payer."'");
		
		$row = $db->fetchAll($select);
		
	
		if(!$row){
			return null;
		}else{
			foreach ($row as $value) {
				$id=$value['id'];
				$select = $db ->select()
				->from(array('im'=>'invoice_detail'))
				->where('im.invoice_main_id=?',$id);
				$det=$db->fetchRow($select);
				if (!$det) return null;
			}
			return $row;
		}
	
	}
	
	public function getAllInvoiceData($billing_no, $active=false){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('im'=>$this->_name))
		->where('im.bill_number = ?', $billing_no);
			
		if($active){
			$selectData->where("im.status = 'A'");
		}
	
		$row = $db->fetchAll($selectData);
	
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
	
	public function getInvoicedProformaData($fomulir, $active=false){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$selectData = $db->select()
		->from(array('im'=>$this->_name))
		->join(array('pi'=>'applicant_proforma_invoice'),'pi.billing_no = im.bill_number', array())
		->where('im.no_fomulir = ?', $fomulir);
			
		if($active){
			$selectData->where("im.status = 'A'");
		}
	
		$row = $db->fetchAll($selectData);
	
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
	public function anyOustandingTillSemester($payee,$semid) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
		//get date current semester
		$semdb=new App_Model_General_DbTable_Semestermaster();
		$cursem=$semdb->fnGetSemestermaster($semid);
		$datestart = $cursem['SemesterMainStartDate'];
		//get all semester
		$semall=$semdb->getAllSemesterByDate($datestart);
		$selectData = $db->select()
		->from(array('im'=>$this->_name),array('balance'=>'SUM(bill_balance)'))
		->where('im.no_fomulir = ?', $payee)
		->where('im.semester in (?)',$semall);
		 
		$row = $db->fetchRow($selectData);
		if ($row)
			return $row['balance'];
		else return 0;
		
	}
	
	public function pushToEColl($idinvoice,$dateexprired,$process=null,$mode=null) {
		date_default_timezone_set('Asia/Bangkok');
		 
		$invoiceDet = new Studentfinance_Model_DbTable_InvoiceDetail();
		$dbInvoice = new Studentfinance_Model_DbTable_InvoiceMain();
		//$dbinvoiceSpc=new Studentfinance_Model_DbTable_InvoiceSpc();
		$bni = new Studentfinance_Model_DbTable_AccessBni();
		$dbProgram=new App_Model_General_DbTable_Program();
		$dbStd=new App_Model_Record_DbTable_StudentRegistration();
		$dbFinance=new App_Model_General_DbTable_Bank();
		$bank=$dbFinance->fnGetBankDetails(1);
		$secretkey=$bank['secret_key'];
		$url=$bank['url_api'];
	
		if ($mode==null) $mode="c";
		$invoice=$dbInvoice->getData($idinvoice);
		//echo var_dump($invoice);exit;
		$idstd=$invoice['IdStudentRegistration'];
		$std=$dbStd->getStudentInfo($idstd);
		$idprogram=$std['IdProgram'];
		$program=$dbProgram->getData($idprogram);
		$clientid=$program['Client_Id'];
		if ($process=='createbilling')
			$va= '8'.$clientid.$invoice['bill_number'];
		else
			$va=$invoice['va'];//$billamount=$invoice['bill_balance'];
		$billamount=$invoice['bill_amount'];
		//get detail
		$invoicedetail=$invoiceDet->getInvoiceDetailBank($idinvoice, $idprogram,$std['IdBranch']);
		$desc=array();
		$amounttotal=0;
		if ($invoicedetail) {
			foreach ($invoicedetail as $det) {
					
				//echo "kode".$kode;
				$amount=$det['amount']*1;
				$amounttotal=$amounttotal+$amount;
				$desc[]=$det['account_code']."_".$det['fi_code']."_".$amount;
					
	
			}
		}
		$desc=implode(';', $desc);
		//echo $amounttotal.'-'.$billamount.' -'.$desc;exit;
			
	
	
		if ($billamount>0 && $amounttotal==$billamount && strlen($desc)<=100) {
			if (!filter_var($std['appl_email'],FILTER_VALIDATE_EMAIL)) $std['appl_email']="" ;
			$invoiceData= array(
	
					'type'=>$process,
					'client_id'=>utf8_decode( $clientid ),
					'trx_id'=>utf8_decode($invoice['bill_number']) , // For chars with accents.
					'trx_amount'=>$billamount,
					'billing_type'=>$mode,
					'customer_name'=>$std['StudentName'],
					'customer_email'=>utf8_decode( $std["appl_email"] ),
					'customer_phone'=>utf8_decode( $std["appl_phone_hp"] ),
					'virtual_account'=>utf8_decode($va),
					'datetime_expired'=>date_format(date_create($dateexprired), 'c'),
					'description'=>$desc,
			);
				
			//
			$respone=$bni->accessBni($clientid, $secretkey, $url, $invoiceData);
	
			if (!isset($respone['status']) && $process=='createbilling')
				$dbInvoice->update(array("va"=>$respone['virtual_account'],"dt_va"=>date('Y-m-d h:i:s'),"Client_id"=>$clientid,"billing_type"=>'c',"Description"=>$desc,'va_expired_dt'=>$dateexprired,"status_remark"=>'ok'), "bill_number ='".$respone['trx_id']."'");
			else if (isset($respone['status'])) {
				$dbInvoice->update(array("status_remark"=>$respone['message']), "bill_number ='".$invoice['bill_number']."'");
					
			}
			//echo var_dump($invoiceData);
			//echo var_dump($respone);exit;
		}
	}
	public function pushToECollForEnrollment($idinvoice,$dateexprired,$process=null,$mode=null,$re=null) {
		date_default_timezone_set('Asia/Bangkok');
			
		$invoiceDet = new Studentfinance_Model_DbTable_InvoiceDetail();
		$dbInvoice = new Studentfinance_Model_DbTable_InvoiceMain();
			
		$bni = new Studentfinance_Model_DbTable_AccessBni();
		 
		$dbAppProfile=new App_Model_Application_DbTable_ApplicantProfile();
		$dbFinance=new App_Model_General_DbTable_Bank();
		$bank=$dbFinance->fnGetBankDetails(1);
		$secretkey=$bank['secret_key'];
		$url=$bank['url_api'];
	
		if ($mode==null) $mode="c";
	 
		$invoice=$dbInvoice->getInvoiceDataByFormulir($idinvoice);
		$applid=$invoice['appl_id'];
		
		$profil=$dbAppProfile->getData($applid);
		//exit;
		//echo var_dump($invoice);exit;
		//$idstd=$invoice['IdStudentRegistration'];
		//$std=$dbStd->getData($idstd);
		//$idprogram=$std['IdProgram'];
		//$program=$dbProgram->getDataDetail($idprogram);
		$clientid='741';//$program['Client_Id'];
		
		if ($process=='createbilling') {
			if ($re=="1" || $invoice['status_remark']!='') {
				// exit;
				$bill=199900000+mt_rand(999999, 9999999);
				//echo $bill;exit;
				$bill=substr($bill, 1,8);
				$dbInvoice->updatedata(array('bill_number'=>$bill), 'bill_number="'.$invoice['bill_number'].'"');
				$invoice=$dbInvoice->getInvoiceDataByFormulir($idinvoice);
			}  
			$bill=$invoice['bill_number'];
			 
			$va= '8'.$clientid.'8888'.$bill;
			
		}
		else
			$va=$invoice['va'];//$billamount=$invoice['bill_balance'];
		$billamount=$invoice['bill_amount']-$invoice['cn_amount'];
		 
		//get detail
		$invoicedetail=$invoiceDet->getInvoiceDetail($invoice['id']);
		$desc=array();
		$amounttotal=0;
		if ($invoicedetail) {
			foreach ($invoicedetail as $det) {
					
				//echo "kode".$kode;
				$amount=$det['amount']*1;
				$amounttotal=$amounttotal+$amount;
				$desc[]='110'."_".'Pendaftaran'."_".$amount-$invoice['cn_amount'];
					
	
			}
			$amounttotal=$amounttotal-$invoice['cn_amount'];
		}
		$desc=implode(';', $desc);
		//echo $amounttotal.'-'.$billamount.' -'.$desc;exit;
			
	
	
		if ($billamount>0 && $amounttotal==$billamount && strlen($desc)<=100) {
			if (!filter_var($profil['appl_email'],FILTER_VALIDATE_EMAIL)) $std['appl_email']="" ;
			$invoiceData= array(
	
					'type'=>$process,
					'client_id'=>utf8_decode( $clientid ),
					'trx_id'=>utf8_decode($invoice['bill_number']) , // For chars with accents.
					'trx_amount'=>$billamount,
					'billing_type'=>$mode,
					'customer_name'=>$profil['appl_fname'].' '.$profil['appl_mname'].' '.$profil['appl_lname'],
					'customer_email'=>utf8_decode( $profil["appl_email"] ),
					'customer_phone'=>utf8_decode( $profil["appl_phone_hp"] ),
					'virtual_account'=>utf8_decode($va),
					'datetime_expired'=>date_format(date_create($dateexprired), 'c'),
					'description'=>$desc,
			);
			
			//
			$respone=$bni->accessBni($clientid, $secretkey, $url, $invoiceData);
	
	
			if (!isset($respone['status']) && $process=='createbilling')
				$dbInvoice->update(array("va"=>$respone['virtual_account'],"dt_va"=>date('Y-m-d h:i:s'),"Client_id"=>$clientid,"billing_type"=>'c',"Description"=>$desc,'va_expired_dt'=>$dateexprired,"status_remark"=>'ok'), "bill_number ='".$respone['trx_id']."'");
			else if (isset($respone['status'])) {
				$dbInvoice->update(array("status_remark"=>$respone['message']), "bill_number ='".$invoice['bill_number']."'");
					
			}
			//echo var_dump($invoiceData);
			//echo var_dump($respone);exit;
			
		}
	}
	
	public function pushToECollForEnrollmentPerBilling($trxid,$billno,$process=null,$mode=null,$re=null) {
		date_default_timezone_set('Asia/Bangkok');
			
		$invoiceDet = new Studentfinance_Model_DbTable_InvoiceDetail();
		$dbInvoice = new Studentfinance_Model_DbTable_InvoiceMain();
		$dbAppProgram=new Application_Model_DbTable_ApplicantProgram();
		$program=$dbAppProgram->getProgramOffered($trxid);
	//echo var_dump($program);exit;
		$bni = new Studentfinance_Model_DbTable_AccessBni();
			
		$dbAppProfile=new App_Model_Application_DbTable_ApplicantProfile();
		$dbFinance=new App_Model_General_DbTable_Bank();
		 
		$bank=$dbFinance->fnGetBankDetails(1);
		$secretkey=$bank['secret_key'];
		$url=$bank['url_api'];
	
		if ($mode==null) $mode="c";
		//echo $billno;exit;
		$invoice=$dbInvoice->getInvoiceData($billno);
		$applid=$invoice['appl_id'];
	
		$profil=$dbAppProfile->getData($applid);
		//exit;
		
		//$idstd=$invoice['IdStudentRegistration'];
		//$std=$dbStd->getData($idstd);
		//$idprogram=$std['IdProgram'];
		//$program=$dbProgram->getDataDetail($idprogram);
		$clientid='741';//$program['Client_Id'];
	
		if ($process=='createbilling') {
			if ($re=="1") {
				// exit;
				$bill=199900000+mt_rand(999999, 9999999);
				//echo $bill;exit;
				$bill=substr($bill, 1,8);
				$dbInvoice->updatedata(array('bill_number'=>$bill), 'bill_number="'.$invoice['bill_number'].'"');
				$invoice=$dbInvoice->getInvoiceData($billno);
			}
			$bill=$invoice['bill_number'];
	
			$va= '8'.$clientid.'88'.$bill;
				
		}
		else
			$va=$invoice['va'];//$billamount=$invoice['bill_balance'];
		$billamount=$invoice['bill_amount']-$invoice['cn_amount'];
		 
		//get detail
		$invoicedetail=$invoiceDet->getInvoiceDetailBank($invoice['id'], $program['program_id'],$program['IdBranchOffer']);
		
		//$invoicedetail=$invoiceDet->getInvoiceDetail($invoice['id']);
		$desc=array();
		$amounttotal=0;
		 
		if ($invoicedetail) {
			foreach ($invoicedetail as $det) {
					
				//echo "kode".$kode;
				$amount=$det['amount']*1;
				$amounttotal=$amounttotal+$amount;
				$desc[]=$det['account_code']."_".$det['fi_code']."_".$amount;
					
	
			}
		}
		$desc=implode(';', $desc);
		
		//echo $amounttotal.'-'.$billamount.' -'.$desc;exit;
	
	
		if ($billamount>0 && $amounttotal==$billamount && strlen($desc)<=100) {
			if (!filter_var($profil['appl_email'],FILTER_VALIDATE_EMAIL)) $std['appl_email']="" ;
			$invoiceData= array(
	
					'type'=>$process,
					'client_id'=>utf8_decode( $clientid ),
					'trx_id'=>utf8_decode($invoice['bill_number']) , // For chars with accents.
					'trx_amount'=>$billamount,
					'billing_type'=>$mode,
					'customer_name'=>$profil['appl_fname'].' '.$profil['appl_mname'].' '.$profil['appl_lname'],
					'customer_email'=>utf8_decode( $profil["appl_email"] ),
					'customer_phone'=>utf8_decode( $profil["appl_phone_hp"] ),
					'virtual_account'=>utf8_decode($va),
					//'datetime_expired'=>date_format(date_create($invoice['va_expired_dt']), 'c'),
					'datetime_expired'=>date_format(date_create('2020-08-10 23:00:00'), 'c'),
					'description'=>$desc,
			);
				
			//
			//echo date_format(date_create($invoice['va_expired_dt']), 'c');
			//echo var_dump($invoiceData);exit;
			$respone=$bni->accessBni($clientid, $secretkey, $url, $invoiceData);
	
	
			if (!isset($respone['status']) && $process=='createbilling')
				$dbInvoice->update(array("va"=>$respone['virtual_account'],"dt_va"=>date('Y-m-d h:i:s'),'va_expired_dt'=>date_format(date_create('2020-08-10 23:00:00'), 'c'),"Client_id"=>$clientid,"billing_type"=>'c',"Description"=>$desc,"status_remark"=>'ok'), "bill_number ='".$respone['trx_id']."'");
			else if (isset($respone['status'])) {
				$dbInvoice->update(array("status_remark"=>$respone['message']), "bill_number ='".$invoice['bill_number']."'");
					
			}
			//echo var_dump($invoiceData);

			//echo var_dump($respone);exit;
				
		} 
	}
	public function isIn($bill){
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$selectData = $db->select()
		->from(array('im'=>$this->_name))
		->where('im.bill_number = ?', $bill);
	
		$row = $db->fetchRow($selectData);
	
		return $row;
	
	}
	
	
	public function isAnyOpenInvoice($idstd){
		
		
		$feeStructure = new Studentfinance_Model_DbTable_FeeStructure();
		$db = Zend_Db_Table::getDefaultAdapter();
	
		//get student id
		$selectData = $db->select()
		->from(array('im'=>'tbl_studentregistration'))
		->join(array('sp'=>'student_profile'),'sp.appl_id=im.IdApplication',array('appl_nationality'))
		->join(array('p'=>'tbl_program'),'p.IdProgram=im.IdProgram')
		->join(array('i'=>'tbl_intake'),'i.IdIntake=im.IdIntake')
		->where('im.IdStudentRegistration = ?', $idstd);
		$std=$db->fetchRow($selectData);
		//get payment activity
	
		$selectData = $db->select()
		->from(array('im'=>'tbl_activity'))
		->join(array('b'=>'tbl_activity_calender'),'im.idActivity=b.IdActivity')
		->join(array('c'=>'tbl_semestermaster'),'c.IdSemesterMaster=b.IdSemesterMain')
		->where('b.IdProgram = ?', $std['IdProgram'])
		->where('c.SemesterMainStartDate>=?',$std['class_start'])
		->where('b.StartDate <= CURDATE()')
		->where('b.EndDate >= CURDATE()')
		->where('im.setter="2"');
		 
		$rows = $db->fetchAll($selectData);
		//echo var_dump($rows);  
		if ($rows) {
			$status="0";
			foreach ($rows as $row) {
				//cek invoice main
				 
				$dbBundle=new Studentfinance_Model_DbTable_BundleFee();
				$bundle=$dbBundle->getCurrentSetup(1, $std['IdCollege'], $std['IdProgram'], $std['IdBranch'], $row['IdSemesterMain'], $row['idActivity'],$std['IdProgramMajoring']);
				//echo var_dump($bundle);exit;
				if ($bundle) {
					if (($row['idActivity']==39 || $row['idActivity']==40 || $row['idActivity']==42)) {
						
						//cek mhs baru
						$selectData = $db->select()
						->from(array('im'=>'tbl_studentsemesterstatus'))
						->join(array('std'=>'tbl_studentregistration'),'std.IdStudentregistration=im.IdStudentregistration')
						->where('im.IdStudentRegistration = ?', $idstd)
						->where('im.idSemesterMain=?',$row['IdSemesterMain']);
						$smt = $db->fetchRow($selectData);
						if ($smt['Level']=="1") {
							//cek pembayaranmahasiswa baru di detail
							$applid=$smt['IdApplication'];
							$selectData = $db->select()
							->from(array('im'=>$this->_name))
							->join(array('det'=>"invoice_detail"),'im.id=det.invoice_main_id')
							->where('im.IdStudentRegistration = ?', $idstd)
							->where('im.appl_id=?',$applid)
							->where('im.bill_balance<bill_amount');
							$smt = $db->fetchRow($selectData);
							if (!$smt)  $mhsbaru="0";
							else $mhsbaru="1";
						} else $mhsbaru="0";
					//get fee structure item
						if ($mhsbaru=="0") {
							if($std['appl_nationality']!=96){
								$student_category = 315;
							}else{
								$student_category = 314;
							}
							$feestrucs =$feeStructure->getApplicantFeeStructure($std['IdIntake'],$std['IdProgram'],$student_category,$std['IdBranch'],$std['IdProgramMajoring']);
							if ($feestrucs) {
								$selectData = $db->select()
								->from(array('fsi'=>'fee_structure_item'),array('fsi_item_id'))
								->where("fsi.fsi_structure_id = '".$feestrucs['fs_id']."'");
								$fiitems = $db->fetchAll($selectData);
								foreach ($fiitems as $itm) {
									$itemsfi[]=$itm['fsi_item_id'];
								}
									
							} 
							//get item detail
							$selectData = $db->select()
							->from(array('a'=>'fee_budle_detail'),array('fee_item'))
							->where("a.idfeebundle = '".$bundle['idfeebundle']."'");
							$bundleDetail = $db->fetchAll($selectData);
							// echo var_dump($bundleDetail);
							if ($bundleDetail) {
									
									foreach ($bundleDetail as $itm) {
										$bundls[]=$itm['fee_item'];
									}
									$items=array_intersect($itemsfi, $bundls);
									if (!empty($items)) {
										foreach ($items as $item) {
											$selectData = $db->select()
											->from(array('a'=>'fee_item'),array('fi_amount_calculation_type','fi_frequency_mode'))
											->where("a.fi_id = '".$item."'");
											$itemdetail = $db->fetchRow($selectData);
											/* if ($itemdetail['fi_frequency_mode']==305) {
												//semester ditetapkan
												$selectData = $db->select()
												->from(array('a'=>'fee_structure_item_semester'))
												->where("a.fsis_item_id = '".$item."'")
												->where("a.fsis_semester = '".$row['IdSemesterMain']."'");
												$itemsem = $db->fetchRow($selectData);
												if ($itemsem) $status="1";
											} */
											/* if ($itemdetail['fi_amount_calculation_type']==459) {
												//tergantung subject
												$subjectset = $db->select()
												->from(array('im'=>'tbl_studentregsubjects'),array('IdSubject'))
												->where('im.IdStudentRegistration=?',$idstd)
												->where('im.IdSemesterMain=?',$row['IdSemesterMain']);
												 
													
												$selectData = $db->select()
												->from(array('a'=>'fee_structure_item_subject'))
												->where("a.fsisub_fsi_id = '".$item."'")
												->where("a.fsisub_subject_id in (".$subjectset.")");
												$subject = $db->fetchRow($selectData);
												if ($subject) $status="1";
											} */
											if ($itemdetail['fi_amount_calculation_type']==299) {
												//tergantung sks
												//cek krs
												$selectData = $db->select()
												->from(array('im'=>'tbl_studentregsubjects'),array('IdSemesterMain'))
												->join(array('sb'=>'tbl_subjectmaster'),'im.IdSubject=sb.IdSubject',array('sks'=>'SUM(CreditHours)','jmlmk'=>'COUNT(*)'))
												->where('im.IdStudentRegistration=?',$idstd)
												->where('im.IdSemesterMain=?',$row['IdSemesterMain'])
												->group('im.IdSemesterMain');
												$rowkrs = $db->fetchRow($selectData);
												//echo var_dump($rowkrs); 
												if ($rowkrs) {
													$selectData = $db->select()
													->from(array('im'=>'invoice_main'))
													->where('im.IdStudentRegistration=?',$idstd)
													->where('im.idactivity=?',$row['idActivity'])
													->where('im.semester=?',$rowkrs['IdSemesterMain']);
													$invoice = $db->fetchRow($selectData);
													
													// echo 'aciviyi='.$row['idActivity'];
													if ($invoice)
													{
														
														$selectData = $db->select()
														->from(array('im'=>'invoice_detail'))
														->join(array('inv'=>'invoice_main'),'im.invoice_main_id=inv.id')
														->join(array('i'=>'fee_item'),'im.fi_id=i.fi_id')
														->where('inv.semester=?',$rowkrs['IdSemesterMain'])
														->where('inv.idactivity=?',$row['idActivity'])
														->where('inv.IdStudentRegistration=?',$idstd);
														$details= $db->fetchAll($selectData);
														foreach ($details as $det) {
															if ($det['fi_amount_calculation_type']==299 || $det['fi_amount_calculation_type']==301 ) {
																$amount[$det['fi_id']]=0;
															}
																
														}
														foreach ($details as $det) {
															if ($det['fi_amount_calculation_type']==299 || $det['fi_amount_calculation_type']==301 ) {
																$amount[$det['fi_id']]=$amount[$det['fi_id']]+$det['amount'];
														
															} else unset($amount[$det['fi_id']]);
														}
														$items=0;
														foreach ($amount as $fiid=>$itemamount) {
															//get fee structure
															$selectData = $db->select()
																->from(array('dt'=>'fee_structure_item'))
																->join(array('fi'=>'fee_item'),'fi.fi_id=dt.fsi_item_id')
																->where('fsi_item_id=?',$fiid)
																->where('dt.fsi_structure_id=?',$feestrucs['fs_id']);
																 
															$feestructure=$db->fetchRow($selectData);
														
															if ($feestructure) {
																if ($feestructure['fi_amount_calculation_type']==299) {
																	//per sks
																	$actualamount=$rowkrs['sks']*$feestructure['fsi_amount'];
																//	echo $actualamount;echo $itemamount;exit;
																	$items=$items+$itemamount;
																} else if ($feestructure['fi_amount_calculation_type']==301) {
																	//per MK
																	$actualamount=$rowkrs['jmlmk']*$feestructure['fsi_amount'];
																	//if ($actualamount-$itemamount>0) $status="1";  
																	$items=$items+$itemamount;
																}
															}
														}
														//echo $actualamount;echo '-';echo $items; exit;
														if ($actualamount-$items>0) return $row['idActivity'];
														
														//echo $status;echo '<br>';
													} else return $row['idActivity'];
														
												}  
											}
											
											
											
										}
										 
										//echo var_dump($row);
									//	if ($status=="1") $activity= $row['idActivity'];
										
									} //else return 0;
								} 
							}
						} else {
								
							$selectData = $db->select()
							->from(array('im'=>'invoice_main'))
							->where('im.IdStudentRegistration=?',$idstd)
							->where('im.semester=?',$row['IdSemesterMain'])
							->where('im.idactivity=?',$row['idActivity']);
							//echo $selectData;
							$rowbpp = $db->fetchRow($selectData);
							//echo $selectData;
							//echo var_dump($rowbpp);exit;
							if (!$rowbpp) {
								//cek mhs baru
								$selectData = $db->select()
								->from(array('im'=>'tbl_studentsemesterstatus'))
								->join(array('std'=>'tbl_studentregistration'),'std.IdStudentregistration=im.IdStudentregistration')
								->where('im.IdStudentRegistration = ?', $idstd)
								->where('im.idSemesterMain=?',$row['IdSemesterMain']);
								$smt = $db->fetchRow($selectData);
								if ($smt['Level']=="1") {
									//cek pembayaranmahasiswa baru di detail
									$applid=$smt['IdApplication'];
									$selectData = $db->select()
									->from(array('im'=>$this->_name))
									->join(array('det'=>"invoice_detail"),'im.id=det.invoice_main_id')
									->where('im.IdStudentRegistration = ?', $idstd)
									->where('im.appl_id=?',$applid)
									->where('im.bill_balance<bill_amount');
									$smt = $db->fetchRow($selectData);
									if (!$smt)  return $row['idActivity'];
								} else return $row['idActivity'];
							}   
							// echo "tetap - ".$status;
						}
					 
					 //else return 0;
				}  
				 
			}
			//echo $activity;exit;
			//if ($status=="1") return $activity;
			//else return 0;
		} 
		return 0;
	}
	
	public function inCompatibilityInvoice($idstd,$idsemester,$idactivity){
	
	
		$feeStructure = new Studentfinance_Model_DbTable_FeeStructure();
		$db = Zend_Db_Table::getDefaultAdapter();
		$restamount=array();
		//get student id
		$selectData = $db->select()
		->from(array('im'=>'tbl_studentregistration'))
		->join(array('sp'=>'student_profile'),'sp.appl_id=im.IdApplication',array('appl_nationality'))
		->join(array('p'=>'tbl_program'),'p.IdProgram=im.IdProgram')
		->where('im.IdStudentRegistration = ?', $idstd);
		$std=$db->fetchRow($selectData);
		//get payment activity
		$selectData = $db->select()
		->from(array('im'=>'invoice_main'),array('bill_amount'=>'SUM(bill_amount)',
				'bill_paid'=>'SUM(bill_paid)',
				'bill_balance'=>'SUM(bill_balance)',
				'cn_amount'=>'SUM(cn_amount)',
				'dn_amount'=>'SUM(dn_amount)','idactivity'
				))
		->where('im.IdStudentRegistration=?',$idstd)
		->where('im.idactivity=?',$idactivity)
		->where('im.semester=?',$idsemester)
		->group('im.idactivity');
		$invoice = $db->fetchRow($selectData);
				//cek invoice main
					
				$dbBundle=new Studentfinance_Model_DbTable_BundleFee();
				$bundle=$dbBundle->getCurrentSetup(1, $std['IdCollege'], $std['IdProgram'], $std['IdBranch'],$idsemester, $idactivity,$std['IdProgramMajoring']);
				//echo var_dump($bundle);exit;
				if ($bundle) {
					//get fee structure item
					if($std['appl_nationality']!=96){
						$student_category = 315;
					}else{
						$student_category = 314;
					}
					$feestrucs =$feeStructure->getApplicantFeeStructure($std['IdIntake'],$std['IdProgram'],$student_category,$std['IdBranch'],$std['IdProgramMajoring']);
					if ($feestrucs) {
						$selectData = $db->select()
						->from(array('fsi'=>'fee_structure_item'),array('fsi_item_id'))
						->where("fsi.fsi_structure_id = '".$feestrucs['fs_id']."'");
						$fiitems = $db->fetchAll($selectData);
						foreach ($fiitems as $itm) {
							$itemsfi[]=$itm['fsi_item_id'];
						}
							
					}
					//get item detail
					$selectData = $db->select()
					->from(array('a'=>'fee_budle_detail'),array('fee_item'))
					->where("a.idfeebundle = '".$bundle['idfeebundle']."'");
					$bundleDetail = $db->fetchAll($selectData);
	
					if ($bundleDetail) {
						$status="0";
						foreach ($bundleDetail as $itm) {
							$bundls[]=$itm['fee_item'];
						}
						$items=array_intersect($itemsfi, $bundls);
						if (!empty($items)) {
							foreach ($items as $item) {
								$selectData = $db->select()
								->from(array('a'=>'fee_item'),array('fi_amount_calculation_type','fi_frequency_mode'))
								->where("a.fi_id = '".$item."'");
								$itemdetail = $db->fetchRow($selectData);
								if ($itemdetail['fi_frequency_mode']==305) {
									//semester ditetapkan
									$selectData = $db->select()
									->from(array('a'=>'fee_structure_item_semester'))
									->where("a.fsis_item_id = '".$item."'")
									->where("a.fsis_semester = '".$idsemester."'");
									$itemsem = $db->fetchRow($selectData);
									//check incompatibility
									
								}
								if ($itemdetail['fi_amount_calculation_type']==459) {
									//tergantung subject
									$subjectset = $db->select()
									->from(array('im'=>'tbl_studentregsubjects'),array('IdSubject'))
									->where('im.IdStudentRegistration=?',$idstd)
									->where('im.IdSemesterMain=?',$idsemester);
									$selectData = $db->select()
									->from(array('a'=>'fee_structure_item_subject'))
									->where("a.fsisub_fsi_id = '".$item."'")
									->where("a.fsisub_subject_id in (".$subjectset.")");
									$subject = $db->fetchRow($selectData);
									//check incompatibility
								}
								if ($itemdetail['fi_amount_calculation_type']==299) {
									//tergantung sks
									//cek krs
									$selectData = $db->select()
									->from(array('im'=>'tbl_studentregsubjects'),array('IdSemesterMain'))
									->join(array('sb'=>'tbl_subjectmaster'),'im.IdSubject=sb.IdSubject',array('sks'=>'SUM(CreditHours)','jmlmk'=>'COUNT(*)'))
									->where('im.IdStudentRegistration=?',$idstd)
									->where('im.IdSemesterMain=?',$idsemester)
									->group('im.IdSemesterMain');
									$rowkrs = $db->fetchRow($selectData);
									//echo var_dump($rowkrs);
									if ($rowkrs) {
										 
										if ($invoice)
										{
											//cek for compatibility
											$selectData = $db->select()
											->from(array('im'=>'invoice_detail'))
											->join(array('inv'=>'invoice_main'),'im.invoice_main_id=inv.id')
											->join(array('i'=>'fee_item'),'im.fi_id=i.fi_id')
											->where('inv.semester=?',$idsemester)
											->where('inv.idactivity=?',$idactivity)
											->where('inv.IdStudentRegistration=?',$idstd);
											$details= $db->fetchAll($selectData);
											foreach ($details as $det) {
												if ($det['fi_amount_calculation_type']==299 || $det['fi_amount_calculation_type']==301 ) {
													$amount[$det['fi_id']]=0;
												}
													
											}
											foreach ($details as $det) {
												if ($det['fi_amount_calculation_type']==299 || $det['fi_amount_calculation_type']==301 ) {
													$amount[$det['fi_id']]=$amount[$det['fi_id']]+$det['amount'];
														
												} else unset($amount[$det['fi_id']]);
											}
												
											foreach ($amount as $fiid=>$itemamount) {
												//get fee structure
												$selectData = $db->select()
												->from(array('dt'=>'fee_structure_item'))
												->join(array('fi'=>'fee_item'),'fi.fi_id=dt.fsi_item_id')
												->where('fsi_item_id=?',$fiid)
												->where('dt.fsi_structure_id=?',$feestrucs['fs_id']);
	
												$feestructure=$db->fetchRow($selectData);
													
												if ($feestructure) {
													if ($feestructure['fi_amount_calculation_type']==299) {
														//per sks
														$actualamount=$rowkrs['sks']*$feestructure['fsi_amount'];
														//	echo $actualamount;echo $itemamount;exit;
														if ($actualamount-$itemamount>0) {
															$restamount[$fiid]['amount']=$actualamount-$itemamount;
															$restamount[$fiid]['fi_name_bahasa']=$feestructure['fi_name_bahasa'];
														}
													} else if ($feestructure['fi_amount_calculation_type']==301) {
														//per MK
														if ($actualamount-$itemamount>0) {
															$actualamount=$rowkrs['jmlmk']*$feestructure['fsi_amount'];
															$restamount[$fiid]['amount']=$actualamount-$itemamount;
															$restamount[$fiid]['fi_name_bahasa']=$feestructure['fi_name_bahasa'];
														}
													}
												}
											}
	
										}  
											
									}
								}
	
	
	
							}
							 
						} //else return 0;
					} //else return 0;
				}
		return $restamount;
	}
	
	public function isInByActivity($idsemester,$idstd,$idactivity){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('im'=>$this->_name))
		->where('im.IdStudentRegistration = ?', $idstd)
		->where('im.idactivity = ?', $idactivity)
		->where('im.semester = ?', $idsemester);
		//echo $selectData;exit;
		$row = $db->fetchRow($selectData);
		if (!$row) {
			$selectData = $db->select()
			->from(array('im'=>'tbl_studentsemesterstatus'))
			->join(array('std'=>'tbl_studentregistration'),'std.IdStudentregistration=im.IdStudentregistration')
			->where('im.IdStudentRegistration = ?', $idstd)
			->where('im.idSemesterMain=?',$idsemester);
			$smt = $db->fetchRow($selectData);
			if ($smt) {
				if ($smt['Level']=="1") {
					//cek pembayaranmahasiswa baru di detail
					$applid=$smt['IdApplication'];
					$selectData = $db->select()
					->from(array('im'=>$this->_name))
					->join(array('det'=>"invoice_detail"),'im.id=det.invoice_main_id')
					//->where('im.IdStudentRegistration = ?', $idstd)
					->where('im.appl_id=?',$applid)
					->where('im.bill_balance<bill_amount');
					$row = $db->fetchRow($selectData);
					if ($row) {
						$row['mhsbaru']="1";
						
					}  
				}
			}  
		}  
		
		return $row;
	
	}
	public  function getInvoiceFee($idsemester,$idRegistration, $feeStructureId,$feeitem,$percentage,$allmode="",$idactivity){
			
			
		$dbInvoiceDeatil=new Studentfinance_Model_DbTable_InvoiceDetail();
		$percentage=$percentage/100;
		//registration detail
		$studentRegistrationDb = new App_Model_Record_DbTable_StudentRegistration();
		$registrationData = $studentRegistrationDb->getStudentInfo($idRegistration);
	
		//student profile
		$studentProfileDb = new App_Model_Student_DbTable_StudentProfile();
		$profile = $studentProfileDb->getData($registrationData['IdApplication']);
				
		//fee structure detail
		$feeStructureItemDb = new Studentfinance_Model_DbTable_FeeStructureItem();
		$fee_item = $feeStructureItemDb->getStructureData($feeStructureId);
	
		//get registered course in particular semester
		$subjectRegisterDb = new App_Model_Record_DbTable_StudentRegSubjects();
		//if ($ses_batch_invoice->ulang=="0")
		$registered_subject = $subjectRegisterDb->getUnInvoiceRegisteredSubject($idRegistration,$idsemester, '1,3');
		//else
		//	$registered_subject = $subjectRegisterDb->getUnInvoiceRepeatRegisteredSubject($idRegistration,$semester, '1,3');
			
		$registered_subject=array_values($registered_subject);
		//semester info
		$semesterDb = new App_Model_General_DbTable_Semestermaster();
		$semester = $semesterDb->fngetSemestermainDetails($idsemester);
		//echo var_dump($fee_item); echo $feeitem;exit;
		//echo $feeitem;
		//filter only selected fee item
		foreach ($fee_item as $index=>$fee){
			if($fee['fi_id']!=$feeitem){
				unset($fee_item[$index]);
			}
	
		}
		
		if ($allmode=="1") {
			//get uncalculate payment
			$recordfee=$dbInvoiceDeatil->getInvoiceDetailByActivity($idsemester, $idRegistration,$idactivity );
			//echo var_dump($recordfee);exit;
			if ($recordfee) {
				foreach ($recordfee as $recfee) {
					$recfees[]=$recfee['fi_id'];
				}
				foreach ($fee_item as $index=>$fee){
					if ($fee['fi_id']==2 ||
					$fee['fi_id']==6 ||
					$fee['fi_id']==7 ||
					$fee['fi_id']==10 ||
					$fee['fi_id']==11  
					) {
						if( in_array($fee['fi_id'],$recfees) ){
							unset($fee_item[$index]);
						}
					}
				}
			}
		}
		//get current semester level
		$db = Zend_Db_Table::getDefaultAdapter();
	//get current semester level
						$sql = $db->select()
						  ->from(array('sss' => 'tbl_studentsemesterstatus'), array('Level'))
							->join(array('b'=>'tbl_semestermaster'),'b.IdSemesterMaster=sss.IdSemesterMain')
						  ->where('sss.IdStudentRegistration  = ?', $idRegistration)
						  ->where('b.IdSemesterMaster=?',$idsemester);
						   
						  $result = $db->fetchRow($sql);
						  if (!$result) {
						  
						  		$semselect=$db->select()
						  		->from('tbl_semestermaster')
						  		->where('IdSemesterMaster=?',$idsemester);
						  		$sem=$db->fetchRow($semselect);
						  		
							  $sql = $db->select()
							  ->from(array('sss' => 'tbl_studentsemesterstatus'), array(new Zend_Db_Expr('max(Level) as Level')))
							  ->join(array('b'=>'tbl_semestermaster'),'b.IdSemesterMaster=sss.IdSemesterMain')
							  ->where('sss.IdStudentRegistration  = ?', $idRegistration)
							  ->where('b.SemesterMainStartDate<= ?',$sem['SemesterMainStartDate']);
							  
							  $result = $db->fetchRow($sql);
							  if (!$result) $result['Level']=1;
							  else $result['Level']=$result['Level'];
							  //echo $sql;
						  } 
							if( $result['Level'] ){
								$student_sem = $result['Level']+1;
							}else{
								//check if senior student then hardcode level
								$intake_year = substr($intake['IntakeId'], 0,4);
								$cur_sem_year = substr($semester['SemesterMainCode'], 0,4);
						
								if($intake_year<2013){
									$current_level=0;
						
									while($intake_year<=$cur_sem_year){
										//check current gasal or genap for currencty
										if($intake_year == $cur_sem_year){
						
											if($semester['SemesterCountType']==1){
												$student_sem += 1;
											}else{
												$student_sem += 2;
											}
						
										}else{
											$student_sem+= 2;
										}
						
										$intake_year++;
									}
						
									//remove 1 because we will add 1 in view
									$student_sem -= 1;
						
								}else{
									$student_sem = 0;
						
									//unset($student_list[$i]);
								}
							}
	
		//get fee item frequency type
		$sem_fee_item = array();
		/* if ($feeitem==9) {echo $student_sem;
			echo var_dump($fee_item);exit;
		}  
		  */
		foreach ($fee_item as $fs){
	
			//1st sem
			if($student_sem==1 && $fs['fi_frequency_mode']== 302 ){
				$sem_fee_item[] = $fs;
			}
	
			//every sem
			if($fs['fi_frequency_mode']== 303 || $fs['fi_frequency_mode']== 453){
				$sem_fee_item[] = $fs;
			}
	
			//every senior semester
			if($student_sem>0 && $fs['fi_frequency_mode']== 304){
				$sem_fee_item[] = $fs;
			}
	
			//defined semester
			if($fs['fi_frequency_mode']== 305){
					
				foreach ($fs['semester'] as $sem_defined){
					if($sem_defined['fsis_semester'] == $student_sem){
						$sem_fee_item[] = $fs;
					}
				}
			}
	
			//every gasal regular
			if($fs['fi_frequency_mode']== 460 && $semester['SemesterCountType']==1 && $semester['SemesterFunctionType']==0){
				$sem_fee_item[] = $fs;
			}
	
		}
			
		//get fee item calculation type
		$invoice['amount']=0.00;
		$regid=array();
		
		 
		foreach ($sem_fee_item as $item){
	
			//nilai tetap
			if($item['fi_amount_calculation_type']==300){
				$invoice['amount'] +=  $item['fsi_amount']*$percentage;
				$item['total_amount'] = $item['fsi_amount']*$percentage;
				$invoice['fee_item'][] = $item;
			}else
	
			//pendaraban SKS
			if($item['fi_amount_calculation_type']==299){
				$total_sks = 0;
	
				for($i=0; $i<sizeof($registered_subject); $i++){
					$total_sks +=$registered_subject[$i]['CreditHours'];
					$regid[$registered_subject[$i]['IdStudentRegistration']][]=$registered_subject[$i]['IdStudentRegSubjects'];
				}
				// echo var_dump($regid);exit;
				if($total_sks!=0){
					$invoice['amount'] +=  $item['fsi_amount']*$total_sks*$percentage;
					$item['total_amount'] = $item['fsi_amount']*$total_sks*$percentage;
					$item['total_sks'] = $total_sks;
					$invoice['fee_item'][] = $item;
				}
					
			}else
	
			//pendaraban jumlah subject
			if($item['fi_amount_calculation_type']==301){
				$total_subject = sizeof($registered_subject);
					
				if($total_subject!=0){
					for($i=0; $i<sizeof($registered_subject); $i++){
						$regid[$registered_subject[$i]['IdStudentRegistration']][]=$registered_subject[$i]['IdStudentRegSubjects'];
					}
					$invoice['amount'] +=  $item['fsi_amount']*$total_subject*$percentage;
					$item['total_amount'] = $item['fsi_amount']*$total_subject*$percentage;
					$item['total_subject'] = $total_subject;
					$invoice['fee_item'][] = $item;
				}
			} else
	
			//registered subject
			if($item['fi_amount_calculation_type']==459){
					
				$item['total_amount'] = 0;
	
				//put registered subject subject id as key
				$arr_registered_subject = array();
				for($i=0; $i<sizeof($registered_subject); $i++){
					$arr_registered_subject[$registered_subject[$i]['IdSubject']] = $registered_subject[$i];
				}
	
				/* echo var_dump($arr_registered_subject);
			 	echo "<br>";echo "-----";
				echo var_dump($item['subject']);
				exit; */
				if(isset($item['subject'])){
					//$itemamount=0;
					foreach ($item['subject'] as $subject){
	
						if( isset($arr_registered_subject[$subject['fsisub_subject_id']]) ){
							//echo $subject['fsisub_subject_id']."<br>";
							//echo $item['fsi_amount']."<br>";
							// $itemamount+=$item['fsi_amount']*$percentage;
							$invoice['amount'] +=  $item['fsi_amount']*$percentage;
							$item['total_amount'] += $item['fsi_amount']*$percentage;
						}
	
					}
					// echo var_dump($invoice);exit;
	
				}
	
				if($item['total_amount']>0){
					$invoice['fee_item'][] = $item;
	
	
				}
	
	
					
			}
	
		}
		if ( $regid!=array())  $invoice['regid'] = $regid;
		return $invoice;
	}
	
	public function dispatcher($idstd,$idactivity) {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
		$redirector->gotoUrl('/studentfinance/invoice/generate-std-invoice/id/'.$idstd.'/idactivity/'.$idactivity);
	}
	
	
}
?>
