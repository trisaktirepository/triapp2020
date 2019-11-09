<?php

class App_Model_Exam_DbTable_Grade extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e001_grade';
	protected $_primary = "id";
		
	public function getGrade($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Grade Program");
		}			
		return $row->toArray();
	}
	
	
	public function search($condition){
					
		$select   = $this->select()
    				->from($this->_name)
    				->order('level ASC');    				
					
		if($condition["grade_group_id"]){
			$select->where('grade_group_id = ?',$condition["grade_group_id"]);
		}	

	  
        $row = $this->fetchAll($select);
        return $row->toArray();
		
	}
	
		
	public function setupDefaultGrade($grade_group_id){
		
		  $auth = Zend_Auth::getInstance(); 
		  $mygrade = array( 
		  				 1=> array('A+','4.0','Excellent +','95','100'),
		  				 2=> array('A','3.4','Excellent','90','95'),
		  				 3=> array('B+','3.0','Very Good +','85','90'),
		  				 4=> array('B','2.6','Very Good','80','85'),
		  				 5=> array('C+','2.2','Good +','75','80'),
		  				 6=> array('C','1.8','Good','70','75'),
		  				 7=> array('D+','1.4','Pass +','65','70'),
		  				 8=> array('D','1.0','Pass','50','60'),
		  				 9=> array('F+','0.5','Weak','30','50'),
		  				 10=> array('F','0.1','Very Weak','0','30')
		  				);
		
	
		 			for($i=1; $i<=count($mygrade); $i++){
		 				$info["symbol"]=$mygrade[$i][0];
		 				$info["point"]=$mygrade[$i][1];
		 				$info["status"]=$mygrade[$i][2];
		 				$info["min_mark"]=$mygrade[$i][3];
		 				$info["max_mark"]=$mygrade[$i][4];
		 				$info["grade_group_id"]=$grade_group_id;
		 				$info["level"]=$i;
		 				$info["createdby"]=$auth->getIdentity()->id;
		 				$info["createddt"]=date("Y-m-d H:i:s");		
		 				$info["modifiedby"]=$auth->getIdentity()->id;
		 				$info["modifieddt"]=date("Y-m-d H:i:s");		 				
		 				
		 				$this->insert($info);
		 			}
				
			
	}
	
	public function addGrade($postData){
		$auth = Zend_Auth::getInstance(); 
			
		$data = array(		        
				'symbol'  	   => $postData['symbol'],
				'point'        => $postData['point'],
				'status'       => $postData['status'],
				'min_mark'     => $postData['min_mark'],
				'max_mark'     => $postData['max_mark'],
			    'grade_group_id'=> $postData['grade_group_id'],
			    'level'        => $postData['level'],
				'createdby'    => $auth->getIdentity()->id,
		 		'createddt'    => date("Y-m-d H:i:s")
				);
		
		$this->insert($data);
	}
	
	public function updateGrade($postData,$id){
		$auth = Zend_Auth::getInstance(); 
		
		$data = array(		       
				'symbol'  	   => $postData['symbol'],
				'point'        => $postData['point'],
				'status'       => $postData['status'],
				'min_mark'     => $postData['min_mark'],
				'max_mark'     => $postData['max_mark'],
				'level'        => $postData['level'],
				'modifiedby'   => $auth->getIdentity()->id,
		 		'modifieddt'   => date("Y-m-d H:i:s")
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	    
	public function deleteGrade($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
        
		
	
	public function getGradeInfo($mark){
		
		$db = Zend_Db_Table::getDefaultAdapter();	
		
		//cari main grade
		$select_main = $db->select(array('group_id'=>'gg.id'))
					      ->from(array('gg'=>'e011_grade_group'),array('group_id'=>'gg.id'))	
					      ->where('gg.group_type=1'); //1 eq to main grade
					      					    
		
		 $select   = $db->select()
    				->from($this->_name)
					->where('min_mark  <= ?',$mark)
					->where('max_mark  >= ?',$mark)					
					->where('grade_group_id = (?)', $select_main);							
       
            
        $row   = $db->fetchRow($select);

       return $row;
	}
	
	public function getGradeInfo_old($program_id,$semester_id,$mark){
		$db = Zend_Db_Table::getDefaultAdapter();	
				        
		$select = $db->select(array('group_id'=>'g.id'))
					    ->from(array('g'=>'e011_grade_group'),array('group_id'=>'g.id'))				       
				        ->join(array('cg'=>'e006_custom_grade'),'g.id = cg.grade_group_id',array())
				        ->where('cg.program_id  = ?',$program_id)
				        ->where('cg.semester_id = ?',$semester_id);
				        
		$rowSet1   = $db->fetchRow($select);
		
		//by default follow university		
		if(!is_array($rowSet1)){
			$select=1;
		}
		
		$select2   = $db->select()
    				->from($this->_name)
					->where('min_mark <= ?',$mark)
					->where('max_mark  > ?',$mark)					
					->where('grade_group_id = (?)', $select);							
       
            
        $rowSet2   = $db->fetchRow($select2);

       return $rowSet2;
    	
    }
    
}

