<?php
class Zend_View_Helper_Clash
{
    protected $_subject;
    
    public function subjectClashList($semester_id,$registration_id)
    {
        //get registered subject for selected semester
        $courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
        $courses = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$idSemester);

        $Schedule = new App_Model_Record_DbTable_StudentRegistration();
        
        $normalSchedule = array(
                'Monday' => array(),
                'Tuesday' => array(),
                'Wednesday' => array(),
                'Thursday' => array(),
                'Friday' => array()
            );
        $dateSchedule   = array();
        
        if($courses){
            $this->view->registeredcourses = $courses;
            
            /**
             *START Generate Schedule
             */
            foreach($courses as $y => $course)
            {
                $idSubject = $course['IdSubject'];
                $idGroup   = $course['IdCourseTaggingGroup'];
                
                if($idGroup != 0)
                {
                    
                    $schedules = $Schedule->getSubjectSchedule($idSubject,$idGroup);
                
                    if(!empty($schedules))
                    {
                        $x = 0;
                        $y = 0;
                        
                        $DetailSubject = new App_Model_Record_DbTable_SubjectMaster();
                        
                        foreach ($schedules as $schedule)
                        {
                            if($schedule['sc_date']!= '')
                            {
                                /**
                                 *Arrange array by date
                                 */
                                
                                $start_time = explode(':',$schedule['sc_start_time']);
                                $end_time   = explode(':',$schedule['sc_end_time']);
                                $start      = $start_time[0];
                                $end        = $end_time[0];
                                
                                $detailSubject = $DetailSubject->getData($schedule['IdSubject']);
                                
                                $dateSchedule[$schedule['sc_date']]['sc_day'] = $schedule['sc_day'];
                                
                                
                                for($x=(int)$start;$x<$end;$x++)
                                {
                                    
                                    $xKey = sprintf("%02d",$x);
                                    
                                    if(isset($dateSchedule[$schedule['sc_date']][$xKey]))
                                    {
                                        /**
                                         *If array has value, append new subject
                                         */
                                        @$dateSchedule[$schedule['sc_date']][$xKey] = $dateSchedule[$schedule['sc_date']][$xKey].'<br />'. $detailSubject['ShortName'];  
                                    }
                                    else
                                    {
                                        /**
                                         * Assign value to array for the first time
                                         */
                                         @$dateSchedule[$schedule['sc_date']][$xKey] = $detailSubject['ShortName'];
                                    }
                                                                                 
                                }
                                
                               
                            }
                            else
                            {
                                /**
                                 *Arrange array for normal schedule (Monday,Tuesday ....)
                                 */
                                $start_time = explode(':',$schedule['sc_start_time']);
                                $end_time   = explode(':',$schedule['sc_end_time']);
                                $start      = $start_time[0];
                                $end        = $end_time[0];
                                
                                $detailSubject = $DetailSubject->getData($schedule['IdSubject']);
                                
                                //$dateSchedule['sc_day'] = $schedule['sc_day'];
                                
                                if(isset($normalSchedule[$schedule['sc_day']][$start]))
                                {
                                    /**
                                     *If array has value, append new subject
                                     */
                                    for($x=(int)$start;$x<$end;$x++)
                                    {
                                        $xKey = sprintf("%02d",$x);
                                        
                                        @$normalSchedule[$schedule['sc_day']][$xKey] = $normalSchedule[$schedule['sc_day']][$xKey].'<br />'. $detailSubject['ShortName'];
                                    }
                                }
                                else
                                {
                                    /**
                                     * Assign value to array for the first time
                                     */
                                    for($x=(int)$start;$x<$end;$x++)
                                    {
                                        $xKey = sprintf("%02d",$x);
                                        
                                        @$normalSchedule[$schedule['sc_day']][$$xKey] = $detailSubject['ShortName'];
                                    }
                                }
                                
                            }
                        }//end foreach $schedule
                        
                    }//end empty $schedule
                }
            }
        }
        
        $clash_list = array();
        
        
        
        
    }

    public function subjectClashSingle($registration_id,$idSemester,$subject_id)
    {
        $courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
        $courses = $courseRegisterDb->getCourseRegisteredBySemesterSubject($registration_id,$idSemester,$subject_id);
        
        print_r($courses);
    }
    
    public function examClash($semester_id,$student_id,$subject_id)
    {
    
    }
}
?>