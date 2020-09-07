<?php
class App_Model_General_DbTable_CourseGroupAttendanceReflection extends Zend_Db_Table_Abstract
{
	protected $_name = 'course_group_attendance_reflextion';
	protected $_primary='cgar_id';
	
	private $lobjDbAdpt;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
    
	public function insertData($data)
    {
        return $this->lobjDbAdpt->insert($this->_name,$data);
    }
    
    public function updateData( $data,$key)
    {
    	$this->lobjDbAdpt->update($this->_name,$data,$key);
    }
    
    public function deleteData($where)
    {
        $db = Zend_Db_Table::getDefaultAdapter(); 
        $db->delete($this->_name, $where);
    }
    
    
    
    public function isIn($cgaid,$stdid)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	$select = $db->select()
    	->from($this->_name)
    	->where('cga_id =?',(int)$cgaid)
    	->where('stdid=?',$stdid);
    
    	$result = $db->fetchRow($select);
    	return $result;
    }
    
    public function getDataByCatId($cgaid)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
       $select = $db->select()
    	->from(array('a'=>$this->_name))
    	->where('cga_id =?',(int)$cgaid);
    
    	$result = $db->fetchAll($select); 
    	return $result;
    }
    public function getDataStd($cgaid,$stdid)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	$select = $db->select()
    	->from(array('a'=>$this->_name))
    	->join(array('b'=>'course_group_attendance'),'a.cga_id=b.id')
    	->where('cga_id =?',(int)$cgaid);
    
    	$result = $db->fetchRow($select);
    	if ($result) {
    		$select = $db->select()
    		->from(array('a'=>$this->_name))
    		->join(array('b'=>'course_group_attendance'),'a.cga_id=b.id')
    		->where('a.stdid=?',$stdid)
    		->where('b.group_id=?',$result['group_id'])
    		->order('b.class_date');
    		$result = $db->fetchAll($select);
    	}
    	return $result;
    }
}
?>
