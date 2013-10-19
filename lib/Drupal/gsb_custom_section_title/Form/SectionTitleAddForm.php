<?php

/**
 * @file
 * Contains \Drupal\gsb_custom_section_title\Form\SectionTitleAddForm.
 */

namespace Drupal\gsb_custom_section_title\Form;

/**
 * @todo.
 */
class SectionTitleAddForm extends SectionTitleFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $form = parent::form($form, $form_state);
    $form['#title'] = $this->t('Add section title');
    return $form;
  }

}
