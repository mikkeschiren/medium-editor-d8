<?php

/**
 * @file
 * Contains \Drupal\medium\Form\MediumForm.
 */

namespace Drupal\medium\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\String;

/**
 * Base form for Medium entities.
 */
class MediumForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $medium_editor = $this->getEntity();
    // Check duplication
    if ($this->getOperation() === 'duplicate') {
      $medium_editor = $medium_editor->createDuplicate();
      $medium_editor->set('label', $this->t('Duplicate of !label', array('!label' => $medium_editor->label())));
      $this->setEntity($medium_editor);
    }
    // Label
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $medium_editor->label(),
      '#maxlength' => 64,
      '#required' => TRUE,
    );
    // Id
    $form['id'] = array(
      '#type' => 'machine_name',
      '#machine_name' => array(
        'exists' => array(get_class($medium_editor), 'load'),
        'source' => array('label'),
      ),
      '#default_value' => $medium_editor->id(),
      '#maxlength' => 32,
      '#required' => TRUE,
    );
    // Description
    $form['description'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $medium_editor->get('description'),
    );
    // Delay
    $form['delay'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Delay'),
      '#default_value' => $medium_editor->get('delay'),
    );
    // Diffleft
    $form['diffleft'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Diffleft'),
      '#default_value' => $medium_editor->get('diffleft'),
    );
    // Diffright
    $form['difftop'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Difftop'),
      '#default_value' => $medium_editor->get('difftop'),
    );
    // Settings
    $form['settings'] = array('#tree' => TRUE);

    $widget_libraries = $widget['libraries'];
    unset($widget['libraries']);
    $form['settings']['toolbar_config'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Toolbar configuration'),
      '#attached' => array(
        'library' => $widget_libraries,
        'drupalSettings' => array('medium' => array('twSettings' => $widget)),
      ),
    );
    $form['settings']['toolbar_config']['toolbar'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Active toolbar'),
      '#default_value' => implode(', ', $medium_editor->getToolbar()),
      '#attributes' => array(
        'class' => array('medium-toolbar-input'),
      ),
      '#maxlength' => NULL,
      '#parents' => array('settings', 'toolbar')
    );
    // Add demo
    if (!$medium_editor->isNew()) {
      $formats = array();
      foreach (filter_formats(\Drupal::currentUser()) as $format) {
        $formats[] = '<option value="' . String::checkPlain($format->id()) . '">' . String::checkPlain($format->label()) . '</option>';
      }
      $form['demo']['#markup'] = '<div class="form-item form-type-textarea medium-demo"><label>' . $this->t('Demo') . '</label><textarea class="form-textarea" cols="40" rows="5"></textarea><div class="form-item form-type-select filter-wrapper"><span class="label">' . $this->t('Text format') . '</span> <select class="filter-list form-select">' . implode('', $formats) . '</select></div></div>';
      $form['demo']['#weight'] = 1000;
      $form['demo']['#attached']['library'] = $medium_editor->getLibraries();
      $form['demo']['#attached']['drupalSettings']['medium']['demoSettings'] = $medium_editor->getJSSettings();
    }
    // Add admin library
    $form['#attached']['library'][] = 'medium/drupal.medium.admin';
    // Allow plugins to add their elements
    \Drupal::service('plugin.manager.medium.plugin')->alterEditorForm($form, $form_state, $medium_editor);
    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, FormStateInterface $form_state) {
    parent::validate($form, $form_state);
    $toolbar = &$form_state->getValue(array('settings', 'toolbar'));
    // Convert toolbar to array.
    if (is_string($toolbar)) {
      $toolbar = array_values(array_filter(array_map('trim', explode(',', $toolbar))));
    }
    \Drupal::service('plugin.manager.medium.plugin')->validateEditorForm($form, $form_state, $this->getEntity());
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $medium_editor = $this->getEntity();
    $status = $medium_editor->save();
    if ($status == SAVED_NEW) {
      drupal_set_message($this->t('Editor %name has been added.', array('%name' => $medium_editor->label())));
    }
    elseif ($status == SAVED_UPDATED) {
      drupal_set_message($this->t('The changes have been saved.'));
    }
    $form_state->setRedirect('entity.medium_editor.edit_form', array('medium_editor' => $medium_editor->id()));
  }

}
