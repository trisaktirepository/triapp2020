<?php

/**
 * Privilege
 * 
 * @author Muhamad Alif Muhammad
 * @date Feb 16, 2011
 * @version 
 */

class App_Model_System_DbTable_Privilege extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'sys004_privilege';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Privilege");
		}
		
		return $row->toArray();
	}
	
	public function getPrivilege($role_id,$module_id,$controller_id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
					->from(array('p'=>$this->_name))
					->where('p.role_id = '.$role_id)
					->where('p.module_id = '.$module_id)
					->where('p.controller_id = '.$controller_id);
		
		$stmt = $db->query($select);
	
		$row = $stmt->fetch();
		
		
		$privilege = array(
				'view' =>isset($row['view']) && $row['view']!=null?$row['view']:0,
				'add' =>isset($row['add']) && $row['add']!=null?$row['add']:0,
				'edit' =>isset($row['edit']) && $row['edit']!=null?$row['edit']:0,
				'delete' =>isset($row['delete']) && $row['delete']!=null?$row['delete']:0,
				);
		
		return $privilege;
	}
	
	public function isPrivilegeAvailable($role_id,$module_id,$controller_id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
					->from(array('p'=>$this->_name))
					->where('p.role_id = '.$role_id)
					->where('p.module_id = '.$module_id)
					->where('p.controller_id = '.$controller_id);
		
		$stmt = $db->query($select);
	
		$row = $stmt->fetch();
		
		if($row){
			return $row;
		}else{
			return null;
		}
	}
}

