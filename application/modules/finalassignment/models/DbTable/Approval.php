<?php 
class Finalassignment_Model_DbTable_Approval extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_Approval';
	protected $_primary='IdTAApproval';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		$data = array(
				'IdTAApplication' => $postData['IdTAApplication'],
				'IdTAFlow' => $postData['IdTAFlow'],
				'Sequence' => $postData['Sequence'],
				'StaffAs' => $postData['StaffAs'],
				'approval_type' => $postData['approval_type'],
				'remark'=> $postData['remark'],
				'Approved_by' => $postData['Approved_by'],
				'dt_latest'=> $postData['dtlatest'],
				'dt_entry' => date('Y-m-d H:i:s'),
				'Id_User' =>  $postData['Id_User']
		);
			
		return $this->insert($data);
	}
	
	public function add($postData){
		 
		return $this->insert($postData);
	}
	public function updateFile($postData, $id) {
	
		$this->update($postData, $this->_primary .' = '. (int) $id);
	}
	
	public function updateData($postData, $id){
	
		$data = array(
				 
				'remark'=> $postData['remark'],
				'ApprovalStatus' => $postData['approvalsts'],
				'dt_Approved'=> date('Y-m-d H:i:sa'),
				'dt_update' => date('Y-m-d H:i:sa'),
				'update_by' =>  $postData['Id_User']
		);
		
		$this->update($data, $this->_primary .' = '. (int) $id);
		//save supervisor if any
		$dbsupervisor=new Finalassignment_Model_DbTable_Supervisor();
		if ($postData['approvalsts']=='1' && $postData['ProcessName']=='Persetujuan Pembimbing'  && ($postData['StaffAs']=='Pembimbing' ||
			$postData['StaffAs']=='Promotor' )  ) {
				//get current approval
				$approve=$this->getData($id);
				
				if ($dbsupervisor->isNotIn($approve['Approved_by'],$approve['IdTAApplication'])) {
				
					$data= array ('IdTAApplication' => $approve['IdTAApplication'],
							'IdStaff' => $approve['Approved_by'],
							'StaffAs'=> $approve['StaffAs'],
							'Id_User' =>  $postData['Id_User']);
					
					$dbsupervisor->addData($data);
				}
			}
		
		// get next step if approved
		$dbFlow=new Finalassignment_Model_DbTable_Flow();
		$next=$dbFlow->getNextFlow($postData['IdTAFlowMain'],$postData['Sequence']);
		$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
		//insert to next approval if any
		if ($next) {
			if ($postData['approvalsts']=='1' && $this->allapproved($postData['IdTAApplication'],$postData['IdTAFlow'],$postData['Sequence'])) {
			//insert pembimbing if any
			//-------------
			
				if (isset($postData['Approved_by']) && $next['IdStaffApproving']==0) {
					$next['IdStaffApproving']=$postData['Approved_by'];
				}
				if ($next['IdStaffApproving']!='' && $next['IdStaffApproving']!=0) {
					$datelatest=date_create(date('Y-m-d'));
					date_add($datelatest,date_interval_create_from_date_string("'".$next['indays'] . " days'"));
					
					$data = array(
						'IdTAApplication' => $postData['IdTAApplication'],
						'IdTAFlow' => $next['IdTAFlow'],
						'Sequence' => $next['Sequence'],
						'StaffAs' => $next['StaffAs'],
						'remark' => null,
						'approval_type' => $next['approval_type'],
						'Approved_by' => $next['IdStaffApproving'],
						'dtlatest'=> date('Y-m-d',$datelatest),
						'dt_entry' => date('Y-m-d H:i:sa'),
						'Id_User' =>  $postData['Id_User']
						);
					$this->addData($data);
					//insert other staff who has same level of othority
					for ($i=1; $i<$postData['nspan'];$i++) {
						if ($postData['othersupervisor'.$i]!='') {// ($postData['othersupervisor'] as $staff) {
							$data = array(
							'IdTAApplication' => $postData['IdTAApplication'],
							'IdTAFlow' => $next['IdTAFlow'],
							'Sequence' => $next['Sequence'],
							'StaffAs' => 'Co-'.$next['StaffAs'],
							'approval_type' => $next['approval_type'],
							'Approved_by' => $postData['othersupervisor'.$i],
							'dtlatest'=>  date('Y-m-d',$datelatest),
							'dt_entry' => date('Y-m-d H:i:sa'),
							'Id_User' =>  $postData['Id_User']
							);
							//echo var_dump($data);exit;
						$this->addData($data);
						}
					}
				} else return false;
			}
		} else {
				//set no  deacree to 
				if ($postData['approval_type']=='OTO') {
					$nodean=$postData['nodean'];
					$dtnodean=$postData['dtnodean'];
					//get current semester 
					
					$semester=$dbSem->getSemesterByDate($dtnodean);
					$idsemester=$semester['IdSemesterMaster'];
					//cek no dean decree
					$dbDean=new Finalassignment_Model_DbTable_DeanDecree();
					$nodeans=$dbDean->isIn($nodean,$idsemester);
					if (!$nodeans) {
						$data = array(
								'decree' => $nodean,
								'dt_effective'=>$dtnodean,
								'IdSemesterMain'=>$idsemester,
								'IdProgram'=>$postData['IdProgram'],
								'active'=> '1' ,
								'dt_entry' => date('Y-m-d H:i:sa'),
								'Id_User' =>  $postData['Id_User']
						);
						$idnodean=$dbDean->addData($data);
					} else $idnodean=$nodeans['Id'];
				}
				//if proposal
				
				$datelatest=date_create(date('Y-m-d'));
				date_add($datelatest,date_interval_create_from_date_string("6 months"));
				if ($postData['StageCode']=='Proposal') {
					//finist proposal and set start date in Application
					$data=array('IdSemester_start'=>$idsemester,
								'Dean_Decree'=>$idnodean,
								'TGL_START'=>date('Y-m-d H:i:sa'),
								'TGL_selesai_normal'=>date_format($datelatest,'Y-m-d H:i:sa'),
								'STS_ACC'=>'1'); 
				}
				else  if ($postData['StageCode']=='ExtendTA') {
						$data=array(
								'TGL_selesai_normal'=>date_format($datelatest,'Y-m-d H:i:sa')
								
						);
						$extend=array('dt_approved'=>date('Y-m-d H:i:sa'),
									'Approved_by'=>$postData['Approved_by']
						);
						$dbExtend=new Finalassignment_Model_DbTable_Extend();
						$dbExtend->updateDataByStudent($extend,$postData['IdStudentRegistration'], $postData['IdTAApplication']);
					} else if ($postData['StageCode']=='Change'){
						$data=array(
								'TGL_selesai_normal'=>date_format($datelatest,'Y-m-d H:i:sa')
						
						);
						$change=array('dt_approved'=>date('Y-m-d H:i:sa'),
									'Approved_by'=>$postData['Approved_by'],
									'sts_acc'=>$postData['approvalsts'],
									'reasonCode'=>$postData['reason'],
									'remark'=>$postData['remark']
						);
						
						$dbChange=new Finalassignment_Model_DbTable_Change();
						$dbChange->updateDataByStudent($change, $postData['IdStudentRegistration'], $postData['IdTAApplication']);
					} else  if ($postData['StageCode']=='SPV') {
						//echo 'ok';exit;
						$dtselesai=date('Y-m-d H:i:sa');
						$semester=$dbSem->getSemesterByDate($dtselesai);
						if ($semester) $idsemester=$semester['IdSemesterMaster'];
						$data=array(
								'TGL_selesai_laporan'=>$dtselesai,
								'IdSemester_stop'=>$idsemester
								
						);
					}
				$dbApp=new Finalassignment_Model_DbTable_Application();
				$dbApp->updatefile($data, $postData['IdTAApplication']);
				//get next main Process if any 
				$dbFlowmain=new Finalassignment_Model_DbTable_FlowMain();
				$nextprocess=$dbFlowmain->getNextProcessMain($postData['IdTAFlowMain']);
				if (count($nextprocess)>0) {
					$next=$dbFlow->getNextFlow($nextprocess['IdTAFlowMain'], '0');
					if ($next) {
						if ($next['IdStaffApproving']!='' && $next['IdStaffApproving']!=0) {
							$datelatest=date_create(date('Y-m-d'));
							date_add($datelatest,date_interval_create_from_date_string("'".$next['indays'] . " days'"));	
							$data = array(
									'IdTAApplication' => $postData['IdTAApplication'],
									'IdTAFlow' => $next['IdTAFlow'],
									'Sequence' => $next['Sequence'],
									'StaffAs' => $next['StaffAs'],
									'remark' => null,
									'approval_type' => $next['approval_type'],
									'Approved_by' => $next['IdStaffApproving'],
									'dtlatest'=> date('Y-m-d',$datelatest),
									'dt_entry' => date('Y-m-d H:i:sa'),
									'Id_User' =>  $postData['Id_User']
							);
							$this->addData($data);
					} else {
						if ($next['StaffAs']=='Pembimbing' || $next['StaffAs']=='Promotor') {
							$supervisor=$dbsupervisor->getAllSupervisor($postData['IdTAApplication']);
							foreach ($supervisor as $item) {
								$data = array(
										'IdTAApplication' => $postData['IdTAApplication'],
										'IdTAFlow' => $next['IdTAFlow'],
										'Sequence' => $next['Sequence'],
										'StaffAs' => $next['StaffAs'],
										'remark' => null,
										'approval_type' => $next['approval_type'],
										'Approved_by' => $item['IdStaff'],
										'dtlatest'=> date('Y-m-d',$datelatest),
										'dt_entry' => date('Y-m-d H:i:sa'),
										'Id_User' =>  $postData['Id_User']
								);
								$this->addData($data);
							}
						}
					}
				}
			}
		}
		return true;
		
	}
	 
	 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	

	public function fnGetOpenStatusProcess($idtaapplication){
		 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('tf'=>'tbl_TA_FLOW'),'p.IdTAFlow = tf.IdTAFlow')
		->joinLeft(array('st'=>'tbl_staffmaster'),'p.Approved_by=st.IdStaff')
		->joinLeft(array('def'=>'tbl_definationms'),'def.IdDefinition=p.approval_type',array('approvaltype'=>'def.BahasaIndonesia'))
		->where('p.IdTAApplication = ?', $idtaapplication)
		->order('tf.IdTAFlowMain')
		->order('p.Sequence');	
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function getData($id){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=p.approval_type',array('ApprovalType'=>'def.DefinitionCode'))
		->where('p.IdTAApproval=?',$id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function isIn($idtaapp,$idflow){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		 ->where('p.IdTAApplication=?',$idtaapp) 
		->where('p.IdTAFlow=?',$idflow); 
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		
		if ($larrResult) return true; else return false;
	}
	public function getApprovalId($idstaff,$idta){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAApplication=?',$idta)
		->where('p.Approved_by=?',$idstaff)
		->where('p.ApprovalStatus=0');
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getApprovedStaff($id){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=p.approval_type')
		->where('def.DefinitionCode= "ACC"')
		->where('p.IdTAApplication=?',$id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getProposedApproval($id){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=p.approval_type')
		->where('def.DefinitionCode= "Propose"')
		->where('p.IdTAApplication=?',$id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function fnGetAllProposalShouldApproval($idStaff){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name),array('p.*','dt_proposed'=>'dt_entry'))
		->join(array('tf'=>'tbl_TA_FLOW'),'p.IdTAFlow = tf.IdTAFlow')
		->join(array('app'=>'tbl_TA_Application'),'app.IdTAApplication=p.IdTAApplication',array('IdTAApplication','STS_ACC','TGL_START','TGL_selesai','IdStudentRegistration'))
		->join(array('pr'=>'tbl_TA_proposal'),'pr.IdTA=app.IdTA')
		->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=app.IdStudentRegistration',array('registrationId'))
		->join(array('sp'=>'student_profile'),'st.idapplication=sp.appl_id',array('studentname'=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)'))
		->join(array('sm'=>'tbl_staffmaster'),'p.Approved_by=sm.IdStaff')
		->where('p.Approved_by = ?', $idStaff)
		->order('p.dt_entry DESC');
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		//echo var_dump($larrResult);exit;
		return $larrResult;
	}
	
	public function fnGetProposal($idTaApproval){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('tf'=>'tbl_TA_FLOW'),'p.IdTAFlow = tf.IdTAFlow')
		//->join(array('tfm'=>'tbl_TA_flow_main'),'p.IdTAFlowMain = tf.IdTAFlowMain')
		->join(array('app'=>'tbl_TA_Application'),'app.IdTAApplication=p.IdTAApplication')
		->join(array('pr'=>'tbl_TA_proposal'),'pr.IdTA=app.IdTA')
		
		->join(array('st'=>'tbl_staffmaster'),'p.Approved_by=st.IdStaff')
		->where('p.IdTAApproval = ?', $idTaApproval);
		
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function allapproved($idTaApplication,$idtaflow,$sequence){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAApplication = ?', $idTaApplication)
		->where('p.IdTAFlow=?',$idtaflow)
		->where('p.Sequence= ?', $sequence)
		->where('p.ApprovalStatus = 0');
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		if ($larrResult) return false; else return true; 
		
	}
	public function isApproved($idTaApplication){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		//->join(array('tf'=>'tbl_TA_FLOW'),'p.IdTAFlow = tf.IdTAFlow')
		->join(array('app'=>'tbl_TA_Application'),'app.IdTAApplication=p.IdTAApplication')
		//->join(array('st'=>'tbl_staffmaster'),'tf.IdStaffApproving=st.IdStaff')
		->where('app.IdTAApplication = ?', $idTaApplication)
		->where('p.ApprovalStatus="1"');
	
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		if ($larrResult) return true; else return false; 
	}
	public function fnGetFieldsStatusApproval() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Status of Approval"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	
	public function initWorkflow($std,$prosesname,$idtaapplication) {
			
		$auth = Zend_Auth::getInstance();
		$dbFlow=new Finalassignment_Model_DbTable_Flow();
		$dbMainFlow=new Finalassignment_Model_DbTable_FlowMain();
		//
		$flowmain=$dbMainFlow->getFlowNameByProgram($std['IdProgram'],$std['IdProgramMajoring'],$std['IdBranch'],$prosesname,$std['IdLandscape']);
		$idflowmain=$flowmain['IdTAFlowMain'];
		
		$flow=$dbFlow->getFlowByFlowMainSeq($idflowmain, "1");
		 
		if ($flow["IdStaffApproving"]=='0') {
			//echo $prosesname.'2';
			if ($flow['StaffAs']=='Pembimbing' || $flow['StaffAs']=='Promotor' ) {
				$dbSupervisor=new Finalassignment_Model_DbTable_Supervisor();
				$supervisor=$dbSupervisor->getAllSupervisor($idtaapplication);
				foreach ($supervisor as $item) {
					$idstaff=$item['IdStaff'];
					$data=array('IdTAApplication'=>$idtaapplication,
							'IdTAFlow'=>$flow['IdTAFlow'],
							'Sequence'=>$flow['Sequence'],
							'ApprovalStatus'=>'0',
							'approval_type'=>$flow['approval_type'],
							'StaffAs'=>$item['StaffAs'],
							'dt_entry'=>date('Y-m-d H:i:sa'),
							'Id_User'=>$auth->getIdentity()->iduser,
							'Approved_by'=>$idstaff,
							//'dt_latest'=> date_add(date_create($time, $object),'+7 days')
					);
					//echo $prosesname.'3';exit;
					$this->add($data);
				}
			}
		} else {
			//echo var_dump($flow);exit;
			if (!$this->isIn($idtaapplication, $flow['IdTAFlow'])) {
				//echo 'ada ada';exit;
				$indays=$flow['indays']." days";
				$Data['IdTAApplication']=$idtaapplication;
				$Data['IdTAFlow']=$flow['IdTAFlow'];
				$Data['IdTAFlowMain']=$flow['IdTAFlowMain'];
				$Data['StaffAs']=$flow['StaffAs'];
				$Data['approval_type']=$flow['approval_type'];
				$Data['Sequence']=1;
				$Data['remark']='';
				$Data['ApprovalStatus']="0";
				$Data['Approved_by']=$flow['IdStaffApproving'];
				$date=date_create(date('Y-m-d'));
				date_add($date,date_interval_create_from_date_string($indays.' days'));
				$Data['dtlatest']=date_format($date,'Y-m-d');
				$Data['Id_User']=$auth->getIdentity()->appl_id;
				$this->addData($Data);
				
			}
		}
		 
	
	}
	
	public function getActivityNameByProcessName($idtaapplication,$prosescode) {
		
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array("fl"=>'tbl_TA_FLOW'),'fl.IdTAFlow=p.IdTAFlow',array())
		->join(array('fm'=>'tbl_TA_flow_main'),'fm.IdTAFlowMain=fl.IdTAFlowMain',array())
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=fm.Activity_Code',array('ActivityName'=>'def.BahasaIndonesia'))
		->join(array('def1'=>'tbl_definationms'),'def1.IdDefinition=fm.ProcessCode',array())
		->where('p.IdTAApplication=?',$idtaapplication)
		->where('def1.DefinitionCode=?',$prosescode);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		 
		return $larrResult;
	}
	
	public function getRoleByApprovalType($idtaapplication,$prosescode,$type) {
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array("fl"=>'tbl_TA_FLOW'),'fl.IdTAFlow=p.IdTAFlow',array())
		->join(array('fm'=>'tbl_TA_flow_main'),'fm.IdTAFlowMain=fl.IdTAFlowMain',array())
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=fm.ProcessCode',array())
		->join(array('def1'=>'tbl_definationms'),'def1.IdDefinition=p.approval_type',array())
		->where('p.IdTAApplication=?',$idtaapplication)
		->where('def.DefinitionCode=?',$prosescode)
		->where('def1.DefinitionCode=?',$type);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
}

?>