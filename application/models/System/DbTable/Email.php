<?php 
class App_Model_System_DbTable_Email extends Zend_Db_Table_Abstract
{
    protected $_name = 'email_que';
	protected $_primary = 'id';
	
	
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from($this->_name)
	                 ->where($this->_primary.' = ' .$id);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
	        if(!$row){
	        	$row =  $row->toArray();
	        }
		}
		
		return $row;
	}
	
	public function addData($data){
		
		$data = array(
			'recepient_email' => $data['recepient_email'],
			'subject' => $data['subject'],
			'content' => $data['content'],
		    'attachment_path' => $data['attachment_path'],
		    'attachment_filename' => $data['attachment_filename'],
			'date_que' => date("Y-m-d H:i:s")
		);
			
		return $this->insert($data);
	}
}
?>