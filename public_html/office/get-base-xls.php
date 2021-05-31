<?php
require_once 'init.php';

set_time_limit(0);
$current_base = check_base(intval($_GET["id"]), $active_site);
$export_uri = urldecode($_GET["url"]);
if(!$current_base)
    die("Choice base.");
if($export_uri == "")
    die("Enter export URI.");

require_once INCLUDE_PATH . 'PHPExcel.php';
require_once INCLUDE_PATH . 'PHPExcel/IOFactory.php';

$cdate = date('d-m-Y H.i', time());
$fileName = $current_base->title .' '.$cdate.'.xls';
$document = new PHPExcel();

$sheet = $document->setActiveSheetIndex(0); // Выбираем первый лист в документе
$first = [$current_base->ident, "Реферальная ссылка", "Ссылка на рейтинг"];
//if($current_base->param1 != "")
    $first[] = $current_base->param1;
//if($current_base->param2 != "")
    $first[] = $current_base->param2;
//if($current_base->param3 != "")
    $first[] = $current_base->param3;
//if($current_base->param4 != "")
    $first[] = $current_base->param4;
//if($current_base->param5 != "")
    $first[] = $current_base->param5;
$data = [$first];

global $mdb;
$query = "SELECT * FROM `{$mdb->referrers}` WHERE `base_id` = '{$current_base->id}'";

$refs = $mdb->get_results($query);
$i = 1;

$link = parse_url($export_uri);
$linkQuery = del_get_param($link["query"], "relead_ref");
$linkQuery = http_build_query($queryParts);
if($linkQuery != "")
    $linkQuery .= "&";
$linkQuery .= "rld_ref=%s";
$linkFormat = $link["scheme"] . "://" . $link["host"] . $link["path"] . "?" . $linkQuery . $link["fragment"];
foreach($refs as $ref)
{
    $ref_link = sprintf($linkFormat, $ref->code);
    $score_link = sprintf($config["score"]["uri_format"], $current_base->id, $ref->code);

    $data[] = [$ref->ident, $ref_link, $score_link, $ref->param1, $ref->param2, $ref->param3, $ref->param4, $ref->param5];
}

$sheet->fromArray($data, NULL, 'A1');
$sheet->getColumnDimension('A')->setWidth("45");
$sheet->getColumnDimension('B')->setWidth("45");
$sheet->getColumnDimension('C')->setWidth("45");
$sheet->getColumnDimension('D')->setWidth("20");
$sheet->getColumnDimension('E')->setWidth("20");
$sheet->getColumnDimension('F')->setWidth("20");
$sheet->getColumnDimension('G')->setWidth("20");

$objWriter = PHPExcel_IOFactory::createWriter($document, 'Excel5');

header('Content-type: application/vnd.ms-excel');

header('Content-Disposition: attachment; filename="'.$fileName.'"');

$objWriter->save('php://output');
?>