<?php
 
class App_Model_Smsgateway_DbTable_SmsGateways extends Zend_Db_Table {  
	
	
	protected $_name = 'tbl_sms_message';
	protected $_primary ='idsms';
	
	protected $username='trisakti_api';
	protected $password='trisakti_api2016!';
	protected $_urlsms='http://api.nusasms.com/api/v3/sendsms/plain?';
	protected $_response='http://api.nusasms.com/api/command?'; 
	
	public function getURL() {
		return $this->_urlsms;
	}
	
	public function getURLReport() {
		return $this->_response;
	}
	public function getUser() {
		return $this->username;
	}
	public function getPassword() {
		return $this->password;
	}
	
	public function checkHp($hp) {
		
		//$helper=Zend_Controller_Action_HelperBroker::addHelper("myHelper");
		
		if (substr($hp,0,1)=='-') $hp=substr($hp,1);
		if (substr($hp,0,1)=='0') $hp='62'.substr($hp,1,strlen($hp)-1);
		else if (substr($hp,0,1)=='8') $hp='62'.$hp;
		$hp=trim($hp);
		if (substr($hp,0,1)=='6' && strlen($hp)>=10)   return $hp; else return '';
			 
			 
		 
	}
	public function sendMessage($message,$hp,$kategori,$iduser=null,$idstd=null){
	
		 
		while (strlen($message)>0) {
			if (strlen($message)>160) {
				$message_sms=substr($message, 0,160);
				$message=substr($message, 160);
			} else {
				$message_sms=$message;
				$message='';
			}
	
			$hp=$this->checkHp($hp);
	
			if ($hp!='') {
				$url=$this->getURL();
				//$this->_helper->redirector->gotoUrlAndExit($url);
				$param=array(
						'username'  => $this->getUser(),
						'password'   => $this->getPassword(),
						'GSM' => $hp,
						'SMSText'   => $message_sms,
						'output'=>'json',
						'otp'=>'Y'
				);
				$ret=$this->HttpResponse($url,$param);
				$ret=json_decode($ret,TRUE);
				if ($ret['results'][0]['status']=='0') $status='Success Send';
				/* $param=array(
						'username'  => $dbsms->getUser(),
						'password'   => $dbsms->getPassword(),
						'cmd' => 'DR',
						'ref_no'   => $ret['results'][0]['messageid'],
						'output'=>'json'
				);
				$status=$this->HttpResponse($this->getURLReport(),$param);
				 */
				 $data=array('sms_message'=>$message_sms,
						'dt_entry'=>date('Y-m-d h:i:sa'),
						'no_destination'=>$hp,
						'kategori'=>$kategori,
						'status'=>$status,
						'id_user'=>$iduser,
						'sms_number'=>$ret,
						'idstudentregistration'=>$idstd
				);
				$this->insertData($data);
				
			} else {
				$data=array('sms_message'=>$message,
						'dt_entry'=>date('Y-m-d h:i:sa'),
						'no_destination'=>$hp,
						'kategori'=>$kategori,
						'status'=>'Nomor Hp tidak valid',
						'id_user'=>$iduser,
						'idstudentregistration'=>$idstd
				);
				$this->insertData($data);
				return 'Nomor Hp tidak valid';
			}
			
		}
	
		return $status;
	
	}
	public function HttpResponse($url,$param) {
	
		$client = new Zend_Http_Client();
		$client->setMethod(Zend_Http_Client::POST);
		$client->setUri($url);
		$client->setParameterGet($param);
		$response = $client->request();
		return $response->getBody();
			
	}
	public function sendMessageToStudents($kategori,$message, $variable,$destination) {
		
		//merger variable to tempate if any
		if (count($variable)>0) {
			foreach ($variable as $key=>$value) {
				$message = str_replace($key,$value,$message);
			}
		}
		//get phone number of student
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		//send message one by one
		foreach ($destination as $value) {
			
			$lstrSelect = $lobjDbAdpt->select()
			->from(array('st'=>'tbl_studentregistration'))
			->join(array('sp'=>'student_profile'),'st.IdApplication=sp.appl_id')
			->where('st.IdStudentRegistration=?',$value);
			$std=$lobjDbAdpt->fetchRow($lstrSelect);
			$hp=$std['appl_phone_hp'];
			$this->sendMsg($message,$hp) ;
		}
	}
	
