<?php

class Counseling_Model_Issues extends Zend_Db_Table
{

    protected $_name = 'counseling_issues';
    protected $_primary = 'id';

    /**
     * belongsTo
     */
    protected $_referenceMap    = array(
        'Student' => array(
            'columns'           => 'IdStudentRegistration',
            'refTableClass'     => 'App_Model_Registration_DbTable_Studentregistration',
            'refColumns'        => 'IdStudentRegistration'
        ),
        'Advisor' => array(
            'columns'           => 'answered_by',
            'refTableClass'     => 'App_Model_General_DbTable_Staffmaster',
            'refColumns'        => 'IdStaff'
        ),
        'ProblemType' => array(
            'columns'           => 'problem_id',
            'refTableClass'     => 'Counseling_Model_IssueType',
            'refColumns'        => 'id'
        )
    );


    public function save(array $data)
    {

        if (isset($data['id'])) {
            $where = $this->getAdapter()->quoteInto('id = ?', $data['id']);
            $this->update($data, $where);
            $id = $data['id'];
        } else {
            $id = $this->insert($data);
        }

        return $id;
    }

    public function del($id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->delete($where);
        return (true);
    }

    /**
     * returns all issues by students
     * @param $IdStudentRegistration
     */
    public function getAllByStudent($IdStudentRegistration, $semesterid,$include_inactive = false)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
        	->from(array('a'=>'counseling_issues'))
        	->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=a.IdStudentRegistration')
        	->join(array('sp'=>'student_profile'),'sp.appl_id=sr.IdApplication',array('StudentName'=>'CONCAT(appl_fname," ",appl_lname)'))
        	->join(array('t'=>'counseling_issue_types'),'t.IdCounselingCategory=a.problem_id', array('name'))
        	 
            ->where('sr.IdStudentRegistration = ?', $IdStudentRegistration)
            ->where('a.IdSemesterMain=?',$semesterid)
            ->order('a.created_at DESC');
 
        $issues = $db->fetchAll($select);
        return ($issues);
    }

    /**
     * returns all that is answered by supervisor
     * @param $supervisor_id
     */
    public function getAnsweredBySupervisor($supervisor_id)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	 
        $select = $db->select()
        	->from(array('i'=>'counseling_issues'))
        	->join(array('sr'=>'tbl_studentregistration'),'i.IdStudentRegistration=sr.IdStudentRegistration')
           	->join(array('sp'=>'student_profile'),'sp.appl_id=sr.IdApplication',array('StudentName'=>'CONCAT(appl_fname," ",appl_lname)'))
			->join(array('t'=>'counseling_issue_types'),'t.id=i.problem_id')
            ->where('sr.AcademicAdvisor = ?', $supervisor_id)
            ->order('appt_dt DESC');

         

        $issues = $this->fetchAll($select);
        return ($issues);

    }

    public function getActive()
    {
        $select = $this->select()
                    ->where('status =?', 1);
        $issues = $this->fetchAll($select);
        return($issues);
    }

}