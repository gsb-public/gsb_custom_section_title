<?php

/**
 * @file
 * Contains \Drupal\gsb_custom_section_title\Form\SectionTitleFormBase.
 */

namespace Drupal\gsb_custom_section_title\Form;

use Drupal\Core\Entity\EntityFormController;

/**
 * @todo.
 */
class SectionTitleFormBase extends EntityFormController {

  /**
   * @var \Drupal\gsb_custom_section_title\SectionTitleInterface
   */
  protected $entity;

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->entity->label(),
      '#description' => $this->t('A unique label for this advanced action. This label will be displayed in the interface of modules that integrate with actions.'),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#disabled' => !$this->entity->isNew(),
      '#description' => $this->t('A unique name for this action. It must only contain lowercase letters, numbers and underscores.'),
      '#required' => TRUE,
      '#machine_name' => array(
        'exists' => array($this, 'exists'),
      ),
    );
    $form['link'] = array(
      '#title' => $this->t('Link?'),
      '#type' => 'checkbox',
      '#default_value' => $this->entity->hasLink(),
    );
    $form['link_path'] = array(
      '#title' => $this->t('Link path'),
      '#type' => 'textfield',
      '#field_prefix' => $this->urlGenerator()->generateFromPath(NULL, array('absolute' => TRUE)),
      '#default_value' => $this->entity->getLinkPath(),
      '#states' => array(
        'visible' => array(
          ':input[name="link"]' => array('checked' => TRUE),
        ),
      ),
    );
    $form['paths'] = array(
      '#title' => $this->t('Paths'),
      '#type' => 'textarea',
      '#default_value' => $this->entity->getPaths(),
      '#rows' => 4,
      '#required' => TRUE,
    );

    return parent::form($form, $form_state);
  }

  public function validate(array $form, array &$form_state) {
    parent::validate($form, $form_state);

    // Ensure no blank lines were included.
    $paths = explode("\n", $form_state['values']['paths']);
    $paths = array_filter(array_map('trim', $paths));
    form_set_value($form['paths'], implode("\n", $paths), $form_state);

    // Uncheck the 'link' checkbox if a link path was not provided.
    $link_path = trim($form_state['values']['link_path']);
    if (empty($link_path) && $form_state['values']['link']) {
      form_set_value($form['link'], FALSE, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $this->entity->save();
    $form_state['redirect'] = 'admin/config/gsb/custom-section-title';
  }

  public function exists($id) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function delete(array $form, array &$form_state) {
    $form_state['redirect'] = 'admin/config/gsb/custom-section-title/manage/' . $this->entity->id() . '/delete';
  }

}
