<?php 

class App_Model_Application_DbTable_ApplicantTransaction extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_transaction';
	protected $_primary = "at_trans_id";
	
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
	
	
	public function getData($id=""){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("at_status IN ('APPLY','CLOSE','PROCESS')")
					  ->order("at_trans_id desc");
					  
		if($id)	{			
			 $select->where("at_appl_id ='".$id."'");
			 $row = $db->fetchRow($select);				 
		}	 
		
		 return $row;
	}
	
	
	public function getDataById($id=""){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'applicant_profile'),'a.at_appl_id=b.appl_id')
		 
		->where('at_trans_id=?',$id);
	
		$row=$db->fetchRow($select);
		 
		return $row;
	}
	
	public function getApplicantPaginateData($app_id){
		$app_id = (int)$app_id;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				->from(array('a'=>$this->_name))
			    ->joinLeft(array('b'=>'tbl_academic_year'),'b.ay_id = a.at_academic_year')
			    ->joinLeft(array('c'=>'tbl_academic_period'),'c.ap_id = a.at_period')
			    ->joinLeft(array('d'=>'tbl_intake'),'d.IdIntake = a.at_intake')
				->where('at_appl_id = '. $app_id)
				->order($this->_primary);
		
		
		return $select;
	}
	
	public function getLastTransaction($applicant_id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->order("at_trans_id desc");
					  
		if($applicant_id!=0)	{			
			 $select->where("at_appl_id ='".$applicant_id."'");
			 $row = $db->fetchRow($select);				 
		}	 
		
		 return $row;
	}
	
	public function getTransactionData($transaction_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("at_trans_id = ?", $transaction_id);
					  
		$row = $db->fetchRow($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	public function getTxn($pes_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->from(array('at' => $this->_name))
		->where("at_pes_id = '".$pes_id."'");
	
		$row = $db->fetchRow($select);
	
		return $row;
	}
	public function getTransaction($transaction_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from(array('at'=>$this->_name) )
					  ->joinleft(array('ap'=>'applicant_profile'),'ap.appl_id=at.at_appl_id')
					  ->joinleft(array('ay'=>'tbl_academic_year'),'ay.ay_id=at.at_academic_year')
					  ->joinleft(array('apd'=>'tbl_academic_period'),'apd.ap_id = at.at_period')
					  ->join(array('i'=>'tbl_intake'),'i.IdIntake=at.at_intake',array('intake'=>'IntakeId'))
					  ->where("at.at_trans_id = ".$transaction_id);
					  
		$row = $db->fetchRow($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getApplicantID($admission_type=1,$intake,$testcode=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();		
		if ($admission_type==1) {
			/*
			$select=$db->select()
			->from('appl_pin_to_bank')
			->where("status = 'E'")
			->where('intakeId = ?',$intake)
			->where('aph_code=?',$testcode)
			->order('payee_id');
			$row=$db->fetchRow($select);
			//echo var_dump($row);exit;
			if ($row) {
				$db->update('appl_pin_to_bank',array('status' => 'P'),'payee_id='.$row['PAYEE_ID']);
				return $row['billing_no'];
			} else return '';
			*/
			//create ID and push tagihan ke BNI
			$select=$db->select()
			->from(array('a'=>'tbl_intake'))
			->where('IdIntake=?',$intake);
			$row=$db->fetchRow($select);
			$pre=$row['IntakeUSMcode'];
			//echo $select;
			$select=$db->select()
			->from(array('a'=>'applicant_transaction'),array('billing_no'=>'ifnull(max(substr(at_pes_id,-5)) ,0)+1'))
			->where("at_appl_type = '1'")
			->where('at_intake=?',$intake);
			$row=$db->fetchRow($select);
			//echo $select;exit;
			$no=$row['billing_no']+100000;
			//echo $no;
			$no=substr($no,1, 5);
			//echo $no;
			//echo $pre.$no;exit;
			return $pre.$no;
		} else if ($admission_type==2) {

			$select=$db->select()
			->from(array('a'=>'tbl_intake'),array('IntakePSSBcode'))
			->where('IdIntake=?',$intake);
			$row=$db->fetchRow($select);
		 	$pre=$row['IntakePSSBcode'];
		 	//echo $select;
			$select=$db->select()
				->from(array('a'=>'applicant_transaction'),array('billing_no'=>'ifnull(max(substr(at_pes_id,-5)) ,0)+1'))
				->where("at_appl_type = '2'")
				->where('at_intake=?',$intake); 
				$row=$db->fetchRow($select);
			//echo $select;exit;
			$no=$row['billing_no']+100000;
			//echo $no;
			$no=substr($no,1, 5);
			//echo $no;
			//echo $pre.$no;exit;
			return $pre.$no;
		
		}  else if ($admission_type==3) {

			$select=$db->select()
			->from(array('a'=>'tbl_intake'),array('CreditTransferCode'))
			->where('IdIntake=?',$intake);
			$row=$db->fetchRow($select);
		 	$pre=$row['CreditTransferCode'];
			$select=$db->select()
				//->from(array('a'=>'applicant_transaction'),array('billing_no'=>'ifnull(max(substr(at_pes_id,-5)) ,0)+1'))
				->from(array('a'=>'applicant_transaction'),array('billing_no'=>'ifnull(max(substr(at_pes_id,-6)) ,0)+1'))
			
				->where("at_appl_type = '3'")
				->where('left(at_pes_id,3)=?',$row['CreditTransferCode']); 
				$row=$db->fetchRow($select);
			//$no=$row['billing_no']+100000;
			$no=$row['billing_no']+1000000;
			//echo $no;
			//$no=substr($no,1, 5);
			$no=substr($no,1, 6);
			//echo $no;
			//echo $pre.$no;exit;
			return $pre.$no;
		
		}  else if ($admission_type==4) {

			$select=$db->select()
			->from(array('a'=>'tbl_intake'),array('InvitationCode'))
			->where('IdIntake=?',$intake);
			$row=$db->fetchRow($select);
		 	$pre=$row['InvitationCode'];
			$select=$db->select()
				->from(array('a'=>'applicant_transaction'),array('billing_no'=>'ifnull(max(substr(at_pes_id,-5)) ,0)+1'))
				->where("at_appl_type = '4'")
				->where('at_intake=?',$intake); 
				$row=$db->fetchRow($select);
			$no=$row['billing_no']+100000;
			
			//echo $no;
			$no=substr($no,1, 5);
			
			//echo $no;
			//echo $pre.$no;exit;
			return $pre.$no;
		
		}else if ($admission_type==5) {

			$select=$db->select()
			->from(array('a'=>'tbl_intake'),array('PortofolioCode'))
			->where('IdIntake=?',$intake);
			$row=$db->fetchRow($select);
		 	$pre=$row['PortofolioCode'];
			$select=$db->select()
				->from(array('a'=>'applicant_transaction'),array('billing_no'=>'ifnull(max(substr(at_pes_id,-5)) ,0)+1'))
				->where("at_appl_type in ('5','8','9','10')")
				->where('left(at_pes_id,3)=?',$row['PortofolioCode']); 
				
				$row=$db->fetchRow($select);
			$no=$row['billing_no']+100000;
			//echo $no;
			$no=substr($no,1, 5);
			//echo $no;
			//echo $pre.$no;exit;
			return $pre.$no;
		
		}else if ($admission_type==6) {

			$select=$db->select()
			->from(array('a'=>'tbl_intake'),array('ScholarshipCode'))
			->where('IdIntake=?',$intake);
			$row=$db->fetchRow($select);
		 	$pre=$row['ScholarshipCode'];
			$select=$db->select()
				->from(array('a'=>'applicant_transaction'),array('billing_no'=>'ifnull(max(substr(at_pes_id,-5)) ,0)+1'))
				->where("at_appl_type = '6'")
				->where('left(at_pes_id,3)=?',$row['ScholarshipCode']); 
				$row=$db->fetchRow($select);
			$no=$row['billing_no']+100000;
			//echo $no;
			$no=substr($no,1, 5);
			//echo $no;
			//echo $pre.$no;exit;
			return $pre.$no;
		
		} else if ($admission_type==7) {

			$select=$db->select()
			->from(array('a'=>'tbl_intake'),array('utbkcode'))
			->where('IdIntake=?',$intake);
			$row=$db->fetchRow($select);
		 	$pre=$row['utbkcode'];
			$select=$db->select()
				->from(array('a'=>'applicant_transaction'),array('billing_no'=>'ifnull(max(substr(at_pes_id,-5)) ,0)+1'))
				->where("at_appl_type = '7'")
				->where('at_intake=?',$intake); 
				$row=$db->fetchRow($select);
			$no=$row['billing_no']+100000;
			//echo $no;
			$no=substr($no,1, 5);
			//echo $no;
			//echo $pre.$no;exit;
			return $pre.$no;
		
		} else if ($admission_type==8) {

			$select=$db->select()
			->from(array('a'=>'tbl_intake'),array('PortofolioCode'))
			->where('IdIntake=?',$intake);
			$row=$db->fetchRow($select);
		 	$pre=$row['PortofolioCode'];
			$select=$db->select()
				->from(array('a'=>'applicant_transaction'),array('billing_no'=>'ifnull(max(substr(at_pes_id,-5)) ,0)+1'))
				->where("at_appl_type in ('5','8','9','10')")
				->where('left(at_pes_id,3)=?',$row['PortofolioCode']);  
				$row=$db->fetchRow($select);
			$no=$row['billing_no']+100000;
			//echo $no;
			$no=substr($no,1, 5);
			//echo $no;
			//echo $pre.$no;exit;
			return $pre.$no;
		
		} else if ($admission_type==9) {

			$select=$db->select()
			->from(array('a'=>'tbl_intake'),array('PortofolioCode'))
			->where('IdIntake=?',$intake);
			$row=$db->fetchRow($select);
		 	$pre=$row['PortofolioCode'];
			$select=$db->select()
				->from(array('a'=>'applicant_transaction'),array('billing_no'=>'ifnull(max(substr(at_pes_id,-5)) ,0)+1'))
				->where("at_appl_type in ('5','8','9','10')")
				->where('left(at_pes_id,3)=?',$row['PortofolioCode']); 
				$row=$db->fetchRow($select);
			$no=$row['billing_no']+100000;
			//echo $no;
			$no=substr($no,1, 5);
			//echo $no;
			//echo $pre.$no;exit;
			return $pre.$no;
		
		} else if ($admission_type==10) {

			$select=$db->select()
			->from(array('a'=>'tbl_intake'),array('PortofolioCode'))
			->where('IdIntake=?',$intake);
			$row=$db->fetchRow($select);
		 	$pre=$row['PortofolioCode'];
			$select=$db->select()
				->from(array('a'=>'applicant_transaction'),array('billing_no'=>'ifnull(max(substr(at_pes_id,-5)) ,0)+1'))
				->where("at_appl_type in ('5','8','9','10')")
				->where('left(at_pes_id,3)=?',$row['PortofolioCode']); 
				$row=$db->fetchRow($select);
			$no=$row['billing_no']+100000;
			//echo $no;
			$no=substr($no,1, 5);
			//echo $no;
			//echo $pre.$no;exit;
			return $pre.$no;
		
		}
		//$stmt = $db->query("CALL pr_appl_pesno($admission_type,@vApplicantId,$intake,$testcode)");
		
		//print_r( $stmt);exit;
		//$select = $db->query("SELECT @vApplicantId as applicantID");	 			
		//echo $select;exit;
		//$row = $select->fetchAll();
		//echo var_dump($row);exit;
		//return $row[0]["applicantID"]; 
		
	}
	
	public function checkValidApplicant($txnId, $appl_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("at_trans_id = ?", $txnId)
					  ->where("at_appl_id = ?", $appl_id);
					  
		$row = $db->fetchRow($select);
		
		if($row){
			return true;
		}else{
			return false;
		}
	}
	
	
	public function uniqueApplicantid($at_pes_id){
		
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select = $db ->select()
						  ->from($this->_name)
						  ->where("at_pes_id = '". $at_pes_id."'");
						  
	
			$row = $db->fetchRow($select);	
			 
			if($row){
			 	return false;
			}else{
				return true;	
			}
		}
		
		
public function checkValidAgentApplicant($txnId, $agent_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("at_trans_id = ?", $txnId)
					  ->where("agent_id = ?", $agent_id);
					  
		$row = $db->fetchRow($select);
		
		if($row){
			return true;
		}else{
			return false;
		}
	}
	
	public function getAgentTransactionData($transaction_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$auth = Zend_Auth::getInstance(); 
		$agent_id = $auth->getIdentity()->id; 
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("at_trans_id = ?", $transaction_id)
					  ->where("agent_id = ?", $auth->getIdentity()->id);
					  
		$row = $db->fetchRow($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getProfileID($at_pes_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = "Select at_trans_id,at_appl_id from ".$this->_name." where at_pes_id = $at_pes_id";
		$row = $db->fetchRow($select);
		return $row;			  
	}
	
	public function getProfileDetail($applid){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select= $db->select()
			->from(array("sp"=>'applicant_profile'))
			->joinLeft(array('def'=>'sis_setup_detl'),'def.ssd_id=sp.appl_religion',array('religion'=>'def.ssd_name_bahasa'))
			->joinLeft(array('def1'=>'sis_setup_detl'),'def1.ssd_id=sp.appl_marital_status',array('marital'=>'def1.ssd_name_bahasa'))
			->joinLeft(array('c'=>'tbl_countries'),'sp.appl_nationality=c.idCountry',array('wn'=>'c.CountryName'))
			->joinLeft(array('c1'=>'tbl_countries'),'sp.appl_province=c1.idCountry',array('negara'=>'c1.CountryName'))
			->joinLeft(array('st'=>'tbl_state'),'st.idstate=sp.appl_state',array('propinsi'=>'st.StateName'))
			->joinLeft(array('ct'=>'tbl_city'),'ct.idCity=sp.appl_city')
			->joinLeft(array('c11'=>'tbl_countries'),'sp.appl_cprovince=c11.idCountry',array('cnegara'=>'c11.CountryName'))
			->joinLeft(array('st1'=>'tbl_state'),'st1.idstate=sp.appl_cstate',array('cpropinsi'=>'st1.StateName'))
			->joinLeft(array('ct1'=>'tbl_city'),'ct1.idCity=sp.appl_ccity',array('cCityName'=>'ct1.CityName'))
				
			->where('sp.appl_id=?',$applid);
		$row = $db->fetchRow($select);
		return $row;
	}
	
	public function getProfileDetailPropose($applid){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select= $db->select()
		->from(array("sp"=>'applicant_profile_propose'))
		->joinLeft(array('def'=>'sis_setup_detl'),'def.ssd_id=sp.appl_religion',array('religion'=>'def.ssd_name_bahasa'))
		->joinLeft(array('def1'=>'sis_setup_detl'),'def1.ssd_id=sp.appl_marital_status',array('marital'=>'def1.ssd_name_bahasa'))
		->joinLeft(array('c'=>'tbl_countries'),'sp.appl_nationality=c.idCountry',array('wn'=>'c.CountryName'))
		->joinLeft(array('c1'=>'tbl_countries'),'sp.appl_province=c1.idCountry',array('negara'=>'c1.CountryName'))
		->joinLeft(array('st'=>'tbl_state'),'st.idstate=sp.appl_state',array('propinsi'=>'st.StateName'))
		->joinLeft(array('ct'=>'tbl_city'),'ct.idCity=sp.appl_city')
		->joinLeft(array('c11'=>'tbl_countries'),'sp.appl_cprovince=c11.idCountry',array('cnegara'=>'c11.CountryName'))
		->joinLeft(array('st1'=>'tbl_state'),'st1.idstate=sp.appl_cstate',array('cpropinsi'=>'st1.StateName'))
		->joinLeft(array('ct1'=>'tbl_city'),'ct1.idCity=sp.appl_ccity',array('cCityName'=>'ct1.CityName'))
	
		->where('sp.appl_id=?',$applid);
		$row = $db->fetchRow($select);
		return $row;
	}
	
public function getAgentData($condition=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db ->select()
					  ->from(array('at'=>$this->_name))
					  ->join(array('ap'=>'applicant_profile'),'at.at_appl_id=ap.appl_id')
					  ->joinleft(array("i"=>"tbl_intake"),"at.at_intake=i.IdIntake",array("IntakeDefaultLanguage","IntakeDesc"))
					  ->order("at.at_create_date DESC");
					  
					  
	  					if($condition!=null){
						  	if( isset($condition['intake_id']) && $condition['intake_id']!=""){
								$select->where("at.at_intake = '".$condition['intake_id']."'");
							
								if(isset($condition['period_id']) && $condition['period_id']!=""){
									//load prev period
									if($condition['load_previous_period']==1){
										$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
										$pData = $periodDB->getData($condition['period_id']);
										
										$pList = $periodDB->getPreviousPeriodData($condition['intake_id'], $pData['ap_number']);
										
										$plistStr = array();
										$i=0;
										foreach ($pList as $period){
											$plistStr[$i] = $period['ap_id'];
											$i++;	
										}
										
										$select->where('at.at_period in ('.implode(",", $plistStr).')');
									}else{
											$select->where('at.at_period = ?', $condition['period_id']);
									}
								}
							}
	  						if(isset($condition["name"]) && $condition["name"]!=''){
					   			$select->where("concat(ap.appl_fname,ap.appl_mname,ap.appl_lname) LIKE '%".$condition['name']."%'");	 
					   		}
	  						if(isset($condition["pes_no"]) && $condition["pes_no"]!=''){
					   			$select->where("at.at_pes_id LIKE '".$condition["pes_no"]."'");
					   		}
	  						if( isset($condition['application_type']) && $condition['application_type']!="" && $condition['application_type']!="0" )	{
								$select->where("at.at_appl_type	= '".$condition['application_type']."'");
							}
	  						if( isset($condition['application_status']) && $condition['application_status']!="" && $condition['application_status']!="ALL" )	{
								$select->where("at.at_status = '".$condition['application_status']."'");
							}
					   		if(isset($condition["agent_id"]) && $condition["agent_id"]!=''){
					   			$select->where("at.agent_id ='".$condition["agent_id"]."'");
					   		}
	  						if(isset($condition["entry_type"]) && $condition["entry_type"]!=''){
					   			$select->where("at.entry_type ='".$condition["entry_type"]."'");
					   		}
					   		
					   }			 
		return $row = $db->fetchAll($select);	
	}
	
	public function getAgentPaginateData($condition=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db ->select()
					  ->from(array('at'=>$this->_name))
					  ->joinleft(array('ap'=>'applicant_profile'),'at.at_appl_id=ap.appl_id')
					  ->joinleft(array("i"=>"tbl_intake"),"at.at_intake=i.IdIntake",array("IntakeDefaultLanguage","IntakeDesc"))
					  ->order("at.at_create_date DESC");
					  
	  						if($condition!=null){
						  	if( isset($condition['intake_id']) && $condition['intake_id']!=""){
								$select->where("at.at_intake = '".$condition['intake_id']."'");
							
								if(isset($condition['period_id']) && $condition['period_id']!=""){
									//load prev period
									if($condition['load_previous_period']==1){
										$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
										$pData = $periodDB->getData($condition['period_id']);
										
										$pList = $periodDB->getPreviousPeriodData($condition['intake_id'], $pData['ap_number']);
										
										$plistStr = array();
										$i=0;
										foreach ($pList as $period){
											$plistStr[$i] = $period['ap_id'];
											$i++;	
										}
										
										$select->where('at.at_period in ('.implode(",", $plistStr).')');
									}else{
											$select->where('at.at_period = ?', $condition['period_id']);
									}
								}
							}
	  						if(isset($condition["name"]) && $condition["name"]!=''){
					   			$select->where("concat(ap.appl_fname,ap.appl_mname,ap.appl_lname) LIKE '%".$condition['name']."%'");	 
					   		}
	  						if(isset($condition["pes_no"]) && $condition["pes_no"]!=''){
					   			$select->where("at.at_pes_id LIKE '".$condition["pes_no"]."'");
					   		}
	  						if( isset($condition['application_type']) && $condition['application_type']!="" && $condition['application_type']!="0" )	{
								$select->where("at.at_appl_type	= '".$condition['application_type']."'");
							}
	  						if( isset($condition['application_status']) && $condition['application_status']!="" && $condition['application_status']!="ALL" )	{
								$select->where("at.at_status = '".$condition['application_status']."'");
							}
					   		if(isset($condition["agent_id"]) && $condition["agent_id"]!=''){
					   			$select->where("at.agent_id ='".$condition["agent_id"]."'");
					   		}
	  						if(isset($condition["entry_type"]) && $condition["entry_type"]!=''){
					   			$select->where("at.entry_type ='".$condition["entry_type"]."'");
					   		}
					   		
					   }			 
		//echo $select;
						
		return $select;
	}
	

	public function getAllowEditingData($id=""){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("at_editing_status=1")
					  ->order("at_trans_id desc");
		return $row = $db->fetchAll($select);	
	}
	
	
	public function getApplicantTransaction($appl_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("at_appl_id = '". $appl_id."'");
					  
		$row = $db->fetchAll($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
public function getListTransaction($app_id,$status=null){
		$app_id = (int)$app_id;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				->from(array('a'=>$this->_name))
			    ->joinLeft(array('b'=>'tbl_academic_year'),'b.ay_id = a.at_academic_year')
			    ->joinLeft(array('c'=>'tbl_academic_period'),'c.ap_id = a.at_period')
			    ->joinLeft(array('d'=>'tbl_intake'),'d.IdIntake = a.at_intake')
				->where('at_appl_id = '. $app_id)			
				->order($this->_primary);
				
		if($status!=''){
			$select->where("at_status='".$status."'");
		}
		//echo $select;
		return $select;
	}
	
	
	public function getPaidAndOffer($app_id,$status=null){
		$app_id = (int)$app_id;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				->from(array('a'=>$this->_name))
			    ->joinLeft(array('b'=>'tbl_academic_year'),'b.ay_id = a.at_academic_year')
			    ->joinLeft(array('c'=>'tbl_academic_period'),'c.ap_id = a.at_period')
			    ->joinLeft(array('d'=>'tbl_intake'),'d.IdIntake = a.at_intake')
				->joinLeft(array('pi'=>'applicant_proforma_invoice'),'pi.txn_id = a.at_trans_id')
				->joinLeft(array('pm'=>'payment_main'),'pm.billing_no = pi.billing_no')				                                             
				->where('at_appl_id = '. $app_id)	
				->where("at_status='OFFER'")		
				->order($this->_primary);	
	
		return $select;
	}
	
	public function getPaidAndOfferChangeProgram($app_id,$txnId_from=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
					
	    $select_payment = $db->select()
						->from(array('im'=>'invoice_main'),array('no_fomulir'))
						->joinLeft(array('pi'=>'applicant_proforma_invoice'),'im.bill_number = pi.billing_no',array())
						->where('im.bill_paid>0.00')
						->group('no_fomulir');		
							  
						
		
		$select = $db->select()			   
				->from(array('at'=>$this->_name))
				->join(array('apr'=>'applicant_program'),'apr.ap_at_trans_id  = at.at_trans_id')	
				//->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = apr.ap_prog_code',array('ArabicName')) 
				->where('at_appl_id = '. $app_id)	
				->where("at_status='OFFER'")
				->where("apr.ap_usm_status='1'")
				->where("at_move_id='0'")	
				->where("at_quit_status='0'")	
				->where("at.at_pes_id IN ?",$select_payment)	
				//->group("at.at_trans_id")	
				->order($this->_primary);

		if(isset($txnId_from) && $txnId_from!=null){
			$select->where("at.at_trans_id != '".$txnId_from."'");
		}
		
		$row = $db->fetchAll($select);	
		
		//print_r($row);
		
		$i=0;
		foreach($row as $r){					
			//get rank
			
			if($r["at_appl_type"]==1){
				
				
				$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
	    		$rank = $assessmentDb->getInfo($r["at_trans_id"]);
	    		$row[$i]["rank"] = $rank["aau_rector_ranking"];
	    		
	    		$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
	    		$program = $applicantProgramDb->getProgramOffered($r["at_trans_id"],1);
	    		$row[$i]["ArabicName"] = $program["program_name_indonesia"];
	    		
			}else if($r["at_appl_type"]==2 || $r["at_appl_type"]==3 || $r["at_appl_type"]==4 || $r["at_appl_type"]==5 || $r["at_appl_type"]==6 || $r["at_appl_type"]==7){
				$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
	    		$rank = $assessmentDb->getInfo($r["at_trans_id"]);
	    		$row[$i]["rank"] = $rank["aar_rating_rector"];		

	    		$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
	    		$program = $applicantProgramDb->getProgramOffered($r["at_trans_id"],$r["at_appl_type"]);
	    		$row[$i]["ArabicName"] = $program["program_name_indonesia"];
			}
		//	echo $i.'appl type:'.$r["at_appl_type"].'(((('.$row[$i]["rank"].')))<br>';		
		
		$i++;
		}
		
		
		return $row;
	}
	
	public function getOfferChangeProgram($app_id,$txnId_from=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	    $select = $db->select()
				->from(array('at'=>$this->_name))
				->join(array('apr'=>'applicant_program'),'apr.ap_at_trans_id  = at.at_trans_id')	
				->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = apr.ap_prog_code',array('ArabicName'))
				->where('at_appl_id = '. $app_id)	
				->where("at.at_trans_id != '".$txnId_from."'")
				->where("at_status='OFFER'")
			 	->where ("apr.ap_usm_status=1")	 
				->where("at_move_id='0'")	
				->where("at_quit_status='0'")	
				//->group("at.at_trans_id")	
				->order($this->_primary);
			
		$row = $db->fetchAll($select);	
					
		
		$i=0;
		foreach($row as $key=>$r){				
			//get rank
			
			if($r["at_appl_type"]==1){
				
				if($r["ap_usm_status"]==2 || $r["ap_usm_status"]==0){
					unset($row[$key]);
				}else{				
					$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
		    		$rank = $assessmentDb->getInfo($r["at_trans_id"]);
		    		$row[$i]["rank"] = $rank["aau_rector_ranking"];
				}
	    		
			}else if($r["at_appl_type"]==2){
				
				$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
	    		$rank = $assessmentDb->getInfo($r["at_trans_id"]);
	    		$row[$i]["rank"] = $rank["aar_rating_rector"];				
			}
		
		$i++;
		}
		
		
		return $row;
	}
	
	
	public function getPaidAndOfferList($app_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				->from(array('at'=>$this->_name))
				->joinLeft(array('apr'=>'applicant_program'),'apr.ap_at_trans_id  = at.at_trans_id')	
				->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = apr.ap_prog_code',array('ArabicName','IdProgram'))			   
				->joinLeft(array('pi'=>'invoice_main'),'pi.no_fomulir = at.at_pes_id',array())
				->join(array('pm'=>'payment_main'),'pm.billing_no = pi.bill_number',array())				                                             
				->where('at_appl_id = '. $app_id)	
				->where("at_status='OFFER'")
				//->where("apr.ap_usm_status='1'")
				->where("at_move_id='0'")	
				->where("at_quit_status='0'")	
				->group("at.at_trans_id")	
				->order($this->_primary);
		//echo $select;exit;
		$row = $db->fetchRow($select);	
		
		return $row;
	}
	
	public function getQuitPaidAndOfferList($app_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select_payment = $db->select()
						->from(array('im'=>'invoice_main'),array('no_fomulir'))
						->joinLeft(array('pi'=>'applicant_proforma_invoice'),'im.bill_number = pi.billing_no',array())
						->where('im.bill_paid>0.00')
						->group('no_fomulir');					
						
		
		$select = $db->select()
				->from(array('at'=>$this->_name))
				->joinLeft(array('apr'=>'applicant_program'),'apr.ap_at_trans_id  = at.at_trans_id')	
				->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = apr.ap_prog_code',array('ArabicName'))
				->where('at_appl_id = '. $app_id)	
				->where("at_status='OFFER'")	
				->where("at_move_id='0'")	
				//->where("at_quit_status='0'")	
				->where("at.at_pes_id IN ?",$select_payment)				
				->group("at.at_trans_id")	
				->order($this->_primary);
			
		$row = $db->fetchAll($select);	
		
		return $row;
	}
	
	public function getTotalAllocateSchedule($rds_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				     ->from(array('at'=>$this->_name))
				     ->where('rds_id = ?',$rds_id);
						    
		$row = $db->fetchAll($select);	

		return count($row);
	}
}	
?>