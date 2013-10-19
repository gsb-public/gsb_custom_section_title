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
