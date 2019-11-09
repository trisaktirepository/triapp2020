<?php
class Examination_Model_DbTable_Grade extends Zend_Db_Table { 
	
	protected $_name = 'tbl_gradesetup_main';
	protected $_primary = '';
	
	
	public function getGrade($semester_id,$program_id,$subject_id,$mark_obtained,$idlandscape=null){
		
		  $db = Zend_Db_Table::getDefaultAdapter();
		  $mark_obtained=round($mark_obtained,2);
		  //check setup yang base on selected semester, program & subject
		  //chek for latest setup
		  $semester_id=$this->getLatestSetup($program_id, $semester_id,$idlandscape);
		  //get base on landscape first
		  $select_grade = $db->select()
		  ->from(array('gsm'=>'tbl_gradesetup_main'))
		  ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
		  ->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
		  ->where('gsm.IdSemester = ?',$semester_id)
		  ->where('gsm.IdProgram = ?',$program_id)
		  ->where('gsm.IdSubject = ?',$subject_id)
		  ->where('gsm.IdLandscape = ?',$idlandscape)
		  ->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' <= MaxPoint");
		   
		  $grade = $db->fetchRow($select_grade);
		   
		   
		   
		  if(!$grade){
			  	
			  	$select_grade = $db->select()
			  	->from(array('gsm'=>'tbl_gradesetup_main'))
			  	->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
			  	->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
			  	->where('gsm.IdSemester = ?',$semester_id)
			  	->where('gsm.IdProgram = ?',$program_id)
			  	->where('gsm.IdLandscape = ?',$idlandscape)
			  	
			  	->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' <= MaxPoint");
			  	 
				   $grade = $db->fetchRow($select_grade);
			   //echo $select_grade;exit;
		   
				   if(!$grade){
				   
				   	$select_grade = $db->select()
				   	->from(array('gsm'=>'tbl_gradesetup_main'))
				   	->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
				   	->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
				   	->where('gsm.IdSemester = ?',$semester_id)
				   	->where('gsm.IdProgram = ?',$program_id)
				   	->where('gsm.IdLandscape = "0"')
				   
				   	->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' <= MaxPoint");
				   	 
				   	$grade = $db->fetchRow($select_grade);
				   	//echo $select_grade;exit;
	       			if(!$grade){		  
	     			//check setup yang based on selected semester and program
				    	 //echo '3<br>';
				    	$select_grade = $db->select()
		 	 				  		  ->from(array('gsm'=>'tbl_gradesetup_main'))
				 	 				  ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
				 	 				  ->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
				 	 				  ->where('gsm.IdSemester = ?',$semester_id)
				 	 				  ->where('gsm.BasedOn = 2')
				 	 				  ->where('gsm.IdProgram = ?',$program_id)
				 	 				  ->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' <= MaxPoint");
		 	 								
			   			$grade_program = $db->fetchRow($select_grade);	
			   			
			   			
			   			if(!$grade_program){
			   				
			   				 
					 	 		//get university grade based on selected semester
					 	 		//echo '5<br>';
					 	 		$select_univ = $db->select()
					 	 				  		  ->from(array('gsm'=>'tbl_gradesetup_main'))
					 	 				  		  ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
					 	 				  		  ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
							 	 				  ->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
							 	 				  ->where('gsm.BasedOn = 3')
							 	 				  ->where('gsm.IdSemester = ?',$semester_id)							 	 				 
							 	 				  ->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' <= MaxPoint");	
								 	 				   	
					 	 		$grade_univ = $db->fetchRow($select_univ);	
					 	 		$grade = $grade_univ;
					 	 				
					 	 		 
			   			
				    }else{				    	
				    	$grade = $grade_program;
				    }
	       		}
		  	}
		  }
	 	   //throw exception if grade never been setup
		   if(!$grade){
		   		throw new exception('There is no grade setup for subject id: '.$subject_id);
		   }
	       return $grade;
	}
	
