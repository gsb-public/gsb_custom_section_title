<?php

/**
 * @file
 * Contains \Drupal\gsb_custom_section_title\Form\SectionTitleTable.
 */

namespace Drupal\gsb_custom_section_title\Form;

use Drupal\Core\Form\FormBase;

/**
 * @todo.
 */
class SectionTitleTable extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gsb_custom_section_title_table';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $form['#attached']['library'][] = array('system', 'drupal.ajax');
    $rows = array();
    $sections = $this->config('gsb_custom_section_title.settings')->get('sections');
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
          'callback' => array($this, 'editSection'),
          'wrapper' => 'gsb-custom-section-title-fieldset .fieldset-wrapper',
          'method' => 'html',
        ),
      );
      $operations['delete'] = array(
        '#type' => 'button',
        '#value' => t('Delete'),
        '#ajax' => array(
          'path' => 'gsb_custom_section_title/nojs/delete/' . $section_id . '/' . drupal_get_token($section_id),
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
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
  }

  /**
   * Edits a section title.
   */
  public function editSection($form, &$form_state) {
    $section_id = str_replace('gsb-custom-section-title-', '', $form_state['triggering_element']['#name']);
    $replacement = drupal_get_form('Drupal\gsb_custom_section_title\Form\SectionTitleForm', $section_id);
    $form['sections']['_new'] = $replacement['sections']['_new'];
    $form['sections']['_new']['id']['#value'] = $section_id;
    return $form['sections'];
  }

}
