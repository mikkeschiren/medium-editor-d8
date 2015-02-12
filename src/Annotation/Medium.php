<?php

/**
 * @file
 * Contains Drupal\medium\Annotation\MediumPlugin.
 */

namespace Drupal\medium\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a MediumPlugin annotation object.
 *
 * Plugin Namespace: Plugin\MediumPlugin
 *
 * @see \Drupal\medium\MediumPluginBase
 *
 * @Annotation
 */
class MediumPlugin extends Plugin {

  /**
   * Plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * Plugin label.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

  /**
   * Plugin weight.
   *
   * @var int
   */
  public $weight = 0;

}
