<?php
// ubuntu 需安裝 package
//  tesseract (OCR tool)
//     $ sudo apt-get install tesseract-ocr tesseract-ocr-chi-tra
//
//  imagemagick
//     $ sudo apt-get install imagemagick

function get_data()
{
	$jsonstr = file_get_contents("http://campaign-finance.g0v.ctiml.tw/api/getrandoms");
	$datalist = json_decode($jsonstr, true);
	return $datalist;
}

function save_png($url, $fname)
{
	$data = file_get_contents($url);
	
	if(empty($data))
		return null;

	$fp = fopen($fname, 'w');
	fwrite($fp, $data);
	fclose($fp);
	return $fname;
}

function resize($fname, $height)
{
	system("convert -resize x{$height} $fname {$fname}_new.png");
	unlink($fname);
	rename("{$fname}_new.png", $fname);
}

function tesseract($fname)
{
	if(empty($fname))
		return null;

	system("tesseract $fname output > /dev/null");
	$eng = file_get_contents("output.txt");

	system("tesseract -l chi_tra $fname output > /dev/null");
	$cht = file_get_contents("output.txt");

	unlink("output.txt");

	return array(
		'eng' => $eng,
		'cht' => $cht,
	);
}
?>

<table border=1>
<tr><th>圖</th><th>英文</th><th>中文</th></tr>

<?php
$list = get_data();

foreach($list as $data)
{
	$url = $data['img_url'];
	$id = $data['id'];
	$fname = "$id.png";

	$fname = save_png($url, $fname);
	if(empty($fname))
		continue;
//	resize($fname, 40);
	$result = tesseract($fname);

	echo <<<DOC
	<tr><td><img src="{$fname}"/></td><td>{$result['eng']}</td><td>{$result['cht']}</td></tr>
DOC;
}
?>

</table>
