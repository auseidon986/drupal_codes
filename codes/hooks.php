
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