<?php

//$debug = 1;
$dbhost = "localhost";
$database = "motion";
$dbuser = "motion";
$dbpasswd = "acn5lIfDwXBqMbTx";

if ($debug) {
    $dbhost = "garfield.holmnet.dk";
}

$db = new mysqli($dbhost, $dbuser, $dbpasswd, $database);
$db->set_charset('utf8');

function get_avarage($start_date, $end_date)
{
    global $db;
    //echo("Startdate $start_date End_date $end_date <br> ");
    $sql2 = "SELECT AVG(Weight) FROM weight WHERE PersonID=1 AND DATE>'" . $start_date .  "' AND DATE<='" . $end_date . "' ";
    $res = $db->query($sql2);
    //print_r($res);
    $val = mysqli_fetch_array($res);
    //print_r($val);
    //echo "VAL ". $val[0] ."<br>";
    if ($val[0] == "") $val[0] = 0;
    return $val[0];
}

function get_data($start_date)
{
    global $db;
    $sql = "SELECT * FROM weight WHERE PersonID=1 AND DATE>'" . $start_date . "' ORDER BY Date ASC";
    $res = $db->query($sql);
    $table = "['Date', 'Weight', 'Fat', 'Muscle', 'BMI', 'Moisture', 'Vfat', 'MuscleP'],\n";
    while ($line = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $table .= "[new Date('" . $line['Date'] . "')," . $line['Weight'] . ", " . $line['Fat'] . ", "
            . $line['Muscle']  . ", " . $line['BMI'] . ", " . $line['Moisture'] . ", " . $line['Vfat'] . ", " . $line['MuscleP'] .  "],\n";
    }
    return $table;
}

function get_js_alldata($start_date)
{
    global $db;
    $sql = "SELECT Date,Weight,Fat,Bone,Muscle,Vfat,Moisture,BMI,MuscleP FROM weight WHERE PersonID=1 AND DATE>'" . $start_date . "' ORDER BY Date ASC";
    $res = $db->query($sql);

    $data = "const data_arr = [";
    $data .= "['Date','Weight','Fat','Bone','Muscle','Vfat','Moisture','BMI','MuscleP'],\n";

    while ($line = mysqli_fetch_array($res, MYSQLI_NUM)) {
        //print_r($line);
        $data .= "[";
        foreach ($line as $e) {
            //echo $e;
            $data .= "$e, ";
        }
        $data .= "],\n";
    }
    $data .= "];\n";   
    return $data;
}


function get_js_field($start_date, $field) 
{
    global $db;
    $sql = "SELECT Date,$field FROM weight WHERE PersonID=1 AND DATE>'" . $start_date . "' ORDER BY Date ASC";
    //echo $sql;
    $res = $db->query($sql);
    $data = "const $field"."_data = [\n";
    while ($line = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $data .= "{x: '" . $line['Date'] . "', y : " . $line[$field] . "},\n" ;
    }
    $data .= "];\n"; 
    return $data;
}

function get_js_weight($start_date) 
{
    global $db;
    $sql = "SELECT Date,Weight FROM weight WHERE PersonID=1 AND DATE>'" . $start_date . "' ORDER BY Date ASC";
    //echo $sql;
    $res = $db->query($sql);
    $data = "const weight_data = [\n";
    while ($line = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $data .= "{x: '" . $line['Date'] . "', y : " . $line['Weight'] . "},\n" ;
    }
    $data .= "];\n";   
    return $data;
}

// $jsdata = get_js_alldata("2024-04-25");
// echo $jsdata;
