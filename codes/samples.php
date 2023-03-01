

// Load block into $variables

$breadcrumb_block = Block::load('breadcrumbs');
if ($breadcrumb_block) {
  $breadcrumb_block_content = \Drupal::entityTypeManager()->getViewBuilder('block')->view($breadcrumb_block);
  $variables['breadcrumbs'] = $breadcrumb_block_content;
}






function ip_basics__paragraph__get_custom_urls ($items) {
  $customUrls = [];

  foreach ($items as $key => $item) {
    $uri = $item->uri;
    if (str_starts_with($uri, 'entity:taxonomy_term')) {
      // get taxonomy id

      $x = explode('/', $uri);
      $tid = end($x);
      $customUrl = ume_base_get_resource_topic_custom_url($tid);

      if ($customUrl) $customUrls[$key] = '/' . $customUrl;
    }
  }

  return $customUrls;
}
