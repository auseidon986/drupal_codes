<?php

$test3 = \Drupal::service('path.current')->getPath();
$test4 = \Drupal::request()->server->get('REQUEST_METHOD');
$test5 = \Drupal::request()->query->get('q');
$test9 = \Drupal::request()->request->all();
$test10 = \Drupal::request()->getRequestUri();

// get node builder
$node = \Drupal::entityManager()->getStorage('node')->load($nid);
$view_builder = \Drupal::entityManager()->getViewBuilder('node');
$variables['node_full'] = $view_builder->view($node, 'full');
$variables['node_teaser'] = $view_builder->view($node, 'teaser');
$variables['body_full'] = $node->body->view('full');
$variables['body_teaser'] = $node->body->view('teaser');


// Load block into $variables

$breadcrumb_block = Block::load('breadcrumbs');
if ($breadcrumb_block) {
  $breadcrumb_block_content = \Drupal::entityTypeManager()->getViewBuilder('block')->view($breadcrumb_block);
  $variables['breadcrumbs'] = $breadcrumb_block_content;
}


$term_ids =  array (1,2,3);
$nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
  'field_tags' => $term_ids,
]);


// If your nodes not having revision then this code will work, by default it will return the first revision

  $nodes = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
 ->condition('field_tags', $termId)
 ->execute();
// If your nodes have multiple revisions, To get the nodes by the latest tagged revision. you need to use the following code

$nodes = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
->latestRevision()
->condition('field_tags', $termId, '=')
->execute();

// if you have multiple values then you can use array

 $termIds = [3,56,456];
 $nodes = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
 ->latestRevision()
 ->condition('field_tags', $termIds)
 ->execute();

// Load form
  $user = \Drupal::currentUser();
  $user_entity = \Drupal::entityTypeManager()
    ->getStorage('user')
    ->load($user->id());

  $formObject = \Drupal::entityTypeManager()
    ->getFormObject('user', 'default')
    ->setEntity($user_entity);
  $form = \Drupal::formBuilder()->getForm($formObject);



// Load View
  $news_view_id = 'carroll_recent_news';
  $view = \Drupal::entityTypeManager()
    ->getStorage('view')
    ->load($news_view_id)
    ->getExecutable();
  dump($view);
  // Get the NID from the View result.
  $view->setArguments([0 => implode('+', $tids)]); // Set first contextual filter parameter
  $view->initDisplay();
  // $view->setDisplay('display_id');
  $view->execute();
  $result = $view->result;

  dump($result);



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



/**
 * Implements hook_views_pre_execute().
 *
 * Modify query to filter related internships except for itself based on better relevance.
 */
function collegetown_base_views_pre_execute($view) {
  $node = \Drupal::routeMatch()->getParameter('node');
  $viewId = $view->storage->id();

  if ($node && ($node instanceof Node)) {

    $type = $node->getType();

    if ($type == 'collegetown_internship') {

      $currentTerm = intval($node->field_collegetown_internship_tm->value);

      $query = &$view->build_info['query'];
      $query->leftJoin('node__field_collegetown_internship_tm', 'tm', '(node_field_data.nid = tm.entity_id) AND (node_field_data.type = tm.bundle)');
      $query->addExpression('("tm"."field_collegetown_internship_tm_value" - ' . $currentTerm . ' + 5) % 5', '_term_order_field');
      $query->orderBy('_term_order_field', 'ASC');
      $query->orderBy('created', 'DESC');
      $query->condition('node_field_data.nid', $node->id(), '<>');

    }
  }

  return $view;
}

/**
 * Implements hook_views_pre_view().
 */
function collegetown_base_views_pre_view($view, $display_id, array &$args) {

  $node = \Drupal::routeMatch()->getParameter('node');
  $viewId = $view->storage->id();

  if ($node && ($node instanceof Node)) {

    $type = $node->getType();

    if ($type == 'collegetown_internship') {
      if (($viewId == 'view_collegetown_internship') && ($display_id == 'block_view_collegetown_internship_more')) {
        // @todo Add your handling code.
      }

    }
  }

  if ($viewId == 'your_view_id') {
    // @todo Add your handling code.
  }

}


