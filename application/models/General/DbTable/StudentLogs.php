<?php 
class App_Model_General_DbTable_StudentLogs extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_student_logs';
	protected $_primary = "log_id";

	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	
	public function logData($data){
		
		    $auth = Zend_Auth::getInstance();  
		    
			//track student log
			$log['IdStudentRegistration']=$data['IdStudentRegistration'];
			$log['log_description']=$data['log_description'];
			$log['log_activity_date']=date("Y-m-d H:i:s");
			$log['log_activity_by']=$auth->getIdentity()->id;
			$log['IdStudentRegSubjects']=$data['IdStudentRegSubjects'];
			
			$this->insert($log);
	}
}