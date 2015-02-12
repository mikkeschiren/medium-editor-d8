<?php

/**
 * @file
 * Contains \Drupal\medium\MediumToolbarWrapper.
 */

namespace Drupal\medium;

/**
 * Defines a class that manages Medium toolbar data.
 */
class MediumToolbarWrapper {

  /**
   * Singleton instance.
   */
  protected static $instance;

  /**
   * Toolbar items.
   *
   * @var array
   */
  protected $toolbar;

  /**
   * Associative array of unique toolbar items.
   *
   * @var array
   */
  protected $assocToolbar;

  /**
   * Creates the singleton with the given toolbar reference.
   */
  public static function set(array &$toolbar) {
    if (!static::$instance) {
      static::$instance = new static();
    }
    return static::$instance->_set($toolbar);
  }

  /**
   * Sets the toolbar reference.
   */
  public function _set(array &$toolbar) {
    if ($this->toolbar !== $toolbar) {
      $this->assocToolbar = array_combine($toolbar, $toolbar);
    }
    $this->toolbar = &$toolbar;
    return $this;
  }

  /**
   * Checks the existence of an item.
   */
  public function has($id) {
    return isset($this->assocToolbar[$id]);
  }

  /**
   * Checks if any of the given items exists.
   */
  public function hasAnyOf(array $ids) {
    foreach ($ids as $id) {
      if (isset($this->assocToolbar[$id])) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Returns items that start with a string or match a regex.
   */
  public function match($str, $is_regex = FALSE) {
    $items = array();
    if ($this->assocToolbar) {
      foreach ($this->assocToolbar as $id) {
        $found = $is_regex ? preg_match($str, $id) : strpos($id, $str) === 0;
        if ($found) {
          $items[$id] = $id;
        }
      }
    }
    return $items;
  }

  /**
   * Removes an item.
   */
  public function remove($id) {
    if (isset($this->assocToolbar[$id])) {
      $this->toolbar = array_diff($this->toolbar, array($id));
      unset($this->assocToolbar[$id]);
    }
    return $this;
  }

}