	public function insertData($lobjFormData){
	 
		$db = Zend_Db_Table::getDefaultAdapter();
		$lobjFormData['dt_entry']=date('Y-m-d h:mm:sa');
		$db->insert($this->_name,$lobjFormData);
		$lastInsertId = $this->getAdapter()->lastInsertId();
		return $lastInsertId;
	}
	
	public function updateData($lobjFormData,$id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$lobjFormData['update_date']=date('Y-m-d h:mm:sa');
		$where = 'id_sms='.$id;
		$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function fnGetKategori($type=null) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Kategori Pesan"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("a.DefinitionCode");
		if ($type!=null) $select->where("left(a.DefinitionCode,1) in (?)",$type);
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	
	public function fnGetStudentStatus() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		//->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('a.idDefType = 20')
		->where('a.Status = 1')
		//->where('b.Active = 1')
		->order("a.DefinitionCode");
		 $result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	
	public function fnGetSkr() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'applicant_selection_detl'),array('key'=>'asd_id','value'=>'asd_nomor'))
		//->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('a.asd_type= 2') 
		//->where('b.Active = 1')
		->order("a.asd_decree_date desc ");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	public function fnGetSkrUSM() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'applicant_assessment_usm_detl'),array('key'=>'aaud_id','value'=>'aaud_nomor'))
		//->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('a.aaud_type= 2')
		//->where('b.Active = 1')
		->order("a.aaud_decree_date desc ");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	public function fnGetValue($sql,$id,$student) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		if ($student=='1')
			$select=$sql.'  where IdStudentRegistration='.$id;
		else 
			$select=$sql.'  where at_trans_id='.$id;
		//echo $select;exit;
		$result = $lobjDbAdpt->fetchRow($select);
		return $result;
	}
	
	public function fnReciver($id,$student) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		if ($student=='1') {
			$select=$lobjDbAdpt->select()
				->from(array('std'=>'tbl_studentregistration'),array('reciverid'=>'registrationId'))
				->join(array('app'=>'student_profile'),'std.IdApplication=app.appl_id',array('recivername'=>"CONCAT(appl_fname,' ',appl_mname,' ',appl_lname)"))
				->where('std.IdStudentRegistration=?',$id);
		}
		else {
			$select=$lobjDbAdpt->select()
			->from(array('std'=>'applicant_transaction'),array('reciverid'=>'at_pes_id'))
			->join(array('app'=>'applicant_profile'),'std.at_appl_id=app.appl_id',array('recivername'=>"CONCAT(appl_fname,' ',appl_mname,' ',appl_lname)"))
			->where('std.at_trans_id=?',$id);
		}
			 
		$result = $lobjDbAdpt->fetchRow($select);
		return $result;
	}
	
	public function getDestinationStudent($filter) {
		
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->distinct()
		->from(array('sp'=>'student_profile'),array('hp'=>'appl_phone_hp'))
		->join(array('st'=>'tbl_studentregistration'),'st.IdApplication=sp.appl_id',array('Id'=>'IdStudentRegistration'))
		->where('appl_phone_hp!="" and appl_phone_hp!="0" and appl_phone_hp!="-"');
			
		if ($filter['nim']!='') {
			$select->where('st.registrationid in (?)',$filter['nim']);
		} else {
			if ($filter['program']!=null) {
				$select->where('st.IdProgram=?',$filter['program']);
			}
			
			if ($filter['intake']!=null) {
				$select->where('st.IdIntake=?',$filter['intake']);
			}
			
			if ($filter['stdstatus']!=null) {
				$select->where('st.profileStatus=?',$filter['stdstatus']);
			}
			
			if ($filter['finance']!=null) {
				if ($filter['finance']=='0') {
					//outstanding
					$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id');
					$select->where('in.bill_balance>0');
				}
				if ($filter['finance']=='1') {
					//zero balance
					$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id');
					$select->where('sum(in.bill_balance)=0');
				}
				if ($filter['finance']=='2') {
					//advance
					$select->join(array('in'=>'advance_payment'),'in.advpy_appl_id=sp.appl_id');
					$select->where('in.advpy_total_balance>0');
				} 
				if ($filter['finance']=='3') {
					//outstanding
					$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id');
					$select->where('in.bill_paid >0 and at.at_trans_id not in (select aq_trans_id from applicant_quit)');
				}
				
			}
			if ($filter['ipk']!=null) {
				$select->join(array('in'=>'tbl_student_grade'),'in.sg_idStudentRegistration=st.IdStudentRegistration');
				$select->where('in.sg_semesterid=?',$filter['smt']);
				$select->where('in.sg_all_cgpa'.$filter['ipksign'].$filter['ipk']);
			}
			if ($filter['sks']!=null) {
				$select->join(array('in'=>'tbl_student_grade'),'in.sg_idStudentRegistration=st.IdStudentRegistration');
				$select->where('in.sg_semesterid=?',$filter['smt']);
				$select->where('in.sg_all_cum_credithour'.$filter['skssign'].$filter['sks']);
			}
		
		}
		
		//echo $select;exit;
		$row=$lobjDbAdpt->fetchAll($select);
		//echo var_dump($row);exit;
		return $row;
	}
	
	public function getDestinationAdvisor($filter) {
	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->distinct()
		->from(array('sp'=>'tbl_staffmaster'),array('hp'=>'Mobile'))
		->join(array('st'=>'tbl_studentregistration'),'st.AcademicAdvisor=sp.Idstaff',array('Id'=>'IdStudentRegistration'));
		

		if ($filter['nim']!='') {
			$select->where('st.registrationid in (?)',$filter['nim']);
		} else {
		if ($filter['program']!=null) {
			$select->where('st.IdProgram=?',$filter['program']);
		}
	
		if ($filter['intake']!=null) {
			$select->where('st.IdIntake=?',$filter['intake']);
		}
	
		if ($filter['stdstatus']!=null) {
			$select->where('st.profileStatus=?',$filter['stdstatus']);
		}
	
		if ($filter['finance']!=null) {
			if ($filter['finance']=='0') {
				//outstanding
				$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id');
				$select->where('in.bill_balance>0');
			}
			if ($filter['finance']=='1') {
				//zero balance
				$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id');
				$select->where('sum(in.bill_balance)=0');
			}
			if ($filter['finance']=='2') {
				//advance
				$select->join(array('in'=>'advance_payment'),'in.advpy_appl_id=sp.appl_id');
				$select->where('in.advpy_total_balance>0');
			} 
			if ($filter['finance']=='3') {
				//outstanding
				$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id');
				$select->where('in.bill_paid >0 and at.at_trans_id not in (select aq_trans_id from applicant_quit)');
			}
				
		}
		if ($filter['ipk']!=null) {
			$select->join(array('in'=>'tbl_student_grade'),'in.sg_idStudentRegistration=st.IdStudentRegistration');
			$select->where('in.sg_semesterid=?',$filter['smt']);
			$select->where('in.sg_all_cgpa'.$filter['ipksign'].$filter['ipk']);
		}
		if ($filter['sks']!=null) {
			$select->join(array('in'=>'tbl_student_grade'),'in.sg_idStudentRegistration=st.IdStudentRegistration');
			$select->where('in.sg_semesterid=?',$filter['smt']);
			$select->where('in.sg_all_cum_credithour'.$filter['skssign'].$filter['sks']);
		}
	
		}
	
		//echo $select;exit;
		$row=$lobjDbAdpt->fetchAll($select);
	
		return $row;
	}
	public function getDestinationApplicant($filter) {
	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->distinct()
		->from(array('sp'=>'applicant_profile'),array('hp'=>'appl_phone_hp'))
		->join(array('at'=>'applicant_transaction'),'at.at_appl_id=sp.appl_id',array('Id'=>'at.at_trans_id'))
		->join(array('ap'=>'applicant_program'),'ap.ap_at_trans_id=at.at_trans_id',array())
		->join(array('pr'=>'tbl_program'),'pr.programcode=ap.ap_prog_code',array())
		->where('appl_phone_hp!="" and appl_phone_hp!="0"  and appl_phone_hp!="-"  and at.at_pes_id is not null');

		if ($filter['nim']!='') {
			$select->where('at.at_pes_id in (?)',$filter['nim']);
		} else {
		
		if ($filter['program']!=null) {
			$select->where('pr.IdProgram=?',$filter['program']);
			$select->where("ap.ap_usm_status =  '1' OR MID( at.at_pes_id, 3,1 ) =  '7'");
 
		}
	
		if ($filter['intake']!=null) {
			$select->where('at.at_intake=?',$filter['intake']);
		}
	
		if ($filter['appstatus']!=null) {
			if ($filter['appstatus']=="1")  
				$select->where('at.at_status="OFFER"');
			else if ($filter['appstatus']=="2" ) $select->where('at.at_status="REJECT"');
				
			if ($filter['appstatus']=="3") {
				//unverified
				$select->where('ap.burekol_verify_by is null');
				
			}
			if ($filter['appstatus']=="4") {
				//unreservasion
				$select->where('at.rds_id is null');
			}
			
			if ($filter['appstatus']=="5") {
				//unreservasion
				$select->where('at.at_selection_status="5"');
			}
		}
	
		if ($filter['finance']!=null) {
			if ($filter['finance']=='0') {
				//outstanding
				$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id',array());
 
				$select->where('in.bill_balance>0');
			}
			if ($filter['finance']=='1') {
				//zero balance
				$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id',array());
				$select->where('sum(in.bill_balance)=0');
			}
			if ($filter['finance']=='2') {
				//advance
				$select->join(array('in'=>'advance_payment'),'in.advpy_appl_id=sp.appl_id',array());
				$select->where('in.advpy_total_balance>0');
			}
			 
			if ($filter['finance']=='3') {
				//outstanding
				$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id',array()); 			}
	
				$select->where('in.bill_paid >0');				
			}
		}

		if ($filter['skr']!=null) {
			if (substr($filter['skr'],0,1)=='0') {
				//PSSB
				$select->join(array('aa'=>'applicant_assessment'),'aa.aar_trans_id=at.at_trans_id',array());
				$select->where('aa.aar_rector_selectionid=?',substr($filter['skr'],1));
			}
			if (substr($filter['skr'],0,1)=='1') {
				//USM
				$select->join(array('au'=>'applicant_assessment_usm'),'au.aau_trans_id=at.at_trans_id',array());
				$select->whereOr('au.aau_rector_selectionid=?',substr($filter['skr'],1));
			}
				
		 
			
		}
		//echo $select;exit;
		$row=$lobjDbAdpt->fetchAll($select);

		if ($filter['finance']=='3') {
			foreach ($row as $key=>$item) {
				$select = $lobjDbAdpt->select()
						->from(array('aq'=>'applicant_quit'))
						->where('aq.aq_trans_id=?',$item['Id']);
				$qt=$lobjDbAdpt->fetchRow($select);
				if ($qt) unset($row[$key]);

			}
		}	 
	 
		return $row;
	}
	
	public function getDestinationStudentOT($filter,$ot) {
	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->distinct()
		->from(array('sp'=>'student_profile'),array())
		->join(array('st'=>'tbl_studentregistration'),'st.IdApplication=sp.appl_id',array('Id'=>'IdStudentRegistration'))
		->join(array('f'=>'applicant_family'),'f.af_appl_id=sp.appl_id',array('hp'=>'af_phone'))
		->where('f.af_relation_type=?',$ot)
		->where('af_phone!="" and af_phone!="0"');
		

		if ($filter['nim']!='') {
			$select->where('st.registrationid in (?)',$filter['nim']);
		} else {
		if ($filter['program']!=null) {
			$select->where('st.IdProgram=?',$filter['program']);
		}
	
		if ($filter['intake']!=null) {
			$select->where('st.IdIntake=?',$filter['intake']);
		}
	
		if ($filter['stdstatus']!=null) {
			$select->where('st.profileStatus=?',$filter['stdstatus']);
		}
	
		if ($filter['finance']!=null) {
			if ($filter['finance']=='0') {
				//outstanding
				$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id',array());
				$select->where('in.bill_balance>0');
			}
			if ($filter['finance']=='1') {
				//zero balance
				$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id',array());
				$select->where('sum(in.bill_balance)=0');
			}
			if ($filter['finance']=='2') {
				//advance
				$select->join(array('in'=>'advance_payment'),'in.advpy_appl_id=sp.appl_id',array());
				$select->where('in.advpy_total_balance>0');
			}
 
			if ($filter['finance']=='3') {
				//outstanding
				$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id',array());
				$select->where('in.bill_paid >0');
				}
				
		}
		if ($filter['ipk']!=null) {
			$select->join(array('in'=>'tbl_student_grade'),'in.sg_idStudentRegistration=st.IdStudentRegistration');
			$select->where('in.sg_semesterid=?',$filter['smt']);
			$select->where('in.sg_all_cgpa'.$filter['ipksign'].$filter['ipk']);
		}
		if ($filter['sks']!=null) {
			$select->join(array('in'=>'tbl_student_grade'),'in.sg_idStudentRegistration=st.IdStudentRegistration');
			$select->where('in.sg_semesterid=?',$filter['smt']);
			$select->where('in.sg_all_cum_credithour'.$filter['skssign'].$filter['sks']);
		}
		
		
		}
	
		//echo $select;exit;
		$row=$lobjDbAdpt->fetchAll($select);
	
		return $row;
	}
	public function getDestinationApplicantOT($filter,$ot) {
	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->distinct()
		->from(array('sp'=>'applicant_profile'),array())
		->join(array('at'=>'applicant_transaction'),'at.at_appl_id=sp.appl_id',array('Id'=>'at.at_trans_id'))
		->join(array('f'=>'applicant_family'),'f.af_appl_id=sp.appl_id',array('hp'=>'af_phone'))
		->where('f.af_relation_type=?',$ot)
		->where('af_phone!="" and af_phone!="0"');
		

		if ($filter['nim']!='') {
			$select->where('at.at_pes_id in (?)',$filter['nim']);
		} else {
		if ($filter['program']!=null) {
			$select->join(array('ap'=>'applicant_program'),'ap.ap_at_trans_id=at.at_trans_id')
			->join(array('pr'=>'tbl_program'),'pr.programcode=ap.ap_prog_code');
			$select->where('pr.IdProgram=?',$filter['program']);
		}
	
		if ($filter['intake']!=null) {
			$select->where('at.at_intake=?',$filter['intake']);
		}
	
		if ($filter['appstatus']!=null) {
			if ($filter['appstatus']=="1")  
				$select->where('at.at_status="OFFER"');
			else if ($filter['appstatus']=="2" ) $select->where('at.at_status="REJECT"');
			
			if ($filter['appstatus']=="3") {
				//unverified
				$select->where('ap.burekol_verify_by is null');
	
			}
			if ($filter['appstatus']=="4") {
				//unreservasion
				$select->where('at.rds_id is null');
				
			}
			if ($filter['appstatus']=="5") {
				//unreservasion
				$select->where('at.at_selection_status="5"');
			}
		}
	
		if ($filter['finance']!=null) {
			if ($filter['finance']=='0') {
				//outstanding
				$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id',array());
				$select->where('in.bill_balance>0');
			}
			if ($filter['finance']=='1') {
				//zero balance
				$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id',array());
				$select->where('sum(in.bill_balance)=0');
			}
			if ($filter['finance']=='2') {
				//advance
				$select->join(array('in'=>'advance_payment'),'in.advpy_appl_id=sp.appl_id',array());
				$select->where('in.advpy_total_balance>0');
			}
			if ($filter['finance']=='3') {
				//outstanding
				$select->join(array('in'=>'invoice_main'),'in.appl_id=sp.appl_id',array());
				$select->where('in.bill_paid >0 and at.at_trans_id not in (select aq_trans_id from applicant_quit)');			}
	
			}
		}
		if ($filter['skr']!=null) {
			if (substr($filter['skr'],0,1)=='0') {
				//PSSB
				$select->join(array('aa'=>'applicant_assessment'),'aa.aar_trans_id=at.at_trans_id');
				$select->where('aa.aar_rector_selectionid=?',substr($filter['skr'],1));
			}
			if (substr($filter['skr'],0,1)=='1') {
				//USM
				$select->join(array('au'=>'applicant_assessment_usm'),'au.aau_trans_id=at.at_trans_id');
				$select->whereOr('au.aau_rector_selectionid=?',substr($filter['skr'],1));
			}
	
				
				
		
		}
		$row=$lobjDbAdpt->fetchAll($select);
			
		if ($filter['finance']=='3') {
			foreach ($row as $key=>$item) {
				$select = $lobjDbAdpt->select()
						->from(array('aq'=>'applicant_quit'))
						->where('aq.aq_trans_id=?',$item['Id']);
				$qt=$lobjDbAdpt->fetchRow($select);
				if ($qt) unset($row[$key]);

			}
		}	 

		return $row;
	}
}

?>