<?php
  require_once 'includes/db_config.php';
  require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Big Fat Greek Truckjectories</title>
  <!-- Bootstrap -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/custom.css" rel="stylesheet"/>
  <!-- Leaflet -->
  <link href="css/leaflet.css" rel="stylesheet"/>
  <!-- Bootstrap -->
  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <!-- Leaflet -->
  <script src="js/leaflet.js"></script>
  <script src="js/custom.js"></script>
</head>

<body>

  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand"><b>Truckjectory Tool</b></a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <!-- navbar left-->
        <ul class="nav navbar-nav">
          <li><a href="index.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Home </a></li>
          <li><a href="trajectory.php"><span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> Trajectory View </a></li>
          <li><a href="segmentation.php"><span class="glyphicon glyphicon-resize-full" aria-hidden="true"></span> Segmentation </a></li>
          <li class="active"><a href="compression.php"><span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Compression </a></li>
          <li><a href="staypoints.php"><span class="glyphicon glyphicon-record" aria-hidden="true"></span> Stay Point Detection </a></li>
        </ul>
        <!-- navbar right-->
        <ul class="nav navbar-nav navbar-right">
        </ul>
      </div><!-- /.navbar-collapse -->
    </div>
  </nav>

  <!-- Content -->
  <div class="col-sm-12">

  <!-- Sidebar -->
  <div class="col-sm-2" id="sidebar-left">
    <label>Select Trajectory</label>
    <select id="trajectories" class="list-group">
    </select>
    <label>Select Thresholds</label>
    <select id="distThreshold">
      <option value="10">10 m</option>
      <option value="20">20 m</option>
      <option value="30">30 m</option>
      <option value="40">40 m</option>
      <option value="50">50 m</option>
      <option value="60">60 m</option>
      <option value="70">70 m</option>
      <option value="80">80 m</option>
      <option value="90">90 m</option>
      <option value="100">100 m</option>
    </select>
    
    <div class="btn btn-primary" id="compress-btn">Compress</div>
  </div>

  <!-- Main window -->
  <div class="col-sm-10" id="main-window">
    <div id="map-container-top"></div>
    <div id="map-container-bottom"></div>
  </div>

  </div>

  <!-- Footer -->
  <footer class="navbar-fixed-bottom">
    <span class="text-left navbar-left">&copy; 2016 by Nicolas Lehmann</span>
  </footer>

  <?php // close database connection
    pg_close($db);
  ?>

</body>

</html>
