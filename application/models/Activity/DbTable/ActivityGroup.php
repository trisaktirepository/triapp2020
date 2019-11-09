<?php 

class App_Model_Activity_DbTable_ActivityGroup extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_activity_tagging_group';
	protected $_primary = "IdCourseTaggingGroup";
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		// echo var_dump($data);echo $id;exit;
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	 
	 public function getOpenActivity($program=null) {
	 
	 	 	$db = Zend_Db_Table::getDefaultAdapter();
	 	
	 		$sql =  $db->select()
	 				->from(array('ag'=>$this->_name))
	 				->join(array('ac'=>'activity'),'ag.IdActivity=ac.IdActivity')
	 				->join(array('c'=>'tbl_definationms'),'c.IdDefinition=ag.IdSubject',array('category'=>'c.BahasaIndonesia','categorycode'=>'DefinitionCode'))
	 				->join(array('ap'=>'activity_group_program'),'ap.group_id=ag.IdCourseTaggingGroup',array())
	 				->join(array('sc'=>'activity_group_schedule'),array())
	 				->where('sc.sc_date_end >=CURDATE()')
	 				 
	 				->group('ac.IdActivity');
	 		if ($program!=null) $sql->where('ap.program_id=?',$program);
	 		$row=$db->fetchAll($sql);
	 	 
	 		return $row;
	 	 
	 }
	 
	 public function getOpenActivityApplicant($program) {
	 
	 	$db = Zend_Db_Table::getDefaultAdapter();
	 	 
	 	$sql =  $db->select()
	 	->from(array('ag'=>$this->_name))
	 	->join(array('ac'=>'activity'),'ag.IdActivity=ac.IdActivity')
	 	->join(array('c'=>'tbl_definationms'),'c.IdDefinition=ag.IdSubject',array('category'=>'c.BahasaIndonesia','categorycode'=>'DefinitionCode'))
	 	->join(array('ap'=>'activity_group_program'),'ap.group_id=ag.IdCourseTaggingGroup',array())
	 	->join(array('sc'=>'activity_group_schedule'),'sc.IdGroup=ag.IdCourseTaggingGroup',array())
	 	->where('ap.program_id=?',$program)
	 	->where('sc.sc_date_end >=CURDATE()')
	 	->where("c.DefinitionCode='PPSB'")
	 	 
	 	->group('ag.IdCourseTaggingGroup'); 
	 	// echo $sql;exit;
	 	$row=$db->fetchAll($sql);
	 
	 	return $row;
	 
	 }
	 
	 public function getGroupActivity($idAct,$programid) {
	 
	 	$db = Zend_Db_Table::getDefaultAdapter();
	 	 
	 	$sql =  $db->select()
	 	->from(array('ag'=>$this->_name)) 
	 	->join(array('c'=>'tbl_definationms'),'c.IdDefinition=ag.IdSubject',array('category'=>'c.BahasaIndonesia','categorycode'=>'DefinitionCode'))
	 	->join(array('ap'=>'activity_group_program'),'ag.IdCourseTaggingGroup=ap.group_id')
	 	->where('ag.IdActivity=?',$idAct)
	 	->where('ap.program_id=?',$programid);
	 	$row=$db->fetchAll($sql);
	 
	 	return $row;
	 
	 }
	 
	 
	
}