<?php

/**
 * @file
 * Views hook implementations for the Search Adds module.
 */

/**
 * Implements hook_views_data().
 */
function search_adds_views_data() {
  // @todo: Only enable for search api views.
  $data['views']['search_adds_area'] = [
    'title' => t('Search Adds Message Area'),
    'help' => t('Print the responses to trigger words.'),
    'area' => [
      'id' => 'search_adds_area',
    ],
  ];

  return $data;
}
