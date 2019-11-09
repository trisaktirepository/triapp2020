<?php 
class Finalassignment_Model_DbTable_Supervision extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_Supervision';
	protected $_primary='IdTASupervision';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		 
			
		return $this->insert($postData);
	}
	
	 
	
	public function updateData($postData,$id){
		return $this->update($postData,$this->_primary . " = " . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function updateFile($postData,$id){
		//echo var_dump($postData);exit;
		$this->update($postData,$id);
		$supervision =$this->getData($id);
		//check syarat n of session
		$auth = Zend_Auth::getInstance();
		if ($supervision['Percent_progress']==100) {
			$dbsuper=new Finalassignment_Model_DbTable_Supervisor();
			$dbsuper->finish($supervision['IdTAApplication'], $supervision['IdStaff']);
			$dbApproval=new Finalassignment_Model_DbTable_Approval();
			$approval=$dbApproval->getApprovalId($supervision['IdStaff'], $supervision['IdTAApplication']);
			if ($approval) {
				//
				$dbFlow=new Finalassignment_Model_DbTable_Flow();
				$flow=$dbFlow->getData($approval['IdTAFlow']);
				//echo var_dump($flow);exit;
				$data=array('remark'=> 'update from 100% student achievement',
				'approvalsts' => '1',
				'IdTAFlow'=>$flow['IdTAFlow'],
				'IdTAFlowMain'=>$flow['IdTAFlowMain'],
				'Sequence'=>$approval['Sequence'],
				'IdTAApplication'=>$approval['IdTAApplication'],
				'Approved_by'=>$approval['Approved_by'],
				'Id_User' =>  $auth->getIdentity()->iduser);
				$dbApproval->updateData($data, $approval['IdTAApproval']);
			}
			
		}
		//if ($this->isAllSupervisorFinish($supervision['IdTAApplication'])) {
			//finish Application
			//echo $supervision['IdTAApplication'];exit;
			//$dbApp=new Finalassignment_Model_DbTable_Application();
		//	$dbApp->finish($supervision['IdTAApplication']);
		//}
	}
	
	public function getData($id){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		//->join(array('app'=>'tbl_TA_Application'),'p.StatusAs=def.idDefinition',array('As'=>'BahasaIndonesia','Aseng'=>'DefinitionDesc'))
 		->join(array('s'=>'tbl_staffmaster'), 's.IdStaff=p.IdStaff',array('Fullname','FrontSalutation','BackSalutation','StaffId'))
		->where('p.IdTASupervision = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function countSupervision($idtaapplication){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name),array('count'=>'COUNT(*)'))
		->where('p.IdTAApplication = ?', $idtaapplication);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult['count'];
	}
	
	public function isSupervisionFullFill($idtaapplication,$minimum){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name),array('count'=>'COUNT(*)'))
		->where('p.IdTAApplication = ?', $idtaapplication);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		if ($larrResult['count'] >= $minimum) return true; else return false;
	}
	
	
	public function isAllSupervisorFinish($id){
	
		
		$dbsupv=new Finalassignment_Model_DbTable_Supervisor();
		$supervisor=$dbsupv->getAllSupervisor( $id);
		$finish='';
		foreach ($supervisor as $item) {
			$idStaff=$item['IdStaff'];
			
			if ($dbsupv->isFinish($id, $idStaff))  {
				  $finish='1';
			} else $finish='0';
		}
		if ($finish==1) return true; else return false;
	}
	public function getSupervisionByStaff($idTAApplication,$idStaff){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('sv'=>'tbl_TA_Supervisor'),'p.IdTAApplication=sv.IdTAApplication and p.IdStaff=sv.IdStaff',array('StaffAs'))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=p.chapter',array('ChapterName'=>'BahasaIndonesia'))
		->join(array('s'=>'tbl_staffmaster'), 's.IdStaff=p.IdStaff',array('Fullname','FrontSalutation','BackSalutation','StaffId'))
		->where('p.IdTAApplication= ?', $idTAApplication)
		->where('sv.IdStaff= ?', $idStaff)
		->order('p.dt_entry DESC'); 
		$larrResult =$this->lobjDbAdpt->fetchAll($lstrSelect);
		//echo var_dump($larrResult);exit;
		return $larrResult;
	}
	public function getProgressByStudent($idTAApplication,$idstd){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name),array('Percent_progress'))
		->join(array('app'=>'tbl_TA_Application'),'app.IdTAApplication=p.IdTAApplication',array())
		->join(array('sv'=>'tbl_TA_Supervision'),'p.IdTAApplication=sv.IdTAApplication',array())
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=p.chapter',array('ChapterName'=>'BahasaIndonesia'))
		->where('p.IdTAApplication= ?', $idTAApplication)
		->where('app.IdStudentRegistration= ?', $idstd)
		->order('p.Percent_progress DESC');
		$larrResult =$this->lobjDbAdpt->fetchRow($lstrSelect);
		if ($larrResult) {
			return $larrResult['Percent_progress'];
		}
		else return null;
	}
	
	public function fnGetChapterName() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Report Chapter"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	
	 
}

?>