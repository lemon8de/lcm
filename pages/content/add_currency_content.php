<div class="row d-flex" style="min-height:400px;">
    <div class="col-6 d-flex align-items-stretch">
        <div class="card p-3 flex-fill">
            <form action="../php_api/billing_add_exchange.php" method="POST">
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Month</label>
                        <select class="form-control" name="month" id="active_month" onchange="find_exchange()">
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        <script>
                            // Get the current month (0-11)
                            const currentMonth = new Date().getMonth();
                            // Select the month in the dropdown
                            const monthSelect = document.getElementById('active_month');
                            monthSelect.selectedIndex = currentMonth;
                        </script>
                    </div>
                    <div class="col-6">
                        <label>Year</label>
                        <select class="form-control" id="active_year" name="year" onchange="find_exchange()">
                        <?php
                            $current_year = date("Y");
                            $current_month = date("m");
                            $end_year = $current_year - 10;
                            for ($year = $current_year; $year >= $end_year; $year--) {
                                echo <<<HTML
                                    <option value="{$year}">{$year}</option>
                                HTML;
                            }
                        ?>
                        </select>
                    </div>
                </div>
                <?php
                    $for_date = new DateTime();
                    $for_date -> setDate($current_year, $current_month, 1);
                    $for_date = $for_date -> format('Y-m-d');
                    $sql = "SELECT cast(round(jpy_php, 7) as nvarchar(50)) as jpy_php, cast(round(usd_php, 7) as nvarchar(50)) as usd_php, cast(round(jpy_usd, 7) as nvarchar(50)) as jpy_usd from t_billing_exchange where for_date = :for_date";
                    $stmt = $conn -> prepare($sql);
                    $stmt -> bindParam(":for_date", $for_date);
                    $stmt -> execute();
                    if ($exchange = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                        $val_jpy_php = $exchange['jpy_php'];
                        $val_usd_php = $exchange['usd_php'];
                        $val_jpy_usd = $exchange['jpy_usd'];
                    } else {
                        $val_jpy_php = 0;
                        $val_usd_php = 0;
                        $val_jpy_usd = 0;
                    }
                ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">JPY - PHP</label>
                    <div class="col-sm-9">
                        <input type="number" step="0.0000001" class="form-control" id="input_jpy_php" name="jpy_php" value="<?php echo $val_jpy_php; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">USD - PHP</label>
                    <div class="col-sm-9">
                        <input type="number" step="0.0000001" class="form-control" id="input_usd_php" name="usd_php" value="<?php echo $val_usd_php; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">JPY - USD</label>
                    <div class="col-sm-9">
                        <input type="number" step="0.0000001" class="form-control" id="input_jpy_usd" name="jpy_usd" value="<?php echo $val_jpy_usd; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-6 d-flex align-items-stretch justify-content-center align-items-center">
            <div id="carouselExampleSlidesOnly" class="d-flex align-items-stretch card p-3 carousel slide" data-ride="carousel" data-pause="false" style="width:100%;">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <canvas id="myChartPHP"></canvas>
                    </div>
                    <div class="carousel-item">
                        <canvas id="myChartUSD"></canvas>
                    </div>
                    <div class="carousel-item">
                        <canvas id="myChartJPY"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $sql = "SELECT TOP 12 jpy_php, usd_php, jpy_usd, FORMAT(for_date, 'yyyy-MM-dd') AS for_date FROM t_billing_exchange ORDER BY for_date";
    $stmt = $conn->query($sql);

    // Initialize the arrays
    $jpy_php_array = [];
    $usd_php_array = [];
    $jpy_usd_array = []; 
    $for_date_array = [];

    // Fetch the results
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Append the values to the respective arrays
        $jpy_php_array[] = $row['jpy_php'];
        $usd_php_array[] = $row['usd_php'];
        $jpy_usd_array[] = $row['jpy_usd'];
        $for_date_array[] = $row['for_date'];
    }

    // Calculate the maximum values
    $max_jpy_php = max($jpy_php_array);
    $max_usd_php = max($usd_php_array);
    $max_jpy_usd = max($jpy_usd_array);

    // Calculate 50% more than the maximum values
    $max_jpy_php_50 = $max_jpy_php * 1.5;
    $max_usd_php_50 = $max_usd_php * 1.5;
    $max_jpy_usd_50 = $max_jpy_usd * 1.5;

    //percentage calculation
    if (count($jpy_php_array) >= 2 ) {
        // Get the last two values for each array
        $jpy_php_old = $jpy_php_array[count($jpy_php_array) - 2];
        $jpy_php_new = $jpy_php_array[count($jpy_php_array) - 1];
        $usd_php_old = $usd_php_array[count($usd_php_array) - 2];
        $usd_php_new = $usd_php_array[count($usd_php_array) - 1];
        $jpy_usd_old = $jpy_usd_array[count($jpy_usd_array) - 2];
        $jpy_usd_new = $jpy_usd_array[count($jpy_usd_array) - 1];
        // Calculate percentage changes
        $jpy_php_percentage_change = (($jpy_php_new - $jpy_php_old) / $jpy_php_old) * 100;
        $usd_php_percentage_change = (($usd_php_new - $usd_php_old) / $usd_php_old) * 100;
        $jpy_usd_percentage_change = (($jpy_usd_new - $jpy_usd_old) / $jpy_usd_old) * 100;
    }
