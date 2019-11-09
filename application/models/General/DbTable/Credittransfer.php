<?php
class App_Model_General_DbTable_Credittransfer extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_credittransfersubjects';
	private $lobjDbAdpt;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
    
	public function insert($data)
    {
        $this->lobjDbAdpt->insert($this->_name,$data);
    }
    
    public function update($data,$key)
    {
    	$this->lobjDbAdpt->update($this->_name,$data,$key);
    }
    
    public function deleteApply($idCreditApply)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $where = $db->quoteInto('IdCreditTransferApply= ?', $idCreditApply);
        $db->delete($this->_name, $where);
    }
    
    public function delete($idCreditsubjects)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$where = $db->quoteInto('IdCreditTransferSubjects= ?', $idCreditsubjects);
    	$db->delete($this->_name, $where);
    }
    
    public function isIn($idCreditApply,$idsubject)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	$select = $db->select()
    	->from($this->_name, array(
                'IdCreditTransferApply' => 'IdCreditTransferApply',
            	'IdCreditTransferSubjects'=>'IdCreditTransferSubjects',
                'CourseCode_apply' => 'EquiCourseCode',
            	'Course_apply' => 'EquiCourse',
            	'CH_apply' => 'EquiCreditHour',
            	'Grade_apply' => 'EquiGrade',
               // 'grade_approve' => 'grade_approve',
                'IdSubject' => 'IdSubject'
            ))
    	->where('IdCreditTransferApply =?',(int)$idCreditApply)
    	->where('IdSubject=?',$idsubject);
   // echo $select;exit;
    	$result = $db->fetchRow($select);
    	return $result;
    }
    
    public function getData($idCreditApply,$returnType = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
       $select = $db->select()
    	->from(array('a'=>$this->_name))
    	->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubject',array('apply_subject'=>'sm.BahasaIndonesia','apply_ch'=>'sm.CreditHours','apply_code'=>'sm.ShortName'))
    	->joinLeft(array('sd'=>'tbl_subjectmaster'),'sd.IdSubject=a.EquivalentCourse',array('dean_subject'=>"CONCAT(sd.BahasaIndonesia,'-',sd.CreditHours,' sks ')",'deansubject'=>"sd.BahasaIndonesia",'dean_ch'=>'sd.CreditHours','dean_code'=>'sd.ShortName'))
    	->joinLeft(array('rec'=>'tbl_subjectmaster'),'a.ApprovedSubject=rec.IdSubject',array('rec_idsubject'=>'rec.IdSubject','rec_subject'=>'rec.BahasaIndonesia','rec_ch'=>'rec.CreditHours','rec_code'=>'rec.ShortName'))
    	->where('IdCreditTransferApply =?',(int)$idCreditApply);
    
    	$result = $db->fetchAll($select);
    	$data=array();
    	foreach ($result as $key => $value)
    	{
    
    		$data[$value['IdSubject']] = array(
    				'IdCreditTransferApply' => $value['IdCreditTransferApply'],
    				'IdCreditTransferSubjects'=>$value['IdCreditTransferSubjects'],
    				'CourseCode_apply' => $value['EquiCourseCode'],
    				'Course_apply' => $value['EquiCourse'],
    				'CH_apply' => $value['EquiCreditHour'],
    				'Grade_apply' => $value['EquiGrade'],
    				'IdSubject' => $value['IdSubject'],
    				'Subjectname'=>$value['apply_subject'],
    				'CHapply'=>$value['apply_ch'],
    				'dean_subject'=>$value['dean_subject'],
    				'GradeApproved'=>$value['EquivalentGrade'],
    				'rec_subject'=>$value['rec_subject'],
    				'rec_idsubject'=>$value['rec_idsubject'],
    				'rec_ch'=>$value['rec_ch'],
    				'rec_Grade'=>$value['ApprovedGrade'],
    				'IdSubjectApproved'=>$value['EquivalentCourse']
    		);
    
    	}
    
    	if($returnType == 'massage')
    	{
    		$result = $data;
    	}
    	return $result;
    }
}
?>
