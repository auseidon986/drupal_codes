<?php

function ume_base_link_alter(&$variables) {

  $urlObj = $variables['url'];

  if (!$urlObj->isRouted() || $urlObj->isExternal()) {
    return;
  }

  try {

    $uri = $urlObj->getInternalPath();

    if (str_starts_with($uri, 'taxonomy/term/')) {

      $x = explode('/', $uri);
      $tid = end($x);
      $fragment = ume_base_get_resource_topic_custom_url($tid);

      if ($fragment) {
        $host = \Drupal::request()->getSchemeAndHttpHost();
        // $newUrlObj = Url::fromUri('base:' . ume_base_get_resource_topic_base_path(), ['absolute' => TRUE, 'fragment' => $fragment]);
        $newUrlObj = Url::fromUri($host . '/' . $fragment, ['absolute' => TRUE]);
        $variables['url'] = $newUrlObj;
      }
    }
  } catch (Exception $e) {}

}

function ip_link_block_with_view_preprocess_paragraph__ip_link_block_with_view(&$variables) {

  return;

  $entity = $variables['elements']['#paragraph'];
  $displayMode = $entity->field_ip_lbv_listing_type->value;

  $viewEntity = $entity->field_ip_lbv_view_block->entity;

  if ($viewEntity) {
    $viewId = $viewEntity->id();

    if ($viewId == 'view_jhu_apl_aoi_listing') {
      // Do something.
    }

    $variables['_view_mode'] = 'fancy';
  }

}

/**
 * Breadcrumb hook
 */
function collegetown_preprocess_breadcrumb(&$variables){
  // Disable cache
  $variables['#cache']['contexts'][] = 'url';
}


/**
 * Implements hook_theme_suggestions_alter().
 */
function idfive_calendar_theme_suggestions_alter(array &$suggestions, array $variables, $hook) {
  if ($hook == 'select') {
    if (isset($variables['element']['#field_name'])) {
      $suggestions[] = 'select__' . str_replace('-', '_', $variables['element']['#field_name']);
    }
  }
}

/**
 * Implements hook_theme().
 */
function idfive_calendar_theme() {
  return [
    'select__field_ic_taxonomy' => [
      'base hook' => 'select',
    ],
  ];
}


function umd_global_preprocess_breadcrumb(&$variables) {
  // Cache control
  $variables['#cache']['contexts'][] = 'url.path';

  /*
  $request = \Drupal::request();
  $route_match = \Drupal::routeMatch();
  if (($node = $route_match->getParameter('node')) && $variables['breadcrumb']) {
    // remove added title from umd_terp base theme. Let menu_breadcrumb module handle it.
    array_pop($variables['breadcrumb']);
  }
  */
}