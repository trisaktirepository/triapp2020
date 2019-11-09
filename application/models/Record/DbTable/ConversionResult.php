<?php
class App_Model_Record_DbTable_ConversionResult extends Zend_Db_Table_Abstract {
    
	protected $_name = 'tbl_conversion_result';
	protected $_primary='IdConversionResult';
	
    public function getStudentList($search){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from(array('a'=>'tbl_studentregistration'), array('value'=>'concat(a.registrationId, " - ",b.appl_fname, " ", b.appl_lname)', 'label'=>'concat(a.registrationId, " - ",b.appl_fname, " ", b.appl_lname)', 'id'=>'a.IdStudentRegistration'))
            ->join(array('b'=>'student_profile'), 'a.IdApplication = b.appl_id')
            //->where('a.profileStatus = ?', 92)
            ->where('concat(a.registrationId, " - ",b.appl_fname, " ", b.appl_lname) like "%'.$search.'%"');
        
        $result = $db->fetchAll($select);
        return $result; 
    }
    public function getConversionResult($id,$idmain){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
    	->distinct()
    	->from(array('c'=>'tbl_conversion_result'),array('grade_point'=>'Grade_point_new','grade_name'=>'Grade_name_new','final_mark'=>'Final_mark_new'))
    	->joinLeft(array('smnew'=>'tbl_subjectmaster'),'c.IdSubjectNew=smnew.IdSubject',array('smnew.IdSubject','SubCodeNew'=>'smnew.ShortName','SubjectNameNew'=>"smnew.BahasaIndonesia",'CreditHoursNew'=>'smnew.CreditHours'))
    	->where('c.IdStudentRegistration = ?', $id)
    	->where('c.IdConversionMain=?',$idmain)
    	->order('c.Status DESC')
    	->order('c.Group');
    
    	$result = $db->fetchAll($select);
    	return $result;
    }
    
    public function getApplicantList($search){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from(array('a'=>'applicant_transaction'), array('value'=>'concat(a.at_pes_id, " - ",b.appl_fname, " ", b.appl_lname)', 'label'=>'concat(a.at_pes_id, " - ",b.appl_fname, " ", b.appl_lname)', 'id'=>'a.at_trans_id'))
            ->join(array('b'=>'applicant_profile'), 'a.at_appl_id = b.appl_id')
            ->join(array('c'=>'applicant_program'), 'a.at_trans_id = c.ap_at_trans_id')
            ->where('concat(a.at_pes_id, " - ",b.appl_fname, " ", b.appl_lname) like "%'.$search.'%"');
        
        $result = $db->fetchAll($select);
        return $result;
    }
    
    public function getIntakeList(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from(array('a'=>'tbl_intake'), array('name'=>'concat(a.IntakeId, " - ", a.IntakeDesc)', 'id'=>'a.IdIntake'))
        ->order('a.IntakeId DESC');
        
        $result = $db->fetchAll($select);
        return $result;
    }
    
    public function getSemesterList(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from(array('a'=>'tbl_semestermaster'), array('name'=>'concat(SemesterMainCode, " - ", a.SemesterMainName)', 'id'=>'a.IdSemesterMaster'));
        
        $result = $db->fetchAll($select);
        return $result;
    }
    
    public function addConversionResult($bind){
        $db = Zend_Db_Table::getDefaultAdapter();
       // echo var_dump($bind);exit;
        $id=$this->insert($bind);
        
        return $id;
    }
    
    public function getData($id,$idmain){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from(array('c'=>'tbl_conversion_result'))
            ->join(array('sm'=>'tbl_subjectmaster'),'c.IdSubject=sm.IdSubject',array('SubCode'=>'sm.ShortName','SubjectName'=>"sm.BahasaIndonesia",'sm.CreditHours'))
    		->join(array('smnew'=>'tbl_subjectmaster'),'c.IdSubjectNew=smnew.IdSubject',array('SubCodeNew'=>'smnew.ShortName','SubjectNameNew'=>"smnew.BahasaIndonesia",'CreditHoursNew'=>'smnew.CreditHours'))
            ->where('c.IdStudentRegistration = ?', $id)
        	->where('c.IdConversionMain=?',$idmain)
        	->order('c.Status DESC')
        	->order('c.Group');
        
        $result = $db->fetchAll($select);
        return $result;
    }
    
    public function getRecord($id){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
    	->from(array('c'=>'tbl_conversion_result'))
    	//->join(array('sm'=>'tbl_subjectmaster'),'c.IdSubject=sm.IdSubject',array('SubCode'=>'sm.ShortName','SubjectName'=>"sm.BahasaIndonesia",'sm.CreditHours'))
    	->join(array('smnew'=>'tbl_subjectmaster'),'c.IdSubjectNew=smnew.IdSubject',array('SubCodeNew'=>'smnew.ShortName','SubjectNameNew'=>"smnew.BahasaIndonesia",'CreditHoursNew'=>'smnew.CreditHours'))
    	->where('c.IdConversionResult = ?', $id)
    	->where('c.Status!="0-0"');
    	$result = $db->fetchRow($select);
    	return $result;
    }
    
