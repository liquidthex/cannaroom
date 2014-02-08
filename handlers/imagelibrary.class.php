<?php

class ImageLibrary
{

	function Resize($src, $dst, $dst_w, $dst_h, $only_minimize=true, $useCrop=false)
	{
		if (file_exists($dst))
		unlink($dst);

		if(!defined('IMG_PNG')) { //gd not exists
			copy($src, $dst);
			chmod($dst, 0755);
			return true;
		}

		$file_info = getimagesize($src);
		$src_w = $file_info[0];
		$src_h = $file_info[1];
		$src_type = $file_info[2]; // file type

		$origDstW = $dst_w;
		$origDstH = $dst_h;
		list ($dst_w, $dst_h) = $this->GetResizeSize($src_w, $src_h, $dst_w, $dst_h, $useCrop);

		// only minimize
		if ($only_minimize && $dst_w > $src_w && $dst_h > $src_h) {
			copy($src, $dst);
			chmod($dst, 0755);
			return true;
		}

		$src_im = $this->CreateFromFile($src, $src_type);
		$dst_im = imagecreatetruecolor($dst_w,$dst_h);

		imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
		$this->OutputToFile($dst_im, $dst, $src_type);

		if ($useCrop) {
			$ox = intval(($dst_w-$origDstW)/2);
			$oy = intval(($dst_h-$origDstH)/2);
			$imgCropped = imagecreatetruecolor($origDstW,$origDstH);
			$file_info = getimagesize($dst);
			$dstType = $file_info[2]; // file type
			$imgResized = $this->CreateFromFile($dst, $dstType);
			imagecopyresampled($imgCropped, $imgResized, 0, 0, $ox, $oy, $origDstW,$origDstH, $origDstW, $origDstH);
			$this->OutputToFile($imgCropped, $dst, $src_type);
		}

		chmod($dst, 0755);

		return true;
	}

	function GetResizeSize($width1, $height1, $width2, $height2, $useCrop=false) {
		// 3.0 updated
		$k1 = $width1/$height1;
		$k2 = $width2/$height2;
		$q = $k1/$k2;

		if ($k1 >= 1) {
			if ($q >= 1) {
				$width = $width2;
				$height = $width/$k1;

				if ($useCrop) {
					$height = $height2;
					$width = $width2*$q;
				}
			} else {
				$height = $height2;
				$width = $width2*$q;

				if ($useCrop) {
					$width = $width2;
					$height = $width/$k1;
				}
			}
		} else {
			if ($q >= 1) {
				$height = $height2/$q;
				$width = $height*$k1;

				if ($useCrop) {
					$height = $height2;
					$width = $height*$k1;
				}
			} else {
				$height = $height2;
				$width = $height*$k1;

				if ($useCrop) {
					$height = $height2/$q;
					$width = $height*$k1;
				}
			}
		}
		return array(round($width), round($height));
	}

	function CreateFromFile($file, $type) {
		switch ($type) {
			case 1:		return imagecreatefromgif($file); break; //GIF
			case 2:		return imagecreatefromjpeg($file); break; //JPG
			case 3:		return imagecreatefrompng($file); break; //PNG
			case 4:		break; //SWF
			case 5:		break; //PSD
			case 6:		break; //BMP
			case 7:		break; //TIFF(intel byte order)
			case 8:		break; //TIFF(motorola byte order)
			case 9:		break; //JPC
			case 10:	break; //JP2
			case 11:	break; //JPX
			case 12:	break; //JB2
			case 13:	break; //SWC
			case 14:	break; //IFF
		}
	}

	function OutputToFile($im, $file, $type) {
		switch ($type) {
			case 1:		imagegif($im, $file); break; //GIF
			case 2:		imagejpeg($im, $file); break; //JPG
			case 3:		imagepng($im, $file); break; //PNG
			case 4:		break; //SWF
			case 5:		break; //PSD
			case 6:		break; //BMP
			case 7:		break; //TIFF(intel byte order)
			case 8:		break; //TIFF(motorola byte order)
			case 9:		break; //JPC
			case 10:	break; //JP2
			case 11:	break; //JPX
			case 12:	break; //JB2
			case 13:	break; //SWC
			case 14:	break; //IFF
		}
	}
}

?>