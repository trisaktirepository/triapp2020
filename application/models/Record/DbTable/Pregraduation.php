<?php
class App_Model_Record_DbTable_Pregraduation extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'pregraduate_list';
	protected $_primary = "id";
		
	public function getData($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('pgl'=>$this->_name))
					->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = pgl.add_by', array('add_by_name'=>"concat_ws(' ',tu.fname,tu.mname,tu.lname)"))
					->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration = pgl.idStudentRegistration')
					->join(array('sp'=>'student_profile'),'sp.appl_id = sr.IdApplication', array('appl_fname', 'appl_mname', 'appl_lname'))
					->join(array('pr'=>'tbl_program'), 'pr.IdProgram = sr.IdProgram', array('program_name'=>'ArabicName'))
					->join(array('itk' => 'tbl_intake'), 'itk.IdIntake = sr.idIntake', array('intake_name'=>'IntakeDefaultLanguage'))
					->joinLeft(array('sk'=>'graduation_skr'),'sk.id = pgl.dean_approval_skr', array('dean_skr'=>'skr'))
					->joinLeft(array('sk2'=>'graduation_skr'),'sk2.id = pgl.rector_approval_skr', array('rector_skr'=>'skr'));
		
		if($id!=0){
			$selectData->where("pgl.id = ?", $id);
			
			$row = $db->fetchRow($selectData);
		}else{
			
			$row = $db->fetchAll($selectData);
		}
			
		if(!$row){
			return null;
		}else{
			return $row;	
		}				
		
	}
	
	public function getDataByStd($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('pgl'=>$this->_name))
		->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = pgl.add_by', array('add_by_name'=>"concat_ws(' ',tu.fname,tu.mname,tu.lname)"))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration = pgl.idStudentRegistration')
		->join(array('sp'=>'student_profile'),'sp.appl_id = sr.IdApplication', array('appl_fname', 'appl_mname', 'appl_lname'))
		->join(array('pr'=>'tbl_program'), 'pr.IdProgram = sr.IdProgram', array('program_name'=>'ArabicName'))
		->join(array('itk' => 'tbl_intake'), 'itk.IdIntake = sr.idIntake', array('intake_name'=>'IntakeDefaultLanguage'))
		->joinLeft(array('sk'=>'graduation_skr'),'sk.id = pgl.dean_approval_skr', array('dean_skr'=>'skr'))
		->joinLeft(array('sk2'=>'graduation_skr'),'sk2.id = pgl.rector_approval_skr', array('rector_skr'=>'skr'));
	
		if($id!=0){
			$selectData->where("pgl.IdStudentRegistration = ?", $id);
				
			$row = $db->fetchRow($selectData);
		}else{
				
			$row = $db->fetchAll($selectData);
		}
			
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
	
	
	public function getDataNotMigrated(array $filter=null){
		
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('pgl'=>$this->_name))
		->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = pgl.add_by', array('add_by_name'=>"concat_ws(' ',tu.fname,tu.mname,tu.lname)"))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration = pgl.idStudentRegistration', array('registrationId', 'idIntake','IdProgram'))
		->join(array('sp'=>'student_profile'),'sp.appl_id = sr.IdApplication', array('appl_fname', 'appl_mname', 'appl_lname'))
		->join(array('pr'=>'tbl_program'), 'pr.IdProgram = sr.IdProgram', array('program_name'=>'ArabicName','Strata_code_EPSBED'))
		->join(array('itk' => 'tbl_intake'), 'itk.IdIntake = sr.idIntake', array('intake_name'=>'IntakeDefaultLanguage'))
		->where('pgl.migrated="0"');
		
		if($session->IdRole == 605 || $session->IdRole == 311 || $session->IdRole == 298 || $session->IdRole == 579 || $session->IdRole == 851){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$selectData->where("pr.IdCollege='".$session->idCollege."'");
		}
		if($session->IdRole == 470 || $session->IdRole == 480){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$selectData->where("pr.IdProgram='".$session->idDepartment."'");
		}
		

		if($filter){
			foreach ($filter as $key=>$value){
		
				if($key == 'registrationId'){
					$selectData->where($key." like '%".$value."%'");
				}else
				if($key == 'dean_approval'){
					if($value == 1){
						$selectData->where('pgl.dean_approval_date is not null');
					}else{
						$selectData->where('pgl.dean_approval_date is null');
					}
				}else
				if($key == 'rector_approval'){
					if($value == 1){
						$selectData->where('pgl.rector_approval_date is not null');
					}else{
						$selectData->where('pgl.rector_approval_date is null');
					}
				}else{
					$selectData->where($key." = ".$value);
				}
		
			}
		}
		
		$selectData->order('sr.registrationid','pr.ProgramCode');
		$row = $db->fetchAll($selectData);
		
			
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
	
	public function getDataToApprove($type=1, array $filter=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('pgl'=>$this->_name))
		->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = pgl.add_by', array('add_by_name'=>"concat_ws(' ',tu.fname,tu.mname,tu.lname)"))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration = pgl.idStudentRegistration', array('registrationId', 'idIntake','idProgramMajoring','idStudentRegistration'))
		->join(array('sp'=>'student_profile'),'sp.appl_id = sr.IdApplication', array('appl_fname'=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)', 'appl_mname', 'appl_lname'))
		->join(array('pr'=>'tbl_program'), 'pr.IdProgram = sr.IdProgram', array('program_id'=>'IdProgram','program_name'=>'ArabicName'))
		->join(array('itk' => 'tbl_intake'), 'itk.IdIntake = sr.idIntake', array('intake_id'=>'IdIntake','intake_name'=>'IntakeDefaultLanguage'))
		->order('sr.registrationid');
		
		
		if($type==1){
			$selectData->where('pgl.dean_approval_date is null');
			$selectData->where('pgl.migrated = 0');
		}else
		if($type==2){
			$selectData->join(array('gskr'=>'graduation_skr'), 'gskr.id = pgl.dean_approval_skr', array('skr'));
			$selectData->where('pgl.rector_approval_date is null');
			$selectData->where('pgl.migrated = 0');
		}
		else 
		if ($type==3){
			$selectData->join(array('gskr'=>'graduation_skr'), 'gskr.id = pgl.dean_approval_skr', array('skr'));
			
			
		}
		
		if($filter){
			foreach ($filter as $key=>$value){
				
				if($key == 'registrationId'){
					$selectData->where($key." like '%".$value."%'");
				}else{
					$selectData->where($key." = ".$value);
				}
						
			}
		}
		
		//echo $selectData;
		//exit;
			
		$row = $db->fetchAll($selectData);
		
			
		if(!$row){
			return null;
		}else{
			return $row;
		}
		
	}
	
	public function getTranscripProfileGraduates($idprofile,$grp,$sem=null,$period=null) {

		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('pgl'=>$this->_name),array())
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration = pgl.idStudentRegistration',array())
		->join(array('pr'=>'transcript_profile'),'pr.idProgram=sr.IdProgram and pr.idMajoring=sr.IdProgramMajoring and sr.idLandscape=sr.idLandscape',array('idLandscape','idProgram','idMajoring','ProfileName','idTranscriptProfile'))
		->join(array('l'=>'tbl_landscape'),'l.idLandscape=pr.idLandscape','ProgramDescription')
		->join(array('det'=>'transcript_profile_detail'),'pr.idTranscriptProfile=det.idTranscriptProfile')
		//->where('pgl.migrated = 0')
		->where('pr.idTranscriptProfile=?',$idprofile)
		->where('det.idGroup=?',$grp)
		->group('pr.idTranscriptProfile');
		
		if ($sem!=null) {
			$selectData->join(array('skr'=>'graduation_skr'),'pgl.dean_approval_skr=skr.id');
			$selectData->where('skr.IdSemesterMain=?',$sem);
			if ($period!=null) $selectData->where('skr.period=?',$period);
		}
		
		$row = $db->fetchAll($selectData);
		//echo var_dump($row);
		//exit;
		return $row;
		
	}
	public function getCountofPreGraduates($program=null,$migrated=null,$prog=null,$dean=null,$semester=null,$periode=null) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('pgl'=>$this->_name),array('counts'=>'count(distinct sr.idStudentRegistration)'))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration = pgl.idStudentRegistration',array())
		->join(array('pr'=>'tbl_program'),'sr.IdProgram=pr.IdProgram',array('IdProgram','ProgramName','ProgramCode'))
		->join(array('cl'=>'tbl_collegemaster'),'pr.IdCollege=cl.IdCollege',array('CollegeName'))
		->joinLeft(array('skr'=>'graduation_skr'),'skr.id=pgl.dean_approval_skr')
		->group('IdProgram','ProgramCode','ProgramName','CollegeName')
		->order('MID(ProgramCode,2,1)','ProgramCode');
		//filtering
		if ($program!=null) $selectData->where('sr.IdProgram = ?',$program);
		if ($migrated!=null) $selectData->where('pgl.migrated = ?',$migrated);
		if ($prog!=null) $selectData->where('pgl.dean_approval_date is not null');
		if ($dean!=null) $selectData->where('pgl.rector_approval_date is not null');
		
		if ($prog==null && $dean==null && $migrated==null) {
			if ($semester!=null) $selectData->where('skr.IdSemesterMain is null or skr.IdSemesterMain=?',$semester);
			if ($periode!=null) $selectData->where('skr.period is null or skr.period=?',$periode);
			
		} else {
			if ($semester!=null) $selectData->where('skr.IdSemesterMain=?',$semester);
			if ($periode!=null) $selectData->where('skr.period=?',$periode);
		}
		$row = $db->fetchAll($selectData); 
		//echo var_dump($row);
		//exit;
		return $row;
	
	}
	public function getDetailofPreGraduatesPerProgram($idProgram,$migrated=null,$prog=null,$dean=null,$semester,$periode) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('pgl'=>$this->_name),array())
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration = pgl.idStudentRegistration',array('idStudentRegistration','registrationid'))
		->join(array('std'=>'student_profile'),'sr.idApplication=std.appl_id',array('appl_email','appl_phone_hp','std.appl_id','appl_fname','appl_mname','appl_lname','appl_address1','appl_address_rt','appl_address_rw','appl_dob','appl_birth_place','appl_gender','appl_nationality','appl_religion','appl_kelurahan','appl_kecamatan','appl_postcode','appl_city'))
		->joinLeft(array('c'=>'tbl_city'),'c.idCity=std.appl_city',array('CityName'))
		->join(array('pr'=>'tbl_program'),'sr.IdProgram=pr.IdProgram',array('IdProgram','ProgramName','ProgramCode'))
		->join(array('cl'=>'tbl_collegemaster'),'pr.IdCollege=cl.IdCollege',array('CollegeName'))
		->joinLeft(array('skr'=>'graduation_skr'),'skr.id=pgl.dean_approval_skr')
		//->where('pgl.migrated = 0')
		//->where('sr.IdProgram =?',$idProgram)
		->order('sr.registrationid');
		
		if ($idProgram!=null) $selectData->where('sr.IdProgram = ?',$idProgram);
		if ($migrated!=null) $selectData->where('pgl.migrated = ?',$migrated);
		if ($prog!=null) $selectData->where('pgl.dean_approval_date is not null');
		if ($dean!=null) $selectData->where('pgl.rector_approval_date is not null');
		
		if ($prog==null && $dean==null && $migrated==null) {
			if ($semester!=null) $selectData->where('skr.IdSemesterMain is null or skr.IdSemesterMain=?',$semester);
			if ($periode!='All') $selectData->where('skr.period is null or skr.period=?',$periode);
				
		} else {
			if ($semester!=null) $selectData->where('skr.IdSemesterMain=?',$semester);
			if ($periode!='All') $selectData->where('skr.period=?',$periode);
		}
		
		$row = $db->fetchAll($selectData);
		//echo var_dump($row);
		//exit;
		return $row;
	
	}
	public function insert(array $data){
		
		$auth = Zend_Auth::getInstance();
		
		if(!isset($data['add_by'])){
			$data['add_by'] = $auth->getIdentity()->iduser;
		}
		
		$data['add_date'] = date('Y-m-d H:i:s');
			
        return parent::insert($data);
	}		
		

	
	
	public function deleteData($id=null){
		if($id!=null){
			$data = array(
				'status' => 0				
			);
				
			$this->update($data, "c_id = '".$id."'");
		}
	}	
	
	public function deleteDataByStd($id=null){
		
		//if($id!=null){
			 
			$db = Zend_Db_Table::getDefaultAdapter();
			$db->delete("pregraduate_list","id = '".$id."'");
		//}
	}
}

?>