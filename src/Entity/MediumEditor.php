<?php

/**
 * @file
 * Contains \Drupal\medium\Entity\MediumEditor.
 */

namespace Drupal\medium\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the Medium Editor entity.
 *
 * @ConfigEntityType(
 *   id = "medium_editor",
 *   label = @Translation("Medium Editor"),
 *   handlers = {
 *     "list_builder" = "Drupal\medium\MediumEditorListBuilder",
 *     "form" = {
 *       "add" = "Drupal\medium\Form\MediumEditorForm",
 *       "edit" = "Drupal\medium\Form\MediumEditorForm",
 *       "delete" = "Drupal\medium\Form\MediumEditorDeleteForm",
 *       "duplicate" = "Drupal\medium\Form\MediumEditorForm"
 *     }
 *   },
 *   admin_permission = "administer medium",
 *   config_prefix = "editor",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/content/medium/{medium_editor}",
 *     "delete-form" = "/admin/config/content/medium/{medium_editor}/delete",
 *     "duplicate-form" = "/admin/config/content/medium/{medium_editor}/duplicate"
 *   }
 * )
 */
class MediumEditor extends ConfigEntityBase {

  /**
   * Editor ID.
   *
   * @var string
   */
  protected $id;

  /**
   * Label.
   *
   * @var string
   */
  protected $label;

  /**
   * Description.
   *
   * @var string
   */
  protected $description;

  /**
   * Settings.
   *
   * @var array
   */
  protected $settings = array();

  /**
   * Javascript data including settings and libraries.
   *
   * @var array
   */
  protected $jsData;

  /**
   * Returns all settings or a specific setting by key.
   */
  public function getSettings($key = NULL, $default = NULL) {
    $settings = $this->settings;
    if (isset($key)) {
      return isset($settings[$key]) ? $settings[$key] : $default;
    }
    return $settings;
  }

  /**
   * Returns the toolbar array.
   */
  public function getToolbar() {
    return $this->getSettings('toolbar', array());
  }

  /**
   * Checks if an item exists in the toolbar.
   */
  public function hasToolbarItem($id) {
    return in_array($id, $this->getToolbar(), TRUE);
  }

  /**
   * Returns JS libraries.
   */
  public function getLibraries(Editor $editor = NULL) {
    $data = $this->getJSData($editor);
    return $data['libraries'];
  }

  /**
   * Returns JS settings.
   */
  public function getJSSettings(Editor $editor = NULL) {
    $data = $this->getJSData($editor);
    return $data['settings'];
  }

  /**
   * Returns JS data including settings and libraries.
   */
  public function getJSData(Editor $editor = NULL) {
    if (!isset($this->jsData)) {
      $this->jsData = array(
        'libraries' => array('medium/drupal.medium'),
        'settings' => array_filter($this->getSettings()) + array('toolbar' => array()),
      );
      \Drupal::service('plugin.manager.medium.plugin')->alterEditorJS($this->jsData, $this, $editor);
    }
    return $this->jsData;
  }

}
