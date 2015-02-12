<?php

/**
 * @file
 * Contains \Drupal\medium\Form\MediumSettingsForm.
 */

namespace Drupal\medium\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Medium settings form.
 */
class MediumSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'medium_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return array('medium.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('medium.settings');
    $form['devmode'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable development mode'),
      '#default_value' => $config->get('devmode'),
      '#description' => t('In development mode minified libraries are replaced by source libraries to make debugging easier.'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('medium.settings');
    // Invalidate library cache if devmode has changed.
    $devmode = $form_state->getValue('devmode');
    if ($config->get('devmode') != $devmode) {
      \Drupal::cache('discovery')->invalidate('library_info');
    }
    // Save config
    $config->set('devmode', $devmode)->save();
    parent::submitForm($form, $form_state);
  }

}
