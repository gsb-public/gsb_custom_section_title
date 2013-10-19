<?php

/**
 * @file
 * Contains \Drupal\gsb_custom_section_title\Form\SectionTitleDeleteForm.
 */

namespace Drupal\gsb_custom_section_title\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;

/**
 * @todo.
 */
class SectionTitleDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the %label section title?', array('%label' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return array(
      'route_name' => 'gsb_custom_section_title.configure',
    );
  }

}
