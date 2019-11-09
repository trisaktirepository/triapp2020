<?php
/**
 * @author Suliana
 */

class Onapp_Model_DbTable_Offer extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'offerlettertemplate';
	protected $_primary = "ID";
	
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Such Applicant.");
		}
		return $row->toArray();
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					->from($this->_name)
					->order($this->_primary);
		
		return $select;
	}
	
	public function addData($postData){
		
		$date = date("Y-m-j G:i:s");
		
		$data= array(
					'SECTION'=>$postData['SECTION'],
					'STATUS'=>1,
					'DATE'=>$date
					); 

		$this->insert($data);
 		$id = $this->getAdapter()->lastInsertId();
		return $id;
		
	}
	
	public function updateData($postData,$id){
		
		$data= array(
					'ARD_PROGRAM_NAME'=>$postData['id_apply'],
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
	
	
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}