	public function getGradeOlder($semester_id,$program_id,$subject_id,$mark_obtained){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$mark_obtained=round($mark_obtained,2);
		//check setup yang base on selected semester, program & subject
		  
		$select_grade = $db->select()
		->from(array('gsm'=>'tbl_gradesetup_main'))
		->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
		->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
		->where('gsm.IdSemester = ?',$semester_id)
		->where('gsm.IdProgram = ?',$program_id)
		->where('gsm.IdSubject = ?',$subject_id)
		->where("g.MinPoint <= '.$mark_obtained.' AND '.$mark_obtained.' < MaxPoint");
	
		$grade = $db->fetchRow($select_grade);
		if(!$grade){
		   
				//echo '2<br>';
				//get grade based on latest semester, program & subject
				$select_latest_grade1 = $db->select()
				->from(array('gsm'=>'tbl_gradesetup_main'))
				->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
				->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
				->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
				->where('gsm.IdProgram = ?',$program_id)
				->where('gsm.IdSubject = ?',$subject_id)
				->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' < MaxPoint")
				->where('sm.SemesterMainStartDate < (SELECT SemesterMainStartDate FROM `tbl_semestermaster` WHERE `IdSemesterMaster`=(?))',$semester_id)
				->order('SemesterMainStartDate desc');
	
				$grade_latest = $db->fetchRow($select_latest_grade1);
				 
	
				if(!$grade_latest){
	
					//check setup yang based on selected semester and program
					//echo '3<br>';
					$select_grade = $db->select()
					->from(array('gsm'=>'tbl_gradesetup_main'))
					->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
					->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
					->where('gsm.IdSemester = ?',$semester_id)
					->where('gsm.BasedOn = 2')
					->where('gsm.IdProgram = ?',$program_id)
					->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' < MaxPoint");
						
					$grade_program = $db->fetchRow($select_grade);
				  
				  
					if(!$grade_program){
	
						//echo '4<br>';
						//get grade based on latest semester & program
						$select_latest_grade2 = $db->select()
						->from(array('gsm'=>'tbl_gradesetup_main'))
						->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
						->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
						->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
						->where('gsm.IdProgram = ?',$program_id)
						->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' < MaxPoint")
						->where('sm.SemesterMainStartDate < (SELECT SemesterMainStartDate FROM `tbl_semestermaster` WHERE `IdSemesterMaster`=(?))',$semester_id)
						->order('SemesterMainStartDate desc');
							
						$grade_latest2 = $db->fetchRow($select_latest_grade2);
							
							
						if(!$grade_latest2){
	
							//get university grade based on selected semester
							//echo '5<br>';
							$select_univ = $db->select()
							->from(array('gsm'=>'tbl_gradesetup_main'))
							->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
							->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
							->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
							->where('gsm.BasedOn = 3')
							->where('gsm.IdSemester = ?',$semester_id)
							->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' < MaxPoint")
							->where('sm.SemesterMainStartDate < (SELECT SemesterMainStartDate FROM `tbl_semestermaster` WHERE `IdSemesterMaster`=(?))',$semester_id)
							->order('SemesterMainStartDate desc');
								
							$grade_univ = $db->fetchRow($select_univ);
	
	
							if(!$grade_univ){
								 
								//get university grade based on latest semester
								//echo '6<br>';
								$select_univ2 = $db->select()
								->from(array('gsm'=>'tbl_gradesetup_main'))
								->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
								->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
								->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
								->where('gsm.BasedOn = 3')
								->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' < MaxPoint")
								->where('sm.SemesterMainStartDate < (SELECT SemesterMainStartDate FROM `tbl_semestermaster` WHERE `IdSemesterMaster`=(?))',$semester_id)
								->order('SemesterMainStartDate desc');
									
								$grade_univ2 = $db->fetchRow($select_univ2);
								$grade = $grade_univ2;
	
							}else{
								$grade = $grade_univ;
							}
	
						}else{
							$grade = $grade_latest2;
						}
	
					}else{
						$grade = $grade_program;
					}
				  
				}else{
					$grade = $grade_latest;
				}
			
		}
		//throw exception if grade never been setup
		if(!$grade){
			throw new exception('There is no grade setup for subject id: '.$subject_id);
		}
		return $grade;
	}
	
