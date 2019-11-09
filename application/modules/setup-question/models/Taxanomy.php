<?php
class GeneralSetup_Model_Taxanomy extends Zend_Db_Table
{
    protected $_name = "taxanomy_level";
    
     
    public function fetchAll ()
    {
        $sql = $this->_db->select()->from($this->_name);
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
        //print_r($result);
        //exit();
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
    
     public function findreturnselect ($keyword = "")
    {
        $sql = $this->_db->select()->from($this->_name);
        if ($keyword != "") {
            //echo $keyword;
            $sql->where(
            '  ( firstName LIKE ? ', 
            '%' . $keyword . '%');
            $sql->orwhere(
            '   lastName LIKE ? ', 
            '%' . $keyword . '%');
            $sql->orwhere(
            '   username LIKE ? )', 
            '%' . $keyword . '%');
        }
        //echo $sql;
        //exit();
        return $sql;
    }
     public function returnselect ()
    {
        $sql = $this->_db->select()
            ->from($this->_name)
            ->order('id ASC');
    
        return $sql;
    }
   
}
?>
