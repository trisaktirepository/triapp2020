<?php
class Studentfinance_Model_DbTable_FeeStructurePlanDetail extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_structure_plan_detl';
	protected $_primary = "fspd_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fspd'=>$this->_name))
					->where("fspd.fspd_id = '".$id."'");
			
		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getPlanData($fee_structure_id, $fee_structure_plan_id, $installment_no, $sem_no=1, $program_id=0, $rank=3){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 $selectData = $db->select()
					->from(array('fsi'=>'fee_structure_item'))
					->joinLeft(array('fspd'=>'fee_structure_plan_detl'), 'fspd.fspd_item_id = fsi.fsi_id and fspd.fspd_plan_id = '.$fee_structure_plan_id.' and fspd.fspd_installment_no = '.$installment_no)
					->joinLeft(array('fi'=>'fee_item'),'fi.fi_id = fsi.fsi_item_id')
					->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fi.fi_amount_calculation_type', array('calType'=>'d.DefinitionDesc','calTypeBahasa'=>'d.Description'))
					->joinLeft(array('e'=>'tbl_definationms'),'e.idDefinition = fi.fi_frequency_mode',array('freqMode'=>'e.DefinitionDesc','freqModeBahasa'=>'e.Description'))
					->where("fsi.fsi_structure_id = '".$fee_structure_id."'");

				
		$row = $db->fetchAll($selectData);	

		if($row){
			
			if($program_id!=0){
				//calculate total amount
				$i=0;
				foreach ($row as $item){
					//amount
					$row[$i]['amount'] = $this->calculateItemAmount($item, $sem_no,$program_id, $rank);
					
					$row[$i]['total_amount'] = $row[$i]['amount'];
					$i++;
				}
			}
		
			return $row;
		}else{
			return null;
		}
	}
	
	/*
	 * calculate item in payment plan for total amout
	 */
	private function calculateItemAmount($arr_item, $sem, $program_id, $rank){
		$value = 0;
		
		
		//calculation type
		if($arr_item['fi_amount_calculation_type']==300){//fix amount
			
			if($arr_item['fi_frequency_mode'] == 302 && $sem==1){//1st sem
				
				if( isset($arr_item['fspd_percentage']) && $arr_item['fspd_percentage']!=null && $arr_item['fspd_percentage']!="" ){
					$value = $this->calculatePercentage($arr_item,$rank);	
					
				}else
				if( isset($arr_item['fspd_amount']) && $arr_item['fspd_amount']!=null && $arr_item['fspd_amount']!="" ){
					$value = $this->calculateAmount($arr_item,$rank);	
				}
				
				
					
			}else
			if($arr_item['fi_frequency_mode'] == 303){//every sem
				
				if( isset($arr_item['fspd_percentage']) && $arr_item['fspd_percentage']!=null && $arr_item['fspd_percentage']!="" ){
					$value = $this->calculatePercentage($arr_item);	
				}else
				if( isset($arr_item['fspd_amount']) && $arr_item['fspd_amount']!=null && $arr_item['fspd_amount']!="" ){
					$value = $this->calculateAmount($arr_item);	
				}
				
			}else
			if($arr_item['fi_frequency_mode'] == 460){//every year
				
				if( isset($arr_item['fspd_percentage']) && $arr_item['fspd_percentage']!=null && $arr_item['fspd_percentage']!="" ){
					$value = $this->calculatePercentage($arr_item);	
				}else
				if( isset($arr_item['fspd_amount']) && $arr_item['fspd_amount']!=null && $arr_item['fspd_amount']!="" ){
					$value = $this->calculateAmount($arr_item);	
				}
				
			}else
			if($arr_item['fi_frequency_mode'] == 305){//defined sem
				
				//get item sem
				$fsisDb = new Studentfinance_Model_DbTable_FeeStructureItemSemester();
				$fsisSem = $fsisDb->getStructureItemData($arr_item['fsi_id'],$sem);
				
				if($fsisSem['fsis_semester']==$sem){
					if( isset($arr_item['fspd_percentage']) && $arr_item['fspd_percentage']!=null && $arr_item['fspd_percentage']!="" ){
						$value = $this->calculatePercentage($arr_item);	
					}else
					if( isset($arr_item['fspd_amount']) && $arr_item['fspd_amount']!=null && $arr_item['fspd_amount']!="" ){
						$value = $this->calculateAmount($arr_item);	
					}
				}
			}
			
		}else
		if($arr_item['fi_amount_calculation_type']==299){//sks multiplication
			//get sks
			$feeStructureProgramDb = new Studentfinance_Model_DbTable_FeeStructureProgram();
			$feeStructureProgramData = $feeStructureProgramDb->getStructureData($arr_item['fsi_structure_id'],$program_id);
			
			$sks = $feeStructureProgramData['fsp_first_sem_sks'];
			
			if($arr_item['fi_frequency_mode'] == 302){//1st sem
				
				if( isset($arr_item['fspd_percentage']) && $arr_item['fspd_percentage']!=null && $arr_item['fspd_percentage']!="" ){
					$value = $this->calculatePercentage($arr_item);	
				}else
				if( isset($arr_item['fspd_amount']) && $arr_item['fspd_amount']!=null && $arr_item['fspd_amount']!="" ){
					$value = $this->calculateAmount($arr_item);	
				}
				//multiply sem
				$value = $value*$sks;
				
			}else
			if($arr_item['fi_frequency_mode'] == 303){//every sem
				
				if( isset($arr_item['fspd_percentage']) && $arr_item['fspd_percentage']!=null && $arr_item['fspd_percentage']!="" ){
					$value = $this->calculatePercentage($arr_item);	
				}else
				if( isset($arr_item['fspd_amount']) && $arr_item['fspd_amount']!=null && $arr_item['fspd_amount']!="" ){
					$value = $this->calculateAmount($arr_item);	
				}
				
				//multiply sem
				$value = $value*$sks;
				
			}else
			if($arr_item['fi_frequency_mode'] == 305){//defined sem
				//get item sem
				$fsisDb = new Studentfinance_Model_DbTable_FeeStructureItemSemester();
				$fsisSem = $fsisDb->getStructureItemData($arr_item['fsi_id'],$sem);
				
				if($fsisSem['fsis_semester']==$sem){
					if( isset($arr_item['fspd_percentage']) && $arr_item['fspd_percentage']!=null && $arr_item['fspd_percentage']!="" ){
						$value = $this->calculatePercentage($arr_item);	
					}else
					if( isset($arr_item['fspd_amount']) && $arr_item['fspd_amount']!=null && $arr_item['fspd_amount']!="" ){
						$value = $this->calculateAmount($arr_item);	
					}
					
					//multiply sem
					$value = $value*$sks;
				
				}
			}
		}
		
		
		return $value;
	}
	
	/*
	 * Calculate item percentage
	 */
	private function calculatePercentage($arr_item, $rank=3){
		
		$discount = $this->calculateItemDiscountAmount($arr_item, $rank, $arr_item['fsi_amount']);
		
		$amount = $arr_item['fsi_amount'] - $discount;
		
		return ($arr_item['fspd_percentage']/100)*$amount;
	}
	
	/*
	 * Calculate item amout
	 */
	private function calculateAmount($arr_item, $rank=3){
		$discount = $this->calculateItemDiscountAmount($arr_item, $rank, $arr_item['fspd_amount']);
		$amount = $arr_item['fspd_amount'] - $discount;
		
		return $amount;
	}
	
	private function calculateItemDiscountAmount($item, $rank, $fee=0){
		$value = 0;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $selectData = $db->select()
					->from(array('fdr'=>'fee_discount_rank'))
					->where("fdr.fdr_item_id = '".$item['fsi_id']."'")
					->where("fdr.fdr_rank = '".$rank."'");
		
		$row = $db->fetchRow($selectData);
		
		if($row){
			
			if( isset($row['fdr_percentage']) && $row['fdr_percentage']!=null && $row['fdr_percentage']!=0.00 ){
				$value = $fee * ($row['fdr_percentage']/100);	
			}else
			if( isset($row['fdr_amount']) && $row['fdr_amount']!=null && $row['fdr_amount']!=0.00 ){
				$value = $row['fdr_amount'];	
			}else{
				$value = $fee;
			}
		}
		
		return $value;
	}
	
	public function getPaginateData($search=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($search){
			$selectData = $db->select()
					->from(array('fs'=>$this->_name))
					->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fi.fi_amount_calculation_type', array('calType'=>'d.DefinitionDesc','calTypeBahasa'=>'d.Description'))
					->joinLeft(array('e'=>'tbl_definationms'),'e.idDefinition = fi.fi_frequency_mode',array('freqMode'=>'e.DefinitionDesc','freqModeBahasa'=>'e.Description'))
					->where("fi.fi_name LIKE '%".$search['fi_name']."%'")
					->where("fi.fi_name_bahasa LIKE '%".$search['fi_name_bahasa']."%'")
					->where("fi.fi_name_short LIKE '%".$search['fi_name_short']."%'")
					->where("fi.fi_code LIKE '%".$search['fi_code']."%'")
					->where("fi.fi_amount_calculation_type LIKE '%".$search['fi_amount_calculation_type']."%'")
					->where("fi.fi_frequency_mode LIKE '%".$search['fi_frequency_mode']."%'")
					->where("fi.fi_active = 1");	
		}else{
			$selectData = $db->select()
					->from(array('fs'=>$this->_name))
					->joinLeft(array('i'=>'tbl_intake'),'i.IdIntake = fs.fs_intake_start', array('s_IntakeId'=>'','s_Intake'=>'IntakeDesc', 's_intake_bahasa'=>'IntakeDefaultLanguage', 'start_date'=>'ApplicationStartDate', 'end_date'=>'ApplicationEndDate'))
					->joinLeft(array('ii'=>'tbl_intake'),'ii.IdIntake = fs.fs_intake_end', array('s_IntakeId'=>'','e_Intake'=>'IntakeDesc', 'e_intake_bahasa'=>'IntakeDefaultLanguage'))
					->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fs.fs_student_category');
						
		}
			
		return $selectData;
	}
	
		
	public function addData($postData){
		
		$data = array(
		        'fspd_plan_id' => $postData['fspd_plan_id'],
				'fspd_installment_no' => $postData['fspd_installment_no'],
				'fspd_item_id' => $postData['fspd_item_id'],
				'fspd_percentage' => $postData['fspd_percentage']!="null"?$postData['fspd_percentage']:null,
				'fspd_amount' => $postData['fspd_amount']!="null"?$postData['fspd_amount']:null				
		);
			
		return $this->insert($data);
	}		
		

	public function updateData($postData,$id){
		
		$data = array(
				'fspd_percentage' => $postData['fspd_percentage']!="null"&&$postData['fspd_percentage']!=""?$postData['fspd_percentage']:null,
				'fspd_amount' => $postData['fspd_amount']!="null"&&$postData['fspd_amount']!=""?$postData['fspd_amount']:null				
		);
			
		return $this->update($data, "fspd_id = '".$id."'");
	}
	
	public function deleteData($id=null){
		if($id!=null){
			$data = array(
				'fi_active' => 0				
			);
				
			$this->update($data, "fi_id = '".$id."'");
		}
	}	
}

