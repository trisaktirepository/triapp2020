<?php
class GeneralSetup_Model_Semester extends Zend_Db_Table
{
    protected $_name = "semester";
    
     
    public function fetchAll ()
    {    	 
        $sql = $this->_db->select()
                         ->from($this->_name)
                         ->order("level asc");				
		
        $result = $this->_db->fetchAll($sql);        
        return $result;
    }
   
    public function fetch ($id = "")
    {
        $sql = $this->_db->select()->from($this->_name);
        if ($id != "") {
            $sql->where('id = ?', $id);
        }
        $result = $this->_db->fetchRow($sql);
        return $result;
    }
    
    public function modify ($data, $id)
    {
        $this->_db->update($this->_name, $data, 'id = ' . (int) $id);
    }
    
    public function delete ($id)
    {
        $this->_db->delete($this->_name, 'id = ' . (int) $id);
    } 
    
   
   
}
?>
