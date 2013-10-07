<?php

/**
 * Form constructor for the custom section titles form.
 */
function gsb_custom_section_title_form($form, &$form_state, $section_id = '_new') {
  $form['#tree'] = TRUE;
  $form['#attached']['library'][] = array('system', 'drupal.ajax');
  $is_new = $section_id === '_new';
  $section = gsb_custom_section_title_get_sections($section_id);
  $form['sections'] = array(
    '#title' => $is_new ? t('Add new section title') : t('Edit section title'),
    '#type' => 'fieldset',
    '#attributes' => array(
      'id' => 'gsb-custom-section-title-fieldset',
    ),
    $section_id => _gsb_custom_section_title_row_form($section_id, $section),
  );
  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => $is_new ? t('Save section title') : t('Update section title'),
  );
  $form['actions']['cancel'] = array(
    '#type' => 'submit',
    '#value' => t('Cancel'),
    '#access' => !$is_new,
    '#submit' => array('gsb_custom_section_title_form_cancel'),
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
function gsb_custom_section_title_form_cancel(&$form, &$form_state) {
  $form_state['redirect'] = 'admin/config/user-interface/gsb-custom-section-title';
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
    $links['edit'] = array(
      'title' => t('Edit'),
      'href' => 'gsb_custom_section_title/nojs/edit/' . $section_id,
      'attributes' => array('class' => array('use-ajax')),
    );
    $links['delete'] = array(
      'title' => t('Delete'),
      'href' => 'gsb_custom_section_title/nojs/delete/' . $section_id,
      'attributes' => array('class' => array('use-ajax')),
      'query' => array(
        'token' => drupal_get_token($section_id),
      ),
    );
    $row[] = array('data' => array('#theme' => 'links', '#links' => $links));
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

/**
 * Edits a section title.
 *
 * @param int $id
 *   The unique identifier for this row.
 * @param bool $js
 *   Whether this form was submitted via AJAX or not. Defaults to FALSE.
 */
function gsb_custom_section_title_edit_section($id, $js = FALSE) {
  if ($js) {
    $form = drupal_get_form('gsb_custom_section_title_form', $id);
    $html = drupal_render($form['sections']);
    //$commands[] = ajax_command_html('#gsb-custom-section-title-fieldset .fieldset-legend', t('Edit section title'));
    $commands[] = ajax_command_replace('#gsb-custom-section-title-fieldset', $html);
    print ajax_render($commands);
    drupal_exit();
  }
  else {
    drupal_goto('admin/config/user-interface/gsb-custom-section-title/' . $id);
  }
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
    //gsb_custom_section_title_set_sections($sections);
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
    drupal_goto('admin/config/user-interface/gsb-custom-section-title');
  }
}
