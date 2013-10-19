<?php

/**
 * @file
 * Contains \Drupal\gsb_custom_section_title\Form\SectionTitleForm.
 */

namespace Drupal\gsb_custom_section_title\Form;

use Drupal\Core\Form\FormBase;

/**
 * @todo.
 */
class SectionTitleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gsb_custom_section_title_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, $section_id = NULL) {
    $form['#tree'] = TRUE;
    $is_new = $section_id === '_new';
    $section = gsb_custom_section_title_get_section($section_id);
    $form['sections'] = array(
      '#title' => $is_new ? t('Add new section title') : t('Edit section title'),
      '#type' => 'fieldset',
      '#attributes' => array(
        'id' => 'gsb-custom-section-title-fieldset',
      ),
      '_new' => _gsb_custom_section_title_row_form($section_id, $section),
    );
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $is_new ? t('Save section title') : t('Update section title'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $sections = gsb_custom_section_title_get_sections();
    $new_section = $form_state['values']['sections']['_new'];
    $count = count($sections);
    // If this is a new section, make the ID the last key.
    if ($new_section['id'] === '') {
      $new_section['id'] = $count;
    }
    // If this section already exists, overwrite the old section.
    if ($new_section['id'] < $count) {
      $sections[$new_section['id']] = $new_section;
      drupal_set_message(t('A section title named %title has been added.', array('%title' => $new_section['title'])));
    }
    // Otherwise, append it to the end.
    else {
      $sections[] = $new_section;
      drupal_set_message(t('The section title named %title has been updated.', array('%title' => $new_section['title'])));
    }
    gsb_custom_section_title_set_sections($sections);
  }

}
