<?php

/**
 * @file
 * Index file that Initializes the foursquare map.
 */

include 'includes/functions.php';

// EDIT THIS LINK TO USE YOUR API KEY!!!
$url = "https://api.foursquare.com/v2/users/self/checkins?oauth_token=FOURSQUARE_ACCESS_TOKEN&v=20131227&limit=10";

// Get and encode json object from foursquare.
$result = foursquare_map_fetch_data($url);
$result = json_decode($result);

// Get necessary information for rendering markers.
$items = $result->response->checkins->items;
$markers = foursquare_map_get_markers($items);

// Get location information for most recent checkin.
$location = $result->response->checkins->items[0]->venue->location;

?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map-canvas { height: 100% }
    </style>
    <script type="text/javascript"
      <!-- EDIT THIS LINK TO USE YOUR API KEY!!! -->
      src="https://maps.googleapis.com/maps/api/js?key=GOOGLE_MAPS_API_KEY&sensor=false">
    </script>
    <script type="text/javascript">
      function initialize() {
        // Position for the most recent checkin.
        var myLatlng = new google.maps.LatLng(<?php print $location->lat;?>, <?php print $location->lng; ?>);

        // Map position and zoom settings.
        var mapOptions = {
          center: myLatlng,
          zoom: 6
        };

        // Initial Map object.
        var map = new google.maps.Map(document.getElementById("map-canvas"),
            mapOptions);

        // Loop over all markers.
        <?php foreach ($markers as $marker): ?>

        var date = new Date(<?php print ($marker['date']); ?>);

        var day = date.getDate();
        var month = date.getMonth();
        var year = date.getFullYear();

        var position = new google.maps.LatLng(<?php print $marker['lat'];?>, <?php print $marker['lng']; ?>);

        // Create the marker object.
        var marker<?php print $marker['id'];?> = new google.maps.Marker({
            position: position,
            //draggable:true,
            animation: google.maps.Animation.DROP,
            map: map,
            title: String(day) + "/" + String(month) + "/" + String(year) + " - <?php print $marker['name']; ?>"
        });

        // Add popup for marker if there is a photo on the checkin.
        <?php if ($marker['photo']): ?>
          var contentString = '<div id="content">'+
                '<h1 id="firstHeading" class="firstHeading"><?php print $marker['name']; ?></h1>'+
                '<div id="bodyContent">'+
                '<a href="<?php print $marker['link']; ?>" />'+
                '<img src="<?php print $marker['photo']; ?>" />'+
                '</a>'+
                '<p><?php print $marker['post']; ?></p>'+
                '</div>'+
                '</div>';

          var infowindow<?php print $marker['id'];?> = new google.maps.InfoWindow({
                content: contentString
          });

          // Open popup when marker is clicked.
          google.maps.event.addListener(marker<?php print $marker['id'];?>, 'click', function() {
                infowindow<?php print $marker['id'];?>.open(map,marker<?php print $marker['id'];?>);
          });

          // Close popup when map is clicked.
          google.maps.event.addListener(map, 'click', function() {
                infowindow<?php print $marker['id'];?>.close();
          });

          // Mark the picture popups as blue.
          marker<?php print $marker['id'];?>.setIcon('http://maps.google.com/mapfiles/ms/icons/blue-dot.png');

        <?php endif; ?>
        <?php endforeach; ?>
      }

      // Initialize the map.
      google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
    <div id="map-canvas"/>
  </body>
</html>
