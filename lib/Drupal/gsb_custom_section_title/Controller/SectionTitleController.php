<?php

/**
 * @file
 * Contains \Drupal\gsb_custom_section_title\Controller\SectionTitleController.
 */

namespace Drupal\gsb_custom_section_title\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for gsb_custom_section_title routes.
 */
class SectionTitleController extends ControllerBase {

  /**
   * Presents the section title table and form.
   */
  public function overview($section_id) {
    return array(
      'table' => drupal_get_form('Drupal\gsb_custom_section_title\Form\SectionTitleTable'),
      'section' => drupal_get_form('Drupal\gsb_custom_section_title\Form\SectionTitleForm', $section_id),
    );
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
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function deleteSection($id, $token, $js) {
    if (\Drupal::csrfToken()->validate($token, $id)) {
      $config = $this->config('gsb_custom_section_title.settings');
      $sections = $config->get('sections');
      unset($sections[$id]);
      $config->set('sections', $sections)->save();
      if ($js) {
        // If there are no more sections, remove the whole table.
        if (empty($sections)) {
          $id = 'table';
        }
        $response = new AjaxResponse();
        $response->addCommand(new RemoveCommand("#gsb-custom-section-title-$id"));
        return $response;
      }
    }
    return $this->redirect('gsb_custom_section_title.configure');
  }

}
