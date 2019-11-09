<?php


class AdmissionRecord_Model_DbTable_Matrix extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r030_matrix_seq';
	protected $_primary = "number";
	
	public function getSeq()
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
        
    	$data = array(
            'number' => null
        );
        
        $this->insert($data);
        
        $id = $db->lastInsertId();
        
        return $id;
    }
}

?>