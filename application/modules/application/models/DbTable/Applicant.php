<?php
require_once 'Zend/Controller/Action.php';
class Application_Model_DbTable_Applicant extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a001_applicant';
	protected $_primary = "ID";
		
	public function getApplicant($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('a'=>$this->_name))
					->where("a.".$this->_primary .' = '. $id)
					->join(array('p'=>"program"), "a.ard_program = p.program_id")
					->join(array('pm'=>"masterprogram"), "p.program_master_id = pm.masterProgramID");
		
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('a'=>$this->_name))
					->join(array('p'=>"program"), "a.ard_program = p.program_id")
					->join(array('pm'=>"masterprogram"), "p.program_master_id = pm.masterProgramID");
			
			$row = $db->fetchAll($select);
			
			//$row = $this->fetchAll();
		}
		
		
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	public function search($name="", $code="", $program=""){
		$sql = "";
		
		if($name!=""){
			if($sql==""){
				$sql = $this->_name.".ARD_NAME like '%". $name."%'";	
			}else{
				$sql .=  " AND ".$this->_name.".ARD_NAME like '%". $name."%'";
			}
			
		}
		
		if($code!=""){
			if($sql==""){
				$sql = $this->_name.".ARD_ID like '%".$code."%'";	
			}else{
				$sql .= " AND ".$this->_name.".ARD_ID like '%".$code."%'";	
			}
			
		}
		
		if($program!="" && $program>0){
			$program = (int)$program;
			if($sql==""){
				$sql = $this->_name.".ARD_PROGRAM = ".$program;	
			}else{
				$sql .= " AND ".$this->_name.".ARD_PROGRAM = ".$program;	
			}
			
		}
		
		//$sql = "ARD_NAME like '%". $name."%' AND ARD_ID like '%".$code."%' OR ARD_PROGRAM = '%".$program."%'";
		//$sql = "ARD_PROGRAM like '%". $program."%'" ;
				
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
					->from($this->_name)
					->where("$sql")
					->join("program", "program.program_id = ".$this->_name.".ard_program")
					->join("masterprogram", "masterprogram.masterProgramID = program.program_master_id");
				
							
		$row = $db->fetchAll($select);
		
		if(!$row){
				throw new Exception("No Application");
		}
		
		return $row;
	}
}

