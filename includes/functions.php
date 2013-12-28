<?php

/**
 * @file
 * This file stores the processing functions.
 */

/**
 * Returns a json object from an API request.
 */
function foursquare_map_fetch_data($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}

/**
 * Takes the full response from foursquare and collects required information.
 */
function foursquare_map_get_markers($items) {
  $markers = array();

  // Build links for images, but only if they are attached to the post.
  foreach ($items as $item) {
    if ($item->photos->count == 1) {
      $photo = $item->photos->items[0]->prefix . 'width200' . $item->photos->items[0]->suffix;
      $link = $item->photos->items[0]->prefix . 'width960' . $item->photos->items[0]->suffix;
    }
    else {
      $photo = FALSE;
      $link = FALSE;
    }

    // Set relevant information into markers.
    $markers[] = array(
      'lat' => $item->venue->location->lat,
      'lng' => $item->venue->location->lng,
      'name' => $item->venue->name,
      'date' => $item->createdAt * 1000,
      'id' => $item->id,
      'photo' => $photo,
      'link' => $link,
      'post' => $item->posts->items[0]->text,
    );
  }

  return $markers;
}
