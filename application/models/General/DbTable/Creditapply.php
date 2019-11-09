<?php
class App_Model_General_DbTable_Creditapply extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_credit_apply';
	private $lobjDbAdpt;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
    
    public function insert($data)
    {
        //print_r($data);
        $this->lobjDbAdpt->insert($this->_name,$data);
        
        return $this->lobjDbAdpt->lastInsertId();
    }
    
    public function update($data)
    {
    
    }
    
    public function delete($transactionId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $where = $db->quoteInto('idTransaction = ?', $transactionId);
        $db->delete($this->_name, $where);
    }
    
    public function getData($transactionId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $select = $db->select()
                    ->from($this->_name)
                    ->where('idTransaction =?',(int)$transactionId);
                    
        $result = $db->fetchRow($select);
        
        return $result;
    }
	
}
?>
