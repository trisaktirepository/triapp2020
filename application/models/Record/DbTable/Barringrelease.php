<?php
class App_Model_Record_DbTable_Barringrelease extends Zend_Db_Table_Abstract {
    
    
    public function getBarringStudent($idregister,$idsemester){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from(array('a'=>'tbl_barringrelease'), array('value'=>'*'))
            ->joinLeft(array('d'=>'applicant_transaction'), 'a.tbr_appstud_id = d.at_trans_id')
            ->joinLeft(array('e'=>'applicant_profile'), 'd.at_appl_id = e.appl_id', array('appName'=>'concat(d.at_pes_id, " - ",e.appl_fname, " ", e.appl_lname)'))
            ->joinLeft(array('f'=>'tbl_studentregistration'), 'a.tbr_appstud_id = f.IdStudentRegistration')
            ->joinLeft(array('g'=>'student_profile'), 'f.IdApplication = g.appl_id', array('studName'=>'concat(f.registrationId, " - ",g.appl_fname, " ", g.appl_lname)'))
			->join(array('def'=>'tbl_definationms'),'def.IdDefinition=a.tbr_type',array('type'=>'BahasaIndonesia'))
            ->where('a.tbr_category = "2"')
            ->where('a.tbr_intake = ?', $idsemester)
        	->where('a.tbr_appstud_id = ?', $idregister);
        //echo $select;exit;
        $result = $db->fetchAll($select);
        return $result;
    }
    
    public function isIn($idregister,$idsemester){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
    	->from(array('a'=>'tbl_barringrelease'), array('value'=>'*'))
    	->where('a.tbr_category = "2"')
    	->where('a.tbr_intake = ?', $idsemester)
    	->where('a.tbr_appstud_id = ?', $idregister);
    	//echo $select;exit;
    	$result = $db->fetchRow($select);
    	return $result;
    }
    public function getBarringApplicant($idregister,$idsemester){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
    	->from(array('a'=>'tbl_barringrelease'), array('value'=>'*'))
    	->joinLeft(array('d'=>'applicant_transaction'), 'a.tbr_appstud_id = d.at_trans_id')
    	->joinLeft(array('e'=>'applicant_profile'), 'd.at_appl_id = e.appl_id', array('appName'=>'concat(d.at_pes_id, " - ",e.appl_fname, " ", e.appl_lname)'))
    	->joinLeft(array('f'=>'tbl_studentregistration'), 'a.tbr_appstud_id = f.IdStudentRegistration')
    	->joinLeft(array('g'=>'student_profile'), 'f.IdApplication = g.appl_id', array('studName'=>'concat(f.registrationId, " - ",g.appl_fname, " ", g.appl_lname)'))
    	->where('a.tbr_category = "1"')
    	->where('a.tbr_intake = ?', $idsemester)
    	->where('a.tbr_appstud_id = ?', $idregister);
    
    	$result = $db->fetchAll($select);
    	return $result;
    }
    
    
}