<?php

/**
 * @file
 * Contains \Drupal\medium\Plugin\Editor\MediumEditor.
 */

namespace Drupal\medium\Plugin\Editor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\editor\Entity\Editor;
use Drupal\editor\Plugin\EditorBase;
use Drupal\medium\Entity\Medium;

/**
 * Defines Medium as an Editor plugin.
 *
 * @Editor(
 *   id = "medium",
 *   label = "MediumEditor",
 *   supports_content_filtering = FALSE,
 *   supports_inline_editing = FALSE,
 *   is_xss_safe = TRUE
 * )
 */
class MediumEditor extends EditorBase {

  /**
   * {@inheritdoc}
   */
  public function getDefaultSettings() {
    $settings['default_editor'] = '';
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $settings = $editor->getSettings();
    $medium_editors = array();
    foreach (entity_load_multiple('medium_editor') as $medium_editor) {
      $medium_editors[$medium_editor->id()] = $medium_editor->label();
    }
    // Default editor
    $form['default_editor'] = array(
      '#type' => 'select',
      '#title' => $this->t('Medium Editor'),
      '#options' => $medium_editors,
      '#default_value' => $settings['default_editor'],
      '#description' => $this->t('Select the default editor for the authorized roles. Editors can be configured at <a href="!url">Medium admin page</a>.', array('!url' => \Drupal::url('medium.admin'))),
      '#empty_option' => '- ' . $this->t('Select an editor') . ' -',
    );
    // Roles editors
    $role_ids = array();
    if ($format_form = $form_state->getCompleteForm()) {
      if (isset($format_form['roles']['#value'])) {
        $role_ids = $format_form['roles']['#value'];
      }
      elseif (isset($format_form['roles']['#default_value'])) {
        $role_ids = $format_form['roles']['#default_value'];
      }
    }
    elseif ($format = $editor->getFilterFormat()) {
      $role_ids = array_keys(filter_get_roles_by_format($format));
    }
    if (count($role_ids) > 1) {
      $form['roles_editors'] = array(
        '#type' => 'details',
        '#title' => t('Role specific editors'),
      );
      $roles = user_roles();
      foreach ($role_ids as $role_id) {
        $form['roles_editors'][$role_id] = array(
          '#type' => 'select',
          '#title' => $this->t('Editor for %role', array('%role' => $roles[$role_id]->label())),
          '#options' => $medium_editors,
          '#default_value' => isset($settings['roles_editors'][$role_id]) ? $settings['roles_editors'][$role_id] : '',
          '#empty_option' => '- ' . $this->t('Use the default') . ' -',
        );
      }
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsFormValidate(array $form, FormStateInterface $form_state) {
    $settings = &$form_state->getValue(array('editor', 'settings'));
    // Remove empty role editor pairs.
    if (isset($settings['roles_editors'])) {
      $settings['roles_editors'] = array_filter($settings['roles_editors']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    $medium_editor = $this->getMedium($editor);
    return $medium_editor ? $medium_editor->getLibraries($editor) : array();
  }

  /**
   * {@inheritdoc}
   */
  public function getJSSettings(Editor $editor) {
    $medium_editor = $this->getMedium($editor);
    return $medium_editor ? $medium_editor->getJSSettings($editor) : array();
  }

  /**
   * Returns the selected Medium Editor entity for an account from editor settings.
   */
  public static function getMedium(Editor $editor, AccountInterface $account = NULL) {
    if (!isset($account)) {
      $account = \Drupal::currentUser();
    }
    $id = static::getMediumId($editor, $account);
    return $id ? entity_load('medium_editor', $id) : FALSE;
  }

  /**
   * Returns the selected Medium Editor id for an account from editor settings.
   */
  public static function getMediumId(Editor $editor, AccountInterface $account) {
    $settings = $editor->getSettings();
    if (!empty($settings['roles_editors'])) {
      // Filter roles in two steps. May avoid a db hit by filter_get_roles_by_format().
      if ($roles_editors = array_intersect_key($settings['roles_editors'], array_flip($account->getRoles()))) {
        if ($roles_editors = array_intersect_key($roles_editors, filter_get_roles_by_format($editor->getFilterFormat()))) {
          return reset($roles_editors);
        }
      }
    }
    return $settings['default_editor'];
  }

}
