<?php
  
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
  <!-- Bootstrap -->		
  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="js/bootstrap.min.js"></script>

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
          <li class="active"><a href="index.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Home </a></li>
          <li><a href="trajectory.php"><span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> Trajectory View </a></li>
	  <li><a href="segmentation.php"><span class="glyphicon glyphicon-resize-full" aria-hidden="true"></span> Segmentation </a></li>
	  <li><a href="compression.php"><span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Compression </a></li>
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

  </div>

  <!-- Main window -->
  <div class="col-sm-10" id="main-window">
    <h2>Welcome to "Truckjectory Tool"!</h2>
    <p>This tool was designed to visualize and analyze trajectories based on the famous record "Trucks" published by <a href="http://chorochronos.datastories.org/?q=node/5" target="_blank">ChoroChronos.org</a>.</p>
    <br />
    <h4>Trajectory View</h4>
    <p>The tab <b>"Trajectory View"</b> enables the user to visualize a single trajectory or a set of trajectories.<br />
       The sidebar contains selectable trajectories.<br />
       <ul>
         <li>Selecting a trajectory will visualized it on the map in the main window.</li>
         <li>Deselecting the trajectory will drop the trajectory from the map.</li>
       </ul>
    </p>
    <br />
    <h4>Segmentation</h4>
    <p>The tab <b>"Segmentation"</b> enables the user to split a single trajectory into smaller parts for further analysis.<br />
       The sidebar contains a trajectory selector, a selector of the kind of segmentation and the button "Compute".<br />
       <ul>
         <li>Use the dropdown menu to select a trajetory. The choosen trajectory will be visualized in the top window.</li>
         <li>Select a segmentation method.</li>
         <li>Press the "Compute" button. The computed segmented trajectory will be visualized in the bottom window.</li>
       </ul>
    </p>
    <br />
    <h4>Compression</h4>
    <p>The tab <b>"Compression"</b> enables the user to compress a single trajectory.<br />
       The sidebar contains a trajectory selector and the button "Compress".<br />
       <ul>
         <li>Use the dropdown menu to select a trajetory. The choosen trajectory will be visualized in the top window.</li>
         <li>Press the "Compress" button. The calculated compressed trajectory will be visualized in the botom window.</li>
       </ul>
    </p>
    <br />
    <h4>Stay Point Detection</h4>
    <p>The tab <b>"Stay Point Detection"</b> enables the user to analyze a single trajectory.<br />
       The sidebar contains a trajectory selector and the button "Stay Points".<br />
       <ul>
         <li>Use the dropdown menu to select a trajetory.</li>
         <li>Press the "Stay Points" button.</li>
         <li>The calculated semantic trajectory will be visualized in the main window.</li>
       </ul>
    </p>
  </div>

  </div>

  <!-- Footer -->
  <footer class="navbar-fixed-bottom">
    <span class="text-left navbar-left">&copy; 2016 by Nicolas Lehmann</span>
  </footer>
  
</body>

</html>
