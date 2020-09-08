<?php 
class Finalassignment_Model_DbTable_Proposal extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_proposal';
	protected $_primary='IdTA';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		$data = array(
				'title_bahasa' => trim($postData['title_bahasa']),
				'title' => trim($postData['title']),
				'problem1' => trim($postData['problem1']),
				'problem2'=> trim($postData['problem2']),
				'problem3' => trim($postData['problem3']),
				'ABSTRAK' => trim($postData['abstrak']),
				'STATUS_PENGAJU' => $postData['STATUS_PENGAJU'],
				'IdPengaju' => $postData['IdPengaju'],
				'cgpa_min' => $postData['cgpa_min'],
				'cgpa_max' => $postData['cgpa_max'],
				'IdProgram' => $postData['IdProgram'],
				'IdMajor' => $postData['IdMajor'],
				'IdBranch' => $postData['IdBranch'],
				'dt_entry' => date('Y-m-d H:i:s'),
				'Id_User' =>  $postData['Id_User']
		);
			
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
	
		$this->update($postData, $this->_primary .' = '. (int) $id);
	}
	
	public function rejectData($by,$id){
	
		$data = array(
				'Sts_proses'=>'2',
				'rejected_by'=>$by,
				'dt_update' => date('Y-m-d H:i:s')				
		);
			
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	

	public function fnGetOpenProposal($program,$majoring,$branch){
		 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('app'=>'tbl_TA_Application'),'p.IdTA = app.IdTA')
		->where('p.IdProgram = ?', $program)
		->where('p.IdMajoring = ?', $majoring)
		->where('p.IdBranch = ?', $branch)
		->where('app.STS_ACC = 0');	
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function fnGetOpenStaffProposal($program,$majoring,$branch){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->joinLeft(array('app'=>'tbl_TA_Application'),'p.IdTA = app.IdTA',array())
		->where('p.IdProgram = ?', $program)
		->where('(p.IdMajor = ?', $majoring)
		->orwhere('p.IdMajor=0)')
		->where('(p.IdBranch = ?', $branch)
		->orwhere('p.IdBranch =0)')
		->where('p.STATUS_PENGAJU = "Staff"')
		->where('app.STS_ACC = 0 or app.STS_ACC is null');
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		
		return $larrResult;
	}
	public function fnGetProposal($id=null){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		//->joinLeft(array('app'=>'tbl_TA_Application'),'p.IdTA = app.IdTA')
		->where('p.IdTA= ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function isExpired($idTaapplication){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('app'=>'tbl_TA_Application'),'p.IdTA = app.IdTA')
		->where('app.IdTAApplication= ?', $idTaapplication)
		->where('app.TGL_selesai_normal < CURDATE()');
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function isApproved($idStudentRegistration){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('app'=>'tbl_TA_Application'),'p.IdTA = app.IdTA')
		->join(array('ap'=>'tbl_TA_Approval'),'ap.IdTAApplication=app.IdTAApplication')
		->where('app.IdStudentRegistration= ?', $idStudentRegistration)
		->where('ap.ApprovalStatus="1"');
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	 
	
	public function fnGetProposalByStudent($id,$idstudent){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('app'=>'tbl_TA_Application'),'p.IdTA = app.IdTA')
		->where('p.IdTA= ?', $id)
		->where('app.IdStudentRegistration=?',$idstudent);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function fnGetApprovedProposal($program,$majoring,$Branch){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('app'=>'tbl_TA_Application'),'p.IdTA = app.IdTA')
		->where('p.IdProgram = ?', $program)
		->where('p.IdMajoring = ?', $majoring)
		->where('p.IdBranch = ?', $Branch)
		->where('app.STS_ACC != 0');
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function fnGetOpenProposalByOwner($idPengaju,$idapp){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('app'=>'tbl_TA_Application'),'p.IdTA = app.IdTA')
		->where('p.IdPengaju = ?', $idPengaju)
		->where('app.IdTAApplication = ?', $idapp)
		->where('app.STS_ACC =0');
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		//echo var_dump($larrResult);exit;
		return $larrResult;
	}
	public function fnGetProposalByOwner($idPengaju,$idapp,$idtaapproval=null){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('app'=>'tbl_TA_Application'),'p.IdTA = app.IdTA')
		->joinLeft(array('apr'=>'tbl_TA_Approval'),'apr.IdTAApplication=app.IdTAApplication',array())
		->joinLeft(array('smt'=>'tbl_semestermaster'),'smt.IdSemesterMaster=app.IdSemester_start',array('SemesterName'=>'LEFT(SemesterMainName,15)'))
		->where('app.IdStudentRegistration = ?', $idPengaju)
		->where('app.IdTAApplication = ?', $idapp);
		if ($idtaapproval==null || $idtaapproval=='' ) $lstrSelect->where('apr.Sequence="1" or apr.Sequence is null');
		else {
			$lstrSelect->where('apr.IdTAApproval = ?', $idtaapproval);
		}
		//->where('app.STS_ACC IS NULL');
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		//echo $lstrSelect;exit;
		return $larrResult;
	}
	
	public function fnGetAllProposalByOwner($idPengaju,$status){
			
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name),array('p.*','IdProposal'=>'p.IdTA'))
		->joinLeft(array('app'=>'tbl_TA_Application'),'p.IdTA = app.IdTA')
		->where('p.STATUS_PENGAJU = ?', $status)
		->where('p.IdPengaju = ?', $idPengaju);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fnGetAllStaffProposalSuitableForStudent($idStudentRegistration){
	
		//get student data
		$dbStd=new Examination_Model_DbTable_StudentRegistration();
		$dbStdRegSub=new Examination_Model_DbTable_StudentRegistrationSubject();
		$dbNilsyarat=new Finalassignment_Model_DbTable_SubjectPrerequisite();
		$student=$dbStd->getStudentInfo($idStudentRegistration);
		$idprogram=$student['IdProgram'];
		$idBranch=$student['IdBranch'];
		$idmajoring=$student['IdProgramMajoring'];
		//cek for IPK
		$dbGrade=new Examination_Model_DbTable_StudentGrade();
		$dbAppl=new Finalassignment_Model_DbTable_Application();
		$gradestatus=$dbGrade->getStudentGradeInfo($idStudentRegistration);
		//get open staff proposal
		$proposal=$this->fnGetOpenStaffProposal($idprogram, $idmajoring, $idBranch);
		//cek proposal validation to student
		
		if (count($proposal) > 0) {
			foreach ($proposal as $key=>$item) {
				
				//cek CGPA
				if (($gradestatus['sg_cgpa']>=$item['cgpa_min'] && $gradestatus['sg_cgpa']<=$item['cgpa_max'] )) {
					//cek for subject prerequisite
					
					$nilaisyarat=$dbNilsyarat->getData($item['IdTA']);
					$status='';
					
					foreach ($nilaisyarat as $value) {
						$grade=$value['Grade_min'];
						
						$idSubject=$value['IdSubject'];
						if (!($dbStdRegSub->isCompleted($idStudentRegistration, $idSubject,$grade,"1",null))) {
							//echo var_dump($value);
							unset($proposal[$key]);
							$status="0";
							break;
			
						}
						
					}
					if ($status=='') {
						//get number of applicant
						$proposal[$key]['nOfapplicant']=$dbAppl->getCountApplicant($item['IdTA']);
					}
				} else unset($proposal[$key]);
				
			}
			//echo 'o';
		}
		//echo var_dump($proposal);exit;
		return $proposal;
		
	}
	
	public function getPrerequsiteProposal($student,$subject) {

		$proposedBy=$student['IdStudentRegistration'];
		// get prerequisite
		
		//echo var_dump($subject); exit;
		$prerequisiteDb=new App_Model_General_DbTable_Landscapesubject();
		$landscapeDb = new App_Model_General_DbTable_Landscape();
		$landscape = $landscapeDb->getData($student["IdLandscape"]);
		$dbSubject=new App_Model_General_DbTable_Subjectmaster();
		$subject_prerequisite=array();$completed='';
		if($landscape["LandscapeType"]==43 && isset($subject['IdSubject'])) {
			$subject_prerequisite = $prerequisiteDb->getPrerequisiteCourse($student["IdLandscape"],$subject['IdSubject'],$student['IdProgram'],$student['IdProgramMajoring']);
			
			$subjectDB=new Examination_Model_DbTable_StudentRegistrationSubject();
			if(count($subject_prerequisite)>0){
				
				foreach ($subject_prerequisite as $index=>$preq){
					//dah pernah complete blom;
					if ($preq['PrerequisiteType']!='3') {
						if($preq["PrerequisiteGrade"]==""){
							$regcompleted = $subjectDB->isCompleted($proposedBy,$preq["IdRequiredSubject"]);
						}else{
							$regcompleted = $subjectDB->isCompleted($proposedBy,$preq["IdRequiredSubject"],$preq["PrerequisiteGrade"],$preq["PrerequisiteType"],$student['credithours']);
		
						}
					} else {
						$regcompleted = $subjectDB->isCoRequisite($proposedBy,$preq["IdRequiredSubject"]);
					}
					if ($preq['PrerequisiteType']!='2') {
						$grade=$subjectDB->getHighestMarkofAllSemesterNoStatus($proposedBy, $preq['IdRequiredSubject']);
						$subject_prerequisite[$index]['Grade']=$grade['grade_name'];
						$presubject=$dbSubject->getData($preq['IdRequiredSubject']);
						$subject_prerequisite[$index]['SubjectName']=$presubject['BahasaIndonesia'];
						$subject_prerequisite[$index]['SubCode']=$presubject['SubCode'];
					} else {
						if ($preq['PrerequisiteType']=='2') $subject_prerequisite[$index]['Grade']=$student['credithours'];
					}
					if ($regcompleted) {
						$subject_prerequisite[$index]['Ok']="Ok";
						if ($completed!='0') $completed = "1";
					}else{
						$completed = 0;
						$subject_prerequisite[$index]['Ok']="X";
					}
				}
				 
			}
			else {
				$completed="2";
				$subject_prerequisite=array();//prerequisite
			}
		}
		$subject_prerequisite[0]['completed']=$completed;
		return $subject_prerequisite;
	}
}

?>