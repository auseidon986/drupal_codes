<?php


namespace Drupal\auslee_module\EventSubscriber;

use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent ;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class ResourceRedirectSubscriber implements EventSubscriberInterface {

  public function redirectResource(RequestEvent $event) {

    $redirect = auslee_module_get_redirect();
    if ($redirect) {

      $response_headers = [
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
      ];

      $response = new TrustedRedirectResponse($redirect['url'], $redirect['status'], $response_headers);
      $response->addCacheableDependency($redirect['node']);
      $event->setResponse($response);
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['redirectResource',30];
    return $events;
  }

}