/**
   * {@inheritdoc}
   */
  public function getSelectionResponse(MediaLibraryState $state, array $selected_ids) {

    /*
    Drupal\media_library\MediaLibraryState {#274
      #parameters: array:9 [
        "media_library_opener_id" => "media_library.opener.editor"
        "media_library_allowed_types" => array:3 [
          0 => "image"
          "audio" => "audio"
          "video" => "video"
        ]
        "media_library_selected_type" => "image"
        "media_library_remaining" => "1"
        "media_library_opener_parameters" => array:1 [
          "filter_format_id" => "basic_html"
        ]
        "hash" => "gqyjqLRGH_ubwgkuJDdY2IDODJ-MjN27b_jR9-nU4V0"
        "_wrapper_format" => "html"
        "ajax_form" => "1"
        "views_display_id" => "widget"
      ]
    }
    */

    $selected_media = $this->mediaStorage->load(reset($selected_ids)); // Drupal\media\Entity\Media

    $fid = $selected_media->getSource()->getSourceFieldValue($selected_media);
    $file = \Drupal\file\Entity\File::load($fid);
    $url = $file->createFileUrl();

    $response = new AjaxResponse();
    $values = [
      'attributes' => [
        'data-entity-type' => 'media',
        'data-entity-uuid' => $selected_media->uuid(),
        'data-align' => 'center',
        'src' => $url,
        'alt' => $selected_media->field_media_image->alt,
        'src-type' => $state->getSelectedTypeId(),
      ],
    ];
    $response->addCommand(new EditorDialogSave($values));

    return $response;
  }


  $webform_submission = \Drupal\webform\Entity\WebformSubmission::load(1);
  $data = $webform_submission->getData();
  // Get the document file object from uri.
  $document = \Drupal::entityTypeManager()
    ->getStorage('file')
    ->loadByProperties(['uri' => 'private://documentA.pdf']);
  // Get the file Id.
  $doc_id = reset($document)->id();
  // Set the file_attachment to fid.
  $data['file_attachment'] = $doc_id;
  $webform_submission->setData($data);
  $webform_submission->save();



  function onehouse_base_webform_submission_presave($submission) {

    $current_user = \Drupal::currentUser();
    $user = \Drupal::entityTypeManager()
        ->getStorage('user')
        ->load($current_user->id());
    //
    // Change bidder status
    //
    $user->set('field_user_bidder_status', 2);
    $user->set('field_user_accept', 1);

    $now = new DrupalDateTime();
    $now->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));
    $now_str = $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
    $user->set('field_user_last_form_on', $now_str);

    $violations = $user->validate();

    if (count($violations) === 0) {
      $user->save();
    }
  }

/// FILE related


  try {
    $response = $this->httpClient->request('GET', $url, [
      RequestOptions::TIMEOUT => 15,
    ]);
  }
  catch (TransferException $e) {
    \Drupal::logger('media_entity_remote_image')->error($e->getMessage() . "<pre>{$e->getTraceAsString()}</pre>");
    throw new RemoteImageException('Could not retrieve the remote image resource.', $url, [], $e);
  }

  [$format] = $response->getHeader('Content-Type');
  $content = (string) $response->getBody();
  $thumbnail = NULL;

  if (strpos($format, 'image/') === 0) {
    //

    try {
      $img_info = pathinfo($url);
      $time = time();
      $filename = "{$img_info['filename']}-$time.{$img_info['extension']}";

      $private_path = PrivateStream::basePath() . '/remote_image_thumbnails';
      \Drupal::service('file_system')->prepareDirectory($private_path, FileSystemInterface:: CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);

      $file = \Drupal::service('file.repository')->writeData($content, 'private://remote_image_thumbnails/' . $filename, FileSystemInterface::EXISTS_REPLACE);
      $file->setTemporary();
      $file->save();
      $thumbnail = $file->getFileUri();
      dump($thumbnail);
      dump(\Drupal::service('file_url_generator')->generateAbsoluteString($thumbnail));
    }
    catch (\Throwable $e) {
      \Drupal::logger('media_entity_remote_image')->error($e->getMessage() . "<pre>{$e->getTraceAsString()}</pre>");
      \Drupal::messenger()->addError('Could not generate thumbnail. See the logs for more information.');
    }

  } else {
    throw new RemoteImageException('The url is not returning an image.', $url);
  }