	public function getGradeByGradePoint($semester_id,$program_id,$subject_id,$gradepoint,$idlandscape=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$semester_id=$this->getLatestSetup($program_id, $semester_id,$idlandscape);
		 
		$select_grade = $db->select()
		->from(array('gsm'=>'tbl_gradesetup_main'))
		->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
		->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
		->where('gsm.IdSemester = ?',$semester_id)
		->where('gsm.IdProgram = ?',$program_id)
		->where('gsm.IdSubject = ?',$subject_id)
		->where("g.GradePoint <= ?",$gradepoint)
		->order('g.GradePoint DESC');
		
		
			
		$grade = $db->fetchRow($select_grade);
			
			
			
		if(!$grade){
		//check setup yang base on selected semester, program & subject
			$select_grade = $db->select()
			->from(array('gsm'=>'tbl_gradesetup_main'))
			->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
			->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
			->where('gsm.IdSemester = ?',$semester_id)
			->where('gsm.IdProgram = ?',$program_id)
			->where('gsm.IdLandscape = ?',$idlandscape)
			->where("g.GradePoint <= ?",$gradepoint)
			->order('g.GradePoint DESC');
			
		$grade = $db->fetchRow($select_grade);
		 
		 
		 
		if(!$grade){
			 
			 
	
				//check setup yang based on selected semester and program
				//echo '3<br>';
				$select_grade = $db->select()
				->from(array('gsm'=>'tbl_gradesetup_main'))
				->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
				->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
				->where('gsm.IdSemester = ?',$semester_id)
				->where('gsm.BasedOn = 2')
				->where('gsm.IdProgram = ?',$program_id)
				->where("g.GradePoint <= ?",$gradepoint)
				->order('g.GradePoint DESC');
			
				$grade = $db->fetchRow($select_grade);
				 
				 
				 
			 
			}
		}
		//throw exception if grade never been setup
		if(!$grade){
			throw new exception('There is no grade setup for subject id: '.$subject_id);
		}
		return $grade;
	}
	
	public function getGradePointByGrade($semester_id,$program_id,$subject_id,$gradeori,$idlandscape=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$gradeori=trim($gradeori);
		
		$semester_id=$this->getLatestSetup($program_id, $semester_id,$idlandscape);
		 
		//check setup yang base on selected semester, program & subject
		$select_grade = $db->select()
		->from(array('gsm'=>'tbl_gradesetup_main'))
		->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
		->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
		->where('gsm.IdSemester = ?',$semester_id)
		->where('gsm.IdProgram = ?',$program_id)
		->where('gsm.IdSubject = ?',$subject_id)
		->where("d.DefinitionDesc = ?",$gradeori); 
		//echo $select_grade;exit;
		$grade = $db->fetchRow($select_grade);
			
			
			
		if(!$grade){
	
			 	//check latest setup using landscape				
			$select_grade = $db->select()
			->from(array('gsm'=>'tbl_gradesetup_main'))
			->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
			->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
			->where('gsm.IdSemester = ?',$semester_id)
			->where('gsm.IdProgram = ?',$program_id)
			->where('gsm.IdLandscape = ?',$idlandscape)
			->where("d.DefinitionDesc = ?",$gradeori);
			
			$grade_landscape = $db->fetchRow($select_grade);
			
			if(!$grade_landscape){
	 
					//echo '3<br>';
					$select_grade = $db->select()
					->from(array('gsm'=>'tbl_gradesetup_main'))
					->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
					->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
					->where('gsm.IdSemester = ?',$semester_id)
					->where('gsm.BasedOn = 2')
					->where('gsm.IdProgram = ?',$program_id)
					->where("d.DefinitionDesc = ?",$gradeori);
					
					 $grade_program = $db->fetchRow($select_grade);
	
	
						if(!$grade_program){
	
							//get university grade based on latest semester
							//echo '6<br>';
							$select_univ = $db->select()
							->from(array('gsm'=>'tbl_gradesetup_main'))
							->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
							->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
							->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
							->where('gsm.BasedOn = 3')
							->where("d.DefinitionDesc = ?",$gradeori)
							->where('gsm.IdSemester = ?',$semester_id);
							$grade_univ = $db->fetchRow($select_univ);
							$grade = $grade_univ;
	
						}else{
							$grade = $grade_program;
						}
					}else{
					$grade = $grade_landscape;
				}
					
			 
		}
		//throw exception if grade never been setup
		if(!$grade){
			//$grade = 'E';
			$grade = array(
				'GradeName' => 'E',
				'GradeDesc' => '',
				'GradePoint' => 0	
			);
			
			//throw new exception('There is no grade setup for subject id: '.$subject_id);
		}
		return $grade;
	}
	
