<?php
set_time_limit(0);
require_once 'init.php';
$current_base = check_base(intval($_GET["id"]), $active_site);
$export_uri = urldecode($_GET["url"]);
if(!$current_base)
    die("Choice base.");

require_once INCLUDE_PATH . 'PHPExcel.php';
require_once INCLUDE_PATH . 'PHPExcel/IOFactory.php';

$cdate = date('d-m-Y H.i', time());
$fileName = $current_base->title .' '.$cdate.'.xls';
$document = new PHPExcel();

$sheet = $document->setActiveSheetIndex(0); // Выбираем первый лист в документе
$first = [$current_base->ident, "Очки", "Место в рейтинге", "Ссылка на рейтинг", "Количество рефералов", "Количество рефералов (уникальные)", "Реферальных целей", "Реферальных целей (уникальные)"];
$data = [$first];

global $mdb;
$query = "SELECT * FROM `{$mdb->scores}` WHERE `base_id` = '{$current_base->id}' ORDER BY `score` DESC";
/*$query .= " ORDER BY ";
$order_conds = [];
$order_conds[] = '`event_uniq_count` DESC';
$order_conds[]  
$order = implode(", ", $order_conds);
$query .= $order; */
$refs = $mdb->get_results($query);
$refs_count = $mdb->num_rows;
$i = 1;
$old_score = 0;
foreach($refs as $ref)
{
    if($old_score > $ref->score)
        $i++;
    $rating = sprintf("%d из %d", $i, $refs_count);
    $score_link = sprintf($config["score"]["uri_format"], $current_base->id, $ref->code);

    $data[] = [$ref->ident, $ref->score, $rating, $score_link, $ref->refs, $ref->uniq_refs, $ref->events, $ref->uniq_events];
    $old_score = $ref->score;
}

$sheet->fromArray($data, NULL, 'A1');
$sheet->getColumnDimension('A')->setWidth("45");
$sheet->getColumnDimension('B')->setWidth("15");
$sheet->getColumnDimension('C')->setWidth("20");
$sheet->getColumnDimension('D')->setWidth("30");
$sheet->getColumnDimension('E')->setWidth("20");
$sheet->getColumnDimension('F')->setWidth("20");
$sheet->getColumnDimension('G')->setWidth("20");

$objWriter = PHPExcel_IOFactory::createWriter($document, 'Excel5');

header('Content-type: application/vnd.ms-excel');

header('Content-Disposition: attachment; filename="'.$fileName.'"');

$objWriter->save('php://output');
?>