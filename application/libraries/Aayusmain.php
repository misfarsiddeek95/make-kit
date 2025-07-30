<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Aayusmain{

	function createHtmlName($string){
		$string = strtolower(preg_replace("/[^a-zA-Z0-9_-]/","-",$string));
		$string = str_replace("(","",$string);
		$string = str_replace(")","",$string);
		$string = str_replace("---","-",$string);
		return str_replace("--","-",$string);
	}

	/* function make_thumb($img_src, $img_th,$quality=100,$new_w=150,$new_h=150){
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
	} */

	function make_thumb($img_src, $img_th, $quality = 100, $new_w = 150, $new_h = 150) {
		$img_info = getimagesize($img_src);
		$mime_type = $img_info['mime'];

		// Load source image
		switch ($mime_type) {
			case 'image/jpeg':
				$img_in = imagecreatefromjpeg($img_src);
				$ext = 'jpg';
				break;
			case 'image/png':
				$img_in = imagecreatefrompng($img_src);
				$ext = 'png';
				break;
			case 'image/gif':
				$img_in = imagecreatefromgif($img_src);
				$ext = 'gif';
				break;
			default:
				return false;
		}

		list($old_x, $old_y) = $img_info;

		if ($old_x > $old_y) {
			$img_x = $new_w;
			$img_y = $old_y * ($new_h / $old_x);
		} elseif ($old_x < $old_y) {
			$img_x = $old_x * ($new_w / $old_y);
			$img_y = $new_h;
		} else {
			$img_x = $new_w;
			$img_y = $new_h;
		}

		$img_out = imagecreatetruecolor($img_x, $img_y);

		if ($ext === 'png') {
			// Preserve transparency
			imagealphablending($img_out, false);
			imagesavealpha($img_out, true);
			$transparent = imagecolorallocatealpha($img_out, 0, 0, 0, 127);
			imagefilledrectangle($img_out, 0, 0, $img_x, $img_y, $transparent);
		} else {
			// Fill with white background for non-transparent formats
			$white = imagecolorallocate($img_out, 255, 255, 255);
			imagefilledrectangle($img_out, 0, 0, $img_x, $img_y, $white);
		}

		imagecopyresampled($img_out, $img_in, 0, 0, 0, 0, $img_x, $img_y, $old_x, $old_y);

		// Save output image in correct format
		if ($ext === 'png') {
			imagepng($img_out, $img_th, 9);
		} elseif ($ext === 'gif') {
			imagegif($img_out, $img_th);
		} else {
			imagejpeg($img_out, $img_th, $quality);
		}

		imagedestroy($img_out);
		imagedestroy($img_in);
	}



}