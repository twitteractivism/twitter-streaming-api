<?php
set_time_limit(0);
ini_set('memory_limit', '1024M');

require_once 'plugins/PHPExcel.php';
require_once 'plugins/PHPExcel/Writer/Excel2007.php';
include("db/db_config.php");

$query = "";
if(isset($_GET['query']))
{
	$query = urldecode($_GET['query']);
}
$con = mysqli_connect($db_host,$db_user,$db_password,$db_name);
// Check connection
if (mysqli_connect_errno()){
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
//set_charset when connecting with database
mysqli_set_charset( $con, 'utf8');

$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
$cacheSettings = array( 'memoryCacheSize' => '100MB');
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

$objPHPExcel = new PHPExcel();
$data = array();
$sql = $query;
$result = mysqli_query($con,$sql);
if(!$result){
	echo mysqli_error($con);
	exit(0);
}
$objPHPExcel->setActiveSheetIndex(0); 
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Tweet Link');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Tweet');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Screen Name');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Sender Profile');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Time');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Location');

$n = 2;

while ($row = mysqli_fetch_array($result)){
	$description = "";
	$location = "";
	if($result2 = mysqli_query($con, "SELECT screen_name,description,location FROM users WHERE screen_name='".$row['screen_name']."'")){
		if($row2 = mysqli_fetch_array($result2)){
		$description = $row2['description'];
		$location = $row2['location'];
		}
	}
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.(string)$n, $row['tweet_id'], PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.(string)$n, $row['tweet_text']);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.(string)$n, $row['screen_name']);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.(string)$n, $description);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.(string)$n, $row['created_at']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.(string)$n, $location);
	$objPHPExcel->getActiveSheet()->getStyle("A$n:F$n")->getAlignment()->setWrapText(true);	
	
$objPHPExcel->getActiveSheet()
    ->getCell('A'.(string)$n)
    ->getHyperlink()
    ->setUrl('http://twitter.com/'.$row['screen_name'].'/status/' . $row['tweet_id'])
    ->setTooltip('Click here to access full tweet');
	// Config
$link_style_array = [
  'font'  => [
    'color' => ['rgb' => '0000FF'],
    'underline' => 'single'
  ]
];
 
// Set it!
$objPHPExcel->getActiveSheet()->getStyle('A'.(string)$n)->applyFromArray($link_style_array);
	$n++;
}

//set column width
$objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getAlignment()->setWrapText(TRUE);
$objPHPExcel->getActiveSheet()->freezePane('A2');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
//styling
$styleArray = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FF0000'),
        'size'  => 10,
        'name'  => 'Verdana'
    ));
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="RTR_Tweets.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
$objWriter->save('php://output');
fclose(fopen("xls", "w"));
?>