<?php
/* 
 *   input file: in.jpg
 *   output file: out.png
 *
 * Usage: 
 *
 *   $ php strip_watermark.php
 *
 * PHP-EXT:
 *   
 *   (Ubuntu) php5-gd
 * 
 */
class gd_util
{
	public static function GetNewSizeByMaxLength($gdImageResource, $target_max_length = 4000)
	{
		$orig_info = array(
			'width' => imagesx($gdImageResource),
			'height' => imagesy($gdImageResource),
			);

		$result = $orig_info;


		if($orig_info['width'] < $target_max_length && $orig_info['height'] < $target_max_length)
		{
			return $result;
		}

		if($orig_info['width'] > $orig_info['height'])
		{
			$key_long = 'width';
			$key_short = 'height';
		}
		else
		{
			$key_long = 'height';
			$key_short = 'width';
		}

		$result[$key_long] = $target_max_length;
		$result[$key_short] = $result[$key_long] * ($orig_info[$key_short] / $orig_info[$key_long]);
		$result[$key_short] = intval($result[$key_short] + 0.5);

		return $result;
	}

	public static function GetResizedImageResource($orig_gdImageResource, $new_width, $new_height)
	{
		//self::Blur($orig_gdImageResource);
		imagefilter($orig_gdImageResource, IMG_FILTER_SMOOTH, 3);

		$orig_info = array(
				'width' => imagesx($orig_gdImageResource),
				'height' => imagesy($orig_gdImageResource),
				);

		$newim = imagecreatetruecolor($new_width, $new_height);
		imagecopyresized($newim, $orig_gdImageResource, 0,0,0,0, $new_width, $new_height, $orig_info['width'], $orig_info['height']);

		return $newim;
	}

	public static function Blur($gdImageResource)
	{
		//imagefilter($gdImageResource, IMG_FILTER_SELECTIVE_BLUR);
//		imagefilter($gdImageResource, IMG_FILTER_SMOOTH, -1);

		$gaussian = array(
			array(2.0, 2.0, 2.0), 
			array(2.0, 4.0, 2.0), 
			array(2.0, 2.0, 2.0),
		);

		$sum = 0;
		foreach($gaussian as $row)
		{
			foreach($row as $col)
			{
				$sum += $col;
			}
		}
		imageconvolution($gdImageResource, $gaussian, $sum, 0);
	}

	public static function ThresholdFilter($gdImageResource, $threshold)
	{
		imagefilter($gdImageResource, IMG_FILTER_GRAYSCALE);

	    $width = imagesx($gdImageResource);
	    $height = imagesy($gdImageResource);

		$cBlack = imagecolorallocate($gdImageResource, 0, 0, 0);
		$cWhite = imagecolorallocate($gdImageResource, 255, 255, 255);

		for($x=0;$x<$width;$x++)
		{
			for($y=0;$y<$height;$y++)
			{
				$rgb=imagecolorat($gdImageResource,$x,$y);
				// r: ($rgb>>16)&0xFF;
				// g: ($rgb>>8)&0xFF;
				// b: $rgb&0xFF;
				$graylevel = $rgb&0xFF;

				if($graylevel > $threshold)
				{
					imagesetpixel($gdImageResource, $x, $y, $cWhite);
				}
				else
				{
				//	imagesetpixel($gdImageResource, $x, $y, $cBlack);
				}
			}
		}

		return;
	}
}

$im = imagecreatefromjpeg('in.jpg');

$orig_info = array(
		'width' => imagesx($im),
		'height' => imagesy($im),
		);

print_r($orig_info);
echo ($orig_info['width'] / $orig_info['height']) . PHP_EOL;

$size = gd_util::GetNewSizeByMaxLength($im);
print_r($size);
echo ($size['width'] / $size['height']) . PHP_EOL;

echo "Resizing...\n";
$newim = gd_util::GetResizedImageResource($im, $size['width'], $size['height']);

echo "Blur ... \n";
gd_util::Blur($newim);

echo "Do threshold ... \n";
gd_util::ThresholdFilter($newim, 95);

echo "Save out.png ... \n";
imagepng($newim, 'out.png');

imagedestroy($im);
imagedestroy($newim);

?>
