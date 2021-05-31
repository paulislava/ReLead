<?php
/**
 * Получение строк/строки из базы данных
 * @param string $table Название таблицы
 * @param array $where Параметры поиска
 * @param string|array $columns Искомые столбцы
 * @param 
 */
function db_where(string $table, array $where = [], $columns = '*', $many = false)
{
    global $mdb;
    if (!is_array($where)) {
        return false;
    }

    foreach ($where as $key => &$val) {
        $n_val = [];
        $n_val['value'] = $val;
        $n_val['format'] = '%s';
        $val = $n_val;
    }

    if (false === $where) {
        return false;
    }

    $conditions = $values = array();
    foreach ($where as $field => $value) {
        if (is_null($value['value'])) {
            $conditions[] = "`$field` IS NULL";
            continue;
        }

        $conditions[] = "`$field` = " . $value['format'];
        $values[]     = $value['value'];
    }

    $conditions = implode(' AND ', $conditions);
    if (is_array($columns)) {
        foreach ($columns as &$column) {
            $column = '`' . $column . '`';
        }
        $columns_cond = implode(', ', $columns);
    } else
        $columns_cond = '*';
    $sql = "SELECT $columns_cond FROM `$table`";
    if (!empty($conditions))
        $sql .= " WHERE {$conditions}";

    if (!$many)
        $sql .= ' LIMIT 1';

    $mdb->check_current_query = false;

    $query = $mdb->prepare($sql, $values);
    if ($many)
        return $mdb->get_results($query);
    else
        return $mdb->get_row($query);
}

function get_where_cond($table, $where, $where_format = '')
{


    $conditions = $values = array();
    foreach ($where as $field => $value) {
        if (is_null($value)) {
            $conditions[] = "`$field` IS NULL";
            continue;
        }

        $conditions[] = get_param_query_string($field, $value);
    }

    $conditions = implode(' AND ', $conditions);

    return $conditions;
}

function get_queries_string($queries, $separator = 'AND')
{
    $result = implode(" {$separator} ", array_map(function ($i) {
        return "({$i})";
    }, $queries));
    return $result;
}

function pil_header() {
    include TEMPLATE_PATH . 'system/header.php';
}

function pil_footer() {
    include TEMPLATE_PATH . 'system/footer.php';
}

function pil_office_sidebar() {
    include TEMPLATE_PATH . 'office/sidebar.php';
}

function pil_office_header() {
    include TEMPLATE_PATH . 'office/header.php';
}

function pil_office_footer() {
    include TEMPLATE_PATH . 'office/footer.php';
}

function soltstring($string)
{
    return md5(SOLT. $string);
}



function render_site($url = '', $referrer = '')
{
    global $mdb;
    $parts = parse_url($url);
    $date = time();
    $host = sqlstring($parts["host"]);
    $utf8_host = idn_to_utf8($host);
    parse_str($parts["query"], $urlQuery);
    $path = $parts["path"];

    if(!$referrer)
        $referrer = $urlQuery["rld_ref"];
    if($host == "")
        return false;
    // Get Site info
    $query = "SELECT `id`, `client_id`, `https_check` FROM `{$mdb->sites}` WHERE `domain` = '{$host}' OR `domain` = '{$utf8_host}'";
    $getSite = $mdb->get_row($query);
    if(!$getSite)
        return false;
    $query = "SELECT `end_date` FROM `{$mdb->clients}` WHERE `id` = '{$getSite->client_id}'";
    $end_date = $mdb->get_var($query);
    if($end_date != 0 && $end_date <= $date)
        return false;
   // var_dump($parts);
    $site["data"] = $getSite;
    $site["url"] = $parts;
    $site["referrer"] = sqlstring($referrer);
    return $site;
}

