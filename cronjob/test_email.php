<?php
	
	$use_smtp = false;
    ini_set('display_errors',1);
  	
	date_default_timezone_set('Asia/Jakarta');
	
	$paths = array(
	    realpath(dirname(__FILE__) . '/../library'),
	    '.',
	);
	set_include_path(implode(PATH_SEPARATOR, $paths));
	//-- for this script (only), you reach directly the Zend folder
	//require_once('Zend/Mail.php');
	//require_once('Zend/Mail/Transport/Smtp.php');
	
	require_once('/var/www/html/triapp/library/Zend/Loader/Autoloader.php');
	$loader = Zend_Loader_Autoloader::getInstance();
	
	
    $row = array(
	    			array(
	    				'id' => -1,
	    				'recepient_email' => 'muhamad_alif@meteor.com.my',
	    				'subject' => 'Test Email',
	    				'content' => 'This is testing from Prod Server',
	    				'date_que' => date('Y-m-d H:i:s'),
	    				'attachment_filename' => '13787703_pssb_confirmation_letter.pdf',
	    				'attachment_path' => '/var/www/html/triapp/documents/applicant/022013/3199/13787703_pssb_confirmation_letter.pdf'
	    			)
	    		);
    
    
	try {

	    foreach ($row as $emailData){
	    	
		    if( $use_smtp == true ){
				/*
				 * Using SMTP server
				 */
		        $config = array(
		            'auth' => 'login',
		            'username' => "spmbtrisakti@gmail.com",
		            'password' => 'trisakti1965',
		            'ssl' => 'tls',
		            'port' => "587"
		        );
		
		        $mailTransport = new Zend_Mail_Transport_Smtp("smtp.gmail.com", $config);
		        Zend_Mail::setDefaultTransport($mailTransport);
		        
			}else{
				/*
				 * Using server's sendmail
				 */
		        $transport = new Zend_Mail_Transport_Smtp('localhost');
				Zend_Mail::setDefaultTransport($transport);
			}
	    	
	    	
		    $email = $emailData['recepient_email'];
		    $subject = $emailData['subject'];
		    $message = $emailData['content'];
		    $attachment_path = $emailData['attachment_path'];	 
		    $attachment_filename = $emailData['attachment_filename'];
		    	
		    
		    
		    //Prepare email				
	   		
		    if($attachment_path && $attachment_filename){
		    	
		   	 	$fileContents = file_get_contents($attachment_path);
		   	 	
	            $mail = new Zend_Mail();
	            $mail->setType(Zend_Mime::MULTIPART_RELATED);
				$mail->setFrom('do_not_reply@trisakti.ac.id', 'PMB-Online');			
				$mail->addTo($email); 
				$mail->setSubject($subject);
				$mail->setBodyHtml($message);	
				$file = $mail->createAttachment($fileContents);
			    $file->filename = $attachment_filename;
			    
		    }else{
		    	
		    	$mail = new Zend_Mail();
				$mail->addTo($email);
				$mail->setSubject($subject);
				$mail->setBodyHtml($message);
				$mail->setFrom('do_not_reply@trisakti.ac.id', 'PMB-Online');
			
		    }
			
			
			
			
			//Send it!
			$sent = true;
			try {
			    $mail->send();
			} catch (Exception $e){
				
			    $sent = false;
			    echo $e->getMessage();
			    echo "<br />";
			}
			
			//Do stuff (display error message, log it, redirect user, etc)
			if($sent){
			    //Mail was sent successfully.
			    echo "Mail Sended ".$emailData['id']." : ".date('Y-m-d His')."\n";
			    
			    //update table
			    /*$data = array(
					'date_send' => date("Y-m-d H:i:s")
				);
				
				$db->update('email_que', $data,'id = '.$emailData['id']);*/  
			} else {
			    //Mail failed to send.
			    echo "Mail Send Failed ".$emailData['id']." : ".date('Y-m-d His')."\n";
			}	
	    }
	    
	} catch (Zend_Exception $e){
        //Do something with exception
        echo $e->getMessage();
    }    
	
	
	exit;
?>
