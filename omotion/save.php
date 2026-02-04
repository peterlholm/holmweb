<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '0');
ini_set('error_reporting', E_ALL);

$debug = 0;

$database = "motion";
$dbuser = "motion";
$dbpasswd = "acn5lIfDwXBqMbTx";
$dbhost = "localhost";

if ($debug) $dbhost = "futte.holmnet.dk";

$db = new mysqli($dbhost, $dbuser, $dbpasswd, $database);
$db->set_charset('utf8');

$datetime = "2024-03-01 10:11";

if (isset($_REQUEST['Send']) || isset($_REQUEST['save'])) {
    $DateTime = $_REQUEST['DateTime'];
    $Date = $_REQUEST['Date'];   // todo - change to current yest
    //    $Gender = $_REQUEST['Gender'];
    $Gender = 1;
    $Age = $_REQUEST['Age'];
    $Height = $_REQUEST['Height'];
    $Weight = $_REQUEST['Weight'];
    $Fat = $_REQUEST['Fat'];
    $Bone = $_REQUEST['Bone'];
    $Muscle = $_REQUEST['Muscle'];
    $Vfat = $_REQUEST['Vfat'];
    $Moisture = $_REQUEST['Moisture'];
    $Calorie = $_REQUEST['Calorie'];

    #if ($Fat > 0) {
    if (True) {
        $sql = "INSERT INTO weight (DateTime, Date, Gender, Age, Height, Weight, Fat, Bone, Muscle, Vfat, Moisture, Calorie) VALUES (\"$DateTime\", \"$Date\", $Gender, $Age, $Height, $Weight, $Fat, $Bone, $Muscle, $Vfat, $Moisture, $Calorie)";
        //echo $sql;
        if ($db->query($sql) === true) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $db->error;
        }
        $db->close();
    }
    echo "OK";
    exit(0);
}
?>
<!DOCTYPE html>
<html lang="da">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="website.css" type="text/css">
    <title>Save</title>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body>
    <div class="container">
        <form>
            <div class="mb-3">
                <label for="startdate" class="form-label">Dato Tid</label>
                <input type="datetime-local" class="form-control" id="startdate" name="DateTime" value="<?= $datetime ?>">
            </div>
            <div class="mb-3">
                <label for="Date" class="form-label">Dato</label>
                <input type="date" class="form-control" name="Date" id="Date" value="2024-03-01">
            </div>
            <div class="mb-3">
                <label for="Weight" class="form-label">Vægt</label>
                <input type="number" class="form-control" name="Weight" id="Weight" value="91.4">
            </div>
            <div class="mb-3">
                <label for="Fat" class="form-label">Fedt</label>
                <input type="number" class="form-control" name="Fat" id="Fat" value="17.9">
            </div>
            <div class="mb-3">
                <label for="Height" class="form-label">Height</label>
                <input type="number" class="form-control" name="Height" id="Height" value="179">
            </div>
            <div class="mb-3">
                <label for="Age" class="form-label">Age</label>
                <input type="number" class="form-control" name="Age" id="Age" value="67">
            </div>
            <div class="mb-3">
                <label for="Bone" class="form-label">Bone</label>
                <input type="number" class="form-control" name="Bone" id="Bone" value="2.9">
            </div>
            <div class="mb-3">
                <label for="Muscle" class="form-label">Muscle</label>
                <input type="number" class="form-control" name="Muscle" id="Muscle" value="61.9">
            </div>
            <div class="mb-3">
                <label for="Vfat" class="form-label">Vfat</label>
                <input type="number" class="form-control" name="Vfat" id="Vfat" value="17">
            </div>
            <div class="mb-3">
                <label for="Moisture" class="form-label">Moisture</label>
                <input type="number" class="form-control" name="Moisture" id="Moisture" value="60.9">
            </div>
            <div class="mb-3">
                <label for="Calorie" class="form-label">Calorie</label>
                <input type="number" class="form-control" name="Calorie" id="Calorie" value="1950">
            </div>
            <div class="mb-3">
                <input type="submit" class="btn btn-primary" name="Send" value="Send">
            </div>
        </form>
    </div>
</body>

</html>
