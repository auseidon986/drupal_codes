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