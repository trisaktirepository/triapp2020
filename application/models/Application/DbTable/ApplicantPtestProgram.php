<?php 
class App_Model_Application_DbTable_ApplicantPtestProgram extends Zend_Db_Table_Abstract
{
    protected $_name = 'applicant_ptest_program';
	protected $_primary = "app_id";
	
	
	public function getPlacementProgram($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	     $select = $db ->select()
					->from(array('app'=>$this->_name))
					->joinLeft(array('aprog'=>'appl_placement_program'),'aprog.app_id=app.app_ptest_program')
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=aprog.app_program_code',array('program_name'=>'p.ProgramName','program_code'=>'p.ProgramCode'))
					->joinLeft(array('d'=>'tbl_departmentmaster'),'p.idFacucltDepartment=d.IdDepartment',array('faculty'=>'d.DeptName'))
					->where("app.app_at_trans_id = '".$transaction_id."'")
					//->where("app.app_ptest_code = '".$placement_test_code."'")
					->order("app.app_preference Asc");
					
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
		
	}
	
	
	public function getProgramPre($appl_id,$placement_test_code,$preference){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	     $select = $db ->select()
					->from(array('app'=>$this->_name))					
					->where("app.app_appl_id = '".$appl_id."'")
					->where("app.app_preference = '".$preference."'")
					->where("app.app_ptest_code = '".$placement_test_code."'");
					
        $row = $db->fetchRow($select);
       
		return $row;
	}
	
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function deleteTransactionData($transaction_id){
		$this->delete('app_at_trans_id =' . (int)$transaction_id);
	}
	
	
	public function deleteDataProgram($appl_id,$ptest_code){		
	  $this->delete("app_at_trans_id  = '".$appl_id."' AND app_ptest_code = '".$ptest_code."'");
	  
		//echo $sql ="DELETE FROM `trisakti_app`.`applicant_ptest_program` WHERE `applicant_ptest_program`.`app_id` = '$appl_id' AND app_ptest_code = '$ptest_code'";
	}
	
	
	
	
	
	
	
	
	
}
?>