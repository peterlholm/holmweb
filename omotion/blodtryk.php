<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="utf-8" >
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap4/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="website.css" type="text/css" >
    <title>Blodtryk</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>
   
<body>
    
<?php
ini_set('display_errors','1');
ini_set('display_startup_errors','0');
ini_set('error_reporting',E_ALL);

$database = "motion";
$dbuser = "motion";
$dbpasswd = "acn5lIfDwXBqMbTx";

$db = new mysqli("localhost", $dbuser, $dbpasswd, $database);
$db->set_charset('utf8');

$res = $db->query("SELECT * FROM weight WHERE PersonID=1 ORDER BY Date ASC");

$table = "['Date', 'Weight', 'Fat', 'Muscle', 'BMI'],\n";
while ($line = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
    $table .= "[new Date('" . $line['Date'] . "')," . $line['Weight'] . ", ". $line['Fat'] . ", "
	  . $line['Muscle']  . ", " . $line['BMI']  .  "],\n";
}

$res = $db->query("SELECT * FROM BlodTryk ORDER BY Dato ASC");

$bttable = "['Date', 'Systolisk', 'Diastolisk'],\n";
while ($line = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
     //print_r($line);
    $bttable .= "[new Date('" . $line['Dato'] . "')," . $line['Systolisk'] . ", ". $line['Diastolisk'] . ", "
	    .  "],\n";
}
//print_r($bttable);

$db->close();
//exit();
#print_r($table);
?>

<script>
google.charts.load('current', {'packages':['bar','corechart']});
google.charts.setOnLoadCallback(prepare);

var globdata;
    
datescale = [ new Date(2018,9,10),new Date(2018,9,15) ,new Date(2018,9,20),new Date(2018,9,25)];

    function prepare() {
        globdata  = new google.visualization.arrayToDataTable([
	<?= $bttable ?>
	]);

	drawBtColors();
         
//        drawLineColors();
//        drawBMI();
//        drawFatColors();
//        drawMuscleColors();
    }

function drawBtColors() {       
    var view = new google.visualization.DataView(globdata);
    
    view.setColumns([0, 1, 2]);
    
    var options = {
        title: 'Blodtryk',
	subtitle: 'gennem tiderne',
	legend : {
		'position': 'in',
		},
//        'legend':   'in',
        backgroundColor: '#EEEEEE',
        pointSize: 2,
        //backgroundColor.stroke: '#444',
	series: {
		0:	{targetAxisIndex: 0},
		1:	{targetAxisIndex: 1}
		},
//        trendlines: {
//            0:  {
//                //  type: 'polynomial',
//                  color: 'green',
//                  lineWidth: 3,
//                  opacity: 0.3,
//                  //showR2: true,
//                  visibleInLegend: true
//                }
//            },
        hAxis: {
          title: 'Time',
	  ticks: datescale
        },
        vAxis: {
            title: 'Bt',
            viewWindow: {
                      min: 70,
		      max: 150
            },
            ticks: [80,90,100,110,120,130,140],
            gridlines : {
                count: -1
            },
            minorGridlines: {
                count: 4
            },
	0:{
          title: 'Kg',
          viewWindow: {
                      min: 87
                  },
          ticks: [85,86,87,88,89,90,91,92],
          gridlines : {
              count: 10
          }
        },
		1: {
		title: 'Fedt%'
		}

     //   colors: ['#a52714', '#097138'],
	}
    } 
      var chart2 = new google.visualization.LineChart(document.getElementById('bt_div'));
      chart2.draw(view, options);

      };
      



 function drawLineColors() {       
    var view = new google.visualization.DataView(globdata);
    
    view.setColumns([0, 1]);
    
    var options = {
        title: 'Peters Vægt',
	subtitle: 'gennem tiderne',
	legend : {
		'position': 'in',
		},
//        'legend':   'in',
        backgroundColor: '#EEEEEE',
        pointSize: 2,
        //backgroundColor.stroke: '#444',
	series: {
		0:	{targetAxisIndex: 0},
		1:	{targetAxisIndex: 1}
		},
        trendlines: {
            0:  {
                //  type: 'polynomial',
                  color: 'green',
                  lineWidth: 3,
                  opacity: 0.3,
                  showR2: true,
                  visibleInLegend: true
                }
            },
        hAxis: {
          title: 'Time',
	  ticks: datescale
        },
        vAxis: {
            title: 'Kg',
            viewWindow: {
                      min: 87,
		      max: 91
            },
            ticks: [85,86,87,88,89,90,91,92],
            gridlines : {
                count: -1
            },
            minorGridlines: {
                count: 3
            },
	0:{
          title: 'Kg',
          viewWindow: {
                      min: 87
                  },
          ticks: [85,86,87,88,89,90,91,92],
          gridlines : {
              count: 10
          }
        },
		1: {
		title: 'Fedt%'
		}

     //   colors: ['#a52714', '#097138'],
	}
    } 
      var chart2 = new google.visualization.LineChart(document.getElementById('weight_div'));
      chart2.draw(view, options);

      };
      
 function drawFatColors() {       
    var view = new google.visualization.DataView(globdata);
    
    view.setColumns([0, 2]);
    
    var options = {
        title: 'Peters fedtprocent',
	subtitle: 'gennem tiderne',
        'legend':   'in',
        backgroundColor: '#EEEEEE',
        pointSize: 2,
        //backgroundColor.stroke: '#444',
	series: {
		0:	{targetAxisIndex: 0},
		1:	{targetAxisIndex: 1}
		},
        trendlines: {
            0:  {
                //  type: 'polynomial',
                  color: 'green',
                  lineWidth: 3,
                  opacity: 0.3,
                  showR2: true,
                  visibleInLegend: true
                }
            },
        hAxis: {
          title: 'Time',
	  ticks:  datescale
        },
        vAxis: {
            title: '%',
            gridlines : {
                count: -1
            },
            minorGridlines: {
                count: 3
            },
	0:{
          title: 'Kg',
          viewWindow: {
                      min: 85
                  },
          ticks: [85,86,87,88,89,90,91,92],
          gridlines : {
              count: 10
          }
        },
		1: {
		title: 'Fedt%'
		}

     //   colors: ['#a52714', '#097138'],
	}
    } 
      var chart2 = new google.visualization.LineChart(document.getElementById('fat_div'));
      chart2.draw(view, options);

      };
      

