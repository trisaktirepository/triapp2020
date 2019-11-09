<?php

/**
 * Country
 * 
 * @author Muhamad Alif Muhammad
 * @date Apr 25, 2010
 * @version 
 */

class Application_Model_DbTable_Manual extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a001_applicant';
	protected $_primary = "ID";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Applicant");
		}
		
		return $row->toArray();
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from($this->_name)
							->order('ID DESC');
		
		return $select;
	}
	
	public function search($condition){
		//echo $condition;
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('s'=>$this->_name));
				//->join(array('p'=>'r006_program'),'p.id=s.program_id',array('program_id'=>'p.id','program_code'=>'p.code'))
				//->join(array('mp'=>'r005_program_main'),'mp.id=p.program_main_id',array('main_name'=>'name'));

		if($condition!=null){
		    if($condition['id']!=""){
				$select->where("s.ARD_IC like '%" .$condition['id']."%'");
			}
			
			if($condition['name']!=""){
				$select->where("s.ARD_NAME like '%" .$condition['name']."%'");
			}
			
		}
		//echo $select;
	
//		 if($condition['id']!=""){	
//		 	$row = $db->fetchRow($select);
//		 }else{
			$row = $db->fetchAll($select);
//		 }
		
		return $row;
	}
	
	public function addData($postData){
		
		$auth = Zend_Auth::getInstance();
		
		$data = array(
				'ARD_NAME' => strtoupper($postData['ARD_NAME']),
				'ARD_IC' => $postData['ARD_IC'],
				'ARD_CITIZEN' => $postData['ARD_CITIZEN'],
				'ARD_IC' => $postData['ARD_IC'],
				'ARD_CITIZEN' => $postData['ARD_CITIZEN'],
				'ARD_SEX' => $postData['ARD_SEX'],
				'ARD_RACE' => $postData['ARD_RACE'],
				'ARD_TELEPHONE' => $postData['ARD_TELEPHONE'],
				'ARD_HPHONE' => $postData['ARD_HPHONE'],
				'ARD_ADDRESS1' => $postData['ARD_ADDRESS1'],
				'ARD_POSTCODE' => $postData['ARD_POSTCODE'],
				'ARD_STATE' => $postData['ARD_STATE'],
				'ARD_EMAIL' => $postData['ARD_EMAIL'],
				'ARD_OFFERED' => $postData['ARD_OFFERED'],
				'ARD_PROGRAM' => $postData['ARD_PROGRAM'],
				'ARD_OFFERED_BY' => $auth->getIdentity()->id
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'ARD_NAME' => strtoupper($postData['ARD_NAME']),
				'ARD_IC' => $postData['ARD_IC'],
				'ARD_CITIZEN' => $postData['ARD_CITIZEN'],
				'ARD_IC' => $postData['ARD_IC'],
				'ARD_CITIZEN' => $postData['ARD_CITIZEN'],
				'ARD_SEX' => $postData['ARD_SEX'],
				'ARD_RACE' => $postData['ARD_RACE'],
				'ARD_PHONE' => $postData['ARD_TELEPHONE'],
				'ARD_HPHONE' => $postData['ARD_HPHONE'],
				'ARD_ADDRESS1' => $postData['ARD_ADDRESS1'],
				'ARD_POSTCODE' => $postData['ARD_POSTCODE'],
				'ARD_STATE' => $postData['ARD_STATE'],
				'ARD_EMAIL' => $postData['ARD_EMAIL'],
				'ARD_OFFERED' => $postData['ARD_OFFERED'],
				'ARD_PROGRAM' => $postData['ARD_PROGRAM']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}

