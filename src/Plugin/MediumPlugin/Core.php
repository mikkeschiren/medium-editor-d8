<?php

/**
 * @file
 * Contains \Drupal\medium\Plugin\MediumPlugin\Core.
 */

namespace Drupal\medium\Plugin\MediumPlugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;
use Drupal\Component\Utility\String;
use Drupal\medium\MediumPluginBase;
use Drupal\medium\Entity\Medium;
use Drupal\medium\MediumToolbarWrapper;

/**
 * Defines Medium Core plugin.
 *
 * @MediumPlugin(
 *   id = "core",
 *   label = "Medium Core",
 *   weight = -10
 * )
 */
class Core extends MediumPluginBase {
  /**
   * {@inheritdoc}
   */
  public function alterToolbarWidget(array &$widget) {
  }

  /**
   * {@inheritdoc}
   */
  public function alterEditorForm(array &$form, FormStateInterface $form_state, Medium $medium_editor) {
    $form['settings'] += $this->getForm($form_state, $medium_editor);
  }

  /**
   * {@inheritdoc}
   */
  public function validateEditorForm(array &$form, FormStateInterface $form_state, Medium $medium_editor) {
    
  }
}