	public function getGradeold($semester_id,$program_id,$subject_id,$mark_obtained){
		
		  $db = Zend_Db_Table::getDefaultAdapter();
		
     			  
		  
		    //check setup yang base on semester, program & subject
		  $select_grade = $db->select()
	 	 				  		  ->from(array('gsm'=>'tbl_gradesetup_main'))
			 	 				  ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
			 	 				  ->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
			 	 				  ->where('gsm.IdSemester = ?',$semester_id)
			 	 				  ->where('gsm.IdProgram = ?',$program_id)
			 	 				  ->where('gsm.IdSubject = ?',$subject_id)
			 	 				  ->where("g.MinPoint <= '.$mark_obtained.' AND '.$mark_obtained.' <= MaxPoint");	 	 
			
		   $grade = $db->fetchRow($select_grade);
		   
		   echo '1<br>';
		   
		   if(!$grade){		    

		   	 echo '2<br>';
		   	  //check setup yang base on semester and program
			  $select_grade = $db->select()
		 	 				  		  ->from(array('gsm'=>'tbl_gradesetup_main'))
				 	 				  ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
				 	 				  ->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
				 	 				  ->where('gsm.IdSemester = ?',$semester_id)
				 	 				  ->where('gsm.BasedOn = 2')
				 	 				  ->where('gsm.IdProgram = ?',$program_id)
				 	 				  ->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' <= MaxPoint");
		 	 								
			   $grade_program = $db->fetchRow($select_grade);		

			   
			   if(!$grade_program){
			   	
				   	 //get latest grade			  
				    echo '3<br>';
				    $select_latest_grade1 = $db->select()
					 	 				  		  ->from(array('gsm'=>'tbl_gradesetup_main'))
					 	 				  		  ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
					 	 				  		  ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
							 	 				  ->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
							 	 				  ->where('gsm.IdProgram = ?',$program_id)
							 	 				  ->where('gsm.IdSubject = ?',$subject_id)
							 	 				  ->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' <= MaxPoint")
							 	 				  ->where('sm.SemesterMainStartDate < (SELECT SemesterMainStartDate FROM `tbl_semestermaster` WHERE `IdSemesterMaster`=(?))',$semester_id)			 	 				
							 	 				  ->order('SemesterMainStartDate desc');	
							 	 				   	
				 	 $grade_latest = $db->fetchRow($select_latest_grade1);		
				 	 
				 	 if(!$grade_latest){
				 	 	 echo '4<br>';
				 	 	 $select_latest_grade2 = $db->select()
					 	 				  		  ->from(array('gsm'=>'tbl_gradesetup_main'))
					 	 				  		  ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
					 	 				  		  ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
							 	 				  ->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
							 	 				  ->where('gsm.IdProgram = ?',$program_id)							 	 				 
							 	 				  ->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' <= MaxPoint")
							 	 				  ->where('sm.SemesterMainStartDate < (SELECT SemesterMainStartDate FROM `tbl_semestermaster` WHERE `IdSemesterMaster`=(?))',$semester_id)			 	 				
							 	 				  ->order('SemesterMainStartDate desc');	
							 	 				   	
				 	 	$grade_latest2 = $db->fetchRow($select_latest_grade2);	
				 	 	
				 	 	
				 	 	
				 	 	if(!$grade_latest2){
				 	 		//get university grade
				 	 		$select_univ = $db->select()
					 	 				  		  ->from(array('gsm'=>'tbl_gradesetup_main'))
					 	 				  		  ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
					 	 				  		  ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain',array('GradeName','GradeDesc','GradePoint','Pass'))
							 	 				  ->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
							 	 				  ->where('gsm.BasedOn = 3')							 	 				 
							 	 				  ->where("g.MinPoint <= '".$mark_obtained."' AND '".$mark_obtained."' <= MaxPoint")
							 	 				  ->where('sm.SemesterMainStartDate < (SELECT SemesterMainStartDate FROM `tbl_semestermaster` WHERE `IdSemesterMaster`=(?))',$semester_id)			 	 				
							 	 				  ->order('SemesterMainStartDate desc');	
							 	 				   	
				 	 		$grade_univ = $db->fetchRow($select_univ);	
				 	 		$grade = $grade_univ;
				 	 		
				 	 	}else{
				 	 		$grade = $grade_latest2;
				 	 	}
				 	 	
				 	 }else{
				 	 	$grade = $grade_latest;
				 	 }
			   	
			   }else{			   	
			   	$grade = $grade_program;
			   }
			   
			}
			   
			   //throw exception if grade never been setup
			   if(!$grade){
			   	throw new exception('There is no grade setup for subject id: '.$subject_id);
			   }
			   
		   return $grade;
	}
	
	
	
	
	
