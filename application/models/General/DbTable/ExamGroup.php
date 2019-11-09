<?php
class App_Model_General_DbTable_ExamGroup extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_group';
	protected $_primary = "eg_id";
    
    public function getExamSchedule($semester_id, $subject_id,$program_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $sql = $db->select()
                ->distinct()
                ->from(array('eg' => $this->_name))
                ->joinLeft(array('egp' => 'exam_group_program'), 'egp.egp_eg_id = eg.eg_id')
                ->where('eg.eg_assessment_type IN (38,40)')
                ->where('eg.eg_sem_id = ?',(int)$semester_id)
                ->where('eg.eg_sub_id = ?',(int)$subject_id)
                ->where('egp.egp_program_id = ?',(int)$program_id)
				->group('eg.eg_assessment_type');
        
        $rows = $db->fetchAll($sql);
        
        return $rows;
    }
    
}
?>