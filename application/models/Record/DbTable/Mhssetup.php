<?php
//error_reporting(0);
include 'Zend/nusoap/nusoap.php';
include 'Zend/nusoap/class.wsdlcache.php';
class App_Model_Record_DbTable_Mhssetup extends Zend_Db_Table_Abstract//model class for schemesetup module
{
	protected $_name = 'mahasiswa';
	protected $_tbl_intake = 'tbl_intake';
	protected $_tbl_program = 'tbl_program';
	protected $_name3 = 'kelas_kuliah';
	protected $_name4 = 'nilai';
	private $lobjDbAdpt;
	protected $wsdl ;//= 'http://103.28.161.75:8082/ws/live.php?wsdl';
	public function init(){
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select=$this->lobjDbAdpt->select()
		->from("tbl_universitymaster")
		->where("IdUniversity=1");
		$row=$this->lobjDbAdpt->fetchRow($select);	
		if ($row) $this->wsdl=$row['url_feeder'];
		else $this->wsdl='';	
	}
	protected $username = '031016' ;
	protected $password = 'usakti1965';
	
	//'031016_d1kt1' ;
	//protected $wsdl = 'http://103.28.161.75:8082/ws/sandbox.php?wsdl';
	
	
	
	public function mhsSearch($post = array()){ //function to search a particular scheme details

		//echo var_dump($post);exit;
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"mahasiswa"),array('idpdmhs'=>"a.id_pd","a.*",'error'=>'a.error_desc'))
		->join(array('mpt'=>'mahasiswa_pt'),'a.id_pd=mpt.id_pdref',array('nipd','tgl_masuk_sp','id_reg_pdref','mulai_smt','error_pt'=>'mpt.error_desc'))
		->join(array('sa'=>'tbl_studentregistration'),'mpt.nipd=sa.registrationid',array('profileStatus','registrationId'))
		->join(array('intk' => 'tbl_intake'), 'intk.IdIntake=sa.IdIntake', array('intk.class_start','intk.IntakeId'))
		->join(array('prg' => 'tbl_program'), 'prg.IdProgram=sa.IdProgram', array('prg.ArabicName','ProgramName','IdProgram'))
		->where('mpt.id_sms=prg.id_sms');
		
		
		if (isset($post['problem']) && $post['problem']=="1" ){
			$lstrSelect = $lstrSelect->where("mpt.status='1'");
		} else 	$lstrSelect = $lstrSelect->where("mpt.status='0'");
		
		if(isset($post['appl_fname']) && !empty($post['appl_fname']) ){
			$lstrSelect = $lstrSelect->where("a.nm_pd like '%?%'",$post['appl_fname']);
		}
		if(isset($post['id']) && !empty($post['id']) ){
			$lstrSelect = $lstrSelect->where("mpt.id  = ".$post["id"]);
		}
		 
		if(isset($post['status']) && !empty($post['status']) ){
			$lstrSelect = $lstrSelect->where("mpt.status  = '".$post["status"]."'");
		}
		
		
		if(isset($post['intake_id']) && !empty($post['intake_id']) ){
			$lstrSelect = $lstrSelect->where("intk.IdIntake = ?",$post['intake_id']);
		}
		
		if(isset($post['IdStudent']) && !empty($post['IdStudent']) ){
			$lstrSelect = $lstrSelect->where("sa.registrationId = ?",$post['IdStudent']);
		}
		
		if(isset($post['programme']) && !empty($post['programme']) ){
			$lstrSelect = $lstrSelect->where("prg.IdProgram  = ?",$post['programme']);
		}
		
		
		 
		if(isset($post['appl_gender']) && !empty($post['appl_gender']) ){
			$lstrSelect = $lstrSelect->where("sp.appl_gender  = ?",$post['appl_gender']);
		}
		//$lstrSelect	->where("a.status = ".$post["field7"]);
		//echo $larrResult;
		//echo $lstrSelect;exit;
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		
		return $larrResult;
	}
	
	public function mhsSearchUpdate($post = array()){ //function to search a particular scheme details
	
		//echo var_dump($post);exit;
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"mahasiswa"),array('idpdmhs'=>"a.id_pd","a.*",'error'=>'a.error_desc'))
		->join(array('mpt'=>'mahasiswa_pt'),'a.id_pd=mpt.id_pdref',array('nipd','tgl_masuk_sp','id_reg_pdref','mulai_smt','error_pt'=>'mpt.error_desc'))
		->join(array('sa'=>'tbl_studentregistration'),'mpt.nipd=sa.registrationid',array('profileStatus','registrationId'))
		->join(array('intk' => 'tbl_intake'), 'intk.IdIntake=sa.IdIntake', array('intk.class_start','intk.IntakeId'))
		->join(array('prg' => 'tbl_program'), 'prg.IdProgram=sa.IdProgram', array('prg.ArabicName','ProgramName','IdProgram'))
		->where('mpt.id_sms=prg.id_sms')
		->where('a.status_update="1"');
	
	
		if (isset($post['problem']) && $post['problem']=="1" ){
			$lstrSelect = $lstrSelect->where("mpt.status='1'");
		} else 	$lstrSelect = $lstrSelect->where("mpt.status='0'");
	
		if(isset($post['appl_fname']) && !empty($post['appl_fname']) ){
			$lstrSelect = $lstrSelect->where("a.nm_pd like '%?%'",$post['appl_fname']);
		}
		if(isset($post['id']) && !empty($post['id']) ){
			$lstrSelect = $lstrSelect->where("mpt.id  = ".$post["id"]);
		}
			
		if(isset($post['status']) && !empty($post['status']) ){
			$lstrSelect = $lstrSelect->where("mpt.status  = '".$post["status"]."'");
		}
	
	
		if(isset($post['intake_id']) && !empty($post['intake_id']) ){
			$lstrSelect = $lstrSelect->where("intk.IdIntake = ?",$post['intake_id']);
		}
	
		if(isset($post['IdStudent']) && !empty($post['IdStudent']) ){
			$lstrSelect = $lstrSelect->where("sa.registrationId = ?",$post['IdStudent']);
		}
	
		if(isset($post['programme']) && !empty($post['programme']) ){
			$lstrSelect = $lstrSelect->where("prg.IdProgram  = ?",$post['programme']);
		}
	
	
			
		if(isset($post['appl_gender']) && !empty($post['appl_gender']) ){
			$lstrSelect = $lstrSelect->where("sp.appl_gender  = ?",$post['appl_gender']);
		}
		 
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
	
		return $larrResult;
	}
	
	public function transmhsSearch($post = array()){ //function to search a particular scheme details
	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
	 
		->from(array('cg'=>'tbl_split_coursegroup'),array())
		->join(array('kls'=>'kelas_kuliah'),'cg.Id=kls.IdSplitGroup and kls.IdSubject=cg.IdSubject and kls.nm_kls=cg.GroupCode')
		->join(array('n'=>'nilai'),'kls.id=n.id_klsk',array('id_reg_pd','idkls'=>'n.id_kls','idnilai'=>'n.id','nilai_angka','nilai_huruf','id_reg_pdref','id_klsk','nilai_indeks','approval'=>'n.status','dtapproval'=>'n.date_of_approval','error'=>'error_desc'))
		->join(array('sg'=>'tbl_studentregistration'),'sg.registrationid=n.id_reg_pdref',array());
	
		if (isset($post['problem']) && $post['problem']=='1')
			$lstrSelect->where('n.status="1"');
		else  $lstrSelect->where('n.status="0"');
		
		if(isset($post['id_reg_pdref']) && !empty($post['id_reg_pdref']) ){
			$lstrSelect = $lstrSelect->where("n.id_reg_pdref  = ".$post["id_reg_pdref"]);
		}
		
		if(isset($post['status']) && !empty($post['status']) ){
			$lstrSelect = $lstrSelect->where("kk.status  = ?",$post['status']);
		}
		
		if(isset($post['id_smt']) && !empty($post['id_smt']) ){
			$lstrSelect = $lstrSelect->where("id_smt  = ?",$post['id_smt']);
		}
		
		if(isset($post['intake_id']) && !empty($post['intake_id']) ){
			$lstrSelect = $lstrSelect->where("sg.IdIntake = ?",$post['intake_id']);
		}
		
		if(isset($post['IdMajoring']) && !empty($post['IdMajoring']) ){
			$lstrSelect = $lstrSelect->where("sg.IdProgramMajoring = ?",$post['IdMajoring']);
		}
		
		if(isset($post['IdSemester']) && !empty($post['IdSemester']) ){
			$lstrSelect = $lstrSelect->where("cg.IdSemester = ?",$post['IdSemester']);
		}
		
		if(isset($post['programme']) && !empty($post['programme']) ){
			$lstrSelect = $lstrSelect->where("sg.IdProgram  = ?",$post['programme']);
			$lstrSelect = $lstrSelect->where("cg.IdProgram  = ?",$post['programme']);
			
		}
		
		if(isset($post['IdStudent']) && !empty($post['IdStudent']) ){
			$lstrSelect = $lstrSelect->where("sg.registrationid  = ?",$post['IdStudent']);
		}
		
		if(isset($post['IdSubject']) && !empty($post['IdSubject']) ){
			$lstrSelect = $lstrSelect->where("cg.IdSubject = ?",$post['IdSubject']);
		}
		
		if(isset($post['SubjectName']) && !empty($post['SubjectName']) ){
			$lstrSelect=$lstrSelect->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject=cg.IdSubject');
			$lstrSelect = $lstrSelect->where("s.ShortName = ?",$post['SubjectName']);
		}
		
		if(isset($post['IdKelas']) && !empty($post['IdKelas']) ){
			$lstrSelect = $lstrSelect->where("kls.nm_kls = ?",$post['IdKelas']);
		}
		//$lstrSelect	->where("a.status = ".$post["field7"]);
		 
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		//echo var_dump($larrResult);exit;
		return $larrResult;
	}
	
	
	public function trakmSearch($post = array()){ //function to search a particular scheme details
	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	     $lstrSelect = $lobjDbAdpt->select()
			->from(array("km"=>"kuliah_mahasiswa"),array("km.*",'error'=>'error_desc','statusprocess'=>'km.status'))
	     	->join(array('mhs'=>'mahasiswa_pt'),'km.id_reg_pd=mhs.id_reg_pdref')
	     	->join(array('st'=>'tbl_studentregistration'),'st.registrationid=mhs.nipd',array('nim'=>'registrationid','IdStudentRegistration'))
	     	->join(array('sp'=>'student_profile'),'sp.appl_id=st.idApplication',array('student_name'=>'CONCAT(appl_fname," ",appl_lname)'))
	     	->order('km.status');
	
	     if (isset($post['problem']) && $post['problem']=="1" ){
	     	$lstrSelect = $lstrSelect->where("km.status ='1'");
	     } else 	$lstrSelect = $lstrSelect->where("km.status ='0'");
	     
		if(isset($post['intake_id']) && !empty($post['intake_id']) ){
			$lstrSelect = $lstrSelect->where("st.IdIntake = ?",$post['intake_id']);
		}
		
		if(isset($post['IdSemester']) && !empty($post['IdSemester']) ){
			$lstrSelect = $lstrSelect->where("km.IdSemesterMain = ?",$post['IdSemester']);
		}
		
		if(isset($post['programme']) && !empty($post['programme']) ){
			$lstrSelect = $lstrSelect->where("st.IdProgram  = ?",$post['programme']);
		}
		if(isset($post['IdMajoring']) && !empty($post['IdMajoring']) ){
			$lstrSelect = $lstrSelect->where("st.IdProgramMajoring = ?",$post['IdMajoring']);
		}
		if(isset($post['IdStudent']) && !empty($post['IdStudent']) ){
			$lstrSelect = $lstrSelect->where("st.registrationid  = ?",$post['IdStudent']);
		}
		//$lstrSelect	->where("a.status = ".$post["field7"]);
		//echo $lstrSelect;exit;
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	
	public function fngetintake2(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->distinct()
		->from(array("kk"=>"kelas_kuliah"),array("key" => "kk.id_smt" , "value" => "kk.id_smt"));
		//->where('a.ApplicationStartDate >= ?',date('Y-m-d'))
		//->order("a.IntakeId desc");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	
	public function fngetintake(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		 $lstrSelect = $lobjDbAdpt->select()->
		 from(array('a' => $this->_tbl_intake),array("key" => "a.IdIntake" , "value" => "a.IntakeId"))
		//->where('a.ApplicationStartDate >= ?',date('Y-m-d'))
		->order("a.IntakeId desc");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function getIdIntake($yearrpt) {
		
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()->
		from(array('a' => $this->_tbl_intake),array("key" => "a.IdIntake" , "value" => "a.IntakeId"))
		->where('left(a.IntakeId,4)=?',substr($yearrpt,0,4))
		->where('right(a.IntakeId,2)=?','-'.substr($yearrpt,4,1));
		//echo $lstrSelect;exit;
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		//echo var_dump($larrResult);exit;
		return $larrResult['key'];
	}
	
	public function fngetprogram(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()->from(array('a' => $this->_tbl_program),array("key" => "a.IdProgram" , "value" => "a.ArabicName"))
		//->where('a.ApplicationStartDate >= ?',date('Y-m-d'))
		->order("a.IdProgram asc");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fngetInstitution($idprogram){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
			->from(array('prg'=>'tbl_program'),array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode','Strata_code_EPSBED'=>'Strata_code_EPSBED', 'Program_code_EPSBED'=>'Program_code_EPSBED','id_sms'))
			->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=prg.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
			->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED','id_sp'))
			->where('prg.IdProgram=?',$idprogram);
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function mhsisSearch($post = array()){ //function to search a particular scheme details
		
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		 
		$lstrSelect = $lobjDbAdpt->select()
				->from(array('sa' => 'tbl_studentregistration'), array('sa.registrationId','IdApplication','sa.IdStudentRegistration','sa.profileStatus'))
				->join(array('sp'=>'student_profile'),'sp.appl_id=sa.IdApplication',array(
				'sp.id','sp.appl_fname','appl_mname','appl_lname','appl_lname','appl_birth_place','appl_dob','sp.appl_gender',
				'appl_religion','appl_nationality','status','date_of_approval','appl_address1','appl_address_rt','appl_address_rw',
				'appl_kelurahan','appl_nik','appl_nis','appl_postcode','appl_email','appl_phone_hp','appl_phone_home','appl_kecamatan'
				))
				->join(array('intk' => 'tbl_intake'), 'intk.IdIntake=sa.IdIntake', array('intk.class_start','intk.IntakeId'))
				->join(array('prg' => 'tbl_program'), 'prg.IdProgram=sa.IdProgram', array('prg.ArabicName','ProgramName','IdProgram','id_sms'))
				->order('sa.registrationid');

		
		if(isset($post['IdStudent']) && !empty($post['IdStudent']) ){
			$lstrSelect = $lstrSelect->where("sa.registrationId = ?",$post['IdStudent']);
		}
		
		if(isset($post['update']) && !empty($post['update']) ){
			$lstrSelect->join(array('mhs'=>'mahasiswa'),'mhs.appl_id=sp.appl_id',array());
			$lstrSelect->where("sp.update_status in (".$post['update'].") ");
		} 
		if(isset($post['intake_id']) && !empty($post['intake_id']) ){
			$lstrSelect = $lstrSelect->where("intk.IdIntake = ?",$post['intake_id']);
		}
		
		if(isset($post['programme']) && !empty($post['programme']) ){
			$lstrSelect = $lstrSelect->where("prg.IdProgram  = ?",$post['programme']);
		}
		
		if(isset($post['IdMajoring']) && !empty($post['IdMajoring']) ){
			$lstrSelect = $lstrSelect->where("sa.IdProgrammajoring = ?",$post['IdMajoring']);
		}
		
		
		
		/*
		if(isset($post['field3']) && !empty($post['field3']) ){
			$lstrSelect = $lstrSelect->where("sp.id  = ?",$post['field3']);
		}
		*/
		if(isset($post['appl_gender']) && !empty($post['appl_gender']) ){
			$lstrSelect = $lstrSelect->where("sp.appl_gender  = ?",$post['appl_gender']);
		}
		//$larrResult	->where("sp.status = ".$post["field7"]);
		//echo var_dump($post);exit;
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		//get mother and father name
		$dbStatus=new Registration_Model_DbTable_Studentregistration();
		$familyDb = new App_Model_Application_DbTable_ApplicantFamily();
		 
		foreach ($larrResult as $key=>$student) {
			$set="1";
			if (isset($post['problem'])) {
				
				if ($post['problem']=="1" ) {
			 
					if (!$this->isInMahasiswaPt($student['registrationId'], $student['id_sms'])) {
						unset($larrResult[$key]);
						$set="0";
					}
						
				} else {
					if ($this->isInMahasiswaPt($student['registrationId'], $student['id_sms'])) {
						unset($larrResult[$key]);
						$set="0";
					}
				}
			}
			 if ($set=="1") {
				//cek semester status
				$semstatus=$dbStatus->fetchStudentProfileSemester( $student['IdStudentRegistration'],$post['semester']);
				if ($semstatus) {
					$larrResult[$key]['semesterstatus']=$semstatus['BahasaIndonesia'];
					$larrResult[$key]['semesterstatuscode']=$semstatus['pdpt_code'];
				}  else {
					
					$larrResult[$key]['semesterstatus']='Aktif';
					$larrResult[$key]['semesterstatuscode']='A';
				}
				$mother = $familyDb->getData($student['IdApplication'], 21);
				$father = $familyDb->getData($student['IdApplication'], 20);
				if($mother){
					$larrResult[$key]['MotherName']=$mother['af_name'];
				} else $larrResult[$key]['MotherName']='-';
				if($father){
					$larrResult[$key]['FatherName']=$father['af_name'];
				} else $larrResult[$key]['FatherName']='-';
			 }
		}
		//echo var_dump($larrResult);exit;
		return $larrResult;
	}
	
	public function mhsisSearchKrs($post = array()){ //function to search a particular scheme details
	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('sa' => 'tbl_studentregistration'), array('sa.registrationId','IdApplication','sa.IdStudentRegistration','sa.profileStatus'))
		->join(array('sp'=>'student_profile'),'sp.appl_id=sa.IdApplication',array('appl_id',
				'sp.id','sp.appl_fname','appl_mname','appl_lname','appl_lname','appl_birth_place','appl_dob','sp.appl_gender',
				'appl_religion','appl_nationality','status','date_of_approval','appl_address1','appl_address_rt','appl_address_rw',
				'appl_kelurahan','appl_nik','appl_nis','appl_postcode','appl_email','appl_phone_hp','appl_phone_home','appl_kecamatan'
		))
		->join(array('intk' => 'tbl_intake'), 'intk.IdIntake=sa.IdIntake', array('intk.class_start','intk.IntakeId'))
		->join(array('prg' => 'tbl_program'), 'prg.IdProgram=sa.IdProgram', array('prg.ArabicName','ProgramName','IdProgram','id_sms'))
		->order('sa.registrationid')
		->where('sp.update_status="0"');
	
	
		if(isset($post['IdStudent']) && !empty($post['IdStudent']) ){
			$lstrSelect = $lstrSelect->where("sa.registrationId = ?",$post['IdStudent']);
		}
	
		 
		if(isset($post['intake_id']) && !empty($post['intake_id']) ){
			$lstrSelect = $lstrSelect->where("intk.IdIntake = ?",$post['intake_id']);
		}
	
		if(isset($post['programme']) && !empty($post['programme']) ){
			$lstrSelect = $lstrSelect->where("prg.IdProgram  = ?",$post['programme']);
		}
	
		if(isset($post['IdMajoring']) && !empty($post['IdMajoring']) ){
			$lstrSelect = $lstrSelect->where("sa.IdProgrammajoring = ?",$post['IdMajoring']);
		}
	
	
	
		/*
			if(isset($post['field3']) && !empty($post['field3']) ){
		$lstrSelect = $lstrSelect->where("sp.id  = ?",$post['field3']);
		}
		*/
		if(isset($post['appl_gender']) && !empty($post['appl_gender']) ){
			$lstrSelect = $lstrSelect->where("sp.appl_gender  = ?",$post['appl_gender']);
		}
		//$larrResult	->where("sp.status = ".$post["field7"]);
		//echo var_dump($post);exit;
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		//get mother and father name
		$dbStatus=new Registration_Model_DbTable_Studentregistration();
		$familyDb = new App_Model_Application_DbTable_ApplicantFamily();
			
		foreach ($larrResult as $key=>$student) {
			$set="1";
			 
			if ($set=="1") {
				//cek semester status
				$semstatus=$dbStatus->fetchStudentProfileSemester( $student['IdStudentRegistration'],$post['semester']);
				if ($semstatus) {
					$larrResult[$key]['semesterstatus']=$semstatus['BahasaIndonesia'];
					$larrResult[$key]['semesterstatuscode']=$semstatus['pdpt_code'];
				}  else {
						
					$larrResult[$key]['semesterstatus']='Aktif';
					$larrResult[$key]['semesterstatuscode']='A';
				}
				$mother = $familyDb->getData($student['IdApplication'], 21);
				$father = $familyDb->getData($student['IdApplication'], 20);
				if($mother){
					$larrResult[$key]['MotherName']=$mother['af_name'];
				} else $larrResult[$key]['MotherName']='-';
				if($father){
					$larrResult[$key]['FatherName']=$father['af_name'];
				} else $larrResult[$key]['FatherName']='-';
			}
		}
		//remove no krs
		foreach ($larrResult as $key=>$value) {
			$idstd=$value['IdStudentRegistration'];
			$lstrSelect = $lobjDbAdpt->select()
			->from(array("a"=>"tbl_studentregsubjects"))
			//->join(array('b'=>'tbl_semestermaster'),'a.IdSemesterMain=b.IdSemesterMaster')
			->where('a.IdSemesterMain=?',$post['semester'])
			->where('a.IdStudentRegistration=?',$idstd);
			$row = $lobjDbAdpt->fetchRow($lstrSelect);
			if (!$row) unset($larrResult[$key]);
		}
		return $larrResult;
	}

	public function fnGetMhsDetails(){//function to display all  details in list
		 
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"mahasiswa"),array("a.*"))
		->joinLeft(array('mpt'=>'mahasiswa_pt'),'a.id_pd=mpt.id_pdref',array('nipd','tgl_masuk_sp','id_reg_pdref'))
		//->joinLeft(array('mpt' => 'mahasiswa_pt'),'a.id_pd=mpt.id_pdref',array(
		//'id_pd','id_sp','id_pd','id_sms','nipd','tgl_masuk_sp','id_jns_daftar'));
		//->where("a.id_pd = 47") ;
		->where("a.status NOT IN ('Approved','1')") 
		->order(array('a.id_pd DESC'));
		echo $lstrSelect;
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fnGetMhsDetailsByApplId($id){//function to display all  details in list
			
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"mahasiswa"))
		->where("appl_id=?",$id);
	 
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function fnGetStudDetails2(){//function to display all  details in list
			
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('sa' => 'tbl_studentregistration'), array('sa.registrationId'))
		->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=sa.IdApplication',array(
				'id','appl_fname','appl_mname','appl_lname','appl_lname','appl_birth_place','appl_dob','appl_gender',
				'appl_religion','appl_nationality','status','date_of_approval'))
		->joinLeft(array('intk' => 'tbl_intake'), 'intk.IdIntake=sa.IdIntake', array('intk.class_start','intk.IntakeId'))
		->where("sp.status != 1");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	
	}


	function fnInsertToDb($formData){//function to insert data to database
		$db = Zend_Db_Table::getDefaultAdapter();
		unset ( $formData ['Save'] );
		$table = "mahasiswa";
		$db->insert($table,$formData);
	}
	
	function fnaddData($table,$formData){//function to insert data to database
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($table,$formData);
		return $db->lastInsertId();
	}
	
	function fndeleteData($table,$key){//function to insert data to database
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->delete($table,$key);
	}

	function fnupdateData($table,$formData,$key){//function to insert data to database
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->update($table,$formData,$key);
	}
	
	function fnGetData($table,$key,$set=null){//function to insert data to database
		$db = Zend_Db_Table::getDefaultAdapter();
		$select=$db->select()
		->from($table)
		->where($key);
		if ($set!=null) return $db->fetchAll($select);
		return $db->fetchRow($select);
	}
	
	function fnGetSetKurikulum($idLanscape){//function to insert data to database
		$db = Zend_Db_Table::getDefaultAdapter();
		$select=$db->select()
		->from(array('k'=>'kurikulum'))
		->joinLeft(array('mj'=>'tbl_programmajoring'),'k.IdMajoring=mj.IDProgramMajoring')
		->where('k.IdLandscape=?',$idLanscape);
		 return $db->fetchAll($select);
		 
	}
	
	function fnGetSetKurikulumByProgram($idprogram){//function to insert data to database
		$db = Zend_Db_Table::getDefaultAdapter();
		$select=$db->select()
		->from(array('k'=>'kurikulum'))
		->joinLeft(array('mj'=>'tbl_programmajoring'),'k.IdMajoring=mj.IDProgramMajoring')
		->where('k.IdProgram=?',$idprogram);
		return $db->fetchAll($select);
			
	}
	public  function fnupdateMhs($formData,$id_pd){//function to update data
		unset ( $formData ['Save'] );
		$where = 'id_pd = '.$id_pd ;
		$this->update($formData,$where);
	}
	
	public  function fnupdateMhsByNameDOB($formData,$where){//function to update data
		unset ( $formData ['Save'] );
		//$where = 'nm_pd = '.$id_pd ;
		$this->update($formData,$where);
	}
	
	public  function fnupdateMhsByApplId($formData,$id_pd){//function to update data
		//unset ( $formData ['Save'] );
		$where = 'appl_id = '.$id_pd ;
		$this->update($formData,$where);
	}
	public  function fnupdateMhspt($formData,$id_reg_pd){//function to update data
		//echo"<pre>" ;	print_r ($id_reg_pd) ;exit();
		$db = Zend_Db_Table::getDefaultAdapter();
		unset ( $formData ['Save'] );
		$table = "mahasiswa_pt";
		$where = 'id_reg_pd = '.$id_reg_pd ;
		$db->update($table,$formData,$where);
	}
	
	public  function fnupdateMhsptByRegistrationId($formData,$id_reg_pd,$sms){//function to update data
		//echo"<pre>" ;	print_r ($id_reg_pd) ;exit();
		$db = Zend_Db_Table::getDefaultAdapter();
		unset ( $formData ['Save'] );
		$table = "mahasiswa_pt";
		$where = 'nipd = "'.$id_reg_pd.'" and id_sms="'.$sms.'"' ;
		
		$db->update($table,$formData,$where);
	}
	
	public  function fnupdateDosen($formData,$id){//function to update data
		$db = Zend_Db_Table::getDefaultAdapter();
		unset ( $formData ['Save'] );
		$table = "kelas_kuliah";
		$where = 'id = '.$id ;
		$db->update($table,$formData,$where);
	}
	
	public  function fnupdateDosenPT($formData,$id){//function to update data
		$db = Zend_Db_Table::getDefaultAdapter();
		unset ( $formData ['Save'] );
		$table = "dosen_pt";
		$where = 'id = '.$id ;
		$db->update($table,$formData,$where);
	}
	
	public  function fnupdateAjard($formData,$id){//function to update data
		$db = Zend_Db_Table::getDefaultAdapter();
		unset ( $formData ['Save'] );
		$table = "ajar_dosen";
		$where = 'id = '.$id ;
		//echo var_dump($formData);exit;
		$db->update($table,$formData,$where);
	}
	
	
	public  function fnupdateDetails2($formData,$id){//function to update data
		$db = Zend_Db_Table::getDefaultAdapter();
		unset ( $formData ['Save'] );
		$table = "student_profile";
		$where = 'id = '.$id ;
		$db->update($table,$formData,$where);
	}
	
	public function fnViewMhsDetails($id_pd){ //function to find the data to populate in a page of a selected english description to edit.

		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('a' => 'mahasiswa'),array("id_pd"=>"a.id_pd","nm_pd"=>"a.nm_pd","jk"=>"a.jk","tgl_lahir"=>"a.tgl_lahir","tmpt_lahir"=>"a.tmpt_lahir",
				"id_kk"=>"a.id_kk","id_wil"=>"a.id_wil","a_terima_kps"=>"a.a_terima_kps","ds_kel"=>"a.ds_kel",
				"id_agama"=>"a.id_agama","id_kebutuhan_khusus_ayah"=>"a.id_kebutuhan_khusus_ayah",
				"nm_ibu_kandung"=>"a.nm_ibu_kandung","id_kebutuhan_khusus_ibu"=>"a.id_kebutuhan_khusus_ibu"))
		->joinLeft(array('b' => 'mahasiswa_pt'),'a.id_pd = b.id_pdref',array('tgl_masuk_sp' => 'b.tgl_masuk_sp',
				'id_jns_keluar' => 'b.id_jns_keluar','tgl_keluar' => 'b.tgl_keluar', 'ket'=> 'b.ket','skhun'=>'b.skhun',
				'a_pernah_paud' => 'b.a_pernah_paud', 'a_pernah_tk' => 'b.a_pernah_tk','mulai_smt' => 'b.mulai_smt', 
				'sks_diakui' => 'b.sks_diakui', 'jalur_skripsi' => 'b.jalur_skripsi', 'bln_awal_bimbingan' => 
				'b.bln_awal_bimbingan', 'judul_skripsi' => 'b.judul_skripsi', 'sk_yudisium' => 'b.sk_yudisium', 
				'bln_akhir_bimbingan' => 'b.bln_akhir_bimbingan', 'ipk' => 'b.ipk', 'tgl_sk_yudisium' => 'b.tgl_sk_yudisium'
				, 'sert_prof' => 'b.sert_prof', 'no_seri_ijazah' => 'b.no_seri_ijazah', 'nm_pt_asal' => 'b.nm_pt_asal', 
				'a_pindah_mhs_asing' => 'a_pindah_mhs_asing', 'nm_prodi_asal' => 'b.nm_prodi_asal'))
		->where('a.id_pd = '.$id_pd);
		$result = $db->fetchRow($select);
		//echo"<pre>" ;
		//print_r($result);
		return $result;
	}
	
	public function fnViewMhsptDetails($id_reg_pd){ //function to find the data to populate in a page of a selected english description to edit.
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('mpt'=>'mahasiswa_pt'),
						array('mpt.id_reg_pdref','mpt.nipd','mpt.tgl_masuk_sp',
						'mpt.id_reg_pd'))
		->where('mpt.nipd = '.$id_reg_pd);
		$result = $db->fetchRow($select);
		/*echo"<pre>" ;
		print_r($result);
		exit();*/
		return $result;
	}
	

	public function fnViewDetails2($id){ //function to find the data to populate in a page of a selected english description to edit.
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array("a"=>"student_profile"),array("a.id","a.appl_fname",
					"appl_mname",
					"appl_lname",
					"appl_dob",
					"appl_birth_place",
					"appl_gender",
					"appl_religion",
					"appl_nationality",
					"a.appl_birth_place"))
		->where('a.id = '.$id);
		$result = $db->fetchRow($select);
		//echo"<pre>" ;
		//print_r($result);
		return $result;
	}
	
	public function fnViewDetails3($id){ //function to find the data to populate in a page of a selected english description to edit.
		$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"tbl_studentregistration"),array("a.registrationId"));
			//->where('a.registrationId = '.$id);
			$result = $db->fetchRow($select);
			//echo"<pre>" ;
			//print_r($result);exit();
			return $result;
	}
		
	
	public function getTransmhs($Id){ //function to find the data to populate in a page of a selected english description to edit.
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $db ->select()
			->from(array('ctg'=>'tbl_course_tagging_group') )
							->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=ctg.IdSemester',array('year'=>'LEFT(SemesterMainCode,4)','SemesterCountType'))
							->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=ctg.IdSubject',array('shortname','subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','sks_mk'=>'CreditHours','sks_tm'=>'ch_tutorial','sks_prak'=>'ch_practice'))
							->join(array('sreg'=>'tbl_studentregsubjects'),'sreg.IdCourseTaggingGroup=ctg.IdCourseTaggingGroup', array('IdStudentRegSubjects','grade_point','grade_name','exam_status','final_course_mark'))
							->join(array('sg'=>'tbl_studentregistration'),'sg.IdStudentRegistration=sreg.IdStudentRegistration',array('nim'=>'registrationId'))	
							->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=sg.IdApplication',array('fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',sp.appl_fname, sp.appl_mname, sp.appl_lname)")))
							->join(array('prg'=>'tbl_program'),'prg.IdProgram=sg.IdProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode','Strata_code_EPSBED'=>'Strata_code_EPSBED', 'Program_code_EPSBED'=>'Program_code_EPSBED','id_sms'))
							->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=prg.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
							->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED'))
							->join(array('intk'=>'tbl_intake'),'intk.IdIntake=sg.IdIntake')
							->order('sg.registrationid')
							->where('sreg.IdStudentRegSubjects = ?', $Id);
			 
			$result = $db->fetchAll($lstrSelect);
			if($result){
				$Feeder = new Reports_Model_DbTable_Feedervalue();
				$mata_kuliah=false;
				foreach ( $result AS $index=>$data )
				{
					if ($data['id_sms']!='') 
						$mata_kuliah=$this->getMatakuliah($data['shortname'],$data['id_sms']);
					if (!$mata_kuliah)
						$mata_kuliah = $Feeder->getMataKuliah($data['shortname'],$data['nim']);
					
					if (isset($data['grade_name'])) {
						if (	$data['exam_status']=='NR' |
								$data['exam_status']=='MG' |
								$data['exam_status']=='IN' |
								$data['exam_status']=='W' |
								$data['exam_status']=='FR' |
								$data['exam_status']=='F' |
								$data['exam_status']=='X'
						) {
							$data['grade_name']= 'E';
							$data['grade_point']= '0';
						}
					}
					$finalData = array(
							$data['IdStudentRegSubjects'],
							$data['year'].$data['SemesterCountType'],
							$data['univ_mohe_code'],
							$data['Strata_code_EPSBED'],
							$data['Program_code_EPSBED'],
							$data['nim'],
							$data['shortname'],
							$data['grade_name'],
							$data['grade_point'],
							$data['GroupCode'],
							$mata_kuliah['id_sms'],
							$mata_kuliah['id_mk'],
							$mata_kuliah['sks_mk'],
							$mata_kuliah['sks_tm'],
							$mata_kuliah['sks_prak'],
							$data['IdCourseTaggingGroup'],
							$data['final_course_mark'],
					);
				}
			}
			/*echo"<pre>" ;
			print_r($finalData);exit();*/
			return $finalData;
		}
		public function getTransNilDetailbyId($Id){ //function to find the data to populate in a page of a selected english description to edit.
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $db ->select()
			->from(array('trn'=>'trans_nilai'),array('trn.*','IdTransNilai'=>'trn.id') )
			->join(array('cg'=>'tbl_split_coursegroup'),'trn.IdSplitGroup=cg.Id')
			->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('year'=>'LEFT(s.SemesterMainCode,4)','s.SemesterCountType'))
			->join(array('s1'=>'tbl_semestermaster'),'s1.IdSemesterMaster=cg.IdSemesterSubject',array('SemesterSubject'=>'s1.SemesterMainName','IdSemester'=>'s1.IdSemesterMaster'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('IdSubject'=>'sm.IdSubject','shortname','subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','sks_mk'=>'CreditHours','sks_tm'=>'ch_tutorial','sks_prak'=>'ch_practice'))
			->join(array('sg'=>'tbl_studentregistration'),'sg.IdStudentRegistration=trn.IdStudentRegistration',array('nim'=>'registrationId','sg.IdIntake'))
			->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=sg.IdApplication',array('fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',sp.appl_fname, sp.appl_mname, sp.appl_lname)")))
			->join(array('prg'=>'tbl_program'),'prg.IdProgram=sg.IdProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode','Strata_code_EPSBED'=>'Strata_code_EPSBED', 'Program_code_EPSBED'=>'Program_code_EPSBED','id_sms','prg.IdProgram'))
			->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=prg.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
			->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED'))
			->join(array('intk'=>'tbl_intake'),'intk.IdIntake=sg.IdIntake')
			->order('sg.registrationid')
			->where('trn.id = ?', $Id);
			$result = $db->fetchRow($lstrSelect);
			return $result;
		}
		public function addHistory($table,$tablehistory,$where) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $db ->select()
				->from($table)
				->where($where);
			$result = $db->fetchRow($lstrSelect);
			$db->insert($tablehistory,$result);
		}
		public function getData($table,$where){
			$db = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $db ->select()
			->from($table)
			->where($where);
			$result = $db->fetchAll($lstrSelect);
			return $result;
		}
		public function getTransNilmhs($Id){ //function to find the data to populate in a page of a selected english description to edit.
				
			$db = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $db ->select()
			->from(array('trn'=>'trans_nilai'),array('status_proses'=>'trn.status','trn.*','IdTransNilai'=>'trn.id') )
			->join(array('cg'=>'tbl_split_coursegroup'),'trn.IdSplitGroup=cg.Id',array('trn.IdSplitGroup','nm_kls'=>'cg.GroupCode'))
			->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('year'=>'LEFT(s.SemesterMainCode,4)','s.SemesterCountType'))
			->join(array('s1'=>'tbl_semestermaster'),'s1.IdSemesterMaster=cg.IdSemesterSubject',array('SemesterSubject'=>'s1.SemesterMainName','IdSemester'=>'s1.IdSemesterMaster'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('IdSubject'=>'sm.IdSubject','shortname','subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','sks_mk'=>'CreditHours','sks_tm'=>'ch_tutorial','sks_prak'=>'ch_practice'))
			->join(array('sg'=>'tbl_studentregistration'),'sg.IdStudentRegistration=trn.IdStudentRegistration',array('nim'=>'registrationId'))
			->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=sg.IdApplication',array('fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',sp.appl_fname, sp.appl_mname, sp.appl_lname)")))
			->join(array('prg'=>'tbl_program'),'prg.IdProgram=sg.IdProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode','Strata_code_EPSBED'=>'Strata_code_EPSBED', 'Program_code_EPSBED'=>'Program_code_EPSBED','id_sms','prg.IdProgram'))
			->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=prg.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
			->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED'))
			->join(array('intk'=>'tbl_intake'),'intk.IdIntake=sg.IdIntake')
			->order('sg.registrationid')
			->where('trn.id = ?', $Id);
			$result = $db->fetchAll($lstrSelect);
			
			//echo $lstrSelect;exit;
			
			if($result){
				$Feeder = new Reports_Model_DbTable_Feedervalue();
				$mata_kuliah=false;
				foreach ( $result AS $index=>$data )
				{
					//get from local
					if ($data['id_sms']!='')
						$mata_kuliah=$this->getMatakuliah($data['shortname'],$data['id_sms']);
					//get from feeder
					if (!$mata_kuliah)
						$mata_kuliah = $Feeder->getMataKuliah($data['shortname'],$data['nim']);
						
					//if (isset($data['nilai_huruf'])) {
						if ($data['exam_status']=='NR' ||
							$data['exam_status']=='MG' ||
							$data['exam_status']=='IN' ||
							$data['exam_status']=='W' ||
							$data['exam_status']=='FR' ||
							$data['exam_status']=='F'  || $data['exam_status']=='X'
									) {
									$data['nilai_huruf']= 'E';
									$data['nilai_indeks']= '0';
						}
						
					//}
					$finalData = array(
							$data['IdStudentRegSubjects'],
							$data['year'].$data['SemesterCountType'],
							$data['univ_mohe_code'],
							$data['Strata_code_EPSBED'],
							$data['Program_code_EPSBED'],
							$data['nim'],
							$data['shortname'],
							$data['nilai_huruf'],
							$data['nilai_indeks'],
							$data['nm_kls'],
							$data['id_sms'],
							$mata_kuliah['id_mk'],
							$mata_kuliah['sks_mk'],
							$mata_kuliah['sks_tm'],
							$mata_kuliah['sks_prak'],
							$data['IdSplitGroup'],
							$data['nilai_angka'],
							$data['IdSubject'],
							$data['status_proses']
							
					);
				}
			}
			/*echo"<pre>" ;
			 print_r($finalData);exit();*/
			return $finalData;

		}
		
public function getstudp($id,$idprogram=null){ //function to find the data to populate in a page of a selected english description to edit.
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select = $db->select()
			->from(array('sa' => 'tbl_studentregistration'), array('sa.registrationId','IdApplication','jenis_pendaftaran','sks_diakui'))
			->join(array('sp'=>'student_profile'),'sp.appl_id=sa.IdApplication',array(
					'id', 'appl_fname'=>'trim(appl_fname)','appl_mname'=>'trim(appl_mname)','appl_lname'=>'trim(appl_lname)' ,'appl_birth_place','appl_dob','appl_gender','appl_religion','appl_nationality',
					'appl_address1','appl_address_rt','appl_address_rw','appl_postcode',
					'appl_kelurahan','appl_nik','appl_nis','appl_phone_hp','appl_phone_home','npwp','appl_email'))
					->join(array('intk' => 'tbl_intake'), 'intk.IdIntake=sa.IdIntake', array('intk.class_start','mulai_smt'=>'CONCAT(left(intk.IntakeId,4),right(intk.IntakeId,1))'))
			->join(array('p'=>'tbl_program'),'sa.IdProgram=p.IdProgram',array('p.IdProgram','Program_code_EPSBED','Strata_code_EPSBED','id_sms'))
			->joinLeft(array('n'=>'tbl_countries'),'sp.appl_nationality=n.idCountry',array('wn'=>'CountryIso'))
			->where('sp.id = '.$id)
			->where('sa.IdProgram = '.$idprogram);
			//echo $select;exit;
			$result = $db->fetchRow($select);
		//get mother and father name
		
		$familyDb = new App_Model_Application_DbTable_ApplicantFamily();
		 
			$mother = $familyDb->getData($result['IdApplication'], 21);
			$father = $familyDb->getData($result['IdApplication'], 20);
			$result['MotherName']='';
			$result['FatherName']='';
			if($mother){
				$result['MotherName']=$mother['af_name'];
				$result['tgl_lahir_ibu']=$mother['af_dbo'];
				
			}
			if($father){
				$result['FatherName']=$father['af_name'];
				$result['tgl_lahir_ayah']=$mother['af_dbo'];
			}
		 
		//echo var_dump($result);exit;
			//print_r($result);
			return $result;
		}
	
		public function getStudentProfile($id,$idprogram=null){ //function to find the data to populate in a page of a selected english description to edit.
			$db = Zend_Db_Table::getDefaultAdapter();
				
			$select = $db->select()
			->from(array('sa' => 'tbl_studentregistration'), array('sa.registrationId','IdApplication','jenis_pendaftaran','sks_diakui'))
			->join(array('sp'=>'student_profile'),'sp.appl_id=sa.IdApplication',array(
					'id', 'appl_fname'=>'trim(appl_fname)','appl_mname'=>'trim(appl_mname)','appl_lname'=>'trim(appl_lname)' ,'appl_birth_place','appl_dob','appl_gender','appl_religion','appl_nationality',
					'appl_address1','appl_address_rt','appl_address_rw','appl_postcode',
					'appl_kelurahan','appl_nik','appl_nis','appl_phone_hp','appl_phone_home','npwp','appl_email'))
					->join(array('intk' => 'tbl_intake'), 'intk.IdIntake=sa.IdIntake', array('intk.class_start','mulai_smt'=>'CONCAT(left(intk.IntakeId,4),right(intk.IntakeId,1))'))
					->join(array('p'=>'tbl_program'),'sa.IdProgram=p.IdProgram',array('p.IdProgram','Program_code_EPSBED','Strata_code_EPSBED','id_sms'))
					->joinLeft(array('n'=>'tbl_countries'),'sp.appl_nationality=n.idCountry',array('wn'=>'CountryIso'))
					->where('sa.IdStudentRegistration = '.$id)
					->where('sa.IdProgram = '.$idprogram);
			//echo $select;exit;
			$result = $db->fetchRow($select);
			//get mother and father name
		
			$familyDb = new App_Model_Application_DbTable_ApplicantFamily();
				
			$mother = $familyDb->getData($result['IdApplication'], 21);
			$father = $familyDb->getData($result['IdApplication'], 20);
			$result['MotherName']='';
			$result['FatherName']='';
			if($mother){
				$result['MotherName']=$mother['af_name'];
				$result['tgl_lahir_ibu']=$mother['af_dbo'];
		
			}
			if($father){
				$result['FatherName']=$father['af_name'];
				$result['tgl_lahir_ayah']=$mother['af_dbo'];
			}
				
			//echo var_dump($result);exit;
			//print_r($result);
			return $result;
		}

	public function insertmhs($data){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_name,$data);
		return $db->lastInsertId();
	}
	
	protected $_name2 = 'mahasiswa_pt';
	public function insertmahasiswa_pt($data){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_name2,$data);
		return $db->lastInsertId();
	}
	
	public function isInMahasiswa($applid) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('mhs'=>'mahasiswa'))
		->where('appl_id=?',$applid);
		$result = $db->fetchRow($select);
		if ($result) return true; else return false;
	}
	
	public function isMahasiswaNotToFeeder($id) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('mhs'=>'mahasiswa'))
		->join(array('sp'=>'student_profile'),'sp.appl_id=mhs.appl_id')
		->where('sp.id=?',$id);
		//->where('mhs.id_pdref=""or mhs.id_pdref is null');
		$result = $db->fetchRow($select);
		return $result;
	}
	
	public function isMahasiswaPtNotToFeeder($regid) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('mhs'=>'mahasiswa_pt'))
		->where('nipd=?',$regid);
		//->where('id_reg_pdref is null or id_reg_pdref=""');
		$result = $db->fetchRow($select);
		return $result;
	}
	public function isInMahasiswaPt($applid,$idsms) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('mhs'=>'mahasiswa_pt'))
		->where('id_sms=?',$idsms)
		->where('trim(nipd)=?',$applid);
		$result = $db->fetchRow($select);
		return $result;//if ($result) return true; else return false;
	}

	public function mhsisApprove($formData,$id) {
		$temp=array();
		$temp2=array();
		$semester=$formData['IdSemester'];
		$db = Zend_Db_Table::getDefaultAdapter();
		//get semester pelaporan
		$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
		$sem=$dbSem->getData($semester);
		$yearintake=substr($sem['SemesterMainCode'],0,4).$sem['SemesterCountType'];
		$acadyear=$sem['idacadyear'];
		$semcounttype=$sem['SemesterCountType'];
		$select=$db->select()
		->from(array('a'=>'tbl_semestermaster'))
		->where('a.idacadyear=?',$acadyear)
		->where('a.SemesterCountType=?',$semcounttype)
		->where('a.SemesterFunctionType=5');
		//echo $select;exit;
		$semtransfer=$db->fetchRow($select);
		
		unset($formData['IdSemester']);
		//echo var_dump($formData);exit;
		$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		$feedval= new Reports_Model_DbTable_Feedervalue();
		
		$idstudentreg=$formData['registrationId'];
		unset($formData['registrationId']);
		$dbMsh=new Registration_Model_DbTable_Studentregistration();
		$idreg=$dbMsh->SearchStudent(array('IdStudent'=>$idstudentreg,'IdProgram'=>$formData['IdProgram']));
		//echo var_dump($idreg);exit;
		$idreg=$idreg[0];
		$idintake=$idreg['IdIntake'];
		$trxid=$idreg['transaction_id'];
		
		//get noform
		$dbTransaction=new App_Model_Application_DbTable_ApplicantTransaction();
		$trx=$dbTransaction->getDataById($trxid);
		//echo var_dump($trx);exit;
		$noform=$trx['at_pes_id'];
		$dbIntake=new GeneralSetup_Model_DbTable_Intake();
		$intake=$dbIntake->fngetIntakeById($idintake);
		$yearintake=substr($intake['IntakeId'],0,4).substr($intake['IntakeId'],10,1);
		//echo $yearintake;exit;
		if ($idreg['IdStudentRegistration']!=null) {
			$dbStatus=new Registration_Model_DbTable_Studentregistration();
		
			$semstatus=$dbStatus->fetchStudentProfileSemester($idreg['IdStudentRegistration'],$semester);
			if ($semstatus) {
				$idstat=$semstatus['pdpt_code'];
			}  else {
				$idstat='A';
			}
			
		} else $idstat='K';
		
		//$formData['id']= $id;
		$idprogram=$formData['IdProgram'];
		unset($formData['IdProgram']);
		//echo var_dump($formData);exit;
		$db->update('student_profile',$formData,'appl_id ='.$idreg['IdApplication']);
		//get student profile
		$getstud=$this->getStudentProfile($idreg['IdStudentRegistration'],$idprogram);
		//echo"<pre>" ;	print_r ($getstud) ; exit();
		//unset($formData['IdProgram']);
		$insertdata = array();
		$temp = array();
		if ($getstud['appl_lname']!='') $stdname=$getstud['appl_lname'];
		else $stdname='';
		if ($getstud['appl_mname']!='') $stdname=$getstud['appl_mname'].' '.$stdname;
		if ($getstud['appl_fname']!='') $stdname=$getstud['appl_fname'].' '.$stdname;
		$temp['nm_pd'] =$stdname ;
		$temp['tmpt_lahir'] = $getstud['appl_birth_place'];
		$temp['tgl_lahir'] = date('Y-m-d',strtotime($getstud['appl_dob']));
		//$temp['kewarganegaraan'] = ($getstud[appl_nationality]);
		///////jantina/////////////////////////
		$temp['jk'] = $getstud['appl_gender'];
		if($getstud['appl_gender']== 1){$temp['jk'] = 'L' ;}
		else if($getstud['appl_gender']== 2){$temp['jk'] = 'P' ;}
		else if($getstud['appl_gender']== null || $getstud['appl_gender']== "null" || $getstud['appl_gender']== ""){$temp['jk'] = 'L' ;}
		else {$temp['jk'] = 'L';}
		//echo"<pre>" ;	print_r ($temp) ; exit();
		/////////id agama////////////////////////
		$temp['id_agama'] = ($getstud['appl_religion']);
		if($getstud['appl_religion']== 5){$temp['id_agama'] = 1 ;}
		else if($getstud['appl_religion']== 7){$temp['id_agama'] = 2 ;}
		else if($getstud['appl_religion']== 6){$temp['id_agama'] = 3 ;}
		else if($getstud['appl_religion']== 10){$temp['id_agama'] = 4 ;}
		else if($getstud['appl_religion']== 8){$temp['id_agama'] = 5 ;}
		else if($getstud['appl_religion']== 11){$temp['id_agama'] = 6 ;}
		else if($getstud['appl_religion']== null || $getstud['appl_religion']== "null" || $getstud['appl_religion']== ""){$temp['id_agama'] = 98 ;}
		else{  $temp['id_agama'] = 99 ;}
		/////////////////////////////////////////////////////////
		//$temp['id_sp'] = '113b4d06-34ca-4a6e-a29a-9af372933c8f';
		//$temp['stat_pd'] = $idstat;// $feedval->getStatPd($getstud['registrationId']); //kena amik dari status student
		$temp['id_kk'] = 0 ; // jenis_sert
		$temp['id_wil'] = '000000' ;
		$temp['a_terima_kps'] = 0 ;
		$temp['ds_kel'] = $getstud['appl_kelurahan'] ;
		$temp['id_kebutuhan_khusus_ayah'] = 0 ;
		$temp['nm_ibu_kandung'] = $getstud['MotherName'] ;
		$temp['nm_ayah'] = $getstud['FatherName'] ;
		$temp["id_kebutuhan_khusus_ibu"] = 0;
		$temp["kewarganegaraan"] = $getstud['wn'];
		$temp["jln"] = $getstud['appl_address1'] ;
		$temp["rt"] = $getstud['appl_address_rt'] ;
		$temp["rw"] = $getstud['appl_address_rw'] ;
		$temp["kode_pos"] = $getstud['appl_postcode'] ;
		$temp['appl_id']=$getstud['IdApplication'];
		$temp["nik"] = $getstud['appl_nik'] ;
		$temp["nisn"] = $getstud['appl_nis'] ;
		$temp["email"] = $getstud['appl_email'] ;
		$temp["no_hp"] = $getstud['appl_phone_hp'] ;
		$temp["no_telp_rmh"] = $getstud['appl_phone_home'] ;
		//cek feeder using registration id 
		$filter="trim(nipd)='".$idstudentreg."' and id_sms='".$getstud['id_sms']."'";
		
		$response=$Feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
		$idregpd='';
		if (count($response)>0) {
			$idpd=$response['id_pd'];
			$idregpd=$response['id_reg_pd'];
			$idsms=$response['id_sms'];
			$idsp=$response['id_sp'];
			//cek mahasiswa
			$filter="id_pd='".$idpd."'";
			$response=$Feeder->fnGetRecord('mahasiswa.raw', $filter);
			//echo var_dump($response);exit;
			if (count($response)>0) {
				//student in feeder
				$temp['id_pdref']=$response['id_pd'];
				//$temp['id_sp']=$response['id_sp'];
				$temp['error_code']='';
				$temp['error_desc']=null;
				//$insertdata = $temp;
			}	
		} else {
			
			$temp['id_pdref']='';
			$filter="trim(UPPER(nm_ibu_kandung))='".strtoupper($getstud['MotherName'])."'  and tgl_lahir='".date('Y-m-d',strtotime($getstud['appl_dob']))."' and trim(UPPER(nm_pd)) ='".trim(strtoupper($stdname))."'";
			$response=$Feeder->fnGetRecord('mahasiswa.raw', $filter);
			//echo var_dump($response); echo $filter;exit;
			if (count($response)>0) {
				//student in feeder
				$temp['id_pdref']=$response['id_pd'];
				//$temp['id_sp']=$response['id_sp'];
				$temp['error_code']='';
				$temp['error_desc']=null;
				//$insertdata = $temp;
			}
			
		}
		//simpan mahasiswa	 
		$table = new Reports_Model_DbTable_Mhssetup();
		if ($this->isInMahasiswa($getstud['IdApplication'])) {
			$table->fnupdateMhsByApplId($temp, $getstud['IdApplication']);
			$mhs=$this->fnGetMhsDetailsByApplId($getstud['IdApplication']);
			$last_id=$mhs['id_pd'];
		}
		else
			$last_id = $table->insertmhs($temp);
				
		$temp2['id_pdref'] = $last_id ;
		
		//kena map program_id to table sms(feeder)
		if ($idregpd!='') {
			$temp2['id_pd']=$idpd;
			$temp2['id_reg_pdref']=$idregpd;
			$temp2['id_sp']=$idsp;
		} else {
			$temp2['id_pd']='';
			$temp2['id_reg_pdref']='';
		}
		if ($getstud['id_sms']=='') {
			$idsms=$feedval->getIdSms($getstud['registrationId'],$idprogram);
			$dbprogram=new GeneralSetup_Model_DbTable_Program();
			$dbprogram->fnupdateProgram(array('id_sms'=>$idsms), $getstud['IdProgram']);
		} else $idsms=$getstud['id_sms'];
	 
		//get biaya masuk kuliah
		$dbInvoiceDetail=new Studentfinance_Model_DbTable_InvoiceDetail();
		
		$biayamasukkuliah=$dbInvoiceDetail->getDataSPP($noform);
		$temp2['id_sms'] = $idsms;
		$temp2['id_smsref'] = $getstud['Strata_code_EPSBED'];
		$temp2['id_sp'] = '113b4d06-34ca-4a6e-a29a-9af372933c8f';
		$temp2['id_spref'] = "031016";
		//$temp2['nipd'] = $getstud['registrationId'] ;
		$temp2['nipd'] = $getstud['registrationId'] ;
		$temp2['tgl_masuk_sp'] = $getstud['class_start'] ;
		$temp2['id_jns_daftar'] = $getstud['jenis_pendaftaran'] ;
		$temp2['sks_diakui'] = $getstud['sks_diakui'] ;
		$temp2['mulai_smt'] = $yearintake;//$getstud['mulai_smt'] ;
		$temp2['error_code']='';
		$temp2['error_desc']=null;
		$temp2['status']="0";
		$temp2['biaya_masuk_kuliah']=$biayamasukkuliah;
		
		$insertdata2 = $temp2;
		//echo var_dump($temp2);
		//echo $idsms;exit;
		$isin=$this->isInMahasiswaPt($getstud['registrationId'],$idsms);
		//echo var_dump($isin);exit;
		if ($isin) {
			if ($isin['id_jns_daftar']=="2") {
				
				unset($temp2['id_jns_daftar']);
				if ($isin['sks_diakui']==0) {
					$select=$db->select()
					->from(array('a'=>'tbl_studentregsubjects'),array())
					->join(array('b'=>'tbl_subjectmaster'),'a.IdSubject=b.IdSubject',array('sks'=>'SUM(CreditHours)'))
					->where('a.idstudentregistration=?',$idreg['IdStudentRegistration'])
					->where('a.IdSemestermain=?',$semtransfer['IdSemesterMaster']);
					//echo $select;
					$row=$db->fetchRow($select);
					//echo var_dump($row);exit;
					if ($row) {
						$this->fnupdateData('tbl_studentregistration', array('sks_diakui'=>$row['sks']), 'IdStudentRegistration='.$idreg['IdStudentRegistration']);
						$this->fnupdateData('mahasiswa_pt', array('sks_diakui'=>$row['sks']), 'nipd="'.$idstudentreg.'"');
					}
				}
				
			}
			$this->fnupdateMhsptByRegistrationId($temp2, $getstud['registrationId'],$idsms);
			
		}else
			$table->insertmahasiswa_pt($insertdata2);
		
	}
	
	
	public function mhsisApproveUpdate($formData,$id) {
		$temp=array();
		$temp2=array();
		$semester=$formData['IdSemester'];
		//get semester pelaporan
		$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
		$sem=$dbSem->getData($semester);
		$yearintake=substr($sem['SemesterMainCode'],0,4).$sem['SemesterCountType'];
		unset($formData['IdSemester']);
		//echo var_dump($formData);exit;
		$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		$feedval= new Reports_Model_DbTable_Feedervalue();
		$db = Zend_Db_Table::getDefaultAdapter();
		$idstudentreg=$formData['registrationId'];
		unset($formData['registrationId']);
		$dbMsh=new Registration_Model_DbTable_Studentregistration();
		$idreg=$dbMsh->SearchStudent(array('IdStudent'=>$idstudentreg,'IdProgram'=>$formData['IdProgram']));
		//echo var_dump($idreg);exit;
		$idreg=$idreg[0];
		$idintake=$idreg['IdIntake'];
		$trxid=$idreg['transaction_id'];
	
		//get noform
		$dbTransaction=new App_Model_Application_DbTable_ApplicantTransaction();
		$trx=$dbTransaction->getDataById($trxid);
		//echo var_dump($trx);exit;
		$noform=$trx['at_pes_id'];
		$dbIntake=new GeneralSetup_Model_DbTable_Intake();
		$intake=$dbIntake->fngetIntakeById($idintake);
		$yearintake=substr($intake['IntakeId'],0,4).substr($intake['IntakeId'],10,1);
		//echo $yearintake;exit;
		if ($idreg['IdStudentRegistration']!=null) {
			$dbStatus=new Registration_Model_DbTable_Studentregistration();
	
			$semstatus=$dbStatus->fetchStudentProfileSemester($idreg['IdStudentRegistration'],$semester);
			if ($semstatus) {
				$idstat=$semstatus['pdpt_code'];
			}  else {
				$idstat='A';
			}
				
		} else $idstat='K';
	
		//$formData['id']= $id;
		$idprogram=$formData['IdProgram'];
		unset($formData['IdProgram']);
		//echo var_dump($formData);exit;
		//get student profile
		$getstud=$this->getStudentProfile($idreg['IdStudentRegistration'],$idprogram);
		//echo"<pre>" ;	print_r ($getstud) ; exit();
		//unset($formData['IdProgram']);
		$insertdata = array();
		$temp = array();
	 	$temp['ds_kel'] = $getstud['appl_kelurahan'] ; 
		$temp["jln"] = $getstud['appl_address1'] ;
		$temp["rt"] = $getstud['appl_address_rt'] ;
		$temp["rw"] = $getstud['appl_address_rw'] ;
		$temp["kode_pos"] = $getstud['appl_postcode'] ; 
		$temp["nik"] = $getstud['appl_nik'] ; 
		$temp["email"] = $getstud['appl_email'] ;
		$temp["no_hp"] = $getstud['appl_phone_hp'] ;
		$temp["no_telp_rmh"] = $getstud['appl_phone_home'] ;
		$temp['status_update']="1";
		//cek feeder using registration id
		//echo $getstud['IdApplication'];
		//echo var_dump($temp);exit;
		//simpan mahasiswa
		$table = new Reports_Model_DbTable_Mhssetup();
		if ($this->isInMahasiswa($getstud['IdApplication'])) {
			$table->fnupdateMhsByApplId($temp, $getstud['IdApplication']);
			$db->update('student_profile',array('update_status'=>0),'appl_id ='.$idreg['IdApplication']);
			
		}
		  
	
	}
	
	
	public function mhsisUnApprove($regid,$id,$idprogram) {
		
		//cek mahasiswa to feeder
		$db=new Reports_Model_DbTable_Mhssetup();
		//get sms program
		$dbFeed=new Reports_Model_DbTable_Feedervalue();
		$sms=$dbFeed->getProgram($idprogram);
		$sms=$sms['id_sms'];
		$row=$db->isMahasiswaNotToFeeder($id);
		if ($row) {
			if ($row['id_pdref']=='' || $row['id_pdref']=null) {
				//delete mahasiswa
				$row=$this->getData('student_profile', 'id='.$id);
				//echo $row;exit;
				if ($row)
					$this->fndeleteData('mahasiswa', 'appl_id='.$row[0]['appl_id']);
				$row=$db->isMahasiswaPtNotToFeeder($regid);
				if ($row) {
					if ($row['id_reg_pdref']==''||$row['id_reg_pdref']==null)
						$this->fndeleteData('mahasiswa_pt', 'nipd='.$regid.' and id_sms="'.$sms.'"');
				}
				$this->fnupdateData('student_profile', array('status'=>null,'date_of_approval'=>null),'id='.$id);
			}  
		}  
		
		
	}
	
	public function mhswsApprove($formData,$id_pd,$registrationId) {
		
		$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		$db = Zend_Db_Table::getDefaultAdapter();
		$idsms=$formData['id_sms'];
		unset($formData['id_sms']);
		$select = $this->select()
		->from(array('m' => 'mahasiswa'),
				array('id_pdref', 'nm_pd', 'tmpt_lahir','tgl_lahir','jk','email','no_telp_rmh','no_hp','npwp',
						'id_agama','id_kk','ds_kel','id_wil','a_terima_kps','nik','nisn',
						'id_kebutuhan_khusus_ayah','nm_ibu_kandung','nm_ayah','jln','rt','rw','kode_pos',
						'id_kebutuhan_khusus_ibu','kewarganegaraan','n_of_update'))
		->where('id_pd='.$id_pd);
		$row = $db->fetchRow($select);
		//echo"<pre>" ;	print_r ($row) ; exit;
		//insert into table Mahsiswa WS
		//$registrationId=$formData['registrationId'];
		unset($formData['registrationId']);
		$records = $row ;
		$idpdref='';
		if (trim($row['kode_pos'])=='') $row['kode_pos']='12345';
		if ($records['id_pdref']=='') {
			
			$filter="trim(nm_pd)= '".$records['nm_pd']."' and tgl_lahir='".date('Y-m-d',strtotime($records['tgl_lahir']))."'";
			$response=$Feeder->fnGetRecord('mahasiswa.raw', $filter); 
			//echo var_dump($response);exit;
			//------------
			if (count($response)>0) {
				//data in feeder
				
				$idpdref=$response['id_pd'];
				$formData['id_pdref']= $idpdref ;
				$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
				//update feeder
				unset($records['id_pdref']);
				unset($records['id_pd']);
				$response=$Feeder->updateToFeeder('mahasiswa',array('id_pd'=>$response['id_pd']),  $records); //, );
				if ($response['result']['error_code']==0) {
					//update suceed, increase number of update
					$response=$response['result'];
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$formData['n_of_update']= $response['n_of_update']+1 ;	 
					$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
				} else {
					//update fail 
					$response=$response['result'];
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
				}
			} else {
				//data not in feeder so insert into feeder
				unset($records['id_pdref']);
				unset($records['n_of_update']);
				$response = $Feeder->insertToFeeder('mahasiswa',$records);
				if ($response['result']['error_code']==0) {
					//insert to feeder succeed, then update mahasiswa
					$response=$response['result'];
					$idpdref=$response['id_pd'];
					$formData['id_pdref']= $idpdref ;
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
					
				} else {
					//insert to feeder fail, save  error message
					$response=$response['result'];
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
				}
			}
		
			 
		} /* else {
			
			$idpdref=$records['id_pdref'];
			unset($records['id_pdref']);
			$nofupdate=$records['n_of_update'];
			unset($records['n_of_update']);
			$key=array('id_pd'=>$idpdref);
			$data= $records;
			//echo var_dump($key);exit;
			$response =$Feeder->updateToFeeder('mahasiswa',$key,$data);
			//echo var_dump($response);exit;
			if ($response['result']['error_code']==0) {
				//update succeed, increase number of update
				$response=$response['result'];
				$formData['error_code']= $response['error_code'] ;
				$formData['error_desc']= $response['error_desc'] ;
				$formData['n_of_update']= $nofupdate+1 ;
				
				$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
			} else {
				//update fail
				$response=$response['result'];
				$formData['error_code']= $response['error_code'] ;
				$formData['error_desc']= $response['error_desc'] ;
				//echo 'l';echo var_dump($formData);exit;
				$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
			}
		} */
		 
		//Insert record to WS table Mahasiswa_pt
		unset($formData['id_pdref']);
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->from(array('mpt' => 'mahasiswa_pt'),
				array('id_reg_pd','id_reg_pdref','id_sp','id_pd','id_sms','nipd',
						'tgl_masuk_sp','id_jns_daftar','mulai_smt','biaya_masuk_kuliah','sks_diakui','id_pt_asal','id_prodi_asal'))	//->order("id_sp desc")
		->setIntegrityCheck(false) // ADD This Lin
		->where('id_pdref='.$id_pd." and id_sms='".$idsms."' and nipd='".$registrationId."'");
		//->where('trim(nipd)=?',$registrationId);
		//echo $select;exit;
		$row2 = $db->fetchRow($select);
		if ($row2) {
			//echo var_dump($row2);exit;
			$idregpd=$row2['id_reg_pd'];
			$records1 = $row2 ;
			//mahasiswa pindahan get origin program
			if ($records1['id_jns_daftar']=="2") {
				$select=$db->select()
				->from(array('a'=>'tbl_apply_credit_transfer'))
				->join(array('b'=>'applicant_transaction'),'a.transaction_id=b.at_trans_id')
				->join(array('c'=>'tbl_studentregistration'),'c.transaction_id=b.at_trans_id')
				->join(array('p'=>'tbl_program'),'p.IdProgram=c.IdProgram')
				->where('c.registrationid=?',$records1['nipd'])
				->where('p.id_sms=?',$records1['id_sms']);
				$transfer=$db->fetchRow($select);
				//echo var_dump($transfer);
				//echo $select;exit;
				if ($transfer) {
					$records1['id_pt_asal']=$transfer['PT_Asal'];
					$records1['id_prodi_asal']=$transfer['Prodi_Asal'];
					$db->update('mahasiswa_pt',$records1,'id_reg_pd='.$idregpd);
				} else {
					$formData['error_desc']= 'Data program studi asal tidak ditemukan' ;
					$db->update('mahasiswa_pt',$formData,'id_reg_pd='.$idregpd);
					return;		
				}
				
			}
			//echo var_dump($records1);exit;
				if ($records1['id_reg_pdref']=='') {
					//register id  not found
					unset($records1['id_reg_pdref']);
					unset($records1['id_reg_pd']);
					if ($records1['id_pd']=="") $records1['id_pd']=$formData['id_pdref'];
					if ($records1['id_pd']=="") $records1['id_pd']=$records['id_pdref'];
						
					//echo 'insert';
					//check ada tidak
					$filter="trim(nipd)='".$records1['nipd']."' and id_sms='".$records1['id_sms']."'";
					$response=$Feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
				 //	echo var_dump($response);
					if (!$response) {
						if ($records1['id_pd']=='') $records1['id_pd']=$idpdref;
						$records1['tgl_create']=date('Y-m-d h:s:i');
						$records1['biaya_masuk_kuliah']=$records1['biaya_masuk_kuliah']*1;
						$response = $Feeder->insertToFeeder('mahasiswa_pt',$records1);
						//echo var_dump($records1);echo var_dump($response);exit;
						if ($response['result']['error_code']==0) {
								//insert to feeder succeed, then update mahasiswa
								$response=$response['result'];
								$idregpdref=$response['id_reg_pd'];
								$formData['id_pd']=$idpdref;
								$formData['id_reg_pdref']= $idregpdref ;
								$formData['error_code']= $response['error_code'] ;
								$formData['error_desc']= $response['error_desc'] ;
								$db->update('mahasiswa_pt',$formData,'id_reg_pd='.$idregpd);
								
						} else {
								//insert to feeder fail, save  error message
								$response=$response['result'];
								$formData['error_code']= $response['error_code'] ;
								$formData['error_desc']= $response['error_desc'] ;
								$db->update('mahasiswa_pt',$formData,'id_reg_pd='.$idregpd);
						}
					} else {
					//update mahasiswa_pt
						$formData['id_reg_pdref']=$response['id_reg_pd'];
						$db->update('mahasiswa_pt',$formData,'id_reg_pd='.$idregpd);
						
					}
				
					 
			} else 
				{
					//echo var_dump($response); 
					//$data=array('id_reg_pdref'=>$response['id_reg_pd'],
					//			'id_pd'=>$response['id_pd']);
					//echo var_dump($data);exit;
					//$db->update('mahasiswa_pt',$data,'id_reg_pd='.$idregpd);
					//----------------------
					//cek nilai transfer jika ada jnsmasuk tidak diupdate
					$filter="id_reg_pd='".$records1['id_reg_pdref']."'";
					$response=$Feeder->fnGetRecord('nilai_transfer.raw', $filter);
					//echo var_dump($response);exit;
					if (count($response)>0) {
						$records1['id_jns_daftar']="2";
					}
					$key=array('id_reg_pd'=>$records1['id_reg_pdref']); 
					$data=array('nipd'=>$records1['nipd'],'tgl_masuk_sp'=>$records1['tgl_masuk_sp'],
							'mulai_smt'=>$records1['mulai_smt'],
							'id_jns_daftar'=>$records1['id_jns_daftar'],
							'biaya_masuk_kuliah'=>$records1['biaya_masuk_kuliah']*1,
							'sks_diakui'=>$records1['sks_diakui'],
							'id_pt_asal'=>$records1['id_pt_asal'],
							'id_prodi_asal'=>$records1['id_prodi_asal']
							 
					);
					 
					if ($records1['id_jns_daftar']=="2") {
						$records1['id_pt_asal']=$transfer['PT_Asal'];
						$records1['id_prodi_asal']=$transfer['Prodi_Asal'];
						$db->update('mahasiswa_pt',$records1,'id_reg_pd='.$idregpd);
					} 
					
					$response=$Feeder->updateToFeeder('mahasiswa_pt',$key, $data);
					//echo var_dump($response);exit;
					if ($response['result']['error_code']==0) {
						//update succeed, increase number of update
						$response=$response['result'];
						$formData['error_code']= $response['error_code'] ;
						$formData['error_desc']= $response['error_desc'] ;
						$formData['n_of_update']= $response['n_of_update']+1 ;
						$db->update('mahasiswa_pt',$formData,'id_reg_pd='.$idregpd);
					} else {
						//update fail
						unset($formData);
						$response=$response['result'];
						$formData['error_code']= $response['error_code'] ;
						$formData['error_desc']= $response['error_desc'] ;
						//$formData['id_reg_pdref']=$response['id_reg_pd'];
						//echo var_dump($formData);exit;
						$db->update('mahasiswa_pt',$formData,'id_reg_pd='.$idregpd);
					}
				} 
			}
	}
	public function mhswsApproveUpdate($formData,$id_pd,$registrationId) {
	
		$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		$db = Zend_Db_Table::getDefaultAdapter();
		$idsms=$formData['id_sms'];
		unset($formData['id_sms']);
		$select = $this->select()
		->from(array('m' => 'mahasiswa'),
				array('appl_id','id_pdref','email','no_tel_rmh'=>'no_telp_rmh','no_hp','nik' ,'jln','rt','rw','kode_pos','ds_kel','n_of_update'))
				->where('id_pd='.$id_pd);
		$row = $db->fetchRow($select);
		$applid=$row['appl_id'];
		unset($row['appl_id']);
		unset($formData['registrationId']);
		$records = $row ;
		$idpdref='';
		if (trim($row['kode_pos'])=='') $row['kode_pos']='12345';
		if ($records['id_pdref']!='') {
		 		$idpdref=$records['id_pdref']; 
				
				//update feeder
				unset($records['id_pdref']);
				unset($records['n_of_update']);
				if ($records['no_tel_rmh']=="0" || $records['no_tel_rmh']=='' || strlen($records['no_tel_rmh'])<9) unset($records['no_tel_rmh']);
				if ($records['rt']=="0" || $records['rt']=='' ) unset($records['rt']);
				if ($records['rw']=="0" || $records['rw']=='' ) unset($records['rw']);
				if ($records['kode_pos']=="0" || $records['kode_pos']=='' ) unset($records['kode_pos']);
				
				//echo var_dump($records);exit;
				$response=$Feeder->updateToFeeder('mahasiswa',array('id_pd'=>$idpdref),  $records); //, );
				//echo var_dump($records);
				//echo var_dump($response);
				//echo $idpdref;
				//exit;
				if ($response['result']['error_code']==0) {
					//update suceed, increase number of update
					$response=$response['result'];
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'];
					$formData['status_update']= '0' ;
					//$formData['n_of_update']= $response['n_of_update']+1 ;
					$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
					$db->update('student_profile',array('update_status'=>'0'),'appl_id='.$applid);
				} else {
					//update fail
					$response=$response['result'];
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
				}
			 
	
		} 
	}
	
	public function mhswsMapUpdate($id_pd) {
	
		$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$select = $db->select()
		->from(array('m' => 'mahasiswa'),array('appl_id','id_pdref','n_of_update'))
		->join(array('a'=>'student_profile'),'m.appl_id=a.appl_id',array('email'=>'appl_email','no_hp'=>'appl_phone_hp','no_tel_rmh'=>'appl_phone_home','nik'=>'appl_nik',
				'jln'=>'appl_address1','rt'=>'appl_address_rt','rw'=>"appl_address_rw",'kode_pos'=>'appl_postcode','ds_kel'=>'appl_kelurahan'
		))		
		->where('m.appl_id='.$id_pd);
		$row = $db->fetchRow($select);
		  
		$records = $row ;
		$idpdref='';
		if (trim($row['kode_pos'])=='') $row['kode_pos']='12345';
		
		if ($records['id_pdref']!='') {
			$idpdref=$records['id_pdref'];
			$filter="id_pd='".$idpdref."'";
	 	 	$response=$Feeder->fnGetRecord('mahasiswa.raw',$filter); //, );
	 	 	 
			if (count($response)>0) {
				//update suceed, increase number of update
			 
				if ($response['jln']!=$records['jln'] 
					|| $response['rt']!=(int) $records['rt']
					|| $response['rw']!=(int) $records['rw']
					|| $response['ds_kel']!=$records['ds_kel']
					|| $response['kode_pos']!=$records['kode_pos']
					|| $response['nik']!=$records['nik']
					|| ($response['no_tel_rmh']!=$records['no_tel_rmh'] && $records['no_tel_rmh']!="0")
					|| $response['no_hp']!=$records['no_hp']
					|| $response['email']!=$records['email']
				) {
				 
					$formData['update_status']= '2' ; 
					$db->update('student_profile',$formData,'appl_id='.$records['appl_id']);
					//exit;
				} else  {
					$formData['update_status']= '5' ;
					$db->update('student_profile',$formData,'appl_id='.$records['appl_id']);
						
				}
			} else {
				//update fail
			
	
				$formData['update_status']= '3' ;
				$db->update('student_profile',$formData,'appl_id='.$records['appl_id']);
			}
	
	
		}
	}
	
	public function mhswsApproveOld($formData,$id_pd) {
	
		$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->from(array('m' => 'mahasiswa'),
				array('id_pdref','id_sp', 'nm_pd', 'tmpt_lahir','tgl_lahir','jk',
						'id_agama','id_kk','ds_kel','id_wil','a_terima_kps',
						'id_kebutuhan_khusus_ayah','nm_ibu_kandung','nm_ayah','jln','rt','rw','kode_pos',
						'id_kebutuhan_khusus_ibu','kewarganegaraan','n_of_update'))
						->where('id_pd='.$id_pd);
		$row = $db->fetchRow($select);
		//echo"<pre>" ;	print_r ($row) ;
		//insert into table Mahsiswa WS
		$records = $row ;
		$idpdref='';
		if ($row['kode_pos']=='') $row['kode_pos']='12345';
		if ($records['id_pdref']=='') {
				
			$filter="id_sp='".$records['id_sp']."'  and trim(nm_pd)= '".$records['nm_pd']."' and tgl_lahir='".date('Y-m-d',strtotime($records['tgl_lahir']))."'";
			$response=$Feeder->fnGetRecord('mahasiswa.raw', $filter);
			//echo var_dump($response);exit;
			//------------
			if (count($response)>0) {
				//data in feeder
	
				$idpdref=$response['id_pd'];
				$formData['id_pdref']= $idpdref ;
				$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
				//update feeder
				$response=$Feeder->updateToFeeder('mahasiswa',array('id_pd'=>$response['id_pd']),  $records); //, );
				if ($response['result']['error_code']==0) {
					//update suceed, increase number of update
					$response=$response['result'];
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$formData['n_of_update']= $response['n_of_update']+1 ;
					$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
				} else {
					//update fail
					$response=$response['result'];
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
				}
			} else {
				//data not in feeder so insert into feeder
				unset($records['id_pdref']);
				unset($records['n_of_update']);
				$response = $Feeder->insertToFeeder('mahasiswa',$records);
				if ($response['result']['error_code']==0) {
					//insert to feeder succeed, then update mahasiswa
					$response=$response['result'];
					$idpdref=$response['id_pd'];
					$formData['id_pdref']= $idpdref ;
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
						
				} else {
					//insert to feeder fail, save  error message
					$response=$response['result'];
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
				}
			}
	
	
		} else {
				
			$idpdref=$records['id_pdref'];
			unset($records['id_pdref']);
			unset($records['n_of_update']);
			$nofupdate=$records['n_of_update'];
			$key=array('id_pd'=>$idpdref);
			$data= $records;
	
			$response =$Feeder->updateToFeeder('mahasiswa',$key,$data);
			//echo var_dump($response);exit;
			if ($response['result']['error_code']==0) {
				//update succeed, increase number of update
				$response=$response['result'];
				$formData['error_code']= $response['error_code'] ;
				$formData['error_desc']= $response['error_desc'] ;
				$formData['n_of_update']= $nofupdate+1 ;
				//echo var_dump($formData);exit;
				$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
			} else {
				//update fail
				$response=$response['result'];
				$formData['error_code']= $response['error_code'] ;
				$formData['error_desc']= $response['error_desc'] ;
				//echo 'l';echo var_dump($formData);exit;
				$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
			}
		}
			
		//Insert record to WS table Mahasiswa_pt
		unset($formData['id_pdref']);
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->from(array('mpt' => 'mahasiswa_pt'),
				array('id_reg_pd','id_reg_pdref','id_sp','id_pd','id_sms','nipd','tgl_masuk_sp','id_jns_daftar','mulai_smt'))	//->order("id_sp desc")
				->setIntegrityCheck(false) // ADD This Lin
				->where('id_pdref='.$id_pd);
		$row2 = $db->fetchRow($select);
		//echo var_dump($row2);exit;
		$idregpd=$row2['id_reg_pd'];
		$records1 = $row2 ;
		if ($records1['id_reg_pdref']=='') {
			//register id  not found
			unset($records1['id_reg_pdref']);
			unset($records1['id_reg_pd']);
			if ($idpdref!='')
				$filter="id_pd='".$idpdref."' and id_sms='".$records1['id_sms']."' and id_sp='".$records1['id_sp']."'";
			else
				$filter="trim(nipd)='".$records1['nipd']."' and id_sms='".$records1['id_sms']."'  and id_sp='".$records1['id_sp']."'";
			$response=$Feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
			//echo var_dump($filter);
			if (count($response)==0) {
				//echo 'insert';
				if ($records1['id_pd']=='') $records1['id_pd']=$idpdref;
				$response = $Feeder->insertToFeeder('mahasiswa_pt',$records1);
				//echo var_dump($filter);exit;
				if ($response['result']['error_code']==0) {
					//insert to feeder succeed, then update mahasiswa
					$response=$response['result'];
					$idregpdref=$response['id_reg_pd'];
					$formData['id_pd']=$idpdref;
					$formData['id_reg_pdref']= $idregpdref ;
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$db->update('mahasiswa_pt',$formData,'id_reg_pd='.$idregpd);
	
				} else {
					//insert to feeder fail, save  error message
					$response=$response['result'];
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$db->update('mahasiswa_pt',$formData,'id_reg_pd='.$idregpd);
				}
					
			} else
			{
				//echo var_dump($response);
				$data=array('id_reg_pdref'=>$response['id_reg_pd'],
						'id_pd'=>$response['id_pd']);
				//echo var_dump($data);exit;
				$db->update('mahasiswa_pt',$data,'id_reg_pd='.$idregpd);
				$key=array('id_reg_pd'=>$response['id_reg_pd']);
	
				$response=$Feeder->updateToFeeder('mahasiswa_pt',$key, $records1);
				//echo var_dump($response);exit;
				if ($response['result']['error_code']==0) {
					//update succeed, increase number of update
					$response=$response['result'];
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$formData['n_of_update']= $response['n_of_update']+1 ;
					$db->update('mahasiswa_pt',$formData,'id_reg_pd='.$idregpd);
				} else {
					//update fail
					unset($formData);
					$response=$response['result'];
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					//$formData['id_reg_pdref']=$response['id_reg_pd'];
					//echo var_dump($formData);exit;
					$db->update('mahasiswa_pt',$formData,'id_reg_pd='.$idregpd);
				}
			}
		} else {
			//udate feeder
	
			$key=array('id_reg_pd'=>$records1['id_reg_pd']);
			unset($records1['id_reg_pd']);
			$response=$Feeder->updateToFeeder('mahasiswa_pt',$key, $records1);
			//echo var_dump($response);exit;
			if ($response['result']['error_code']==0) {
				//update succeed, increase number of update
				$response=$response['result'];
				$formData['error_code']= $response['error_code'] ;
				$formData['error_desc']= $response['error_desc'] ;
				$formData['n_of_update']= $response['n_of_update']+1 ;
				$db->update('mahasiswa_pt',$formData,'id_reg_pd='.$idregpd);
			} else {
				//update fail
				unset($formData);
				$response=$response['result'];
				$formData['error_code']= $response['error_code'] ;
				$formData['error_desc']= $response['error_desc'] ;
				//$formData['id_reg_pdref']=$response['id_reg_pd'];
				//echo var_dump($formData);exit;
				$db->update('mahasiswa_pt',$formData,'id_reg_pd='.$idregpd);
			}
				
				
		}
	}
	
	public function fnGetDosenDetails(){//function to display all  details in list
			
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		//echo 
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"kelas_kuliah"),array("a.*"))
		->joinLeft(array('b'=>'ajar_dosen'),'a.id=b.id_klsm',array('b.id_klsm','b.id_reg_ptk','b.id_ajar','b.sks_subst_tot',
				'b.sks_tm_subst','b.jml_tm_renc','b.jml_tm_real','b.id_jns_eval',"id2"=>"b.id",))
		->order("a.id desc");
		//->joinLeft(array('mpt' => 'mahasiswa_pt'),'a.id_pd=mpt.id_pdref',array(
		//		'id_pd','id_sp','id_pd','id_sms','nipd','tgl_masuk_sp','id_jns_daftar'));
		//->where("a.id_pd = 47") ;
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fnSearchLecturerTransaction($post){//function to display all  details in list
			
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		//echo
		$lstrSelect = $lobjDbAdpt->select()
		 		->from(array("a"=>"kelas_kuliah"),array("a.*",'id_kls'=>'a.id_kls'))
				->join(array('cg'=>'tbl_split_coursegroup'),'a.IdSplitGroup=cg.Id',array('GroupName'))
				->join(array('b'=>'ajar_dosen'),'a.id=b.id_klsm',array('b.id_klsm','b.id_reg_ptk','b.id_ajar','b.sks_subst_tot',
				'b.sks_tm_subst','b.jml_tm_renc','b.jml_tm_real','b.id_jns_eval',"id2"=>"b.id",'status','error_desc'));
		//echo $lstrSelect;exit;
		if(isset($post['IdSemester']) && !empty($post['IdSemester']) ){
			$lstrSelect = $lstrSelect->where("cg.IdSemester = ?",$post['IdSemester']);
		}
		
		if(isset($post['programme']) && !empty($post['programme']) ){
			$lstrSelect = $lstrSelect->where("cg.IdProgram  = ?",$post['programme']);
		}
		
		if(isset($post['IdSubject']) && !empty($post['IdSubject']) ){
			$lstrSelect = $lstrSelect->where("cg.IdSubject  = ?",$post['IdSubject']);
		}
		if(isset($post['IdSplitGroup']) && !empty($post['IdSplitGroup']) ){
			$lstrSelect = $lstrSelect->where("cg.IdSplitGroup  = ?",$post['IdSplitGroup']);
		}
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		 
		//echo var_dump($larrResult);exit;
		foreach ($larrResult as $key=>$value) {
			$regptk=$value['id_reg_ptk'];
			$lstrSelect = $lobjDbAdpt->select()
			 ->from(array("a"=>"dosen_pt"))
			 ->where('a.id_reg_ptk=?',$regptk);
			$row=$this->lobjDbAdpt->fetchRow($lstrSelect);
			if ($row)
				$larrResult[$key]['IdStaff']=$row['IdStaff'];
			else $larrResult[$key]['IdStaff']=0;
			
		}
		return $larrResult;
	}
	
	public function fnGetTrakmDetails(){//function to display all  details in list
			
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"kuliah_mahasiswa"),array("a.*"))
		->order("a.id desc");
		//->joinLeft(array('mpt'=>'mahasiswa_pt'),'a.id_reg_pd=mpt.id_reg_pd',array("mpt.*"));
		//->joinLeft(array('mpt'=>'mahasiswa_pt'),'a.id_pd=mpt.id_pdref',array('nipd','tgl_masuk_sp','id_reg_pdref'));
		//->joinLeft(array('mpt' => 'mahasiswa_pt'),'a.id_pd=mpt.id_pdref',array(
		//		'id_pd','id_sp','id_pd','id_sms','nipd','tgl_masuk_sp','id_jns_daftar'));
		//->where("a.id_pd = 47") ;
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}	
	
	public function fnGetTransmhs(){//function to display all  details in list
			
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		//echo 
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"kelas_kuliah"),array("a.*"))
		->joinLeft(array('n'=>'nilai'),'a.id=n.id_klsk',array("n.*"))
		//->joinLeft(array('s'=>'sms'),'a.id_smsref=s.kode_prodi',array('s.*'))
		//->joinLeft(array('mpt'=>'mahasiswa_pt'),'a.id_pd=mpt.id_pdref',array('nipd','tgl_masuk_sp','id_reg_pdref'));
		//->joinLeft(array('mpt' => 'mahasiswa_pt'),'a.id_pd=mpt.id_pdref',array(
		//		'id_pd','id_sp','id_pd','id_sms','nipd','tgl_masuk_sp','id_jns_daftar'));
		//->where("a.id_pd = 47") ;
		->order("a.id desc");
		 $larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		/*echo"<pre>" ;
		print_r($larrResult);
		exit();*/
		return $larrResult;
	}
	
	
	public function fnGetStudentRegSubjects($post){//function to display all  details in list
			
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo
		$lstrSelect = $db ->select()
							//->from(array('cgsm'=>'tbl_course_group_student_mapping'))
							->from(array('ctg'=>'tbl_course_tagging_group') )
							->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=ctg.IdSemester',array('year'=>'LEFT(SemesterMainCode,4)','SemesterCountType'))
							->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=ctg.IdSubject',array('shortname','subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage'))
							->join(array('sreg'=>'tbl_studentregsubjects'),'sreg.IdCourseTaggingGroup=ctg.IdCourseTaggingGroup', array('IdStudentRegSubjects','grade_point','grade_name','exam_status'))
							->join(array('sg'=>'tbl_studentregistration'),'sg.IdStudentRegistration=sreg.IdStudentRegistration',array('nim'=>'registrationId','sg.IdStudentRegistration'))	
							->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=sg.IdApplication',array('fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',sp.appl_fname, sp.appl_mname, sp.appl_lname)")))
							->join(array('prg'=>'tbl_program'),'prg.IdProgram=sg.IdProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode','Strata_code_EPSBED'=>'Strata_code_EPSBED', 'Program_code_EPSBED'=>'Program_code_EPSBED','id_sms'))
							
							->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=prg.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
							->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED'))
							->join(array('intk'=>'tbl_intake'),'intk.IdIntake=sg.IdIntake')
							->order('sg.registrationid');
		
		if(isset($post['intake_id']) && !empty($post['intake_id']) ){
			$lstrSelect = $lstrSelect->where("intk.IdIntake = ?",$post['intake_id']);
		}
		
		if(isset($post['IdSemester']) && !empty($post['IdSemester']) ){
			$lstrSelect = $lstrSelect->where("sreg.IdSemesterMain = ?",$post['IdSemester']);
		}
		
		if(isset($post['programme']) && !empty($post['programme']) ){
			$lstrSelect = $lstrSelect->where("prg.IdProgram  = ?",$post['programme']);
		}	

		if(isset($post['IdStudent']) && !empty($post['IdStudent']) ){
			$lstrSelect = $lstrSelect->where("sg.registrationid  = ?",$post['IdStudent']);
		}
		$data = $db->fetchAll($lstrSelect);
		foreach ($data as $key=>$value) {
			 
			if ($this->isGraduate($value['IdStudentRegistration'])) {
				if ($this->isGraduateInSemester($value['IdStudentRegistration'], $post['IdSemester']))
					$data[$key]['aktif']='L';
				else $data[$key]['aktif']='OL';
			} else
				$data[$key]['aktif']='A';
			

			//cek cuti/defer
			$dbDefer=new Records_Model_DbTable_Defer();
			if ($dbDefer->isDefer($value['IdStudentRegistration'], $post['IdSemester']))
				$data[$key]['aktif']='C';
			 
			//cek data mahasiswa_pt
			$row=$this->getMahasiswaPT($value['nim'],$value['id_sms']);
			if (!$row) $data[$key]['aktif']='mhs_pt kosong';
			else if ($row['id_reg_pdref']=='') $data[$key]['aktif']='blm registrasi feeder';
			 
			//cek approval
			if ($this->isInKuliahByNim($post['IdSemester'], $value['nim'])) $data[$key]['status']='Approved';
			else $data[$key]['status']='';
		}
		//echo var_dump($data);exit;
		return $data;
	}
	
	public function fnGetActiveClass($post){//function to display all  details in list
			
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo var_dump($post);exit;
		$dbsem=new GeneralSetup_Model_DbTable_Semestermaster();
		if ($post['semester']=='') $post['semester']=$post['IdSemester'];
		$sem=$dbsem->getData($post['IdSemester']);
		$semlap=$dbsem->getData($post['semester']);
		 
		 
		$acadyear=$sem['idacadyear'];
		if ($semlap['SemesterCountType']=="6") $semlap['SemesterCountType']="3";
		$year=substr($semlap['SemesterMainCode'],0,4).$semlap['SemesterCountType'];
		$gasal_genap=$sem['SemesterCountType'];
		$db = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect1 = $db ->select()
		->from(array('sg'=>'tbl_semestermaster'),array('IdSemesterMaster'))
		->where('sg.idacadyear=?',$acadyear)
		->where('sg.SemesterFunctionType in ("0","1","3","4")')
		->where('sg.SemesterCountType=?',$gasal_genap);
		 
		//---------------------
		
		$lstrSelect = $db ->select()
		->from(array('ctg'=>'tbl_course_tagging_group'))
		->join(array('p'=>'course_group_program'),'ctg.IdCourseTaggingGroup=p.group_id')
		->joinLeft(array('st'=>'tbl_staffmaster'),'ctg.IdLecturer=st.IdStaff',array('IdStaff'=>'st.IdStaff','fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',st.firstname, st.secondname, st.thirdname)"),'Dosen_Code_EPSBED','id_sdm','status'))
		->where('ctg.IdSemester in (?)',$lstrSelect1)
		->where('p.program_id=?',$post['programme']);
		 
		if(isset($post['IdSubject']) && !empty($post['IdSubject']) ){
			$lstrSelect = $lstrSelect->where("ctg.IdSubject  = ?",$post['IdSubject']);
		}
		
		if(isset($post['GroupCode']) && !empty($post['GroupCode']) ){
			$lstrSelect = $lstrSelect->where("ctg.GroupCode  = ?",$post['GroupCode']);
		} 
		
		if(isset($post['IdCourseTaggingGroup']) && !empty($post['IdCourseTaggingGroup']) ){
			$lstrSelect = $lstrSelect->where("ctg.IdCourseTaggingGroup  = ?",$post['IdCourseTaggingGroup']);
		}
		
		 
		//echo $lstrSelect;exit;
		
		$larrResult = $db->fetchAll($lstrSelect);
		
		//delete record if in tbl_split_group
		//echo var_dump($larrResult);
		//echo 'ori---<br>';
		if (isset($post['addonly']) && $post['addonly']=='1') {
			foreach ($larrResult as $key=>$value) {
				$split=$db ->select()
					->from(array('a'=>'tbl_split_coursegroup'))
					->where('a.IdCourseTaggingGroup=?',$value['IdCourseTaggingGroup']);
				$row=$db->fetchRow($split);
				if ($row) unset($larrResult[$key]);
			}
		}
		//echo var_dump($larrResult);exit;
		//echo 'ori-<br>';
		$dbProgram=new GeneralSetup_Model_DbTable_Program();
		$program=$dbProgram->getDataDetail($post['programme']);
		
		foreach ($larrResult as $key=>$value) {
			//cek for program and majoring;
			$sql=$db->select()
			->from(array('a'=>'tbl_studentregsubjects'))
			->join(array('b'=>'tbl_studentregistration'),'a.IdStudentRegistration=b.IdStudentRegistration')
			->where('a.IdCourseTaggingGroup=?',$value['IdCourseTaggingGroup']);
			
			if(isset($post['programme']) && !empty($post['programme']) ){
				$sql = $sql->where("b.IdProgram  = ?",$post['programme']);
			}
			
			if(isset($post['IdMajoring']) && !empty($post['IdMajoring']) ){
				$sql = $sql->where("b.IdProgramMajoring  = ?",$post['IdMajoring']);
			}
			//echo $sql;
			$row=$db->fetchAll($sql);
			if (!$row) unset($larrResult[$key]);
			else {
				$larrResult[$key]['IdLandscape']=$row[0]['IdLandscape'];
				$larrResult[$key]['jenjang']=$program['id_jenjang_pendidikan'];
				$larrResult[$key]['ProgramName']=$program['ArabicName'];
				$larrResult[$key]['ProgramCode']=$program['ProgramCode'];
				$larrResult[$key]['Strata_code_EPSBED']=$program['Strata_code_EPSBED'];
				$larrResult[$key]['Program_code_EPSBED']=$program['Program_code_EPSBED'];
				$larrResult[$key]['id_sms']=$program['id_sms'];
				$larrResult[$key]['IdProgram']=$program['IdProgram']; 
				$larrResult[$key]['JmlStd']=count($row);
			}
			
		}
		//echo var_dump($larrResult);exit;
		//echo '---Student <br>';
		 
		$dbSubject=new GeneralSetup_Model_DbTable_Subjectmaster();
		$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
		$dblandBlockSubject=new GeneralSetup_Model_DbTable_LandscapeBlockSubject();
		$dbLandSubject=new GeneralSetup_Model_DbTable_Landscapesubject();
		foreach ($larrResult as $key=>$value) {
			
			 
				$subject=$dbSubject->getData($value['IdSubject']);
				if ($post['SubjectName']!=null && $subject['ShortName']!=$post['SubjectName']) { 
					 unset($larrResult[$key]);
				} else {
					$sem=$dbSem->getData($value['IdSemester']);
					$larrResult[$key]['bulan']=$sem['bulan'];
					$larrResult[$key]['SemesterMainName']=$sem['SemesterMainName'];
					$larrResult[$key]['shortname']=$subject['ShortName'];
					$larrResult[$key]['SubCode']=$subject['SubCode'];
					$larrResult[$key]['SubjectName']=$subject['BahasaIndonesia'];
					$larrResult[$key]['CreditHours']=$subject['CreditHours'];
					$larrResult[$key]['ch_tutorial']=$subject['ch_tutorial'];
					$larrResult[$key]['ch_practice']=$subject['ch_practice'];
					$larrResult[$key]['ch_practice_field']=$subject['ch_practice_field'];
					$larrResult[$key]['ch_sim']=$subject['ch_sim']; 
					$idgrp=$value['IdCourseTaggingGroup'];
					$rencanas=$dbLandSubject->getInfo($value['IdLandscape'], $value['IdSubject']);
					if (!$rencanas) $rencanas=$dblandBlockSubject->getLandscapeSubjectInfo($value['IdLandscape'], $value['IdSubject']);
					if ($rencanas && $rencanas['n_of_session']!='' ) $rencana=$rencanas['n_of_session']; else $rencana=14;
					$row=$this->fnGetLecturerSessionPdptByLect($idgrp,$value['IdStaff']);
					if (!$row) $row=$this->fnGetLecturerSessionPdptByLect($idgrp);
					if ($row) {
						$larrResult[$key]['realisasi']=$row['count'];
						$larrResult[$key]['fullname']=$value['fullname'];
						$larrResult[$key]['IdLecturer']=$value['IdStaff'];
						//$value['Dosen_Code_EPSBED']=$item['Dosen_Code_EPSBED'];
						$larrResult[$key]['rencana']=$rencana;
						$larrResult[$key]['year']=$year;
						
					} else {
						$larrResult[$key]['realisasi']=0;
						$larrResult[$key]['fullname']=$value['fullname'];
						$larrResult[$key]['IdLecturer']=$value['IdStaff'];
						//$value['Dosen_Code_EPSBED']=$item['Dosen_Code_EPSBED'];
						$larrResult[$key]['rencana']=$rencana;
						$larrResult[$key]['year']=$year;
					}
					//$result_final[]=$value;
				}
		} 
		//echo var_dump($larrResult);
		//exit;//echo "------------j";echo $post['GroupCode'];echo "-----------";
		if(isset($post['GroupCode']) && !empty($post['GroupCode']) && $post['GroupCode']!='' ){
			foreach ($larrResult as $key=>$value) {
				if ($value['GroupCode']!=$post['GroupCode']) unset($larrResult[$key]);
			}
		}
		
	//	echo var_dump($larrResult);exit;
		return $larrResult;
	}
	
	
	public function fnGetActiveClassByLecturer($post){//function to display all  details in list
			
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo var_dump($post);exit;
		$dbsem=new GeneralSetup_Model_DbTable_Semestermaster();
		if ($post['semester']=='') $post['semester']=$post['IdSemester'];
		$sem=$dbsem->getData($post['IdSemester']);
		$semlap=$dbsem->getData($post['semester']);
	 	$acadyear=$sem['idacadyear'];
		if ($semlap['SemesterCountType']=="6") $semlap['SemesterCountType']="3";
		$year=substr($semlap['SemesterMainCode'],0,4).$semlap['SemesterCountType'];
		$gasal_genap=$sem['SemesterCountType'];
		$db = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect1 = $db ->select()
		->from(array('sg'=>'tbl_semestermaster'),array('IdSemesterMaster'))
		->where('sg.idacadyear=?',$acadyear)
		->where('sg.SemesterFunctionType in ("0","1","3","4")')
		->where('sg.SemesterCountType=?',$gasal_genap);
			
		//---------------------
	
		$lstrSelect = $db ->select()
		->from(array('ctg'=>'tbl_course_tagging_group'))
		->joinLeft(array('cl'=>'course_group_lecturers'),'cl.cgl_IdCourseTaggingGroup=ctg.IdCourseTaggingGroup') 
		->where('ctg.IdSemester in (?)',$lstrSelect1)
		->where("ctg.IdSubject  = ?",$post['IdSubject'])
		->where('ctg.IdLecturer='.$post['IdStaff'].' or cl.cgl_IdLecturer='.$post['IdStaff']);
		$larrResult = $db->fetchAll($lstrSelect);
	
		  
		//echo 'ori-<br>';
		$dbProgram=new GeneralSetup_Model_DbTable_Program();
		$program=$dbProgram->getDataDetail($post['programme']);
		$dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		foreach ($larrResult as $key=>$value) {
			if ($value['cgl_IdLecturer']!='') $larrResult[$key]['IdLecturer']=$value['cgl_IdLecturer'];
			$staff=$dbStaff->getData($value['IdLecturer']);
			if ($staff) {
				$larrResult[$key]['IdStaff']=$staff['IdStaff'];
				$larrResult[$key]['fullname']=$staff['FullName'];
			}
			//cek for program and majoring;
			$sql=$db->select()
			->from(array('a'=>'tbl_studentregsubjects'))
			->join(array('b'=>'tbl_studentregistration'),'a.IdStudentRegistration=b.IdStudentRegistration')
			->where('a.IdCourseTaggingGroup=?',$value['IdCourseTaggingGroup']);
			//echo $sql;
			$row=$db->fetchAll($sql);
			if (!$row) unset($larrResult[$key]);
			else {
				$larrResult[$key]['IdLandscape']=$row[0]['IdLandscape'];
				$larrResult[$key]['jenjang']=$program['id_jenjang_pendidikan'];
				$larrResult[$key]['ProgramName']=$program['ArabicName'];
				$larrResult[$key]['ProgramCode']=$program['ProgramCode'];
				$larrResult[$key]['Strata_code_EPSBED']=$program['Strata_code_EPSBED'];
				$larrResult[$key]['Program_code_EPSBED']=$program['Program_code_EPSBED'];
				$larrResult[$key]['id_sms']=$program['id_sms'];
				$larrResult[$key]['IdProgram']=$program['IdProgram'];
				$larrResult[$key]['JmlStd']=count($row);
			}
				
		}
		//echo var_dump($larrResult);exit;
		//echo '---Student <br>';
			
		$dbSubject=new GeneralSetup_Model_DbTable_Subjectmaster();
		$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
		$dblandBlockSubject=new GeneralSetup_Model_DbTable_LandscapeBlockSubject();
		$dbLandSubject=new GeneralSetup_Model_DbTable_Landscapesubject();
		foreach ($larrResult as $key=>$value) {
				
	
			$subject=$dbSubject->getData($value['IdSubject']);
			if ($post['SubjectName']!=null && $subject['ShortName']!=$post['SubjectName']) {
				unset($larrResult[$key]);
			} else {
				$sem=$dbSem->getData($value['IdSemester']);
				$larrResult[$key]['bulan']=$sem['bulan'];
				$larrResult[$key]['SemesterMainName']=$sem['SemesterMainName'];
				$larrResult[$key]['shortname']=$subject['ShortName'];
				$larrResult[$key]['SubCode']=$subject['SubCode'];
				$larrResult[$key]['SubjectName']=$subject['BahasaIndonesia'];
				$larrResult[$key]['CreditHours']=$subject['CreditHours'];
				$larrResult[$key]['ch_tutorial']=$subject['ch_tutorial'];
				$larrResult[$key]['ch_practice']=$subject['ch_practice'];
				$larrResult[$key]['ch_practice_field']=$subject['ch_practice_field'];
				$larrResult[$key]['ch_sim']=$subject['ch_sim'];
				$idgrp=$value['IdCourseTaggingGroup'];
				$rencanas=$dbLandSubject->getInfo($value['IdLandscape'], $value['IdSubject']);
				if (!$rencanas) $rencanas=$dblandBlockSubject->getLandscapeSubjectInfo($value['IdLandscape'], $value['IdSubject']);
				if ($rencanas && $rencanas['n_of_session']!='' ) $rencana=$rencanas['n_of_session']; else $rencana=14;
				$row=$this->fnGetLecturerSessionPdptByLect($idgrp,$value['IdStaff']);
				if (!$row) $row=$this->fnGetLecturerSessionPdptByLect($idgrp);
				if ($row) {
					$larrResult[$key]['realisasi']=$row['count'];
					$larrResult[$key]['fullname']=$value['fullname'];
					$larrResult[$key]['IdLecturer']=$value['IdStaff'];
					//$value['Dosen_Code_EPSBED']=$item['Dosen_Code_EPSBED'];
					$larrResult[$key]['rencana']=$rencana;
					$larrResult[$key]['year']=$year;
	
				} else {
					$larrResult[$key]['realisasi']=0;
					$larrResult[$key]['fullname']=$value['fullname'];
					$larrResult[$key]['IdLecturer']=$value['IdStaff'];
					//$value['Dosen_Code_EPSBED']=$item['Dosen_Code_EPSBED'];
					$larrResult[$key]['rencana']=$rencana;
					$larrResult[$key]['year']=$year;
				}
				//$result_final[]=$value;
			}
		}
		 
	
		//	echo var_dump($larrResult);exit;
		return $larrResult;
	}
	
	public function fnGetActiveClassOnly($post){//function to display all  details in list
			
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo var_dump($post);exit;
		$dbsem=new GeneralSetup_Model_DbTable_Semestermaster();
		if ($post['semester']=='') $post['semester']=$post['IdSemester'];
		$sem=$dbsem->getData($post['IdSemester']);
		$semlap=$dbsem->getData($post['semester']);
			
			
		$acadyear=$sem['idacadyear'];
		if ($semlap['SemesterCountType']=="6") $semlap['SemesterCountType']="3";
		$year=substr($semlap['SemesterMainCode'],0,4).$semlap['SemesterCountType'];
		$gasal_genap=$sem['SemesterCountType'];
		$db = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect1 = $db ->select()
		->from(array('sg'=>'tbl_semestermaster'),array('IdSemesterMaster'))
		->where('sg.idacadyear=?',$acadyear)
		->where('sg.SemesterCountType=?',$gasal_genap);
			
		//---------------------
		$lstrSelect = $db ->select()
			
		->from(array('ctg'=>'tbl_course_tagging_group'),array('JmlStd'=>'count(distinct registrationid)','ctg.IdCourseTaggingGroup','GroupName','GroupCode','IdCourse'=>'IdSubject') )
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=ctg.IdSemester',array('IdSemester'=>'IdSemesterMaster','bulan'=>'MONTH(SemesterMainStartDate)','SemesterMainName'))
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=ctg.IdSubject',array( 'shortname','SubCode','SubjectName'=>'BahasaIndonesia','CreditHours','ch_tutorial','ch_practice','ch_practice_field','ch_sim'))
		->join(array('sreg'=>'tbl_studentregsubjects'),'sreg.IdCourseTaggingGroup=ctg.IdCourseTaggingGroup', array())
		->join(array('sg'=>'tbl_studentregistration'),'sg.IdStudentRegistration=sreg.IdStudentRegistration',array('nim'=>'registrationId'))
		->joinLeft(array('st'=>'tbl_staffmaster'),'ctg.IdLecturer=st.IdStaff',array('IdStaff'=>'st.IdStaff','fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',st.firstname, st.secondname, st.thirdname)"),'Dosen_Code_EPSBED','id_sdm','status'))
		->join(array('prg'=>'tbl_program'),'prg.IdProgram=sg.IdProgram',array('jenjang'=>'prg.id_jenjang_pendidikan','ProgramName'=>'prg.ArabicName', 'ProgramCode'=>'prg.ProgramCode','Strata_code_EPSBED'=>'prg.Strata_code_EPSBED', 'Program_code_EPSBED'=>'prg.Program_code_EPSBED','prg.id_sms','prg.IdProgram'))
		->where('sreg.IdSemesterMain in (?)',$lstrSelect1)
		->group('ctg.IdCourseTaggingGroup');
	
			
		if(isset($post['programme']) && !empty($post['programme']) ){
			$lstrSelect = $lstrSelect->where("prg.IdProgram  = ?",$post['programme']);
		}
	
		if(isset($post['IdMajoring']) && !empty($post['IdMajoring']) ){
			$lstrSelect = $lstrSelect->where("sg.IdProgramMajoring  = ?",$post['IdMajoring']);
		}
		if(isset($post['IdSubject']) && !empty($post['IdSubject']) ){
			$lstrSelect = $lstrSelect->where("ctg.IdSubject  = ?",$post['IdSubject']);
		}
		if(isset($post['IdCourseTaggingGroup']) && !empty($post['IdCourseTaggingGroup']) ){
			$lstrSelect = $lstrSelect->where("ctg.IdCourseTaggingGroup  = ?",$post['IdCourseTaggingGroup']);
		}
	
		if(isset($post['IdCourseTaggingGroup']) && !empty($post['IdCourseTaggingGroup']) ){
			$lstrSelect = $lstrSelect->where("ctg.IdCourseTaggingGroup  = ?",$post['IdCourseTaggingGroup']);
		}
		if(isset($post['GroupCode']) && !empty($post['GroupCode']) ){
	
		}
		//echo $lstrSelect;exit;
		$larrResult = $db->fetchAll($lstrSelect);
	 
		return $result_final;
	}
	public function fnGetLecturerSession($idGroup){//function to display all  details in list
			
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo
		$lstrSelect = $db ->select()
		->from(array('att'=>'course_group_attendance'),array('count'=>'count(*)','Lecturer_id'))
		->join(array('st'=>'tbl_staffmaster'),'att.Lecturer_id=st.IdStaff',array('fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',st.firstname, st.secondname, st.thirdname)"),'Dosen_Code_EPSBED'))
		->where('att.group_id=?',$idGroup)
		->group('att.group_id')
		->group('lecturer_id');
		$larrResult = $db->fetchAll($lstrSelect);
		//echo var_dump($larrResult);exit;
		return $larrResult;
		
	}
	
	
	public function fnGetLecturerSessionPdpt($idGroup){//function to display all  details in list
			
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo
		$lstrSelect = $db ->select()
		->from(array('att'=>'course_group_attendance'),array('count'=>'count(*)','lecturer_id'))
		//->join(array('st'=>'tbl_staffmaster'),'att.Lecturer_id=st.IdStaff',array('fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',st.firstname, st.secondname, st.thirdname)"),'Dosen_Code_EPSBED'))
		->where('att.group_id=?',$idGroup)
		->group('att.lecturer_id');
		$larrResult = $db->fetchAll($lstrSelect);
		return $larrResult;
		 
	
	}
	
	public function fnGetLecturerSessionPdptByLect($idGroup,$idlec=null){//function to display all  details in list
			
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo
		$lstrSelect = $db ->select()
		->from(array('att'=>'course_group_attendance'),array('count'=>'count(*)'))
		//->join(array('st'=>'tbl_staffmaster'),'att.Lecturer_id=st.IdStaff',array('fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',st.firstname, st.secondname, st.thirdname)"),'Dosen_Code_EPSBED'))
		->where('att.group_id=?',$idGroup);
		if ($idlec!=null) $lstrSelect->where('att.lecturer_id=?',$idlec);
		$larrResult = $db->fetchRow($lstrSelect);
		return $larrResult ;
	
	}
			
	
	
	public function fnViewTrakmDetails($id){ //function to find the data to populate in a page of a selected english description to edit.
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('a' => 'kuliah_mahasiswa'),array("id"=>"a.id",
				"id_reg_pdref"=>"a.id_reg_pdref","id_reg_pd"=>"a.id_reg_pd",
				"id_smt"=>"a.id_smt","ips"=>"a.ips",
				"sks_smt"=>"a.sks_smt","sks_total"=>"a.sks_total",
				"id_stat_mhs"=>"a.id_stat_mhs"))
				->where('a.id = '.$id);
		$result = $db->fetchRow($select);
		//echo"<pre>" ;
		//print_r($result);
		return $result;
	}
	
	public function fnKelaskuliahDetails($id){ //function to find the data to populate in a page of a selected english description to edit.
	
		$db = Zend_Db_Table::getDefaultAdapter();
	    $select = $db->select()
		->from(array('a' => 'kelas_kuliah'),array("a.*"))
		->where('a.id = '.$id);
		$result = $db->fetchRow($select);
		//echo"<pre>" ;
		//print_r($result);
		return $result;
	}
	
	public function fnNilaiDetails($id){ //function to find the data to populate in a page of a selected english description to edit.
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('a' => 'nilai'),array("a.*"))
		->where('a.id = '.$id);
		$result = $db->fetchRow($select);
		//echo"<pre>" ;
		//print_r($result);
		return $result;
	}

	
	public  function fnupdateTrakmDetails($formData,$id){//function to update data
		$db = Zend_Db_Table::getDefaultAdapter();
		unset ( $formData ['Save'] );
		$table = "kuliah_mahasiswa";
		$where = 'id = '.$id ;
		$db->update($table,$formData,$where);
	}
	
	public  function fnupdateKelasKuliah($formData,$id){//function to update data
		$db = Zend_Db_Table::getDefaultAdapter();
		unset ( $formData ['Save'] );
		$table = "kelas_kuliah";
		$where = 'id = '.$id ;
		$db->update($table,$formData,$where);
	}
	
	public  function fnupdateNilai($formData,$id){//function to update data
		$db = Zend_Db_Table::getDefaultAdapter();
		unset ( $formData ['Save'] );
		$table = "nilai";
		$where = 'id = '.$id ;
		$db->update($table,$formData,$where);
	}

	public function trakmwsApprove($formData,$id) {
		$Feeder = new Reports_Model_DbTable_Wsclienttbls();
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//query
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('km' => 'kuliah_mahasiswa'), array('id_smt', 'id_reg_pd', 'ips','sks_smt','ipk','sks_total',
						'id_stat_mhs','n_of_update','biaya_smt'))
		->where('id='.$id); 

		 
		$row = $db->fetchRow($select);
		$nofupdate=$row['n_of_update'];
		unset($row['n_of_update']);
		$records = $row;

		//cek first
		$select = $db->select()
		->from(array('km' => 'mahasiswa_pt'))
		->where('id_reg_pdref=?',$row["id_reg_pd"]);
		//echo $select;exit;
		$mhspt = $db->fetchRow($select);

		if ($records['id_stat_mhs']=='L' || $records['id_stat_mhs']=='A' || $records['id_stat_mhs']=='D' || $records['id_stat_mhs']=='C' || $records['id_stat_mhs']=='N' || $records['id_stat_mhs']=='G' ) {
			//status semester
			$status='';
			if ($records['id_stat_mhs']=='L') {
				$status='L';
				$records['id_stat_mhs']='A';
			}
			if ($records['id_stat_mhs']=='D') {
				$status='N';
				$records['id_stat_mhs']='N';
			}
				//echo var_dump($records);exit;
			$filter="id_smt='".$records['id_smt']."' and id_reg_pd='".$records['id_reg_pd']."'";
			//echo $filter;
			$response=$Feeder->fnGetRecord('kuliah_mahasiswa.raw', $filter);
			//echo var_dump($records);exit;
			//------------
			if (count($response)==0 || $response=='') {
				$records['biaya_smt']=$records['biaya_smt']*1;
				
				$result = $Feeder->insertToFeeder('kuliah_mahasiswa',$records);
				//echo 'insert';echo var_dump($result);exit;
				if ($result['result']['error_code']==0) {
				 	$formData['error_code']=$result['result']['error_code'];
					$formData['error_desc']='Ok';
					$table = "kuliah_mahasiswa";
					$formData['n_of_update']=0;
					$where = 'id = '.$id;
					$db->update($table,$formData,$where);
					//update mahasiswa_pt if status='L';
					if ($status=='L') {
						$key=array(
								'id_reg_pd'=>$records['id_reg_pd']
						);

						/*$select = $this->select()
						->from(array('km' => 'mahasiswa_pt'))
						->where('id_reg_pd='.$row["id_reg_pd"]);
						$mhspt = $db->fetchRow($select);*/

						$data=array('jalur_skripsi'=>'1',
							'judul_skripsi'=>$mhspt['judul_skripsi'],
							'bln_awal_bimbingan'=>$mhspt['bln_awal_bimbingan'],
							'bln_akhir_bimbingan'=>$mhspt['bln_akhir_bimbingan'],
							'sk_yudisium'=>$mhspt['sk_yudisium'],
							'tgl_sk_yudisium'=>$mhspt['tgl_sk_yudisium'],
							'ipk'=>$mhspt['ipk'],
							'no_seri_ijazah'=>$mhspt['no_seri_ijazah'],
							'id_jns_keluar'=>$mhspt['id_jns_keluar'],
							'tgl_keluar'=>$mhspt['tgl_keluar'],
							'smt_yudisium'=>$mhspt['smt_yudisium']
								 	
						);
						$result = $Feeder->updateToFeeder('mahasiswa_pt',$key, $data);
						$data['error_code']=$result['result']['error_code'];
						$data['error_desc']=$result['result']['error_desc'];
						$table = "mahasiswa_pt";
						//$formData['n_of_update']=0;
						$where = "id_reg_pd = '".$records['id_reg_pd']."'";
						$db->update($table,$data,$where);
					}
				}
				else
				{
					//echo var_dump($records);echo var_dump($result);exit;
					//fail to add
					$formData['error_code']=$result['result']['error_code'];
					$formData['error_desc']=$result['result']['error_desc'];
					$table = "kuliah_mahasiswa";
					$formData['n_of_update']=0;
					$formData['status']=null;
					$formData['date_of_approval']=null;
					$where = 'id = '.$id;
					$db->update($table,$formData,$where);
				}
			} else {
				//update kuliah_mahasiswa
				 
				 
				$idregpd=$records['id_reg_pd'];
				unset($records['id_reg_pd']);
 				$idsmt=$records['id_smt'];
 				unset($records['id_smt']);
 				$records['biaya_smt']=$records['biaya_smt']*1;
				$key=array('id_reg_pd'=>$idregpd,'id_smt'=>$idsmt);

				$result = $Feeder->updateToFeeder('kuliah_mahasiswa',$key, $records);
				//echo var_dump($key);
				//echo var_dump($result);exit;
				$table = "kuliah_mahasiswa";
				$formData['n_of_update']=$nofupdate+1;
				$formData['error_code']=$result['result']['error_code'];
				$formData['error_desc']=$result['result']['error_desc'];
				$where = 'id = '.$id;
				$db->update($table,$formData,$where);

				if ($status=='L') {
					$key=array( 
						'id_reg_pd'=>$idregpd
					);
				//	echo var_dump($mhspt); 
					$data=array('jalur_skripsi'=>'1',
							'judul_skripsi'=>$mhspt['judul_skripsi'],
							'bln_awal_bimbingan'=>$mhspt['bln_awal_bimbingan'],
							'bln_akhir_bimbingan'=>$mhspt['bln_akhir_bimbingan'],
							'sk_yudisium'=>$mhspt['sk_yudisium'],
							'tgl_sk_yudisium'=>$mhspt['tgl_sk_yudisium'],
							'ipk'=>$mhspt['ipk'],
							'no_seri_ijazah'=>$mhspt['no_seri_ijazah'],
							'id_jns_keluar'=>$mhspt['id_jns_keluar'],
							'tgl_keluar'=>$mhspt['tgl_keluar'],
							'smt_yudisium'=>$mhspt['smt_yudisium']
								 	
					);
 //echo var_dump($data);echo var_dump($key);exit;

					$result = $Feeder->updateToFeeder('mahasiswa_pt',$key, $data);
					
					$table = "kuliah_mahasiswa";
					$formData['n_of_update']=$nofupdate+1;
					$formData['error_code']=$result['result']['error_code'];
					$formData['error_desc']=$result['result']['error_desc'];
					$where = 'id = '.$id;
					$db->update($table,$formData,$where);
				} 
			}
		}else {
			//status mahasiswa
			//status semester
			$filter="id_reg_pd='".$records['id_reg_pd']."'";
			//echo $filter;
			$response=$Feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
			//echo var_dump($response);exit;
			//------------
			if (count($response)==0 || $response=='') {
				 
					$formData['error_code']='01';
					$formData['error_desc']='Mahasiswa Tidak Terdaftar';
					$table = "kuliah_mahasiswa";
					$formData['n_of_update']=0;
					$where = 'id = '.$id;
					$db->update($table,$formData,$where);
				 
			}
			else {
				$filter="id_smt='".$records['id_smt']."' and id_reg_pd='".$records['id_reg_pd']."'";
				//echo $filter;
				$response=$Feeder->fnGetRecord('kuliah_mahasiswa.raw', $filter);
				if (count($response)>0 ) {
					//delete kuliah_mahasiswa
					$key=$key=array('id_reg_pd'=>$records['id_reg_pd'],'id_smt'=>$records['id_smt']);
					$Feeder->deleteToFeeder('kuliah_mahasiswa', $key);
				}
				//echo var_dump($response);exit;
				//------------
			 
				$key=array('id_reg_pd'=>$records['id_reg_pd']);
				$status=$mhspt['id_jns_keluar']; 
				
				$result = $Feeder->updateToFeeder('mahasiswa_pt',$key, array('id_jns_keluar'=>$status,'tgl_keluar'=>$records['tgl_keluar']));
				//echo var_dump($result);exit;
				$table = "kuliah_mahasiswa";
				$formData['n_of_update']=$nofupdate+1;
				$formData['error_code']=$result['result']['error_code'];
				$formData['error_desc']=$result['result']['error_desc'];
				$where = 'id = '.$id;
				$db->update($table,$formData,$where);
			}
		}
		 

	}
	
	public function trakmwsApprovetest($formData,$id) {
		$Feeder = new Reports_Model_DbTable_Wsclienttbls();
		$db = Zend_Db_Table::getDefaultAdapter();
	
		//query
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('km' => 'kuliah_mahasiswa'), array('id_smt', 'id_reg_pd', 'ips','sks_smt','ipk','sks_total',
				'id_stat_mhs','n_of_update'))
				->where('id='.$id);
	
			
		$row = $db->fetchRow($select);
		$nofupdate=$row['n_of_update'];
		unset($row['n_of_update']);
		$records = $row;
	
		//cek first
		$select = $db->select()
		->from(array('km' => 'mahasiswa_pt'))
		->where('id_reg_pdref=?',$row["id_reg_pd"]);
		//echo $select;exit;
		$mhspt = $db->fetchRow($select);
	
		if ($records['id_stat_mhs']=='L' || $records['id_stat_mhs']=='A' || $records['id_stat_mhs']=='C' || $records['id_stat_mhs']=='N' || $records['id_stat_mhs']=='G' ) {
			//status semester
			$status='';
			if ($records['id_stat_mhs']=='L') {
				$status='L';
				$records['id_stat_mhs']='A';
			}
			//echo var_dump($records);exit;
			$filter="id_smt='".$records['id_smt']."' and id_reg_pd='".$records['id_reg_pd']."'";
			//echo $filter;
			$response=$Feeder->fnGetRecord('kuliah_mahasiswa.raw', $filter);
			echo var_dump($response); echo "<br>";
			//------------
			if (count($response)==0 || $response=='') {
				$result = $Feeder->insertToFeeder('kuliah_mahasiswa',$records);
				echo var_dump($records);echo var_dump($result);exit;
				if ($result['result']['error_code']==0) {
					$formData['error_code']=$result['result']['error_code'];
					$formData['error_desc']='Ok';
					$table = "kuliah_mahasiswa";
					$formData['n_of_update']=0;
					$where = 'id = '.$id;
					$db->update($table,$formData,$where);
					//update mahasiswa_pt if status='L';
					if ($status=='L') {
						$key=array(
								'id_reg_pd'=>$records['id_reg_pd']
						);
	
						/*$select = $this->select()
							->from(array('km' => 'mahasiswa_pt'))
						->where('id_reg_pd='.$row["id_reg_pd"]);
						$mhspt = $db->fetchRow($select);*/
	
						$data=array('jalur_skripsi'=>'1',
								'judul_skripsi'=>$mhspt['judul_skripsi'],
								'bln_awal_bimbingan'=>$mhspt['bln_awal_bimbingan'],
								'bln_akhir_bimbingan'=>$mhspt['bln_akhir_bimbingan'],
								'sk_yudisium'=>$mhspt['sk_yudisium'],
								'tgl_sk_yudisium'=>$mhspt['tgl_sk_yudisium'],
								'ipk'=>$mhspt['ipk'],
								'no_seri_ijazah'=>$mhspt['no_seri_ijazah'],
								'id_jns_keluar'=>$mhspt['id_jns_keluar'],
								'tgl_keluar'=>$mhspt['tgl_keluar']
	
						);
						$result = $Feeder->updateToFeeder('mahasiswa_pt',$key, $records);
						$formData['error_code']=$result['result']['error_code'];
						$formData['error_desc']=$result['result']['error_desc'];
						$table = "mahasiswa_pt";
						//$formData['n_of_update']=0;
						$where = "id_reg_pd = '".$records['id_reg_pd']."'";
						$db->update($table,$formData,$where);
					}
				}
				else
				{
					//echo var_dump($records);echo var_dump($result);exit;
					//fail to add
					$formData['error_code']=$result['result']['error_code'];
					$formData['error_desc']=$result['result']['error_desc'];
					$table = "kuliah_mahasiswa";
					$formData['n_of_update']=0;
					$formData['status']=null;
					$formData['date_of_approval']=null;
					$where = 'id = '.$id;
					$db->update($table,$formData,$where);
				}
			} else {
				//update kuliah_mahasiswa
					
					
				$idregpd=$records['id_reg_pd'];
				unset($records['id_reg_pd']);
				$idsmt=$records['id_smt'];
				unset($records['id_smt']);
	
				$key=array('id_reg_pd'=>$idregpd,'id_smt'=>$idsmt);
	
				$result = $Feeder->updateToFeeder('kuliah_mahasiswa',$key, $records);
	
	
				$table = "kuliah_mahasiswa";
				$formData['n_of_update']=$nofupdate+1;
				$formData['error_code']=$result['result']['error_code'];
				$formData['error_desc']=$result['result']['error_desc'];
				$where = 'id = '.$id;
				$db->update($table,$formData,$where);
	
				if ($status=='L') {
					$key=array(
							'id_reg_pd'=>$idregpd
					);
					//	echo var_dump($mhspt);
					$data=array('jalur_skripsi'=>'1',
							'judul_skripsi'=>$mhspt['judul_skripsi'],
							'bln_awal_bimbingan'=>$mhspt['bln_awal_bimbingan'],
							'bln_akhir_bimbingan'=>$mhspt['bln_akhir_bimbingan'],
							'sk_yudisium'=>$mhspt['sk_yudisium'],
							'tgl_sk_yudisium'=>$mhspt['tgl_sk_yudisium'],
							'ipk'=>$mhspt['ipk'],
							'no_seri_ijazah'=>$mhspt['no_seri_ijazah'],
							'id_jns_keluar'=>$mhspt['id_jns_keluar'],
							'tgl_keluar'=>$mhspt['tgl_keluar']
	
					);
					//echo var_dump($data);echo var_dump($key);exit;
	
					$result = $Feeder->updateToFeeder('mahasiswa_pt',$key, $data);
						
					$table = "kuliah_mahasiswa";
					$formData['n_of_update']=$nofupdate+1;
					$formData['error_code']=$result['result']['error_code'];
					$formData['error_desc']=$result['result']['error_desc'];
					$where = 'id = '.$id;
					$db->update($table,$formData,$where);
				}
			}
		}else {
			//status mahasiswa
			//status semester
			$filter="id_reg_pd='".$records['id_reg_pd']."'";
			//echo $filter;
			$response=$Feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
			//echo var_dump($response);exit;
			//------------
			if (count($response)==0 || $response=='') {
					
				$formData['error_code']='01';
				$formData['error_desc']='Mahasiswa Tidak Terdaftar';
				$table = "kuliah_mahasiswa";
				$formData['n_of_update']=0;
				$where = 'id = '.$id;
				$db->update($table,$formData,$where);
					
			}
			else {
				$filter="id_smt='".$records['id_smt']."' and id_reg_pd='".$records['id_reg_pd']."'";
				//echo $filter;
				$response=$Feeder->fnGetRecord('kuliah_mahasiswa.raw', $filter);
				if (count($response)>0 ) {
					//delete kuliah_mahasiswa
					$key=$key=array('id_reg_pd'=>$records['id_reg_pd'],'id_smt'=>$records['id_smt']);
					$Feeder->deleteToFeeder('kuliah_mahasiswa', $key);
				}
				//echo var_dump($response);exit;
				//------------
	
				$key=array('id_reg_pd'=>$records['id_reg_pd']);
				$status=$mhspt['id_jns_keluar'];
				$result = $Feeder->updateToFeeder('mahasiswa_pt',$key, array('id_jns_keluar'=>$status));
				//echo var_dump($result);exit;
				$table = "kuliah_mahasiswa";
				$formData['n_of_update']=$nofupdate+1;
				$formData['error_code']=$result['result']['error_code'];
				$formData['error_desc']=$result['result']['error_desc'];
				$where = 'id = '.$id;
				$db->update($table,$formData,$where);
			}
		}
			
	
	}

 	public function transmhswsApprove($formData,$id) {
			
			$Feeder=new Reports_Model_DbTable_Wsclienttbls();
			$db = Zend_Db_Table::getDefaultAdapter();
			$table = "kelas_kuliah";
			$formData['id']= $id;
			$formData['status']='1';
			$formData['date_of_approval']=date('Y-m-d H:i:s');
			//echo"<pre>" ;	print_r ($formData) ; exit();
			$where = 'id = '.$id;
			$db->update($table,$formData,$where);
			//query
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $this->select()
			->from(array('kk' => 'kelas_kuliah'))				
			->where('id='.$id)
				->setIntegrityCheck(false); // ADD This Line
			$row = $db->fetchRow($select);
			unset($row['IdCourseTaggingGroup']);
			unset($row['id']);
			unset($row['date_of_approval']);
			unset($row['error_code']);
			unset($row['error_desc']);
			unset($row['id_mkrefk']);
			unset($row['id_smsref']);
			
			if ($row['id_kls']=='') {
				//cek feeder
				$filter="id_smt='".$row['id_smt']."' and nm_kls='".$row['nm_kls']."' and id_sms='".$row['id_sms']."' and id_mk='".$row['id_mk']."'";
				//echo $filter;
				$response=$Feeder->fnGetRecord('kelas_kuliah.raw', $filter);
				//echo var_dump($response);exit;
				if (count($response)>0) {
					//record already
					$idkls=$response['id_kls'];
					$db->update('kelas_kuliah',array('id_kls'=>$idkls),'id='.$id);
				}else{
					//no record there
					$response=$Feeder->insertToFeeder('kelas_kuliah',$row);
					if ($response['result']['error_code']==0) {
						$idkls=$response['result']['id_kls'];
						$db->update('kelas_kuliah',array('id_kls'=>$idkls),'id='.$id);
					} else {
						$data=array('error_code'=>$response['result']['error_code'],
								'error_desc'=>$response['result']['error_desc']
						);
						$db->update('kelas_kuliah',$data,'id='.$id);
					}
				}
			} else {
				$idkls=$row['id_kls'];
			}
				
			//////Insert record to WS table nilai
				$db = Zend_Db_Table::getDefaultAdapter();
				$select = $this->select()
				->from(array('n' => 'nilai'),
						array('id_kls','id_reg_pd','asal_data','nilai_angka','nilai_huruf','nilai_indeks'))	//->order("id_sp desc")
				->setIntegrityCheck(false) // ADD This Line
				->where('id_klsk ='.$id);
				$row2 = $db->fetchAll($select);
				//echo"<pre>" ;	print_r ($row) ;exit();
				$recordb = $row2 ;
				$client = new nusoap_client($this->wsdl, true);
				$proxy = $client->getProxy();
				$resultnl = $proxy->GetToken($this->username, $this->password);
				$token = $resultnl ;
				$table = 'nilai';
				$records1 = $recordb;
				$resultnl = $proxy->InsertRecordset($token, $table, json_encode($records1));
				//echo"<pre>" ;	print_r ($resultnl) ;
				unset($resultnl['error_code']);
				unset($resultnl['error_desc']);
				$resultnl2 = array_shift($resultnl);
				$resultnl3 = $resultnl2[0][id_reg_pd];
				$resultnl4 = $resultnl2[0][error_code];
				$resultnl5 = $resultnl2[0][error_desc];
				echo"<pre>" ;
				 print_r ($resultmpt3) ;
				 print_r ($resultmpt4) ;
				 print_r ($resultmpt5) ;
				//exit();
				//insert id_reg_pd from ws to table mahasiswa_pt
				$db = Zend_Db_Table::getDefaultAdapter();
				$formData['id_reg_pd']= $resultnl3 ;
				$formData['error_code']= $resultnl4 ;
				$formData['error_desc']= $resultnl5 ;
				//$formData['date_sync']=date('Y-m-d H:i:s');
				unset($formData['status']);
				unset($formData['date_of_approval']);
				$table = "nilai";
				$where = 'id = '.$id;
				$db->update($table,$formData,$where);
				echo"<pre>" ;	print_r ($resultnl3) ;print_r ($resultnl4) ;print_r ($resultnl5) ;
				
				}
				

		
		
		
		public function fnViewDosenDetails($id){ //function to find the data to populate in a page of a selected english description to edit.
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"kelas_kuliah"),array("a.*"))
					->where('a.id = '.$id);
			$result = $db->fetchRow($select);
			//echo"<pre>" ;
			//print_r($result);exit();
			return $result;
		}
		
		public function fnViewAjardDetails($id){ //function to find the data to populate in a page of a selected english description to edit.
			$db = Zend_Db_Table::getDefaultAdapter();
		    $select = $db->select()
			->from(array("a"=>"ajar_dosen"),array("a.*"))
			->join(array('kls'=>'kelas_kuliah'),'kls.Id=a.id_klsm',array('id_kls'=>'kls.id_kls'))
			->where('a.id = '.$id);
			$result = $db->fetchRow($select);
			//echo"<pre>" ;
			//print_r($result);exit();
			return $result;
		}
		
		public function tmhsisApprove($formData,$Id) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$dbmhs = new Reports_Model_DbTable_Mhssetup();
			$formData['id']= $Id;
			$getTransmhs=$this->getTransNilmhs($Id);
			
			if ($getTransmhs[5]==null || $getTransmhs[10]==null) {
				$dbmhs->fnupdateData('trans_nilai', array('error_desc'=>'Laporkan MK ke Feeder'), "id='".$Id."'");
				return false;
			}
			$select = $db->select()
					->from(array('m' => 'mahasiswa_pt'))
					->where('trim(m.nipd)=?',$getTransmhs[5])
					->where('m.id_sms=?',$getTransmhs[10]);
			$mahasiswa=$db->fetchRow($select);
			//echo var_dump($mahasiswa); echo $select;exit;
			if (!$mahasiswa) $mahasiswa = $this->getMahasiswaPTF($getTransmhs[5],$getTransmhs[10]);
			else $mahasiswa['id_reg_pd']=$mahasiswa['id_reg_pdref'];//echo var_dump($mahasiswa);exit;
			if(empty($mahasiswa)) {
				return false;
			}
			else {
				$insertdata = array();
				$temp = array();
				//echo"<pre>" ;	print_r ($insertdata) ; exit();
				$temp['id_smt'] = ($getTransmhs[1]);
				//$temp['nm_pd'] = $data_arr[$i]['univ_mohe_code'];
				$temp['id_sms'] = ($getTransmhs[10]);
				$temp['id_smsref'] = ($getTransmhs[4]);
				//$temp['id_sms'] = $data_arr[$i]['Program_code_EPSBED'];
				$temp['id_mk'] = ($getTransmhs[11]);
				$temp['id_mkref'] = ($getTransmhs[6]);
				//$temp['id_kls'] = ($getTransmhs[7]);
				$temp['nm_kls'] = ($getTransmhs[9]);
				$temp['sks_mk'] = ($getTransmhs[12]);
				$temp['sks_tm'] = ($getTransmhs[13]);
				$temp['sks_prak'] = ($getTransmhs[14]);
				$temp['IdSplitGroup'] = ($getTransmhs[15]);
				$temp['IdSubject'] = ($getTransmhs[17]);
				$insertdata = $temp;
				//echo"<pre>" ;	print_r ($getTransmhs) ; exit();
				if ($getTransmhs[10]==null) return false;
				$row=$this->isInKelas($getTransmhs[1],$getTransmhs[15],$getTransmhs[9],$getTransmhs[17],$getTransmhs[10]);
				if (!$row) {
					$last_id = $dbmhs->insertkelas_kuliah($insertdata);
					$idkls=null;
				}
				else {
					$last_id=$row['id'];
					$idkls=$row['id_kls'];
				}
				//$last_id = $table->insertkelas_kuliah($insertdata);
				
				
				//table nilai
				$dtupdate=date('Y-m-d H:i:sa');
				$temp2['id_klsk'] = $last_id ;
				$temp2['id_kls'] = $idkls ;
				$temp2['id_reg_pd'] = $mahasiswa['id_reg_pd'];
				$temp2['id_reg_pdref'] = ($getTransmhs[5]);
				$temp2['asal_data'] = '9';
				$temp2['nilai_huruf'] = ($getTransmhs[7]);
				$temp2['nilai_angka'] = ($getTransmhs[16]);
				$temp2['nilai_indeks'] = ($getTransmhs[8]);
				//$temp2['IdStudentRegSubjects'] = ($getTransmhs[0]);
				//$temp2['nm_kls'] = ($getTransmhs[9]);
				$insertdata2 = $temp2;
				if ($last_id!='') {
					//update baru pengecekan nilai berdasarkan kelas_kuliah dan nilai// tadinya hanya nilai saja
					$row=$this->isInNilai($getTransmhs[1],$getTransmhs[6],$getTransmhs[5]);
					if ($row) 
						$dbmhs->fnupdateNilai($insertdata2, $row['id']);
					else {
						 
						$dbmhs->insertnilai($insertdata2);
						
					}
					$dbmhs->fnupdateData('trans_nilai', array('status'=>'1','dt_of_approval'=>$dtupdate), 'id='.$Id);
					
				}
			}
			
			
		}
		
		public function insertkelas_kuliah($data){
			$db = Zend_Db_Table::getDefaultAdapter();
			$db->insert($this->_name3,$data);
			return $db->lastInsertId();
		}
		 
		public function insertnilai($data){
			$db = Zend_Db_Table::getDefaultAdapter();
			$db->insert($this->_name4,$data);
			//return $db->lastInsertId();
		}
		
	public function mhswsApproveNew($formData,$id_pd) {
		$client = new nusoap_client($this->wsdl, true);
		$proxy = $client->getProxy();
		$token = $proxy->GetToken($this->username, $this->password);
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$mdata['status']='1';
		$mdata['date_of_approval']=date('Y-m-d H:i:s');
		
		$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->from(array('m' => 'mahasiswa'),
				array('id_sp', 'nm_pd', 'tmpt_lahir','tgl_lahir','jk',
						'id_agama','id_kk','ds_kel','id_wil','a_terima_kps',
						'id_kebutuhan_khusus_ayah','nm_ibu_kandung',
						'id_kebutuhan_khusus_ibu','kewarganegaraan',))
					->where('id_pd='.$id_pd);
		$recorda = $db->fetchRow($select);
		
		$select = $this->select()
		->from(array('mpt' => 'mahasiswa_pt'),
				array('id_pd','id_sp','id_pd','id_sms','nipd','tgl_masuk_sp','id_jns_daftar'))	//->order("id_sp desc")
		->setIntegrityCheck(false) 
		->where('id_pdref='.$id_pd);
		$recordb = $db->fetchrow($select);
		
		
		$fdata=$this->getMahasiswaPTF($recordb["nipd"],$recordb['id_sms']);
		
		if($fdata){
/*			echo "Data Exist";
			echo "<br>-Update id_pd kat tbl_studentregistration";
			echo "<br>-Update data kat feeder ";
			echo "<br>-Mark temp status to sent to feeder";
*/			$fdata2=$this->getMahasiswaF($fdata["id_pd"]);
			$recordMahasiswa = array(
				"key" => array(
					"id_pd" => $fdata["id_pd"]
				),
				"data" => $recorda
			);			
			
			$fresult = $proxy->UpdateRecord($token, "mahasiswa", json_encode($recordMahasiswa));
			$mdata['date_sync']=date('Y-m-d H:i:s');
			$mdata['id_pdref']= $fdata["id_pd"];
			$mdata['error_code']= $fresult["result"]["error_code"];
			$mdata['error_desc']= $fresult["result"]["error_desc"];
			
			$recordMahasiswapt = array(
				"key" => array(
					"id_reg_pd" => $fdata["id_reg_pd"]
				),
				"data" => $recordb
			);
			
			$token = $proxy->GetToken($this->username, $this->password);
			$fresult2 = $proxy->UpdateRecord($token, "mahasiswa_pt", json_encode($recordMahasiswapt));
				
			$db->update('mahasiswa',$mdata,'id_pd='.$id_pd);

			$mptdata['id_pd']=$mdata["id_pdref"];
			$mptdata['id_reg_pdref']=$fdata["id_reg_pd"];
			$mptdata['error_code']= $resultmpt["result"]["error_code"] ;
			$mptdata['error_desc']= $resultmpt["result"]["error_desc"] ;
			$mptdata['date_sync']=date('Y-m-d H:i:s');
		
			$db->update('mahasiswa_pt',$mptdata,'id_pdref='.$id_pd);
						
			$sgdata["id_pd"]=$fdata["id_pd"];
			$sgdata["id_reg_pd"]=$fdata["id_reg_pd"];
			$db->update('tbl_studentregistration',$sgdata,'registrationId='.$recordb["nipd"]);

		}else{
//			echo "add data to feeder";
			$fresult = $proxy->InsertRecord($token, 'mahasiswa', json_encode($recorda));
			if($fresult["result"]["error_code"]==200){
				$mhsexist=$this->getMahasiswaNameDOBF($recorda["nm_pd"],$recorda["tgl_lahir"]);
				//echo "<pre>";print_r($mhsexist);exit;
				$recordb["id_pd"]=$mhsexist["id_pd"];
			}else{
				$recordb["id_pd"]=$fresult["result"]["id_pd"];
			}
			$mdata['date_sync']=date('Y-m-d H:i:s');
			$mdata['id_pdref']= $fresult["result"]["id_pd"];
			$mdata['error_code']= $fresult["result"]["error_code"];
			$mdata['error_desc']= $fresult["result"]["error_desc"];
			//echo "<pre>";print_r($fresult);
			$resultmpt = $proxy->InsertRecord($token, 'mahasiswa_pt', json_encode($recordb));
			
			$mptdata['id_pd']=$mdata["id_pdref"];
			$mptdata['id_reg_pdref']=$fdata["id_reg_pd"];			
			$mptdata['error_code']= $resultmpt["result"]["error_code"] ;
			$mptdata['error_desc']= $resultmpt["result"]["error_desc"] ;
			$mptdata['date_sync']=date('Y-m-d H:i:s');
		
			$db->update('mahasiswa_pt',$mptdata,'id_pdref='.$id_pd);
//			echo "<pre>";print_r($resultmpt);  
			$sgdata["id_pd"]=$fresult["result"]["id_pd"];
			$sgdata["id_reg_pd"]=$resultmpt["id_reg_pd"];
			$db->update('tbl_studentregistration',$sgdata,'registrationId='.$recordb["nipd"]);
		
		}
		
		
	}		
		public function getMahasiswaPTF($nim,$sms){
			$Feeder=new Reports_Model_DbTable_Wsclienttbls();
			$filter = "trim(nipd) = '".$nim."' and id_sms='".$sms."'"; 
			 
			$fres = $Feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
			//echo var_dump($fres);
			if(count($fres)>0){
				return $fres;
			} 
				return false;
			 			
		}		

		public function getMahasiswaF($idpd){
			$filter = "id_pd = '$idpd'";
			$client = new nusoap_client($this->wsdl, true);
			$proxy = $client->getProxy();
			$token = $proxy->GetToken($this->username, $this->password);
			$table = 'mahasiswa';
			$fres = $proxy->GetRecord($token, $table, $filter);
			if(is_array($fres["result"])){
				return $fres["result"];
			}else{
				return false;
			}
		}
		
		public function getMahasiswaNameDOBF($name,$dob){
			$name =trim($name);
			$filter = "nm_pd = '$name' and tgl_lahir='$dob'";
			$client = new nusoap_client($this->wsdl, true);
			$proxy = $client->getProxy();
			$token = $proxy->GetToken($this->username, $this->password);
			$table = 'mahasiswa';
			$fres = $proxy->GetRecord($token, $table, $filter);
			//echo "<pre>";echo $filter;print_r($fres);
			if(is_array($fres["result"])){
				return $fres["result"];
			}else{
				return false;
			}				
		}
		
		public function getKuliahMahasiswa($id_smt,$id_reg_pd){
			$filter = "id_smt = '$id_smt' and id_reg_pd ='$id_reg_pd'";
			$client = new nusoap_client($this->wsdl, true);
			$proxy = $client->getProxy();
			$token = $proxy->GetToken($this->username, $this->password);
			$table = 'kuliah_mahasiswa';
			$fres = $proxy->GetRecord($token, $table, $filter);
			//echo "<pre>";echo $filter;print_r($fres);
			if(is_array($fres["result"])){
				return $fres["result"];
			}else{
				return false;
			}
		}

	public function approveTransMhs(array $data, $id) {
		
		$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$select = $db->select();
		$select->from(array('a' => 'nilai'));
		$select->where('a.id = ?',(int)$id);
		$result = $db->fetchRow($select);
	
		$idkls='';
		$idklsk=$result['id_klsk'];
		if ($result['id_kls']=='') {
			//klas null get from kelas_kuliah
			$select = $db->select();
			$select->from(array('a' => 'kelas_kuliah'));
			$select->where('a.id = ?',(int)$idklsk);
			$klsk = $db->fetchRow($select);
			
			if ($klsk['id_kls']=='') {
				//check feeder
				$filter="id_smt='".$klsk['id_smt']."' and trim(nm_kls)='".$klsk['nm_kls']."' and id_sms='".$klsk['id_sms']."' and id_mk='".$klsk['id_mk']."'";
				//echo $filter;
				$response=$Feeder->fnGetRecord('kelas_kuliah.raw', $filter);
			//	echo var_dump($response);
				if (count($response)>0) {
					//record already
					$idkls=$response['id_kls'];
					$db->update('kelas_kuliah',array('id_kls'=>$idkls,'status'=>$data['status'],'date_of_approval'=>$data['date_of_approval'],'error_code'=>'0','error_desc'=>null),'id='.$idklsk);
				}else{
					//no record there
					unset($klsk['id_kls']);
					unset($klsk['IdSplitGroup']);
					unset($klsk['error_code']);
					unset($klsk['error_desc']);
					unset($klsk['date_of_approval']);
					unset($klsk['status']);
					unset($klsk['IdCourseTaggingGroup']);
					unset($klsk['id']);
					unset($klsk['id_smsref']);
					unset($klsk['id_mkref']);
					unset($klsk['id_mou']);
					//unset($klsk['id_kls_pditt']);
					$klsk['tgl_mulai_koas']=date('Y-m-d',strtotime($klsk['tgl_mulai_koas']));
					$klsk['tgl_selesai_koas']=date('Y-m-d',strtotime($klsk['tgl_selesai_koas']));
					$response=$Feeder->insertToFeeder('kelas_kuliah',$klsk);
					//echo var_dump($response);echo 'kelas idnsert';
					if ($response['result']['error_code']==0) {
						$idkls=$response['result']['id_kls'];
						$db->update('kelas_kuliah',array('id_kls'=>$idkls,'error_code'=>0,
								'error_desc'=>null),'id='.$idklsk);
					} else {
						$dataupdate=array('error_code'=>$response['result']['error_code'],
								'error_desc'=>$response['result']['error_desc']
						);
						$db->update('kelas_kuliah',$dataupdate,'id='.$idklsk);
					}
				}
			} else {
				$idkls=$klsk['id_kls'];
				$db->update('kelas_kuliah',array('id_kls'=>$idkls,'status'=>$data['status'],'date_of_approval'=>$data['date_of_approval'],'error_code'=>0,
						'error_desc'=>null),'id='.$idklsk);
					
			}
		} else {
			$idkls=$result['id_kls'];
			$db->update('kelas_kuliah',array('id_kls'=>$idkls,'status'=>$data['status'],'date_of_approval'=>$data['date_of_approval'],'error_code'=>0,
					'error_desc'=>null),'id='.$idklsk);
			
		}
		//check nilai on feeder
		
		//$idstdredsubject=$result['IdStudentRegSubjects'];
		if ($idkls!='') {
			$db->update('nilai',array('id_kls'=>$idkls,'error_code'=>0,
								'error_desc'=>null),'id='.(int)$id);
			unset( $result['id']);
			unset( $result['error_code']);
			unset( $result['error_desc']);
			unset( $result['error_code']);
			unset( $result['date_of_approval']);
			unset( $result['id_smsref']);
			unset( $result['id_mkref']);
			unset( $result['status']);
			unset( $result['id_reg_pdref']);
			//unset( $result['nm_klas']);
			unset( $result['id_klsk']);
			unset( $result['date_lastinsert']);
			unset( $result['nm_kls']);
			unset( $result['date_sync']);
			unset($result['asal_data']);
			//$idstdredsubject=$result['IdStudentRegSubjects'];
			//unset($result['IdStudentRegSubjects']);
			//$result['id_kls'] = '46';
			$result['id_kls']=$idkls;
			$filter = "id_kls='".$idkls."' AND id_reg_pd= '".$result['id_reg_pd']."'";
			$response=$Feeder->fnGetRecord('nilai.raw', $filter);
			//echo $filter;echo var_dump($result);exit;
			if (count($response)>0) {
				//nilai there
				if ($response['nilai_huruf']!=$result['nilai_huruf'] ||
					$response['nilai_angka']!=$result['nilai_angka'] ||
					$response['nilai_indeks']!=$result['nilai_indeks']  ) {
					//update feeder
					unset($result['id_kls']);
					unset($result['id_reg_pd']);
					$response=$Feeder->updateToFeeder('nilai',array('id_kls'=>$idkls,'id_reg_pd'=>$response['id_reg_pd']), $result);
					if ($response['result']['error_code']==0) {
						$data['error_code']=0;
						$data['error_desc']=null;
						$db->update('nilai',$data,'id='.$id);
						//$db->update('trans_nilai',array( 'dt_of_approval'=>date('Y-m-d H:i:sa')),'IdStudentRegSubjects='.$idstdredsubject);
					
					} else {
						$dataerror=array('error_code'=>$response['result']['error_code'],
								'error_desc'=>$response['result']['error_desc'],'status'=>'0'
						);
						$db->update('nilai',$dataerror,'id='.$id);
					}
					 
				} else {
					$db->update('nilai',array('id_kls'=>$idkls,'error_code'=>0,'status'=>'1',
							'error_desc'=>null),'id='.(int)$id);
				}
			} else {
				//no nilai
				//echo var_dump($result);
				$response=$Feeder->insertToFeeder('nilai',$result);
				//echo var_dump($response);echo var_dump($result);exit;
				if ($response['result']['error_code']==0) {
					$idkls=$response['result']['id_kls'];
					$data['error_code']=0;
					$data['error_desc']=null;
					$db->update('nilai',$data,'id='.$id);
					//$db->update('tbl_studentregsubjects',array( 'dt_to_feeder'=>date('Y-m-d H:i:sa')),$idstdredsubject);
				} else {
					if ($response['result']['error_code']=='806') {
						//get another class may be
						$select = $db->select();
						$select->from(array('a' => 'kelas_kuliah'));
						$select->where('a.id = ?',(int)$idklsk);
						$klsk = $db->fetchRow($select);
						$filter="id_smt='".$klsk['id_smt']."' and id_sms='".$klsk['id_sms']."' and id_mk='".$klsk['id_mk']."'";
						$klset=$Feeder->fnGetRecordSet('kelas_kuliah.raw', $filter);
						//echo var_dump($response);exit;
						if (count($klset)>0) {
							foreach ($klset as $kelas) {
								$idklsOther=$kelas['id_kls'];
								$filter = "id_kls='".$idklsOther."' AND id_reg_pd= '".$result['id_reg_pd']."'";
								$response=$Feeder->fnGetRecord('nilai.raw', $filter);
								if (count($response)>0) {
									/* if ($response['nilai_huruf']!=$result['nilai_huruf'] ||
										$response['nilai_angka']!=$result['nilai_angka'] ||
										$response['nilai_indeks']!=$result['nilai_indeks']  ) {
										 */
										//delete data feeder
										 $response=$Feeder->deleteToFeeder('nilai', array('id_kls'=>$idklsOther,'id_reg_pd'=>$result['id_reg_pd']));
										 if ($response['result']['error_code']==0) {
											//insert ke feeder

										 	//unset($result['id_kls']);
										 	//unset($result['id_reg_pd']);
											$response=$Feeder->insertToFeeder('nilai',$result);
											if ($response['result']['error_code']==0) {
												$data['error_code']=0;
												$data['error_desc']=null;
												$db->update('nilai',$data,'id='.$id);
													
											} else {
												$dataerror=array('error_code'=>$response['result']['error_code'],
														'error_desc'=>$response['result']['error_desc'],'status'=>'0'
												);
												$db->update('nilai',$dataerror,'id='.$id);
												
											}
											break;
										 }
										/* $response=$Feeder->updateToFeeder('nilai',array('id_kls'=>$idkls,'id_reg_pd'=>$response['id_reg_pd']), $result);
										if ($response['result']['error_code']==0) {
											$data['error_code']=0;
											$data['error_desc']=null;
											$db->update('nilai',$data,'id='.$id);
											//$db->update('trans_nilai',array( 'dt_of_approval'=>date('Y-m-d H:i:sa')),'IdStudentRegSubjects='.$idstdredsubject);
										
										} else {
											$dataerror=array('error_code'=>$response['result']['error_code'],
													'error_desc'=>$response['result']['error_desc']
											);
											$db->update('nilai',$dataerror,'id='.$id);
										} */
									/* } else {
										$data['error_code']=0;
										$data['error_desc']=null;
										$db->update('nilai',$data,'id='.$id);
									} */
								}
							}
						}
					} else {
						$dataerror=array('error_code'=>$response['result']['error_code'],
								'error_desc'=>$response['result']['error_desc'],'status'=>'0'
						);
						$db->update('nilai',$dataerror,'id='.$id);
					}
				}
			}
		} else {
			$db->update('nilai',array('id_kls'=>$idkls,'error_desc'=>'No Kelas','status'=>'0'),'id='.$id);
		}
		
	}
	
	 
	
	public function deleteTransMhs($id) {
	
		$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select();
		$select->from(array('a' => 'nilai'));
		$select->where('a.id = ?',(int)$id);
		$result = $db->fetchRow($select);
		if ($result) {
			$idregpd=$result['id_reg_pd'];
			$idklsk=$result['id_kls'];
			if ($idregpd!='' && $idklsk!='') {
				$data=array('id_kls'=>$idklsk,'id_reg_pd'=>$idregpd);
				//  echo var_dump($data);
				$response=$Feeder->deleteToFeeder('nilai',$data);
				//echo var_dump($response);exit;
				if ($response['result']['error_code']==0) {
					
					$data=array('id_kls'=>'','id_reg_pd'=>'');
					$data['error_desc']='deleted';
					$this->fnupdateData('nilai',$data, "id=".$id);
				} else {
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$this->fnupdateData('nilai', $formData, "id=".$id);
				}
			}
		}
	}
		
	
	public function approveTransMhsOld(array $data, $id) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->update($this->_name3,$data,'id ='.$id);
	
		$select = $db->select();
		$select->from(array('a' => $this->_name3));
		$select->where('a.id = ?',(int)$id);
	
		$result = $db->fetchRow($select);
	
		unset( $result['id_kls']);
		unset( $result['id']);
		unset( $result['error_code']);
		unset( $result['error_desc']);
		unset( $result['error_code']);
		unset( $result['date_of_approval']);
		unset( $result['id_smsref']);
		unset( $result['id_mkref']);
		unset( $result['status']);
		unset( $result['IdCourseTaggingGroup']);
		//$result['id_kls'] = '46';
		foreach ($result as $key => $value) {
			if(($result[$key] == NULL) || (empty($result[$key])))
				unset($result[$key]);
		}
	
		$post['filter'] = "id_sms='".$result['id_sms']."' AND id_smt= '".$result['id_smt']."'
							AND id_mk = '".$result['id_mk']."' AND nm_kls ='".$result['nm_kls']."'";
	
		$post['table'] = 'kelas_kuliah.raw';
		//echo var_dump($post);exit;
		$Feeder = new Reports_Model_DbTable_Wsclienttbls();
		$checkCurrentKuliah = $Feeder->feedSearch($post);
		//echo var_dump($checkCurrentKuliah);exit;
		//---------------------------
		if(!isset($checkCurrentKuliah[0]))
		{
			$response = $Feeder->insertToFeeder('kelas_kuliah',$result);
			//echo var_dump($response);exit;
		}
		else
		{
			$response['result']['id_kls'] = $checkCurrentKuliah[0]['id_kls'];
			$response['error_code'] = 0;
			$response['error_desc'] = NULL;
				
			$updateTable['id_kls'] = $response['result']['id_kls'];
			$updateTable['error_code'] = $response['error_code'];
			$updateTable['error_desc'] = $response['error_desc'];
		}
	
		if($response['error_code'] == 0) {
				
			$updateTable['id_kls'] = $response['result']['id_kls'];
			$updateTable['error_code'] = $response['error_code'];
			$updateTable['error_desc'] = $response['error_desc'];
				
			$db->update($this->_name3,$updateTable,'id ='.$id);
				
			//update  table nilai supaya dapat id_kls
			$nilai['id_kls'] = $updateTable['id_kls'];
			$nilai['date_of_approval'] = date('Y-m-d',time());
			$nilai['status'] = 1;
			$nilai['id_kls'] = $updateTable['id_kls'];
			$db->update($this->_name4,$nilai,'id_klsk ='.$id);
				
			$Select_nilai = $db->select();
			$Select_nilai->from(array('a' => $this->_name4))
			->where('a.id_klsk = ?',$id);
				
			$result_nilai = $db->fetchRow($Select_nilai);
			//echo var_dump($result_nilai);exit;
			if($result_nilai) {
	
				$result_nilai['nilai_angka'] = (!empty($result_nilai['nilai_angka'])) ? $result_nilai['nilai_angka'] : 0;
				$result_nilai['asal_data']   = (!empty($result_nilai['asal_data'])) ? $result_nilai['asal_data'] : 9;
				$result_nilai['nilai_huruf'] = (!empty($result_nilai['nilai_huruf'])) ? $result_nilai['nilai_huruf'] : 'F';
				$result_nilai['nilai_indeks']= (!empty($result_nilai['nilai_angka'])) ? $result_nilai['nilai_indeks'] : 0;
	
				$feeder_nilai = array(
						'id_reg_pd' => $result_nilai['id_reg_pd'],
						'asal_data' => $result_nilai['asal_data'],
						'nilai_angka'  => $result_nilai['nilai_angka'],
						'nilai_huruf'  => $result_nilai['nilai_huruf'],
						'nilai_indeks' => $result_nilai['nilai_indeks'],
						'id_kls' 	   => $result_nilai['id_kls']
				);
	
				$post['filter'] = "id_reg_pd='".$result_nilai['id_reg_pd']."' AND id_kls= '".$result_nilai['id_kls']."'";
	
				$post['table'] = 'nilai.raw';
				$checkCurrentNilai = $Feeder->feedSearch($post);
	
				if(!isset($checkCurrentNilai[0])) {
						
					$response_nilai = $Feeder->insertToFeeder('nilai',$feeder_nilai);
					//echo var_dump($response_nilai);exit;
				}
				else {
					$keys['id_reg_pd'] = $checkCurrentNilai[0]['id_reg_pd'];
					$keys['id_kls']    = $checkCurrentNilai[0]['id_kls'];
						
					unset($feeder_nilai['id_kls']);
					unset($feeder_nilai['id_reg_pd']);
						
					$response_nilai = $Feeder->updateToFeeder('nilai',$keys,$feeder_nilai);
					//echo var_dump($response_nilai);exit;
				}
	
	
				if($response_nilai['error_code'] == 0) {
						
					$nilai_response['error_code'] = $response_nilai['error_code'];
					$nilai_response['error_desc'] = $response_nilai['error_desc'];
					$nilai_response['status']     = 1;
					$nilai_response['date_sync']  = date('Y-m-d',time());
						
					$db->update($this->_name4,$nilai_response,'id ='.$result_nilai['id']);
				}
	
			}
				
		}
	}
		
		public function getMatakuliah($subcode,$idsms) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"mata_kuliah"),array("a.*"))
			->where('a.kode_mk = ?',$subcode)
			->where('a.id_sms = ?',$idsms);
			$result = $db->fetchRow($select);
			if (!$result)
				$select = $db->select()
				->from(array("a"=>"mata_kuliah"),array("a.*"))
				->where('a.kode_mk = ?',$subcode);
				//->where('a.id_sms = ?',$idsms);
				$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isInKelas($idsmt,$idgroup,$nmklas,$idsubject,$idsms) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"kelas_kuliah"),array("a.*"))
			//->where('a.IdSplitGroup = ?',$idgroup)
			->where('a.nm_kls = ?',$nmklas)
			->where('a.IdSubject = ?',$idsubject)
			->where('a.id_sms = ?',$idsms)
			->where('a.id_smt = ?',$idsmt);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isInKelasByIdSplit($idSplit){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"kelas_kuliah"),array("a.*"))
			//->where('a.IdSplitGroup = ?',$idgroup)
			->where('a.IdSplitGroup = ?',$idSplit);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isKelasNotToFeeder($idgroup) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"kelas_kuliah"),array("a.*"))
			->where('a.IdSplitGroup = ?',$idgroup) 
			->where('a.id_kls is null');
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isKelasInNilai($idkelas) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"nilai"),array("a.*"))
			->where('a.id_kls = ?',$idkelas);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isAjarToFeeder($idkelas,$idregptk) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select() 
			->from(array('a'=>'ajar_dosen'))
			->where('a.id_klsm = ?',$idkelas)
			->where('a.id_reg_ptk=?',$idregptk);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isAjarNotEmpty($idkelas) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('a'=>'ajar_dosen'))
			->where('a.id_klsm = ?',$idkelas);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isInKelasRaw($idsmt,$idkls,$idmk) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"kelas_kuliah"),array("a.*"))
			->where('a.id_kls=?',$idkls)
			->where('a.id_mk = ?',$idmk)
			->where('a.id_smt = ?',$idsmt);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isInKelasbyKode($idsms,$idsmt,$idmk,$nmklas) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"kelas_kuliah"),array("a.*"))
			->where('a.id_sms=?',$idsms)
			->where('a.id_mk = ?',$idmk)
			->where('a.id_smt = ?',$idsmt)
			->where('a.nm_kls = ?',$nmklas);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isInNilai($idsmt,$kdmk,$regid) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"nilai"),array("a.*"))
			->join(array('kls'=>'kelas_kuliah'),'a.id_klsk=kls.id',array())
			->where('kls.id_smt = ?',$idsmt)
			->where('kls.id_mkref = ?',$kdmk)
			//->where('a.id_klsk = ?',$idgroup)
			->where('a.id_reg_pdref = ?',$regid);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isInMK($idsms,$idmk) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"mata_kuliah"),array("a.*"))
			->where('a.id_sms = ?',$idsms)
			->where('a.id_mk = ?',$idmk);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isInMKbyCode($idsms,$kode) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"mata_kuliah"),array("a.*"))
			->where('a.id_sms = ?',$idsms)
			->where('a.kode_mk = ?',$kode);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isInNilaiRaw($idkls,$regid) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"nilai"),array("a.*"))
			->where('a.id_kls = ?',$idkls)
			->where('a.id_reg_pd = ?',$regid);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isInKuliah($idsmt,$idregpd) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"kuliah_mahasiswa"),array("a.*"))
			->where('a.id_smt = ?',$idsmt)
			->where('a.id_reg_pd = ?',$idregpd);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isInKuliahByNim($idsmt,$nim,$idsms=null) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"kuliah_mahasiswa"),array("a.*"))
			->join(array('mhs'=>'mahasiswa_pt'),'mhs.id_reg_pdref=a.id_reg_pd',array())
			->where('a.IdSemesterMain = ?',$idsmt)
			->where('mhs.nipd = ?',$nim);
			if ($idsms!=null) $select->where('mhs.id_sms=?',$idsms);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isInAjarKuliah($idkls,$iddosen) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"ajar_dosen"),array("a.*"))
			->where('a.id_klsm = ?',$idkls)
			->where('a.id_reg_ptk = ?',$iddosen);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function getAjarDosen($idkls,$idstaff) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"ajar_dosen"))
			->join(array('b'=>'dosen_pt'),'a.id_reg_ptk=b.id_reg_ptk',array())
			->where('a.id_klsm = ?',$idkls)
			->where('b.IdStaff = ?',$idstaff);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isGraduate($idreg) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"graduate"))
			->where('a.IdStudentRegistration = ?',$idreg)
			->where('a.rector_approval_skr > 0'); 
			$result = $db->fetchRow($select);
			if ($result) return true; else return false;
		}
		
		public function isGraduateInSemester($idreg,$idsem) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"graduate"))
			->join(array('b'=>'graduation_skr'),'a.rector_approval_skr = b.id')
			->where('a.IdStudentRegistration = ?',$idreg)
			->where('b.IdSemesterMain = ?',$idsem);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		
		public function isGraduateAfterSemester($idreg,$idsem) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$sem =$db->select()
			->from(array('a'=>'tbl_semestermaster'),array('SemesterMainStartDate'))
			->where('a.IdSemesterMaster=?',$idsem);
			 
			
			$select = $db->select()
			->from(array("a"=>"graduate"))
			->join(array('b'=>'graduation_skr'),'a.rector_approval_skr = b.id')
			->join(array('sm'=>'tbl_semestermaster'),'b.IdSemesterMain=sm.IdSemesterMaster')
			->where('a.IdStudentRegistration = ?',$idreg)
			->where('sm.SemesterMainStartDate > ?',$sem);
			$result = $db->fetchRow($select);
			if ($result) return true; else return false;
		}
		
		public function isInDosenPT($idptk,$idsp,$idsms) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"dosen_pt"),array("a.*"))
			->where('a.id_sdm = ?',$idptk)
			->where('a.id_sp = ?',$idsp)
			->where('a.id_sms = ?',$idsms);
			//->where('a.id_thn_ajaran = ?',$thnajar);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function classApproval($data,$id,$idlecturer) {
			//query class
			$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
			$dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
			$result=$this->getSplitKelasByIdDetail($id,$idlecturer);
			$dbFeeder=new Reports_Model_DbTable_Wsclienttbls();
			$dbMhs=new Reports_Model_DbTable_Mhssetup();//echo var_dump($data) ;
			//echo var_dump($result);exit;
			foreach ($result as $value) {
				$idptk=null;
				$idsp=null;
				$idsms=null;
				$rencanamk=$value['rencana'];
				if ($value['id_sp']=='') 
				{
					//get id_sp from feeder
					
					$filter="npsn='".$value['univ_mohe_code']."'";
					$response=$dbFeeder->fnGetRecord('satuan_pendidikan', $filter);
					//update university
					//echo $filter;echo var_dump($response);exit;
					if (count($response)>0) {
						$dbUniv=new GeneralSetup_Model_DbTable_University();
						$idsp=$response['id_sp'];
						$dbUniv->fnupdateUniversity(array('id_sp'=>$idsp), $value['IdUniversity']);
					}
				} else 
					$idsp=$value['id_sp'];
				 
				if ($value['id_sdm']=='')
				{
					//get id_sp from feeder
					//$dbFeeder=new Reports_Model_DbTable_Wsclienttbls();
					
					$filter="trim(nm_sdm)='".strtoupper(trim($value['fullname']))."' and tgl_lahir='".date('Y-m-d',strtotime($value['DOB']))."'";
					//$filter='tgl_lahir="'.date('Y-m-d',strtotime($value['DOB'])).'" and trim(nm_sdm) = "'.strtoupper(trim($value['fullname'])).'"';
				 
					$response=$dbFeeder->fnGetRecord("dosen.raw", $filter);
					//$response=$dbFeeder->feedSearch(array('filter'=>$filter,'table'=>'dosen.raw','limit'=>'10'));
					//update university
					//echo $filter;echo var_dump($response);exit;
					if (count($response)>0) {
						 
						$idptk=$response['id_sdm'];
						$dbStaff->fnupdateStaffmaster(array('id_sdm'=>$idptk,'status'=>'','Dosen_Code_EPSBED'=>$response['nidn']), $value['IdStaff']);
					} else {
					
						$filter="tgl_lahir='".date('Y-m-d',strtotime($value['DOB']))."' and trim(nidn)='".$value['Dosen_Code_EPSBED']."'";// and id_sp='".$idsp."'";
						$response=$dbFeeder->fnGetRecord('dosen.raw', $filter);
						//update university
						//echo $filter;echo var_dump($response);exit;
						if (count($response)>0) {
							
							$idptk=$response['id_sdm'];
							$dbStaff->fnupdateStaffmaster(array('id_sdm'=>$idptk,'status'=>''), $value['IdStaff']);
						} else {
							$this->fnupdateData('tbl_staffmaster', array('status'=>'No Registered (cek: nidn/tgl lhr/nama'), 'IdStaff='.$value['IdStaff']);
						}
					} 
					
				} else
					$idptk=$value['id_sdm'];
				
				if ($value['id_sms']=='')
				{
					//get id_sp from feeder
					//$dbFeeder=new Reports_Model_DbTable_Wsclienttbls();
					 
					$filter="kode_prodi='".$value['Program_code_EPSBED']."' and id_sp='".$idsp."'";
					$response=$dbFeeder->fnGetRecord('sms.raw', $filter);
					//update university
					
					if (count($response)>0) {
						$dbUniv=new GeneralSetup_Model_DbTable_Program();
						$idsms=$response['id_sms'];
						$dbUniv->fnupdateProgram( array('id_sms'=>$idsms), $value['IdProgram']);
					}
				} else
					$idsms=$value['id_sms'];
				//echo var_dump($value);exit;
				
				//get dosen
				$dosenpt=$this->getDosenPTbyIdLecturer($idlecturer);
			 	 
				if ($dosenpt['id_reg_ptk']=='')
				{
					
					$filter="id_sdm='".$idptk."' and id_sp='".$idsp."' and id_sms='".$idsms."'";
					$response=$dbFeeder->fnGetRecord('dosen_pt.raw', $filter);
					//update university
					
					if (count($response)==0) {
						$filter="id_sdm='".$idptk."' and id_sp='".$idsp."'";
						$response=$dbFeeder->fnGetRecord('dosen_pt.raw', $filter);
							
					}
//echo var_dump($response);exit;
					if (count($response)>0) {
					 
						$idregptk=$response['id_reg_ptk'];
						$row=$this->isInDosenPT($idptk,$idsp,$idsms);
						if ($row)
							$this->fnupdateData('dosen_pt',array('id_reg_ptk'=>$idregptk), "IdStaff='".$value['IdStaff']."'");
						else 
						{
							$data=array(
									'id_sdm'=>$idptk,
									'id_reg_ptk'=>$idregptk,
									'id_sp'=>$idsp,
									'id_sms'=>$value['id_sms'],
									'IdStaff'=>$value['IdStaff']);
							$this->fnaddData('dosen_pt', $data);
						}
					} else {
						$filter="id_sms ='".$idsms."' and id_sp='".$idsp."' and id_sdm='".$idptk."'";
						$response=$dbFeeder->fnGetRecord('dosen_pt.raw', $filter);
						//echo $filter;echo var_dump($response);exit;
						if (count($response)>0)	 {
							 
							$idregptk=$response['id_reg_ptk'];
							$row=$this->isInDosenPT($idptk,$idsp,$idsms);
							if ($row)
								$this->fnupdateData('dosen_pt',array('id_reg_ptk'=>$idregptk), "IdStaff='".$value['IdStaff']."'");
							else
							{
								$data=array(
										'id_sdm'=>$idptk,
										'id_reg_ptk'=>$idregptk,
										'id_sp'=>$idsp, 
										'id_sms'=>$value['id_sms'],
										'IdStaff'=>$value['IdStaff']
								);
								$this->fnaddData('dosen_pt', $data);
							}
							 
						} else $dbStaff->fnupdateStaffmaster(array('status'=>'Cek: Penugasan di forlap'), $value['IdStaff']);
						 
					}
				
				} else {
					
					$idregptk=$dosenpt['id_reg_ptk'];
					//cek ke feeder
					$filter="id_reg_ptk='".$idregptk."' and id_sp='".$idsp."' and id_sms='".$idsms."'";
					//$filter="id_reg_ptk='".$idregptk."' and id_sp='".$idsp."'";
					$response=$dbFeeder->fnGetRecord('dosen_pt.raw', $filter);
					 
					if (count($response)==0) {
						$filter="id_reg_ptk='".$idregptk."' and id_sp='".$idsp."'";
						//$filter="id_reg_ptk='".$idregptk."' and id_sp='".$idsp."'";
						$response=$dbFeeder->fnGetRecord('dosen_pt.raw', $filter);	
					}//update university
					//echo var_dump($response);exit;
					if (count($response)>0) {
					
						$idregptk=$response['id_reg_ptk'];
						$row=$this->isInDosenPT($idptk,$idsp,$idsms);
						if ($row)
							$this->fnupdateData('dosen_pt',array('id_reg_ptk'=>$idregptk), "IdStaff='".$value['IdStaff']."'");
						else
						{
							$data=array(
									'id_sdm'=>$idptk,
									'id_reg_ptk'=>$idregptk,
									'id_sp'=>$idsp,
									'id_sms'=>$value['id_sms'],
									'IdStaff'=>$value['IdStaff']);
							$this->fnaddData('dosen_pt', $data);
						}
					} else {
							//echo $value['IdStaff'];exit;
							$this->fnupdateData('dosen_pt',array('id_reg_ptk'=>''), "IdStaff='".$value['IdStaff']."'");
						
						}
				}
				
				//echo $idsp.'/-'.$idptk.'/-'.$idsms.'/-'.$idregptk;exit;
				if ($idsp!='' && $idptk!='' && $idsms!='' && $idregptk!='') {
				
				//save ajar_dosen
				//get id kelas
				$mata_kuliah=$this->getMatakuliah(trim($value['subject_code']),$value['id_sms']);
			//	echo var_dump($mata_kuliah);exit;
				if (!$mata_kuliah || $mata_kuliah['id_mk']=='') { 
					//get from Feeder
					$filter="id_sms='".$idsms."' and trim(kode_mk)='".trim($value['subject_code'])."'";
					//$filter="id_sms='3f9b23b9-6724-4c18-aa53-12950c24c9f4'  and kode_mk='GBHH4B'";
					$response=$dbFeeder->fnGetRecord('mata_kuliah.raw', $filter);
					//echo $filter; echo var_dump($response);exit;
					if (count($response)==0) {
						$filter=" trim(kode_mk)='".trim($value['subject_code'])."'";
						//$filter="id_sms='3f9b23b9-6724-4c18-aa53-12950c24c9f4'  and kode_mk='GBHH4B'";
						$response=$dbFeeder->fnGetRecord('mata_kuliah.raw', $filter);
							
					}
					if (count($response)>0 && $response!='') {
						//echo $response;exit;
						$idmk=$response['id_mk'];
						//insert to mata_kuliah in sis;
						$data=array('id_mk'=>$response['id_mk'],
								'id_sms'=>$response['id_sms'],
								'id_jenj_dik'=>$response['id_jenj_didik'],
								'kode_mk'=>$response['kode_mk'],
								'nm_mk'=>$response['nm_mk'],
								'jns_mk'=>$response['jns_mk'],
								'kel_mk'=>$response['kel_mk'],
								'sks_mk'=>$response['sks_mk'],
								'sks_tm'=>$response['sks_tm'],
								'sks_prak_lap'=>$response['sks_prak_lap'],
								'sks_sim'=>$response['sks_sim']
						);
						$mk=$this->isInMKbyCode($response['id_sms'], $response['kode_mk']);
						if (!$mk) 
							$this->fnaddData('mata_kuliah', $data);
						else  
							$this->fnupdateData('mata_kuliah', $data, 'id='.$mk['id']);
						$mata_kuliah=$data;
						//echo var_dump($matakuliah);
						//echo '-----<br>';
					} else {
				
						//get form tbl_subject master
						$dbSubject=new GeneralSetup_Model_DbTable_Subjectmaster();
						$mk=$dbSubject->fngetsubjcodeRow(trim($value['subject_code']));
						//echo var_dump($mk);echo "===mk".$value['subject_code'];
						if ($mk['tgl_mulai_efektif']=='0000-00-00' || $mk['tgl_mulai_efektif']==null) $mk['tgl_mulai_efektif']='2015-01-01';
						if ($mk['tgl_akhir_efektif']=='0000-00-00' || $mk['tgl_akhir_efektif']==null) $mk['tgl_akhir_efektif']='2015-01-01';
						$data=array( 'id_sms'=>$idsms,
								'id_jenj_didik'=>$value['jenjang'],
								'kode_mk'=>$mk['ShortName'],
								'nm_mk'=>$mk['BahasaIndonesia'],
								'jns_mk'=>'A',//sementara ambil wajib
								'kel_mk'=>'A',//sementara ambil A
								'sks_mk'=>(int)$mk['CreditHours'],
								'sks_tm'=>(int)$mk['ch_tutorial'],
								'sks_prak'=>(int)$mk['ch_practice'],
								'sks_prak_lap'=>(int)$mk['ch_practice_field'],
								'sks_sim'=>(int)$mk['ch_sim'],
								'metode_pelaksanaan_kuliah'=>$mk['metode_pelaksanaan_kuliah'],
								'a_sap'=>$mk['a_sap'],
								'a_silabus'=>$mk['a_silabus'],
								'a_bahan_ajar'=>$mk['a_bahan_ajar'],
								'acara_prak'=>$mk['a_bahan_ajar'],
								'a_diktat'=>$mk['a_bahan_ajar'],
								'tgl_mulai_efektif'=>date('Y-m-d',strtotime($mk['tgl_mulai_efektif'])),
								'tgl_akhir_efektif'=>date('Y-m-d',strtotime($mk['tgl_akhir_efektif']))
						);
						//echo var_dump($data); 
						
						$response=$dbFeeder->insertToFeeder('mata_kuliah',$data);
						
						//echo var_dump($response);exit;
						if ($response['result']['error_code']==0) {
							$idmk=$response['result']['id_mk'];
							$filter="trim(kode_mk)='".trim($value['subject_code'])."' and  id_sms='".$idsms."'";
							$response=$dbFeeder->fnGetRecord('mata_kuliah', $filter);
							if (count($response)>0)	 {
								$data=array('id_mk'=>$idmk,
										'id_sms'=>$response['id_sms'],
										'id_jenj_dik'=>$response['id_jenj_didik'],
										'kode_mk'=>$response['kode_mk'],
										'nm_mk'=>$response['nm_mk'],
										'jns_mk'=>$response['jns_mk'],
										'kel_mk'=>$response['kel_mk'],
										'sks_mk'=>$response['sks_mk'],
										'sks_tm'=>$response['sks_tm'],
										'sks_prak_lap'=>$response['sks_prak_lap'],
										'sks_sim'=>$response['sks_sim']
								);
								$mk=$this->isInMKbyCode($response['id_sms'], $response['kode_mk']);
								if (!$mk) 
									$this->fnaddData('mata_kuliah', $data);
								else  
									$this->fnupdateData('mata_kuliah', $data, 'id='.$mk['id']);
						
							}
						}  
					}
					if ($idmk=="") $mata_kuliah=$this->getMatakuliah(trim($value['subject_code']),$value['id_sms']);
				}
				
				$row=$this->isInKelas($value['year'],$value['Id'],$value['GroupCode'],$value['IdCourse'],$value['id_sms']);
				//echo var_dump($row);echo "-";echo var_dump($mata_kuliah);exit;
				if (!$row ) {
						if ($mata_kuliah) {
							//save kelas kuliah
							$temp['id_smt'] = $value['year'];//.$value['SemesterCountType'];
							$temp['id_sms'] = $value['id_sms'];
							$temp['id_smsref'] = $value['Program_code_EPSBED'];
							//$temp['id_sms'] = $data_arr[$i]['Program_code_EPSBED'];
							$temp['id_mk'] = $mata_kuliah['id_mk'];					
							$temp['id_mkref'] = $value['subject_code'];
							//$temp['id_kls'] = ($getTransmhs[7]);
							$temp['nm_kls'] = $value['GroupCode'];
							$temp['sks_mk'] = $mata_kuliah['sks_mk'];
							$temp['sks_tm'] = $mata_kuliah['sks_tm'];
							$temp['sks_prak'] = $mata_kuliah['sks_prak'];
							$temp['IdSplitGroup'] = $value['IdSplitGroup'];
							$temp['IdSubject']=$value['IdCourse'];
							$insertdata = $temp;
							//echo"<pre>" ;	print_r ($getTransmhs) ; exit();
							//$table = new Reports_Model_DbTable_Mhssetup();
							$id = $this->insertkelas_kuliah($insertdata);
						}
				} else {
						$temp=array();
						$temp['id_smt'] = $value['year'];//.$value['SemesterCountType'];
						$temp['id_sms'] = $value['id_sms'];
						$temp['id_smsref'] = $value['Program_code_EPSBED'];
					//$temp['id_sms'] = $data_arr[$i]['Program_code_EPSBED'];
						$temp['id_mk'] = $mata_kuliah['id_mk'];
						$temp['id_mkref'] = $value['subject_code'];
					//$temp['id_kls'] = ($getTransmhs[7]);
						$temp['nm_kls'] = $value['GroupCode'];
						$temp['sks_mk'] = $mata_kuliah['sks_mk'];
						$temp['sks_tm'] = $mata_kuliah['sks_tm'];
						$temp['sks_prak'] = $mata_kuliah['sks_prak'];
						$temp['IdSplitGroup'] = $value['IdSplitGroup'];
						$temp['IdSubject']=$value['IdCourse'];
					//	echo var_dump($temp);exit;
						$this->fnupdateData('kelas_kuliah',$temp , 'id='.$row['id']);
						if ($row['id_kls']=='') {
							$id=$row['id'];
							$idkls='';
						} else {
							$id=$row['id'];
							$idkls=$row['id_kls'];
						}
					}
					$data=array('id_klsm'=>$id,
							 'id_reg_ptk'=>$idregptk,
							 'id_kls'=>$idkls, //id kelas kuliah
							 'sks_subst_tot'=>$mata_kuliah['sks_mk']*$value['rencanaLect']/$rencanamk,
							// 'sks_tm_subst'=>$mata_kuliah['sks_tm'],
							 //'sks_prak_subst'=>$mata_kuliah['sks_prak'],
							 //'sks_prak_lap_subst'=>$mata_kuliah['sks_prak_lap'],
							 //'sks_sim_subst'=>$mata_kuliah['sks_sim'],
							 'jml_tm_renc'=>$value['rencanaLect'],
							 'jml_tm_real'=>$value['realisasiLect'],
							 'id_jns_eval'=>'1');
					//echo var_dump($data);exit;
					$row=$this->isInAjarKuliah($id,$idregptk);
					if ($row) {
						unset($data['id_klsm']);
						$this->fnupdateData('ajar_dosen', $data, 'id='.(int)$row['id']);
					} else $this->fnaddData('ajar_dosen', $data);
					
					$this->fnupdateData('tbl_split_coursegroup', array('status'=>'1'), 'Id='.(int)$value['IdSplitGroup']);
					$this->fnupdateData('tbl_split_lecturer', array('status'=>'1'), 'IdSplitGroup='.(int)$value['IdSplitGroup'].' and IdLecturer='.$value['IdStaff']);
						
					//echo 'ok';exit;
				} else {
					if ($idptk=='') {
						$dbUniv=new GeneralSetup_Model_DbTable_Staffmaster();
						$dbUniv->fnupdateStaffmaster(array('status'=>'Nidon Salah'), $value['IdStaff']);
						$this->fnupdateData('tbl_split_lecturer', array('status'=>null), 'IdSplitGroup='.(int)$value['IdSplitGroup'].' and IdLecturer='.$value['IdStaff']);
					} 
					if ($idregptk=='') {
						$dbUniv=new GeneralSetup_Model_DbTable_Staffmaster();
						$dbUniv->fnupdateStaffmaster(array('status'=>'No Registered'), $value['IdStaff']);
						$this->fnupdateData('tbl_split_lecturer', array('status'=>null), 'IdSplitGroup='.(int)$value['IdSplitGroup'].' and IdLecturer='.$value['IdStaff']);
					}
				}
			}
			
			return true;
		}
		
		public function classUnApproval($data,$id,$idlecturer,$proses) {
			//cek kelas kuliah
			$dbFeeder=new Reports_Model_DbTable_Wsclienttbls();
			$db=new Reports_Model_DbTable_Mhssetup();
			$result=$this->getSplitKelasByIdDetail($id,$idlecturer);
			
			foreach ($result as $value) {
				   
					$kelas=$this->isInKelas($value['year'],$value['Id'],$value['GroupCode'],$value['IdCourse'],$value['id_sms']);
					 
					$idklaslec=$value['idlec'];
					$idkls=$value['id_kls'];
					$delstatus='';
					if ($kelas) {
						$row=$db->getDosenPTbyIdLecturer($idlecturer);
						
						$infeeder='';
						//$idkls='';
						if ($row) {
							if ($row['id_reg_ptk']=='') {
								//not register to feeder
								$this->fndeleteData('ajar_dosen', 'id_klsm='.$kelas['id'].' and id_reg_ptk=""');
							} else {
								//register
								$regptk=$row['id_reg_ptk'];
								$row=$db->isAjarToFeeder($kelas['id'],$regptk);
								if ($row) {
									 
									//$idkls=$row['id_kls'];
									if ($row['id_ajar']!='') {
										//delete ajar in feeder
										$infeeder='1';$deleajar='';
										$key=array("id_ajar"=>$row['id_ajar']);
										$response=$dbFeeder->deleteToFeeder('ajar_dosen', $key);
										if ($response['result']['error_code']==0) $deleajar="1";
									} else $deleajar="1";
									//not to feeder
									if ($deleajar=="1") {
										 //cek nilai not in
										 $this->fndeleteData('ajar_dosen','id_klsm='.$kelas['id'].' and id_reg_ptk="'.$regptk.'"');
										 $row=$this->isInLecturer($value['IdSplitGroup'],'IdLecturer='.$value['IdStaff']);
										 if ( $row && $proses=='unapproved') $this->fnupdateData('tbl_split_lecturer', array('status'=>null), 'Id='.(int)$idklaslec); 
										 
										 if ($proses=='drop')
											 $this->fndeleteData('tbl_split_lecturer', 'Id='.$idklaslec);								 	
									}
								} else {
									//delete ajar_dosen
									 
									$this->fndeleteData('ajar_dosen','id_klsm='.$kelas['id'].' and id_reg_ptk="'.$regptk.'"');
									if ($proses=='drop') $this->fndeleteData('tbl_split_lecturer', 'Id='.$idklaslec);
								}
							 
						}
						
						$row=$db->isAjarNotEmpty($kelas['id']);
						 
						if (!$row) {
							//delete kelas infeeder
							if ($idkls!='') {
								//cek nilai empty
								$filter="id_kls='".$idkls."'";
								
								$response=$dbFeeder->fnGetRecord('nilai.raw', $filter);
								if (count($response)==0) {
									$key=array('id_kls'=>$idkls);
									$response=$dbFeeder->deleteToFeeder('kelas_kuliah', $key);
									$this->fnupdateData('kelas_kuliah', array('id_kls'=>''),'id_kls="'.$idkls.'"');
									
								} else $delstatus='1';
								 
							}
							if ($delstatus=='')
								$this->fndeleteData('kelas_kuliah',  'id='.$kelas['id']);
							else 
								 $this->fnupdateData('kelas_kuliah', array('id_kls'=>''),'id_kls="'.$idkls.'"');
							 
							if ($proses=='drop' && $delstatus=='') {
								$this->fndeleteData('nilai', 'id_klsk='.$kelas['id']);
								$this->fndeleteData('trans_nilai', 'IdSplitGroup='.$id);
								$this->fndeleteData('tbl_split_coursegroup', 'id='.$id);
						
							}
						}
						
					} else  {
						if ($proses=='drop') {
							$this->fndeleteData('tbl_split_lecturer', 'Id='.$idklaslec);
							//ada dosen?
							if (!$this->isSplitKelasNotEmpty($id)) 		$this->fndeleteData('tbl_split_coursegroup', 'id='.$id);
						 
						}
						else $this->fnupdateData('tbl_split_lecturer', array('status'=>null), 'Id='.(int)$idklaslec);
							
					}
				} else 
				if ($proses=='drop') {
					$this->fndeleteData('tbl_split_lecturer', 'IdLecturer='.$idlecturer.' and IdSplitGroup='.$id);
					//ada dosen?
					if (!$this->isSplitKelasNotEmpty($id)) 		$this->fndeleteData('tbl_split_coursegroup', 'id='.$id);
						
				}
			}
		}
		
		public function classApprovalOld($data,$id,$nmklas) {
			//query class
			$result=$this->getSplitKelasByIdDetail($id);
			$dbFeeder=new Reports_Model_DbTable_Wsclienttbls();
			$dbMhs=new Reports_Model_DbTable_Mhssetup();//echo var_dump($data) ;
			//echo var_dump($result);exit;
			foreach ($result as $value) {
				$idptk=null;
				$idsp=null;
				$idsms=null;
				if ($value['id_sp']=='')
				{
					//get id_sp from feeder
						
					$filter="npsn='".$value['univ_mohe_code']."'";
					$response=$dbFeeder->fnGetRecord('satuan_pendidikan.raw', $filter);
					//update university
					//echo var_dump($response);exit;
					if (count($response)>0) {
						$dbUniv=new GeneralSetup_Model_DbTable_University();
						$idsp=$response['id_sp'];
						$dbUniv->fnupdateUniversity(array('id_sp'=>$idsp), $value['IdUniversity']);
					}
				} else
					$idsp=$value['id_sp'];
				if ($value['id_sdm']=='')
				{
					//get id_sp from feeder
					//$dbFeeder=new Reports_Model_DbTable_Wsclienttbls();
		
					$filter="nidn='".$value['Dosen_Code_EPSBED']."' and id_sp='".$idsp."'";
					$response=$dbFeeder->fnGetRecord('dosen.raw', $filter);
					//update university
					//echo var_dump($filter);exit;
					if (count($response)>0) {
						$dbUniv=new GeneralSetup_Model_DbTable_Staffmaster();
						$idptk=$response['id_sdm'];
						$dbUniv->fnupdateStaffmaster(array('id_sdm'=>$idptk), $value['IdStaff']);
					}
						
				} else
					$idptk=$value['id_sdm'];
		
				if ($value['id_sms']=='')
				{
					//get id_sp from feeder
					//$dbFeeder=new Reports_Model_DbTable_Wsclienttbls();
		
					$filter="kode_prodi='".$value['Program_code_EPSBED']."' and id_sp='".$idsp."'";
					$response=$dbFeeder->fnGetRecord('sms.raw', $filter);
					//update university
						
					if (count($response)>0) {
						$dbUniv=new GeneralSetup_Model_DbTable_Program();
						$idsms=$response['id_sms'];
						$dbUniv->fnupdateProgram( array('id_sms'=>$idsms), $value['IdProgram']);
					}
				} else
					$idsms=$value['id_sms'];
		
		
				if ($value['id_reg_ptk']=='')
				{
					//get id_sp from feeder
					//$dbFeeder=new Reports_Model_DbTable_Wsclienttbls();
						
					$filter="id_thn_ajaran='".$value['year']."' and id_sdm='".$idptk."' and id_sp='".$idsp."' and id_sms='".$idsms."'";
					$response=$dbFeeder->fnGetRecord('dosen_pt.raw', $filter);
					//update university
					//echo var_dump($response);exit;
					if (count($response)>0) {
						$dbUniv=new GeneralSetup_Model_DbTable_Staffmaster();
						$idregptk=$response['id_reg_ptk'];
						$dbUniv->fnupdateStaffmaster(array('id_reg_ptk'=>$idregptk), $value['IdStaff']);
					} else {
						//save dosen_pt
						if ($value['ProgramCode'] == $value['HomeCode']) $home='1'; else $home='0';
						$data=array(
								'id_sdm'=>$idptk,
								'id_sp'=>$idsp,
								'id_thn_ajaran'=>$value['year'],
								'id_sms'=>$value['id_sms'],
								'no_srt_tgs'=>"xx",//$data['nosrt'],
								'tgl_srt_tgs'=>date('Y-m-d',strtotime("1965-02-02")),//date('Y-m-d',strtotime($data['dtsrt'])),
								'tmt_srt_tgs'=>date('Y-m-d',strtotime("1965-02-02")),//date('Y-m-d',strtotime($data['dteffective'])),
								'a_sp_homebase'=> $home,
								'a_aktif_bln_1'=>($value['bulan'] == 1 ? '1' : '0'),
								'a_aktif_bln_2'=>($value['bulan'] == 2 ? '1' : '0'),
								'a_aktif_bln_3'=>($value['bulan'] == 3 ? '1' : '0'),
								'a_aktif_bln_4'=>($value['bulan'] == 4 ? '1' : '0'),
								'a_aktif_bln_5'=>($value['bulan'] == 5 ? '1' : '0'),
								'a_aktif_bln_6'=>($value['bulan'] == 6 ? '1' : '0'),
								'a_aktif_bln_7'=>($value['bulan'] == 7 ? '1' : '0'),
								'a_aktif_bln_8'=>($value['bulan'] == 8 ? '1' : '0'),
								'a_aktif_bln_9'=>($value['bulan'] == 9 ? '1' : '0'),
								'a_aktif_bln_10'=>($value['bulan'] == 10 ?'1' : '0'),
								'a_aktif_bln_11'=>($value['bulan'] == 11 ? '1' : '0'),
								'a_aktif_bln_12'=>($value['bulan'] == 12 ? '1' : '0'),
						);
		
						//echo var_dump($data);
						//save ajar_dosen
						$response=$dbFeeder->insertToFeeder('dosen_pt', $data);
						if ($response['result']['error_code']!=0) {
							$dbUniv->fnupdateStaffmaster(array('status'=>$response['result']['error_desc']), $value['IdStaff']);
							$filter="id_thn_ajaran='".$value['year']."' and id_sdm='".$idptk."'";
							$response=$dbFeeder->fnGetRecord('dosen_pt.raw', $filter);
							//echo var_dump($response);exit;
							if (count($response)>0)	 {
								$dbUniv=new GeneralSetup_Model_DbTable_Staffmaster();
								$idregptk=$response['id_reg_ptk'];
								$dbUniv->fnupdateStaffmaster(array('id_reg_ptk'=>$idregptk), $value['IdStaff']);
							}
						} else {
							$idregptk=$response['id_reg_ptk'];
						}
		
					}
		
				} else
					$idregptk=$value['id_reg_ptk'];
		
				//echo $idsp.'/-'.$idptk.'/-'.$idsms.'/-'.$idregptk;exit;
				if ($idsp!='' && $idptk!='' && $idsms!='' && $idregptk!='') {
		
					//save ajar_dosen
					//get id kelas
					$mata_kuliah=$this->getMatakuliah($value['SubCode'],$value['id_sms']);
					//echo var_dump($mata_kuliah);
					if (!$mata_kuliah) {
						//get from Feeder
						$filter="id_sms='".$idsms."' and kode_mk='".$value['shortname']."'";
						//$filter="id_sms='3f9b23b9-6724-4c18-aa53-12950c24c9f4'  and kode_mk='GBHH4B'";
						$response=$dbFeeder->fnGetRecord('mata_kuliah', $filter);
		
						if (count($response)>0) {
							//insert to mata_kuliah in sis;
							$data=array(id_mk=>$response['id_mk'],
									id_sms=>$response['id_sms'],
									id_jenj_dik=>$response['id_jenj_didik'],
									kode_mk=>$response['kode_mk'],
									nm_mk=>$response['nm_mk'],
									jns_mk=>$response['jns_mk'],
									kel_mk=>$response['kel_mk'],
									sks_mk=>$response['sks_mk'],
									sks_tm=>$response['sks_tm'],
									sks_prak_lap=>$response['sks_prak_lap'],
									sks_sim=>$response['sks_sim']
							);
							$this->fnaddData('mata_kuliah', $data);
							$idmk=$response['id_mk'];
						} else {
		
							//get form tbl_subject master
							$dbSubject=new GeneralSetup_Model_DbTable_Subjectmaster();
							$mk=$dbSubject->fngetsubjcodeRow($value['SubCode']);
							//echo var_dump($mk);echo "===mk";
							if ($mk['tgl_mulai_efektif']=='0000-00-00' || $mk['tgl_mulai_efektif']==null) $mk['tgl_mulai_efektif']='2015-01-01';
							if ($mk['tgl_akhir_efektif']=='0000-00-00' || $mk['tgl_akhir_efektif']==null) $mk['tgl_akhir_efektif']='2015-01-01';
							$data=array( id_sms=>$idsms,
									id_jenj_didik=>$value['jenjang'],
									kode_mk=>$mk['ShortName'],
									nm_mk=>$mk['BahasaIndonesia'],
									jns_mk=>'A',//sementara ambil wajib
									kel_mk=>'A',//sementara ambil A
									sks_mk=>(int)$mk['CreditHours'],
									sks_tm=>(int)$mk['ch_tutorial'],
									sks_prak=>(int)$mk['ch_practice'],
									sks_prak_lap=>(int)$mk['ch_practice_field'],
									sks_sim=>(int)$mk['ch_sim'],
									metode_pelaksanaan_kuliah=>$mk['metode_pelaksanaan_kuliah'],
									a_sap=>$mk['a_sap'],
									a_silabus=>$mk['a_silabus'],
									a_bahan_ajar=>$mk['a_bahan_ajar'],
									acara_prak=>$mk['a_bahan_ajar'],
									a_diktat=>$mk['a_bahan_ajar'],
									tgl_mulai_efektif=>date('Y-m-d',strtotime($mk['tgl_mulai_efektif'])),
									tgl_akhir_efektif=>date('Y-m-d',strtotime($mk['tgl_akhir_efektif']))
							);
							$response=$dbFeeder->insertToFeeder('mata_kuliah',$data);
							//echo var_dump($response);exit;
							if ($response['result']['error_code']==0) {
								$idmk=$response['result']['id_mk'];
								$filter="kode_mk='".$value['shortname']."' and  id_sms='".$idsms."'";
								$response=$dbFeeder->fnGetRecord('mata_kuliah.raw', $filter);
								if (count($response)>0)	 {
									$data=array(id_mk=>$idmk,
											id_sms=>$response['id_sms'],
											id_jenj_dik=>$response['id_jenj_didik'],
											kode_mk=>$response['kode_mk'],
											nm_mk=>$response['nm_mk'],
											jns_mk=>$response['jns_mk'],
											kel_mk=>$response['kel_mk'],
											sks_mk=>$response['sks_mk'],
											sks_tm=>$response['sks_tm'],
											sks_prak_lap=>$response['sks_prak_lap'],
											sks_sim=>$response['sks_sim']
									);
									$this->fnaddData('mata_kuliah', $data);
								}
							}
						}
						$mata_kuliah=$this->getMatakuliah($value['SubCode'],$value['id_sms']);
					}
					$row=$this->isInKelas($value['IdCourseTaggingGroup'],$value['GroupCode'],$value['IdCourse']);
					if (!$row && $mata_kuliah) {
						//save kelas kuliah
						$temp['id_smt'] = $idsmt;//$value['year'].$value['SemesterCountType'];
						$temp['id_sms'] = $value['id_sms'];
						$temp['id_smsref'] = $value['Program_code_EPSBED'];
						//$temp['id_sms'] = $data_arr[$i]['Program_code_EPSBED'];
						$temp['id_mk'] = $mata_kuliah['id_mk'];
						$temp['id_mkref'] = $value['SubCode'];
						//$temp['id_kls'] = ($getTransmhs[7]);
						$temp['nm_kls'] = $value['GroupCode'];
						$temp['sks_mk'] = $mata_kuliah['sks_mk'];
						$temp['sks_tm'] = $mata_kuliah['sks_tm'];
						$temp['sks_prak'] = $mata_kuliah['sks_prak'];
						$temp['IdCourseTaggingGroup'] = $value['IdCourseTaggingGroup'];
						$temp['IdSubject']=$value['IdCourse'];
						$insertdata = $temp;
						$id = $this->insertkelas_kuliah($insertdata);
					} else if ($row['id_kls']=='') {
						$id=$row['id'];
						$idkls='';
					} else {
						$id=$row['id'];
						$idkls=$row['id_kls'];
					}
						
					$data=array('id_klsm'=>$id,
							'id_reg_ptk'=>$idregptk,
							'id_kls'=>$idkls, //id kelas kuliah
							'sks_subst_tot'=>$mata_kuliah['sks_mk'],
							'sks_tm_subst'=>$mata_kuliah['sks_tm'],
							'sks_prak_subst'=>$mata_kuliah['sks_prak'],
							'sks_prak_lap_subst'=>$mata_kuliah['sks_prak_lap'],
							'sks_sim_subst'=>$mata_kuliah['sks_sim'],
							'jml_tm_renc'=>$value['rencana'],
							'jml_tm_real'=>$value['realisasi'],
							'id_jns_eval'=>'1');
					//echo var_dump($data);exit;
					$row=$this->isInAjarKuliah($id,$idregptk);
					if ($row) {
						$this->fnupdateData('ajar_dosen', $data, $row['id']);
					} else $this->fnaddData('ajar_dosen', $data);
		
				} else {
					if ($idptk=='') {
						$dbUniv=new GeneralSetup_Model_DbTable_Staffmaster();
						$dbUniv->fnupdateStaffmaster(array('status'=>'Nidon Salah'), $value['IdStaff']);
					}
					if ($idregptk=='') {
						$dbUniv=new GeneralSetup_Model_DbTable_Staffmaster();
						$dbUniv->fnupdateStaffmaster(array('status'=>'No Registered'), $value['IdStaff']);
					}
				}
			}
				
			return true;
		}
		public function getKelas($id){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"kelas_kuliah"),array("a.*"))
			->where('a.id  = ?',$id);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function getKelasByGrp($idgrp){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"kelas_kuliah"))
			->join(array('b'=>'tbl_split_coursegroup'),'a.IdSplitGroup=b.Id',array())
			->where('b.IdCourseTaggingGroup  = ?',$idgrp);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function getSplitKelas($id,$idprogram){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"tbl_split_coursegroup"))
			->where('a.idcoursetagginggroup  = ?',$id);
			
			$select->where('a.IdProgram=?',$idprogram);
			$result = $db->fetchAll($select);
			return $result;
		}
		
		public function getSplitKelasRow($id,$idprogram){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"tbl_split_coursegroup"))
			->where('a.idcoursetagginggroup  = ?',$id);
				
			$select->where('a.IdProgram=?',$idprogram);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function getTransNilai($idstd,$idsemester){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"),array("a.*",'grade_point'=>'nilai_indeks'))
			->join(array('cg'=>'tbl_split_coursegroup'),'cg.Id=a.IdSplitGroup')
			->join(array('s'=>'tbl_semestermaster'),'cg.IdSemesterSubject=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('CreditHours','subject_name'=>'sm.BahasaIndonesia','subject_code'=>'shortname'))
			->where('cg.IdSemester  = ?',$idsemester)
			->where('s.SemesterFunctionType <> "5"')
			->where('s.SemesterFunctionType <> "2"')
			->where('a.IdStudentRegistration  = ?',$idstd);
			//echo $select;exit;
			$result = $db->fetchAll($select);
			return $result;
		}
		
		public function getTransNilaiById($id){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"))
			->join(array('cg'=>'tbl_split_coursegroup'),'cg.Id=a.IdSplitGroup')
			->where('a.id  = ?',$id);
			//echo $select;exit;
			$result = $db->fetchRow($select);
			return $result;
		}
		public function getTransNilaiDeposit($idstd,$idsemester){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"),array("a.*",'grade_point'=>'nilai_indeks'))
			->join(array('cg'=>'tbl_split_coursegroup'),'cg.Id=a.IdSplitGroup')
			->join(array('s'=>'tbl_semestermaster'),'cg.IdSemesterSubject=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('CreditHours','subject_name'=>'sm.BahasaIndonesia','subject_code'=>'shortname'))
			->where('cg.IdSemester  <> ?',$idsemester)
			->where('s.SemesterFunctionType <> "5"')
			->where('s.SemesterFunctionType <> "2"')
			->where('a.IdStudentRegistration  = ?',$idstd)
			->where('a.postpone  = "1"')
			->order('cg.IdSemester');
			//echo $select;exit;
			$result = $db->fetchAll($select);
			return $result;
		}
		
		public function hasDeposit($idstd){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"))
			//->join(array('cg'=>'tbl_split_coursegroup'),'cg.Id=a.IdSplitGroup')
			->where('a.IdStudentRegistration  = ?',$idstd)
			->where('a.postpone  = "1"');
			//echo $select;exit;
			$result = $db->fetchAll($select);
			if ($result) return true; else return false;
			 
		}
		public function getTransNilaiDepositSKS($idstd,$idsemester){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"),array())
			->join(array('cg'=>'tbl_split_coursegroup'),'cg.Id=a.IdSplitGroup',array())
			->join(array('s'=>'tbl_semestermaster'),'cg.IdSemesterSubject=s.IdSemesterMaster',array())
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('skstotal'=>'SUM(CreditHours)'))
			->where('cg.IdSemester  <> ?',$idsemester)
			->where('s.SemesterFunctionType <> "5"')
			->where('s.SemesterFunctionType <> "2"')
			->where('a.IdStudentRegistration  = ?',$idstd)
			->where('a.postpone  = "1"');
			//echo $select;exit;
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function getTransferNilai($post){
			$db = Zend_Db_Table::getDefaultAdapter();
			
			//$result=false;
			//if ($post['problem']=="1") {
				$select = $db->select()
				->from(array("a"=>"nilai_transfer"),array('a.*','subcode'=>'kode_mk_asal','newsubcode'=>'kode_mk_tujuan','subject'=>'nm_mk_asal','newsubject'=>'nm_mk_tujuan','sks'=>'sks_asal','newsks'=>'sks_diakui','Grade_name_new'=>'nilai_huruf_diakui','Grade_name'=>'nilai_huruf_asal','IdConversionResult'=>'IdTransfer'))
				->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration')
				//->where('a.Approvedby >0')
				->order('st.registrationId');
				if ($post['IdSemester']!=null) $select->where('a.IdSemesterMain  = ?',$post['IdSemester']);
				if ($post['IdStudent']!=null) $select->where('st.registrationId  = ?',$post['IdStudent']);
				if ($post['programme']!=null) $select->where('st.IdProgram  = ?',$post['programme']);
				if ($post['intake_id']!=null) $select->where('st.IdIntake  = ?',$post['intake_id']);
				
				//echo $select;exit;
				$result = $db->fetchAll($select);
		//	}
			if (!$result) {
			
			
				$select = $db->select()
				->from(array("a"=>"tbl_conversion_result"))
				->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubject',array('subcode'=>'sm.shortname','subject'=>'sm.BahasaIndonesia','sks'=>'sm.CreditHours'))
				->join(array('s'=>'tbl_semestermaster'),'a.IdSemesterMain=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
				->join(array('sm1'=>'tbl_subjectmaster'),'sm1.IdSubject=a.IdSubjectNew',array('newsubcode'=>'sm1.shortname','newsubject'=>'sm1.BahasaIndonesia','newsks'=>'sm1.CreditHours'))
				->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration')
				->where('a.Approvedby >0')
				->order('st.registrationId');
				if ($post['IdSemester']!=null) $select->where('a.IdSemesterMain  = ?',$post['IdSemester']);
				if ($post['IdStudent']!=null) $select->where('st.registrationId  = ?',$post['IdStudent']);
				if ($post['programme']!=null) $select->where('st.IdProgram  = ?',$post['programme']);
				if ($post['intake_id']!=null) $select->where('st.IdIntake  = ?',$post['intake_id']);
					
				//echo $select;exit;
				$result = $db->fetchAll($select);
				
				if(!$result) {
					//get form course registrer
					$select = $db->select()
					->from(array("a"=>"tbl_studentregsubjects"),array('Grade_name_new'=>'a.grade_name','IdConversionResult'=>'IdStudentRegSubjects'))
					 ->join(array("t"=>'nilai_transfer_proposal'),'t.IdStudentRegSubjects=a.IdStudentRegSubjects',array('subcode'=>'kode_mk_asal','subject'=>'nm_mk_asal','sks'=>'sks_asal','Grade_name'=>'nilai_huruf_asal'))
					// ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=t.IdSubjectAsal',array('subcode'=>'sm.shortname','subject'=>'sm.BahasaIndonesia','sks'=>'sm.CreditHours'))
					->join(array('s'=>'tbl_semestermaster'),'a.IdSemesterMain=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
					->join(array('sm1'=>'tbl_subjectmaster'),'sm1.IdSubject=t.IdSubjectTujuan',array('newsubcode'=>'sm1.shortname','newsubject'=>'sm1.BahasaIndonesia','newsks'=>'sm1.CreditHours'))
					->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration')
					->where('t.Approved_by>0')
					->order('st.registrationId');
					if ($post['IdSemester']!=null) $select->where('a.IdSemesterMain  = ?',$post['IdSemester']);
					if ($post['IdStudent']!=null) $select->where('st.registrationId  = ?',$post['IdStudent']);
					if ($post['programme']!=null) $select->where('st.IdProgram  = ?',$post['programme']);
					if ($post['intake_id']!=null) $select->where('st.IdIntake  = ?',$post['intake_id']);
					//echo $select;
					$result = $db->fetchAll($select);
					//echo var_dump($result);exit;
					if(!$result) {
						//get form course registrer
						$select = $db->select()
						->from(array("a"=>"tbl_studentregsubjects"),array('Grade_name_new'=>'a.grade_name','IdConversionResult'=>'IdStudentRegSubjects'))	
						->join(array('s'=>'tbl_semestermaster'),'a.IdSemesterMain=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
						->join(array('sm1'=>'tbl_subjectmaster'),'sm1.IdSubject=a.IdSubject',array('subcode'=>'','newsubcode'=>'sm1.shortname','newsubject'=>'sm1.BahasaIndonesia','newsks'=>'sm1.CreditHours'))
						->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration')
						->order('st.registrationId');
						if ($post['IdSemester']!=null) $select->where('a.IdSemesterMain  = ?',$post['IdSemester']);
						if ($post['IdStudent']!=null) $select->where('st.registrationId  = ?',$post['IdStudent']);
						if ($post['programme']!=null) $select->where('st.IdProgram  = ?',$post['programme']);
						if ($post['intake_id']!=null) $select->where('st.IdIntake  = ?',$post['intake_id']);
						//echo $select;exit;
						$result = $db->fetchAll($select);
					}
				}
			}
			//echo $select;exit;
			return $result;
		}
		
		
		public function isPostpone($idstd,$idsemester){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"),array("a.*",'grade_point'=>'nilai_indeks'))
			->join(array('cg'=>'tbl_split_coursegroup'),'cg.Id=a.IdSplitGroup')
			->where('cg.IdSemester  = ?',$idsemester)
			->where('a.IdStudentRegistration  = ?',$idstd)
			->where('a.postpone  = "1"');
			//echo $select;exit;
			$result = $db->fetchAll($select);
			return $result;
		}
		
		public function isPostponeCourse($idstd,$idsemester,$idsubject){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"),array("a.*",'grade_point'=>'nilai_indeks'))
			->join(array('cg'=>'tbl_split_coursegroup'),'cg.Id=a.IdSplitGroup')
			->where('cg.IdSemester  = ?',$idsemester)
			->where('a.IdStudentRegistration  = ?',$idstd)
			->where('cg.IdSubject  = ?',$idsubject)
			->where('a.postpone  = "1"');
			//echo $select;exit;
			$result = $db->fetchRow($select);
			return $result;
		}
		public function isMovein($idstd,$idsemester){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"),array("a.*",'grade_point'=>'nilai_indeks'))
			->join(array('cg'=>'tbl_split_coursegroup'),'cg.Id=a.IdSplitGroup')
			->where('cg.IdSemester  = ?',$idsemester)
			->where('a.IdStudentRegistration  = ?',$idstd)
			->where('a.movein  = "1"');
			//echo $select;exit;
			$result = $db->fetchAll($select);
			return $result;
		}
		
		public function isMoveinOrPostpone($idstd,$idsemester){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"),array('grade_point'=>'nilai_indeks'))
			->join(array('cg'=>'tbl_split_coursegroup'),'cg.Id=a.IdSplitGroup',array())
			->where('cg.IdSemester  = ?',$idsemester)
			->where('a.IdStudentRegistration  = ?',$idstd)
			->where('a.movein  = "1" or a.postpone="1"');
			//echo $select;exit;
			$result = $db->fetchRow($select);
			if ($result) return true;else 
			return false;
		}
		
		public function getTransNilaibyIdSplitGroup($idgroup){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"),array('IdStudentRegistration','id'))
			->where('a.IdSplitGroup  = ?',$idgroup);
			$result = $db->fetchAll($select);
			return $result;
		}
		
		public function getCountTransNilai($idgrp){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"),array("Jml"=>"count(*)"))
			 ->where('a.IdSplitGroup  = ?',$idgrp)
				->where('a.postpone  = "0"');
			$result = $db->fetchRow($select);
			return $result['Jml'];
		}
		
		public function getLecturer($idgrp){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"tbl_split_lecturer"))
			->joinLeft(array('sm'=>'tbl_staffmaster'),'sm.IdStaff=a.IdLecturer')
			->where('a.IdSplitGroup  = ?',$idgrp);
			$result = $db->fetchAll($select);
			return $result;
		}
		public function isInLecturer($idgrp,$idlec=null){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"tbl_split_lecturer"))
			->join(array('sm'=>'tbl_staffmaster'),'sm.IdStaff=a.IdLecturer')
			->where('a.IdSplitGroup  = ?',$idgrp);
			if ($idlec!=null) $select->where('a.IdLecturer  = ?',$idlec);
			$result = $db->fetchRow($select);
			return $result;
		}
		public function getLecturerRaw($idgrp){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"tbl_split_lecturer"))
			//->join(array('sm'=>'tbl_staffmaster'),'sm.IdStaff=a.IdLecturer')
			->where('a.IdSplitGroup  = ?',$idgrp);
			$result = $db->fetchAll($select);
			return $result;
		}
		
		public function getTransNilaiRow($groupcode,$idstd,$idsubject,$idsemester,$idsemsubject){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"),array("a.*","idtrans"=>'a.id','grade_point'=>'nilai_indeks'))
			->join(array('cg'=>'tbl_split_coursegroup'),'a.IdSplitGroup=cg.id')
			->where('cg.IdSubject  = ?',$idsubject)
			->where('a.IdStudentRegistration  = ?',$idstd);
			if ($groupcode!=0) $select->where('cg.GroupCode  = ?',$groupcode);
			if ($idsemsubject!=0) $select->where('cg.IdSemesterSubject  = ?',$idsemsubject);
			if ($idsemester!=0) $select->where('cg.IdSemester  = ?',$idsemester);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function getTransNilaiSearch($post){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"),array("a.*","status_proses"=>'a.status',"IdTransNilai"=>'a.id'))
			->join(array('cg'=>'tbl_split_coursegroup'),'a.IdSplitGroup=cg.Id')
			->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration',array('nim'=>'registrationId','IdProgram'))
			//->join(array('km'=>'kuliah_mahasiswa'),'st.registrationid=km.id_reg_pdref and km.id_smt=cg.year',array())
			->join(array('ss'=>'tbl_semestermaster'),'ss.IdSemesterMaster=cg.IdSemester',array('yearsem'=>'CONCAT(LEFT(ss.SemesterMainCode,4),IF(ss.SemesterCountType=6,ss.SemesterPdpt,ss.SemesterCountType))','SemesterCountType'=>'IF(ss.SemesterCountType=6,ss.SemesterPdpt,ss.SemesterCountType)'))
			->join(array('s'=>'tbl_semestermaster'),'cg.IdSemesterSubject=s.IdSemesterMaster',array('semester_name'=>'s.SemesterMainName'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('CreditHours','subject_name'=>'sm.BahasaIndonesia','subject_code'=>'shortname'))
			//->join(array('sp'=>'student_profile'),'sp.appl_id=st.IdApplication',array('fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',sp.appl_fname, sp.appl_mname, sp.appl_lname)")))
			->join(array('prg'=>'tbl_program'),'prg.IdProgram=st.IdProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode','Strata_code_EPSBED'=>'Strata_code_EPSBED', 'Program_code_EPSBED'=>'Program_code_EPSBED','id_sms'))
			//->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=prg.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
			//->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED','id_sp'))
			//->join(array('intk'=>'tbl_intake'),'intk.IdIntake=st.IdIntake')
			->where('a.postpone="0"')
			->order('st.registrationid');
			 
			if (isset($post['problem']) && $post['problem']=='1')
				$select->where('a.status="1"');
			else  $select->where('a.status="0"');
			if (isset($post['IdSemester']) && $post['IdSemester']!='')
				$select->where('cg.IdSemester=?',$post['IdSemester']);
			
			if (isset($post['programme']) && $post['programme']!='')
				$select->where('st.IdProgram=?',$post['programme']);
				
			if (isset($post['IdMajoring']) && $post['IdMajoring']!='')
				$select->where('st.IdProgramMajoring=?',$post['IdMajoring']);
				
			if (isset($post['intake_id']) && $post['intake_id']!='')
				$select->where('st.IdIntake=?',$post['intake_id']);
			
			if (isset($post['IdStudent']) && $post['IdStudent']!='')
				$select->where('st.registrationid=?',$post['IdStudent']);
			
			if (isset($post['IdSubject']) && $post['IdSubject']!='')
				$select->where('sm.IdSubject=?',$post['IdSubject']);
			
			if (isset($post['GroupCode']) && $post['GroupCode']!='')
				$select->where('cg.GroupCode=?',$post['GroupCode']);
			
			if (isset($post['SubjectName']) && $post['SubjectName']!='')
				$select->where('sm.ShortName=?',$post['SubjectName']);
			
			//echo $select;exit;
			$data = $db->fetchAll($select);
			$temp='';
			foreach ($data as $key=>$value) {
			   
				 
				if ($value['IdStudentRegistration']!=$temp) {
					if ($this->isGraduate($value['IdStudentRegistration'])) {
						if ($this->isGraduateInSemester($value['IdStudentRegistration'], $post['IdSemester']))
							$aktif='L';
						else $aktif='LL';
					} else
						$aktif='A';
						
				
					//cek cuti/defer
					 
					if ($this->isDefer($value['IdStudentRegistration'], $post['IdSemester']))
						$aktif='C';
				
					//cek data mahasiswa_pt
					$row=$this->getMahasiswaPT($value['nim'],$value['id_sms']);
					if (!$row) $aktif='mhs_pt kosong';
					else if ($row['id_reg_pdref']=='') $aktif='blm registrasi feeder';
				
					//cek approval
					if ($this->isInKuliahByNim($post['IdSemester'], $value['nim'])) $status='Approved';
					else $status='';
					$temp=$value['IdStudentRegistration'];
				}
				$data[$key]['aktif']=$aktif;
				$data[$key]['status']=$status;
				
			} 
			//echo var_dump($data);exit;
			return $data;
			
			 
		}
		
		 public function getGradeFromFeederOld($post){
		 	
		 	$dbgrade=new Examination_Model_DbTable_StudentRegistrationSubject();
			$db = Zend_Db_Table::getDefaultAdapter();
			$feeder=new Reports_Model_DbTable_Wsclienttbls();
			$select = $db->select()
			->from(array("a"=>"tbl_studentregsubjects"),array('IdStudentRegSubjects','Pdpt_grade'=>'IFNULL(a.pdpt_grade,"")','grade_name','grade_point'))
			->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration',array('nim'=>'registrationId','IdProgram'))
		    ->join(array('sp'=>'student_profile'),'st.IdApplication=sp.appl_id',array('appl_id','appl_fname','appl_lname'))
		 	->join(array('ss'=>'tbl_semestermaster'),'ss.IdSemesterMaster=a.IdSemesterMain',array('SemesterMainName','yearsem'=>'CONCAT(LEFT(ss.SemesterMainCode,4),ss.SemesterCountType)','ss.SemesterCountType'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubject',array('CreditHours','subject_name'=>'sm.BahasaIndonesia','subject_code'=>'trim(sm.shortname)'))
			->join(array('pr'=>'tbl_program'),'st.IdProgram=pr.IdProgram')
			//->where('a.grade_name <> IFNULL(a.pdpt_grade,"")')
			->order('st.registrationid');
			
			if (isset($post['IdSemester']) && $post['IdSemester']!='')
				$select->where('a.IdSemesterMain=?',$post['IdSemester']);
			
			if (isset($post['programme']) && $post['programme']!='')
				$select->where('st.IdProgram=?',$post['programme']);
				
			if (isset($post['IdMajoring']) && $post['IdMajoring']!='')
				$select->where('st.IdProgramMajoring=?',$post['IdMajoring']);
				
			if (isset($post['intake_id']) && $post['intake_id']!='')
				$select->where('st.IdIntake=?',$post['intake_id']);
			
			if (isset($post['IdStudent']) && $post['IdStudent']!='')
				$select->where('st.registrationid=?',$post['IdStudent']);
			
			if (isset($post['IdSubject']) && $post['IdSubject']!='')
				$select->where('sm.IdSubject=?',$post['IdSubject']);
			
			 
			$data = $db->fetchAll($select);
			 
			$nim='';
			foreach ($data as $key=>$value) {
				if (trim($value['grade_name'])!=trim($value['Pdpt_grade'])) {
				    if (substr($value['yearsem'],4,1)=='3'|| substr($value['yearsem'],4,1)=='4') $value['yearsem']=substr($value['yearsem'],0,4).'1';
				    if (substr($value['yearsem'],4,1)=='5') $value['yearsem']=substr($value['yearsem'],0,4).'2';
					 
					if ($nim!=$value['nim']) {
						
						$filter="trim(nipd)='".$value['nim']."' and id_sms='".$value['id_sms']."'";
					    $response=$feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
						$nim=$value['nim'];
						if (count($response)>0) { 
							$idregpd=$response['id_reg_pd'];
							
						}
					}
					
					$filter="trim(kode_mk)='".$value['subject_code']."' and id_sms='".$value['id_sms']."'";
					$response=$feeder->fnGetRecord('mata_kuliah.raw', $filter);
					//echo $filter;echo var_dump($response);exit;
					$idmk=''; 
					$grade='';
					if (count($response)>0) {
						$idmk=$response['id_mk'];
					} else {
						$filter="trim(kode_mk)='".$value['subject_code']."'";
						$response=$feeder->fnGetRecord('mata_kuliah.raw', $filter);
						if (count($response)>0) $idmk=$response['id_mk'];
					}
					if ($idmk!='')	 {
						//get kelas 
						$filter="id_smt='".$value['yearsem']."' and id_sms='".$value['id_sms']."' and id_mk='".$idmk."'";
						$response=$feeder->fnGetRecordSet('kelas_kuliah.raw', $filter,100,0);
						//echo var_dump($response);exit;
						if (count($response)>0) {
							$grade='';
							foreach ($response as $item) {
								$idkls=$item['id_kls'];
								$filter="id_reg_pd='".$idregpd."' and id_kls='".$idkls."'";
								$responsenilai=$feeder->fnGetRecord('nilai.raw', $filter);
								
								if (count($responsenilai)>0) {
									$grade=$responsenilai['nilai_huruf'];
									$gradepoint=$responsenilai['nilai_indeks'];
									
									$gradehighest['IdStudentRegSubjects']=null;
									if ($gradepoint>$value['grade_point']) {
										//cek perbaikan atau remedial
										$gradehighest=$dbgrade->getHighestGradeByStdRegSubjects($value['IdStudentRegSubjects']);
									}  
									break;
								}
							}
							if ($grade!='')
								$dbgrade->updateData(array('Pdpt_grade'=>$grade,'IdStudentRegSubjects_P'=>$gradehighest['IdStudentRegSubjects']), $value['IdStudentRegSubjects']);
						}
					}
					 
					$data[$key]['Pdpt_grade']=$grade;
				}
				
			} 
			//echo var_dump($data);exit;
			return $data;
			
			 
		}
		
		public function getGradeFromFeeder($post){
		
			$dbgrade=new Examination_Model_DbTable_StudentRegistrationSubject();
			$db = Zend_Db_Table::getDefaultAdapter();
			$feeder=new Reports_Model_DbTable_Wsclienttbls();
			
			if (isset($post['update']) && $post['update']=='1') {
				$select = $db->select()
				->from(array("a"=>"tbl_studentregsubjects"),array('a.IdSubject','a.IdSemesterMain','a.IdStudentRegSubjects','a.IdStudentRegistration','exam_status','grade_point'))
				->joinLeft(array("ct"=>"tbl_course_tagging_group"),'ct.IdCourseTaggingGroup=a.IdCourseTaggingGroup',array("GroupCode"))
				->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration',array('registrationId'))
				->join(array('ss'=>'tbl_semestermaster'),'ss.IdSemesterMaster=a.IdSemesterMain',array('yearsem'=>'CONCAT(LEFT(ss.SemesterMainCode,4),ss.SemesterCountType)'))
				->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubject',array('subjectcode'=>'ShortName'))
				->join(array('pr'=>'tbl_program'),'st.IdProgram=pr.IdProgram',array('id_sms'))
				->where('a.IdCourseTaggingGroup<>0');
				//->where('a.grade_name <> IFNULL(a.pdpt_grade,"")')
				//->group('a.IdSemesterMain')
				//->group('a.IdSubject');
				
				
				if (isset($post['IdSemester']) && $post['IdSemester']!='') {
					$sql=$db->select()
					->from('tbl_semestermaster')
					->where('IdSemesterMaster=?',$post['IdSemester']);
					$smt=$db->fetchRow($sql);
					$semgasalgenap=$smt['SemesterCountType'];
					$sql=$db->select()
					->from('tbl_semestermaster',array('IdSemesterMaster'))
					->where('idacadyear=?',$smt['idacadyear'])
					->where('SemesterCountType=?',$semgasalgenap)
					->where('SemesterFunctionType in (1,0)');
					$select->where('a.IdSemesterMain in ('.$sql.')');
				
				}
					
					
				if (isset($post['programme']) && $post['programme']!='')
					$select->where('st.IdProgram=?',$post['programme']);
			
				if (isset($post['IdMajoring']) && $post['IdMajoring']!='')
					$select->where('st.IdProgramMajoring=?',$post['IdMajoring']);
			
				if (isset($post['intake_id']) && $post['intake_id']!='')
					$select->where('st.IdIntake=?',$post['intake_id']);
					
				if (isset($post['IdStudent']) && $post['IdStudent']!='')
					$select->where('st.registrationid=?',$post['IdStudent']);
					
				if (isset($post['IdSubject']) && $post['IdSubject']!='')
					$select->where('a.IdSubject=?',$post['IdSubject']);
				
				if (isset($post['problem']) && $post['problem']=='1')
					$select->where('a.grade_name <> IFNULL(TRIM(a.pdpt_grade),"")');
				
				//echo $select;exit;
				
				$data = $db->fetchAll($select);
				 
				 
				
				foreach ($data as $key=>$value) {
					$kodemk=trim($value['subjectcode']);
					$idsubject=$value['IdSubject'];
					$idsmtmain=$value['IdSemesterMain'];
					if (substr($value['yearsem'],4,1)=='3'|| substr($value['yearsem'],4,1)=='4') $value['yearsem']=substr($value['yearsem'],0,4).'1';
					if (substr($value['yearsem'],4,1)=='5') $value['yearsem']=substr($value['yearsem'],0,4).'2';
					$select = $db->select()
					->from(array("a"=>"trans_nilai"))
					->join(array('b'=>'tbl_split_coursegroup'),'a.IdSplitGroup=b.Id')
					->where('a.IdStudentRegistration=?',$value['IdStudentRegistration'])
					->where('b.IdSubject=?',$value['IdSubject'])
					->where('b.IdSemesterSubject=?',$value['IdSemesterMain'])
					->where("a.postpone='0'");
					//echo $select;
					$isIn=$db->fetchRow($select);
					
						if (!$isIn) {
							$select = $db->select()
							->from(array("a"=>"trans_nilai"))
							->join(array('b'=>'tbl_split_coursegroup'),'a.IdSplitGroup=b.Id')
							->where('a.IdStudentRegistration=?',$value['IdStudentRegistration'])
							->where('b.IdSubject=?',$value['IdSubject'])
							->where('b.IdSemester=?',$value['IdSemesterMain'])
							->where("a.postpone='0'");
							//echo $select;
							$isIn=$db->fetchRow($select);
						}
						/* echo 'idreg='.$value['IdStudentRegSubjects'];
						echo '<br>';
						echo var_dump($isIn);
						echo '<br>'; */
						if ($isIn) {
							//get kelas kuliah
							$select = $db->select()
							->from(array("a"=>"kelas_kuliah")) 
							->where('a.IdSplitGroup=?',$isIn['Id'])
							->where('a.id_kls is not null');
							$kelas=$db->fetchRow($select);
							//echo $select;
							//echo var_dump($kelas); exit;
							if ($kelas) {
								$mhs=$db->select()
								->from(array('mt'=>'mahasiswa_pt'))
								->where('mt.nipd=?',$value['registrationId']);
								$mhs=$db->fetchRow($mhs);
								if ($mhs) {
									$idregpdf=$mhs['id_reg_pdref'];
									$filter="id_kls='".$kelas['id_kls']."' and id_reg_pd='".$mhs['id_reg_pdref']."'";
									//get nilai from feeder
									$nilai=$feeder->fnGetRecord('nilai.raw', $filter);
								//	echo '<br>..nilai...'.$filter; echo var_dump($nilai);
									//echo '<br>';exit;
									
									if (count($nilai)>0) {
										 
											$grade=trim($nilai['nilai_huruf']);
											$gradepoint=$nilai['nilai_indeks'];
											$key="IdStudentRegSubjects='".$value['IdStudentRegSubjects']."'";
											echo $key;
											$this->fnupdateData('tbl_studentregsubjects', array('pdpt_grade'=>$grade), $key);
									} else {
										//outside  class 
										$filter="id_smt='".$value['yearsem']."' and id_sms='".$value['id_sms']."' and id_mk='".$kelas['id_mk']."'";
										$response=$feeder->fnGetRecordSet('kelas_kuliah.raw', $filter,100,0);
										//echo var_dump($response); 
										if (count($response)>0) {
											$grade='';
											foreach ($response as $item) {
												$idkls=$item['id_kls'];
												$filter="id_reg_pd='".$idregpdf."' and id_kls='".$idkls."'";
												$responsenilai=$feeder->fnGetRecord('nilai.raw', $filter);
										
												if (count($responsenilai)>0) {
													$grade=$responsenilai['nilai_huruf'];
													$gradepoint=$responsenilai['nilai_indeks'];
														
													$gradehighest['IdStudentRegSubjects']=null;
													if ($gradepoint>$value['grade_point']) {
														//cek perbaikan atau remedial
														$gradehighest=$dbgrade->getHighestGradeByStdRegSubjects($value['IdStudentRegSubjects']);
													}
													 
													break;
												}
												
											}
											
											//echo 'key-'.$value['IdStudentRegSubjects'];
											if ($grade!='')
												$dbgrade->updateDataPdpt(array('Pdpt_grade'=>trim($grade),'IdStudentRegSubjects_P'=>$gradehighest['IdStudentRegSubjects']), $value['IdStudentRegSubjects']);
										}
									}
						 
								}
							} else {
								$select = $db->select()
								->from(array("a"=>"kelas_kuliah"))
								->where('a.id_mkref=?',$kodemk)
								->where('a.id_smt =?',$value['yearsem'])
								->where('a.id_sms=?',$value['id_sms']);
								$kelas=$db->fetchRow($select);
								if ($kelas) {
									
									$mhs=$db->select()
									->from(array('mt'=>'mahasiswa_pt'))
									->where('mt.nipd=?',$value['registrationId']);
									$mhs=$db->fetchRow($mhs);
									if ($mhs) {
										$idregpdf=$mhs['id_reg_pdref'];
										$filter="id_kls='".$kelas['id_kls']."' and id_reg_pd='".$mhs['id_reg_pdref']."'";
										//get nilai from feeder
										$nilai=$feeder->fnGetRecord('nilai.raw', $filter);
										 
										//echo var_dump($nilai);echo $value['IdStudentRegSubjects'];
										if (count($nilai)>0) {
									
											$grade=trim($nilai['nilai_huruf']);
											$gradepoint=$nilai['nilai_indeks'];
											$key="IdStudentRegSubjects='".$value['IdStudentRegSubjects']."'";
												
											$this->fnupdateData('tbl_studentregsubjects', array('pdpt_grade'=>$grade), $key);
										} else {
											//outside  class
											$filter="id_smt='".$value['yearsem']."' and id_sms='".$value['id_sms']."' and id_mk='".$kelas['id_mk']."'";
											$response=$feeder->fnGetRecordSet('kelas_kuliah.raw', $filter,100,0);
											//echo var_dump($response);exit;
											if (count($response)>0) {
												$grade='';
												foreach ($response as $item) {
													$idkls=$item['id_kls'];
													$filter="id_reg_pd='".$idregpdf."' and id_kls='".$idkls."'";
													$responsenilai=$feeder->fnGetRecord('nilai.raw', $filter);
														
													if (count($responsenilai)>0) {
														$grade=$responsenilai['nilai_huruf'];
														$gradepoint=$responsenilai['nilai_indeks'];
															
														$gradehighest['IdStudentRegSubjects']=null;
														if ($gradepoint>$value['grade_point']) {
															//cek perbaikan atau remedial
															$gradehighest=$dbgrade->getHighestGradeByStdRegSubjects($value['IdStudentRegSubjects']);
														}
														//echo var_dump($responsenilai);exit;
														//break;
														
													}
												}
												//echo $grade;exit;
												if ($grade!='')
													$dbgrade->updateDataPdpt(array('Pdpt_grade'=>$grade,'IdStudentRegSubjects_P'=>$gradehighest['IdStudentRegSubjects']), $value['IdStudentRegSubjects']);
											}
										}
									
									}
									
								}
						}
					}  
				}
			}
		 
			
			$select = $db->select()
			->from(array("a"=>"tbl_studentregsubjects"),array('IdStudentRegSubjects','Pdpt_grade'=>'IFNULL(a.pdpt_grade,"")','grade_name','grade_point','IdSubject','exam_status'))
			->joinLeft(array("ct"=>"tbl_course_tagging_group"),'ct.IdCourseTaggingGroup=a.IdCourseTaggingGroup',array("GroupCode"))
				
			->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration',array('nim'=>'registrationId','IdProgram','IdLandscape','IdStudentRegistration'))
			->join(array('sp'=>'student_profile'),'st.IdApplication=sp.appl_id',array('appl_id','appl_fname','appl_lname'))
			->join(array('ss'=>'tbl_semestermaster'),'ss.IdSemesterMaster=a.IdSemesterMain',array('SemesterMainName','yearsem'=>'CONCAT(LEFT(ss.SemesterMainCode,4),ss.SemesterCountType)','ss.SemesterCountType'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubject',array('CreditHours','subject_name'=>'sm.BahasaIndonesia','subject_code'=>'trim(sm.shortname)'))
			->join(array('pr'=>'tbl_program'),'st.IdProgram=pr.IdProgram',array())
			->where('a.IdCourseTaggingGroup<>0')
			//->where('a.grade_name <> IFNULL(a.pdpt_grade,"")')
			->order('st.registrationid');
				
			if (isset($post['IdSemester']) && $post['IdSemester']!='')
			{
				$sql=$db->select()
				->from('tbl_semestermaster')
				->where('IdSemesterMaster=?',$post['IdSemester']);
				$smt=$db->fetchRow($sql);
				$semgasalgenap=$smt['SemesterCountType'];
				$sql=$db->select()
				->from('tbl_semestermaster',array('IdSemesterMaster'))
				->where('idacadyear=?',$smt['idacadyear'])
				->where('SemesterCountType=?',$semgasalgenap)
				->where('SemesterFunctionType in (1,0)');
				$select->where('a.IdSemesterMain in ('.$sql.')');
			}
				
			if (isset($post['programme']) && $post['programme']!='')
				$select->where('st.IdProgram=?',$post['programme']);
			
			if (isset($post['IdMajoring']) && $post['IdMajoring']!='')
				$select->where('st.IdProgramMajoring=?',$post['IdMajoring']);
			
			if (isset($post['intake_id']) && $post['intake_id']!='')
				$select->where('st.IdIntake=?',$post['intake_id']);
				
			if (isset($post['IdStudent']) && $post['IdStudent']!='')
				$select->where('st.registrationid=?',$post['IdStudent']);
				
			if (isset($post['IdSubject']) && $post['IdSubject']!='')
				$select->where('sm.IdSubject=?',$post['IdSubject']);
				
			if (isset($post['problem']) && $post['problem']=='1')
				$select->where('a.grade_name <> IFNULL(TRIM(a.pdpt_grade),"")');
			
			$data = $db->fetchAll($select);
			return $data;
		}
		
		
		 
		
		public function getSplitKelasById($id){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"tbl_split_coursegroup"),array("a.*"))
			->join(array('s'=>'tbl_semestermaster'),'a.IdSemesterSubject=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubject',array('subject_name'=>'sm.BahasaIndonesia','subject_code'=>'shortname'))
			->where('a.id  = ?',$id);
			$result = $db->fetchRow($select);
			return $result;
		}
		public function getSplitKelasByIdDetail($id,$idlect){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"tbl_split_coursegroup"),array("a.*","IdSplitGroup"=>'a.Id',"thnlapor"=>'LEFT(year,4)'))
			->join(array('s'=>'tbl_semestermaster'),'a.IdSemester=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
			->join(array('prg'=>'tbl_program'),'prg.IdProgram=a.IdProgram',array('strata','jenjang'=>'prg.id_jenjang_pendidikan','ProgramName'=>'prg.ArabicName', 'ProgramCode'=>'prg.ProgramCode','Strata_code_EPSBED'=>'prg.Strata_code_EPSBED', 'Program_code_EPSBED'=>'prg.Program_code_EPSBED','prg.id_sms','prg.IdProgram'))
			->join(array('ll'=>'tbl_split_lecturer'),'ll.IdSplitGroup=a.Id',array('rencanaLect'=>'ll.rencana','realisasiLect'=>'ll.realisasi','idlec'=>'ll.Id'))
			//->joinLeft(array('dt'=>'dosen_pt'),'ll.IdLecturer=dt.IdStaff',array('id_reg_ptk'))
			->join(array('st'=>'tbl_staffmaster'),'ll.IdLecturer=st.IdStaff',array('IdStaff'=>'st.IdStaff','fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',TRIM(CONCAT_WS(' ',TRIM(st.firstname), TRIM(st.secondname))), TRIM(st.thirdname))"),'Dosen_Code_EPSBED','id_sdm','status','DOB'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubject',array('IdCourse'=>'sm.IdSubject','subject_name'=>'sm.BahasaIndonesia','subject_code'=>'shortname','CreditHours'))
			->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=prg.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
			->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED','id_sp','IdUniversity'))
			->where('a.id  = ?',$id)
			->where('ll.IdLecturer  = ?',$idlect);
			 
			$result = $db->fetchAll($select);
			//echo var_dump($result);exit;
			return $result;
		}
		
		
		public function getSplitKelasBySubject($idsubject,$idprogram=0,$semester=null){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"tbl_split_coursegroup"),array("IdGroup"=>'a.Id',"a.*"))
			->join(array('lec'=>'tbl_split_lecturer'),'a.Id=lec.IdSplitGroup')
			->join(array('s'=>'tbl_semestermaster'),'a.IdSemesterSubject=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
			->joinLeft(array('st'=>'tbl_staffmaster'),'lec.IdLecturer=st.IdStaff',array('IdStaff','staffName'=>'FullName','Dosen_Code_EPSBED'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubject',array('subject_name'=>'sm.BahasaIndonesia','subject_code'=>'shortname'))
			->where('a.IdSubject  = ?',$idsubject)
			->where('a.IdProgram  = ?',$idprogram); 
			if ($semester!=null) {
				$select->where('a.IdSemester=?',$semester);
				$result = $db->fetchRow($select);
			} else 	$result = $db->fetchAll($select);
			return $result;
		}
		public function getSplitKelasResultById($id){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"tbl_split_coursegroup"),array("a.*"))
			->join(array('s'=>'tbl_semestermaster'),'a.IdSemester=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
			//->joinLeft(array('st'=>'tbl_staffmaster'),'a.IdLecturer=st.IdStaff',array('staffName'=>'FullName'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubject',array('subject_name'=>'sm.BahasaIndonesia','subject_code'=>'shortname'))
			->where('a.IdGroupOrigin  = ?',$id);
			$result = $db->fetchAll($select);
			return $result;
		}
		
		public function getSplitStudent($id){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"trans_nilai"),array("a.*"))
			->join(array('st'=>'tbl_studentregistration'),'a.IdStudentRegistration=st.IdStudentRegistration',array('registrationId'))
			->join(array('sp'=>'student_profile'),'st.IdApplication=sp.appl_id',array('StudentName'=>"CONCAT(appl_fname,' ',appl_mname,' ',appl_lname)"))
			->where('a.IdSplitGroup  = ?',$id);
			$result = $db->fetchAll($select);
			return $result;
		}
		
		public function isInSplitKelas($idstd,$idsubject,$idsemester){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"tbl_split_lecturer"))
			->join(array('kls'=>'tbl_split_coursegroup'),'a.IdSplitGroup=kls.id')
			->where('kls.IdSemester  = ?',$idsemester)
			->where('a.IdStudentRegistration=?',$idstd)
			->where('kls.IdSubject=?',$idsubject);
			$result = $db->fetchRow($select);
			return $result;
		}
		public function isSplitKelasNotEmpty($id){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"tbl_split_lecturer"))
			->join(array('kls'=>'tbl_split_coursegroup'),'a.IdSplitGroup=kls.id')
			->where('kls.id = ?',$id);
			$result = $db->fetchRow($select);
			return $result;
		}
		public function isInSplitCourse($idgroup,$idsubject,$idsemester,$idprogram){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('kls'=>'tbl_split_coursegroup'))
			->where('kls.IdCourseTaggingGroup=?',$idgroup)
			->where('kls.IdSemesterSubject=?',$idsemester)
			->where('kls.IdSubject=?',$idsubject)
			->where('kls.IdProgram=?',$idprogram);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function isInTransNilai($idstudent,$idsem,$idsubject){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('kls'=>'trans_nilai'))
			->join(array('sp'=>'tbl_split_coursegroup'),'sp.id=kls.idSplitGroup')
			->where('sp.IdSemester=?',$idsem)
			->where('sp.IdSubject=?',$idsubject)
			->where('kls.IdStudentRegistration=?',$idstudent); 
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function getMahasiswaPT($id,$sms){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"mahasiswa_pt"),array("a.*"))
			->where('a.nipd = ?',$id)
			;
			if ($sms!=null) $select->where('a.id_sms = ?',$sms);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function getDosenPT($id){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"dosen_pt"),array("a.*"))
			->where('a.id  = ?',$id);
			$result = $db->fetchRow($select);
			return $result;
		}
		
		public function getDosenPTbyIdLecturer($id){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"dosen_pt"),array("a.*"))
			->where('a.IdStaff  = ?',$id);
			//->where('a.id_thn_ajaran=?',$thn);
			$result = $db->fetchRow($select);
			return $result;
		}
		public function getDosenPTbyIdLecturerProg($id,$idsms){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array("a"=>"dosen_pt"),array("a.*"))
			->where('a.IdStaff  = ?',$id)
			->where('a.id_sms  = ?',$idsms);
			//->where('a.id_thn_ajaran=?',$thn);
			$result = $db->fetchRow($select);
			return $result;
		}
	
		public function approveAjarDosen($data,$id_ajar) {
			unset($data['IdSemester']);
			unset($data['programme']);
			unset($data['IdSubject']);
			$Feeder = new Reports_Model_DbTable_Wsclienttbls();
			$ajars=$this->fnViewAjardDetails($id_ajar);
			// echo var_dump($ajars);exit;
			$stserror="0";
			if ($ajars) {
				//transfer kelas
				$idklsm=$ajars['id_klsm'];
				$klas=$this->getKelas($idklsm);
				//echo var_dump($klas);exit;
				$idkls=$klas['id_kls'];
				if ($klas['id_kls']=='') {
					//
					$id=$klas['id'];
					//echo var_dump($klas);exit;
					$this->unsetKelasKuliah($klas);
					

					unset($klas['tgl_mulai_koas']);
					unset($klas['tgl_selesai_koas']);
					unset($klas['id_mou']);
					 
					unset($klas['id_kls_pditt']); 

					$filter = "id_sms='".$klas['id_sms']."' AND  id_smt= '".$klas['id_smt']."' AND id_mk = '".$klas['id_mk']."' AND nm_kls ='".$klas['nm_kls']."'";
					//$filter='';
					//echo $filter;
					$checkCurrentKuliah = $Feeder->fnGetRecord('kelas_kuliah.raw', $filter);
					//echo var_dump($checkCurrentKuliah);exit;
					//---------------------------
					if(count($checkCurrentKuliah)==0)
					{
						//echo var_dump($klas);echo 'ini kelas.';exit;
						//unset($klas['IdSubject']);
						$temp1['id_smt'] = $klas['id_smt'];
						$temp1['id_sms'] = $klas['id_sms'];
						$temp1['id_mk'] = $klas['id_mk'];
						$temp1['nm_kls'] = $klas['nm_kls'];
						$temp1['sks_mk'] = $klas['sks_mk'];
						$temp1['sks_tm'] = $klas['sks_tm'];
						$temp1['sks_prak'] = $klas['sks_prak'];
						$temp1['sks_prak_lap'] = $klas['sks_prak_lap'];
						$temp1['sks_sim'] = $klas['sks_sim'];
						$temp1['bahasan_case']='';
						//$temp1['tgl_mulai_koas']='';
						//$temp1['tgl_selesai_koas']='';
						//$temp1['id_mou']='';
						$temp1['a_selenggara_pditt']='0';
						$temp1['kuota_pditt']='0';
						$temp1['a_pengguna_pditt']='0';
						//$temp1['id_kls_pditt']='';
						//echo var_dump($temp1);
						$response = $Feeder->insertToFeeder('kelas_kuliah',$temp1);
						//update kelas
						//echo var_dump($temp1);echo '<br>';
						//echo var_dump($response);
						//echo 'ini kelas.';exit;
						if ($response['result']['error_code']==0) {
							$idkls=$response['result']['id_kls'];
							$ajars['id_kls']=$idkls;
							$this->fnupdateKelasKuliah(array('id_kls'=>$idkls), $id);
							//echo var_dump($response);echo 'ini kelas.';exit;
						} else {
							$formdata['error_code']=$response['result']['error_code'];
							$formdata['error_desc']=$response['result']['error_desc'];
							$this->fnupdateKelasKuliah($formdata, $id);
							$stserror="1";
							
						}
					} else 
					{
						//update feeder
						$temp1=array();
						$temp1['id_smt'] = $klas['id_smt'];
						$temp1['id_sms'] = $klas['id_sms'];
						$temp1['id_mk'] = $klas['id_mk'];
						$temp1['nm_kls'] = $klas['nm_kls'];
						$temp1['sks_mk'] = $klas['sks_mk'];
						$temp1['sks_tm'] = $klas['sks_tm'];
						$temp1['sks_prak'] = $klas['sks_prak'];
						$temp1['sks_prak_lap'] = $klas['sks_prak_lap'];
						$temp1['sks_sim'] = $klas['sks_sim'];
						$temp1['bahasan_case']='';
						 
						$temp1['a_selenggara_pditt']='0';
						$temp1['kuota_pditt']='0';
						$temp1['a_pengguna_pditt']='0';
						$idkls=$checkCurrentKuliah['id_kls'];
						//echo $idkls;exit;
						$res=$Feeder->updateToFeeder('kelas_kuliah',array('id_kls'=>$idkls),$temp1);
						//echo var_dump($res);exit;
						
						$this->fnupdateKelasKuliah(array('id_kls'=>$idkls), $id);
					}
					
				} else  {
					$filter = "id_sms='".$klas['id_sms']."' AND  id_smt= '".$klas['id_smt']."' AND id_mk = '".$klas['id_mk']."' AND nm_kls ='".$klas['nm_kls']."'";
					//$filter='';
						
					$checkCurrentKuliah = $Feeder->fnGetRecord('kelas_kuliah.raw', $filter);
					//echo var_dump($checkCurrentKuliah);
					if (count($checkCurrentKuliah)>0) {
					//update feeder
					$temp1=array();
					$temp1['id_smt'] = $klas['id_smt'];
					$temp1['id_sms'] = $klas['id_sms'];
					$temp1['id_mk'] = $klas['id_mk'];
					$temp1['nm_kls'] = $klas['nm_kls'];
					$temp1['sks_mk'] = $klas['sks_mk'];
					$temp1['sks_tm'] = $klas['sks_tm'];
					$temp1['sks_prak'] = $klas['sks_prak'];
					$temp1['sks_prak_lap'] = $klas['sks_prak_lap'];
					$temp1['sks_sim'] = $klas['sks_sim'];
					$temp1['bahasan_case']='';
						
					$temp1['a_selenggara_pditt']='0';
					$temp1['kuota_pditt']='0';
					$temp1['a_pengguna_pditt']='0';
					
					$idkls=$checkCurrentKuliah['id_kls'];
					//echo $idkls; 
					$res=$Feeder->updateToFeeder('kelas_kuliah',array('id_kls'=>$idkls),$temp1);
					//echo var_dump($res);exit;
					$id=$klas['id'];
					$this->fnupdateKelasKuliah(array('id_kls'=>$idkls), $id);
				
					}
				}
				// exit;
				//transfer ajar dosen
					if ($stserror=="0") {
					unset($ajars['id']);
					unset($ajars['dtstamp']);
					unset($ajars['id_klsm']);
					unset($ajars['error_code']);
					unset($ajars['error_desc']);
					unset($ajars['status']);
					unset($ajars['date_of_approval']);
					unset($ajars['id_ajar']);
					unset($ajars['id_subst']);
					//calculate sks
					
					//if ($ajars['id_reg_ptk']=='') $ajars['id_reg_ptk']=$idregptk;
					if ($ajars['id_kls']=='') $ajars['id_kls']=$idkls;
					
					$filter= "id_reg_ptk='".$ajars['id_reg_ptk']."' AND  id_kls= '".$ajars['id_kls']."'";
					$checkCurrentAjarDosen = $Feeder->fnGetRecord('ajar_dosen.raw', $filter);
					//echo $filter;exit;
					$idajar='';
					unset($data['id']);
					if(count($checkCurrentAjarDosen)==0)
					{
						//echo var_dump($ajars);exit;
						 
						$response = $Feeder->insertToFeeder('ajar_dosen',$ajars);
						//update dosen pt
						//echo var_dump($response);exit;
						if ($response['result']['error_code']==0) {
							$idajar=$response['result']['id_ajar'];
							$data['error_code']=$response['result']['error_code'];
							$data['error_desc']=$response['result']['error_desc'];
							$data['id_ajar']=$idajar;
							$data['id_kls']=$ajars['id_kls'];
							$this->fnupdateAjard($data, $id_ajar);
						} else {
							//save error message;
							$data['error_code']=$response['result']['error_code'];
							$data['error_desc']=$response['result']['error_desc'];
							$this->fnupdateAjard($data, $id_ajar);
						}
					} else {
						$idajar=$checkCurrentAjarDosen['id_ajar'];
						$response=$Feeder->updateToFeeder('ajar_dosen',array('id_ajar'=>$checkCurrentAjarDosen['id_ajar']), $ajars);
						$data['id_ajar']=$checkCurrentAjarDosen['id_ajar'];
						$data['id_kls']=$checkCurrentAjarDosen['id_kls'];
						$data['error_code']=null;
						$data['error_desc']=null;
						$this->fnupdateAjard($data, $id_ajar);
						
					}
					
				}
			}
			return array('idkelas'=>$idkls,'idajar'=>$idajar);
		}
		
		public function unsetKelasKuliah(&$result) {
			unset( $result['id_kls']);
			unset( $result['id']);
			unset( $result['error_code']);
			unset( $result['error_desc']);
			unset( $result['error_code']);
			unset( $result['date_of_approval']);
			unset( $result['id_smsref']);
			unset( $result['id_mkref']);
			unset( $result['status']);
			unset( $result['IdSplitGroup']);
			unset($result['IdSubject']);
		}
		
		public function getSemesterPdptStyle($idSem) {
			
			$db = Zend_Db_Table::getDefaultAdapter();
			 
			$lstrSelect = $db ->select()
		 	->from(array('s'=>'tbl_semestermaster'),array('yearsem'=>'CONCAT(LEFT(SemesterMainCode,4),SemesterCountType)'))
		 	->where('s.IdSemesterMaster=?',$idSem);
		 	$sem = $db->fetchRow($lstrSelect);
		 	$yearsem=$sem['yearsem'];
			
			return $yearsem;
		}
		
		public function getSemesterPdptList($semester,$yearnumber) {
			
			$Feeder=new Reports_Model_DbTable_Wsclienttbls();
			if (substr($semester,4,1)=="1")
				$filter="id_smt <='".$semester."'";
			else $filter="id_smt <='".substr($semester, 0,4)."3'";
			$response=$Feeder->fnGetRecordSet('semester', $filter,null,null);
			if (count($response)>0) {
				 
				foreach ($response as $value) {
					$semesterlist[]=$value['id_smt'];
				}
			} else $sem=array();
			array_multisort($semesterlist,SORT_DESC);
			$yeardef="";
			$count=0;
			foreach ($semesterlist as $item) {
				$year=substr($item, 0,4);
				//echo $semester.'-'.$year;exit;
				if ($year!=$yeardef) {
					$yeardef=$year;
					
					$count++;
				}
				$sem[]=$item;
				if ($count == $yearnumber) break;
			}
			array_multisort($sem,SORT_ASC);
			return $sem;
		}
		
		public function getAktivitasKuliah(&$nim,&$semester,&$skstotal,&$data,$idsms=null) {
			
			$Feeder=new Reports_Model_DbTable_Wsclienttbls();
			
			if ($idsms!=null) $filter="trim(nipd)= '".trim($nim)."' and id_sms='".$idsms."'";
				else $filter="trim(nipd)= '".trim($nim)."'";
			$response=$Feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
			//echo $filter; echo var_dump($response);exit;
			if (count($response)>0) {
				//get nama
				$skyudisium=$response['sk_yudisium'];
				$tglyudisium=date('Y-m-d',strtotime($response['tgl_sk_yudisium']));
				$noseriijasah=$response['no_seri_ijazah'];
				$idreg=$response['id_reg_pd'];
				$filter="id_pd= '".$response['id_pd']."'";
				$response=$Feeder->fnGetRecord('mahasiswa.raw', $filter);
				$nim=$response['nm_pd'];
				$data=$response;
				$data['sk_yudisium']=$skyudisium;
				$data['tgl_sk_yudisium']=$tglyudisium;
				$data['no_seri_ijazah']=$noseriijasah;
				
				$filter="id_reg_pd='".$idreg."'";
				$response=$Feeder->fnGetRecordSet('kuliah_mahasiswa.raw', $filter,null,null);
				// echo var_dump($semester);
				if (count($response)>0) {
					foreach ($response as $key=>$value) {
						$index=array_search($value['id_smt'], $semester);
						if ($index) {
							$semester[$index]=array('sks'=>$value['sks_smt'],'ipk'=>$value['ipk']);
							$skstotal=$skstotal+(int)$value['sks_smt'];
						}  else {
							
							
							$semester[$index]=array();
						}
					}
					//$semester[]=$skstotal;
					//echo var_dump($semester);//exit;
					return "0"; 
				} 
				return "2";
				
			} else {
				return "1";
			}
		}
		
		public function getsksDiakui($nim) {
				
			$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		
			$filter="trim(nipd)= '".$nim."'";
			$response=$Feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
			if (count($response)>0) {
				//get nama
				$idreg=$response['id_reg_pd'];
				$filter="id_pd= '".$response['id_pd']."'";
					
				$filter="id_reg_pd='".$idreg."'";
				$response=$Feeder->fnGetRecordSet('nilai_transfer.raw', $filter,null,null);
				$skstotal=0;
				if (count($response)>0) {
					foreach ($response as $key=>$value) {
						  
							$skstotal=$skstotal+(int)$value['sks_diakui'];
						 
					} 
					
					 
				}
				return $skstotal;	 
			} return 0;
		}
		
		 public function getStudentGrade($post) {
		 	// echo var_dump($post);
		 	if ($post['IdSemester']==''||$post['programme']=='') return false;
		 	
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	
		 	if ($post['semester']=='') $post['semester']=$post['IdSemester'];
		 	$lstrSelect = $db ->select()
		 	->from(array('s'=>'tbl_semestermaster'),array('yearsem'=>'CONCAT(LEFT(SemesterMainCode,4),SemesterCountType)'))
		 	->where('s.IdSemesterMaster=?',$post['semester']);
		 	$sem = $db->fetchRow($lstrSelect);
		 	$yearsem=$sem['yearsem'];
		 	
		 	$lstrSelect = $db ->select()
		 	->from(array('sg'=>'tbl_studentregistration'),array('nim'=>'registrationId','IdStudentRegistration','StdStatus'=>'sg.Status','jenis_pendaftaran','sks_diakui'))
		 	->join(array('int'=>'tbl_intake'),'sg.IdIntake=int.IdIntake',array('angkatan'=>'CONCAT(LEFT(IntakeId,4),Period)'))
		 	->join(array('sp'=>'student_profile'),'sp.appl_id=sg.IdApplication',array('fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',sp.appl_fname, sp.appl_mname, sp.appl_lname)")))
		 	->join(array('prg'=>'tbl_program'),'prg.IdProgram=sg.IdProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode','Program_code_EPSBED','Strata_code_EPSBED','id_sms'))
		 	->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=prg.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
		 	->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED','id_sp'))
		 	//->where("sg.profileStatus in ('92','93','96','248')")
		 	->order('sg.registrationid');
		 	 
		 	if($post['faculty']>0){
		 		$lstrSelect->where('prg.IdCollege =?',$post['faculty']);
		 	}
		 	/* if (isset($post['problem'])) {
		 		if ($post['problem']=="1" ){
		 			$lstrSelect = $lstrSelect->where("sg.registrationId in (select id_reg_pdref from kuliah_mahasiswa where IdSemesterMain =?)",$post['IdSemester']);
		 		
		 		}	else 	$lstrSelect = $lstrSelect->where("sg.registrationId not in (select id_reg_pdref from kuliah_mahasiswa where IdSemesterMain =?)",$post['IdSemester']);
		 	} */
		 	 
		 	if (isset($post['intake_id']) && !empty($post['intake_id']) ){
		 		$lstrSelect = $lstrSelect->where("sg.IdIntake = ?",$post['intake_id']);
		 	}
		 	 
		 	if(isset($post['programme']) && !empty($post['programme']) ){
		 		$lstrSelect = $lstrSelect->where("sg.IdProgram  = ?",$post['programme']);
		 	}
		 	
		 	if(isset($post['IdMajoring']) && !empty($post['IdMajoring']) ){
		 		$lstrSelect = $lstrSelect->where("sg.IdProgramMajoring  = ?",$post['IdMajoring']);
		 	}
		 	
		 	if(isset($post['IdStudent']) && !empty($post['IdStudent']) ){
		 		$lstrSelect = $lstrSelect->where("sg.registrationid  = ?",$post['IdStudent']);
		 	}
		 	
		 	if(isset($post['IdStudentRegistration']) && !empty($post['IdStudentRegistration']) ){
		 		$lstrSelect = $lstrSelect->where("sg.IdStudentRegistration  = ?",$post['IdStudentRegistration']);
		 	}
		 	if(isset($post['status']) && $post['status']=='L' ){
		 		$lstrSelect->join(array('g'=>'graduate'),'g.IdStudentRegistration=sg.IdStudentRegistration');
		 		 
		 	}
		 	
		 	//echo $lstrSelect;exit;
		 	$data = $db->fetchAll($lstrSelect);
		 	//echo var_dump($data);
		 	$dbGrade=new Examination_Model_DbTable_StudentGrade();
		 	if (isset($post['problem'])) {
		 		if ($post['problem']=="1" ){
				 	foreach ($data as $key=>$value) {
				 		if (!$this->isInKuliahByNim($post['semester'], $value['nim'],$value['id_sms']))  
				 			unset($data[$key]);
				 		 
				 	}
		 		} else {
		 			foreach ($data as $key=>$value) {
		 				if ($this->isInKuliahByNim($post['semester'], $value['nim'],$value['id_sms']))  
		 					unset($data[$key]);
		 				 
		 			}
		 		}
		 	}
		 	
		 	foreach ($data as $key=>$value) {
		 		 
		 		$data[$key]['semesterlapor']=$yearsem;
		 		//if any posposne set status postposen=1
		 		$grade=$dbGrade->getStudentGrade($value['IdStudentRegistration'], $post['IdSemester']);
		 		$ips=array('GPA'=>$grade['sg_gpa'],
		 				'sks_sem'=>$grade['sg_sem_credithour'],
		 				'point'=>$grade['sg_sem_totalpoint']
		 		);
		 		$pospone="0";//$ipspost=false;
		 		if (($value['Strata_code_EPSBED']=='C' || 
		 			$value['Strata_code_EPSBED']=='D' || 
		 			$value['Strata_code_EPSBED']=='E' ) && $grade['sg_sem_credithour']>24) $pospone="1";
		 		if ($value['Strata_code_EPSBED']=='B'  && $grade['sg_sem_credithour']>18) $pospone="1";
		 		 
		 		if ($value['Strata_code_EPSBED']=='A'  && $grade['sg_sem_credithour']>12) $pospone="1";
		 		 
		 		$trans=$this->isMoveinOrPostpone($value['IdStudentRegistration'], $post['IdSemester']);
		 		//echo var_dump($trans);exit;
		 		if ($pospone=="1" || $trans) {
		 			//echo var_dump($trans);exit;
		 			$data[$key]['postpone']="1";
		 			$trans=$this->getTransNilai($value['IdStudentRegistration'], $post['IdSemester']);
		 			//$ipspost=true;
		 			if ($trans) {
		 					$ips=$this->calculateGPA($trans,'0');
		 					$grade=$dbGrade->getStudentGrade($value['IdStudentRegistration'], $post['IdSemester']);
		 					
		 			}
		 			//else $ipspost=false;
		 		}/*  else {
		 			$data[$key]['postpone']="0";
		 			$grade=$dbGrade->getStudentGrade($value['IdStudentRegistration'], $post['IdSemester']);
		 			$ips=array('GPA'=>$grade['sg_gpa'],
		 					'sks_sem'=>$grade['sg_sem_credithour'],
		 					'point'=>$grade['sg_sem_totalpoint']
		 			);
		 		} */
		 		
		 		
		 	 
		 		$data[$key]['IdSemesterMain']=$post['IdSemester'];
		 		$unset="0";
		 	    if ($grade && !($this->isQuit($value['IdStudentRegistration'],$post['semester']) || $this->isDefer($value['IdStudentRegistration'],$post['semester']))) {
		 			//if ($ipspost) {
		 				$ipsem=$ips['GPA'];
		 				$sksem=$ips['sks_sem'];
		 				$point=$ips['point'];
		 			//} 
		 			
 		 			/* else {
  		 				 $ipsem=$grade['sg_gpa'];
  		 				 $sksem=$grade['sg_sem_credithour'];
  		 				 $point=$grade['sg_sem_totalpoint'];
  		 			} */
		 			//echo $value['angkatan']."=".$grade['year'].$grade['SemesterCountType'];exit;
		 			if($value['angkatan']==$grade['year'].$grade['SemesterCountType'] && $value['jenis_pendaftaran']=="1") {
		 				$ipk=$ipsem;
		 				$skstotal=$sksem;
		 				$totalpoint=$point;
		 				//echo "dsasma";exit;
		 			} else {
		 				$ipk=$grade['sg_all_cgpa'];
		 				$skstotal=$grade['sg_all_cum_credithour'];
		 				$totalpoint=$grade['sg_all_cum_totalpoint'];
		 			}
		 			$data[$key]['year']=$grade['year'];
		 			$data[$key]['SemesterCountType']=$grade['SemesterCountType'];
		 			$data[$key]['sg_gpa']=$ipsem;
		 			$data[$key]['sg_sem_credithour']=$sksem;
		 			$data[$key]['sg_all_cgpa']=$ipk;
		 			$data[$key]['sg_all_cum_credithour']=$skstotal;
		 			$data[$key]['sg_sem_totalpoint']=$point;
		 			$data[$key]['sg_all_cum_totalpoint']=$totalpoint;
		 			$data[$key]['sg_univ_gpa']=$grade['sg_univ_gpa'];
		 			$data[$key]['sg_cgpa']=$grade['sg_cgpa'];
		 			$data[$key]['nin']='';
		 			//echo var_dump($data);echo "----";
		 			if ($this->isGraduate($value['IdStudentRegistration'])) {
		 				
		 				$data[$key]['sg_all_cgpa']=$grade['sg_cgpa'];
		 				$data[$key]['sg_all_cum_credithour']=$grade['sg_cum_credithour'];
		 				$graduatein=$this->isGraduateInSemester($value['IdStudentRegistration'], $post['semester']);
		 				if ($graduatein) {
		 					$data[$key]['aktif']='L';
		 					$data[$key]['nin']=$graduatein['NoSeriIjazah'];
		 				} else if ($this->isGraduateAfterSemester($value['IdStudentRegistration'], $post['semester']))
		 					$data[$key]['aktif']='A';
		 				else {
		 					unset($data[$key]); $unset="1";
		 				}
		 			} else
		 				$data[$key]['aktif']='A';
		 			//echo var_dump($data);echo "--xxx--";
		 		} else {
		 			$data[$key]['year']='';
		 			$data[$key]['SemesterCountType']='';
		 			$data[$key]['sg_gpa']=0;
		 			$data[$key]['sg_sem_credithour']=0;
		 			$data[$key]['sg_sem_totalpoint']=0;
		 			$data[$key]['sg_univ_gpa']=0;
		 			//get from latest grade status
		 			$lstrSelect = $db ->select()
		 			->from(array('sgd'=>'tbl_student_grade'))
		 			->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=sgd.sg_semesterid',array('year'=>'LEFT(SemesterMainCode,4)','SemesterCountType'))
		 			->where('sgd.sg_IdStudentRegistration=?',$value['IdStudentRegistration'])
		 			->order('sgd.sg_all_cum_credithour DESC')
		 			->order('sgd.sg_all_cgpa DESC');
		 			$grade = $db->fetchRow($lstrSelect);
		 			$data[$key]['sg_all_cum_totalpoint']=$grade['sg_all_cum_totalpoint'];
		 			$data[$key]['sg_all_cgpa']=$grade['sg_all_cgpa'];
		 			$data[$key]['sg_cgpa']=$grade['sg_cgpa'];
		 			$data[$key]['sg_all_cum_credithour']=$grade['sg_all_cum_credithour'];
		 			if ($this->isGraduate($value['IdStudentRegistration'])) { 
		 				
		 				$data[$key]['sg_all_cgpa']=$grade['sg_cgpa'];
		 				$data[$key]['sg_all_cum_credithour']=$grade['sg_cum_credithour'];
		 				if ($this->isGraduateInSemester($value['IdStudentRegistration'],$post['semester'])) 
		 					$data[$key]['aktif']='L';
		 				else {unset($data[$key]); $unset="1";}
		 			} else// if ($ips) {
		 						//act$ipsive without grade only KRS
		 						//$data[$key]['sg_sem_credithour']=$ips['sks_sem'];
		 						//$data[$key]['aktif']='A';
		 					//} else 
		 					{
		 						
		 						//cek Quit
		 						$row=$this->isQuitInSemester($value['IdStudentRegistration'], $post['semester']);
		 						if ($row) {
		 							$data[$key]['dt_status']=$row['dt_status'];
		 							//echo var_dump($row);exit;
		 							if ($row['profileStatus']=='94') $data[$key]['aktif']='K';
		 							else if ($row['profileStatus']=='249') $data[$key]['aktif']='K';
		 							else if ($row['profileStatus']=='95') $data[$key]['aktif']='K';
		 							else if ($row['profileStatus']=='1391') $data[$key]['aktif']='K';
		 							else if ($row['profileStatus']=='253') $data[$key]['aktif']='D';
		 							else if ($row['profileStatus']=='248') $data[$key]['aktif']='C';
		 						} else if ($this->isQuit($value['IdStudentRegistration'],$post['semester'])) {
		 							unset($data[$key]); $unset="1";
		 							} else if ($value['angkatan']>$yearsem) {
		 								//echo var_dump($value);echo $yearsem;exit;
		 								unset($data[$key]); $unset="1";
		 							}
		 						
		 						else $data[$key]['aktif']='N';
		 					}
		 			
		 		}
		 		
		 		if ($unset=='0') {
		 	
			 		//cek approval
			 		if ($this->isInKuliahByNim($post['IdSemester'], $value['nim'],$value['id_sms'])) $data[$key]['status']='Approved';
			 		else $data[$key]['status']='';
			 		//echo var_dump($data);exit;
			 		//cek data mahasiswa_pt
			 		$rows=$this->getMahasiswaPT($value['nim'],$value['id_sms']);
			 		if (!$rows) $data[$key]['status']='mhs_pt kosong';
			 		else if ($rows['id_reg_pdref']=='') $data[$key]['status']='blm registrasi feeder';
		 		 
		 		}
		 	}
		 	//echo var_dump($data);exit;
		 	return $data;
		 }
		 
		 public function approveTrakmList($idstdreg,$idsem,$semlap,$program) {
		 	
		 	$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
		 	$semester=$dbSem->getData($idsem);
		 	$data=$this->getStudentGrade(array('IdStudentRegistration'=>$idstdreg,'IdSemester'=>$idsem,'isapprove'=>'1','semester'=>$semlap,'programme'=>$program));
		 	
			if($data){
		 		//echo var_dump($data);echo "<br>========<br>";
		 		 $student=$data[0];
		 		//echo var_dump($student);exit;
		 		if ($student['sg_all_cgpa']=='') $student['sg_all_cgpa']=0;
		 		if ($student['sg_gpa']=='') $student['sg_gpa']=0;
		 		if ($student['sg_sem_credithour']=='') $student['sg_sem_credithour']=0;
		 		if ($student['sg_all_cum_credithour']=='') $student['sg_all_cum_credithour']=0;
		 		if ($student['aktif']=='L') $ipk=$student['sg_cgpa']; else $ipk=$student['sg_all_cgpa'];
				//get biaya semester
				$dbInvoiceDetail=new Studentfinance_Model_DbTable_InvoiceDetail();
				$biayasmt=$dbInvoiceDetail->getBiayaSemester($idstdreg,$idsem);
				
		 		 $finalData = array(
		 					'id_smt'=>$student['semesterlapor'],//$student['year'].$student['SemesterCountType'],
		 					'ips'=>$student['sg_gpa'],
		 					'sks_smt'=>$student['sg_sem_credithour'],
		 					'ipk'=>$ipk,
		 					'sks_total'=>$student['sg_all_cum_credithour'],
		 					'id_stat_mhs'=>$student['aktif'],
		 		 			'IdSemesterMain'=>$idsem,
		 		 			'id_reg_pdref'=>$student['nim'],
		 		 			'biaya_smt'=>$biayasmt
		 		);
		 		  
		 		// get id_pd
		 		$row=$this->getMahasiswaPT($student['nim'],$student['id_sms']);
		 		//echo var_dump($row);exit;
		 		$idmhspt=$row['id_reg_pd'];
		 		$idmhs=$row['id_pdref'];
		 		
		 		// get from feeder
		 		$filter="trim(nipd)='".$row['nipd']."' and id_sms='".$student['id_sms']."'";
				$response=$Feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
				$idregpd="";
				if (count($response)>0) {
					if ($response['id_reg_pd']!=$row['id_reg_pdref']) {
						//update mahasiswa_pt lokal
						$this->fnupdateData('mahasiswa_pt', array('id_reg_pdref'=>$response['id_reg_pd']), "id_reg_pd='".$idmhspt."'");
						$idregpd=$response['id_reg_pd'];
					} else $idregpd=$row['id_reg_pdref'];
						 
				}  else $idregpd=$row['id_reg_pdref'];
		 		
		 		 
				//echo 'reg='.$idregpd;exit;
		 		if ($idregpd!='') {
		 			$finalData['id_reg_pd']=$idregpd;
		 			$row=$this->isInKuliah($finalData['id_smt'], $idregpd);
		 			//echo var_dump($row);exit;
		 			if ($row) {
		 				//echo var_dump($finalData);exit;
		 				$this->fnupdateData('kuliah_mahasiswa', $finalData, 'id='.$row['id']);
		 			}	else
		 				$this->fnaddData('kuliah_mahasiswa', $finalData);
		 		}
		 		//update lulusan dan status mahasiswa
		 		if ($student['aktif']=='L') {
		 			$dbGrad=new Graduation_Model_DbTable_Graduation();
		 			$grad=$dbGrad->getSmtYudisium($idstdreg);
		 			$smtyudisium=$grad['smtyudisium'];
		 			$row=$dbGrad->getGraduationByIdReg($idstdreg, $idsem);
		 			if ($row) {
		 				if ($row['tgl_bimbingan_mulai']=='0000-00-00' || $row['tgl_bimbingan_mulai']== '1970-01-01') $row['tgl_bimbingan_mulai']=date('Y-m-d',strtotime($semester['SemesterMainStartDate']));
		 				if ($row['tgl_bimbingan_selesai']=='0000-00-00' || $row['tgl_bimbingan_selesai']=='1970-01-01') $row['tgl_bimbingan_selesai']=date('Y-m-d',strtotime($row['tglydsm']));
		 			
			 			$data=array('jalur_skripsi'=>'1',
			 					'judul_skripsi'=>$row['TitleBahasa'],
			 					'bln_awal_bimbingan'=>$row['tgl_bimbingan_mulai'],
			 					'bln_akhir_bimbingan'=>$row['tgl_bimbingan_selesai'],
			 					'sk_yudisium'=>$row['skr'],
			 					'tgl_sk_yudisium'=>$row['tglydsm'],
			 					'ipk'=>$student['sg_cgpa'],
			 					'no_seri_ijazah'=>$row['NoSeriIjazah'],
			 					'id_jns_keluar'=>'1',
			 					'tgl_keluar'=>$row['tglydsm'],
			 					'smt_yudisium'=>$smtyudisium
			 					 //'sert_prof'
			 					
			 			);
			 			$this->fnupdateMhspt($data, $idmhspt);
			 			//$this->fnupdateMhs(array('stat_pd'=>'L'), $idmhs);
		 			}
		 		} else if ($student['aktif']=='K') {
		 			  $data=array('id_jns_keluar'=>'4','tgl_keluar'=>$student['dt_status']);
		 			  $this->fnupdateMhspt($data, $idmhspt);
		 		}
		 		//status mhs
		 		//if ($data['aktif']!='L') {
		 		//	$this->fnupdateMhs(array('stat_pd'=>$data['aktif']), $idmhs);
		 		//}
		 		 
		 	}
		 	
		 }
		 
		 public function getRegisteredSubject($idsemester,$idstd){
		 	$dbsem=new GeneralSetup_Model_DbTable_Semestermaster();
		 	$sem=$dbsem->getData($idsemester);
		 	
		 	$acadyear=$sem['idacadyear'];
		 	$gasal_genap=$sem['SemesterCountType'];
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$lstrSelect1 = $db ->select()
		 	->from(array('sg'=>'tbl_semestermaster'),array('IdSemesterMaster'))
		 	->where('sg.idacadyear=?',$acadyear)
		 	->where('sg.SemesterCountType=?',$gasal_genap);
		 	 
		 	$lstrSelect = $db ->select()
		 	->distinct()
		 	->from(array('sg'=>'tbl_studentregsubjects'),array('sg.IdSubject'))
		 	->where('sg.IdSemesterMain in (?)',$lstrSelect1)
		 	->where('sg.IdStudentRegistration=?',$idstd);
		 	//echo $lstrSelect;exit;
		 	$subjects=$db->fetchAll($lstrSelect);
		 	foreach ($subjects as $key=>$value) {
		 		$idsubject=$value['IdSubject'];
		 		//get highest
		 		$lstrSelect = $db ->select() 
		 		->from(array('sg'=>'tbl_studentregsubjects'),array('IdCourseTaggingGroup','IdSubject','IdStudentRegSubjects','IdStudentRegistration','IdSemesterSubject'=>'IdSemesterMain','grade_name','grade_status','exam_status','grade_point','final_course_mark'))
		 		->joinLeft(array('ctg'=>'tbl_course_tagging_group'),'sg.IdCourseTaggingGroup=ctg.IdCourseTaggingGroup',array('GroupCode','GroupName'))
		 		->join(array('s'=>'tbl_semestermaster'),'sg.IdSemesterMain=s.IdSemesterMaster',array("semester"=>'SemesterMainName'))
		 		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=sg.IdSubject',array('subcode'=>'ShortName','CreditHours','subjectname'=>'BahasaIndonesia'))
		 		->where('sg.IdSemesterMain in (?)',$lstrSelect1)
		 		->where('sg.IdStudentRegistration=?',$idstd)
		 		->where('sg.IdSubject=?',$idsubject)
		 		->order('sg.grade_point DESC');
		 		$row=$db->fetchRow($lstrSelect);
		 		$row['IdSemesterMain']=$idsemester;
		 		$subjects[$key]=$row;
		 	}
		 	//echo var_dump($subjects) ;exit;
		 	return $subjects;
		 	
		 }
		 
		 public function calculateGPA($subjects,$bypass=0){
		 				
		 	//$subjects=$this->getRegisteredSubject($idsemester, $idstd);
		 	$skstotal=0;
		 	$totalpoint=0;
		 	foreach ($subjects as $value) {
		 		if ($value['postpone']=="0" || $bypass=='1') {
			 		$skstotal=$skstotal+$value['CreditHours'];
			 		$totalpoint=$totalpoint+$value['CreditHours']*$value['grade_point'];

		 		}
		 	}
		 	$row=array('GPA'=>$totalpoint/$skstotal,
		 				'sks_sem'=>$skstotal,
		 				'point'=>$totalpoint
		 			);
		 	return $row;	
		 }
		 
		 public function moveLecturerTransaction($course,$idSemester,$idprogram) {
		 	$db=Zend_Db_Table::getDefaultAdapter();
		 	$groupname='';
		 	$dbSchedule=new GeneralSetup_Model_DbTable_CourseGroupSchedule();
		 	$dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		 	$dbStdRegSub=new Examination_Model_DbTable_StudentRegistrationSubject();
		 	//echo var_dump($course);exit;
		 	foreach ($course as $value) {
		 		$idgrp=$value['IdCourseTaggingGroup'];
		 		$sts="1";
		 		if ($value['ProgramCode']=='0400') {
		 			$lstrSelect = $db ->select()
		 			->from(array('l'=>'tbl_landscapeblocksubject'))
		 			->where('l.subjectid=?',$value['IdSubject'])
		 			->where('l.type="2"');
		 			$row=$db->fetchRow($lstrSelect);
		 			if ($row) {
		 				unset($course[$index]);
		 				$sts='0';
		 			}
		 		}
		 		if ($sts=="1") {
		 			//if ($groupname!=$idgrp) {
		 			//check for parent
		 					
			 			$data=array('IdCourseTaggingGroup'=>$idgrp,
			 					'GroupCode'=>$value['GroupCode'],
			 					'GroupName'=>$value['GroupName'],
						 		'IdSemester'=>$idSemester,
						 		'IdSubject'=>$value['IdSubject'],
			 					'IdSemesterSubject'=>$value['IdSemester'],
						 		'rencana'=>$value['rencana'],
						 		'realisasi'=>$value['realisasi'],
			 					'year'=>$value['year'],
			 					'bulan'=>$value['bulan'],
						 		'status'=>'0',
						 		'nOfStudent'=>$value['JmlStd'],
			 					'IdProgram'=>$value['IdProgram']
			 			);
			 			
			 			//if ($value['IdCourse']=="33" || $value['IdCourse']=="52") {echo var_dump($data);exit;}
			 			$row=$this->isInSplitCourse($idgrp,$value['IdSubject'],$value['IdSemester'],$value['IdProgram']);
			 			if (!$row)
			 				$idsplit=$this->fnaddData('tbl_split_coursegroup', $data);
			 			else {
			 			//	if ($row['Edited']=='') 
			 				$idsplit=$row['Id'];
			 				$this->fnupdateData('tbl_split_coursegroup', $data, 'Id='.$idsplit.' and Edited is null');
			 			}
			 			//$groupname=$idgrp;
			 			//$staff=$value['IdStaff'];
			 			
			 		
			 			
			 			
		 			//}
			 		if ($value['IdLecturer']>0) {
			 			$datalecturer=array(
			 					'IdSplitGroup'=>$idsplit,
			 					'IdLecturer'=>$value['IdLecturer'],
			 					'rencana'=>$value['rencana'],
			 					'realisasi'=>$value['realisasi'],
			 			);
			 			$row=$this->isInLecturer($idsplit, $value['IdLecturer']);
			 			if (!$row)
			 				$this->fnaddData('tbl_split_lecturer', $datalecturer);
			 			else
			 				$this->fnupdateData('tbl_split_lecturer', $datalecturer, 'Id='.$row['Id'].' and Edited is null');
			 		}
		 		    //add get team teaching on schedule
		 		    //$schedule=$dbSchedule->getSchedulePdpt($idgrp);
		 		    //echo var_dump($schedule);exit;

		 		  	
			 		$rows=$this->fnGetLecturerSessionPdpt($idgrp);
			 		//echo var_dump($rows);echo var_dump($value);exit;
			 		if ($rows) {
			 			foreach ($rows as $items) {
			 					
			 				if ($items['count']>14) $rencana=$items['count']; else $rencana=14;
			 				$datalecturer=array(
			 						'IdSplitGroup'=>$idsplit,
			 						'IdLecturer'=>$items['lecturer_id'],
			 						'rencana'=>$rencana,
			 						'realisasi'=>$items['count'],
			 				);
			 				$row=$this->isInLecturer($idsplit, $items['lecturer_id']);
			 				if (!$row)
			 					$this->fnaddData('tbl_split_lecturer', $datalecturer);
			 				else
			 					$this->fnupdateData('tbl_split_lecturer', $datalecturer, 'Id='.$row['Id'].' and Edited is null');
			 				
			 			}
			 		}

			 			
			 			//move highest mark
			 			$std=$this->getStudent($idgrp,$value['IdSubject'], $value['IdProgram']);
			 			//echo var_dump($std);exit;
			 			foreach ($std as $stdindi) {
			 		 		$idstd=$stdindi['IdStudentRegistration'];
			 		 		//$idSemester=$stdindi['IdSemesterMain'];
			 		 		$idsubject=$stdindi['IdSubject'];
			 		 		$idlandscape=$stdindi['IdLandscape'];
			 		 		$grade=$dbStdRegSub->getHighestMarkNoStatus($idstd,$idsubject,$idSemester);
			 		 		if ( $grade['exam_status'] !='C') {
			 		 			if ($grade['grade_name']==''||$grade['grade_name']==null) {
			 		 				$gradename='';
			 		 				$gradepoint=0;
			 		 				$examstatus='';
			 		 			}
			 		 			else {
			 		 				$gradename=$grade['grade_name'];
			 		 				$gradepoint=$grade['grade_point'];
			 		 				$examstatus='X';
			 		 			}
			 		 		}
			 		 		else {
			 		 			$gradename=$grade['grade_name'];
			 		 			$gradepoint=$grade['grade_point'];
			 		 			$examstatus=$grade['exam_status'];
			 		 		}
			 		 		
			 		 		if ($grade['final_course_mark']==null) $nilai=0; else $nilai=$grade['final_course_mark'];
			 		 		$datanil=array(
			 		 				'nilai_angka'=>$nilai,
			 		 				'nilai_huruf'=>$gradename,
			 		 				'nilai_indeks'=>$gradepoint,
			 		 			 	'IdStudentRegistration'=>$grade['IdStudentRegistration'],
			 		 				'IdStudentRegSubjects'=>$grade['IdStudentRegSubjects'],
			 		 				'exam_status'=>$examstatus,
			 		 				'grade_status'=>$grade['grade_status'],
			 		 				'IdSplitGroup'=>$idsplit,
			 		 				'status'=>'0'
			 		 		
			 		 		);
			 		 		//cek transnilai
			 		 		$row=$this->isInTransNilai($grade['IdStudentRegistration'],$idSemester,$value['IdSubject']);
			 		 		if (!$row)
			 		 			$this->fnaddData('trans_nilai', $datanil);
			 		 		else {
			 		 			unset($datanil['IdStudentRegistration']);
			 		 			//$datanil['IdStudentRegSubjects'];
			 		 			$this->fnupdateData('trans_nilai', $datanil, 'id='.$row['id']);
			 		 				
			 		 		}
			 			}
		 		}
		 	 
		 	 
		 	}
		 	
		 }
		 
		 public function updateLecturerAtt($course,$idSemester,$idprogram) {
		 	$db=Zend_Db_Table::getDefaultAdapter();
		 	$groupname='';
		 	$dbSchedule=new GeneralSetup_Model_DbTable_CourseGroupSchedule();
		 	//echo var_dump($course);exit;
		 	foreach ($course as $value) {
		 		 
		 		$sts="1";
		 		if ($value['ProgramCode']=='0400') {
		 			$lstrSelect = $db ->select()
		 			->from(array('l'=>'tbl_landscapeblocksubject'))
		 			->where('l.subjectid=?',$value['IdSubject'])
		 			->where('l.type="2"');
		 			$row=$db->fetchRow($lstrSelect);
		 			if ($row) {
		 				unset($course[$index]);
		 				$sts='0';
		 			}
		 		}
		 		if ($sts=="1") {
		 			$idgrp=$value['IdCourseTaggingGroup'];
		 			$row=$this->isInSplitCourse($idgrp,$value['IdSubject'],$value['IdSemester'],$value['IdProgram']);
			 		if ($row) {
			 			
			 			$idsplit=$row['Id'];
			 			$rows=$this->fnGetLecturerSessionPdpt($idgrp);
			 			//echo var_dump($rows);exit;
			 			if ($rows) {
			 				foreach ($rows as $items) {
			 						
			 					if ($items['count']>14) $rencana=$items['count']; else $rencana=14;
			 					$datalecturer=array(
			 							'IdSplitGroup'=>$idsplit,
			 							'IdLecturer'=>$items['lecturer_id'],
			 							'rencana'=>$rencana,
			 							'realisasi'=>$items['count'],
			 					);
			 					$row=$this->isInLecturer($idsplit, $items['lecturer_id']);
			 					if (!$row)
			 						$this->fnaddData('tbl_split_lecturer', $datalecturer);
			 					else
			 						$this->fnupdateData('tbl_split_lecturer', $datalecturer, 'Id='.$row['Id'].' and Edited is null');
			 			
			 				}
			 			} else {
			 				//take coordinator for no attendance record
			 				//if ($items['count']>14) $rencana=$items['count']; else $rencana=14;
			 				$datalecturer=array(
			 						'IdSplitGroup'=>$idsplit,
			 						'IdLecturer'=>$value['IdLecturer'],
			 						'rencana'=>14,
			 						'realisasi'=>1,
			 				);
			 				$row=$this->isInLecturer($idsplit, $value['IdLecturer']);
			 				if (!$row)
			 					$this->fnaddData('tbl_split_lecturer', $datalecturer);
			 				else
			 					$this->fnupdateData('tbl_split_lecturer', $datalecturer, 'Id='.$row['Id'].' and Edited is null');
			 				 
			 			}
			 		}
		 		}
		 	}
		 	 
		 
		 
		 }
		 
		 public function makeHighest($idSemester,$idprogram) {
		 	$db=Zend_Db_Table::getDefaultAdapter();
		 	$select = $db->select()
		 	->from(array("a"=>"trans_nilai"),array('a.*','idtrans'=>'a.id'))
		 	->join(array("cg"=>'tbl_split_coursegroup'),'a.IdSplitgroup=cg.id',array('IdSubject','IdSemesterSubject','IdSemester','GroupCode'))
		 	->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration',array())
		 	->where('cg.IdSemester = ?',$idSemester)
		 	->where('st.IdProgram  = ?',$idprogram)
		 	->order('a.IdStudentRegistration')
		 	->order('cg.IdSubject')
		 	->order('a.nilai_indeks DESC');
		 	//echo $select;exit;
		 	$result = $db->fetchAll($select);
		 	$idsubject='';
		 	$idstd='';
		 	//echo var_dump($result);exit;
		 	foreach ($result as $value) {
		 		if ($value['IdStudentRegistration']==$idstd && $value['IdSubject']==$idsubject ) {
		 	
		 			if ($value['IdSemesterSubject']!=$value['IdSemester']) $this->fndeleteData('trans_nilai','id='.(int)$value['id']);
		 				
		 		} else {
		 			$idsubject=$value['IdSubject'];
		 			$idstd=$value['IdStudentRegistration'];
		 			if ($value['IdSemesterSubject']!=$value['IdSemester']) {
		 				$dataupdate=array('nilai_huruf'=>$value['nilai_huruf'],
		 						'nilai_angka'=>$value['nilai_angka'],
		 						'nilai_indeks'=>$value['nilai_indeks'],
		 						//'exam_status'=>'C',
		 						//'grade_status'=>$value['grade_status']
		 				);
		 				$row=$this->getTransNilaiRow(0, $idstd, $idsubject, 0,$idSemester);
		 				if ($row) {
		 					$idsplit2=$row['idtrans'];
		 					$where='id='.(int)$idsplit2;
		 					//echo $idsplit2."-".$value['id']."=".$idstd;exit;
		 					$this->fnupdateData('trans_nilai',$dataupdate,$where);
		 					//echo var_dump($value);exit;
		 					$this->fndeleteData('trans_nilai','id='.(int)$value['idtrans']);
		 				}
		 			}
		 		}
		 		 
		 	}
		 	
		 }
		 public function fnGetProcessedClass($post){//function to display all  details in list
		 		
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	//echo
		 	$lstrSelect = $db ->select()
		 		
		 	->from(array('ctg'=>'tbl_split_coursegroup'),array('ctg.*','IdGroup'=>'ctg.Id','status_proses'=>'ctg.status'))
		 	->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=ctg.IdSemester',array())
		 	->join(array('s1'=>'tbl_semestermaster'),'s1.IdSemesterMaster=ctg.IdSemesterSubject',array('IdSemester'=>'s1.IdSemesterMaster','s1.SemesterMainName')) 
		  	->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=ctg.IdSubject',array( 'shortname','SubCode','SubjectName'=>'BahasaIndonesia','CreditHours','ch_tutorial','ch_practice','ch_practice_field','ch_sim'))
		 	->join(array('sreg'=>'trans_nilai'),'sreg.IdSplitGroup=ctg.Id', array())
		 	->join(array('sg'=>'tbl_studentregistration'),'sg.IdStudentRegistration=sreg.IdStudentRegistration',array('nim'=>'registrationId'))
		 	->where('sreg.postpone="0"') 
		 	->where('s.SemesterFunctionType not in (2,5)')
		 	->group('ctg.Id');
		 	//->group('lec.IdLecturer');
		 
		 	
		 	if(isset($post['IdSemester']) && !empty($post['IdSemester']) && $post['IdSemester']<>$post['semester'] ){
		 		$lstrSelect = $lstrSelect->where("ctg.IdSemesterSubject = ?",$post['IdSemester']);
		 	}
		 
		 	if(isset($post['semester']) && !empty($post['semester']) ){
		 		$lstrSelect = $lstrSelect->where("ctg.IdSemester = ?",$post['semester']);
		 	}
		 	if(isset($post['programme']) && !empty($post['programme']) ){
		 		$lstrSelect = $lstrSelect->where("sg.IdProgram  = ?",$post['programme']);
		 		$lstrSelect = $lstrSelect->where("ctg.IdProgram  = ?",$post['programme']);
		 		
		 	}
		 
		 	if(isset($post['IdSubject']) && !empty($post['IdSubject']) ){
		 		$lstrSelect = $lstrSelect->where("ctg.IdSubject  = ?",$post['IdSubject']);
		 	}
		 	if(isset($post['IdCourseTaggingGroup']) && !empty($post['IdCourseTaggingGroup']) ){
		 		$lstrSelect = $lstrSelect->where("ctg.IdCourseTaggingGroup  = ?",$post['IdCourseTaggingGroup']);
		 	}
		 
		 	if(isset($post['IdSplitGroup']) && !empty($post['IdSplitGroup']) ){
		 		$lstrSelect = $lstrSelect->where("ctg.Id  = ?",$post['IdSplitGroup']);
		 	}
		 	
		 	if(isset($post['GroupCode']) && !empty($post['GroupCode']) ){
		 		$lstrSelect = $lstrSelect->where("ctg.GroupCode  = ?",$post['GroupCode']);
		 	}
		 	//echo $lstrSelect;exit;
		 	$larrResult = $db->fetchAll($lstrSelect);
		 	//cek # n of student
		 	foreach ($larrResult as $key=>$value) {
		 		$idsplit=$value['IdGroup'];
		 		//get lecturer
		 		$lecturer=$db ->select()
		 		->from(array('lec'=>'tbl_split_lecturer'),array('rencanaLect'=>'lec.rencana','realisasiLect'=>'lec.realisasi','LecEdit'=>'Edited','lec_status'=>'lec.status'))
		 		->joinLeft(array('st'=>'tbl_staffmaster'),'lec.IdLecturer=st.IdStaff',array('IdStaff'=>'st.IdStaff','fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',st.firstname, st.secondname, st.thirdname)"),'Dosen_Code_EPSBED','id_sdm','staff_status'=>'st.status'))
		 		->joinLeft(array('prg1'=>'tbl_program'),'prg1.IdProgram=st.IdDepartment',array('HomeName'=>'prg1.ArabicName', 'HomeCode'=>'prg1.ProgramCode','Strata_code_EPSBED'=>'prg1.Strata_code_EPSBED', 'Program_code_EPSBED'=>'prg1.Program_code_EPSBED','IdHome'=>'prg1.id_sms'))
		 		->where('lec.IdSplitGroup=?',$idsplit);
		 		$lects=$db->fetchAll($lecturer);
		 		$larrResult[$key]['lecturer']=$lects;
		 		//get number of student
		 		$larrResult[$key]['nOfStudent']=$this->getCountTransNilai($idsplit);
		 	}
		 	//echo $lstrSelect;exit;
		 	return $larrResult;
		 }
		 
		 public function fnGetProcessedClassAll($post){//function to display all  details in list
		 	 
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	//echo
		 	$lstrSelect = $db ->select()
		 	 
		 	->from(array('ctg'=>'tbl_split_coursegroup'),array('ctg.*','IdGroup'=>'ctg.Id','status_proses'=>'ctg.status'))
		 	->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=ctg.IdSemester',array())
		 	->join(array('s1'=>'tbl_semestermaster'),'s1.IdSemesterMaster=ctg.IdSemesterSubject',array('IdSemester'=>'s1.IdSemesterMaster','s1.SemesterMainName'))
		 	->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=ctg.IdSubject',array( 'shortname','SubCode','SubjectName'=>'BahasaIndonesia','CreditHours','ch_tutorial','ch_practice','ch_practice_field','ch_sim'))
		 	->join(array('sreg'=>'trans_nilai'),'sreg.IdSplitGroup=ctg.Id', array())
		 	->join(array('sg'=>'tbl_studentregistration'),'sg.IdStudentRegistration=sreg.IdStudentRegistration',array('nim'=>'registrationId'))
		 	->where('sreg.postpone="0"')
		 	->where('s.SemesterFunctionType not in (2,5)')
		 	->group('ctg.Id');
		 	//->group('lec.IdLecturer');
		 		
		 
		 	if(isset($post['IdSemester']) && !empty($post['IdSemester']) ){
		 		$lstrSelect = $lstrSelect->where("ctg.IdSemesterSubject = ?",$post['IdSemester']);
		 	}
		 		
		 	if(isset($post['semester']) && !empty($post['semester']) ){
		 		$lstrSelect = $lstrSelect->where("ctg.IdSemester = ?",$post['semester']);
		 	}
		 	if(isset($post['programme']) && !empty($post['programme']) ){
		 		$lstrSelect = $lstrSelect->where("sg.IdProgram  = ?",$post['programme']);
		 		$lstrSelect = $lstrSelect->where("ctg.IdProgram  = ?",$post['programme']);
		 		 
		 	}
		 		
		 	if(isset($post['IdSubject']) && !empty($post['IdSubject']) ){
		 		$lstrSelect = $lstrSelect->where("ctg.IdSubject  = ?",$post['IdSubject']);
		 	}
		 	if(isset($post['IdCourseTaggingGroup']) && !empty($post['IdCourseTaggingGroup']) ){
		 		$lstrSelect = $lstrSelect->where("ctg.IdCourseTaggingGroup  = ?",$post['IdCourseTaggingGroup']);
		 	}
		 		
		 	if(isset($post['IdSplitGroup']) && !empty($post['IdSplitGroup']) ){
		 		$lstrSelect = $lstrSelect->where("ctg.Id  = ?",$post['IdSplitGroup']);
		 	}
		 	//echo $lstrSelect;exit;
		 	$larrResult = $db->fetchAll($lstrSelect);
		 	//cek # n of student
		 	foreach ($larrResult as $key=>$value) {
		 		$idsplit=$value['IdGroup'];
		 		//get lecturer
		 		$lecturer=$db ->select()
		 		->from(array('lec'=>'tbl_split_lecturer'),array('rencanaLect'=>'lec.rencana','realisasiLect'=>'lec.realisasi','LecEdit'=>'Edited','lec_status'=>'lec.status'))
		 		->joinLeft(array('st'=>'tbl_staffmaster'),'lec.IdLecturer=st.IdStaff',array('IdStaff'=>'st.IdStaff','fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',st.firstname, st.secondname, st.thirdname)"),'Dosen_Code_EPSBED','id_sdm','staff_status'=>'st.status'))
		 		->joinLeft(array('prg1'=>'tbl_program'),'prg1.IdProgram=st.IdDepartment',array('HomeName'=>'prg1.ArabicName', 'HomeCode'=>'prg1.ProgramCode','Strata_code_EPSBED'=>'prg1.Strata_code_EPSBED', 'Program_code_EPSBED'=>'prg1.Program_code_EPSBED','IdHome'=>'prg1.id_sms'))
		 		->where('lec.IdSplitGroup=?',$idsplit);
		 		$lects=$db->fetchAll($lecturer);
		 		$larrResult[$key]['lecturer']=$lects;
		 		//get number of student
		 		$larrResult[$key]['nOfStudent']=$this->getCountTransNilai($idsplit);
		 	}
		 	
		 	return $larrResult;
		 }
		 
		 public function getStudent($idgrp,$idsubject,$idprog) {
		 	 	
		 	
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	//echo
		   	$lstrSelect = $db ->select()	
		 		->from(array('sreg'=>'tbl_studentregsubjects'))
		 		->join(array('st'=>'tbl_studentregistration'),'sreg.IdStudentRegistration=st.IdStudentRegistration',array())
		 		->where('sreg.IdCourseTaggingGroup=?',$idgrp)
		 		->where('sreg.IdSubject=?',$idsubject)
		 		->where('st.IdProgram=?',$idprog);
		 		$larrResult = $db->fetchAll($lstrSelect);		 
		 		return $larrResult;
		 	
		 }
		 
		 public function deleteLecturerTransaction($course,$idprogram) {
		 	foreach ($course as $value) {
		 		$idgrp=$value['IdCourseTaggingGroup'];
		 		$kelas=$this->getSplitKelas($idgrp,$idprogram);
		 		foreach ($kelas as $item) {
		 			$id=$item['Id'];
		 			$this->fndeleteData('trans_nilai', 'IdSplitGroup='.(int)$id);
		 			$this->fndeleteData('tbl_split_lecturer', 'IdSplitGroup='.(int)$id);
		 			$this->fndeleteData('tbl_split_coursegroup', 'Id='.(int)$id);	 		
		 		}
		 		
		 	}
		 	 
		 }
		 
		 public function getStudentDetail($program) {
		 
		 	 
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 
		 	$lstrSelect = $db ->select()
		 	->from(array('st'=>'tbl_studentregistration'),array('registrationid','IdStudentRegistration','id_pd','id_reg_pd'))
		 	->join(array('sp'=>'student_profile'),'st.IdApplication=sp.appl_id',array('appl_id','appl_birth_place'=>'trim(appl_birth_place)','appl_dob'))
		 	->join(array('pr'=>'tbl_program'),'pr.IdProgram=st.IdProgram',array('id_sms'))
		 	->where('st.IdProgram=?',$program)
		 	->where("trim(sp.appl_dob)='' or sp.appl_dob is null or trim(sp.appl_birth_place)=''  or sp.appl_birth_place is null or STR_TO_DATE(appl_dob, '%d,%m,%Y') IS NOT NULL")
		 	->order('st.IdProgram');
		 	$larrResult = $db->fetchAll($lstrSelect);
		 	//echo $lstrSelect;exit;
		 	return $larrResult;
		 
		 }
		 
		 public function isDefer($idstd,$idsem) {
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 		
		 	$lstrSelect = $db ->select()
		 			->from(array('defer'=>'tbl_student_status_history'))
		 			->where('defer.IdSemesterMain=?',$idsem)
		 			->where('defer.IdStudentRegistration=?',$idstd)
		 			->where("defer.profileStatus='248'");
		 	$larrResult = $db->fetchRow($lstrSelect);
		 	if ($larrResult) return true; else return false;
		 }
		 
		 public function isQuit($idstd,$idsemester) {
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	 
		 	$sem=$db->select()
		 	->from('tbl_semestermaster')
			->where('IdSemesterMaster=?',$idsemester);
		 	$semrow=$db->fetchRow($sem);
		 	$date=$semrow['SemesterMainStartDate'];
		 	$lstrSelect = $db ->select()
		 	->from(array('defer'=>'tbl_student_status_history'))
		 	->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=defer.IdSemesterMain')
		 	->where('defer.IdStudentRegistration=?',$idstd)
		 	->where("defer.profileStatus in ('249','94','95','1391')")
		 	->where('s.SemesterMainStartDate<?',$date);
		 	$larrResult = $db->fetchRow($lstrSelect);
		 	if ($larrResult) return true; else return false;
		 }
		 
		 public function isQuitInSemester($idstd,$idsem) {
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	 
		 	$lstrSelect = $db ->select()
		 	->from(array('defer'=>'tbl_student_status_history'))
		 	->where('defer.IdSemesterMain=?',$idsem)
		 	->where('defer.IdStudentRegistration=?',$idstd)
		 	->where("defer.profileStatus in ('249','94','95','253','248','1391')");
		 	$larrResult = $db->fetchRow($lstrSelect);
		 	return $larrResult;
		 }
		 
		 public function getLandscape($idlanscape) {
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	 
		 	$lstrSelect = $db ->select()
		 	->from(array('k'=>'kurikulum'),array())
		 	->join(array('mkk'=>'mata_kuliah_kurikulum'),'k.id=mkk.idkurikulumref',array('IdKuri'=>'k.id','IdMKKuri'=>'mkk.id','mkk.*','IdMK'=>'mkk.id_mk','sksmk'=>'mkk.sks_mk','skstm'=>'mkk.sks_tm','sksprak'=>'mkk.sks_prak','sksprak_lap'=>'mkk.sks_prak_lap','skssim'=>'mkk.sks_sim'))
		 	->join(array('mk'=>'tbl_subjectmaster'),'mkk.IdSubject=mk.IdSubject',array('subject_code'=>'ShortName','subject_name'=>'BahasaIndonesia'))
		 	->joinLeft(array('mt'=>'mata_kuliah'),'mt.kode_mk=mk.shortname and mt.id_sms=k.id_sms')
		 	->where('k.id=?',$idlanscape);
		 	
		 	$larrResult = $db->fetchAll($lstrSelect);
		 	//echo var_dump($larrResult);exit;
		 	return $larrResult;
		 }
		 
		 public function KurikulumApprove($formData,$kuri,$id) {
		 	
		 	$dbMhs=new Reports_Model_DbTable_Mhssetup();
		 	$feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	 
		 	$lstrSelect = $db ->select()
		 	->from(array('k'=>'kurikulum'),array('k.*','idkurikulumsp'=>'id_kurikulum_sp','idsmskuri'=>'k.id_sms'))
		 	->join(array('pr'=>'tbl_program'),'pr.IdProgram=k.IdProgram',array('idsmsprg'=>'pr.id_sms','Program_Code_EPSBED'))
		 	->join(array('mkk'=>'mata_kuliah_kurikulum'),'k.id=mkk.idkurikulumref',array('IdKuri'=>'k.id','IdMKKuri'=>'mkk.id','mkk.*','IdMK'=>'mkk.id_mk','sksmk'=>'mkk.sks_mk','skstm'=>'mkk.sks_tm','sksprak'=>'mkk.sks_prak','sksprak_lap'=>'mkk.sks_prak_lap','skssim'=>'mkk.sks_sim'))
		 	->join(array('mk'=>'tbl_subjectmaster'),'mkk.IdSubject=mk.IdSubject',array('subcode'=>'subcode','subject_code'=>'ShortName','subject_name'=>'BahasaIndonesia'))
		 	//->joinLeft(array('mt'=>'mata_kuliah'),'mt.kode_mk=mk.shortname and mt.id_sms=k.id_sms')
		 	->where('k.id=?',$kuri)
		 	->where('mkk.id=?',$id);
		 	
		 	$row=$db->fetchRow($lstrSelect);
		 	 
		 	if ($row) {
		 		//echo $filter;echo var_dump($row);exit;
		 		if ($row['idsmsprg']=='') {
					//get id_sp from feeder
					//$dbFeeder=new Reports_Model_DbTable_Wsclienttbls();
					 
					$filter="kode_prodi='".$row['Program_code_EPSBED']."' and id_sp='".$idsp."'";
					$response=$feeder->fnGetRecord('sms.raw', $filter);
					//update university
					
					if (count($response)>0) {
						$dbUniv=new GeneralSetup_Model_DbTable_Program();
						$idsms=$response['id_sms'];
						$dbUniv->fnupdateProgram( array('id_sms'=>$idsms), $row['IdProgram']);
					}
				} else $idsms=$row['idsmsprg'];
				
				if ($row['idkurikulumsp']=='' || $row['idkurikulumsp']==null) {
					$filter="id_sms='".$idsms."' and trim(nm_kurikulum_sp)='".trim($row['nm_kurikulum_sp'])."'";
					//echo $filter;
					$response=$feeder->fnGetRecord('kurikulum', $filter);
					//update university
					//echo var_dump($response);exit;
					if (count($response)>0) {
						$idkuri=$response['id_kurikulum_sp'];
						$dbMhs->fnupdateData('kurikulum',array('id_sms'=>$idsms,'id_kurikulum_sp'=>$idkuri), 'id='.$kuri);
					}
					else {
						//inert kurikulum to Feeder
						$data=array(
								'nm_kurikulum_sp'=>$row['nm_kurikulum_sp'],
								'jml_sem_normal'=>$row['jml_sem_normal'],
								'jml_sks_lulus'=>$row['jml_sks_lulus'],
								'jml_sks_wajib'=>$row['jml_sks_wajib'],
								'jml_sks_pilihan'=>$row['jml_sks_pilihan'],
								'id_sms'=>$idsms,
								'id_jenj_didik'=>$row['id_jenj_didik'],
								'id_smt'=>$row['id_smt_berlaku']
						);
						$response=$feeder->insertToFeeder('kurikulum',$data);
						
						if ($response['result']['error_code']==0) {
							//insert to feeder succeed, then update mahasiswa
							$response=$response['result'];
							$idkuri=$response['id_kurikulum_sp'];
							$formData['id_kurikulum_sp']= $idkuri ;
							$formData['id_sms']= $idsms ;
							$formData['error_code']= $response['error_code'] ;
							$formData['error_desc']= $response['error_desc'] ;
							$db->update('kurikulum',$formData,'id='.$kuri);
								
						} else {
							//insert to feeder fail, save  error message
							$response=$response['result'];
							$formData['error_code']= $response['error_code'] ;
							$formData['error_desc']= $response['error_desc'] ;
							$db->update('kurikulum',$formData,'id='.$kuri);
						}
						
					}
				} else $idkuri=$row['idkurikulumsp'];
				//send matakualiah to feeder
				$lstrSelect = $db ->select()
				->from(array('mk'=>'mata_kuliah'))
				->where('mk.kode_mk=?',$row['subject_code'])
				->where('mk.id_sms=?',$idsms);
				//echo $lstrSelect;exit;
				$mk=$db->fetchRow($lstrSelect);
				  
				//echo var_dump($mk); 
				if (!$mk) {
					//no data mata kuliah so get mk from feeder
					$filter="id_sms='".$idsms."' and trim(kode_mk)='".trim($row['subject_code'])."'";
					//$filter="id_sms='3f9b23b9-6724-4c18-aa53-12950c24c9f4'  and kode_mk='GBHH4B'";
					$response=$feeder->fnGetRecord('mata_kuliah.raw', $filter);
					//echo $filter; echo var_dump($response); 
					if (count($response)>0 && $response!='') {
						//echo $response;exit;
						//insert to mata_kuliah in sis;
						 $data=array(id_mk=>$response['id_mk'],
								'id_sms'=>$response['id_sms'],
								'id_jenj_dik'=>$response['id_jenj_didik'],
								'kode_mk'=>$response['kode_mk'],
								'nm_mk'=>$response['nm_mk'],
								'jns_mk'=>$response['jns_mk'],
								'kel_mk'=>$response['kel_mk'],
								'sks_mk'=>$response['sks_mk'],
								'sks_tm'=>$response['sks_tm'],
								'sks_prak_lap'=>$response['sks_prak_lap'],
								'sks_sim'=>$response['sks_sim'],
								'metode_pelaksanaan_kuliah'=>$response['metode_pelaksanaan_kuliah'],
								'a_sap'=>$response['a_sap'],
								'a_silabus'=>$response['a_silabus'],
								'a_bahan_ajar'=>$response['a_bahan_ajar'],
								'acara_prak'=>$response['a_bahan_ajar'],
								'a_diktat'=>$response['a_bahan_ajar'],
								'tgl_mulai_efektif'=>date('Y-m-d',strtotime($response['tgl_mulai_efektif'])),
								'tgl_akhir_efektif'=>date('Y-m-d',strtotime($response['tgl_akhir_efektif']))
						); 
						  
						$this->fnaddData('mata_kuliah', $data);
						$idmk=$response['id_mk'];
					} else {
					
						//insert to feeder
						$dbSubject=new GeneralSetup_Model_DbTable_Subjectmaster();
						$mk=$dbSubject->fngetsubjcodeRow(trim($row['subcode']));
						//echo var_dump($mk);echo "===mk".$value['subject_code'];
						if ($mk['tgl_mulai_efektif']=='0000-00-00' || $mk['tgl_mulai_efektif']==null) $mk['tgl_mulai_efektif']='2015-01-01';
						if ($mk['tgl_akhir_efektif']=='0000-00-00' || $mk['tgl_akhir_efektif']==null) $mk['tgl_akhir_efektif']='2015-01-01';
						$data=array( 'id_sms'=>$idsms,
								'id_jenj_didik'=>$row['id_jenj_didik'],
								'kode_mk'=>$mk['ShortName'],
								'nm_mk'=>$mk['BahasaIndonesia'],
								'jns_mk'=>'A',//sementara ambil wajib
								'kel_mk'=>'A',//sementara ambil A
								'sks_mk'=>(int)$mk['CreditHours'],
								'sks_tm'=>(int)$mk['ch_tutorial'],
								'sks_prak'=>(int)$mk['ch_practice'],
								'sks_prak_lap'=>(int)$mk['ch_practice_field'],
								'sks_sim'=>(int)$mk['ch_sim'],
								'metode_pelaksanaan_kuliah'=>$mk['metode_pelaksanaan_kuliah'],
								'a_sap'=>$mk['a_sap'],
								'a_silabus'=>$mk['a_silabus'],
								'a_bahan_ajar'=>$mk['a_bahan_ajar'],
								'acara_prak'=>$mk['a_bahan_ajar'],
								'a_diktat'=>$mk['a_bahan_ajar'],
								'tgl_mulai_efektif'=>date('Y-m-d',strtotime($mk['tgl_mulai_efektif'])),
								'tgl_akhir_efektif'=>date('Y-m-d',strtotime($mk['tgl_akhir_efektif']))
						);
						$response=$feeder->insertToFeeder('mata_kuliah',$data);
						
						//echo var_dump($response);exit;
						if ($response['result']['error_code']==0) {
							$idmk=$response['result']['id_mk'];
							$filter="trim(kode_mk)='".trim($row['subject_code'])."' and  id_sms='".$idsms."'";
							$response=$feeder->fnGetRecord('mata_kuliah.raw', $filter);
							if (count($response)>0)	 {
								$data=array(id_mk=>$idmk,
										'id_sms'=>$response['id_sms'],
										'id_jenj_dik'=>$response['id_jenj_didik'],
										'kode_mk'=>$response['kode_mk'],
										'nm_mk'=>$response['nm_mk'],
										'sks_mk'=>$response['jns_mk'],
										'kel_mk'=>$response['kel_mk'],
										'sks_mk'=>$response['sks_mk'],
										'sks_tm'=>$response['sks_tm'],
										'sks_prak_lap'=>$response['sks_prak_lap'],
										'sks_sim'=>$response['sks_sim'],
										'metode_pelaksanaan_kuliah'=>$response['metode_pelaksanaan_kuliah'],
										'a_sap'=>$response['a_sap'],
										'a_silabus'=>$response['a_silabus'],
										'a_bahan_ajar'=>$response['a_bahan_ajar'],
										'acara_prak'=>$response['a_bahan_ajar'],
										'a_diktat'=>$response['a_bahan_ajar'],
										'tgl_mulai_efektif'=>date('Y-m-d',strtotime($response['tgl_mulai_efektif'])),
										'tgl_akhir_efektif'=>date('Y-m-d',strtotime($response['tgl_akhir_efektif']))
								);
								$this->fnaddData('mata_kuliah', $data);
							}
						}
					}
				}  else {
						$idmk=$mk['id_mk'];
						
						//if ($idmk=='') {
							//get mk from feeder
						$filter="id_sms='".$idsms."' and trim(kode_mk)='".trim($row['subject_code'])."'";
						//$filter="id_sms='3f9b23b9-6724-4c18-aa53-12950c24c9f4'  and kode_mk='GBHH4B'";
						$response=$feeder->fnGetRecord('mata_kuliah.raw', $filter);
						//echo 'a='.$filter; echo var_dump($response);exit;
						if (count($response)>0 && $response!='') {
							//echo $response;exit;
							//insert to mata_kuliah in sis;
							//$dbSubject=new GeneralSetup_Model_DbTable_Subjectmaster();
							//$mk=$dbSubject->fngetsubjcodeRow(trim($row['subcode']));
							//echo var_dump($mk);echo "===mk".$value['subject_code'];
							//if ($mk['tgl_mulai_efektif']=='0000-00-00' || $mk['tgl_mulai_efektif']==null) $mk['tgl_mulai_efektif']='2015-01-01';
							//if ($mk['tgl_akhir_efektif']=='0000-00-00' || $mk['tgl_akhir_efektif']==null) $mk['tgl_akhir_efektif']='2015-01-01';
							$data=array( 'id_sms'=>$idsms,
									'id_jenj_didik'=>$row['id_jenj_didik'],
									'kode_mk'=>$row['subject_code'],
									'nm_mk'=>$row['subject_name'],
									'jns_mk'=>'A',//sementara ambil wajib
									'kel_mk'=>'A',//sementara ambil A
									'sks_mk'=>$row['sks_mk'],
									'sks_tm'=>$row['sks_tm'],
									'sks_prak'=>$row['sks_prak'],
									'sks_prak_lap'=>$row['sks_prak_lap'],
									'sks_sim'=>$row['sks_sim'],
									'metode_pelaksanaan_kuliah'=>$row['metode_pelaksanaan_kuliah'],
									'a_sap'=>$row['a_sap'],
									'a_silabus'=>$row['a_silabus'],
									'a_bahan_ajar'=>$row['a_bahan_ajar'],
									'acara_prak'=>$row['a_bahan_ajar'],
									'a_diktat'=>$row['a_bahan_ajar'],
									'tgl_mulai_efektif'=>date('Y-m-d',strtotime($row['tgl_mulai_efektif'])),
									'tgl_akhir_efektif'=>date('Y-m-d',strtotime($row['tgl_akhir_efektif']))
								);
							//echo var_dump($data);exit;
							
							$res=$feeder->updateToFeeder('mata_kuliah', array('id_mk'=>$response['id_mk']), $data);
							//echo var_dump($data);echo var_dump($res); 
							$this->fnupdateData('mata_kuliah', array('id_mk'=>$response['id_mk']),'Id='.$mk['id']);
							$idmk=$response['id_mk'];
						} else {
							/*
							//$idmk='';
							//get all subjects not depend on sms
							 $filter="trim(kode_mk)='".trim($row['subject_code'])."'";
							//$filter="id_sms='3f9b23b9-6724-4c18-aa53-12950c24c9f4'  and kode_mk='GBHH4B'";
							$mkall=$feeder->fnGetRecordSet('mata_kuliah.raw', $filter,100,0);
							foreach ($mkall as $mkr) {
								//cek for valid landscape on feeder
								$idmk=$mkr['id_mk'];
								$filter="id_kurikulum_sp='".$idkuri."' and id_mk='".$idmk."'";
								//$filter="id_sms='3f9b23b9-6724-4c18-aa53-12950c24c9f4'  and kode_mk='GBHH4B'";
								$mkkuri=$feeder->fnGetRecord('mata_kuliah_kurikulum.raw', $filter);
								if (count($response)>0) {
									$idmk=$mkkuri['id_mk'];
									break;
								}
								
									 
							} 
							*/
							//echo 'mk='.var_dump($idmk);exit;
							if ($idmk=='') {
								//insert to feeder
								$dbSubject=new GeneralSetup_Model_DbTable_Subjectmaster();
								$mks=$dbSubject->fngetsubjcodeRow(trim($row['subcode']));
								//echo var_dump($mk);echo "===mk".$value['subject_code'];
								if ($mks['tgl_mulai_efektif']=='0000-00-00' || $mks['tgl_mulai_efektif']==null) $mks['tgl_mulai_efektif']='2015-01-01';
								if ($mks['tgl_akhir_efektif']=='0000-00-00' || $mks['tgl_akhir_efektif']==null) $mks['tgl_akhir_efektif']='2015-01-01';
								$data=array( 'id_sms'=>$idsms,
										'id_jenj_didik'=>$row['id_jenj_didik'],
										'kode_mk'=>$mks['ShortName'],
										'nm_mk'=>$mks['BahasaIndonesia'],
										'jns_mk'=>'A',//sementara ambil wajib
										'kel_mk'=>'A',//sementara ambil A
										'sks_mk'=>(int)$mks['CreditHours'],
										'sks_tm'=>(int)$mks['ch_tutorial'],
										'sks_prak'=>(int)$mks['ch_practice'],
										'sks_prak_lap'=>(int)$mks['ch_practice_field'],
										'sks_sim'=>(int)$mks['ch_sim'],
										'metode_pelaksanaan_kuliah'=>$mks['metode_pelaksanaan_kuliah'],
										'a_sap'=>$mks['a_sap'],
										'a_silabus'=>$mks['a_silabus'],
										'a_bahan_ajar'=>$mks['a_bahan_ajar'],
										'acara_prak'=>$mks['a_bahan_ajar'],
										'a_diktat'=>$mks['a_bahan_ajar'],
										'tgl_mulai_efektif'=>date('Y-m-d',strtotime($mks['tgl_mulai_efektif'])),
										'tgl_akhir_efektif'=>date('Y-m-d',strtotime($mks['tgl_akhir_efektif']))
								);
								$response=$feeder->insertToFeeder('mata_kuliah',$data);
								//echo var_dump($response);exit;
								if ($response['result']['error_code']==0) {
									$idmk=$response['result']['id_mk'];
								} 
							} /* else {
								//echo var_dump($data);
								
								$response=$feeder->insertToFeeder('mata_kuliah',$data);
								//echo var_dump($response);exit;
								if ($response['result']['error_code']==0) {
									$idmk=$response['result']['id_mk'];
								}
								$this->fnupdateData('mata_kuliah', array('id_mk'=>$idmk),'Id='.$mk['id']);
									
							} */
						}
					//}
				}
				//kurikulum mk
				 
				if ($idmk!='' && $idkuri!='') {
					$data=array(
							'id_kurikulum_sp'=>$idkuri,
							'id_mk'=>$idmk,
							'smt'=>$row['smt'],
							'sks_mk'=>$row['sksmk'],
							'sks_tm'=>$row['skstm'],
							'sks_prak'=>$row['sksprak'],
							'sks_prak_lap'=>$row['sksprak_lap'],
							'sks_sim'=>$row['sks_sim'],
							'a_wajib'=>$row['a_wajib']
					);
					//cek feeder
					$filter="id_mk='".$idmk."' and id_kurikulum_sp='".$idkuri."'";
					$response=$feeder->fnGetRecord('mata_kuliah_kurikulum.raw', $filter);
					//echo $filter;echo var_dump($data);exit;
					if (count($response)==0 || $response=='')
					{
						//echo var_dump($data);
						$response=$feeder->insertToFeeder('mata_kuliah_kurikulum',$data);
						//echo var_dump($response);exit;
						if ($response['result']['error_code']==0) {
							$formData['id_kurikulum_sp']=$idkuri;
							$formData['id_mk']=$idmk;
							$this->fnupdateData('mata_kuliah_kurikulum', $formData, 'id='.$id);
						} else {
							$response=$response['result'];
							unset($formData['date_of_approval']);
							unset($formData['status']);
							$formData['error_code']= $response['error_code'] ;
							$formData['error_desc']= $response['error_desc'] ;
							$db->update('mata_kuliah_kurikulum',$formData,'id='.$id);
						
						}
					} else {
						unset($data['id_kurikulum_sp']);
						unset($data['id_mk']); 
						$res=$feeder->updateToFeeder('mata_kuliah_kurikulum',array('id_mk'=>$response['id_mk'],'id_kurikulum_sp'=>$response['id_kurikulum_sp']), $data);
						//echo var_dump($data);echo var_dump($res); exit
						$formData['id_kurikulum_sp']=$response['id_kurikulum_sp'];
						$formData['id_mk']=$response['id_mk'];
						$this->fnupdateData('mata_kuliah_kurikulum', $formData, 'id='.$id);
					}
				}
				
		 	}
			
		 	 
		 	
		 }
		 
		 public function KurikulumDelete($kuri,$id) {
		 
		 	$dbMhs=new Reports_Model_DbTable_Mhssetup();
		 	$feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$data=array('id_kurikulum_sp'=>$kuri,'id_mk'=>$id);
		 	//  echo var_dump($data);
		 	
		 		$response=$feeder->deleteToFeeder('mata_kuliah_kurikulum',$data);
		 	 
			 	if ($response['result']['error_code']==0) {
					$formData['id_kurikulum_sp']=null;
					$formData['id_mk']=null;
					$formData['status']=null;
					$formData['date_of_approval']=null;
					$this->fnupdateData('mata_kuliah_kurikulum', $formData, "id_kurikulum_sp='".$kuri."' and id_mk='".$id."'");
				} else { 
					$formData['error_code']= $response['error_code'] ;
					$formData['error_desc']= $response['error_desc'] ;
					$this->fnupdateData('mata_kuliah_kurikulum', $formData, "id_kurikulum_sp='".$kuri."' and id_mk='".$id."'");
				}
		 	 
		 		
		 	
		 }
		 
		 public function trakmwsDelete($id) {
		 		
		 	$dbMhs=new Reports_Model_DbTable_Mhssetup();
		 	$feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$select=$db->select()
		 	->from(array('km'=>'kuliah_mahasiswa'))
		 	->where('km.id=?',$id);
		 	$row=$db->fetchRow($select);
		 	if ($row) {
		 		//if ($row['error_code']==0) {
			 		$data=array('id_smt'=>$row[id_smt],'id_reg_pd'=>$row['id_reg_pd']);
				 	//  echo var_dump($data);
				 	$response=$feeder->deleteToFeeder('kuliah_mahasiswa',$data);
				 	//echo var_dump($response);exit;
				 	if ($response['result']['error_code']==0) {
				 		 
				 		$this->fndeleteData('kuliah_mahasiswa', "id=".$id);
				 	} else {
				 		$formData['error_code']= $response['error_code'] ;
				 		$formData['error_desc']= $response['error_desc'] ;
				 		$this->fnupdateData('kuliah_mahasiswa', $formData, "id=".$id);
				 	}
// 		 		} else {
// 		 			$this->fndeleteData('kuliah_mahasiswa', "id=".$id);
// 		 		}
		 	}
		 }
		 
		 public function getDataFromFeeder() {
		 	$feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$filter="sks_total='0' and sks_smt='0' and id_stat_mhs='A' and id_smt >='20091'";
		 	$offset=0;
		 	$status='0';
		 	while ( $status=='0' )  {
		 		$response=$feeder->fnGetRecordSet('kuliah_mahasiswa', $filter,100,$offset);
		 		
		 		//echo var_dump($response);exit;
		 		if (count($response)>0) {
		 			 
		 			foreach ($response as $value) {

			 			$filter="id_reg_pd='".$value['id_reg_pd']."' and id_smt='".$value['id_smt']."'";
						
			 			$response=$feeder->fnGetRecord('nilai.raw', $filter);
			 			
			 			if (count($response)>0) {
			 				//echo var_dump($response);exit;
				 			$data=array('id_smt'=>$value['id_smt'],
				 						'id_reg_pd'=>$value['id_reg_pd'],
				 						'ips'=>$value['ips'],
				 						 'sks_smt'=>$value['sks_smt'],
				 						 'ipk'=>$value['ipk'],
				 						 'sks_total'=>$value['sks_total'],
				 						 'id_stat_mhs'=>$value['id_stat_mhs']);
				 			$row=$this->isInKuliah($value['id_smt'], $value['id_reg_pd']);
				 			if ($row) {
				 				$data['error_code']='0';
				 				$this->fnupdateData('kuliah_mahasiswa', $data, 'id='.$row['id']);
				 			} else
				 				$this->fnaddData('kuliah_mahasiswa', $data);
			 			}
		 			
		 			}
		 			$offset=$offset+100;
		 		
		 		} else $status='1';
		 	//echo $offset;echo var_dump($response);exit;
		 	}
		 	
		 	
		 }
		 
		 public function getKelasFromFeeder() {
		 	$feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$select = $db->select()
		 	->from(array('km'=>'kuliah_mahasiswa'),array('id_smt'))
		 	->where('km.sks_total=0 and km.sks_smt=0 and error_code="0"')
		 	->group('km.id_smt');
		 	$row=$db->fetchAll($select);
		 	//echo var_dump($row);exit;
		 	foreach ($row as $value)  {
		 		$status="0";
		 		$offset=0;
		 		while ($status=="0") {
		 			 
		 		$response=$feeder->fnGetRecordSet('kelas_kuliah.raw', "id_smt='".$value['id_smt']."'",100,$offset);
		 		//echo var_dump($response);exit;
		 		if (count($response)>0) {
		 		 	
		 			foreach ($response as $value) {
		 				 
		 				$data=array(
		 						'id_smt'=>$value['id_smt'],
		 						'id_kls'=>$value['id_kls'],
		 						'id_mk'=>$value['id_mk'],
		 						'id_sms'=>$value['id_sms'],
		 						'nm_kls'=>$value['nm_kls'],
		 						'sks_mk'=>$value['sks_mk'],
		 						'sks_tm'=>$value['sks_tm'],
		 						'sks_prak_lap'=>$value['sks_prak_lap'],
		 						'sks_sim'=>$value['sks_sim']
		 				);
		 				$row=$this->isInKelasRaw($value['id_smt'], $value['id_kls'], $value['id_mk']);
		 				if (!$row)
		 					$this->fnaddData('kelas_kuliah', $data);
		 			}	 					
		 			 
		 		
		 			$offset=$offset+100;
		 			 
		 			} else $status='1';
		 		}
		 	}
		 }
		 
		 public function getArrayKelasFromFeederbySmt($idsmt,$idsms=null,$idprogram,$idsubject=null) {
		 	$feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$lstrSelect = $db ->select()
		 	->from(array('s'=>'tbl_semestermaster'),array('year'=>'CONCAT(LEFT(SemesterMainCode,4),SemesterCountType)'))
		 	->where('s.IdSemesterMaster=?',$idsmt);
		 	$smt=$db->fetchRow($lstrSelect);
		 	$idsmt=$smt['year'];
		 	$filter="id_smt='".$idsmt."' and id_sms='".$idsms."'";
		 	//echo $filter;exit;
		 	if ($idsubject!=null) {
		 		$lstrSelect = $db ->select()
		 		->from(array('s'=>'tbl_subjectmaster'))
		 		->where('s.IdSubject=?',$idsubject);
		 		$sub=$db->fetchRow($lstrSelect);
		 		$sub=$feeder->fnGetRecord('mata_kuliah.raw',"id_sms='".$idsms."' and trim(kode_mk)='".$sub['ShortName']."'" );
		 		if ($sub) $filter=$filter." and id_mk='".$sub['id_mk']."'";
		 	}
		 	$status="0";
		 	$offset=0;
		 	$i=0;
		 	$kelas=array();
		 	while ($status=="0") {
		 		//echo $filter;exit;
		 		$response=$feeder->fnGetRecordSet('kelas_kuliah.raw', $filter,100,$offset);
		 		//echo var_dump($response);exit;
		 		
		 		if (count($response)>0) {
		 
		 			foreach ($response as $value) {
		 					
		 				$data=array(
		 						'id_smt'=>$value['id_smt'],
		 						'id_kls'=>$value['id_kls'],
		 						'id_mk'=>$value['id_mk'],
		 						'id_sms'=>$value['id_sms'],
		 						'nm_kls'=>$value['nm_kls'],
		 						'sks_mk'=>$value['sks_mk'],
		 						'sks_tm'=>$value['sks_tm'],
		 						'sks_prak_lap'=>$value['sks_prak_lap'],
		 						'sks_sim'=>$value['sks_sim']
		 				);
		 				$kelas[$i]=$data;
		 				//get student
		 				$nilai=$feeder->fnGetRecordSet('nilai.raw',"id_kls='".$value['id_kls']."'",1000,0);
		 				if (count($nilai)>0) {
		 					foreach ($nilai as $idx=>$std) {
		 						$lstrSelect = $db ->select()
		 						->from(array('s'=>'mahasiswa_pt'))
		 						->join(array('p'=>'tbl_program'),'s.id_sms=p.id_sms')
		 						->where('s.id_reg_pdref=?',$std['id_reg_pd'])
		 						->where('p.IdProgram <> ?',$idprogram)
		 						;
		 						//echo $lstrSelect;exit;
		 						$regstd=$db->fetchRow($lstrSelect);
		 						if ($regstd) {
		 							//echo var_dump($regstd);exit;
		 							$nilai[$idx]['programcode']=$regstd['ProgramCode'];
		 							$nilai[$idx]['nipd']=$regstd['nipd'];
		 							//$nilai[$idx]['id_reg_pd']=$regstd['id_reg_pdref'];
		 						} else unset($nilai[$idx]);
		 						
		 					}
		 					//echo var_dump($nilai);exit;
		 					$kelas[$i]['mhs']=$nilai;
		 				} else $kelas[$i]['mhs']=array();
		 				$i++;
		 				 
		 			}
		 
		 			
		 			$offset=$offset+100;
		 
		 		} else $status='1';
		 	}
		 	//echo var_dump($kelas);exit;
		 	return $kelas;
		 }
		 	
		 public function getKelasFromFeederbySmt($idsmt) {
		 	$feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$lstrSelect = $db ->select()
		 	->from(array('s'=>'tbl_semestermaster'),array('year'=>'CONCAT(LEFT(SemesterMainCode,4),SemesterCountType)'))
		 	->where('s.IdSemesterMaster=?',$idsmt);
		 	$smt=$db->fetchRow($lstrSelect);
		 	$idsmt=$smt['year'];
		 	$filter="id_smt='".$idsmt."'"; 
		 	$status="0";
		 	$offset=0;
		 		while ($status=="0") {
		 		 	
		 			$response=$feeder->fnGetRecordSet('kelas_kuliah.raw', $filter,100,$offset);
		 			//echo var_dump($response);exit;
		 			if (count($response)>0) {
		 					
		 				foreach ($response as $value) {
		 
		 					$data=array(
		 							'id_smt'=>$value['id_smt'],
		 							'id_kls'=>$value['id_kls'],
		 							'id_mk'=>$value['id_mk'],
		 							'id_sms'=>$value['id_sms'],
		 							'nm_kls'=>$value['nm_kls'],
		 							'sks_mk'=>$value['sks_mk'],
		 							'sks_tm'=>$value['sks_tm'],
		 							'sks_prak_lap'=>$value['sks_prak_lap'],
		 							'sks_sim'=>$value['sks_sim']
		 					);
		 					//$row=$this->isInKelasRaw($value['id_smt'], $value['id_kls'], $value['id_mk']);
		 					//if (!$row)
		 						$this->fnaddData('kelas_kuliah_tmp', $data);
		 				}
		 					
		 				 
		 				$offset=$offset+100;
		 					
		 			} else $status='1';
		 		}
		 
		 	
		 }
		 
		 public function getNilaiFromFeeder() {
		 	$feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$select = $db->select()
		 	->from(array('km'=>'kuliah_mahasiswa'),array('id','id_smt','id_reg_pd'))
		 	->where('km.sks_total=0 and km.sks_smt=0 and km.id_stat_mhs="A" and id_smt!="20142" and id_reg_pd!=""')
		 	//->where('km.id_reg_pd not in (select id_reg_pd from nilai)')
		 	->where('km.error_code="" ');
		 	$row=$db->fetchAll($select);
		 	//echo var_dump($row);exit;
		 	foreach ($row as $value)  {
		 		//echo var_dump($value);exit;
		 		$idreg=$value['id_reg_pd'];
		 		$idsmt=$value['id_smt'];
		 		$response=$feeder->fnGetRecordSet('nilai.raw', "id_reg_pd='".$idreg."'",100,0);
		 			//echo 'id_kls="'.$idkls."' and id_reg_pd='".$idreg."'";exit;
		 			if (count($response)>0) {
			 			foreach ($response as $item) {		
			 					$data=array(
			 							 
			 							'id_kls'=>$item['id_kls'],
			 							'id_reg_pd'=>$item['id_reg_pd'],
			 							'asal_data'=>"9",
			 							'nilai_angka'=>$item['nilai_angka'],
			 							'nilai_huruf'=>$item['nilai_huruf'],
			 							'nilai_indeks'=>$item['nilai_indeks']
			 					);
			 					$row=$this->isInNilaiRaw($item['id_kls'], $item['id_reg_pd']);
			 					if (!$row)
			 						$this->fnaddData('nilai', $data);
			 			}
			 			$this->fnupdateData('kuliah_mahasiswa', array('error_code'=>'10'), "id=".$value['id']);
		 					
		 			} else {
		 				$this->fnupdateData('kuliah_mahasiswa', array('error_code'=>'100'), "id=".$value['id']);
		 			}
		 	}
		 	
		}
		public function getNilaiFromFeederBySmt($idsmt) {
			$feeder=new Reports_Model_DbTable_Wsclienttbls();
			$db = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $db ->select()
			->from(array('s'=>'tbl_semestermaster'),array('year'=>'CONCAT(LEFT(SemesterMainCode,4),SemesterCountType)'))
			->where('s.IdSemesterMaster=?',$idsmt);
			$smt=$db->fetchRow($lstrSelect);
			$idsmt=$smt['year'];
			$select = $db->select()
			->from(array('km'=>'kelas_kuliah_tmp'),array('id_kls'))
			->where('km.error_code is null')
			->where('km.id_smt=?',$idsmt);
			//echo $select;
			$row=$db->fetchAll($select);
			//echo var_dump($row);exit;
			
			foreach ($row as $value)  {
				//echo var_dump($value);exit;
				$idkls=$value['id_kls']; 
				
				$offset=0;
				$status="0";
				while ($status=="0") {
					$response=$feeder->fnGetRecordSet('nilai.raw', "id_kls='".$idkls."'",100,$offset);
					//echo 'id_kls="'.$idkls."' and id_reg_pd='".$idreg."'";exit;
					if (count($response)>0 && $response!='') {
						foreach ($response as $item) {
							$data=array(
			
									'id_kls'=>$item['id_kls'],
									'id_reg_pd'=>$item['id_reg_pd'],
									'asal_data'=>"9",
									'nilai_angka'=>$item['nilai_angka'],
									'nilai_huruf'=>$item['nilai_huruf'],
									'nilai_indeks'=>$item['nilai_indeks']
							);
							//$row=$this->isInNilaiRaw($item['id_kls'], $item['id_reg_pd']);
							//if (!$row)
								$this->fnaddData('nilai_tmp', $data);
						}
						//$this->fnupdateData('kuliah_mahasiswa', array('error_code'=>'10'), "id=".$value['id']);
			
						$offset=$offset+100;
						
					} else {
						//echo 'ok';exit;
						$status="1";
					}
					
				}
				$this->fnupdateData('kelas_kuliah_tmp', array('error_code'=>'100'), "id_kls='".$idkls."'");
				
			}
		
		}
		 
		public function getNilaiFromSIS($prg=null) {
			ini_set('session.gc_maxlifetime', 60 * 60 * 6);
			$feeder=new Reports_Model_DbTable_Wsclienttbls();
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('km'=>'kuliah_mahasiswa'),array('id','id_reg_pd','id_smt'))
			->joinLeft(array('st'=>'tbl_studentregistration'),'st.id_reg_pd=km.id_reg_pd')
			//->join(array('pr'=>'tbl_program'),'pr.IdProgram=st.IdProgram')
			->where('km.error_code="100" and id_reg_pdref=""');
			if ($prg!=null) $select->where('st.IdProgram=?',$prg);
		
			$row=$db->fetchAll($select);
			//echo $select;echo var_dump($row);exit;
			foreach ($row as $item) {
				$idreg=$item['id_reg_pd'];
				//$idsmt=$item['id_smt'];
				$response=$feeder->fnGetRecord('mahasiswa_pt.raw', "id_reg_pd='".$idreg."'");
				//echo $idreg;echo var_dump($response);exit;
				if (count($response)>0)  {
					$regis=trim($response['nipd']);
					$select = $db->select()
					->from(array('ctg'=>'tbl_course_tagging_group'),array('GroupCode'))
					->join(array('srs'=>'tbl_studentregsubjects'),'ctg.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array('nilai_huruf'=>'grade_name','nilai_indeks'=>'grade_point','nilai_angka'=>'final_course_mark'))
					->join(array('st'=>'tbl_studentregistration'),'st.idstudentregistration=srs.idstudentregistration')
					->join(array('pr'=>'tbl_program'),'st.IdProgram=pr.IdProgram',array('pr.IdProgram','Program_Code_EPSBED','id_sms','jenjang'=>'id_jenjang_pendidikan'))
					->join(array('sb'=>'tbl_subjectmaster'),'sb.IdSubject=srs.IdSubject',array('subjectcode'=>'ShortName','sks_mk'=>'CreditHours','sks_tm'=>'CH_tutorial','sks_prak'=>'CH_practice','sks_prak_lap'=>'ch_practice_field','sks_sim'=>'ch_sim'))
					->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=ctg.IdSemester',array('idsmt'=>'CONCAT(LEFT(semestermaincode,4),SemesterCountType)'))
					->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=pr.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
					->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED','id_sp'))
					
					->where('st.registrationid=?',$regis);
					
					$nilais=$db->fetchAll($select);
					//echo var_dump($nilais);exit;
					if (count($nilais)>0) {
						foreach ($nilais as $value) {
							$nmklas=$value['GroupCode'];
							if ($nmklas!='01'||$nmklas!='02'||$nmklas!='03'||$nmklas!='04'||$nmklas!='05'||$nmklas!='06'
											||$nmklas!='07'||$nmklas!='08'||$nmklas!='09'||$nmklas!='10') {
								if ($nmklas!='A') $nmklas='01';
								else if ($nmklas!='B') $nmklas='02';
								else if ($nmklas!='C') $nmklas='03';
								else if ($nmklas!='D') $nmklas='04';
								else if ($nmklas!='E') $nmklas='05';
								else if ($nmklas!='F') $nmklas='06';
								else if ($nmklas!='G') $nmklas='07';
								else if ($nmklas!='H') $nmklas='08';
								else if ($nmklas!='I') $nmklas='09';
								else if ($nmklas!='J') $nmklas='10';
								else $nmklas='01';
							}
							$idsmt=$value['idsmt'];
							$idsms=$value['id_sms'];
							$idsp=$value['id_sp'];
							if ($idsms=='') {

								$filter="kode_prodi='".$value['Program_Code_EPSBED']."' and id_sp='".$idsp."'";
								$response=$feeder->fnGetRecord('sms.raw', $filter);
								//update university
								//echo var_dump($response);exit;	
								if (count($response)>0) {
									$dbUniv=new GeneralSetup_Model_DbTable_Program();
									$idsms=$response['id_sms'];
									$dbUniv->fnupdateProgram( array('id_sms'=>$idsms), $value['IdProgram']);
								}
							}
							$subcode=trim($value['subjectcode']);
							$mk=$this->isInMKbyCode($idsms, $subcode);
							//echo var_dump($mk);exit;
							if (!$mk) {
								$filter="id_sms='".$idsms."' and trim(kode_mk)='".$subcode."'";
								$mk=$feeder->fnGetRecord('mata_kuliah', $filter);
								//echo $filter;exit;
								if (count($mk)==0) {
									$dbSubject=new GeneralSetup_Model_DbTable_Subjectmaster();
									$mk=$dbSubject->fngetsubjcodeRow(trim($value['subjectcode']));
									//echo var_dump($mk);echo "===mk".$value['subject_code'];
									if ($mk['tgl_mulai_efektif']=='0000-00-00' || $mk['tgl_mulai_efektif']==null) $mk['tgl_mulai_efektif']='2015-01-01';
									if ($mk['tgl_akhir_efektif']=='0000-00-00' || $mk['tgl_akhir_efektif']==null) $mk['tgl_akhir_efektif']='2015-01-01';
									$data=array( 'id_sms'=>$idsms,
											'id_jenj_didik'=>$value['jenjang'],
											'kode_mk'=>$mk['ShortName'],
											'nm_mk'=>$mk['BahasaIndonesia'],
											'jns_mk'=>'A',//sementara ambil wajib
											'kel_mk'=>'A',//sementara ambil A
											'sks_mk'=>(int)$mk['CreditHours'],
											'sks_tm'=>(int)$mk['ch_tutorial'],
											'sks_prak'=>(int)$mk['ch_practice'],
											'sks_prak_lap'=>(int)$mk['ch_practice_field'],
											'sks_sim'=>(int)$mk['ch_sim'],
											'metode_pelaksanaan_kuliah'=>$mk['metode_pelaksanaan_kuliah'],
											'a_sap'=>$mk['a_sap'],
											'a_silabus'=>$mk['a_silabus'],
											'a_bahan_ajar'=>$mk['a_bahan_ajar'],
											'acara_prak'=>$mk['a_bahan_ajar'],
											'a_diktat'=>$mk['a_bahan_ajar'],
											'tgl_mulai_efektif'=>date('Y-m-d',strtotime($mk['tgl_mulai_efektif'])),
											'tgl_akhir_efektif'=>date('Y-m-d',strtotime($mk['tgl_akhir_efektif']))
									);
									$response=$feeder->insertToFeeder('mata_kuliah',$data);
									//echo var_dump($response);exit;
									if ($response['result']['error_code']==0) {
										$idmk=$response['result']['id_mk'];
										$filter="trim(kode_mk)='".trim($value['subject_code'])."' and  id_sms='".$idsms."'";
										$response=$feeder->fnGetRecord('mata_kuliah.raw', $filter);
										if (count($response)>0)	 {
											$data=array('id_mk'=>$idmk,
													'id_sms'=>$response['id_sms'],
													'id_jenj_dik'=>$response['id_jenj_didik'],
													'kode_mk'=>$response['kode_mk'],
													'nm_mk'=>$response['nm_mk'],
													'jns_mk'=>$response['jns_mk'],
													'kel_mk'=>$response['kel_mk'],
													'sks_mk'=>$response['sks_mk'],
													'sks_tm'=>$response['sks_tm'],
													'sks_prak_lap'=>$response['sks_prak_lap'],
													'sks_sim'=>$response['sks_sim']
											);
											$this->fnaddData('mata_kuliah', $data);
										}
									}
								} else $idmk=$mk['id_mk'];
							} else $idmk=$mk['id_mk'];
							//cek kelas
							//echo $value['subjectcode']; echo 'sms:'.$idsms;echo 'smt:'.$idsmt; echo 'mk:'.$idmk;exit;
							if ($idsms!='' && $idmk!='' && $idsmt!='') {
								$row=$this->isInKelasbyKode($idsms, $idsmt, $idmk, $nmklas);
								if (!$row) {
									//add kelas
									$filter="id_sms='".$idsms."' and id_smt='".$idsmt."' and id_mk='".$idmk."' and nm_kls='".$nmklas."'";
									$response=$feeder->fnGetRecord('kelas_kuliah.raw', $filter);
									if (count($response)>0) {
										$idkls=$response['id_kls'];
										$temp['id_kls'] = $response['id_kls'];
										$temp['id_smt'] = $response['id_smt'];
										$temp['id_sms'] = $response['id_sms'];
										$temp['id_mk'] = $response['id_mk'];
										$temp['id_mkref'] = $response['kode_mk'];
										$temp['nm_kls'] = $response['nm_kls'];
										$temp['sks_mk'] = $response['sks_mk'];
										$temp['sks_tm'] = $response['sks_tm'];
										$temp['sks_prak'] = $response['sks_prak'];
										$this->fnaddData('kelas_kuliah', $temp);
									} else {
										$temp1['id_smt'] = $idsmt;
										$temp1['id_sms'] = $idsms;
										$temp1['id_mk'] = $idmk;
										$temp1['nm_kls'] = $nmklas;
										$temp1['sks_mk'] = (int)$value['sks_mk'];
										$temp1['sks_tm'] = (int)$value['sks_tm'];
										$temp1['sks_prak'] = (int)$value['sks_prak'];
										$temp1['sks_prak_lap'] = (int)$value['sks_prak_lap'];
										$temp1['sks_sim'] = (int)$value['sks_sim'];
										$response=$feeder->insertToFeeder('kelas_kuliah',$temp1);
										//echo var_dump($response);exit;
										if ($response['result']['error_code']==0) {
											$idkls=$response['result']['id_kls'];
											$temp1['id_kls'] = $idkls;
											$temp1['id_smt'] = $idsmt;
											$temp1['id_sms'] = $idsms;
											$temp1['id_mk'] = $idmk;
											$temp1['id_mkref'] = $value['subjectcode'];
											$temp1['nm_kls'] = $nmklas;
											$temp1['sks_mk'] = $value['sks_mk'];
											$temp1['sks_tm'] = $value['sks_tm'];
											$temp1['sks_prak'] = $value['sks_prak'];
											$temp1['sks_prak_lap'] = $value['sks_prak_lap'];
											$temp1['sks_sim'] = $value['sks_sim'];
											$this->fnaddData('kelas_kuliah', $temp1);
										}
									}
									
								} else $idkls=$row['id_kls'];
								//insert into nilai
								$row=$this->isInNilaiRaw($idkls, $idreg);
								if (!$row) {
									$filter = "id_kls='".$idkls."' and id_reg_pd='".$idreg."'";
									$response=$feeder->fnGetRecord('nilai.raw', $filter);
									if (count($response)>0) {
										 
											$data=array(
														
													'id_kls'=>$response['id_kls'],
													'id_reg_pd'=>$response['id_reg_pd'],
													'asal_data'=>$response['asal_data'],
													'nilai_angka'=>$response['nilai_angka'],
													'nilai_huruf'=>$response['nilai_huruf'],
													'nilai_indeks'=>$response['nilai_indeks']
											);
											$this->fnaddData('nilai', $data);
									} else {
										
										//add nilai to feeder
										$data=array(
													
												'id_kls'=>$idkls,
												'id_reg_pd'=>$idreg,
												'asal_data'=>"9",
												'nilai_angka'=>$value['nilai_angka'],
												'nilai_huruf'=>$value['nilai_huruf'],
												'nilai_indeks'=>$value['nilai_indeks']
										);
										$response=$feeder->insertToFeeder('nilai',$data);
										if ($response['result']['error_code']=="0") {
											$this->fnaddData('nilai', $data);
										}
									}
									//$this->fnupdateData('kuliah_mahasiswa', array('error_code'=>'101'), 'id='.$item['id']);
								}	 
								else {
									if ($row['nilai_indeks']<$value['nilai_indeks']) {
										$data=array('nilai_huruf'=>$value['nilai_huruf'],
												'nilai_angka'=>$value['nilai_angka'],
												'nilai_indeks'=>$value['nilai_indeks']
										);
										$response=$feeder->updateToFeeder('nilai',array('id_reg_pd'=>$idreg,'id_kls'=>$idkls), $data);
										if ($response['result']['error_code']=="0") {
											$this->fnupdateData('nilai', $data, "id_reg_pd='".$idreg."' and id_kls='".$idkls."'");
										}
										//$this->fnupdateData('kuliah_mahasiswa', array('error_code'=>'101'), 'id='.$item['id']);
									}
								}
								}
						}
						$this->fnupdateData('kuliah_mahasiswa', array('error_code'=>'101'), 'id='.$item['id']);
						
					} else $this->fnupdateData('kuliah_mahasiswa', array('error_code'=>'99'), 'id='.$item['id']);	
		 	
				} else $this->fnupdateData('kuliah_mahasiswa', array('error_code'=>'98'), 'id='.$item['id']);	
			
		 	}
			
		}
		
		public function pushTrakm() {
			 
				$feeder=new Reports_Model_DbTable_Wsclienttbls();
				$db = Zend_Db_Table::getDefaultAdapter();
				$select = $db->select()
				->from(array('km'=>'kuliah_mahasiswa'),array('id_reg_pd','id_smt'))
				->where('km.ips=0 and km.sks_smt=0 and km.id_stat_mhs="A"')
				->where('error_code in ("10","101") and id_reg_pdref="" ');
				$row=$db->fetchAll($select);
				foreach ($row as $value) {
					$idreg=$value['id_reg_pd'];
					$idsmt=$value['id_smt'];
					//calucate IPS dan sks IPS
					$select = $db->select()
					->from(array('kls'=>'kelas_kuliah'))
					//->join(array('mk'=>'mata_kuliah'),'mk.id_mk=kls.id_mk and mk.id_sms=kls.id_sms')
					->join(array('n'=>'nilai'),'n.id_kls=kls.id_kls')
					->where('n.id_reg_pd=?',$idreg)
					->where('kls.id_smt=?',$idsmt)
					->order('kls.id_mk')
					->order('n.nilai_indeks DESC');
					$nilais=$db->fetchAll($select);
					if ($nilais) {
						$idmk='';
						$sksem=0;
						$point=0;
						//echo var_dump($nilais);echo $idreg;
						foreach ($nilais as $items) {
							if ($idmk!=$items['id_mk']) {
								$sksem=$sksem+$items['sks_mk'];
								$point=$point+$items['sks_mk']*$items['nilai_indeks'];
							}
						}
						if ($sksem>0) $ips=$point/$sksem;
						//calculate IPK dan sks total get from previous
						if (substr($idsmt,strlen($idsmt)-1,1)=='1'){ 
							$thn=substr($idsmt,0,4);
							//echo $thn;
							$thn=$thn-1;
							$idsmtpre=$thn.'2';
							//echo $thn;
						} else 
						 	$idsmtpre=substr($idsmt,0,4).'1';
						//get form feerder
						$filter="id_smt='".$idsmtpre."' and id_reg_pd='".$idreg."'";
						//echo $filter;exit;
						$response=$feeder->fnGetRecord('kuliah_mahasiswa', $filter);
						//echo "Respon=";echo var_dump($response);
						if (count($response)>0) {
							$ipkpre=$response['ipk'];
							$skstotal=$response['sks_total'];
							$pointall=$skstotal*$ipkpre;
							$pointall=$pointall+$point;
							$skstotal=$skstotal+$sksem;
							$ipk=$pointall/$skstotal;
						} else {
							$ipk=$ips;
							$skstotal=$sksem;
						}
						$data=array('ips'=>$ips,
								'sks_smt'=>$sksem,
								'ipk'=>$ipk,
								'sks_total'=>$skstotal
						);
						$key=array('id_smt'=>$idsmt,
								'id_reg_pd'=>$idreg
						);
						//echo $idreg; echo var_dump($data);exit;
						$response=$feeder->updateToFeeder('kuliah_mahasiswa', $key,$data);
						//echo "Respon=".$idreg;echo var_dump($response);
						if ($response['result']['error_code']==0) {
							$this->fnupdateData('kuliah_mahasiswa', $data,"id_smt='".$idsmt."' and id_reg_pd='".$idreg."'");
						}
					} else {
							$this->fnupdateData('kuliah_mahasiswa', array('error_code'=>"102"),"id_smt='".$idsmt."' and id_reg_pd='".$idreg."'");
						
					}
					
				}
				
				
				//echo $idreg;exit;
		}
		public function getTRAKD($idsemester,$faculty=null,$program=null) {
		
			$feeder=new Reports_Model_DbTable_Wsclienttbls();
			$db = Zend_Db_Table::getDefaultAdapter();
		
			$lstrSelect = $db ->select()
			->from(array('s'=>'tbl_semestermaster'),array('year'=>'CONCAT(LEFT(SemesterMainCode,4),SemesterCountType)'))
			->where('s.IdSemesterMaster=?',$idsemester);
			$smt=$db->fetchRow($lstrSelect);
			$yearsmt=$smt['year'];
			
			$select = $db->select()
			->from(array('km'=>'kelas_kuliah'),array('id_smt','nm_kls','SubCode'=>'id_mkref'))
			->join(array('sb'=>'ajar_dosen'),'km.Id=sb.Id_klsm',array('jml_tm_renc','jml_tm_real'))
			->join(array('sm'=>'tbl_subjectmaster'),'km.IdSubject=sm.IdSubject',array('SubjectName'=>'sm.BahasaIndonesia'))
			->join(array('ds'=>'dosen_pt'),'ds.id_reg_ptk=sb.id_reg_ptk',array())
			->join(array('st'=>'tbl_staffmaster'),'st.IdStaff=ds.IdStaff',array('FullName','st.Dosen_Code_EPSBED'))
			->join(array('prg'=>'tbl_program'),'prg.id_sms=km.id_sms',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode','Program_code_EPSBED','Strata_code_EPSBED'))
			->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=prg.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
			->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED'))
			->where('sb.id_ajar!=""')
			->where('km.id_smt=?',$yearsmt);
			
			if ($faculty!=null) $select->where('col.IdCollege=?',$faculty);
			if ($program!=null) $select->where('prg.IdProgram=?',$program);
				
			
			$results=$db->fetchAll($select);
			return $results;
		}
		public function getTRNLM($idsemester,$faculty=null,$program=null) {
		
			$feeder=new Reports_Model_DbTable_Wsclienttbls();
			$db = Zend_Db_Table::getDefaultAdapter();
		
			$lstrSelect = $db ->select()
			->from(array('s'=>'tbl_semestermaster'),array('year'=>'CONCAT(LEFT(SemesterMainCode,4),SemesterCountType)'))
			->where('s.IdSemesterMaster=?',$idsemester);
			$smt=$db->fetchRow($lstrSelect);
			$yearsmt=$smt['year'];
				
			$select = $db->select()
			->from(array('km'=>'kelas_kuliah'),array('id_smt','nm_kls','id_mkref'))
			->join(array('sm'=>'tbl_subjectmaster'),'km.IdSubject=sm.IdSubject',array('SubjectName'=>'sm.BahasaIndonesia'))
			->join(array('sb'=>'nilai'),'km.id_kls=sb.id_kls',array('nilai_huruf','nilai_indeks'))
			->join(array('st'=>'mahasiswa_pt'),'sb.id_reg_pd=st.id_reg_pdref',array('nipd'))
			->join(array('str'=>'tbl_studentregistration'),'st.nipd=str.registrationid',array('nim'=>'registrationId'))
			->join(array('sp'=>'student_profile'),'str.idapplication=sp.appl_id',array('FullName'=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)'))
			->join(array('prg'=>'tbl_program'),'str.IdProgram=prg.IdProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode','Program_code_EPSBED','Strata_code_EPSBED'))
			->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=prg.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
			->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED'))
			//->where('sb.status="1"')
			->where('km.id_smt=?',$yearsmt);
				
			if ($faculty!=null) $select->where('col.IdCollege=?',$faculty);
			if ($program!=null) $select->where('prg.IdProgram=?',$program);
			
			$results=$db->fetchAll($select);
			
			//echo $select;exit;
			return $results;
		}
		
		public function getTRNLMByStd($idregpd,$idsmt) {
		
			$feeder=new Reports_Model_DbTable_Wsclienttbls();
			$db = Zend_Db_Table::getDefaultAdapter();
		
			 
			$select = $db->select()
			->from(array('km'=>'kelas_kuliah'))
			->join(array('sm'=>'tbl_subjectmaster'),'km.IdSubject=sm.IdSubject',array('SubjectName'=>'sm.BahasaIndonesia'))
			->join(array('sb'=>'nilai'),'km.id_kls=sb.id_kls',array('nilai_huruf','nilai_indeks'))
			->join(array('sr'=>'mahasiswa_pt'),'sr.id_reg_pdref=sb.id_reg_pd',array())
			  //->where('sb.status="1"')
			->where('km.id_smt=?',$idsmt)
			->where('sr.id_reg_pd=?',$idregpd);
		 	
			$results=$db->fetchAll($select);
			//echo var_dump($results);
			//echo $select;exit;
			return $results;
		}
		
		public function getTRNLMKRS($idsemester,$faculty=null,$program=null) {
		
			$feeder=new Reports_Model_DbTable_Wsclienttbls();
			$db = Zend_Db_Table::getDefaultAdapter();
		
			$lstrSelect = $db ->select()
			->from(array('s'=>'tbl_semestermaster'),array('SemesterCountType','idacadyear','year'=>'CONCAT(LEFT(SemesterMainCode,4),SemesterCountType)'))
			->where('s.IdSemesterMaster=?',$idsemester);
			$smt=$db->fetchRow($lstrSelect);
			$yearsmt=$smt['year'];
			$semcountype=$smt['SemesterCountType'];
			$year=$smt['idacadyear'];
		
			$select = $db->select()
			->from(array('km'=>'tbl_course_tagging_group'),array('nm_kls'=>'GroupCode'))
			->join(array('sm'=>'tbl_subjectmaster'),'km.IdSubject=sm.IdSubject',array('id_mkref'=>'ShortName','SubjectName'=>'sm.BahasaIndonesia'))
			->join(array('sb'=>'tbl_studentregsubjects'),'km.idCourseTaggingGroup=sb.idCourseTaggingGroup',array('nilai_huruf'=>null,'nilai_indeks'=>null))
			 ->join(array('str'=>'tbl_studentregistration'),'sb.IdStudentRegistration=str.IdStudentRegistration',array('nim'=>'registrationId','nipd'=>'registrationId'))
			->join(array('sp'=>'student_profile'),'str.idapplication=sp.appl_id',array('FullName'=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)'))
			->join(array('prg'=>'tbl_program'),'str.IdProgram=prg.IdProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode','Program_code_EPSBED','Strata_code_EPSBED'))
			->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=prg.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
			->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED'))
			->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=sb.IdSemesterMain',array('SemesterMainName','id_smt'=>'CONCAT(LEFT(s.SemesterMainCode,4),s.SemesterCountType)'))
			
			//->where('sb.status="1"')
			->where('s.idacadyear=?',$year)
			->where('s.SemesterCountType=?',$semcountype)
			->where('s.SemesterFunctionType not in ("2","5")')
			->order('s.SemesterMainName')
			->order('str.registrationId');
		
			if ($faculty!=null) $select->where('col.IdCollege=?',$faculty);
			if ($program!=null) $select->where('prg.IdProgram=?',$program);
			//echo $select;exit;
			$results=$db->fetchAll($select);
				
			//echo var_dump($results);exit;
			return $results;
		}
		
		
	/*	
	  public function updateKelasNilai($idsmt){
			$feeder=new Reports_Model_DbTable_Wsclienttbls();
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$lstrSelect = $db ->select()
			->from(array('s'=>'tbl_semestermaster'),array('year'=>'CONCAT(LEFT(SemesterMainCode,4),SemesterCountType)'))
			->where('s.IdSemesterMaster=?',idsmt);
			$smt=$db->fetchRow($lstrSelect);
			$yearsmt=$smt['year'];
			$select = $db->select()
			->from(array('km'=>'kelas_kuliah'),array('id_smt','nm_kls','id_mk'))
			->join(array('sb'=>'nilai'),'km.id_kls=sb.id_kls',array('sb.id_kls'))
			->where('km.id_smt=?',$yearsmt)
			->where('sb.error_code="0"');
			$nilais=$db->fetchAll($select);
			 
				
			
		}*/
		 
		
		 public function transferNilaiApprove($data,$Id) {
		 	
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	if (!isset($data['Grade_name'])) {
		 		//get from conversion
		 		$select = $db->select()
		 		->from(array("a"=>"tbl_conversion_result"))
		 		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubject',array('subcode'=>'sm.shortname','subject'=>'sm.BahasaIndonesia','sks'=>'sm.CreditHours'))
		 		->join(array('s'=>'tbl_semestermaster'),'a.IdSemesterMain=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
		 		->join(array('sm1'=>'tbl_subjectmaster'),'sm1.IdSubject=a.IdSubjectNew',array('newsubcode'=>'sm1.shortname','newsubject'=>'sm1.BahasaIndonesia','newsks'=>'sm1.CreditHours'))
		 		->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration',array('IdProgram'))
		 		->where('a.IdConversionResult=?',$Id);
		 		$row=$db->fetchRow($select);
		 		if (!$row) {
		 			$select = $db->select()
		 			->from(array("a"=>"tbl_studentregsubjects"),array('Grade_name_new'=>'a.grade_name','IdConversionResult'=>'IdStudentRegSubjects','Grade_point_new'=>'grade_point','IdSemesterMain','idStudentRegistration'=>'IdStudentRegistration'))
		 			->join(array('t'=>'nilai_transfer_proposal'),'a.IdStudentRegSubjects=t.IdStudentRegSubjects',array('subcode'=>'kode_mk_asal','subjectnew'=>'kode_mk_tujuan','subject'=>'nm_mk_asal','sks'=>'sks_asal','Grade_name'=>'nilai_huruf_asal'))
		 			->join(array('s'=>'tbl_semestermaster'),'a.IdSemesterMain=s.IdSemesterMaster',array('IdSemesterMain'=>'a.IdSemesterMain','semester_name'=>'SemesterMainName'))
		 			->join(array('sm1'=>'tbl_subjectmaster'),'sm1.IdSubject=a.IdSubject',array('newsubcode'=>'sm1.shortname','newsubject'=>'sm1.BahasaIndonesia','newsks'=>'sm1.CreditHours'))
		 			->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration',array('IdProgram'))
		 			->where('a.IdStudentRegSubjects=?',$Id);
		 			$row=$db->fetchRow($select);
		 		//	echo $select;exit;
		 			if (!$row) {
		 				$select = $db->select()
		 				//->from(array("a"=>"tbl_studentregsubjects"),array('Grade_name_new'=>'a.grade_name','IdConversionResult'=>'IdStudentRegSubjects','Grade_point_new'=>'grade_point','IdSemesterMain','idStudentRegistration'=>'IdStudentRegistration'))
		 				->from(array('t'=>'nilai_transfer'),array('Grade_name_new'=>'t.nilai_huruf_diakui','IdConversionResult'=>'IdTransfer','Grade_point_new'=>'nilai_angka_diakui','IdSemesterMain','idStudentRegistration'=>'IdStudentRegistration','subcode'=>'kode_mk_asal','subjectnew'=>'kode_mk_tujuan','subject'=>'nm_mk_asal','sks'=>'sks_asal','Grade_name'=>'nilai_huruf_asal','newsubcode'=>'kode_mk_tujuan','newsubject'=>'nm_mk_tujuan','newsks'=>'sks_diakui'))
		 				->join(array('s'=>'tbl_semestermaster'),'t.IdSemesterMain=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
		 			//	->join(array('sm1'=>'tbl_subjectmaster'),'sm1.IdSubject=a.IdSubject',array())
		 				->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=t.IdStudentRegistration',array('IdProgram'))
		 				->where('t.IdTransfer=?',$Id);
		 				$row=$db->fetchRow($select);
		 			}
		 		}
		 		 
		 	}	
		 	else {
		 		  
		 			$select = $db->select()
		 			->from(array("a"=>"tbl_studentregsubjects"),array('Grade_name_new'=>'a.grade_name','IdConversionResult'=>'IdStudentRegSubjects','Grade_point_new'=>'grade_point','IdSemesterMain','idStudentRegistration'=>'IdStudentRegistration'))
		 			//->join(array('t'=>'nilai_transfer_proposal'),'a.IdStudentRegSubjects=t.IdStudentRegSubjects',array('subcode'=>'kode_mk_asal','subjectnew'=>'kode_mk_tujuan'))
		 			->join(array('s'=>'tbl_semestermaster'),'a.IdSemesterMain=s.IdSemesterMaster',array('IdSemesterMain'=>'a.IdSemesterMain','semester_name'=>'SemesterMainName'))
		 			->join(array('sm1'=>'tbl_subjectmaster'),'sm1.IdSubject=a.IdSubject',array('newsubcode'=>'sm1.shortname','newsubject'=>'sm1.BahasaIndonesia','newsks'=>'sm1.CreditHours'))
		 			->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration',array('IdProgram'))
		 			->where('a.IdStudentRegSubjects=?',$Id);
		 			$row=$db->fetchRow($select);
		 		 
		 	}
		 	//echo $select;exit;
			if ($row) {
			 	$data=array('kode_mk_asal'=>$row['subcode'],
			 				'kode_mk_tujuan'=>$row['newsubcode'],
			 				'nm_mk_asal'=>$row['subject'],
			 				'nm_mk_tujuan'=>$row['newsubject'],
			 				'sks_asal'=>$row['sks'],
			 				'sks_diakui'=>$row['newsks'],
			 				'nilai_huruf_asal'=>$row['Grade_name'],
			 				'nilai_huruf_diakui'=>$row['Grade_name_new'],
			 				'nilai_angka_diakui'=>$row['Grade_point_new'],
			 				'IdProgram'=>$row['IdProgram'],
			 				'IdSemesterMain'=>$row['IdSemesterMain'],
			 				'IdStudentRegistration'=>$row['idStudentRegistration']
			 	);
			 	$isin=$this->isInTransferNilai($row['IdSemesterMain'], $row['idStudentRegistration'], $row['subcode'], $row['newsubcode']);
			 	
			 	if ($isin) {
			 		//echo var_dump($isin);exit;
			 		$db->update('nilai_transfer',$data,'IdTransfer='.$isin['IdTransfer']);
			 	} else {
			 		 
			 		$db->insert('nilai_transfer',$data);
			 	}
			 }
		 	
		 }
		 public function isInTransferNilai($idsemester,$idstudent,$idsubject,$idnewsubject) {
		 	
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$select = $db->select()
		 	->from(array("a"=>"nilai_transfer"))
		 	->where('a.IdStudentRegistration=?',$idstudent)
		 	->where('a.kode_mk_tujuan=?',$idnewsubject);
		 	if($idsemester!=null) $select->where('a.IdSemesterMain=?',$idsemester);
		 	//echo $select;exit;
		 	if ($idsubject!=null) $select->where('a.kode_mk_asal=?',$idsubject);
		 	$row=$db->fetchRow($select);
		 	//echo var_dump($row);exit;
		 	return $row;
		 }
		 
		 public function getTransferNilaiApproved($post){
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$select = $db->select()
		 	->from(array("a"=>"nilai_transfer"))
		 	->joinLeft(array('sm'=>'tbl_subjectmaster'),'a.kode_mk_asal=sm.subcode',array('subcode'=>'sm.shortname','subject'=>'sm.BahasaIndonesia','sks'=>'sm.CreditHours'))
		 	->join(array('s'=>'tbl_semestermaster'),'a.IdSemesterMain=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
		 	->join(array('sm1'=>'tbl_subjectmaster'),'sm1.subcode=a.kode_mk_tujuan',array('newsubcode'=>'sm1.shortname','newsubject'=>'sm1.BahasaIndonesia','newsks'=>'sm1.CreditHours'))
		 	->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration',array('registrationId','transaction_id')) 
		 	->join(array('sp'=>'student_profile'),'sp.appl_id=st.idapplication',array('StudentName'=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)'))
		 	->order('st.registrationId');
		 	if ($post['IdSemester']!=null) $select->where('a.IdSemesterMain  = ?',$post['IdSemester']);
		 	if ($post['IdStudent']!=null) $select->where('st.registrationId  = ?',$post['IdStudent']);
		 	if ($post['programme']!=null) $select->where('st.IdProgram  = ?',$post['programme']);
		 	if ($post['intake_id']!=null) $select->where('st.IdIntake  = ?',$post['intake_id']);
		 	if ($post['IdSubject']!=null) $select->where('sm1.IdSubject  = ?',$post['IdSubject']);
		 	if ($post['problem']=="1") $select->where("TRIM(a.error_desc)='Ok' ");
		 	else  $select->where("TRIM(a.error_desc)<>'Ok' ");
		 	
		  
		 	$result = $db->fetchAll($select);
		 	 
		 	// echo var_dump($post);
		 	return $result;
		 }
		 
		 public function nilaiTransferDelete($idekivalen) {
		 	$Feeder = new Reports_Model_DbTable_Wsclienttbls();
		 	$response = $Feeder->deleteToFeeder('nilai_transfer',array('id_ekuivalensi'=>$idekivalensi));
		 	//echo var_dump($response);exit;
		 	//update kelas
		 	//echo var_dump($temp1);echo 'ini kelas.';exit;
		 	if ($response['result']['error_code']==0) {
		 		return $response['result']['id_ekuivalensi'];
		 		//echo var_dump($response);echo 'ini kelas.';exit;
		 	} else  return false;
		 }
		 public function getTransferStudent($post){
		 	//echo var_dump($post);
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$select = $db->select()
		 	->from(array("a"=>"nilai_transfer"),array('id_reg_pd'))
		 	->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration',array('IdStudentRegistration','jenis_pendaftaran','sks_diakui'))
		 	->group('st.registrationId');
		 	if ($post['IdSemester']!=null) $select->where('a.IdSemesterMain  = ?',$post['IdSemester']);
		 	if ($post['IdStudent']!=null) $select->where('st.registrationid  = ?',$post['IdStudent']);
		 	if ($post['programme']!=null) $select->where('st.IdProgram  = ?',$post['programme']);
		 	if ($post['IdIntake']!=null) $select->where('st.IdIntake  = ?',$post['IdIntake']);
		 	
		 	//echo $select;exit;
		 	
		 	$result = $db->fetchAll($select);
		 	 
		 	return $result;
		 }
		 	
		public function getSemesterListFromFeeder($idsem=null) {
			$Feeder = new Reports_Model_DbTable_Wsclienttbls();
			
		}
		 public function transferNilaiApproveToFeeder($data,$Id) {
		 	$Feeder = new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$select=$db->select()
		 	->from(array("a"=>"nilai_transfer"))
		 	->joinLeft(array('sm'=>'tbl_subjectmaster'),'a.kode_mk_asal=sm.subcode',array('subcode'=>'sm.shortname','subject'=>'sm.BahasaIndonesia','sks'=>'sm.CreditHours'))
		 	->join(array('s'=>'tbl_semestermaster'),'a.IdSemesterMain=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
		 	->join(array('sm1'=>'tbl_subjectmaster'),'sm1.subcode=a.kode_mk_tujuan',array('newsubcode'=>'sm1.shortname','newsubject'=>'sm1.BahasaIndonesia','newsks'=>'sm1.CreditHours'))
		 	->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration')
		 	->join(array('p'=>'tbl_program'),'p.IdProgram=st.IdProgram',array('id_sms'=>'p.id_sms'))
		 	->where('a.IdTransfer=?',$Id);
		 	$row=$db->fetchRow($select);
		 	// echo var_dump($row);
		 	// echo '<br>';
		 	if ($row) {
		 		//get id_mk from feeder
		 		$filter = "id_sms='".$row['id_sms']."' AND  kode_mk= '".$row['newsubcode']."'";
		 		$response = $Feeder->fnGetRecord('mata_kuliah.raw', $filter);
		 		//echo var_dump($response); 
		 		if (count($response)==0) {
		 			$filter = "trim(kode_mk) = '".$row['newsubcode']."'";
		 			
		 			$response = $Feeder->fnGetRecord('mata_kuliah.raw', $filter);

		 		}
		 		//echo $filter;
		 		//echo var_dump($response);exit;
		 		if (count($response)>0) {
		 			$idmk=$response['id_mk'];
		 			//cek Id mhs
		 			$filter = "id_sms='".$row['id_sms']."' AND  trim(nipd)= '".$row['registrationId']."'";
		 			$response = $Feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
		 			//echo var_dump($response);exit;
		 			if (count($response)>0) {
		 				$idregpd=$response['id_reg_pd'];
		 				if ($row['subcode']=='') {
		 					$row['subcode']=$row['kode_mk_asal'];
		 					if ($row['subcode']=='') $row['subcode']='XXXX';
		 					
		 					$row['sks']=$row['sks_asal'];
		 					$row['subject']=$row['nm_mk_asal'];
		 				}
		 				//echo var_dump($row);
		 				//echo '<br>';
		 				$data=array(
		 						'id_mk'=>$idmk,
		 						'id_reg_pd'=>$idregpd,
		 						'kode_mk_asal'=>$row['subcode'],
		 						//'kode_mk_tujuan'=>$row['newsubcode'],
		 						'nm_mk_asal'=>$row['subject'],
		 						//'nm_mk_tujuan'=>$row['newsubject'],
		 						'sks_asal'=>$row['sks']*1,
		 						'sks_diakui'=>$row['newsks']*1,
		 						'nilai_huruf_asal'=>$row['nilai_huruf_asal'],
		 						'nilai_huruf_diakui'=>$row['nilai_huruf_diakui'],	
		 						'nilai_angka_diakui'=>$row['nilai_angka_diakui']*1
		 				//nilai angka diakui bobot nilai grade_point nya berapa
		 						
		 				);
		 				//update if any
		 				$filter = "id_reg_pd= '".$idregpd."' and id_mk='".$idmk."'";
		 				$response = $Feeder->fnGetRecord('nilai_transfer.raw', $filter);
		 				
		 				if (count($response)==0) {
			 				//echo var_dump($data); 
			 				$response = $Feeder->insertToFeeder('nilai_transfer',$data);
			 				//echo var_dump($response);exit;
			 				//update kelas
			 				//echo var_dump($temp1);echo 'ini kelas.';exit;
			 				if ($response['result']['error_code']==0) {
			 					$idekivalensi=$response['result']['id_ekuivalensi'];
			 					$this->fnupdateData('nilai_transfer', array('id_mk'=>$idmk,'id_reg_pd'=>$idregpd,'id_ekivalensi'=>$idekivalensi,'error_desc'=>'Ok'),'IdTransfer='.$Id);
			 					
			 					//echo var_dump($response);echo 'ini kelas.';exit;
			 				} else {
			 					$formdata['error_code']=$response['result']['error_code'];
			 					$formdata['error_desc']=$response['result']['error_desc'];
			 					$this->fnupdateData('nilai_transfer', $formdata, 'IdTransfer=.'.$Id);
			 						
			 				}
		 				} else {
		 					unset($data['id_mk']);
		 					unset($data['id_reg_pd']);
		 					$idekivalensi=$response['id_ekuivalensi'];
		 					$response=$Feeder->updateToFeeder('nilai_transfer', array('id_reg_pd'=>$idregpd,'id_mk'=>$idmk),$data);
		 					//echo var_dump($response);exit;
		 					if ($response['result']['error_code']==0) {
		 						//$idekivalensi=$response['result']['id_ekuivalensi'];
		 						$this->fnupdateData('nilai_transfer', array('id_mk'=>$idmk,'id_reg_pd'=>$idregpd,'id_ekivalensi'=>$idekivalensi,'error_desc'=>'Ok'),'IdTransfer='.$Id);
		 							
		 						//echo var_dump($response);echo 'ini kelas.';exit;
		 					} else {
		 						$formdata['error_code']=$response['result']['error_code'];
		 						$formdata['error_desc']=$response['result']['error_desc'];
		 						$this->fnupdateData('nilai_transfer', $formdata, 'IdTransfer=.'.$Id);
		 					
		 					}
		 				}
		 			} else {
		 				$this->fnupdateData('nilai_transfer', array('error_desc'=>'MHS tidak terdaftar di Feeder'),'IdTransfer='.$Id);
		 				 
		 			}
		 		} else {
		 			//update error
		 			$this->fnupdateData('nilai_transfer', array('error_desc'=>'MK tidak ada di Feeder'),'IdTransfer='.$Id);
		 		}
		 	}
		 	
		 	/* if ($this->isInTransferNilai($row['IdSemesterMain'], $row['IdStudentRegistration'], $row['SubCode'], $row['newsubcode'])) {
		 		$db->update('nilai_transfer',$data,'IdTransfer='.$Id);
		 	
		 	} else
		 		$db->insert('nilai_transfer',$data);
		  */
		 }
		 public function updateNilai($post){
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	// echo var_dump($post);
		 	$select = $db->select()
		 	->from(array('kls'=>'trans_nilai'),array('id'=>'kls.id','IdStudentRegSubjects','nilai_huruf','IdStudentRegistration'))
		 	->join(array('srs'=>'tbl_split_coursegroup'),'kls.IdSplitGroup=srs.Id')
		 	->join(array('st'=>'tbl_studentregistration'),'kls.IdStudentRegistration=st.IdStudentRegistration',array());
		 	
		 	if (isset($post['IdSemester']) && $post['IdSemester']!='')
		 		$select->where('srs.IdSemester=?',$post['IdSemester']);
		 		
		 	if (isset($post['programme']) && $post['programme']!='')
		 		$select->where('st.IdProgram=?',$post['programme']);
		 	
		 	if (isset($post['IdMajoring']) && $post['IdMajoring']!='')
		 		$select->where('st.IdProgramMajoring=?',$post['IdMajoring']);
		 	
		 	if (isset($post['intake_id']) && $post['intake_id']!='')
		 		$select->where('st.IdIntake=?',$post['intake_id']);
		 		
		 	if (isset($post['IdStudent']) && $post['IdStudent']!='0')
		 		$select->where('st.registrationid=?',$post['IdStudent']);
		 		
		 	if (isset($post['IdSubject']) && $post['IdSubject']!='')
		 		$select->where('srs.IdSubject=?',$post['IdSubject']);
		 		
		 	//echo $select;exit;
		 	$result = $db->fetchAll($select);
		 	//echo var_dump($result);
		 	//echo '<br>';
		 	//update trans_nilai
		 	$dbStdRegSub=new Examination_Model_DbTable_StudentRegistrationSubject();
		 	foreach ($result as $value) {
		 		
		 		//$idStudentRegSubject=$value['IdStudentRegSubjects'];
		 		$grade=$dbStdRegSub->getHighestRegulerGrade($value['IdStudentRegistration'],$value['IdSubject'],$value['IdSemesterSubject']);
		 		//echo var_dump($grade);
		 		//echo '<br>';
		 		if (count($grade)>0)  {
			 			$data=array('nilai_angka'=>$grade['final_course_mark'],
			 				'nilai_huruf'=>$grade['grade_name'],
			 				'nilai_indeks'=>$grade['grade_point'],
			 				'IdStudentRegSubjects'=>$grade['IdStudentRegSubjects'],
			 				'exam_status'=>$grade['exam_status']
			 			);
			 			$this->fnupdateData('trans_nilai', $data, 'id='.$value['id']);
		 		}
		 	}
		 	//exit;
		 	return $result;
		 }
		 
		 public function getRecape($idsemester,$semester,$idprogram) {
		 	 
		 		$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		 		$dbProgram=new GeneralSetup_Model_DbTable_Program();
		 		$prg=$dbProgram->fngetProgramData($idprogram);
		 	//	echo var_dump($prg);exit;
		 		$limit=null;$offset=null;
		 		$programcode=$prg['ProgramCode'];
		 		$idsms=$prg['id_sms'];
		 		$yearrpt=$this->getYearReport($semester);
		 		//get student list report
		 		$filter = "id_sms='".$idsms."' and mulai_smt='".$yearrpt."'"; 
		 		$jmlmhs = $Feeder->fnGetRecordSet('mahasiswa_pt.raw', $filter,$limit,$offset);
		 		$data=array();
		 		$dbstudent=new Registration_Model_DbTable_Studentregistration();
		 		$std=$dbstudent->getStudentRegistrationDetailbyProgram($idprogram, $this->getIdIntake($yearrpt));
		 		$data=array('mhsbaru_sis'=>count($std),'mhsbaru_feeder'=>count($jmlmhs));
		 		
		 		//get transaksi dosen
		 		$filter = "id_sms='".$idsms."' and id_smt='".$yearrpt."'";
		 		$jmlkelas = $Feeder->fnGetRecordSet('kelas_kuliah.raw', $filter,$limit,$offset);
		 		$data['kelas_feeder']=count($jmlkelas);
		 		$dbCourse=new GeneralSetup_Model_DbTable_CourseGroup();
		 		$course=$this->fnGetActiveClass(array('IdSemester'=>$idsemester,'programme'=>$idprogram,'semester'=>$semester));
		 		$data['kelas_sis']=count($course);
		 		
		 		//get kuliah mhs
		 		$dbstdgrade=new Examination_Model_DbTable_StudentGrade();
		 		$stdgrade=$dbstdgrade->getStdGrade($idsemester, $idprogram);
		 		$data['kuliah_sis']=count($stdgrade);
		 		$stdgrade=$dbstdgrade->getStdGradeFeeder($idsemester, $idprogram);
		 		$jml=0;
		 		foreach ($stdgrade as $grade) {
		 			$filter = "id_smt='".$yearrpt."' and id_reg_pd='".$grade['id_reg_pd']."'";
		 			$jmlkelas = $Feeder->fnGetRecord('kuliah_mahasiswa.raw', $filter,$limit,$offset);
		 			if (count($jmlkelas)>0) $jml++;
		 		}
		 		$data['kuliah_feeder']=$jml;
		 		
		 		
		 		//get trans
		 		$filter = "id_smt='".$yearrpt."' and nilai_huruf <> '' and left(nipd,3)='".substr($programcode,0,3)."'";
		 		$jmlkelas = $Feeder->fnGetRecordSet('nilai', $filter,$limit,$offset);
		 		$data['nilai_feeder']=count($jmlkelas);
		 		$nilai=$this->transmhsSearch(array('programme'=>$idprogram,'id_smt'=>$yearrpt));
		 		$data['nilai_sis']=count($nilai);
		 		return $data;
		 		 
		 		
		 		
		 }
		 public function getRecapeNilai($idsemester,$semester,$idprogram) {
		 	 
		 	$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$msdb=new Reports_Model_DbTable_Mhssetup();
		 	$dbProgram=new GeneralSetup_Model_DbTable_Program();
		 	$prg=$dbProgram->fngetProgramData($idprogram);
		 	
		 	$programcode=$prg['ProgramCode'];
		 	$idsms=$prg['id_sms'];
		 	$yearrpt=$this->getYearReport($idsemester);
		 	 
		 	//get transaksi dosen
		 	$limit=300;
		 	$offset=0;
		 	 
		 	$course=$msdb->fnGetProcessedClassAll(array('IdSemester'=>$idsemester,'programme'=>$idprogram,'semester'=>$semester));
		 	
		 	foreach ($course as $key=>$value) {
		 		 
		 		$nilai=$this->transmhsSearch(array('programme'=>$idprogram,'id_smt'=>$yearrpt,'IdKelas'=>$value['GroupCode'],'IdSubject'=>$value['IdSubject']));
		 		$course[$key]['nilai_sis']=count($nilai);
		 		//get feeder
		 		
		 		if ($nilai) {
		 			
		 			$filter = "id_kls='".$nilai[0]['id_kls']."' and nilai_huruf <> '' ";
		 			 
		 			$jmlkelas = $Feeder->fnGetRecordSet('nilai.raw', $filter,$limit,$offset);
		 			$course[$key]['nilai_feeder']=count($jmlkelas);
		 		} else 
		 			$course[$key]['nilai_feeder']=0;
		 		
		 	} 
		 	return $course;
		 
		 	 
		 	 
		 }
		 
		 public function getYearReport($idsemester) {
		 	
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	
		 	 $lstrSelect = $db ->select()
		 	->from(array('s'=>'tbl_semestermaster'),array('yearsem'=>'CONCAT(LEFT(SemesterMainCode,4),SemesterCountType)'))
		 	->where('s.IdSemesterMaster=?',$idsemester);
		 	$sem = $db->fetchRow($lstrSelect);
		 	$yearsem=$sem['yearsem'];
		 	return $yearsem;
		 }
		 
		 public function getProgram($idprogram) {
		 	
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	
		 	$lstrSelect = $db ->select()
		 	->from(array('prg'=>'tbl_program'),array('strata','jenjang'=>'prg.id_jenjang_pendidikan','ProgramName'=>'prg.ArabicName', 'ProgramCode'=>'prg.ProgramCode','Strata_code_EPSBED'=>'prg.Strata_code_EPSBED', 'Program_code_EPSBED'=>'prg.Program_code_EPSBED','prg.id_sms','prg.IdProgram'))
		 	->join(array('col' => 'tbl_collegemaster'), 'col.IdCollege=prg.IdCollege',array('collegeName'=>'col.ArabicName','collegeShortcode'=>'col.ShortName'))
		 	->join(array('univ'=>'tbl_universitymaster'), 'univ.idUniversity = col.affiliatedto', array('univ_mohe_code'=>'Univ_code_EPSBED','id_sp','IdUniversity'))
		 	->where('prg.IdProgram=?',$idprogram);
		 	return $db->fetchRow($lstrSelect);
		 }
		 
		 public function getTransferNilaiProposal($idsemester,$idprog,$nim=null){
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$select = $db->select()
		 	->from(array("a"=>"nilai_transfer_proposal"))
		 	->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubjectAsal',array('subcode'=>'sm.shortname','subject'=>'sm.BahasaIndonesia','sks'=>'sm.CreditHours'))
		 	->join(array('s'=>'tbl_semestermaster'),'a.IdSemesterMain=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
		 	->joinLeft(array('sm1'=>'tbl_subjectmaster'),'sm1.IdSubject=a.IdSubjectTujuan',array('newsubcode'=>'sm1.shortname','newsubject'=>'sm1.BahasaIndonesia','newsks'=>'sm1.CreditHours'))
		 	->joinLeft(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration')
		 	->where('a.IdSemesterMain=?',$idsemester)
		 	->where('a.IdProgram=?',$idprog)
		 	->order('a.NIMTujuan');
		 	if ($nim!=null) $select->where('a.NIMTujuan=?',$nim);
		 	//echo $select;exit;
		 	$result = $db->fetchAll($select);
// 		 	if (!$result) {
// 		 		$select = $db->select()
// 		 		->from(array("a"=>"nilai_transfer_proposal"))
// 		 		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubjectAsal',array('subcode'=>'sm.shortname','subject'=>'sm.BahasaIndonesia','sks'=>'sm.CreditHours'))
// 		 		->join(array('s'=>'tbl_semestermaster'),'a.IdSemesterMain=s.IdSemesterMaster',array('semester_name'=>'SemesterMainName'))
// 		 		->joinLeft(array('sm1'=>'tbl_subjectmaster'),'sm1.IdSubject=a.IdSubjectTujuan',array('newsubcode'=>'sm1.shortname','newsubject'=>'sm1.BahasaIndonesia','newsks'=>'sm1.CreditHours'))
// 		 		->joinLeft(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=a.IdStudentRegistration')
// 		 		->where('a.IdSemesterMain=?',$idsemester)
// 		 		->order('a.NIMTujuan');
		 		 
// 		 		//echo $select;exit;
// 		 		$result = $db->fetchAll($select);
// 		 	}
		 	return $result;
		 }
		 public function getTransferNilaiProposalById($id){
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$select = $db->select()
		 	->from(array("a"=>"nilai_transfer_proposal"))
		 	 ->where('a.IdTransfer=?',$id) ;
		 	 
		 	//echo $select;exit;
		 	$result = $db->fetchRow($select);
		 	return $result;
		 }
		 
		 public function updateTransferNilaiProposal($data,$id){
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	 $db->update('nilai_transfer_proposal',$data,'IdTransfer='.$id);
		 }
		 
		 public function getTransNilByMhs($idstudent,$idmst){ //function to find the data to populate in a page of a selected english description to edit.
		 
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$lstrSelect = $db ->select()
		 	->from(array('trn'=>'trans_nilai'),array('status_proses'=>'trn.status','trn.*','IdTransNilai'=>'trn.id') )
		 	->join(array('cg'=>'tbl_split_coursegroup'),'trn.IdSplitGroup=cg.Id',array('trn.IdSplitGroup','nm_kls'=>'cg.GroupCode'))
		 	->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('year'=>'LEFT(s.SemesterMainCode,4)','s.SemesterCountType'))
		 	->join(array('s1'=>'tbl_semestermaster'),'s1.IdSemesterMaster=cg.IdSemesterSubject',array('SemesterSubject'=>'s1.SemesterMainName','IdSemester'=>'s1.IdSemesterMaster'))
		 	->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('IdSubject'=>'sm.IdSubject','shortname','subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','sks_mk'=>'CreditHours','sks_tm'=>'ch_tutorial','sks_prak'=>'ch_practice'))
		 	->join(array('sg'=>'tbl_studentregistration'),'sg.IdStudentRegistration=trn.IdStudentRegistration',array('nim'=>'registrationId'))
		 	->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=sg.IdApplication',array('fullname'=>new Zend_Db_Expr("CONCAT_WS(' ',sp.appl_fname, sp.appl_mname, sp.appl_lname)")))
		 	->where('trn.IdStudentRegistration = ?', $idstudent)
		 	->where('cg.IdSemester=?',$idmst)
		 	->where('trn.postpone="0"');
		 	
		 	$result = $db->fetchAll($lstrSelect);
		 	return $result;
		 }
		 
		 public function getDayaTampung($idsmt,$idsms) {
		 
		 	$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$filter=" id_smt='".$idsmt."' and id_sms='".$idsms."'";
		 	$sub=$Feeder->fnGetRecord('daya_tampung.raw',$filter );
		 	if (count($sub)>0) return $sub;
		 	else return null;
		 
		 
		 }
		 
		 public function approveDayaTampung($data) {
		 		
		 	$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	$filter="id_smt='".$data['id_smt']."' and id_sms='".$data['id_sms']."'";
		 	$key=array('id_smt'=>$data['id_smt'],'id_sms'=>$data['id_sms']);
		 	$sub=$Feeder->fnGetRecord('daya_tampung.raw',$filter );
		 	if (count($sub)>0) {
		 		unset($data['id_sms']);
		 		unset($data['id_smt']);
		 		 return $Feeder->updateToFeeder('daya_tampung',$key, $data);
		 	}
		 	else  
		 		return $Feeder->insertToFeeder('daya_tampung',$data);
		 		
		 }
		 
		 public function updateLulus($data) {
		 	 
		 	$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 	
		 	$filter="trim(nipd)='".trim($data['nim'])."' and id_sms='".$data['id_sms']."'";//  and id_sp='".$data['id_sp']."'";
		 	$response=$Feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
		 	//echo $filter;
		 	//echo var_dump($response);exit;
			 if (count($response)>0) {
			 	$datas=array('jalur_skripsi'=>'1',
			 			//'judul_skripsi'=>$mhspt['judul_skripsi'],
			 			//'bln_awal_bimbingan'=>$mhspt['bln_awal_bimbingan'],
			 			//'bln_akhir_bimbingan'=>$mhspt['bln_akhir_bimbingan'],
			 			'sk_yudisium'=>$data['no_skr'],
			 			'tgl_sk_yudisium'=>$data['tgl_skr'],
			 			//'ipk'=>$mhspt['ipk'],
			 			'no_seri_ijazah'=>$data['no_ijasah'],
			 			'id_jns_keluar'=>1,
			 			'tgl_keluar'=>$data['tgl_lulus']
			 	
			 	);
			 	$key=array(
			 			'id_reg_pd'=>$response['id_reg_pd']
			 	);
			 	 
			 	$result = $Feeder->updateToFeeder('mahasiswa_pt',$key, $datas);
			 	if ($result['result']['error_code']=="0") return $result['result']['id_reg_pd'];
			 	else return  $result['result']['error_desc'];
			 	
			 }
		 	 
		 	 
		 }
		 
		 public function updateNik($data) {
		 	 
		 	$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 
		 	$filter="trim(nipd)='".trim($data['nim'])."' and id_sms='".$data['id_sms']."'";//  and id_sp='".$data['id_sp']."'";
		 	$response=$Feeder->fnGetRecord('mahasiswa_pt.raw', $filter);
		 	//echo $filter;
		 	//echo var_dump($response);exit;
		 	if (count($response)>0) {
		 		$datas=array('nik'=>$data['nik']
		 					
		 		);
		 		$key=array(
		 				'id_pd'=>$response['id_pd']
		 		);
		 
		 		$result = $Feeder->updateToFeeder('mahasiswa',$key, $datas);
		 		if ($result['result']['error_code']=="0") return $result['result']['id_pd'];
		 		else return  $result['result']['error_desc'];
		 			
		 	}
		 	 
		 	 
		 }
		 
		 public function getAllNik($filter,$limit,$offset) {
		 	 
		 	$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$db = Zend_Db_Table::getDefaultAdapter();
		 		
		 	//$filter="id_sms='".$data['id_sms']."'";//  and id_sp='".$data['id_sp']."'";
		 	$response=$Feeder->fnGetRecordSet('mahasiswa_pt.raw', $filter,$limit,$offset);
		 	foreach ($response as $key=>$value) {
		 		$filter='id_pd="'.$value['id_pd'].'"';
		 		$mhs=$Feeder->fnGetRecord('mahasiswa.raw', $filter);
		 		if (count($mhs)>0)
		 			$response[$key]['nama']=$mhs['nm_pd'];
		 	}
		 	 return $response;
		 }
		 
		 
		 public function aktivitasMhs($idSemester,$data,$idactivity,$jnsakt) {
		 	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		 	 
		 	//get semester pelaporan
		 	$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
		 	$dbAktMhs=new Ws_Model_DbTable_AktivitasMahasiswa();
		 	$dbAnggAktMhs=new Ws_Model_DbTable_AnggotaAktivitasMahasiswa();
		 	$dbBim=new Ws_Model_DbTable_BimbingMahasiswa();
		 	$sem=$dbSem->getData($idSemester);
		 	$smtid=substr($sem['SemesterMainCode'],0,4).$sem['SemesterCountType'];
		 	
		 	if ($jnsakt=="1" || $jnsakt=="2"||$jnsakt=="3"||$jnsakt=="4") {
		 		$lstrSelect = $lobjDbAdpt->select()
		 		->from(array("a"=>"tbl_finalAssignment"),array('idActivity'=>'idFinalAssignment','a.*'))
		 		->join(array('b'=>'tbl_staffmaster'),'a.Supervisor_1=b.IdStaff',array('pembimbing1'=>'id_sdm','nama1'=>'FullName'))
		 		->joinLeft(array('c'=>'tbl_staffmaster'),'a.Supervisor_2=c.IdStaff',array('pembimbing2'=>'id_sdm','nama2'=>'FullName'))
		 		//->joinLeft(array('mhs'=>'anggota_aktivitas_mahasiswa'),'a.id_akt_mhs=mhs.id_akt_mhs')
		 		->joinLeft(array('std'=>'tbl_studentregistration'),'std.IdStudentRegistration=a.IdStudentRegistration')
		 		->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=std.IdApplication')
		 		->join(array('s'=>'tbl_semestermaster'),'s.idsemestermaster=a.SemesterMain')
		 		->join(array('prg' => 'tbl_program'), 'std.IdProgram=prg.IdProgram', array('prg.ArabicName','ProgramName','IdProgram','id_sms','kkni_level'))
		 		->where('idFinalAssignment=?',$idactivity);
		 		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		 		//echo var_dump($larrResult);exit;
		 		if ($larrResult) {
		 			if ($larrResult['kkni_level']=="5") $jnsakt="1";
		 			else if ($larrResult['kkni_level']=="6") $jnsakt="2";
		 			else if ($larrResult['kkni_level']=="8") $jnsakt="3";
		 			else if ($larrResult['kkni_level']=="9") $jnsakt="4";
		 			$data['id_smt']=$smtid;
		 			$data['id_sms']=$larrResult['id_sms'];
		 			$data['id_jns_akt_mhs']=$jnsakt;
		 			$data['judul_akt_mhs']=$larrResult['TitleBahasa'];
		 			$data['lokasi_kegiatan']=$larrResult['ArabicName'];
		 			$data['sk_tugas']=$larrResult['sk_tugas'];
		 			$data['tgl_sk_tugas']=$larrResult['tgl_sk_tugas'];
		 			$data['a_komunal']='0';  
		 			//cek exsiting data
		 			//echo $jnsakt;echo var_dump($data);exit;
		 			$sql=$lobjDbAdpt->select()
		 			->from(array('a'=>'aktivitas_mahasiswa'))
		 			->where('a.id_smt=?',$smtid)
		 			->where('a.id_sms=?',$larrResult['id_sms'])
		 			->where('a.judul_akt_mhs=?',$larrResult['TitleBahasa']);
		 			$row=$lobjDbAdpt->fetchRow($sql);
		 			if ($row) {
		 				$id=$row['id'];
		 				$dbAktMhs->update($data,'id='.$id);
		 			} else $id=$dbAktMhs->addData($data);
		 			//save supervisor
		 			if ($larrResult['pembimbing1']!="") {
			 			$data=array('id_sdm'=>$larrResult['pembimbing1'],
			 					'urutan_promotor'=>1,
			 					'id_katgiat'=>'110400',
			 					'idaktmhs'=>$id,
			 					'dt_entry'=>date('Y-m-d H:i:s')
			 			);
			 			$row=$dbBim->isIn($id, $larrResult['pembimbing1']);
			 			if ($row) $dbBim->updateData($data, $row['idbimmhs']);
			 			else $dbBim->addData($data);
		 			}
		 			if ($larrResult['pembimbing1']!=$larrResult['pembimbing2'] && $larrResult['pembimbing2']!='') {
			 			if ($larrResult['pembimbing2']!='') {
			 				$data=array('id_sdm'=>$larrResult['pembimbing2'],
				 					'urutan_promotor'=>2,
				 					'id_katgiat'=>'110400',
				 					'idaktmhs'=>$id,
				 					'dt_entry'=>date('Y-m-d H:i:s')
				 			);
				 			$row=$dbBim->isIn($id, $larrResult['pembimbing2']);
				 			if ($row) $dbBim->updateData($data, $row['idbimmhs']);
				 			else $dbBim->addData($data);
			 			}
		 			}
		 			
		 			//save mahasiswa
		 			$data=array('idaktmhs'=>$id,
		 					'nipd'=>$larrResult['registrationId'],
		 					'nm_pd'=>$larrResult['appl_fname'].' '.$larrResult['appl_mname'].' '.$larrResult['appl_lname'],
		 					'jns_peran_mhs'=>'3',
		 					'idstudentregistration'=>$larrResult['IdStudentRegistration']
		 			);
		 			$row=$dbAnggAktMhs->isIn($id, $larrResult['IdStudentRegistration']);
		 			if ($row) $dbAnggAktMhs->updateData($data, $row['id']);
		 			else $dbAnggAktMhs->addData($data);
		 			
		 			
		 			$dbfinalAss=new Finalassignment_Model_DbTable_FinalAssignment();
		 			$dbfinalAss->update(array('approved_status'=>'1'), 'idFinalAssignment='.$idactivity);
		 			
		 		}
		 		
		 	}
		 	
		 }
		 
		 public function pushAktivitasMhs($data,$idactivity,$jnsakt) {
		 	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		 	 
		 	//get semester pelaporan
		 	$Feeder=new Reports_Model_DbTable_Wsclienttbls();
		 	$dbAktMhs=new Ws_Model_DbTable_AktivitasMahasiswa();
		 	$dbExaminer=new Ws_Model_DbTable_UjiMahasiswa();
		 	$dbAnggAktMhs=new Ws_Model_DbTable_AnggotaAktivitasMahasiswa();
		 	$dbBim=new Ws_Model_DbTable_BimbingMahasiswa();
		 	 
		 	//if ($jnsakt=="1" || $jnsakt=="2"||$jnsakt=="3"||$jnsakt=="4") {
		 		$lstrSelect = $lobjDbAdpt->select()
	 				->from(array("a"=>"aktivitas_mahasiswa"),array('idActivity'=>'id','a.*'))
	 				->join(array('mpt'=>'jenis_aktivitas_mahasiswa'),'a.id_jns_akt_mhs=mpt.id_jns_akt_mhs',array('nm_jns_akt_mhs'))
	  				->join(array('prg' => 'tbl_program'), 'a.id_sms=prg.id_sms', array('prg.ArabicName','ProgramName','IdProgram','id_sms'))
	  				->where('a.id=?',$idactivity);
		 		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		 		if ($larrResult && $larrResult['sk_tugas']!='' && $larrResult['tgl_sk_tugas']!='') {
		 			 
		 			$data['id_smt']=$larrResult['id_smt'];
		 			$data['id_sms']=$larrResult['id_sms'];
		 			$data['id_jns_akt_mhs']=$jnsakt;
		 			$data['judul_akt_mhs']=$larrResult['judul_akt_mhs'];
		 			$data['lokasi_kegiatan']=$larrResult['lokasi_kegiatan'];
		 			$data['sk_tugas']=$larrResult['sk_tugas'];
		 			$data['tgl_sk_tugas']=date('Y-m-d',strtotime($larrResult['tgl_sk_tugas']));
		 			$data['a_komunal']=$larrResult['a_komunal'];
		 			$data['id_jns_akt_mhs']=$larrResult['id_jns_akt_mhs'];
		 			//cek exsiting data
		 			$filter="id_sms= '".$larrResult['id_sms']."' and id_smt='".$larrResult['id_smt']."' and judul_akt_mhs='".$data['judul_akt_mhs']."'";
		 			$response=$Feeder->fnGetRecord('aktivitas_mahasiswa.raw', $filter);
		 			//echo var_dump($response);echo $filter;
		 			//exit;
		 			if (count($response)>0) {
					//data in feeder
						$idaktmhs=$response['id_akt_mhs'];
						$dbAktMhs->update(array('id_akt_mhs'=>$idaktmhs), 'id='.$idactivity);
						$key='id_akt_mhs="'.$idaktmhs.'"';
						$Feeder->updateToFeeder('aktivitas_mahasiswa',$key ,$data);
		 			} else {
		 				$response=$Feeder->insertToFeeder('aktivitas_mahasiswa',$data);
		 				//echo var_dump($response);exit;
		 				if ($response['result']['error_code']==0) {
		 					
		 					$response=$response['result'];
		 					$idaktmhs=$response['id_akt_mhs'];
		 					$formData['error_code']= $response['error_code'] ;
		 					$formData['error_desc']= $response['error_desc'] ;
		 					$formData['id_akt_mhs']=$response['id_akt_mhs'];
		 					$lobjDbAdpt->update('aktivitas_mahasiswa',$formData,'id='.$idactivity);
		 				} else{
		 					$response=$response['result'];
		 					$formData['error_code']= $response['error_code'] ;
		 					$formData['error_desc']= $response['error_desc'] ;
		 					$lobjDbAdpt->update('aktivitas_mahasiswa',$formData,'id='.$idactivity);
		 				}
		 			}
		 			//save supervisor
		 			$spv=$dbBim->getData($idactivity);
		 			foreach ($spv as $value) {
		 				$data=array('id_sdm'=>$value['id_sdm'],
		 					'urutan_promotor'=>$value['urutan_promotor'],
		 					'id_katgiat'=>$value['id_katgiat'],
		 					'id_akt_mhs'=>$idaktmhs 
		 				); 
		 				//cek first
		 				$filter="id_sdm= '".$value['id_sdm']."' and id_akt_mhs='".$value['id_akt_mhs']."'";
		 				$response=$Feeder->fnGetRecord('bimbing_mahasiswa.raw', $filter);
		 				if (count($response)==0) {
		 					$response=$Feeder->insertToFeeder('bimbing_mahasiswa',$data);
		 					if ($response['result']['error_code']==0) 
		 						$lobjDbAdpt->update('bimbing_mahasiswa',array('id_bimb_mhs'=>$response['result']['id_bimb_mhs'],'id_akt_mhs'=>$idaktmhs),'idbimmhs='.$value['idbimmhs']);
		 				}
		 			}
		 			//save examiner
		 			$spv=$dbExaminer->getData($idactivity);
		 			foreach ($spv as $value) {
		 				$data=array('id_sdm'=>$value['id_sdm'],
		 						'urutan_uji'=>$value['urutan_uji'],
		 						'id_katgiat'=>$value['id_katgiat'],
		 						'id_akt_mhs'=>$idaktmhs
		 				);
		 				//cek first
		 				$filter="id_sdm= '".$value['id_sdm']."' and id_akt_mhs='".$value['id_akt_mhs']."'";
		 				$response=$Feeder->fnGetRecord('uji_mahasiswa.raw', $filter);
		 				if (count($response)==0) {
		 					$response=$Feeder->insertToFeeder('uji_mahasiswa',$data);
		 					if ($response['result']['error_code']==0)
		 						$lobjDbAdpt->update('uji_mahasiswa',array('id_uji_mhs'=>$response['result']['id_uji_mhs'],'id_akt_mhs'=>$idaktmhs),'iduji='.$value['iduji']);
		 				}
		 			}
		 		 	//save mahasiswa
		 		 	$std=$dbAnggAktMhs->getDataByAkt($idactivity);
		 		 	foreach ($std as $value) {
		 		 		
		 		 		$idregpd=$value['id_reg_pd'];
		 		 		if($idregpd=="") {
		 		 			$sql=$lobjDbAdpt->select()
		 		 				->from('mahasiswa_pt')
		 		 				->where('id_sms=?',$larrResult['id_sms'])
		 		 				->where('nipd=?',$value['nipd']);
		 		 			$std=$lobjDbAdpt->fetchRow($sql);
		 		 			if ($std) 
		 		 				$idregpd=$std['id_reg_pdref'];
		 		 		}   
		 		 		if ($idregpd!='') {
			 		 		$data=array('id_akt_mhs'=>$idaktmhs,
			 					'nipd'=>$value['registrationId'],
			 					'nm_pd'=>$value['nm_pd'],
			 					'jns_peran_mhs'=>$value['jns_peran_mhs'],
			 		 			'id_reg_pd'=>$idregpd
			 				);
			 		 		$filter="id_reg_pd= '".$idregpd."' and id_akt_mhs='".$idaktmhs."'";
			 		 		$response=$Feeder->fnGetRecord('anggota_aktivitas_mahasiswa.raw', $filter);
			 		 		 
			 		 		if (count($response)==0) {
			 		 			$response=$Feeder->insertToFeeder('anggota_aktivitas_mahasiswa',$data);
			 		 			
			 		 			if ($response['result']['error_code']==0) 
			 		 				$lobjDbAdpt->update('anggota_aktivitas_mahasiswa',array('id_ang_akt_mhs'=>$response['result']['id_ang_akt_mhs'],'id_reg_pd'=>$idregpd,'id_akt_mhs'=>$idaktmhs),'id='.$value['idAgg']);
			 		 			//echo var_dump($response);
			 		 		} 
			 		 		else {
			 		 			$lobjDbAdpt->update('anggota_aktivitas_mahasiswa',array('id_ang_akt_mhs'=>$response['id_ang_akt_mhs'],'id_reg_pd'=>$idregpd,'id_akt_mhs'=>$idaktmhs),'id='.$value['idAgg']);
			 		 				
			 		 		}
			 		 		//echo $value['idAgg'];exit;
		 		 		}
		 		 		
		 		   }
		 			 
		 		}
		 		 
		 	//}
		 
		 }

		  
}
?>