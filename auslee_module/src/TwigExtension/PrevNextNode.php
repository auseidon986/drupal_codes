<?php

namespace Drupal\auslee_module\TwigExtension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Provide custom Twig filter for Next & Previous Node.
 */
class PrevNextNode extends AbstractExtension {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new MyBlock.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager instance.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The languge manager instance.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, LanguageManagerInterface $language_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->languageManager = $language_manager;
  }

  /**
   * Generates a list of all Twig filters that this extension defines.
   */
  public function getFunctions() {
    return [
      new TwigFunction('customPrevious', [$this, 'customPrevious']),
      new TwigFunction('customNext', [$this, 'customNext']),
    ];
  }

  /**
   * Gets a unique identifier for this Twig extension.
   */
  public function getName() {
    return 'custom.prevnextnode_extension';
  }

  /**
   * Previous node.
   *
   * @param array $prevNextInfo
   *   Base node to get previous node.
   *
   * @return array|bool
   *   Previous node or false if no existing.
   */
  public function customPrevious(array $prevNextInfo) {

    $node_storage = $this->entityTypeManager->getStorage('node');
    $node = $node_storage->load($prevNextInfo['nid']);

    if (!$node) {
      return NULL;
    }
    return $this->getNodeInformation($prevNextInfo['node_type'], $node->getCreatedTime(), '<', 'DESC');
  }

  /**
   * Next node.
   *
   * @param array $prevNextInfo
   *   Base node to get next node.
   *
   * @return array|bool
   *   Next node or false if no existing.
   */
  public function customNext(array $prevNextInfo) {

    $node_storage = $this->entityTypeManager->getStorage('node');
    $node = $node_storage->load($prevNextInfo['nid']);

    if (!$node) {
      return NULL;
    }
    return $this->getNodeInformation($prevNextInfo['node_type'], $node->getCreatedTime(), '>', 'ASC');
  }

  /**
   * Get current langcode.
   *
   * @return string
   *   Current language id.
   */
  public function getCurrentLangcode() {
    return $this->languageManager->getCurrentLanguage()->getId();
  }

  /**
   * Previous or next node.
   *
   * @param string $node_type
   *   Node type.
   * @param int $date
   *   Base datetime.
   * @param string $date_comparator
   *   Comparator.
   * @param string $sort_order
   *   Sort order.
   *
   * @return array|bool
   *   Node or false if no existing.
   */
  public function getNodeInformation($node_type, $date, $date_comparator, $sort_order) {
    $prev_or_next = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', $node_type)
      ->condition('status', 1)
      ->condition('created', $date, $date_comparator)
      ->sort('created', $sort_order)
      ->range(0, 1)
      ->execute();

    if (!$prev_or_next) {
      return FALSE;
    }

    // Get the node itself.
    $node_storage = $this->entityTypeManager->getStorage('node');
    $prev_or_next = $node_storage->load(array_values($prev_or_next)[0]);

    // Get the available languages for the given node.
    $available_languages = $prev_or_next->getTranslationLanguages();
    // If the current language is defined in the available languages array.
    if (array_key_exists($this->getCurrentLangcode(), $available_languages)) {
      // Get the translated node.
      $translation = $prev_or_next->getTranslation($this->getCurrentLangcode());

      // Return the information you need, can be w/e you want.
      return [
        'title' => $translation->getTitle(),
        'id' => $translation->id(),
        'path' => $translation->toUrl()->toString(),
      ];
    }

    return FALSE;
  }

}
