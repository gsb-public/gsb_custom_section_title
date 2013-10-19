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
      '_new' => $this->getRowForm($section_id, $section),
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
    $sections = $this->config('gsb_custom_section_title.settings')->get('sections');
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

  /**
   * Generates the section title form item for a single row.
   *
   * @param string|int $row
   *   The unique identifier for this row. Either an integer or '_new'.
   * @param array $data
   *   The data for this row.
   *
   * @return array
   *   The form item for a single section title row.
   */
  protected function getRowForm($row, array $data) {
    $form['id'] = array(
      '#type' => 'hidden',
    );
    $form['title'] = array(
      '#title' => t('Title'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $data['title'],
    );
    $form['link'] = array(
      '#title' => t('Link?'),
      '#type' => 'checkbox',
      '#default_value' => $data['link'],
    );
    $form['link_path'] = array(
      '#title' => t('Link path'),
      '#type' => 'textfield',
      '#field_prefix' => url(NULL, array('absolute' => TRUE)),
      '#default_value' => $data['link_path'],
      '#states' => array(
        'visible' => array(
          ':input[name="sections[' . $row . '][link]"]' => array('checked' => TRUE),
        ),
      ),
    );
    $form['paths'] = array(
      '#title' => t('Paths'),
      '#type' => 'textarea',
      '#default_value' => $data['paths'],
      '#rows' => 4,
      '#required' => TRUE,
    );
    $form['actions']['cancel'] = array(
      '#type' => 'link',
      '#title' => t('Cancel'),
      '#href' => 'admin/config/gsb/custom-section-title',
      '#access' => $row !== '_new',
    );
    return $form;
  }

}
