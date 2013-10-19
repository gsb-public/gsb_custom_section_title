<?php

/**
 * @file
 * Contains \Drupal\gsb_custom_section_title\Form\SectionTitleEditForm.
 */

namespace Drupal\gsb_custom_section_title\Form;

/**
 * @todo.
 */
class SectionTitleEditForm extends SectionTitleFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $form = parent::form($form, $form_state);
    $form['#title'] = $this->entity->label();
    return $form;
  }

}
