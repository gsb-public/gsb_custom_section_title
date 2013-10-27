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
      'table' => \Drupal::formBuilder()->getForm('Drupal\gsb_custom_section_title\Form\SectionTitleTable'),
      'section' => \Drupal::formBuilder()->getForm('Drupal\gsb_custom_section_title\Form\SectionTitleForm', $section_id),
    );
  }

  /**
   * Deletes a section title when called via AJAX.
   *
   * @param int $id
   *   The unique identifier for this row.
   * @param string $token
   *   The secure token.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function deleteSectionAjax($id, $token) {
    if (\Drupal::csrfToken()->validate($token, $id)) {
      $sections = $this->deleteSection($id);
      // If there are no more sections, remove the whole table.
      if (empty($sections)) {
        $id = 'table';
      }
      $response = new AjaxResponse();
      $response->addCommand(new RemoveCommand("#gsb-custom-section-title-$id"));
      return $response;
    }
    return $this->redirect('gsb_custom_section_title.configure');
  }

  /**
   * Deletes a section title when called directly.
   *
   * @param int $id
   *   The unique identifier for this row.
   * @param string $token
   *   The secure token.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function deleteSectionNojs($id, $token) {
    if (\Drupal::csrfToken()->validate($token, $id)) {
      $this->deleteSection($id);
    }
    return $this->redirect('gsb_custom_section_title.configure');
  }

  /**
   * Deletes a section title.
   *
   * @param $id
   *   The section title being deleted.
   *
   * @return array
   *   The remaining section titles.
   */
  protected function deleteSection($id) {
    $config = $this->config('gsb_custom_section_title.settings');
    $sections = $config->get('sections');
    unset($sections[$id]);
    $config->set('sections', $sections);
    //$config->set('sections', $sections)->save();
    return $sections;
  }

}
