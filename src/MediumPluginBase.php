<?php

/**
 * @file
 * Contains \Drupal\medium\Plugin\MediumPluginBase.
 */

namespace Drupal\medium;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;
use Drupal\medium\MediumPluginInterface;
use Drupal\medium\Entity\Medium;

/**
 * Defines a base Medium plugin implementation.
 *
 * @see \Drupal\medium\MediumPluginInterface
 * @see \Drupal\medium\MediumPluginManager
 * @see plugin_api
 */
abstract class MediumPluginBase extends PluginBase implements MediumPluginInterface {
  /**
   * {@inheritdoc}
   */
  public function alterEditorJS(array &$data, Medium $medium_editor, Editor $editor = NULL) {
  }

  /**
   * {@inheritdoc}
   */
  public function alterToolbarWidget(array &$widget) {
  }

  /**
   * {@inheritdoc}
   */
  public function alterEditorForm(array &$form, FormStateInterface $form_state, Medium $medium_editor) {
  }

  /**
   * {@inheritdoc}
   */
  public function validateEditorForm(array &$form, FormStateInterface $form_state, Medium $medium_editor) {
  }
}
