<?php
class Studentfinance_Model_DbTable_InvoiceMain extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'invoice_main';
	protected $_primary = "id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('im'=>$this->_name))
					->where("im.id = ?", (int)$id);

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
		->join(array('pi'=>'applicant_proforma_invoice'),'im.bill_number = pi.billing_no')
		->where("pi.payee_id ='".$payer."'");
			
		$row = $db->fetchAll($select);
	
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
}
?>