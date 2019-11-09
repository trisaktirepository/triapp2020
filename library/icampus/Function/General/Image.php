<?php
/**
 *  @author alif 
 *  @date Jul 1, 2013
 */
 
class icampus_Function_General_Image extends Zend_View_Helper_Abstract{
	
	public function getImagePath($filepathname,$width,$height){

		//path
		$filebroken = explode( '/', $filepathname);
		$filename = array_pop($filebroken);
		$path = implode('/', $filebroken);
		
		//new filename
		$file = explode( '.', $filename);
		$extension = array_pop($file);
		$newfilename_without_ext = implode('.', $file);
		
		$newFileName = $newfilename_without_ext."-".$width."x".$height.".".$extension;
		
		$newfilepathname = $path."/".$newFileName;
		
		
		if( file_exists($newfilepathname) ){
			
			$dt = explode("triapp",$newfilepathname);
			$path = $dt[1];
			return $path;
			exit;
		}else{

			//resize image
			$params = array(
					'constraint' => array('width' => $width, 'height' => $height)
			);
			
			if($this->img_resize($filepathname, $newfilepathname, $params)){
				
				$dt = explode("triapp",$newfilepathname);
				$path = $dt[1];
				return $path;
			}else{
				return null;
			}
			
			
		}
		
	}
	
	
	/**
	 * Images scaling
	 * @param string  $ini_path Path to initial image.
	 * @param string $dest_path Path to save new image.
	 * @param array $params [optional] Must be an associative array of params
	 * $params['width'] int New image width.
	 * $params['height'] int New image height.
	 * $params['constraint'] array.$params['constraint']['width'], $params['constraint'][height]
	 * If specified the $width and $height params will be ignored.
	 * New image will be resized to specified value either by width or height.
	 * $params['aspect_ratio'] bool If false new image will be stretched to specified values.
	 * If true aspect ratio will be preserved an empty space filled with color $params['rgb']
	 * It has no sense for $params['constraint'].
	 * $params['crop'] bool If true new image will be cropped to fit specified dimensions. It has no sense for $params['constraint'].
	 * $params['rgb'] Hex code of background color. Default 0xFFFFFF.
	 * $params['quality'] int New image quality (0 - 100). Default 100.
	 * @return bool True on success.
	 */
	
	private function img_resize($ini_path, $dest_path, $params = array()) {
		$width = !empty($params['width']) ? $params['width'] : null;
		$height = !empty($params['height']) ? $params['height'] : null;
		$constraint = !empty($params['constraint']) ? $params['constraint'] : false;
		$rgb = !empty($params['rgb']) ?  $params['rgb'] : 0xFFFFFF;
		$quality = !empty($params['quality']) ?  $params['quality'] : 100;
		$aspect_ratio = isset($params['aspect_ratio']) ?  $params['aspect_ratio'] : true;
		$crop = isset($params['crop']) ?  $params['crop'] : true;
	
		if (!file_exists($ini_path)) return false;
	
		if (!is_dir($dir=dirname($dest_path))) mkdir($dir);
	
		$size = getimagesize($ini_path);
		if ($size === false) return false;
	
		$ini_p = $size[0]/$size[1];
		if ( $constraint ) {
			$con_p = $constraint['width']/$constraint['height'];
			$calc_p = $constraint['width']/$size[0];
	
			if ( $ini_p < $con_p ) {
				$height = $constraint['height'];
				$width = $height*$ini_p;
			} else {
				$width = $constraint['width'];
				$height = $size[1]*$calc_p;
			}
		} else {
			if ( !$width && $height ) {
				$width = ($height*$size[0])/$size[1];
			} else if ( !$height && $width ) {
				$height = ($width*$size[1])/$size[0];
			} else if ( !$height && !$width ) {
				$width = $size[0];
				$height = $size[1];
			}
		}
	
		preg_match('/\.([^\.]+)$/i',basename($dest_path), $ext);
		$output_format = $ext[1];
		
		$output_format = strtolower($output_format);
		
		if($output_format=='jpg'){
			$output_format = 'jpeg';
		}
		
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		$icfunc = "imagecreatefrom" . $format;
	
		$iresfunc = "image" . $output_format;
	
		if (!function_exists($icfunc)) return false;
	
		$dst_x = $dst_y = 0;
		$src_x = $src_y = 0;
		$res_p = $width/$height;
		if ( $crop && !$constraint ) {
			$dst_w  = $width;
			$dst_h = $height;
			if ( $ini_p > $res_p ) {
				$src_h = $size[1];
				$src_w = $size[1]*$res_p;
				$src_x = ($size[0] >= $src_w) ? floor(($size[0] - $src_w) / 2) : $src_w;
			} else {
				$src_w = $size[0];
				$src_h = $size[0]/$res_p;
				$src_y    = ($size[1] >= $src_h) ? floor(($size[1] - $src_h) / 2) : $src_h;
			}
		} else {
			if ( $ini_p > $res_p ) {
				$dst_w = $width;
				$dst_h = $aspect_ratio ? floor($dst_w/$size[0]*$size[1]) : $height;
				$dst_y = $aspect_ratio ? floor(($height-$dst_h)/2) : 0;
			} else {
				$dst_h = $height;
				$dst_w = $aspect_ratio ? floor($dst_h/$size[1]*$size[0]) : $width;
				$dst_x = $aspect_ratio ? floor(($width-$dst_w)/2) : 0;
			}
			$src_w = $size[0];
			$src_h = $size[1];
		}
	
		$isrc = $icfunc($ini_path);
		$idest = imagecreatetruecolor($width, $height);
		if ( ($format == 'png' || $format == 'gif') && $output_format == $format ) {
			imagealphablending($idest, false);
			imagesavealpha($idest,true);
			imagefill($idest, 0, 0, IMG_COLOR_TRANSPARENT);
			imagealphablending($isrc, true);
			$quality = 0;
		} else {
			imagefill($idest, 0, 0, $rgb);
		}
		imagecopyresampled($idest, $isrc, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
		$res = $iresfunc($idest, $dest_path, $quality);
	
		imagedestroy($isrc);
		imagedestroy($idest);
	
		return $res;
	}
}
 ?>