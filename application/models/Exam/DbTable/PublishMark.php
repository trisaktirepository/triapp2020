<?php
class App_Model_Exam_DbTable_PublishMark extends Zend_Db_Table { 
	
	protected $_name = 'tbl_publish_mark';
	protected $_primary = 'pm_id';
	
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	public function isPublished($idProgram,$idSemester,$idSubject,$idGroup,$idComponent=null,$type=null) {
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select 	= $db->select()
		->from($this->_name)
		->where('pm_idProgram = ?',$idProgram)
		->where('pm_idSemester = ?',$idSemester)
		->where('pm_idSubject = ?',$idSubject)
		->where('pm_idGroup = ?',$idGroup)
		->where('pm_date <= CURDATE()')
		->order('pm_date DESC');
	
		if ($idComponent!=null) $select->where('pm_idComponent = ?',$idComponent);
		if ($type!=null ) $select->where('pm_type = ?',$type);
	
		$result = $db->fetchRow($select);
		//echo $select ;exit;
		return $result;
	}
	
	public function isSetPublish($idProgram,$idSemester,$idSubject,$idGroup,$idComponent=null,$type=null) {
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select 	= $db->select()
		->from($this->_name)
		->where('pm_idProgram = ?',$idProgram)
		->where('pm_idSemester = ?',$idSemester)
		->where('pm_idSubject = ?',$idSubject)
		->where('pm_idGroup = ?',$idGroup)
		->order('pm_date DESC');
	
		if ($idComponent!=null) $select->where('pm_idComponent = ?',$idComponent);
		if ($type!=null ) $select->where('pm_type = ?',$type);
	
		$result = $db->fetchRow($select);
		//echo $select ;exit;
		return $result;
	}
	public function getData($idProgram,$idSemester,$idSubject,$idGroup,$idComponent,$type) {
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select 	= $db->select()
								->from(array('a'=>$this->_name),array('a.*','now'=>'CURDATE()'))
								//->where('pm_idProgram = ?',$idProgram)
								->where('pm_idSemester = ?',$idSemester)
								->where('pm_idSubject = ?',$idSubject)
								->where('pm_idGroup = ?',$idGroup)
								->where('pm_idComponent = ?',$idComponent)
								->where('pm_type = ?',$type);
		return $result = $db->fetchRow($select);
	}
	
	public function getPublishResult($idProgram,$idSemester) {
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select 	= $db->select()
								->from($this->_name)
								->where('pm_idProgram = ?',$idProgram)
								->where('pm_idSemester = ?',$idSemester)								
								->where('pm_type = 2');
		return $result = $db->fetchRow($select);
	}
	
	
	public function getPublishResultData($idProgram,$idSemester) {
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select 	= $db->select()
								->from(array('pm'=>$this->_name))
								->joinLeft(array('p'=>'tbl_program'),'p.IdProgram=pm.pm_idProgram',array('ProgramCode','ProgamName'=>'ArabicName'))
								->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=pm.pm_idSemester',array('SemesterName'=>'SemesterMainName'))
								->where('pm_idProgram = ?',$idProgram)
								->where('pm_idSemester = ?',$idSemester)								
								->where('pm_type = 2');
		return $result = $db->fetchAll($select);
	}
	
	
	public function publishAvailableDate($registrationId,$idProgram,$landscape=null){
		
		/* Case : Block Landascape
		 * Belum cater case jika dalam satu semester ada 2 block
		 * For now every block yg dalam semseter tu akan dipaparkan
		 */		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$sql_sem = $db->select()
                   		  ->from(array('s' => "tbl_studentsemesterstatus"),array('IdSemesterMain')) 
                          ->where('s.IdStudentRegistration = ?',$registrationId); 		
		
		$select 	= $db->select()
						->from($this->_name)
						->where('pm_idProgram = ?',$idProgram)
						->where('pm_idSemester IN (?)',$sql_sem)
						->where('pm_date <= CURDATE()')								
						->where('pm_type = 2');
						
		return $result = $db->fetchAll($select);
	}
	public function getInitSemester($idProgram) {
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select 	= $db->select()
		->from(array('a'=>$this->_name),array())
		->join(array('s'=>'tbl_semestermaster'),'a.pm_idSemester=s.IdSemesterMaster',array('year'=>'LEFT(SemesterMainCode,4)'))
		->where('pm_idProgram = ?',$idProgram)
		->order('s.SemesterMainStartDate ASC');
		$row=$db->fetchRow($select);
		return $row['year'];
	}
	
	public function isAllMarkShown($idSemester,$idProgram,$idSubject,$idGroup) {
		//get component
		$dbCourse=new App_Model_General_DbTable_CourseGroupStudent();
		$branchs=$dbCourse->getStudentMarkDistribution($idGroup);
		
		if ($branchs) $idBranch=$branchs[0]['IdBranch']; else $idBranch=null;
		//
		$markDistributionDB =  new Examination_Model_DbTable_Marksdistributionmaster();
		$list_component = $markDistributionDB->getListMainComponent($idSemester,$idProgram,$idSubject,$idBranch);
		if (!$list_component) return true;
		//academic year bottom limit
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$yearini=$this->getInitSemester($idProgram);
		
		$select 	= $db->select()
		->from('tbl_semestermaster',array('year'=>'LEFT(SemesterMainCode,4)'))
		->where('IdSemesterMaster=?',$idSemester);
		$row=$db->fetchRow($select);
		//echo var_dump($row);exit;
		if ($row['year'] < $yearini) return true;
		//
		foreach($list_component as $index=>$component){
	
			$publish = $this->getData($idProgram,$idSemester,$idSubject,$idGroup,$component["IdMarksDistributionMaster"],1);
	
			if($publish){
				//echo var_dump($publish);
	
				if ($publish['pm_date']>$publish['now']) {
	
					return false;
				}
			} else return false;
	
		}
	
		return true;
	}
	
	
	public function isAllMarkPublished($idSemester,$idProgram,$idSubject,$idGroup) {
		//get component
		$dbCourse=new GeneralSetup_Model_DbTable_CourseGroupStudent();
		$branchs=$dbCourse->getStudentMarkDistribution($idGroup);
		if ($branchs) $idBranch=$branchs[0]['IdBranch']; else $idBranch=null;
		//
		$markDistributionDB =  new Examination_Model_DbTable_Marksdistributionmaster();
		$list_component = $markDistributionDB->getListMainComponent($idSemester,$idProgram,$idSubject,$idBranch);
	
		//academic year bottom limit
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$yearini=$this->getInitSemester($idProgram);
		$select 	= $db->select()
		->from('tbl_semestermaster',array('year'=>'LEFT(SemesterMainCode,4)'))
		->where('IdSemesterMaster=?',$idSemester);
		$row=$db->fetchRow($select);
		//echo var_dump($row);exit;
		if ($row['year'] < $yearini) return true;
		//
		foreach($list_component as $index=>$component){
	
			$publish = $this->getData($idProgram,$idSemester,$idSubject,$idGroup,$component["IdMarksDistributionMaster"],1);
	
			if(!$publish) return false;
	
	
		}
	
		return true;
	}
	
}
?>