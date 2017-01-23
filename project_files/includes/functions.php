<?php

if(isset($_POST['type'])) {
  if ($_POST['type'] == 'stayPointDetection') {//$tr, $dT, $tT
    echo stayPointDetection(getPointsFromTrajectory($_POST['trajectoryId']), $_POST['distanceThreshold'], $_POST['timeThreshold']*60);
  } else if($_POST['type'] == 'getTrajectories') {
    $t = getTrajectories();
    $result = '';
    foreach ($t as $value) {
      echo renderTrajectoryForDatalist($value);
    }
    //echo $result;
  } else if($_POST['type'] == 'douglasPeucker') {
    echo douglasPeucker(getPointsFromTrajectory($_POST['trajectoryId']), $_POST['distanceThreshold']);
  } else if($_POST['type'] == 'segmentTrajectory') {
    echo segmentTrajectory(getPointsFromTrajectory($_POST['trajectoryId']), $_POST['timeThreshold']*60);
  }
}

// // // // // SQL FUNCTIONS // // // // //

/*
   getTrajectories()
   Returns an array with all trajectories.

   @return (Array) Trajectories
*/
function getTrajectories() {

  require ('db_config.php');

  $query  = 'SELECT * FROM "Trajectory" ORDER BY id ASC;';
  $result = pg_query($db,$query);
  $rset   = pg_fetch_all($result);

  return $rset;
}



/*
   getPointsFromArc(trajectory_id)
   Returns an array with all latitude-longitude-coordinates of a certain trajectory.

   @params (Integer) A trajectory id
   @return (Array) All points of an arc
*/
function getPointsFromArc($tid) {

  require ('db_config.php');

  $query  = 'SELECT ST_X(lonlat) as lon, ST_Y(lonlat) as lat FROM "Trajectory", "Point", "hasTP" WHERE "Trajectory".id = "hasTP".tid AND "Point".id = "hasTP".pid AND "Trajectory".id = '.strval($tid).' ORDER BY pid ASC;';
  $result = pg_query($db,$query);
  $rset   = pg_fetch_all($result);

  $ml = [];
  foreach ($rset as $t) {
    $ml[] = new LatLng($t['lat'], $t['lon']);
  }

  return json_encode($ml);
}

/*
   getPointsFromTrajectory(trajectory_id)
   Returns an array with all latitude-longitude-coordinates of a certain trajectory.

   @params (Integer) A trajectory id
   @return (Array) All points of a trajectory
*/
function getPointsFromTrajectory($tid) {

  require ('db_config.php');

  $query  = 'SELECT ST_X(lonlat) as lon, ST_Y(lonlat) as lat, EXTRACT(EPOCH FROM date + time) AS unixtimestamp FROM "Trajectory", "Point", "hasTP" WHERE "Trajectory".id = "hasTP".tid AND "Point".id = "hasTP".pid AND "Trajectory".id = '.strval($tid).' ORDER BY pid ASC;';
  $result = pg_query($db,$query);
  $rset   = pg_fetch_all($result);

  $ml = [];
  foreach ($rset as $t) {
    $ml[] = new Trajectory($t['lon'], $t['lat'], $t['unixtimestamp'] );
  }

  return $ml;
}

function segmentTrajectory($tr, $t) {
  $i = 0;
  $j = 1;
  $result = [];

  while($i < count($tr) && $j < count($tr)) {
    while($j < count($tr)) {
      $deltaT = $tr[$j]->dateTime - $tr[$i]->dateTime;
      if($deltaT > $t) {
        $result[] = array_slice($tr,$i,$j);
        $i = $j;
      } else {
        $j = $j + 1;
      }
    }
  }

  return json_encode($result);
}

function getFuelstations ($lon, $lat) {

  require ('db_config.php');

  $query = "select osm.name, ST_Distance(ST_MakePoint(ST_Y(ST_Transform(osm.geometry,4326)),ST_X(ST_Transform(osm.geometry,4326))),ST_MakePoint($lon,$lat),True) from osm_amenities osm
where osm.type = 'fuel' and osm.name <> '' and ST_Distance(ST_MakePoint(ST_Y(ST_Transform(osm.geometry,4326)),ST_X(ST_Transform(osm.geometry,4326)),4326),(ST_Point($lon,$lat)),True) < 5000 order by ST_Distance(ST_MakePoint(ST_Y(ST_Transform(osm.geometry,4326)),ST_X(ST_Transform(osm.geometry,4326)),4326),ST_Point($lon,$lat),True);";

  $result = pg_query($db,$query);
  $rset   = pg_fetch_all($result);

  $ml = [];
  foreach ($rset as $t) {
    $ml[] = $t['name'];
  }

  return $ml;
}

// // // // // FORMAT FUNCTIONS // // // // //


// classes
class LatLng {

  public $lat;
  public $lng;

  function LatLng($lng,$lat){
    $this->lng = $lng;
    $this->lat = $lat;

  }
}

class Trajectory {
  public $lon;
  public $lat;
  public $dateTime;

  function Trajectory($lon, $lat, $dateTime) {
    $this->lon = $lon;
    $this->lat = $lat;
    $this->dateTime = $dateTime;
  }
}

// functions

