<?php

namespace Drupal\auslee_module\TwigExtension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * Extra Utility Class to provide custom Twig Filters.
 */
class Xtras extends AbstractExtension {

  /**
   * Generates a list of all Twig filters that this extension defines.
   */
  public function getFunctions() {
    return [
      new TwigFunction('urlFriendlyName', [$this, 'getUrlFriendlyName']),
    ];
  }

  /**
   * Gets a unique identifier for this Twig extension.
   */
  public function getName() {
    return 'auslee_module.xtras_extension';
  }

  /**
   * Previous node.
   *
   * @param string $name
   *   The source string to be converted.
   *
   * @return string
   *   The converted URL Friendly Name.
   */
  public function getUrlFriendlyName($name) {
    return auslee_module_alias_cleaner($name);
  }

}
