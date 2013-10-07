<?php

/**
 * Form constructor for the custom section titles form.
 */
function gsb_custom_section_title_form($form, &$form_state) {
  $form['#tree'] = TRUE;
  $form['#attached']['library'][] = array('system', 'drupal.ajax');
  $new_data = array(
    'title' => '',
    'link' => FALSE,
    'link_path' => '',
    'paths' => '',
  );
  $form['sections'] = array(
    '#title' => t('Add new section title'),
    '#type' => 'fieldset',
  );
  $form['sections']['_new'] = _gsb_custom_section_title_row_form('_new', $new_data);
  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save section titles'),
  );
  return $form;
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
function _gsb_custom_section_title_row_form($row, array $data) {
  // Form items should never be required for the "new" row.
  $form['id'] = array(
    '#type' => 'value',
    '#value' => $row,
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
  return $form;
}

/**
 * Form submission handler for gsb_custom_section_title_form().
 */
function gsb_custom_section_title_form_submit(&$form, &$form_state) {
  $sections = gsb_custom_section_title_get_sections();
  $sections[] = $form_state['values']['sections']['_new'];
  gsb_custom_section_title_set_sections($sections);
}

/**
 * Returns HTML for the section titles form.
 *
 * @param array $variables
 *   An associative array containing:
 *   - form: A render element representing the form.
 *
 * @return string
 *   The HTML for the section titles form.
 */
function theme_gsb_custom_section_title_form($variables) {
  $form = $variables['form'];

  $rows = array();
  foreach (gsb_custom_section_title_get_sections() as $section_id => $section) {
    $row = array();
    $row[] = $section['title'];
    $row[] = !empty($section['link']) ? $section['link_path'] : '';
    $row[] = array('data' => array('#theme' => 'item_list', '#items' => explode("\n", $section['paths'])));
    $operations = array();
    $operations['delete'] = array(
      '#type' => 'link',
      '#title' => t('Delete'),
      '#href' => 'gsb_custom_section_title/nojs/delete/' . $section_id,
      '#options' => array(
        'query' => array(
          'token' => drupal_get_token($section_id),
        ),
      ),
      '#ajax' => array(
        'wrapper' => "gsb-custom-section-title-$section_id",
      ),
    );
    $row[] = array('data' => $operations);
    $rows[] = array('data' => $row, 'id' => "gsb-custom-section-title-$section_id");
  }

  if (!empty($rows)) {
    $form['table'] = array(
      '#theme' => 'table',
      '#header' => array(
        t('Title'),
        t('Link'),
        t('Paths'),
        t('Operations'),
      ),
      '#rows' => $rows,
      '#weight' => -100,
      '#attributes' => array('id' => 'gsb-custom-section-title-table'),
    );
    uasort($form, 'element_sort');
  }

  return drupal_render_children($form);
}

function _gsb_custom_section_title_callback($form, &$form_state) {
  dpm($form_state['triggering_element']);
  return $form;
}
