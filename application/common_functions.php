<?php
/**
 ** Common function
 **/

function pr($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';

}

/**
 * prints variable values and code location
 *
 * @param mixed $variable
 * */
function debug($variable)
{
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
    echo "<pre>";
    echo "file:<strong> " . $backtrace[0]['file'] . "</strong>\n";
    echo "line : <strong>" . $backtrace[0]['line'] . "</strong>\n";

    print_r($variable);
    echo "</pre>";
}

/**
 * like debug but
 * exits as well
 *
 * @param mixed $variable
 * */
function dd($variable)
{
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
    echo "<pre>";
    echo "file:<strong> " . $backtrace[0]['file'] . "</strong>\n";
    echo "line : <strong>" . $backtrace[0]['line'] . "</strong>\n";

    print_r($variable);
    echo "</pre>";
    exit;
}

/**
 * this changes a flat array into a multi-dimension array based on field value
 * $records an array of records returned from a query
 * $groupbyfield = array('country','state','fief')
 *
 * @param unknown $records
 * @param unknown $groupbyfield
 * @return unknown
 * */
function flat2structured($records, $groupbyfield)
{
    $varname = array();
    $groupbyes = $groupbyfield; //array('country','state','fief');

    $variablename = '$varname';
    foreach ($groupbyes as $groupby) {
        $variablename .= '[\'{' . strtolower($groupby) . '}\']';
    }

    $variablename .= '[] = $record;';
    foreach ($records as $record) {
        $tobereplace = $variablename;
        foreach ($record as $key => $value) {
            $tobereplace = str_replace("{" . strtolower($key) . "}", $value, $tobereplace);
        }
        eval($tobereplace);
    }
    return $varname;
}

//Clears the array of fields that's not in the schema.
//mysql/mariadb ONLY
function friendly_columns($table, $data) {

    $db = getDb();
    $sql = "Describe " . $table ;
    $result = $db->query($sql);

    $schema =  $result->fetchAll();
    foreach($schema as $row) {
        $fields[]  = $row['Field'];
    }


    foreach($data as $fieldname => $value) {
        if(!in_array($fieldname, $fields)) {
            unset($data[$fieldname]);
        }
    }

    return($data);


}

//get the date part correctly from dojo calendar input
function splitAndFormatDojodate($date) {
    $positionoftime = strpos($date, '00:00:00');
    $date_parts = str_split($date, $positionoftime);
    $datetodatabase = date('Y-m-d', strtotime($date_parts[0]));

    return($datetodatabase);
}

function time_ago($time)
{
    $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths = array("60","60","24","7","4.35","12","10");

    $now = time();

    $difference     = $now - $time;
    $tense         = "ago";

    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);

    if($difference != 1) {
        $periods[$j].= "s";
    }

    return "$difference $periods[$j] ".$tense;
}