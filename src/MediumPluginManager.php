<?php

/**
 * @file
 * Contains \Drupal\medium\MediumPluginManager.
 */

namespace Drupal\medium;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;
use Drupal\medium\Entity\Medium;

/**
 * Provides a plugin manager for Medium Plugins.
 *
 * @see \Drupal\medium\MediumPluginInterface
 * @see \Drupal\medium\MediumPluginBase
 * @see \Drupal\medium\Annotation\MediumPlugin
 * @see plugin_api
 */
class MediumPluginManager extends DefaultPluginManager {

  /**
   * Available plugin hooks.
   *
   * @var array
   */
  protected $hooks;

  /**
   * Available plugin instances.
   *
   * @var array
   */
  public $instances;

  /**
   * Constructs a MediumPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/MediumPlugin', $namespaces, $module_handler, 'Drupal\medium\MediumPluginInterface', 'Drupal\medium\Annotation\MediumPlugin');
    $this->alterInfo('medium_plugin_info');
    $this->setCacheBackend($cache_backend, 'medium_plugins');
  }

  /**
   * Returns available plugin instances.
   *
   * @return array
   *   A an array plugin intances.
   */
  public function getInstances() {
    if (!isset($this->instances)) {
      $this->instances = array();
      $definitions = $this->getDefinitions();
      uasort($definitions, array('Drupal\Component\Utility\SortArray', 'sortByWeightElement'));
      foreach ($definitions as $plugin => $def) {
        $this->instances[$plugin] = $this->createInstance($plugin);
      }
    }
    return $this->instances;
  }

  /**
   * Returns available hooks.
   *
   * @return array
   *   An array of method names defined by plugin interface.
   */
  public function getHooks() {
    if (!isset($this->hooks)) {
      $this->hooks = get_class_methods('Drupal\medium\MediumPluginInterface');
    }
    return $this->hooks;
  }

  /**
   * Invokes a hook in all available plugins.
   *
   * @return array
   *   An array of results keyed by plugin id.
   */
  public function invokeAll($hook, &$a = NULL, $b = NULL, $c = NULL) {
    $ret = array();
    if (in_array($hook, $this->getHooks())) {
      foreach ($this->getInstances() as $plugin => $instance) {
        $ret[$plugin] = $instance->$hook($a, $b, $c);
      }
    }
    return $ret;
  }

  /**
   * Alters javascript data of a Medium Editor entity.
   */
  public function alterEditorJS(array &$data, Medium $medium_editor, Editor $editor = NULL) {
    return $this->invokeAll('alterEditorJS', $data, $medium_editor, $editor);
  }

  /**
   * Alters a toolbar widget
   */
  public function alterToolbarWidget(array &$widget) {
    return $this->invokeAll('alterToolbarWidget', $widget);
  }

  /**
   * Alters a Medium Editor form.
   */
  public function alterEditorForm(array &$form, FormStateInterface $form_state, Medium $medium_editor) {
    return $this->invokeAll('alterEditorForm', $form, $form_state, $medium_editor);
  }

  /**
   * Validates a Medium Editor form.
   */
  public function validateEditorForm(array &$form, FormStateInterface $form_state, Medium $medium_editor) {
    return $this->invokeAll('validateEditorForm', $form, $form_state, $medium_editor);
  }
}
