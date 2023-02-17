

// Load block into $variables

$breadcrumb_block = Block::load('breadcrumbs');
if ($breadcrumb_block) {
  $breadcrumb_block_content = \Drupal::entityTypeManager()->getViewBuilder('block')->view($breadcrumb_block);
  $variables['breadcrumbs'] = $breadcrumb_block_content;
}