<?
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
function vardump($str)
{
	echo "<pre>";
	print_r($str);
	echo "</pre>";
}

$url = "https://newlms.magtu.ru/mod/folder/view.php?id=1223702";

$result = array();

$filename = "25.05.23-27.05.23.xlsx";

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$reader = IOFactory::createReader('Xlsx');

$spreadsheet = $reader->load($filename);
// Только чтение данных
$reader->setReadDataOnly(true);

// Количество листов

$sheetsCount = $spreadsheet->getSheetCount();

// Данные в виде массива

$data = $spreadsheet->getActiveSheet()->toArray();

$array = array();

foreach ($data as $key => $value) {
	foreach ($value as $k => $v) {
		if ($key == 1) {
			if (!isset($data[$key][$k])) {
				unset($data[$key][$k]);
			}
		}
	}
}

foreach ($data[1] as $a => &$b) {
	$b= mb_strtoupper($b);
	$array[$b] = array();
	foreach ($data as $key => $value) {
		$count = 1;
		$date;
		$day;
		foreach ($value as $k => $v) {
			if ($k == $a && $key != 1 && !is_null($v)) {
				$name = $key < 11 ? 'first_day' : (($key >= 11) && ($key < 20) ? 'second_day' : 'third_day');
				$array[$b][$date][$day][$count] = preg_split("/[^0][1-2]\./", $v);
			} else if (
				($key == 2 || $key == 11 || $key == 20) && ($k == 0 || $k == 1)
			) {
				if($k == 1) {
					$date = $v;
					$day = $data[$key][0];
				}
			} else if ($k == 2 && isset($data[$key][$k])) {
				$count = $v;
				$name = $key < 11 ? 'first_day' : (($key >= 11) && ($key < 20) ? 'second_day' : 'third_day');
				$array[$b][$date][$day][$count] = '';
			}
		}
	}
}
echo json_encode($array);