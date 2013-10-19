<?php

/**
 * Returns HTML for the custom section title table.
 */
function theme_gsb_custom_section_title_table($variables) {
  $form = $variables['form'];
  if (!empty($form['operations'])) {
    foreach (element_children($form['operations']) as $key) {
      $form['table']['#rows'][$key]['data'][]['data'] = $form['operations'][$key];
      hide($form['operations'][$key]);
    }
  }
  return drupal_render_children($form);
}

/**
 * Deletes a section title.
 *
 * @param int $id
 *   The unique identifier for this row.
 * @param string $token
 *   The secure token.
 * @param bool $js
 *   Whether this form was submitted via AJAX or not. Defaults to FALSE.
 */
function gsb_custom_section_title_delete_section($id, $token, $js = FALSE) {
  if (drupal_valid_token($token, $id)) {
    $sections = \Drupal::config('gsb_custom_section_title.settings')->get('sections');
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
