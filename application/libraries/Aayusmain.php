<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Aayusmain{

	function createHtmlName($string){
		$string = strtolower(preg_replace("/[^a-zA-Z0-9_-]/","-",$string));
		$string = str_replace("(","",$string);
		$string = str_replace(")","",$string);
		$string = str_replace("---","-",$string);
		return str_replace("--","-",$string);
	}

	function make_thumb($img_src, $img_th,$quality=100,$new_w=150,$new_h=150){
		$img_size = GetImageSize ($img_src);
		$img_in = ImageCreateFromJPEG ($img_src);
		list($old_x, $old_y) = getimagesize($img_src);
		if ($old_x > $old_y) {
			$img_x=$new_w;
			$img_y=$old_y*($new_h/$old_x);
		}
		if ($old_x < $old_y) {
			$img_x=$old_x*($new_w/$old_y);
			$img_y=$new_h;
		}
		if ($old_x == $old_y) {
			$img_x=$new_w;
			$img_y=$new_h;
		}
		$img_out = ImageCreateTrueColor($img_x, $img_y);
		ImageCopyResampled ($img_out, $img_in, 0, 0, 0, 0, $img_x, $img_y, $img_size[0], $img_size[1]);
		ImageJPEG ($img_out, $img_th, $quality);
		ImageDestroy ($img_out);
		ImageDestroy ($img_in);
	}
}