<?php

/**
 * @file
 * Contains \Drupal\medium\MediumPluginInterface.
 */

namespace Drupal\medium;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;
use Drupal\medium\Entity\MediumEditor;

/**
 * Defines an interface for Medium plugins.
 *
 * @see \Drupal\medium\MediumPluginBase
 * @see \Drupal\medium\MediumPluginManager
 * @see plugin_api
 */
interface MediumPluginInterface extends PluginInspectionInterface {

  /**
   * Returns plugin buttons.
   *
   * @return array
   *   An array of id:label pairs.
   */
  public function getButtons();

  /**
   * Alters JS data of a Medium Editor.
   *
   * @param array $data
   *   An associative array that holds 'libraries' and 'settings' of the editor.
   * @param \Drupal\medium\Entity\MediumEditor $medium_editor
   *   Medium Editor entity that owns the data.
   * @param \Drupal\editor\Entity\Editor $editor
   *   An optional Editor entity which the Medium Editor is attached to.
   */
  public function alterEditorJS(array &$data, MediumEditor $medium_editor, Editor $editor = NULL);

  /**
   * Alters the toolbar widget used in Medium Editor form.
   *
   * @param array $widget
   *   An associative array that holds 'libraries' and 'items' in it.
   */
  public function alterToolbarWidget(array &$widget);

  /**
   * Alters entity form of a Medium Editor.
   */
  public function alterEditorForm(array &$form, FormStateInterface $form_state, MediumEditor $medium_editor);

  /**
   * Validates entity form of a Medium Editor.
   */
  public function validateEditorForm(array &$form, FormStateInterface $form_state, MediumEditor $medium_editor);
}
