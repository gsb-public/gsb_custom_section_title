<?php

/**
 * @file
 * Contains \Drupal\gsb_custom_section_title\SectionTitleList.
 */

namespace Drupal\gsb_custom_section_title;

use Drupal\Core\Config\Entity\ConfigEntityListController;
use Drupal\Core\Entity\EntityInterface;

/**
 * @todo.
 */
class SectionTitleList extends ConfigEntityListController {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['link'] = $this->t('Link');
    $header['paths'] = $this->t('Paths');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var $entity \Drupal\gsb_custom_section_title\SectionTitleInterface */
    $row['label'] = $this->getLabel($entity);
    $row['link'] = $entity->getLink();
    $row['paths'] = array('data' => array(
      '#theme' => 'item_list',
      '#items' => explode("\n", $entity->getPaths()),
    ));
    return $row + parent::buildRow($entity);
  }

}