/*
   renderTrajectory(trajectory)
   -- Returns a HTML representation of a certain trajectory

   @params (Array) Trajectory
   @return (String) HTML representation of a trajectory
*/
function renderTrajectory($t) {

  $id = $t['id'];
  $latlngs = getPointsFromArc($id);

  return "<li class='trajectory-item' data-id='$id' data-latlngs='$latlngs'>Trajectory (ID $id)</li>";
}
/*
   renderTrajectory(trajectory)
   -- Returns a HTML representation of a certain trajectory

   @params (Array) Trajectory
   @return (String) HTML representation of a trajectory
*/
function renderTrajectoryForDatalist($t) {

  $id = $t['id'];
  $latlngs = getPointsFromArc($id);

  return "<option class='trajectory-item' data-id='$id' data-latlngs='$latlngs' value='$id'>Trajectory (ID $id)</option>";
}


// // // // // ALGORITHMS // // // // //

/*
   douglasPeucker(pointList,epsilon)
   Returns an array with all latitude-longitude-coordinates of a certain trajectory.

   @params (Integer) A trajectory id, (Integer) A distance threshhold
   @return (Array) A compressed pointList (list of points of a trajectory)
*/
function douglasPeucker($pointList, $epsilon)
{
    // Find the point with the maximum distance
    $dmax = 0;
    $index = 0;
    $totalPoints = count($pointList);
    for ($i = 1; $i < ($totalPoints - 1); $i++)
    {
        $d = perpendicularDistance($pointList[$i]->lon, $pointList[$i]->lat, $pointList[0]->lon, $pointList[0]->lat, $pointList[$totalPoints-1]->lon, $pointList[$totalPoints-1]->lat);

        if ($d > $dmax)
        {
            $index = $i;
            $dmax = $d;
        }
    }

    $resultList = array();

    // If max distance is greater than epsilon, recursively simplify
    $rad = 0.000008998719243599958;
    $dmax = $dmax / $rad;
    if ($dmax >= $epsilon)
    {
        // Recursive call
        $recResults1 = douglasPeucker(array_slice($pointList, 0, $index + 1), $epsilon);
        $recResults2 = douglasPeucker(array_slice($pointList, $index, $totalPoints - $index), $epsilon);

        // Build the result list
        //TODO iterate and add one at a time
        //$resultList = array_merge($recResults1, $recResults);
        for($i = 0; $i < count($recResults1); $i++) {
          $resultList[] = $recResults1[$i];
        }
        for($i = 0; $i < count($recResults2); $i++) {
          $resultList[] = $recResults2[$i];
        }
    }
    else
    {
        $resultList = array($pointList[0], $pointList[$totalPoints-1]);
    }
    // Return the result
    return json_encode($resultList);
}

// sub-function of douglasPeucker function
function perpendicularDistance($ptX, $ptY, $l1x, $l1y, $l2x, $l2y)
{
    $result = 0;
    if ($l2x == $l1x)
    {
        //vertical lines - treat this case specially to avoid divide by zero
        $result = abs($ptX - $l2x);
    }
    else
    {
        $slope = (($l2y-$l1y) / ($l2x-$l1x));
        $passThroughY = (0-$l1x)*$slope + $l1y;
        $result = (abs(($slope * $ptX) - $ptY + $passThroughY)) / (sqrt($slope*$slope + 1));
    }
    return $result;
}

/*
   stayPointDetection(trajectory, timeTreshold,distThreshold)
*/

/*
   stayPointDetection(trajectory, timeTreshold,distThreshold)
   Returns an array with all points of a trajectory.

   @params (Integer) A trajectory id, (Integer) A time threshhold, (Integer) A distance threshhold
   @return (Array) A (compressed) semantic trajectory
*/
function stayPointDetection($tr, $dT, $tT) {

  $i = 0;
  $j = 0;
  $pointNum = count($tr);
  $SP = [];

  while ($i < $pointNum && $j < $pointNum) {

    $j = $j + 1;

    while ($j < $pointNum) {

      $dist = getDistanceBetweenPoints($tr[$i], $tr[$j]);

      if ($dist > $dT)  {

        $deltaT = $tr[$j]->dateTime - $tr[$i]->dateTime;

        if ($deltaT > $tT) {

          $S = [];
          $S['coord'] = computeMean($tr, $i, $j);
          $S['stayTime'] = $deltaT;
          $S['fuelStations'] = getFuelstations($tr[$j]->lon, $tr[$j]->lat);
          $SP[] = $S;

        }

        $i = $j;
        break;

      }

      $j = $j + 1;

    }
  }

  return json_encode($SP);
}

// sub-function of stayPointDetection
function getDistanceBetweenPoints($first, $second) {
  $rad = 0.000008998719243599958;
  return sqrt(pow($second->lon - $first->lon, 2) + pow($second->lat - $first->lat, 2))/$rad;
}

// sub-function of stayPointDetection
function computeMean($trajectory, $start, $end) {
  $resultLon = 0;
  $resultLat = 0;
  $count = ($end + 1) - $start;
  for ($i = $start; $i <= $end; $i++) {
    $resultLon = $resultLon + $trajectory[$i]->lon;
    $resultLat = $resultLat + $trajectory[$i]->lat;
  }
  return new LatLng($resultLon/$count, $resultLat/$count);
}

?>
