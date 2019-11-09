<?php 
class App_Model_General_DbTable_Admin extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_user';
	protected $_primary = "iduser";
	
	public function adminlogin($username,$passwd){
 		$db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        ->from(array('a' => $this->_name), array('a.*'))
                        ->where('a.loginName = ?', $username)
                        ->where('a.passwd = md5(?)', $passwd)
                        ->where('a.IdRole = 1 or a.IdRole = 298 or a.IdRole = 445')
                        ;
                       
        $result = $db->fetchRow($sql);
        return $result;				
	}
}