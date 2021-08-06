<?php

namespace Drupal\search_adds\Plugin\views\area;

use Drupal\views\Plugin\views\area\AreaPluginBase;

/**
 * Provides an area for the triggered text.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("search_adds_area")
 */
class SearchAddsArea extends AreaPluginBase {

  /**
   * Render the area.  This is the minimal required definition.
   *
   * @param bool $empty
   *   (optional) Indicator if view result is empty or not. Defaults to FALSE.
   *
   * @return array
   *   In any case we need a valid Drupal render array to return.
   */
  public function render($empty = FALSE) {
    // Collect the search text.
    $query = $this->view->exposed_data["search_text"];
    // Retrieve our trigger list.
    $config = \Drupal::config('search_adds.settings');
    $limit = $config->get('counted');
    // All letters lowercase and remove punctuation.
    $fuzzy_search = preg_replace('/[^a-z0-9]/', '', strtolower($query));

    for ($i = 1; $i <= $limit; $i++) {
      // @TODO:  allow for more than one match.
      $fuzzy_trigger = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($config->get('trigger_' . $i)));

      if (strpos($query, $config->get('trigger_' . $i)) !== FALSE) {
        $message = '<span class="search-adds-text">' . $config->get('response_' . $i) . '</span>';
        // Exact match.
        return [
          '#markup' => $message,
        ];
      }
      if (strpos($fuzzy_search, $fuzzy_trigger) !== FALSE) {
        $message = '<span class="search-adds-text">' . $config->get('response_' . $i) . '</span>';
        // Meh, close enough.
        return [
          '#markup' => $message,
        ];
      }

    }

    return [];
  }

}
