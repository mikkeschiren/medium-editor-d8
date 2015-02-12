<?php

/**
 * @file
 * Contains \Drupal\medium\Form\MediumButtonForm.
 */

namespace Drupal\medium\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\String;

/**
 * Base form for Medium Button entities.
 */
class MediumButtonForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $medium_button = $this->getEntity();
    // Check duplication
    if ($this->getOperation() === 'duplicate') {
      $medium_button = $medium_button->createDuplicate();
      $medium_button->set('label', $this->t('Duplicate of !label', array('!label' => $medium_button->label())));
      $this->setEntity($medium_button);
    }
    // Label
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $medium_button->label(),
      '#maxlength' => 64,
      '#required' => TRUE,
    );
    // Id
    $id = $medium_button->id();
    $form['id'] = array(
      '#type' => 'machine_name',
      '#machine_name' => array(
        'exists' => array(get_class($medium_button), 'load'),
        'source' => array('label'),
      ),
      '#default_value' => $id && strpos($id, 'custom_') === 0 ? substr($id, 7) : $id,
      '#maxlength' => 32,
      '#required' => TRUE,
      '#field_prefix' => 'custom_',
    );
    // Template button
    $code = $medium_button->get('code');
    $template = $medium_button->get('template');
    $js_info = $this->t('If the code starts with <code>js:</code> it is executed as javascript inside <code>function(E, $){...}</code> where <code>E</code> is the editor instance, and <code>$</code> is JQuery. Ex: <code>js: console.log(this, E, $);</code>');
    $template_checked = array(':input[name="is_template"]' => array('checked' => TRUE));
    $template_unchecked = array(':input[name="is_template"]' => array('checked' => FALSE));
    $form['is_template'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('This is a template button'),
      '#default_value' => $template && !$code,
    );
    $form['template_button'] = array(
      '#type' => 'details',
      '#title' => $this->t('Template button'),
      '#description' => $this->t('A template button is used for inserting a custom element into toolbar.'),
      '#open' => TRUE,
      '#states' => array(
        'visible' => $template_checked,
      ),
    );
    // Template
    $form['template_button']['template'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Template'),
      '#default_value' => $template,
      '#description' => $this->t('Html template that will be inserted into toolbar.') . '<br />' . $js_info,
      '#states' => array(
        'required' => $template_checked,
      ),
    );
    // Normal button
    $form['button'] = array(
      '#type' => 'details',
      '#title' => $this->t('Button properties'),
      '#open' => TRUE,
      '#states' => array(
        'visible' => $template_unchecked,
      ),
    );
    // Code
    $form['button']['code'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Code'),
      '#default_value' => $code,
      '#description' => $this->t('Text to insert into editor textarea. Ex: <code>&lt;strong&gt;|&lt;/strong&gt;</code>. The vertical bar <strong>|</strong> represents the cursor position or the selected text in the textarea.') . '<br />' . $js_info,
      '#states' => array(
        'required' => $template_unchecked,
      ),
    );
    // Button text
    $form['button']['text'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Button text'),
      '#default_value' => $medium_button->get('text'),
      '#description' => $this->t('A text label or html icon for the button element.'),
    );
    // Tooltip
    $form['button']['tooltip'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Tooltip'),
      '#default_value' => $medium_button->get('tooltip'),
      '#description' => $this->t('Descriptive text displayed on button hover.'),
    );
    // Class name
    $form['button']['cname'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Class name'),
      '#default_value' => $medium_button->get('cname'),
      '#description' => $this->t('Additional class name for the button element.') . '<br />' . $this->t('Font icon class can be used as <code>ficon-NAME</code> where <code>NAME</code> is one of <em>!names</em>.', array('!names' => 'bold, italic, underline, strike, image, link, quote, code, ul, ol, table, template, undo, redo, preview, help')),
    );
    // Class name
    $form['button']['shortcut'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Shortcut'),
      '#default_value' => $medium_button->get('shortcut'),
      '#description' => $this->t('Button shortcut as a combination of Modifier keys (<kbd>Ctrl</kbd>, <kbd>Alt</kbd>, <kbd>Shift</kbd>) and Alphanumeric keys(<kbd>0-9</kbd>, <kbd>A-Z</kbd>) or special keys like Back space(<kbd>BACKSPACE</kbd>), Tabulator(<kbd>TAB</kbd>), Return(<kbd>ENTER</kbd>), Escape(<kbd>ESC</kbd>), Space(<kbd>SPACE</kbd>), Arrow keys(<kbd>LEFT|RIGHT|UP|DOWN</kbd>), Function keys(<kbd>F1-F12</kbd>).') . '<br />' . $this->t('Example shortcuts: <kbd>Ctrl+M</kbd>, <kbd>Alt+Shift+5</kbd>, <kbd>Ctrl+Shift+ENTER</kbd>.') . '<br />' . $this->t('Make sure not to override default shortcuts like <kbd>Ctrl+A|C|V|X</kbd> which are critical for text editing.'),
    );
    // Libraries
    $form['libraries'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Required libraries'),
      '#default_value' => implode(', ', $medium_button->get('libraries')),
      '#description' => $this->t('Comma separated list of required drupal libraries for the button. Ex: core/drupal.progress, node/drupal.node.preview'),
    );
    // Add demo
    if (!$medium_button->isNew()) {
      $medium_editor = entity_create('medium_editor', array('id' => '_button_demo', 'settings' => array('toolbar' => array($medium_button->id()))));
      $formats = array();
      foreach (filter_formats(\Drupal::currentUser()) as $format) {
        $formats[] = '<option value="' . String::checkPlain($format->id()) . '">' . String::checkPlain($format->label()) . '</option>';
      }
      $form['demo']['#markup'] = '<div class="form-item form-type-textarea medium-demo"><label>' . $this->t('Demo') . '</label><textarea class="form-textarea" cols="40" rows="5"></textarea><div class="form-item form-type-select filter-wrapper"><span class="label">' . $this->t('Text format') . '</span> <select class="filter-list form-select">' . implode('', $formats) . '</select></div></div>';
      $form['demo']['#weight'] = 1000;
      $form['demo']['#attached']['library'] = $medium_editor->getLibraries();
      $form['demo']['#attached']['drupalSettings']['medium']['demoSettings'] = $medium_editor->getJSSettings();
    }
    // Add library
    $form['#attached']['library'][] = 'medium/drupal.medium.admin';
    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, FormStateInterface $form_state) {
    $medium_button = $this->getEntity();
    $values = $form_state->getValues();
    // Add ID prefix
    if (!$form_state->getError($form['id'])) {
      $id = 'custom_' . $values['id'];
      $form_state->setValue('id', $id);
      // Check duplicate
      if ($id != $medium_button->id()) {
        if ($medium_button->load($id)) {
          $form_state->setError($form['id'], $this->t('The machine-readable name is already in use. It must be unique.'));
        }
      }
    }
    // Template button
    if (!empty($values['is_template'])) {
      $form_state->setValue('code', '');
    }
    // Normal button
    else {
      $form_state->setValue('template', '');
      // Check class name
      if (!empty($values['cname']) && preg_match('/[^a-zA-Z0-9\-_ ]/', $values['cname'])) {
        $form_state->setErrorByName('cname', $this->t('!field contains invalid characters.', array('!field' => $this->t('Class name'))));
      }
      // Check shortcut
      if (!empty($values['shortcut']) && preg_match('/[^a-zA-Z0-9\+]/', $values['shortcut'])) {
        $form_state->setErrorByName('shortcut', $this->t('!field contains invalid characters.', array('!field' => $this->t('Shortcut'))));
      }
    }
    // Convert libraries to array.
    if (isset($values['libraries']) && is_string($values['libraries'])) {
      $form_state->setValue('libraries', array_values(array_filter(array_map('trim', explode(',', $values['libraries'])))));
    }
    return parent::validate($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $medium_button = $this->getEntity();
    $status = $medium_button->save();
    if ($status == SAVED_NEW) {
      drupal_set_message($this->t('Button %name has been added.', array('%name' => $medium_button->label())));
    }
    elseif ($status == SAVED_UPDATED) {
      drupal_set_message($this->t('The changes have been saved.'));
    }
    $form_state->setRedirect('entity.medium_button.edit_form', array('medium_button' => $medium_button->id()));
  }

}
