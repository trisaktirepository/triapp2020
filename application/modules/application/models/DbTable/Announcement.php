<?php
/**
 * @author Zahili
 */


class Application_Model_DbTable_Announcement extends Zend_Db_Table_Abstract {
	

	protected $_name = "tbl_announcement";

    /**
     * renamed from fetchAll to getAll. we don't want to override base class function
     */
    public function getAll ()
	{
		$sql = $this->_db->select()->from($this->_name);
		$result = $this->_db->fetchAll($sql);
		return $result;
	}
	public function upload ($data)
	{
		$this->_db->insert($this->_name, $data);
	}
	public function fetch ($id = "")
	{
		$sql = $this->_db->select()->from($this->_name);
		if ($id != "") {
			$sql->where('sl_id = ?', $id);
		}
		$result = $this->_db->fetchRow($sql);
		//print_r($result);
		//exit();
		return $result;
	}
	public function modify ($data, $id)
	{
		$this->_db->update($this->_name, $data, 'sl_id = ' . (int) $id);
	}
	public function delete ($id)
	{
		$this->_db->delete($this->_name, 'sl_id = ' . (int) $id);
	}
	
	
	
	
	

}

