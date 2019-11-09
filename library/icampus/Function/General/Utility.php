<?php
/**
 *  @author alif 
 *  @date Jul 1, 2013
 */
 
class icampus_Function_General_Utility extends Zend_View_Helper_Abstract{
	
	public function exportPdf($html,$filename='Document.pdf'){
		
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');

//  		echo "<pre>";
//  		echo $html;
//  		echo "</pre>";
//  		exit;
 		
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		
		
		$dompdf->stream($filename);
		
		return false;
	}
}
 ?>