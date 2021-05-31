<?php
function create_base($site_id, $title, $ident, $param1 = "", $param2 = "", $param3 = "", $param4 = "", $param5 = "") {
    
    if($site_id == 0 || $title == "" || $ident == "")
        return ["result" => EMPTY_FIELDS];
    
    global $mdb;
    $query = "SELECT COUNT(`id`) FROM `{$mdb->bases}` WHERE `site_id` = '{$site_id}' AND `title` = '{$title}'";
    $checkTitle = $mdb->get_var($query);
    if($checkTitle > 0)
        return ["result" => REPEAT_STRING];
    $date = time();
    $query = "INSERT INTO `{$mdb->bases}` SET `site_id` = '{$site_id}', `title` = '{$title}', `ident` = '{$ident}', `param1` = '{$param1}', `param2` = '{$param2}', `param3` = '{$param3}', `param4` = '{$param4}', `param5` = '{$param5}', `create_date` = '{$date}'";
    $insert = $mdb->query($query);
    if(!$insert)
        return ["result" => UNKNOWN_ERROR];
    return ["result" => SUCCESS, "id" => $mdb->insert_id];
}

function import_base_part($sheet, $start_row, $end_row, $ident, $param1, $param2, $param3, $param4, $param5, $site_id, $base_id) {
    $count = 0;
    for ($row = $start_row; $row <= $end_row; $row++) { 
        $ident_val = sqlstring($sheet->getCellByColumnAndRow($ident, $row));

        if($param1 != -1)
            $param1_val = sqlstring($sheet->getCellByColumnAndRow($param1, $row));
        if($param2 != -1)
            $param2_val = sqlstring($sheet->getCellByColumnAndRow($param2, $row));
        if($param3 != -1)
            $param3_val = sqlstring($sheet->getCellByColumnAndRow($param3, $row));
        if($param4 != -1)
            $param4_val = sqlstring($sheet->getCellByColumnAndRow($param4, $row));
        if($param5 != -1)
            $param5_val = sqlstring($sheet->getCellByColumnAndRow($param5, $row));
        $code = "";
        
        $insert = insert_base($site_id, $base_id, $ident_val, $code, $param1_val, $param2_val, $param3_val, $param4_val, $param5_val);
        if($insert)
            $count++;
    }
    
    return $count;
    
}

function import_base($site_id, $base_id, $file, $start_row = 2, $ident, $param1 = -1, $param2 = -1, $param3 = -1, $param4 = -1, $param5 = -1, $deletefile = true, $tmpbase_name = false) {
    global $mdb;
    include_once INCLUDE_PATH . 'ExcelChunk.php';
    $count = 0;
    $objReader = PHPExcel_IOFactory::createReaderForFile($file);
 
   // $objReader = PHPExcel_IOFactory::createReaderFo("Excel2007");
    $chunkSize = 1000;
    $chunkFilter = new chunkReadFilter();
    $objReader->setReadFilter($chunkFilter);
    $objReader->setReadDataOnly(true);
    //$start_row = 16010;
    if($_SESSION["start_row_{$base_id}"])
        $start_row = $_SESSION["start_row_{$base_id}"];
        
    for($startRow = $start_row; $startRow < 100000; $startRow+=$chunkSize) {
        $_SESSION["start_row_{$base_id}"] = $startRow;
        $chunkFilter->setRows($startRow,$chunkSize);
        $xls = $objReader->load($file);
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();

        $highestRow = $sheet->getHighestDataRow(); 
        $import_part = import_base_part($sheet, $startRow, $highestRow, $ident, $param1, $param2, $param3, $param4, $param5, $site_id, $base_id);
        $count += $import_part;
        unset($sheet);
        unset($xls);
        if($highestRow < ($chunkSize - 1))
            break;
    }
    
    unset($objReader); 

    
    
    if($deletefile) {
        unlink($file);
        if($tmpbase_name !== false)
        {
            $query = "UPDATE `{$mdb->temp_bases}` SET `deleted` = '1' WHERE `fname` = '{$tmpbase_name}'";
            $result = $mdb->query($query);
        }
    }
    
    return [SUCCESS, $count];
}

function delete_old_bases() {
    
}

function insert_base($site_id, $base_id, $ident, $code = "", $param1 = "", $param2 = "", $param3 = "", $param4 = "", $param5 = "")
{
    global $mdb;
    $date = time();
    if($ident == "") 
        return EMPTY_FIELDS;
 
    if($code != "")
    {
        $checkCode = $mdb->get_var("SELECT COUNT(*) FROM `{$mdb->referrers}` WHERE `code` = '{$code}' AND `site_id` = '{$site_id}'");
        if($checkCode > 0)
            return REPEAT_CODE;
    } else {
        $code = gen_code($site_id, $ident, $date);
    }

    $check_ident = $mdb->get_var("SELECT COUNT(*) FROM `{$mdb->referrers}` WHERE `base_id` = '{$base_id}' AND `ident` = '{$ident}'");
    if($check_ident > 0)
        return REPEAT_STRING;
    
    $insert = $mdb->insert($mdb->referrers, array(
        "site_id" => $site_id,
        "base_id" => $base_id,
        "ident" => $ident,
        "code" => $code,
        "param1" => $param1,
        "param2" => $param2,
        "param3" => $param3,
        "param4" => $param4,
        "param5" => $param5,
    ));
    
    if(!$insert)
        return UNKNOWN_ERROR;
    return SUCCESS;
}

function upload_base($client_id, $tmp_name) {
    global $mdb;
	$date = time();
	$i = 1;
	do {
		$fname = md5($date . $client_id . $i++);
		$dest_path = TMP_BASE_PATH . $fname;
	} while (file_exists($dest_path));
	$loaded = move_uploaded_file($tmp_name, $dest_path);
	
	$query = "INSERT INTO `{$mdb->temp_bases}` SET `fname` = '{$fname}', `load_date` = '{$date}', `client_id` = '{$client_id}'";
	$insert = $mdb->query($query);

	delete_old_bases();
	
	return ["result" => $loaded, "fname" => $fname];
}
?>