	public function listsubjectreg(){
		$sql="
				SELECT IdStudentRegSubjects, a.IdSemesterMain, IdProgram, IdSubject, final_course_mark
				FROM `tbl_studentregsubjects` a
				INNER JOIN tbl_studentregistration AS b ON a.IdStudentRegistration = b.IdStudentRegistration
				WHERE a.`IdSemesterMain` =1
				AND final_course_mark IS NOT NULL 
			";
		$db = Zend_Db_Table::getDefaultAdapter();
		$rows=$db->fetchAll($sql);
		return $rows;
	}
	
	
	public function getExamStatus($idSemester,$idProgram,$idSubject){		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//based on selected semester
		$select 	= $db->select()
					     ->from(array("gsm" => "tbl_gradesetup_main"),array())
					     ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain')
			             ->where("gsm.IdProgram = ?",$idProgram)
			             ->where("gsm.IdSemester = ?",$idSemester)
			             ->where("gsm.IdSubject = ?",$idSubject)
			             ->where("g.grade ='428'");
			             
		$result = $db->fetchRow($select);
		
		if(!$result){			
			
			//based on latest setup semester
			$select2 	= $db->select()
						     ->from(array("gsm" => "tbl_gradesetup_main"),array())
						     ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain')
						     ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
				             ->where("gsm.IdProgram = ?",$idProgram)				            
				             ->where("gsm.IdSubject = ?",$idSubject)
				             ->where("g.grade ='428'")
				             ->where('gsm.BasedOn = 2')
				             ->where('sm.SemesterMainStartDate < (SELECT SemesterMainStartDate FROM `tbl_semestermaster` WHERE `IdSemesterMaster`=(?))',$idSemester)			 	 				
							 ->order('SemesterMainStartDate desc');
				             
			$result2 = $db->fetchRow($select2);
			
			
			if(!$result2){
				
				//based on selected semester
				$select3 	= $db->select()
							     ->from(array("gsm" => "tbl_gradesetup_main"),array())
							     ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain')
					             ->where("gsm.IdProgram = ?",$idProgram)
					             ->where("gsm.IdSemester = ?",$idSemester)					           
					             ->where("g.grade ='428'");
					             
				$result3 = $db->fetchRow($select3);				
				
				if(!$result3){
					
						//based on latest setup semester
						$select4 	= $db->select()
									     ->from(array("gsm" => "tbl_gradesetup_main"),array())
									     ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain')
									     ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
							             ->where("gsm.IdProgram = ?",$idProgram)	
							             ->where("g.grade ='428'")
							             ->where('gsm.BasedOn = 2')
							             ->where('sm.SemesterMainStartDate < (SELECT SemesterMainStartDate FROM `tbl_semestermaster` WHERE `IdSemesterMaster`=(?))',$idSemester)			 	 				
										 ->order('SemesterMainStartDate desc');
							             
						$result4 = $db->fetchRow($select4);
			
						if(!$result4){
							
								//based on selected semester
								$select5 	= $db->select()
											     ->from(array("gsm" => "tbl_gradesetup_main"),array())
											     ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain')
											     ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())									            
									             ->where('gsm.IdSemester = ?',$idSemester)
									             ->where("g.grade ='428'")
									             ->where('gsm.BasedOn = 3')									             
									             ->where('sm.SemesterMainStartDate < (SELECT SemesterMainStartDate FROM `tbl_semestermaster` WHERE `IdSemesterMaster`=(?))',$idSemester)			 	 				
												 ->order('SemesterMainStartDate desc');
									             
								$result5 = $db->fetchRow($select5);	

								if(!$result5){
									
									//based on selected semester
									$select6 	= $db->select()
												     ->from(array("gsm" => "tbl_gradesetup_main"),array())
												     ->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain')
												     ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())										           
										             ->where("g.grade ='428'")
										             ->where('gsm.BasedOn = 3')									             
										             ->where('sm.SemesterMainStartDate < (SELECT SemesterMainStartDate FROM `tbl_semestermaster` WHERE `IdSemesterMaster`=(?))',$idSemester)			 	 				
													 ->order('SemesterMainStartDate desc');
										             
									$result6 = $db->fetchRow($select6);	
									$grade = $result6;
								}else{
									$grade = $result5;
								}
						}else{
							$grade = $result4;
						}
				}else{
					$grade = $result3;
				}
			}else{
				$grade = $result2;
			}
		}
		
		 if(!$grade){
		   	throw new exception('There is no grade setup for subject id: '.$idSubject);
		   }
	       return $grade;
	}
	public function getGradePropertiByGrade($idSemester,$idProgram,$idSubject,$grade,$idlandscape=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$idSemester=$this->getLatestSetup($idProgram, $idSemester,$idlandscape);
		 
		//based on selected semester
		$select 	= $db->select()
		->from(array("gsm" => "tbl_gradesetup_main"),array())
		->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain')
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
		->where("def.DefinitionDesc = ?",$grade)
		->where("gsm.IdProgram = ?",$idProgram)
		->where("gsm.IdSemester = ?",$idSemester)
		->where("gsm.IdSubject = ?",$idSubject);
		
		//->where("g.grade ='428'");
	
		$result = $db->fetchRow($select);
	
		if(!$result){
			$select2 	= $db->select()
			->from(array("gsm" => "tbl_gradesetup_main"),array())
			->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain')
			->join(array('def'=>'tbl_definationms'),'def.IdDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
			->where("def.DefinitionDesc = ?",$grade)
			->where("gsm.IdProgram = ?",$idProgram)
			->where("gsm.IdSemester = ?",$idSemester)
			->where("gsm.IdLandscape = ?",$idlandscape);
			
			 
			 
			$result2 = $db->fetchRow($select2);
				
				
			if(!$result2){
	
				//based on selected semester
				$select3 	= $db->select()
				->from(array("gsm" => "tbl_gradesetup_main"),array())
				->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain')
				->join(array('def'=>'tbl_definationms'),'def.IdDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
				->where("def.DefinitionDesc = ?",$grade)
				->where("gsm.IdProgram = ?",$idProgram)
				->where("gsm.IdSemester = ?",$idSemester);
				//->where("g.grade ='428'");
	
				$result3 = $db->fetchRow($select3);
	
				if(!$result3){
					 
						 
							//based on selected semester
							$select6 	= $db->select()
							->from(array("gsm" => "tbl_gradesetup_main"),array())
							->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain')
							->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
							->join(array('def'=>'tbl_definationms'),'def.IdDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
							->where("def.DefinitionDesc = ?",$grade)
							//->where("g.grade ='428'")
							->where('gsm.BasedOn = 3')
							->where('sm.IdSemesterMaster=? ',$idSemester)
							->order('SemesterMainStartDate desc');
							 
							$result6 = $db->fetchRow($select6);
							$grade = $result6;
						 
				}else{
					$grade = $result3;
				}
			}else{
				$grade = $result2;
			}
		} else $grade = $result;
	
		if(!$grade){
			throw new exception('There is no grade setup for subject id: '.$idSubject);
		}
		return $grade;
	}
	
	public function getGradeProgram($idProgram,$idSemester) {
		$db = Zend_Db_Table::getDefaultAdapter();
		//get latest semester setup
		$select4 	= $db->select()
		->from(array("gsm" => "tbl_gradesetup_main"),array())
		->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain')
		->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array('IdSemesterMaster'))
		->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
		->where("gsm.IdProgram = ?",$idProgram)
		//->where("g.grade ='428'")
		//->where('gsm.BasedOn = 2')
		->where('g.GradePoint > 0')
		->where('sm.SemesterMainStartDate < (SELECT SemesterMainStartDate FROM `tbl_semestermaster` WHERE `IdSemesterMaster`=(?))',$idSemester)
		->order('sm.SemesterMainStartDate DESC');
		
		$result4 = $db->fetchRow($select4);
		if ($result4) $idSemester=$result4['IdSemesterMaster'];
		//
		$select4 	= $db->select()
		->from(array("gsm" => "tbl_gradesetup_main"),array())
		->join(array('g'=>'tbl_gradesetup'),'g.IdGradeSetUpMain=gsm.IdGradeSetUpMain')
		->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester',array())
		 ->join(array('d'=>'tbl_definationms'),'d.idDefinition=g.Grade',array('GradeName'=>'DefinitionDesc'))
		->where("gsm.IdProgram = ?",$idProgram)
		->where("sm.IdSemesterMaster = ?",$idSemester)
		//->where("g.grade ='428'")
		//->where('gsm.BasedOn = 2')
		->where('g.GradePoint > 0')
		->order('g.GradePoint DESC');
		
		$result4 = $db->fetchAll($select4);
		return $result4;
	}
	
	public function getLatestSetup($idprogram,$idsemester,$idlandscape=null) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
		//-----------------
		$select = $db->select()
		->from(array('gsm'=>'tbl_gradesetup_main'),array())
		->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester')
		->where('gsm.IdProgram = ?',$idprogram)
		->where('gsm.IdLandscape = ?',$idlandscape)
		->where('sm.SemesterMainStartDate <= (select SemesterMainStartDate from tbl_semestermaster where IdSemesterMaster=?)',$idsemester)
		->order('SemesterMainStartDate desc');
		
		$sem_latest = $db->fetchRow($select);
		
		if (!$sem_latest) {
			$select = $db->select()
			->from(array('gsm'=>'tbl_gradesetup_main'),array())
			->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=gsm.IdSemester')
			->where('gsm.IdProgram = ?',$idprogram)
			->where('gsm.IdLandscape = "0"')
			->where('sm.SemesterMainStartDate <= (select SemesterMainStartDate from tbl_semestermaster where IdSemesterMaster=?)',$idsemester)
			->order('SemesterMainStartDate desc');
			
			$sem_latest = $db->fetchRow($select);
			//echo var_dump($sem_latest);exit;
		}
		return $sem_latest['IdSemesterMaster'];
	}
}
?>