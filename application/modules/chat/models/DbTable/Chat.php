<?php

class Chat_Model_DbTable_Chat extends Zend_Db_Table
{

    protected $_name = 'applicant_chat';
    protected $_primary = 'idChat';

       
    public function add($data)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	$db->insert($this->_name,$data);
    	$id='';//$db->getLastInsertValue();
    
    	return $id;
    }
    
    public function del($id)
    {
    	$where = $this->getAdapter()->quoteInto('idChat = ?', $id);
    	$this->delete($where);
    	return (true);
    }
    
    public function getDataByUser($transid)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	 
    	$select = $db->select()
    	->from(array('a'=>$this->_name)) 
    	->order('created_dt DESC');
    	if ($transid!=null) $select->where('at_trans_id = ?', $transid);
    	$issues = $db->fetchAll($select);
    	 
    	return ($issues);
    }
    
    public function getDataChat($transid)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$dtstart=date_create(date('Y-m-d H:i:s'));
    	date_sub($dtstart, date_interval_create_from_date_string('2 hours'));
    	$dtstop=date_create(date('Y-m-d H:i:s'));
    	date_add($dtstop, date_interval_create_from_date_string('30 minutes'));
    	 
    	$select = $db->select()
    	->from(array('a'=>$this->_name))
    	->where('a.created_dt between "'.date_format($dtstart,'Y-m-d H:i:s').'" and "'.date_format($dtstop,'Y-m-d H:i:s').'"')
    	->order('created_dt ASC'); 
    	if ($transid!=null) $select->where('at_trans_id = ?', $transid);
    	//echo $select;exit;
    	$issues = $db->fetchAll($select);
    
    	return ($issues);
    }
    
    public function getDataDest()
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$dtstart=date_create(date('Y-m-d H:i:s'));
    	date_sub($dtstart, date_interval_create_from_date_string('2 hours'));
    	$dtstop=date_create(date('Y-m-d H:i:s'));
    	date_add($dtstop, date_interval_create_from_date_string('30 minutes'));
    
    	$select = $db->select()
    	->distinct()
    	->from(array('a'=>$this->_name),array())
    	->join(array('b'=>'applicant_transaction'),'a.at_trans_id=b.at_trans_id',array('at_trans_id','at_pes_id'))
    	->where('a.created_dt between "'.date_format($dtstart,'Y-m-d H:i:s').'" and "'.date_format($dtstop,'Y-m-d H:i:s').'"')
    	->order('b.at_pes_id ASC');
    	 //echo $select;exit;
    	$issues = $db->fetchAll($select);
    
    	return ($issues);
    }
    

}