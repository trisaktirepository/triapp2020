<?php 
class App_Model_Question_DbTable_Studentimport extends Zend_Db_Table_Abstract
{
//    protected $_main = 'q003_question_main';
//    protected $_question = 'q004_question';
//	protected $_primarymain = "id";
//	protected $_primaryquestion = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('u'=>$this->_name))
	                 ->where('u.'.$this->_primary.' = ' .$id);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        $row =  $row->toArray();
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	public function getApplicationSachin(){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 echo $select = $db->select()
		             ->from(array('a'=>'tbl_studentapplication'))	
		             ->joinLeft(array('b'=>'tbl_registereddetails'),'b.IDApplication = a.IDApplication',array('Regid'=>'b.Regid','attendance'=>'b.Cetreapproval','approved'=>'b.Approved','RegistrationPin'=>'b.RegistrationPin'))
		             ->joinLeft(array('c'=>'tbl_studentpaymentoption'),'c.IDApplication = a.IDApplication',array('paymentmode'=>'c.ModeofPayment'))
		             ->order('a.ICNO ASC')
		             ->where('a.Examdate = 28')
		             ->where('a.Exammonth = 7')
		            // ->where('a.Payment = 1')
		              ->where('a.Examvenue= 32');
//		             exit;
					// ->where('s.QuestionGroup = "'.$pool.'"');
	 
        $row = $db->fetchAll($select);
	      
		return $row;
	}
	
	public function checkApplication($icno){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s'=>'r015_student'))	
					 ->where('s.ARD_IC = "'.$icno.'"');
	 
        $row = $db->fetchRow($select);
	      
		return $row;
	}
	
public function checkCenter($idCenter){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s'=>'g009_venue'))	
					 ->where('s.idSachin = '.$idCenter);
	 
        $row = $db->fetchRow($select);
	      
		return $row;
	}
	
public function checkSchedule($date,$idVenue){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s'=>'s001_schedule'))	
					 ->where("s.exam_date = '".$date."'")
					 ->where('s.exam_center = '.$idVenue);
	 
        $row = $db->fetchRow($select);
	      
		return $row;
	}
	
public function getCorrectanswerSachin($idQuestion,$idAnswer){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s'=>'tbl_answers'))	
//		             ->joinLeft(array('q'=>$this->_question),'q.question_main_id = s.id',array('question'=>'q.question','idQuestion'=>'q.id'))
					 ->where('s.idanswers = '.$idAnswer)
		             ->where('s.CorrectAnswer = 1');
	 
        $row = $db->fetchAll($select);
	      
		return $row;
	}
	
public function getChapter($name){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s'=>'q002_chapter'))	
					 ->where('s.name = "'.$name.'"');
	 
        $row = $db->fetchRow($select);
	      
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
							->from($this->_name);
		
		return $selectData;
	}
	
	public function addData($table,$data){
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($table,$data);
		$id = $db->lastInsertId();
        
        return $id;
		
	}
	
	public function updateData($data,$id){
		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
}
?>