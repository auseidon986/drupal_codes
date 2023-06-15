<?php

namespace Drupal\carroll_base\EventSubscriber;

use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 */
class RedirectSubscriber implements EventSubscriberInterface {

  /**
   *
   */
  public function redirectRequest(RequestEvent $event) {

    $rcs = [];
    $newPath = '';
    $newPathPieces = [];

    $config = \Drupal::config('carroll_base.settings');
    $redirectConfigs = $config->get('carroll_base.carroll_base_redirect_rules');

    $uri = $this->requestStack->getCurrentRequest()->getRequestUri();
    $path = $this->requestStack->getCurrentRequest()->getPathInfo();

    if ($redirectConfigs) {
      $redirectConfigs = str_replace("\r\n", "\n", $redirectConfigs);
      $rcsLines = explode("\n", $redirectConfigs);

      foreach ($rcsLines as $rcl) {
        $_rule = explode('|', $rcl);
        if (count($_rule) == 2) {
          $rcs[trim($_rule[0])] = explode(',', trim($_rule[1]));
        }
      }

      // $path = \Drupal::service('path.current')->getPath();
      $path = \Drupal::request()->getRequestUri();

      foreach ($rcs as $rk => $rv) {
        if (strpos($path, $rk) === 0) {
          $newPath = $rv[0] . substr($path, strlen($rk));
          $newPathPieces = [
            'old' => $rk,
            'tail' => substr($path, strlen($rk)),
            'new' => $rv[0],
            'siblings' => $rv,
          ];
          break;
        }
      }
    }

    if ($newPath != '') {

      $path_alias_repository = \Drupal::service('path_alias.repository');
      $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

      if (!$path_alias_repository->lookupByAlias($newPath, $language)) {
        //
        // Search for siblings
        // .
        $nextPathFound = FALSE;
        for ($ci = 1; $ci < count($newPathPieces['siblings']); $ci++) {
          $candPath = $newPathPieces['siblings'][$ci] . $newPathPieces['tail'];
          if ($path_alias_repository->lookupByAlias($candPath, $language)) {
            $newPath = $candPath;
            $nextPathFound = TRUE;
            break;
          }
        }

        if (!$nextPathFound) {
          // Try to find another possible case
          // For some persons, old url is {last-name}-{first_name}
          // .
          $p = explode("-", $newPathPieces['tail']);
          $candidate = implode("-", array_reverse($p));
          if ($path_alias_repository->lookupByAlias($newPathPieces['new'] . $candidate, $language)) {
            $newPath = $newPathPieces['new'] . $candidate;
          }

        }
      }

      header('Location: ' . $newPath, TRUE, 301);
      exit();

      //
      // Following code is not used because it redirects to homepage if no content found.
      //
      $responseHeaders = [
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
      ];

      $response = new TrustedRedirectResponse($newPath, 301, $responseHeaders);
      $event->setResponse($response);
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['redirectRequest', 30];
    return $events;
  }

}
