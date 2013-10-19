<?php

/**
 * @file
 * Contains \Drupal\gsb_custom_section_title\Entity\SectionTitle.
 */

namespace Drupal\gsb_custom_section_title\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\gsb_custom_section_title\SectionTitleInterface;

/**
 * Defines the section title entity.
 *
 * @EntityType(
 *   id = "gsb_custom_section_title",
 *   label = @Translation("Section title"),
 *   controllers = {
 *     "storage" = "Drupal\Core\Config\Entity\ConfigStorageController",
 *     "list" = "Drupal\gsb_custom_section_title\SectionTitleList",
 *     "form" = {
 *       "add" = "Drupal\gsb_custom_section_title\Form\SectionTitleAddForm",
 *       "edit" = "Drupal\gsb_custom_section_title\Form\SectionTitleEditForm",
 *       "delete" = "Drupal\gsb_custom_section_title\Form\SectionTitleDeleteForm"
 *     }
 *   },
 *   admin_permission = "administer custom section titles",
 *   config_prefix = "gsb_custom.section_title",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "edit-form" = "admin/config/gsb/custom-section-title/manage/{gsb_custom_section_title}"
 *   }
 * )
 */
class SectionTitle extends ConfigEntityBase implements SectionTitleInterface {

  /**
   * @var string
   */
  public $id;

  /**
   * @var string
   */
  public $label;

  /**
   * @var string
   */
  public $uuid;

  /**
   * @var bool
   */
  protected $link = FALSE;

  /**
   * @var string
   */
  protected $link_path;

  /**
   * @var string
   */
  protected $paths;

  /**
   * @return bool
   */
  public function hasLink() {
    return (bool) $this->link;
  }

  /**
   * @return string
   */
  public function getLink() {
    return $this->hasLink() ? l($this->label(), $this->getLinkPath()) : '';
  }

  /**
   * @return string
   */
  public function getLinkPath() {
    return $this->link_path;
  }

  /**
   * @return string
   */
  public function getPaths() {
    return $this->paths;
  }

  /**
   * {@inheritdoc}
   */
  public function getExportProperties() {
    $properties = parent::getExportProperties();
    $names = array(
      'link',
      'link_path',
      'paths',
    );
    foreach ($names as $name) {
      $properties[$name] = $this->get($name);
    }
    return $properties;
  }

}
