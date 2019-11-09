<?php 

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);		
		$params = array('host'=>'127.0.0.1',
						'username' => 'root',
						'password'=>'',
						'dbname'=>'icampus'
					);
		$db= Zend_Db::factory('Pdo_Mysql', $params);
		
		$sql = 'SELECT * FROM sys006_language'; 
		$result = $db->fetchAll($sql);
			

	/*$array = array(
			'Home'=>'منزل',
			'Dashboard'=>'لوحة القيادة',
			'Staff Information'=>'معلومات الموظ�?ين',
			'Staff Movement'=>'حركة الموظ�?ين',
			'Leave'=>'إجازة',
			'Attendance'=>'الحضور',
			'Training'=>'تدريب',
			'Recruitment'=>'تجنيد',
			'Overtime'=>'إضا�?ي',
			'Benefits'=>'ال�?وائد',
			'General Setup'=>'الإعداد العام',
			'Logout'=>'خروج',
	
			'Employee Profile'=>'موظ�? الشخصي',
	
			'Nationality'=>'جنسية',
			'Identification'=>'تحديد الهوية',
			'Personal ID'=>'الهوية الشخصية',
			'Date of issue'=>'تاريخ صدوره',
			'Expiry Date'=>'تاريخ انتهاء الصلاحية',
			'Place of Issue'=>'بدلا من العدد',
			'First Name'=>'أول اسم',
			'Second Name'=>'الاسم الثاني',
			'Third Name'=>'الاسم الثالث',
			'Forth Name'=>'عليها اسم',
			'Surname'=>'لقب'	
			
		);*/	
			
	 $info=array();
	 foreach($result as $data){
        	$key = $data["english"];
        	$val = $data["arabic"];
        	$info[$key]=$val;        	
      } 
       
	return $info;