<!DOCTYPE html>
<html lang="da">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="website.css" type="text/css">
  <link rel="icon" href="pic/weight120.png" type="image/png" sizes="120x120">
  <link rel="icon" href="favicon.ico" type="image/x-ico">
  <link rel="icon" href="pic/weight192.png" type="image/png" sizes="192x192">
  <link rel="icon" href="pic/weight512.png" type="image/png" sizes="512x512">
  <link rel="manifest" href="manifest.json">
  <link rel="apple-touch-icon" href="pic/weight192.png" type="image/png" sizes="192x192">
  <title>Motion</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
</head>

<body>
  <div class="container-lg">
    <?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    ini_set('error_reporting', E_ALL);

    $debug = 0;
    include 'db.php';


    $minweight = 86;
    $maxweight = 91;

    $start_date = "2025-06-01";
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
        $vagtdiff = round(($vagt2 - $vagt1)*1000);
    }
    //echo "Vægtændring / uge: $vagtdiff g<br>";

    $weight_data = get_js_weight($start_date);
    $bmi_data = get_js_field($start_date, 'BMI');
    $fat_data = get_js_field($start_date, 'Fat');
    $muscle_data = get_js_field($start_date, 'Muscle');

    ?>

    <form>
      <div class="row">
        <div class="col-2  d-none d-sm-block">
          <h2>Vægt</h2>
        </div>
        <label for="startdate" class="col-sm-2 col-form-label d-none d-sm-block">Start dato</label>
        <div class="col">
          <input type="date" class="form-control" id="startdate" name="startdate" value="<?= $start_date ?>" min="2018-01-01" max="2025-04-15">
        </div>
        <div class="col">
          <input type="submit" class="btn btn-primary" value="update">
        </div>
      </div>
    </form>

    <style>
      .graph {
        height: 400px;
      }
    </style>

    <div class="row">
      <div class="col-md-6 graph">
        <canvas id="weight_div" class="graph"></canvas>
      </div>
      <div class="col-md-6 graph">
        <canvas id="bmi_div" class="graph"></canvas>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-md-6 graph" style="background-color: beige;">
        <canvas id="fat_div"></canvas>
      </div>
      <div class="col-md-6 graph" style="background-color:bisque">
        <canvas id="muscle_div"></canvas>
      </div>
    </div>

    <script>
      const d_weight = document.getElementById('weight_div');
      const d_bmi = document.getElementById('bmi_div');
      const d_fat = document.getElementById('fat_div');
      const d_muscle = document.getElementById('muscle_div');

      const start_date = "<?= $start_date ?>";
      <?= $weight_data ?>
      <?= $bmi_data ?>
      <?= $fat_data ?>
      <?= $muscle_data ?>

      const vagtdiff = <?=$vagtdiff?>;

      //now = Date.now();

      // diff vagt
      const textLegend = {
        id: 'textLegend',
        beforeDatasetsDraw(chart, args, plugins) {
          const{ ctx, chartArea: {top, bottom, left, right, width, height}} = chart;
          ctx.save();
          ctx.font = '18px sans-serif';
          ctxfillStyle = "grey";
          ctx.textAlign = "center";
          ctx.fillText("Ændring: "+ vagtdiff + " g", width*0.5 + left, height * 0.95 + top);
        }
      }
      const weight_config = {
        type: 'line',
        data: {
          datasets: [{
              label: 'min vægt',
              data: weight_data,
              fill: true
            },
            {
              //label: 'Cubic interpolation',
              data: weight_data,
              borderColor: "rgb(0,255,0)",
              fill: false,
              tension: 0.4,
            }
          ]
        },
        plugins: [textLegend],
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: {
              display: true,
              text: 'Vægt'
            },
            legend: {
              display: false,
              position: 'top',
            },

          },
          scales: {
            x: {
              type: 'time',
              time: {
                unit: 'day'
              },
              min: start_date,
              //max: '2024-05-01'
            },
            y: {
              suggestedMax: 90,
              suggestedMin: 86,
              //   min: 86,
              //   max: 90
              title: {
                display: false,
                text: "Kg"
              }
            }
          }
        }
      };

      new Chart(d_weight, weight_config)

      ///////////////////////////////// BMI /////////////////////////////////

      const bmi_config = {
        type: 'line',
        data: {
          datasets: [{
              label: 'BMI',
              data: BMI_data,
            },
            {
              label: 'Cubic interpolation',
              data: BMI_data,
              borderColor: "rgb(0,255,0)",
              fill: false,
              tension: 0.4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: {
              display: true,
              text: 'BMI'
            },
            legend: {
              display: false,
              position: 'top',
            },
          },
          scales: {
            x: {
              type: 'time',
              time: {
                unit: 'day'
              },
              min: start_date,
              //max: '2024-05-01'
            },
            y: {
              min: 25,
              max: 30
            }
          }


        }
      };

      new Chart(d_bmi, bmi_config)

            ///////////////////////////////// FAT /////////////////////////////////

      const fat_config = {
        type: 'line',
        data: {
          datasets: [{
              label: 'Fat',
              data: Fat_data,
            },
            {
              label: 'Cubic interpolation',
              data: Fat_data,
              borderColor: "rgb(0,255,0)",
              fill: false,
              tension: 0.4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: {
              display: true,
              text: 'Fat %'
            },
            legend: {
              display: false,
              position: 'top',
            },
          },
          scales: {
            x: {
              type: 'time',
              time: {
                unit: 'day'
              },
              min: start_date,
              //max: '2024-05-01'
            },
            y: {
              min: 10,
              max: 20
            }
          }


        }
      };
      new Chart(d_fat, fat_config)
      ///////////////////////////////// MUSCLE /////////////////////////////////

      const muscle_config = {
        type: 'line',
        data: {
          datasets: [{
              label: 'Muscle',
              data: Muscle_data,
            },
            {
              label: 'Cubic interpolation',
              data: Muscle_data,
              borderColor: "rgb(0,255,0)",
              fill: false,
              tension: 0.4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: {
              display: true,
              text: 'Muscle'
            },
            legend: {
              display: false,
              position: 'top',
            },
          },
          scales: {
            x: {
              type: 'time',
              time: {
                unit: 'day'
              },
              min: start_date,
              //max: '2024-05-01'
            },
            y: {
              min: 50,
              max: 70
            }
          }


        }
      };

      new Chart(d_muscle, muscle_config)
    </script>


    <!--    <div id="columnchart_material" style="width: 800px; height: 500px;"></div>-->
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
  </script>
</body>

</html>
