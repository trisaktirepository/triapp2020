<?php

class Counseling_Model_IssuesDetail extends Zend_Db_Table
{

    protected $_name = 'counseling_issue_detail';
    protected $_primary = 'idCounselingIssueDetail';

    /**
     * belongsTo
     */
     
    static public function get_status() {
        $translator = Zend_Registry::get('Zend_Translate');
        $adapter = $translator->getAdapter();
        $status = array(
            1 => $adapter->translate('new'),
            2 => $adapter->translate('replied'),
            3 => $adapter->translate('closed')
        );

        return($status);
    }

    public function update(array $data,$id)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
        
            $where = 'IdCounselingIssueDetail ='. $id;
                     
            $db->update($this->_name,$data, $where);
      
        return $id;
    }
    
    public function add($data)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	 
    		$id = $db->insert($this->_name,$data);
    	 
    
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
    public function getByIssueId($idIssue)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	
    	$select = $db->select()
    		->from(array('Id'=>'counseling_issue_detail'))
    		->joinLeft(array('act'=>'counseling_action'),'act.IdAction=Id.action',array('actionname'=>'ActionName','ActionCode'))
            ->where('IdCounselingIssue = ?', $idIssue)
            ->order('dt_answered DESC');
 
        $issues = $db->fetchAll($select);
       
        return ($issues);
    }

    /**
     * returns all that is answered by supervisor
     * @param $supervisor_id
     */
    public function getAnsweredBySupervisor($supervisor_id, $include_inactive = false)
    {
        $select = $this->select()
            ->where('answered_by = ?', $supervisor_id)
            ->order('answered_at DESC');

        if ($include_inactive == false) {
            $select->where('status = ?', 1);
        }

        $issues = $this->fetchAll($select);
        return ($issues);

    }

    public function getSupervised($supervisor_id, $include_inactive = false) {

    	$db = Zend_Db_Table::getDefaultAdapter();
 		$select = $db->select()
        			->from(array('a'=>$this->_name))
        			->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=a.IdStudentRegistration',array('registrationId'))
        			->join(array('ap'=>'applicant_profile'),'ap.appl_id=sr.IdApplication',array("stdname"=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)'))
        			->join(array('pr'=>'tbl_program'),'pr.IdProgram=sr.IdProgram',array())
        			->join(array('cit'=>'counseling_issue_types'),'cit.id=a.problem_id',array('problemtype'=>'name'))
        			->joinLeft(array('st'=>'tbl_staffmaster'),'st.IdStaff=sr.AcademicAdvisor',array('FullName'=>"CONCAT(FirstName,' ',SecondName,' ',ThirdName)"))
                   ->join(array('usr'=>'tbl_user'),'usr.idstaff=st.idstaff')
                    ->where('usr.iduser=?',$supervisor_id)
        			->order('a.created_at DESC');
            
        if ($include_inactive == false) {
            $select->where('a.status = ?', 1);
        }
      //  echo $select;exit;
        $issues = $db->fetchAll($select);
       
        return ($issues);
    }

    public function getActive()
    {
    	$session = new Zend_Session_Namespace('sis');
    	$db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
        			->from(array('a'=>$this->_name))
        			->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=a.IdStudentRegistration')
        			->join(array('ap'=>'applicant_profile'),'ap.appl_id=sr.IdApplication',array("stdname"=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)'))
        			->join(array('pr'=>'tbl_program'),'pr.IdProgram=sr.IdProgram')
        			->join(array('cit'=>'counseling_issue_types'),'cit.id=a.problem_id',array('problemtype'=>'name'))
        			->joinLeft(array('st'=>'tbl_staffmaster'),'st.IdStaff=sr.AcademicAdvisor',array('FullName'=>"CONCAT(FirstName,' ',SecondName,' ',ThirdName)"))
                    ->where('a.status =?', 1)
        			->order('a.created_at DESC');

        
        	if($session->IdRole == 605 || $session->IdRole == 311 || $session->IdRole == 298){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
        		$select->where("pr.IdCollege='".$session->idCollege."'");
        	}
        	if($session->IdRole == 470 || $session->IdRole == 480){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
        		$select->where("pr.IdProgram='".$session->idDepartment."'");
        	}
        
        $issues = $db->fetchAll($select);
        return($issues);
    }
    public function getViewActive($id)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
    	->from(array($this->_name))
    	->where('status =?', 1)
    	->where('id =?', $id);
    	$issues = $db->fetchRow($select);
    	return($issues);
    }
    public function isPermit($idstudentregistration,$idsemester)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
    	->from(array('a'=>$this->_name))
    	->join(array('IS'=>'counseling_issues'),'a.IdCounselingIssue=IS.id')
    	->join(array('act'=>'counseling_action'),'a.action=act.IdAction')
    	->where('IS.IdSemesterMain=?',$idsemester)
    	->where('IS.IdStudentRegistration=?',$idstudentregistration)
    	->where('act.ActionCode="01"')
    	->where('act.IdCounselingCategory="1"');
    	$issues = $db->fetchRow($select);
    	if ($issues) return true; else return false;
    }

}