function drawMuscleColors() {       
    var view = new google.visualization.DataView(globdata);
    
    view.setColumns([0, 3]);
    
    var options = {
        title: 'Muscle procent',
	subtitle: 'gennem tiderne',
        'legend':   'in',
        backgroundColor: '#EEEEEE',
        pointSize: 2,
	series: {
		0:	{targetAxisIndex: 0},
		1:	{targetAxisIndex: 1}
		},
        trendlines: {
            0:  {
                //  type: 'polynomial',
                  color: 'green',
                  lineWidth: 3,
                  opacity: 0.3,
                  showR2: true,
                  visibleInLegend: true
                }
            },
        hAxis: {
          title: 'Time',
	  ticks: datescale
        },
        vAxis: {
            title: '%',
            gridlines : {
                count: -1
            },
            minorGridlines: {
                count: 3
            },
	0:{
          title: 'Kg',
          viewWindow: {
                      min: 86
                  },
          ticks: [85,86,87,88,89,90,91,92],
          gridlines : {
              count: 10
          }
        },
		1: {
		title: 'Fedt%'
		}

     //   colors: ['#a52714', '#097138'],
	}
    } 
      var chart2 = new google.visualization.LineChart(document.getElementById('muscle_div'));
      chart2.draw(view, options);

      };
      

function drawBMI() {     
    var view = new google.visualization.DataView(globdata);
    
    view.setColumns([0, 4]);
    
    var options = {
        title: 'Peters BMI',
	subtitle: 'gennem tiderne',
        'legend':   'in',
        backgroundColor: '#EEEEEE',
        pointSize: 2,
	xseries: {
		0:	{targetAxisIndex: 0},
		1:	{targetAxisIndex: 1}
		},
        trendlines: {
            0:  {
        //          type: 'polynomial',
                  color: 'green',
                  lineWidth: 3,
                  opacity: 0.3,
                  showR2: true,
                  visibleInLegend: true
                }
            },
        hAxis: {
          title: 'Time',
	  ticks:  datescale
        },
        vAxis: {
            title: 'BMI',
            baseline: 25,
            baselineColor: 'red',
            viewWindow: {
                      min: 22
            },
            ticks: [18, {v:18.5, f:'Normalvægt'},19,20,21,22,23,24, {v:25, f:'Overvægt'},26,27 ,28,29,{v:30, f: 'Svær overvægt'}],
            gridlines : {
                count: -1
            },
            xminorGridlines: {
                count: 3
            },
	0:{
          title: 'Kg',
          viewWindow: {
                      min: 85
                  },
          ticks: [85,86,87,88,89,90,91,92],
          gridlines : {
              count: 10
          }
        },
		1: {
		title: 'Fedt%'
		}

     //   colors: ['#a52714', '#097138'],
	}
       
      };
      
      var chart2 = new google.visualization.LineChart(document.getElementById('bmi_div'));
      chart2.draw(view, options);
    }
</script>
   
    <h2>Peter Blodtryks side</h2>
    
    <div id="bt_div" style="width: 50%; height: 500px; float: left;"></div>

    <script src="../js/jquery-3.3.1.slim.min.js"></script>
<!--    <script src="../js/popper.min.js"></script>-->
    <script src="../js/bootstrap4/bootstrap.min.js"></script>

</body>
</html>
