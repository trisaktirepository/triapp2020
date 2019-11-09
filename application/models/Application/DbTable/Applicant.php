<?php

class App_Model_Application_DbTable_Applicant extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a001_applicant';
	protected $_primary = "ID";
	
	public function getData($id=0){
		
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.ID = '.$id)
						->join(array('p'=>'r003_award'),'p.id = a.ARD_AWARD',array('award_name'=>'name'));
						
	        $row = $db->fetchRow($select);				
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->join(array('p'=>'r003_award'),'p.id = a.ARD_AWARD',array('award_name'=>'name'));
						
			$row = $db->fetchAll($select);
		}
		
		if(!$row){
			throw new Exception("There is No Applicant");
		}
		
		return $row;
	}
	
	public function getApplicant($id=0){
		
		$id = (int)$id;
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.ID = '.$id);				
//						->join(array('p'=>'r006_program'),'p.id = a.ARD_PROGRAM',array('program_code'=>'code'))
//						->join(array('mp'=>'r005_program_main'),'mp.id = p.program_main_id',array('main_name'=>'name'));
						
						
	        $row = $db->fetchRow($select);				
		
	    	        
		if(!$row){
			throw new Exception("There is No Applicant");
		}
		
		return $row;
	}
	
	public function getApplicantMigrate($id=0){
		
		$id = (int)$id;
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.ID = '.$id)			
						->join(array('p'=>'r006_program'),'p.id = a.ARD_PROGRAM',array('program_code'=>'code'))
						->join(array('mp'=>'r005_program_main'),'mp.id = p.program_main_id',array('main_name'=>'name'));
						
						
	        $row = $db->fetchRow($select);				
		
	    	        
		if(!$row){
			throw new Exception("There is No Applicant");
		}
		
		return $row;
	}
	
	public function getApplication($id=0){
		
		$id = (int)$id;
		
			$db = Zend_Db_Table::getDefaultAdapter();
			echo $select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.ID = '.$id)							
						->join(array('et'=>'a002_applicant_entry'),'et.app_id = a.ID',array('cert'=>'cert'))
						->join(array('ap'=>'a013_applied'),'ap.applicantid = a.ID',array('programid'=>'programid'))
						->join(array('p'=>'r006_program'),'p.id = ap.programid',array('program_code'=>'code'))
						->join(array('mp'=>'r005_program_main'),'mp.id = p.program_main_id',array('main_name'=>'name'));
						
						
	        $stmt = $select->query();
			$result = $stmt->fetchAll();	
		
	    	        
//		if(!$row){
//			throw new Exception("There is No Applicant");
//		}
//		
		return $result;
	}
	
	
	
	public function search($name="", $id="", $id_type=0, $program_id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where("a.ARD_PROGRAM != 0 and a.ARD_OFFERED = 1")
						->join(array('p'=>'r006_program'),'p.id = a.ARD_PROGRAM',array('program_code'=>'code'))
						->join(array('mp'=>'r005_program_main'),'mp.id = p.program_main_id',array('main_name'=>'name'));
						
		if($name!=""){
			$select->where("ARD_NAME like '%".$name."%'");	
		}						
		
		if($id!=""){
			$select->where("ARD_IC like '%".$id."%'");	
		}
		
		if($id_type!=0){
			$select->where("ARD_TYPE_IC = ".$id_type);	
		}
		
		if($program_id!=0){
			$select->where("ARD_PROGRAM = ".$program_id);	
		}
		
		$stmt = $db->query($select);
	    $row = $stmt->fetchAll();
	    
	    return $row;
	}
	
	
	public function searchApplicant($name="", $ic="", $id_type=0, $program_id=0, $id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where("a.ARD_PROGRAM != 0")
						->where("a.ARD_MIGRATED != 1")
						->join(array('p'=>'r006_program'),'p.id = a.ARD_PROGRAM',array('program_code'=>'code'))
						->join(array('mp'=>'r005_program_main'),'mp.id = p.program_main_id',array('main_name'=>'name'));
						
		if($name!=""){
			$select->where("ARD_NAME like '%".$name."%'");	
		}						
		
		if($ic!=""){
			$select->where("ARD_IC like '%".$ic."%'");	
		}
		
		if($id_type!=0){
			$select->where("ARD_TYPE_IC = ".$id_type);	
		}
		
		if($program_id!=0){
			$select->where("ARD_PROGRAM = ".$program_id);	
		}	
		
				
		$stmt = $db->query($select);
		$row = $stmt->fetchAll();
			    
	    return $row;
	}
		
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('a'=>$this->_name));
		
		return $selectData;
	}
	
	public function addData($postData){
		
		$date = date('Y-m-d H:i:s');
		
		$data= array(
					'ARD_IC'=>$postData['ARD_IC'],
					'ARD_TYPE_IC'=>$postData['ARD_TYPE_IC'],
//					'ARD_IC_PLACE'=>$postData['ARD_IC_PLACE'],
//					'ARD_IC_DATE'=>$postData['ARD_IC_DATE'],
//					'ARD_IC_EXPIRE'=>$postData['ARD_IC_EXPIRE'],
					'ARD_NAME'=>strtoupper($postData['ARD_NAME']),
					'ARD_NAME_2'=>strtoupper($postData['ARD_NAME_2']),
					'ARD_NAME_3'=>strtoupper($postData['ARD_NAME_3']),
					'ARD_NAME_4'=>strtoupper($postData['ARD_NAME_4']),
					'ARD_SURNAME'=>strtoupper($postData['ARD_SURNAME']),
					'ARD_NAME_ARAB'=>$postData['ARD_NAME_ARAB'],
					'ARD_SEX'=>$postData['ARD_SEX'],
					'ARD_PHONE'=>$postData['ARD_PHONE'],
					'ARD_DATE_APP'=>$date,
					'ARD_EMAIL'=>$postData['ARD_EMAIL'],
					'ARD_AWARD'=>$postData['ARD_AWARD'],
					'ARD_CHANNEL'=>1
					); 

		$this->insert($data);
 		$id = $this->getAdapter()->lastInsertId();
		return $id;
	}
	
	public function updateData($postData,$id){
		
		$data= array(
					'ARD_IC'=>$postData['ARD_IC'],
					'ARD_TYPE_IC'=>$postData['ARD_TYPE_IC'],
					'ARD_NAME'=>strtoupper($postData['ARD_NAME']),
					'ARD_NAME_2'=>strtoupper($postData['ARD_NAME_2']),
					'ARD_NAME_3'=>strtoupper($postData['ARD_NAME_3']),
					'ARD_NAME_4'=>strtoupper($postData['ARD_NAME_4']),
					'ARD_SURNAME'=>strtoupper($postData['ARD_SURNAME']),
					'ARD_NAME_ARAB'=>$postData['ARD_NAME_ARAB'],
					'ARD_SEX'=>$postData['ARD_SEX'],
					'ARD_PHONE'=>$postData['ARD_PHONE'],
					'ARD_DATE_APP'=>$date,
					'ARD_EMAIL'=>$postData['ARD_EMAIL'],
					'ARD_AWARD'=>$postData['ARD_AWARD']
					); 
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function updateAdditional($postData,$id){
		
		echo $data= array(
					'ARD_ADDRESS1'=>strtoupper($postData['ARD_ADDRESS1']),
					'ARD_ADDRESS2'=>strtoupper($postData['ARD_ADDRESS2']),
					'ARD_TOWN'=>$postData['ARD_TOWN'],
					'ARD_POSTCODE'=>$postData['ARD_POSTCODE'],
					'ARD_STATE'=>$postData['ARD_STATE'],
					'ARD_COUNTRY'=>$postData['ARD_COUNTRY']
					); 
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function getList($sel,$table,$where=1){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$sql	=	"SELECT $sel
					FROM $table
					WHERE $where";
		
		$result = $this->getAdapter()->fetchAll($sql);
//		$result = $this->getAdapter()->fetchRow($sql);
	
		return $result;
	}
	
    //UPDATE MIGRATE STATUS yatie
	public function updateStatus($id){
		
		$data = array('ARD_MIGRATED'=>1);
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	
}

