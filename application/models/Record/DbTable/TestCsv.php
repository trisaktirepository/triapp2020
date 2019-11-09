<?php


class App_Model_Record_DbTable_TestCsv extends Zend_Db_Table_Abstract {
	
	/**
	 * The default table name 
	 */
	protected $_name = 'test_csv';
	protected $_primary = "id";
	
	
	public function migrateData($data){
		$db = Zend_Db_Table::getDefaultAdapter();
        
        $this->insert($data);
        $id = $db->lastInsertId();
        
        return $id;
	}
}	
?>