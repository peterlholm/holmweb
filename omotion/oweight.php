<!DOCTYPE html>
<html lang="da">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="website.css" type="text/css">
    <title>Motion</title>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body>
    <div class="container-lg">
        <?php
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        ini_set('error_reporting', E_ALL);

        $debug = 1;

        $minweight = 86;
        $maxweight = 91;

        $dbhost = "localhost";
        $database = "motion";
        $dbuser = "motion";
        $dbpasswd = "acn5lIfDwXBqMbTx";

        if ($debug) {
            $dbhost = "futte.holmnet.dk";
        }

        function get_avarage($start_date, $end_date) {
            global $db;
            //echo("Startdate $start_date End_date $end_date <br> ");
            $sql2 = "SELECT AVG(Weight) FROM weight WHERE PersonID=1 AND DATE>'" . $start_date .  "' AND DATE<='" . $end_date ."' ";
            $res = $db->query($sql2);
            //print_r($res);
            $val = mysqli_fetch_array($res);
            //print_r($val);
            //echo "VAL ". $val[0] ."<br>";
            if ($val[0] =="") $val[0] = 0;
            return $val[0];
        }

        $db = new mysqli($dbhost, $dbuser, $dbpasswd, $database);
        $db->set_charset('utf8');
        $start_date = '2024-03-01';
        if (isset($_GET['startdate'])) {
            $startdate = $_GET['startdate'];
            //echo "startdate format YYYY-MM-DD : $startdate";
            $start_date = $startdate;
        }

        $now = time();
        $oneweek = 60 * 60 * 24 * 7;	
        $today = date("y-m-d", $now);
        $lastweek =  date("y-m-d", $now - $oneweek);	
        $firstweek =  date("y-m-d", $now - 2*$oneweek);	
        //echo "Today $today Lastweek $lastweek Firstweek $firstweek <br>\n";
        $vagt1 = get_avarage($firstweek, $lastweek);
        $vagt2 = get_avarage($lastweek, $today);
        //echo "First week $vagt1 Second week $vagt2 <br>";
        if (($vagt1 == 0) || ($vagt2 == 0)) {
            $vagtdiff = 0;
        } else {
            $vagtdiff = round(($vagt1 - $vagt2)*1000);
        }
        echo "Vægtændring / uge: $vagtdiff g<br>";
        
        $sql = "SELECT * FROM weight WHERE PersonID=1 AND DATE>'" . $start_date . "' ORDER BY Date ASC";
        $res = $db->query($sql);
        $table = "['Date', 'Weight', 'Fat', 'Muscle', 'BMI'],\n";
        while ($line = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
            $table .= "[new Date('" . $line['Date'] . "')," . $line['Weight'] . ", " . $line['Fat'] . ", "
                . $line['Muscle']  . ", " . $line['BMI']  .  "],\n";
        }

        $db->close();
        #print_r($table);
        ?>

        <script>
            // google.charts.load('current', {
            //     'packages': ['bar', 'corechart']
            // });
            // google.charts.setOnLoadCallback(prepare);

            var globdata;

            datescale = [new Date(2019, 1, 1), new Date(2019, 4, 1), new Date(2019, 7, 1), new Date(2019, 10, 1),
                new Date(2020, 1, 1), new Date(2020, 4, 1), new Date(2020, 7, 1), new Date(2020, 10, 1),
                new Date(2021, 1, 1), new Date(2021, 4, 1), new Date(2021, 7, 1), new Date(2021, 10, 1),
                new Date(2022, 1, 1), new Date(2022, 4, 1)
            ];

            function prepare() {
                globdata = new google.visualization.arrayToDataTable([
                    <?= $table ?>
                ]);

                drawLineColors();
                drawBMI();
                drawFatColors();
                drawMuscleColors();
            }

            function drawLineColors() {
                var view = new google.visualization.DataView(globdata);
                view.setColumns([0, 1]);
                var options = {
                    title: 'Vægt',
                    subtitle: 'gennem tiderne',
                    legend: {
                        'position': 'in',
                    },
                    //        'legend':   'in',
                    backgroundColor: '#EEEEEE',
                    pointSize: 2,
                    //backgroundColor.stroke: '#444',
                    series: {
                        0: {
                            targetAxisIndex: 0
                        },
                        1: {
                            targetAxisIndex: 1
                        }
                    },
                    trendlines: {
                        0: {
                            type: 'polynomial',
                            color: 'green',
                            lineWidth: 3,
                            opacity: 0.3,
                            showR2: true,
                            visibleInLegend: true
                        }
                    },
                    hAxis: {
                        title: 'Time',
                        format: 'd/M/yy',
                        //ticks: datescale
                    },
                    vAxis: {
                        title: 'Kg',
                        viewWindow: {
                            min: <?= $minweight ?>,
                            max: <?= $maxweight ?>
                        },
                        ticks: [85, 86, 87, 88, 89, 90, 91, 92],
                        gridlines: {
                            count: -1
                        },
                        minorGridlines: {
                            count: 3
                        },
                        0: {
                            title: 'Kg',
                            viewWindow: {
                                min: 87
                            },
                            ticks: [85, 86, 87, 88, 89, 90, 91, 92],
                            gridlines: {
                                count: 5
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
                    title: 'Fedt %',
                    subtitle: 'gennem tiderne',
                    'legend': 'in',
                    backgroundColor: '#EEEEEE',
                    pointSize: 2,
                    //backgroundColor.stroke: '#444',
                    series: {
                        0: {
                            targetAxisIndex: 0
                        },
                        1: {
                            targetAxisIndex: 1
                        }
                    },
                    trendlines: {
                        0: {
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
                        //ticks:  datescale
                    },
                    vAxis: {
                        title: '%',
                        gridlines: {
                            count: -1
                        },
                        minorGridlines: {
                            count: 3
                        },
                        0: {
                            title: 'Kg',
                            viewWindow: {
                                min: 85
                            },
                            ticks: [85, 86, 87, 88, 89, 90, 91, 92],
                            gridlines: {
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
                    title: 'Muscle %',
                    subtitle: 'gennem tiderne',
                    'legend': 'in',
                    backgroundColor: '#EEEEEE',
                    pointSize: 2,
                    series: {
                        0: {
                            targetAxisIndex: 0
                        },
                        1: {
                            targetAxisIndex: 1
                        }
                    },
                    trendlines: {
                        0: {
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
                        //ticks: datescale
                    },
                    vAxis: {
                        title: '%',
                        gridlines: {
                            count: -1
                        },
                        minorGridlines: {
                            count: 3
                        },
                        0: {
                            title: 'Kg',
                            viewWindow: {
                                min: 86
                            },
                            ticks: [85, 86, 87, 88, 89, 90, 91, 92],
                            gridlines: {
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
                    title: 'BMI',
                    subtitle: 'gennem tiderne',
                    'legend': 'in',
                    backgroundColor: '#EEEEEE',
                    pointSize: 2,
                    xseries: {
                        0: {
                            targetAxisIndex: 0
                        },
                        1: {
                            targetAxisIndex: 1
                        }
                    },
                    trendlines: {
                        0: {
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
                        //ticks:  datescale
                    },
                    vAxis: {
                        title: 'BMI',
                        baseline: 25,
                        baselineColor: 'red',
                        viewWindow: {
                            min: 24
                        },
                        ticks: [18, {
                            v: 18.5,
                            f: 'Normalvægt'
                        }, 19, 20, 21, 22, 23, 24, {
                            v: 25,
                            f: 'Overvægt'
                        }, 26, 27, 28, 29, {
                            v: 30,
                            f: 'Svær overvægt'
                        }],
                        gridlines: {
                            count: -1
                        },
                        xminorGridlines: {
                            count: 3
                        },
                        0: {
                            title: 'Kg',
                            viewWindow: {
                                min: 85
                            },
                            ticks: [85, 86, 87, 88, 89, 90, 91, 92],
                            gridlines: {
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

        <form>
            <div class="row">
                <div class="col-sm-2">
                    <h2>Vægt</h2>
                </div>
                <label for="startdate" class="col-sm-2 col-form-label">Start dato</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control" id="startdate" name="startdate" value="<?= $start_date ?>" min="2018-01-01" max="2024-04-01">
                </div>
                <div class="col-sm-1">
                    <input type="submit" class="btn btn-primary" value="update">
                </div>
            </div>
        </form>

        <!--    <div id="columnchart_material" style="width: 800px; height: 500px;"></div>-->
        <style>
            .graph {
                height: 400px;
            }
        </style>
        <div class="row">
            <div id="weight_div" class="col-md-6 graph"></div>
            <div id="bmi_div" class="col-md-6 graph"></div>
        </div>
        <br>
        <div class="row">
            <div id="fat_div" class="col-md-6 graph"></div>
            <div id="muscle_div" class="col-md-6 graph"></div>
        </div>
    </div>
    </div>
    <script>
        google.charts.load('current', {
            'packages': ['bar', 'corechart']
        });
        google.charts.setOnLoadCallback(prepare);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>