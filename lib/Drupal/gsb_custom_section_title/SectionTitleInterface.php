<?php

/**
 * @file
 * Contains \Drupal\gsb_custom_section_title\SectionTitleInterface.
 */

namespace Drupal\gsb_custom_section_title;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * @todo.
 */
interface SectionTitleInterface extends ConfigEntityInterface {

  /**
   * @return bool
   */
  public function hasLink();

  /**
   * @return string
   */
  public function getLink();

  /**
   * @return string
   */
  public function getLinkPath();

  /**
   * @return string
   */
  public function getPaths();

}