    public function updateConversionResult($bind, $id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $update =$this->update($bind, $this->_primary.'='.$id);
        return $update;
    }
    
    public function getStudent($postData){
    	
    	
    	$auth = Zend_Auth::getInstance();
    
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
    	->from(array('a'=>'tbl_studentregistration'), array('IdStudentRegistration','registrationId','IdLandscape','IdProgramMajoring','IdProgram','IdBranch'))
    	->join(array('mj'=>'tbl_programmajoring'),'a.IdProgramMajoring=mj.IDProgramMajoring',array('majoring'=>'BahasaDescription'))
    	->join(array('st'=>'student_profile'),'st.appl_id=a.IdApplication',array('StudentName'=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)'));
    	if ($postData['tbr_programfor']!='') $select->where('a.IdProgram=?',$postData['tbr_programfor']);
    	if ($postData['tbr_branchfor']!='') $select->where('a.IdBranch=?',$postData['tbr_branchfor']);
    	//if ($postData['tbr_majorfor']!='') $select->where('a.IdProgramMajoring=?',$postData['tbr_majorfor']);
    	if ($postData['tbr_intakefor']!='') $select->where('a.IdIntake=?',$postData['tbr_intakefor']);
    	if ($postData['tbr_appstud_id']!='') $select->where('a.registrationid in (?)',$postData['tbr_appstud_id']);
    	$result = $db->fetchAll($select);
    	if ($postData['tbr_ch']!='' && $postData['tbr_ch']==0)  $postData['tbr_ch']=144;
    	$dbGrade=new Examination_Model_DbTable_StudentGrade();
    	//gel highest level for each student
    	foreach ($result as $key=>$value) {
			$gradestatus=$dbGrade->getStudentGradeInfo($value['IdStudentRegistration']);//get grade;
    		if ($gradestatus['sg_cum_credithour'] >= $postData['tbr_ch']) {
    			unset($result[$key]);
    		} else {
    			$result[$key]['CreditHours']=$gradestatus['sg_cum_credithour'];
    			$result[$key]['CGPA']=$gradestatus['sg_cgpa'];
    		}
    	}
    	
    	//echo $select;exit;
    	return $result;
    }
    public function getStudentResultList($postdata){
    	
    	$session = new Zend_Session_Namespace('sis');
        $auth = Zend_Auth::getInstance();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
        	->distinct()
            ->from(array('a'=>'tbl_conversion_result'), array())
			->join(array('st'=>'tbl_studentregistration'),'a.IdStudentRegistration=st.IdStudentRegistration',array('registrationId','IdStudentRegistration'))
			->join(array('pm'=>'tbl_programmajoring'),'pm.IDProgramMajoring=st.IdProgramMajoring',array('majoring'=>'BahasaDescription'))
            ->join(array('sp'=>'student_profile'),'sp.appl_id=st.idapplication',array('StudentName'=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)','appl_address1','appl_phone_hp','appl_email'))
        	->where('a.IdConversionMain=?',$postdata['IdConversionMain']);
        	if ($postdata['tbr_appstud_id']!='') $select->where('st.IdIntake =?',$postdata['tbr_intakefor']);
		 	if ($postdata['tbr_appstud_id']!='') $select->where('st.registrationid in (?)',$postdata['tbr_appstud_id']);
		 	if ($postdata['tbr_appstud_name']!='') $select->where('CONCAT(sp.appl_fname,sp.appl_mname,sp.appl_lname) like  "%?%"',$postdata['tbr_appstud_name']);

	      
        
       // echo $select;exit;
        $result = $db->fetchAll($select);
        return $result;
    }
    
    public function deleteConversionResul($id){
       
        $delete = $this->delete($this->_primary."=".$id);
        return $delete;
    }
    
    public function deleteConversionResult($idreg,$idmain){
    	 
    	$delete = $this->delete('IdStudentRegistration='.$idreg.' and IdConversionMain='.$idmain);
    	return $delete;
    }
    
    public function getDataById($id){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
    	->from(array('c'=>'tbl_conversion_result'))
    	->where('c.IdConversionResult = ?', $id);
    	$result = $db->fetchRow($select);
    	return $result;
    }
    public function getDataByIdSubject($idstd,$idsubject,$idmain){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
    	->from(array('c'=>'tbl_conversion_result'))
    	->where('c.IdStudentRegistration = ?', $idstd)
    	->where('c.IdSubject = ?', $idsubject)
    	->where('c.IdConversionMain = ?', $idmain);
    	$result = $db->fetchRow($select);
    	return $result;
    }
}