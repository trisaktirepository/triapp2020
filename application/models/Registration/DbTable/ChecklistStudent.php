<?php 
class App_Model_Registration_DbTable_ChecklistStudent extends Zend_Db_Table_Abstract
{
	
	/**
	 * The default table name 
	 */
	
	protected $_name = 'registration_checklist_student';
	protected $_primary = "rcstd_id";
	
	public function getStudentData($studentRegistrationId=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db ->select()
					  ->from(array('rcs'=>$this->_name))
					  ->joinLeft(array('tu'=>'tbl_user'), 'tu.iduser = rcs.rcstd_create_by', array())
					  ->joinLeft(array('ts'=>'tbl_staffmaster'), 'ts.IdStaff = tu.IdStaff', array('rcstd_create_by_name'=>'Fullname'))
					  ->joinLeft(array('tu2'=>'tbl_user'), 'tu2.iduser = rcs.rcstd_update_by', array())
					  ->joinLeft(array('ts2'=>'tbl_staffmaster'), 'ts2.IdStaff = tu2.IdStaff', array('rcstd_update_by_name'=>'Fullname'))
					  -join(array('rc'=>'registration_checklist_setup'), 'rc.rcs_id = rcs.rcstd_rcs_id', array('rcs_name'))
					  ->where('rcs.rcstd_idStudentRegistration = ?',$studentRegistrationId)
		              ->order("rc.rcs_name");
	
		
		$row = $db->fetchAll($select);
		
		return $row;
	}
	
	public function getChecklistStudentData($studentRegistrationId=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db ->select()
		->from(array('rcs'=>'registration_checklist_setup'))
		 
		->order("rcs.rcs_name");
	
	
		$row = $db->fetchAll($select);
		if ($row) {
			foreach ($row as $key=>$value) {
				$select = $db ->select()
				->from(array('rcstd'=>'registration_checklist_student'))
				->where('rcstd.rcstd_idStudentRegistration =?',$studentRegistrationId)
				->where('rcstd.rcstd_rcs_id =?',$value['rcs_id']);
				$rowset = $db->fetchRow($select);
				if ($rowset) {
					$row[$key]['rcstd_status']="1";
					$row[$key]['rcstd_id']=$rowset['rcstd_id'];
				} else {
					$row[$key]['rcstd_status']='';
					$row[$key]['rcstd_id']='';
				}
			}
		}
		//echo var_dump($row);exit;
		return $row;
	}
	
	public function insert(array $data){
	
		$auth = Zend_Auth::getInstance();
	
		$data['rcstd_create_by'] = $auth->getIdentity()->appl_id;
		$data['rcstd_create_date'] = date('Y-m-d H:i:s');
			
		return parent::insert($data);
	}
	
	
	public function update(array $data,$where){
	
		$auth = Zend_Auth::getInstance();
		
		$data['rcstd_update_by'] = $auth->getIdentity()->appl_id;
		$data['rcstd_update_date'] = date('Y-m-d H:i:s');
	
		return parent::update($data, $where);
	}
}