<?php

class Counseling_Model_UploadFile extends Zend_Db_Table
{

    protected $_name = 'tbl_counseling_upload_file';
    protected $_primary = 'auf_id';

     

	public function save(array $data)
    {

        if (isset($data['id'])) {
            $where = $this->getAdapter()->quoteInto('id = ?', $data['id']);
            $this->update($data, $where);
            $id = $data['id'];
        } else {
            $id = $this->insert($data);
        }

        return $id;
    }
    
    public function del($id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->delete($where);
        return (true);
    }
    
    public function update($data,$id)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$db->update($this->_name,$data,'auf_id='.$id);
    	
    	return (true);
    }

    function getData($idissue,$idissuedetail) {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	
    	$select = $db ->select()
    	->from(array('a'=>$this->_name))
    	->where('a.IdIssues=?',$idissue)
    	->where('a.IdIssueDetail=?',$idissuedetail);
    	$row =$db->fetchRow($select);
    	return $row;
    }
     

     

}