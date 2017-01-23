$( document ).ready( function () {

  var polylines = [],
    polylineBounds;

  if ( $( '#map-container' ).length > 0 ) {
    var map = L.map( 'map-container' ).setView( [ 39.16, 22.44 ], 7 );
    L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    } ).addTo( map );
  }

  if ( $( '#map-container-top' ).length > 0 ) {
    var polylinesBottom = [],
      polylineBoundsBottom;
    $.ajax( {
      method: 'POST',
      url: '/includes/functions.php',
      data: {
        type: 'getTrajectories'
      }
    } ).done( function ( data, status, jqXHR ) {;
      var current = $( '#trajectories' );
      current.append( data )
      addTrajectoryToMap( current.find( 'option:first-child' ).data( 'latlngs' ), current.find( 'option:first-child' ).data( 'id' ), map, polylines, polylineBounds );
    } );
    var map = L.map( 'map-container-top' ).setView( [ 39.16, 22.44 ], 7 );
    L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    } ).addTo( map );
    var mapBottom = L.map( 'map-container-bottom' ).setView( [ 39.16, 22.44 ], 7 );
    L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    } ).addTo( mapBottom );
  }

  var calculateBoundingBox = function () {
    polylineBounds = false;
    for ( var polyline in polylines ) {
      if ( polylineBounds ) {
        polylineBounds.extend( polylines[ polyline ].getBounds() );
      } else {
        polylineBounds = polylines[ polyline ].getBounds();
      }
    }
  }

  var addTrajectoryToMap = function ( latlngs, latlonId ) {
    var polyline = L.polyline( latlngs, {
      color: 'red'
    } ).addTo( map );
    polylines[ latlonId ] = polyline;
    calculateBoundingBox();
    map.fitBounds( polylineBounds );
  }

  var removeTrajectoryFromMap = function ( layerId ) {
    map.removeLayer( polylines[ layerId ] );
    delete polylines[ layerId ]
    if ( Object.keys( polylines ).length > 0 ) {
      calculateBoundingBox();
      map.fitBounds( polylineBounds );
    } else {
      polylines = [];
    }
  }
  var calculateBottomBoundingBox = function () {
    polylineBoundsBottom = false;
    for ( var polyline in polylinesBottom ) {
      if ( polylineBoundsBottom ) {
        polylineBoundsBottom.extend( polylinesBottom[ polyline ].getBounds() );
      } else {
        polylineBoundsBottom = polylinesBottom[ polyline ].getBounds();
      }
    }
  }

  var addTrajectoryToBottomMap = function ( latlngs, latlonId, color ) {
    var polyline = L.polyline( latlngs, {
      color: color
    } ).addTo( mapBottom );
    polylinesBottom[ latlonId ] = polyline;
    calculateBottomBoundingBox();
    mapBottom.fitBounds( polylineBoundsBottom );
  }

  var removeTrajectoryFromBottomMap = function ( layerId ) {
    mapBottom.removeLayer( polylinesBottom );
    delete polylinesBottom[ layerId ]
    if ( Object.keys( polylinesBottom ).length > 0 ) {
      calculateBottomBoundingBox();
      mapBottom.fitBounds( polylineBoundsBottom );
    } else {
      polylinesBottom = [];
    }
  }

  var buildTrajectoriesFromPairs = function ( listOfPairs ) {
    var result = [];
    for ( var i = 0; i < Object.keys( listOfPairs ).length; i++ ) {
      if ( listOfPairs[ i ][ 'coord' ] ) {
        result.push( {
          lng: listOfPairs[ i ][ 'coord' ][ 'lat' ],
          lat: listOfPairs[ i ][ 'coord' ][ 'lng' ]
        } );
      } else {
        result.push( {
          lng: listOfPairs[ i ][ 'lat' ],
          lat: listOfPairs[ i ][ 'lon' ]
        } );
      }
    }
    return result;
  }

  var addCircleToMap = function ( lonlat, radius, fuelStations ) {
    var circle = L.circle( lonlat, radius, {
      color: 'blue',
      fillColor: 'lightblue',
      fillOpacity: 0.5
    } ).addTo( mapBottom );
    circle.bindPopup( fuelStations.length > 0 ? fuelStations.join( ', ' ) : 'No fuel stations in the near vicinity found.' );
  }

  var addStayPoints = function ( listOfStayPoints ) {
    for ( var i = 0; i < Object.keys( listOfStayPoints ).length; i++ ) {
      addCircleToMap( {
        lat: listOfStayPoints[ i ][ 'coord' ][ 'lng' ],
        lon: listOfStayPoints[ i ][ 'coord' ][ 'lat' ]
      }, 100, listOfStayPoints[ i ][ 'fuelStations' ] );
    }
  }

  // add function for adding partial polyline or just line with colour as parameter and starting point and end point

  $( '#sidebar-left' ).on( 'click', '#selectAll, #deselectAll', function ( event ) {
    var cur = $( this ),
      numberOfTrajectories;
    if ( cur.hasClass( 'select' ) ) {
      var trajectories = $( '#sidebar-left ul li:not(.active)' );
      numberOfTrajectories = trajectories.length;
      trajectories.trigger( 'click' );
      var timerId = setInterval( function () {
        calculateBoundingBox();
        clearInterval( timerId );
      }, 100 * numberOfTrajectories );
    } else {
      $( '#sidebar-left ul li.active' ).trigger( 'click' );
      var timerId = setInterval( function () {
        calculateBoundingBox();
        clearInterval( timerId );
      }, 1500 );
    }
  } );

  $( '#sidebar-left' ).on( 'click', '.trajectory-item', function ( event ) {
    var current = $( this );
    current.toggleClass( 'active' );
    var latlngs = current.data( 'latlngs' );
    if ( polylines[ current.data( 'id' ) ] ) {
      removeTrajectoryFromMap( current.data( 'id' ) );
    } else {
      addTrajectoryToMap( latlngs, current.data( 'id' ) );
    }
  } );

  $( '#sidebar-left' ).on( 'change', '#trajectories', function ( event ) {
    var current = $( this ).find( 'option[data-id="' + $( this ).val() + '"]' );
    var latlngs = current.data( 'latlngs' );
    if ( Object.keys( polylines ).length > 0 ) {
      removeTrajectoryFromMap( polylines.length - 1 );
    }
    addTrajectoryToMap( latlngs, current.data( 'id' ) );
  } );

  $( '#sidebar-left' ).on( 'click', '#compute', function ( event ) {
    var distanceThreshold = $( '#distThreshold' ).val();
    var timeThreshold = $( '#timeThreshold' ).val();
    var trajectoryId = $( '#trajectories' ).val();
    $.ajax( {
      method: 'POST',
      url: '/includes/functions.php',
      data: {
        type: 'stayPointDetection',
        trajectoryId: trajectoryId,
        timeThreshold: timeThreshold,
        distanceThreshold: distanceThreshold
      }
    } ).done( function ( data, status, jqXHR ) {;
      var result = JSON.parse( data );
      var trajectory = buildTrajectoriesFromPairs( result );
      if ( Object.keys( polylinesBottom ).length > 0 ) {
        mapBottom.remove();
        mapBottom = L.map( 'map-container-bottom' ).setView( [ 39.16, 22.44 ], 7 );
        L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        } ).addTo( mapBottom );
        polylinesBottom = [];
      }
      addTrajectoryToBottomMap( trajectory, 1, 'red' );
      addStayPoints( result );
    } );
  } );

  $( '#sidebar-left' ).on( 'click', '#compress-btn', function ( event ) {
    var trajectoryId = $( '#trajectories' ).val();
    var distanceThreshold = $( '#distThreshold' ).val();
    $.ajax( {
      method: 'POST',
      url: '/includes/functions.php',
      data: {
        type: 'douglasPeucker',
        trajectoryId: trajectoryId,
        distanceThreshold: distanceThreshold
      }
    } ).done( function ( data, status, jqXHR ) {;
      var result = JSON.parse( data );
      var trajectory = buildTrajectoriesFromPairs( result );
      if ( Object.keys( polylinesBottom ).length > 0 ) {
        removeTrajectoryFromBottomMap( 1 );
      }
      addTrajectoryToBottomMap( trajectory, 1, 'red' );
    } );
  } );

  $( '#sidebar-left' ).on( 'click', '#segment-btn', function ( event ) {
    var trajectoryId = $( '#trajectories' ).val();
    var timeThreshold = $( '#timeThreshold' ).val();
    $.ajax( {
      method: 'POST',
      url: '/includes/functions.php',
      data: {
        type: 'segmentTrajectory',
        trajectoryId: trajectoryId,
        timeThreshold: timeThreshold
      }
    } ).done( function ( data, status, jqXHR ) {;
      var result = JSON.parse( data );
      if ( Object.keys( polylinesBottom ).length > 0 ) {
        mapBottom.remove();
        mapBottom = L.map( 'map-container-bottom' ).setView( [ 39.16, 22.44 ], 7 );
        L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        } ).addTo( mapBottom );
        polylinesBottom = [];
      }
      for ( var i = 0; i < Object.keys( result ).length; i++ ) {
        var color = i % 3 === 0 ? 'yellow' : i % 3 === 1 ? 'blue' : 'red';
        var trajectory = buildTrajectoriesFromPairs( result[ i ] );
        addTrajectoryToBottomMap( trajectory, 1, color );
      }
      timerId = setInterval( function () {
        mapBottom.fitBounds( map.getBounds() );
        mapBottom.zoomIn();
        clearInterval( timerId );
      }, 500 );
    } );
  } );

} );