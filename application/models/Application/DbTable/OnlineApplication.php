<?php

class App_Model_Application_DbTable_OnlineApplication extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a001_applicant';
	protected $_primary = "ID";
		
	public function getData2($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}

		return $row->toArray();
	}
	
	public function getPaginatePlacementtest(){
		$db = Zend_Db_Table::getDefaultAdapter();
		echo $selectData = $db ->select()
						->from(array('program'=>$this->_name))
						->join(array('main_program'=>'r005_program_main'),'program.ID_PROG = main_program.id',array('main_name'=>'name'))
						->join(array('branch'=>'g004_branch'),'program.VENUE = branch.id',array('branch'=>'name'))
             		    ->order($this->_primary.' DESC');
						
		return $selectData;
	}
	
	public function getData($table,$cond=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->where($cond);
		//->order($orderby);
//		echo $select;
		$stmt = $select->query();
		$result = $stmt->fetchAll();
			
		return $result;
	}
	
	public function getList($table,$tbljoin,$joincond,$tbljoin2=0,$joincond2=0,$cond){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->join($tbljoin, $joincond)
		->join($tbljoin2, $joincond2)
		//->join($tbljoin3, $joincond3)
		->where($cond);	
		//->order('charging_type');
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	public function addPlacementtest($postData){
		$data = array(
		        'ID_PROG' => $postData['ID_PROG'],
				'DATE' => $postData['datepicker'],
				'VENUE' => $postData['VENUE']
				);
			
		$this->insert($data);
	}
	
	public function updateAdditionalData($postData,$id){
		
		$data= array(
					'ARD_SEX'=>$postData['ARD_SEX'],
					'ARD_DOB'=>$postData['ARD_YEAR'].'-'.$postData['ARD_MONTH'].'-'.$postData['ARD_DAY'],
					'ARD_RACE'=>$postData['ARD_RACE'],
					'ARD_RELIGION'=>$postData['ARD_RELIGION'],
					'ARD_MARITAL'=>$postData['ARD_MARITAL'],
					'ARD_CITIZEN'=>$postData['ARD_CITIZEN'],
					'ARD_ADDRESS1'=>$postData['ARD_ADDRESS1'],
					'ARD_ADDRESS2'=>$postData['ARD_ADDRESS2'],
					'ARD_POSTCODE'=>$postData['ARD_POSTCODE'],
					'ARD_STATE'=>$postData['ARD_STATE']
					);
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function updateProgram($postData,$id){
		$data = array(
		        'DATE' => $postData['DATE'],
				'VENUE' => $postData['VENUE']
				);
			
		$this->update($data, 'ID = '. (int)$id);
	}
	
	public function deleteProgram($id){
		$this->delete('ID =' . (int)$id);
	}

}

