<?php
class Examapplicant_Model_DbTable_ExamScriptConfig extends Zend_Db_Table_Abstract
{
	
    protected $_name = "tbl_placement_exam_config";
    protected $_primary="idConfig";
    
     
     
   
    
    public function addData($data){				
		$id = $this->insert($data);
		return $id;
	}
	
	public function updateData($data,$id){		
		$this->_db->update($this->_name,$data,$this->_primary .' = ' . (int)$id);
	}
	
	public function deleteData($id){		
		$this->_db->delete($this->_name,$this->_primary . ' = ' . (int)$id);
	}
	
	public function  getData($id=null) {
		$select=$this->_db->select()
			->from(array('b'=>$this->_name))
			->join(array('p'=>'appl_placement_head'),'b.placement_code=p.aph_placement_code',array('placement_name'=>'aph_placement_name'))
			->join(array('tt'=>'appl_test_type'),'tt.act_id=b.test_type',array('test_type'=>'CONCAT(act_name," ",act_start_time)'))
			->joinLeft(array('sc'=>'appl_placement_schedule'),'sc.aps_id=b.sch_id',array('placement_schedule'=>'sc.aps_test_date'))
			->join(array('a'=>'tbl_definationms'),'a.IdDefinition=b.config_mode',array('config_mode'=>'a.BahasaIndonesia'));
		if ($id!=null) {
			$select->where($this->_primary.' ='.$id);
			return $this->_db->fetchRow($select);
		}
		return $this->_db->fetchAll($select);
	}
	
	public function  getMatchConfig($placementcode,$schid,$testtype) {
		$select=$this->_db->select()
		->from(array('b'=>$this->_name))
		->join(array('a'=>'appl_placement_head'),'b.placement_code=a.aph_placement_code',array('aph_id'))
		->where('b.placement_code=?',$placementcode)
		->where('b.test_type=?',$testtype)
		->where('b.sch_id=?',$schid);
		 $row=$this->_db->fetchRow($select);
		 if (!$row) {
		 	$select=$this->_db->select()
		 	->from(array('b'=>$this->_name))
		 	->join(array('a'=>'appl_placement_head'),'b.placement_code=a.aph_placement_code',array('aph_id'))
			->where('b.placement_code=?',$placementcode)
		 	->where('b.test_type=?',$testtype)
		 	->where('b.sch_id=0 or b.sch_id is null');
		 	$row=$this->_db->fetchRow($select);
		 }
		 return $row;
	}
	
	public function fnGetConfig() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Exam Configuration"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	  
	 
	
	
	
}
?>
