<?php
class Examination_Model_DbTable_Marksdistributiondetails extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_marksdistributiondetails';
	protected $_primary = 'IdMarksDistributionDetails';

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}


	public function fnGetMarksDistributionList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_marksdistributionmaster'),array("key"=>"a.IdMarksDistributionMaster","value"=>"a.Name"))
		->where('a.Active = 1')
		->order("a.Name");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fnGetProgramList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_program'),array("key"=>"a.IdProgram","value"=>"a.ProgramName"))
		->where('a.Active = 1')
		->order("a.ProgramName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fngetMarksDistributionMasterDetails() { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("mdm"=>"tbl_marksdistributionmaster"))
		->join(array('p' => 'tbl_program'),'mdm.IdProgram  = p.IdProgram')
		->where('mdm.Active = 1')
		->group("mdm.Name")
		->order("mdm.Name");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetsemesterdetails(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("sa"=>"tbl_semestermaster"),array("key"=>"b.IdSemester","value"=>"CONCAT_WS(' ',IFNULL(sa.SemesterMainName,''),IFNULL(b.year,''))"))
		->join(array('b'=>'tbl_semester'),'sa.IdSemesterMaster = b.Semester')
		->where("sa.Active = 1");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetstaffdetails(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("sa"=>"tbl_staffmaster"),array("key"=>"sa.IdStaff","value"=>"CONCAT_WS(' ',IFNULL(sa.FirstName,''),IFNULL(sa.SecondName,''),IFNULL(sa.ThirdName,''),IFNULL(sa.FourthName,''))"));
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetCourseTypeList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_coursetype'),array("key"=>"a.IdCourseType","value"=>"a.CourseType"))
		->where('a.Active = 1')
		->order("a.CourseType");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnSearchMarksDistributionMasterDetails($post = array()) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("mdm"=>"tbl_marksdistributionmaster"))
		->join(array('p' => 'tbl_program'),'mdm.IdProgram  = p.IdProgram');
			
		if(isset($post['field5']) && !empty($post['field5']) ){
			$lstrSelect = $lstrSelect->where("mdm.IdMarksDistributionMaster = ?",$post['field5']);

		}
		if(isset($post['field8']) && !empty($post['field8']) ){
			$lstrSelect = $lstrSelect->where("mdm.IdProgram = ?",$post['field8']);

		}

		$lstrSelect	->where('mdm.Active = 1')
		->group("mdm.Name")
		->order("mdm.Name");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetProgramNameDetails($IdMarksDistributionMaster) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("mdm"=>"tbl_marksdistributionmaster"))
		->join(array('p' => 'tbl_program'),'mdm.IdProgram  = p.IdProgram')
		->where("mdm.IdMarksDistributionMaster = ?",$IdMarksDistributionMaster);

		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}

	public function fnViewMarksDistributionDetails($IdMarksDistributionMaster) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("mdd"=>"tbl_marksdistributiondetails"))
		->join(array('dms' => 'tbl_definationms'),'mdd.PassStatus = dms.idDefinition')
		->join(array('c' => 'tbl_staffmaster'),'mdd.idStaff = c.IdStaff',array("CONCAT_WS(' ',IFNULL(c.FirstName,''),IFNULL(c.SecondName,''),IFNULL(c.ThirdName,''),IFNULL(c.FourthName,'')) as staffname"))
		->join(array('d' => 'tbl_semester'),'mdd.idSemester = d.IdSemester',array("CONCAT_WS(' ',IFNULL(e.SemesterMainName,''),IFNULL(d.year,'')) as semestername"))
		->join(array('e'=>'tbl_semestermaster'),'d.Semester = e.IdSemesterMaster')
		->where("mdd.IdMarksDistributionMaster = ?",$IdMarksDistributionMaster)
		->where("mdd.deleteFlag = 0");

		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fnAddMarksDistributionDetails($larrformData) {

		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_marksdistributiondetails";
		$countvar=count($larrformData['Weightagegrid']);
		for($i=0;$i<$countvar;$i++) {
			$larrAddMarksDistribution = array('IdMarksDistributionMaster'=>$larrformData['IdMarksDistributionMaster'],
					'idStaff'=>$larrformData['idStaffgrid'][$i],
					'idSemester'=>$larrformData['idSemestergrid'][$i],
					'ComponentName'=>$larrformData['ComponentNamegrid'][$i],
					'Weightage'=>$larrformData['Weightagegrid'][$i],
					'PassMark'=>$larrformData['PassMarkgrid'][$i],
					'TotalMark'=>$larrformData['TotalMarkgrid'][$i],
					'PassStatus'=>$larrformData['PassStatusgrid'][$i],
					'deleteFlag'=>0,
					'UpdDate'=>$larrformData['UpdDate'],
					'UpdUser'=>$larrformData['UpdUser']
			);
			$db->insert($table,$larrAddMarksDistribution);
		}


	}
	public function fndeletemarksdistributiondtls($IdMarksDistributionDetails) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_marksdistributiondetails";
		$larramounts = array('deleteFlag'=>1);
		$where ='IdMarksDistributionDetails = '.$IdMarksDistributionDetails;
		$db->update($table,$larramounts,$where);
	}


	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
    public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function getListComponentItem($IdMarksDistributionMaster) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$sql =  $this->lobjDbAdpt->select()->from(array("mdd" =>"tbl_marksdistributiondetails"))
							    								
							    ->where("mdd.IdMarksDistributionMaster = ?",$IdMarksDistributionMaster);

		$result = $this->lobjDbAdpt->fetchAll($sql);
		
		return $result;
	}
	
	
 	public function deleteComponentItem($id){		
	  $this->delete('IdMarksDistributionMaster =' . (int)$id);
	}
	
	public function getDataComponentItem($IdMarksDistributionDetails) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$sql =  $this->lobjDbAdpt->select()->from(array("mdd" =>"tbl_marksdistributiondetails"))								
							    ->where("mdd.IdMarksDistributionDetails = ?",$IdMarksDistributionDetails);

		$result = $this->lobjDbAdpt->fetchRow($sql);
		
		return $result;
	}
	

}