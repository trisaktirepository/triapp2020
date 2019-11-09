<?php

class App_Model_Skpi_DbTable_Cp extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_SKPI_CP';
	protected $_primary = "IdSKPI";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		 
		
		return $row->toArray();
	}
	public function getDataGrup($program,$idlandscape,$idmajoring=null){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->from(array('a'=>$this->_name))
			->join(array('def'=>'tbl_definationms'),'a.Type=def.idDefinition',array('typename'=>'BahasaIndonesia'))
			->join(array('mj'=>'tbl_programmajoring'),'a.IdMajoring=mj.IDProgramMajoring')
			->where('a.IdProgram =? ', $program)
			->where('a.IdLandscape =? ', $idlandscape)
			->order('Type');
			
			if ($idmajoring!=null) $select->where('a.IdMajoring =? ', $idmajoring);
			$row = $lobjDbAdpt->fetchAll($select);
	 
		return $row;
	}
	public function addData($postData){
		$data = array(
				'CP_Bahasa' => $postData['cp_bahasa'],
				'CP_English' => $postData['cp_english'],	
				'Type' => $postData['type'],	
				'sequence'=> $postData['seq'],
				'IdLandscape' => $postData['idLandscape'],	
				'IdProgram' => $postData['idProgram'],
				'IdMajoring' => $postData['idMajoring'],
				'level_entry' => 1,
				'dt_entry' => date('Y-m-d H:i:s'),
				'id_user' =>  $postData['id_user']
				);
			
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
		
		$data = array(
				'CP_Bahasa' => $postData['cp_bahasa'],
				'CP_English' => $postData['cp_english'],	
				'Type' => $postData['type'],	
				'sequence'=> $postData['seq'],
				'IdLandscape' => $postData['idLandscape'],	
				'IdProgram' => $postData['idProgram'],
				'IdMajoring' => $postData['idMajoring'],
				'dt_update' => date('Y-m-d H:i:s')
				 
				 
				);
		 
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function rejectData($by,$id){
	
		$data = array(
				'approved'=>'2',
				'rejected_by'=>$by,
				'dt_update' => date('Y-m-d H:i:s')
					
					
		);
			
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function approvedData($by,$id){
	
		$data = array(
				'approved'=>'1',
				'rejected_by'=>0,
				'dt_update' => date('Y-m-d H:i:s')
					
					
		);
			
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function fnGetCPType() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "CP Types"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	public function fnGetProgramMajoring($program) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_programmajoring'),array('key'=>'IDProgramMajoring','value'=>'BahasaDescription'))
		->where('idProgram=?',$program);
		 
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
}

