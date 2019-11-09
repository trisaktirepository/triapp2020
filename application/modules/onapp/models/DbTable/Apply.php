<?php
/**
 * @author Suliana
 */

class Onapp_Model_DbTable_Apply extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant';
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
					->join('hg015_branch', 'ha001_hg015_branch_id = hg015_branch_id')
					->order($this->_primary);
		
		return $select;
	}
	
	public function getInfo($table,$order){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					->from($table)
					->order($order);
		
		return $select;
	}
	
	public function getCount($table,$where){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					->from($table)
					->where($where);
					
        $row = $this->getAdapter()->query($select)->rowCount();
		return $row;
	}
	
//	public function getCount()
//    {
//    	$db = Zend_Db_Table::getDefaultAdapter();
//        $select = $this->select()
//       			->from($this, array('count(*) as amount'));
//        $rows = $this->fetchAll($select);
//       
//        return($rows[0]->amount);       
//    }
	
	public function getList($sel,$table,$where=1){
		$db = Zend_Db_Table::getDefaultAdapter();
		
//		$sql = $db->select($sel)
//					->from($table)
//					->from(array('p' => 'people'), array('id','name'))
//					->where($where);
//					
//		$sql = $select->__toString();

	
		$sql	=	"SELECT $sel
					FROM $table
					WHERE $where";
		
		$result = $this->getAdapter()->fetchAll($sql);
//		$result = $this->getAdapter()->fetchRow($sql);
	
		return $result;
	}
	
	public function addData($postData){
		
		$date = date("Y-m-j G:i:s");
		
		$data= array(
					'ARD_IC'=>$postData['sa001_ic'],
					'ARD_TYPE_IC'=>$postData['type_id'],
					'ARD_IC_PLACE'=>$postData['ARD_IC_PLACE'],
					'ARD_IC_DATE'=>$postData['ARD_IC_DATE'],
					'ARD_IC_EXPIRE'=>$postData['ARD_IC_EXPIRE'],
					'ARD_NAME'=>$postData['sa001_name'],
					'ARD_NAME_ARAB'=>$postData['sa001_name_arab'],
					'ARD_HPHONE'=>$postData['sa001_contact'],
					'ARD_DATE_APP'=>$date,
					'ARD_EMAIL'=>$postData['sa001_email'],
					'ARD_PROGRAM'=>$postData['sc001_program_id']
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
	
	public function search($name=""){
		$sql = "ARD_IC like '%".$name."%'";
		
		$row = $this->fetchAll($sql);
		
		return $row->toArray();
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}