function referral_id($siteID, $referrer = "", $path = "", $referral = "") {
    global $mdb, $config;
    $referrer = sqlstring($referrer);
    // $referral_code = sqlstring($_COOKIE["relead_id_{$siteID}"]);
    $referral_id = $_SESSION["relead_id_{$siteID}"];
   // if($referral_code == "")
     //   $referral_code = sqlstring($referral);
     if(!$referral_id)
         $referral_code = sqlstring($referral);
    //var_dump($referral_code);
    
    // Check session value
    if($referral_id != 0)
    {
        $query = "SELECT `id_code`, `ref_id` FROM `{$mdb->referrals}` WHERE `id` = '{$referral_id}' AND `site_id` = '{$siteID}'";
        $ref  = $mdb->get_row($query);
        $code = $ref->id_code;
        //var_dump($query);

        //die();
        if($code)
            return ["id" => $referral_id, "code" => $code, "ref_id" => $ref->ref_id];
    }
    // Check cookie value
    if($referral_code != "")
    {
        $query = "SELECT `id`, `ref_id` FROM `{$mdb->referrals}` WHERE `id_code` = '{$referral_code}' AND `site_id` = '{$siteID}'";
        $ref = $mdb->get_row($query);
        $id = $ref->id;
        //var_dump($query);
        //die();
        if($id)
            return ["id" => $id, "code" => $referral_code, "ref_id" => $ref->ref_id];
    }
    // Check get value
    if($referrer != "")
    {
        $query = "SELECT `id` FROM `{$mdb->referrers}` WHERE `code` = '{$referrer}'";
        $ref_id = $mdb->get_var($query);
        
        if($ref_id)
        {
            $date = time();
            $ip = $_SERVER["REMOTE_ADDR"];
            $referral_code = md5($date . $ref_id . $ip . SALT);
            $query = "INSERT INTO `{$mdb->referrals}` SET `id_code` = '{$referral_code}', `ref_id` = '{$ref_id}', `site_id` = '{$siteID}', `url` = '{$path}', `ip` = '{$ip}', `date` = '{$date}'";
            $insert = $mdb->query($query);


            if($insert) 
            {
                $referral_id = $mdb->insert_id;

                $query = "UPDATE `{$mdb->referrers}` SET 
                `refs` = (SELECT COUNT(*) FROM `{$mdb->referrals}` WHERE `ref_id` = '{$ref_id}'), 
                `uniq_refs` = (SELECT COUNT(DISTINCT(`ip`)) FROM `{$mdb->referrals}` WHERE `ref_id` = '{$ref_id}') 
                WHERE `id` = '{$ref_id}'";
                $update_refs = $mdb->query($query);

                $_SESSION["relead_id_{$siteID}"] = $referral_id;
                setcookie("relead_id_{$siteID}", $referral_code, $date + $config["referral"]["expire"]);
                return ["id" => $referral_id, "code" => $referral_code, "ref_id" => $ref_id];
            }
        }
    }
    return false;
}


function sql_where($table, $data, $esc_val = false, $esc_key = false) {
    global $mdb;
    $query = "SELECT * FROM `{$table}` WHERE ";
    $where = [];
    foreach($data as $key=>$val) {
        if($esc_key)
            $key = sqlstring($key);
        if($esc_val)
            $val = sqlstring($val);
        $where[] = "`{$key}` = '{$val}'";
    }
    $query .= implode(" AND ", $where);
    $query.= " LIMIT 1";
    $result = $mdb->get_row($query);
    return $result;
}

function sqlstring($string, $notags = true)
{
    global $mdb;
    if($notags)
        $string = strip_tags($string);
    return trim($mdb->_real_escape($string));
}
function check_base($base_id, $site = 0) {
    global $active_site, $mdb;
    if($site == 0)
        $site = $active_site;
    if($base_id > 0)
    {
        $get_base = sql_where($mdb->bases, array("site_id" => $site, "id" => $base_id));
        return $get_base;
    } else 
        return false;
}

function check_event($event_id, $site = 0) {
    global $active_site, $mdb;
    if($site == 0)
        $site = $active_site;
    if($event_id > 0)
    {
        $get_event = sql_where($mdb->event_ids, array("site_id" => $site, "id" => $event_id));
        return $get_event;
    } else 
        return false;
}

