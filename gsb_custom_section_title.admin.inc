<?php

/**
 * Page callback: Presents the section title table and form.
 */
function gsb_custom_section_title_overview($section_id = '_new') {
  return array(
    'table' => drupal_get_form('gsb_custom_section_title_table'),
    'section' => drupal_get_form('gsb_custom_section_title_form', $section_id),
  );
}

/**
 * Form constructor for the custom section titles form.
 */
function gsb_custom_section_title_form($form, &$form_state, $section_id) {
  $form['#tree'] = TRUE;
  $is_new = $section_id === '_new';
  $section = gsb_custom_section_title_get_sections($section_id);
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
  $is_new = $row == '_new';
  $form['id'] = array(
    '#type' => 'hidden',
  );
  $form['title'] = array(
    '#title' => t('Title'),
    '#type' => 'textfield',
    '#required' => !$is_new,
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
    '#required' => !$is_new,
  );
  $form['actions']['cancel'] = array(
    '#type' => 'link',
    '#title' => t('Cancel'),
    '#href' => 'admin/config/gsb/custom-section-title',
    '#access' => !$is_new,
  );
  return $form;
}

/**
 * Form submission handler for gsb_custom_section_title_form().
 */
function gsb_custom_section_title_form_submit(&$form, &$form_state) {
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

/**
 * Form constructor for the custom section titles table.
 */
function gsb_custom_section_title_table($form, &$form_state) {
  $form['#attached']['library'][] = array('system', 'drupal.ajax');
  $rows = array();
  $sections = gsb_custom_section_title_get_sections();
  uasort($sections, 'drupal_sort_title');
  foreach ($sections as $section_id => $section) {
    $row = array();
    $row[] = $section['title'];
    $row[] = !empty($section['link']) ? l($section['link_path'], $section['link_path']) : '';
    $row[] = array('data' => array('#theme' => 'item_list', '#items' => explode("\n", $section['paths'])));
    $operations = array();
    $operations['edit'] = array(
      '#type' => 'button',
      '#value' => t('Edit'),
      '#name' => "gsb-custom-section-title-$section_id",
      '#ajax' => array(
        'callback' => 'gsb_custom_section_title_edit_section',
        'wrapper' => 'gsb-custom-section-title-fieldset .fieldset-wrapper',
        'method' => 'html',
      ),
    );
    $operations['delete'] = array(
      '#type' => 'link',
      '#title' => t('Delete'),
      '#href' => 'gsb_custom_section_title/nojs/delete/' . $section_id,
      '#options' => array(
        'attributes' => array('class' => array('use-ajax')),
        'query' => array(
          'token' => drupal_get_token($section_id),
        ),
      ),
    );
    $form['operations'][$section_id] = $operations;
    $rows[$section_id] = array('data' => $row, 'id' => "gsb-custom-section-title-$section_id");
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
      '#attributes' => array('id' => 'gsb-custom-section-title-table'),
    );
  }

  return $form;
}

/**
 * Returns HTML for the custom section title table.
 */
function theme_gsb_custom_section_title_table($variables) {
  $form = $variables['form'];
  foreach (element_children($form['operations']) as $key) {
    $form['table']['#rows'][$key]['data'][]['data'] = $form['operations'][$key];
    hide($form['operations'][$key]);
  }
  return drupal_render_children($form);
}

/**
 * Edits a section title.
 */
function gsb_custom_section_title_edit_section($form, &$form_state) {
  $section_id = str_replace('gsb-custom-section-title-', '', $form_state['triggering_element']['#name']);
  $replacement = drupal_get_form('gsb_custom_section_title_form', $section_id);
  $form['sections']['_new'] = $replacement['sections']['_new'];
  $form['sections']['_new']['id']['#value'] = $section_id;
  return $form['sections'];
}

/**
 * Deletes a section title.
 *
 * @param int $id
 *   The unique identifier for this row.
 * @param bool $js
 *   Whether this form was submitted via AJAX or not. Defaults to FALSE.
 */
function gsb_custom_section_title_delete_section($id, $js = FALSE) {
  if (isset($_GET['token']) && drupal_valid_token($_GET['token'], $id)) {
    $sections = gsb_custom_section_title_get_sections();
    unset($sections[$id]);
    gsb_custom_section_title_set_sections($sections);
    if ($js) {
      // If there are no more sections, remove the whole table.
      if (empty($sections)) {
        $id = 'table';
      }
      $commands[] = ajax_command_remove("#gsb-custom-section-title-$id");
      print ajax_render($commands);
      drupal_exit();
    }
  }
  else {
    drupal_goto('admin/config/gsb/custom-section-title');
  }
}
