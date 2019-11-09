<?php 
class App_Model_Finance_DbTable_Invoice extends Zend_Db_Table_Abstract
{
    protected $_name = 'r019_invoicedetail';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){
	                
			$select = $db->select()
	                ->from(array('f'=>$this->_name))
	                ->where('f.'.$this->_primary.' = ' .$id);	                
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                ->from(array('f'=>$this->_name))
	                ->order('name ASC');
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
		}
		
		return $row;
	}
	
	public function getDataGenerate(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		echo $select = $db->select()
	                ->from(array('s'=>'r016_registrationdetails'))
	                ->join(array('p'=>'r015_student'),'p.ID=s.idApplication',array('*'))
	                ->join(array('f'=>'f005_feestructure'),'f.idCourse=s.idCourse',array('amount'=>'amount','currency'=>'currency'))
	                ->where('s.paymentMode IS NOT NULL')
	                ->where("s.idSchedule != '0'")
//	                ->limit(1)
	                ;
	                
	    $stmt = $db->query($select);
	    $row = $stmt->fetchAll();
		
		return $row;
	}
	
	public function getLastInvoice(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('s'=>$this->_name))
	                ->order("s.runningNumber desc")
	                ->limit(1)
	                ;
	                
	   $row = $db->fetchRow($select);
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('f'=>$this->_name))
	                ->join(array('p'=>'r015_student'),'p.ID=f.idApplication',array('*'))
	                ->joinLeft(array('pf'=>'f001_paymentmode'),'pf.id=f.paymentmode',array('paymentMode'=>'name'))
	                ->order('f.runningNumber DESC')
	                ;
		
		return $select;
	}
	
	public function addData($data){
		
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
}
?>