?>

<div class="row d-flex justify-content-center">
    <div class="col-3">
        <div class="card d-flex justify-content-center align-items-center">
            <div style="font-weight:700;font-size:150%;" class="<?php echo $jpy_php_percentage_change > 0 ? 'text-success' : 'text-danger'; ?>">
                <i class="fas fa-arrow-<?php echo $jpy_php_percentage_change > 0 ? 'up' : 'down'; ?>"></i>
                <?php echo abs(round($jpy_php_percentage_change, 2)); ?>%
            </div>
            <div style="font-weight:700;">JPY - PHP</div>
        </div>
    </div>
    <div class="col-3">
    <div class="card d-flex justify-content-center align-items-center">
            <div style="font-weight:700;font-size:150%;" class="<?php echo $usd_php_percentage_change > 0 ? 'text-success' : 'text-danger'; ?>">
            <i class="fas fa-arrow-<?php echo $usd_php_percentage_change > 0 ? 'up' : 'down'; ?>"></i>
            <?php echo abs(round($usd_php_percentage_change, 2)); ?>%
            </div>
            <div style="font-weight:700;">USD - PHP</div>
        </div>
    </div>
    <div class="col-3">
    <div class="card d-flex justify-content-center align-items-center">
            <div style="font-weight:700;font-size:150%;" class="<?php echo $jpy_usd_percentage_change > 0 ? 'text-success' : 'text-danger'; ?>">
            <i class="fas fa-arrow-<?php echo $jpy_usd_percentage_change > 0 ? 'up' : 'down'; ?>"></i>
            <?php echo abs(round($jpy_usd_percentage_change, 2)); ?>%
            </div>
            <div style="font-weight:700;">JPY - USD</div>
        </div>
    </div>
</div>

<script>
  const php = document.getElementById('myChartPHP');
  const chartphp = new Chart(php, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($for_date_array); ?>,
      datasets: [{
        data: <?php echo json_encode($jpy_php_array); ?>,
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        legend: {
            display: false
        },
        title: {
            display: true,
            position: 'bottom',
            text: 'USD - PHP',
            font: {
                size: 24
            }
        }
      },
      scales: {
        y: {
          type: 'linear',
          beginAtZero: true,
          max: <?php echo json_encode($max_jpy_php_50); ?>
        },
      }
    }
  });
  const usd = document.getElementById('myChartUSD');
  const chartusd = new Chart(usd, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($for_date_array); ?>,
      datasets: [{
        data: <?php echo json_encode($usd_php_array); ?>,
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        legend: {
            display: false
        },
        title: {
            display: true,
            position: 'bottom',
            text: 'USD - PHP',
            font: {
                size: 24
            }
        }
      },
      scales: {
        y: {
          type: 'linear',
          beginAtZero: true,
          max: <?php echo json_encode($max_usd_php_50); ?>
        },
      }
    }
  });
  const jpy = document.getElementById('myChartJPY');
  const chartjpy = new Chart(jpy, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($for_date_array); ?>,
      datasets: [{
        data: <?php echo json_encode($jpy_usd_array); ?>,
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        legend: {
            display: false
        },
        title: {
            display: true,
            position: 'bottom',
            text: 'JPY - USD',
            font: {
                size: 24
            }
        }
      },
      scales: {
        y: {
          type: 'linear',
          beginAtZero: true,
          max: <?php echo json_encode($max_jpy_usd_50); ?>
        },
      }
    }
  });

function find_exchange() {
    $.ajax({
        type: 'GET', // or 'GET' depending on your needs
        url: '../php_api/search_billing_exchange.php',
        data: {
            'month' : document.getElementById('active_month').value,
            'year' : document.getElementById('active_year').value
        },
        dataType: 'json',
        success: function(response) {
            document.getElementById('input_jpy_php').value = response.jpy_php;
            document.getElementById('input_usd_php').value = response.usd_php;
            document.getElementById('input_jpy_usd').value = response.jpy_usd;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("AJAX request failed: " + textStatus + ", " + errorThrown);
            alert("An error occurred while processing your request. Please try again later.");
        }
    });
}
</script>