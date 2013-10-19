<?php

/**
 * @file
 * Contains \Drupal\gsb_custom_section_title\Controller\SectionTitleController.
 */

namespace Drupal\gsb_custom_section_title\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for gsb_custom_section_title routes.
 */
class SectionTitleController extends ControllerBase {

  /**
   * Presents the section title table and form.
   */
  public function overview($section_id) {
    module_load_include('admin.inc', 'gsb_custom_section_title');
    return array(
      'table' => drupal_get_form('gsb_custom_section_title_table'),
      'section' => drupal_get_form('gsb_custom_section_title_form', $section_id),
    );
  }

}