function display_menu($menu, $active_id = false, $tag = "ul", $tagclass = [], $litag = "li", $liclass = []) {
    global $Page;
    if(!$active_id)
        $active_id = $Page["id"];
    $class = implode(" ", $tagclass);
    echo '<'.$tag.' class="'.$class.'">';
    foreach($menu as $item) {
        display_menu_item($item, $active_id, $litag, $liclass);
        if(is_array($item["submenu"]) && count($item["submenu"] > 0)) {
        /*echo '<ul class="submenu" id="item-'.$item["id"].'-sub">';
        foreach($item["submenu"] as $subItem) {
            display_menu_item($subItem);
        }
        echo '</ul>'; */
        $subclass = $tagclass;
        $subclass[] = "submenu";
        display_menu($item["submenu"], $active_id, $tag, $subclass, $litag, $liclass);
        }
    }
    echo '</'.$tag.'>';
}

function display_menu_item($item, $active_id, $tag = "li", $tagclass = []) {
    $classes = $item["classes"];
    if($active_id == $item["id"] || (is_array($active_id) && in_array($item["id"], $active_id)))
        $classes[] = "active";
    $classes = array_merge($classes, $tagclass);
    $class = implode(" ", $classes);
    echo '<'.$tag.' class="'.$class.'" id="item-'. $item["id"] .'"><a href=' . $item["href"] . '>' . $item['title'] . '</a></'.$tag.'>';
}

function set_get_param($query, $param, $val) {
    parse_str($query, $params);
    $params[$param] = $val;
    return http_build_query($params);
}

function del_get_param($query, $param) {
    parse_str($query, $params);
    unset($params[$param]);
    return http_build_query($params);
}

function gen_code($site_id, $ident, $time = 0) {
    if($time == 0)
        $time = time();
        
    global $mdb;
	$i = 1;
	do {
	$code = strtoupper(substr(md5($ident.$site_id.$i++), 5, 5));
    $check = $mdb->get_var("SELECT COUNT(*) FROM `{$mdb->referrers}` WHERE `site_id` = '{$site_id}' AND `code` = '{$code}'");
    } while($check > 0);
	return $code;
}

function format_money($summ) {
    return number_format($summ, 0, '', ' ');
}

function confirm_pay($pay_id)
{
    global $mdb;
    $pay_id = intval($pay_id);
    $date = time();
    $pay = sql_where($mdb->pays, array("id" => $pay_id));
    if(!$pay)
        return false;
    $query = "UPDATE `{$mdb->pays}` SET `update_date` = '{$date}', `success` = '1' WHERE `id` = '{$pay_id}'";
    $update = $mdb->query($query);
    if(!$update)
        return false;
    
    return prolong_tarif($pay->client, $pay->tarif);
}


function prolong_tarif($client_id, $tarif_id) {
    global $mdb;
    $client = sql_where($mdb->clients, array("id" => $client_id));
    $tarif = sql_where($mdb->tarifs, array("id" => $tarif_id));
    if(!$client || !$tarif)
        return false;
    $current_end = max($client->end_date, time());
    $end_date = $current_end + intval($tarif->duration);
    $query = "UPDATE `{$mdb->clients}` SET `tarif` = '{$tarif_id}', `end_date` = '{$end_date}', `first_pay` = '1' WHERE `id` = '{$client_id}'";
    $update = $mdb->query($query);
    return $update;
}

function url_with_param($param, $val) {
    $parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    $query = $parts[1];
    parse_str($query, $params);
    $params[$param] = $val;
    if($val === false)
        unset($params[$param]);
    return $parts[0] . '?'. http_build_query($params);
}

function html_attrs($attrs) {
    $attrs = array_map(function($key) use ($attrs)
    {
        if(is_bool($attrs[$key]))
        {
            return $attrs[$key]?$key:'';
        }
        return $key.'="'.$attrs[$key].'"';
    }, array_keys($attrs));
    return join(' ' , $attrs);
}

/**
 * Склонение исчисляемых
 * @param int $number Число
 * @param string[] $after Массив исчисляемых в формате ['яблоко', 'яблока', 'яблок']
 * @param bool $show_number Показывать число в выводе
 * @return string Число и слово в подходящей форме через пробел
 */
function plural_form(int $number, array $after, $show_number = true) : string
{

    $cases = array(2, 0, 1, 1, 1, 2);
    $result = $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    if($show_number)
        $result = $number . ' ' . $result;
    return $result;
}
?>