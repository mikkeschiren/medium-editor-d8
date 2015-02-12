<?php

/**
 * @file
 * Contains \Drupal\medium\Entity\MediumButton.
 */

namespace Drupal\medium\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the Medium Button entity.
 *
 * @ConfigEntityType(
 *   id = "medium_button",
 *   label = @Translation("Medium Button"),
 *   handlers = {
 *     "list_builder" = "Drupal\medium\MediumButtonListBuilder",
 *     "form" = {
 *       "add" = "Drupal\medium\Form\MediumButtonForm",
 *       "edit" = "Drupal\medium\Form\MediumButtonForm",
 *       "delete" = "Drupal\medium\Form\MediumButtonDeleteForm",
 *       "duplicate" = "Drupal\medium\Form\MediumButtonForm"
 *     }
 *   },
 *   admin_permission = "administer medium",
 *   config_prefix = "button",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/content/medium/buttons/{medium_button}",
 *     "delete-form" = "/admin/config/content/medium/buttons/{medium_button}/delete",
 *     "duplicate-form" = "/admin/config/content/medium/buttons/{medium_button}/duplicate"
 *   }
 * )
 */
class MediumButton extends ConfigEntityBase {

  /**
   * Button ID.
   *
   * @var string
   */
  protected $id;

  /**
   * Button label.
   *
   * @var string
   */
  protected $label;

  /**
   * Button tooltip.
   *
   * @var string
   */
  protected $tooltip;

  /**
   * Button text.
   *
   * @var string
   */
  protected $text;

  /**
   * Class name.
   *
   * @var string
   */
  protected $cname;

  /**
   * Shortcut.
   *
   * @var string
   */
  protected $shortcut;

  /**
   * Code to insert into text area.
   *
   * @var string
   */
  protected $code;

  /**
   * Template html to insert into editor UI.
   *
   * @var string
   */
  protected $template;

  /**
   * Required libraries.
   *
   * @var array
   */
  protected $libraries = array();

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    // Add id prefix.
    $id = $this->id();
    if (isset($id) && strpos($id, 'custom_') !== 0) {
      $this->set('id', 'custom_' . $id);
    }
  }

  /**
   * Returns an array of button properties for JS.
   */
  public function jsProperties() {
    $props = array('id', 'label', 'tooltip', 'text', 'cname', 'shortcut', 'code', 'template');
    $data = array();
    foreach ($props as $prop) {
      if ($value = $this->get($prop)) {
        $data[$prop] = $value;
      }
    }
    return $data;
  }

}
