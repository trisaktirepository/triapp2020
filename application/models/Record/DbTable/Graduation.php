<?php 
class App_Model_Record_DbTable_Graduation extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'graduate';
	protected $_primary = "id";

public function getGraduatesNoWis($idstd=null){
	$db = Zend_Db_Table::getDefaultAdapter();
	$selectData = $db->select()
	->from(array('g'=>$this->_name))
	->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration = g.idStudentRegistration', array('registrationId', 'idIntake','sr.IdProgram','transaction_id','IdLandscape','jenis_pendaftaran','sks_diakui'))
	->join(array('sp'=>'student_profile'),'sp.appl_id = sr.IdApplication', array('appl_id','appl_fname', 'appl_mname', 'appl_lname','appl_address1','appl_address_rt','appl_address_rw','appl_city','appl_kelurahan','appl_postcode','appl_birth_place','appl_dob','appl_phone_hp','front_salutation','back_salutation','appl_religion','appl_gender'))
	->joinLeft(array('mj'=>'tbl_programmajoring'),'mj.IDProgramMajoring=sr.IdProgramMajoring',array('majoring'=>'mj.BahasaDescription','majoring_english'=>'mj.EnglishDescription'))
	->join(array('pr'=>'tbl_program'), 'pr.IdProgram = sr.IdProgram', array('programEng'=>'pr.ProgramName','program_name'=>'ArabicName','ProgramCode','Departement','Dept_Bahasa','strata','print_majoring','transcript_template','certificate_template'))
	->joinLeft(array('def'=>'tbl_definationms'),'def.idDefinition=pr.Award',array('program_pendidikan'=>'BahasaIndonesia','program_eng'=>'description'))
	->join(array('cl'=>'tbl_collegemaster'),'cl.IdCollege=pr.IdCollege',array('idCol'=>'pr.IdCollege','CollegeName','CollegeBahasa'=>'ArabicName'))
	->join(array('def1'=>'tbl_definationms'),'def1.idDefinition=pr.programSalutation',array('gelar'=>'BahasaIndonesia','gelarP'=>'DefinitionDesc','gelarEng'=>'Description'))
	->join(array('itk' => 'tbl_intake'), 'itk.IdIntake = sr.idIntake', array('intake_name'=>'IntakeDefaultLanguage','IntakeId'))
	->joinLeft(array('dean'=>'graduation_skr'),'g.dean_approval_skr=dean.id',array('date_of_Yudisium'=>'dean.date_of_skr','dean_sk'=>'dean.skr'))
	->joinLeft(array('skr'=>'graduation_skr'),'g.rector_approval_skr=skr.id',array('date_of_skr'=>'skr.date_of_skr','skr.skr','IdSemesterMain'))
	->where('sr.idStudentRegistration=?',$idstd)
	->order('left(IntakeId,4) DESC')
	->order('itk.period DESC')
	->order('sr.registrationid ASC');
 
	$row = $db->fetchRow($selectData);
	 
	return $row;
}

public function update($data,$id) {
	$db = Zend_Db_Table::getDefaultAdapter();
	$db->update($this->_name,$data,'idStudentRegistration='.$id);
}
}

?>