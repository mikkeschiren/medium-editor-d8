<?php

/**
 * @file
 * Contains \Drupal\medium\Controller\MediumImageController.
 */

namespace Drupal\medium\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\editor\Entity\Editor;
use Drupal\Core\Form\FormStateInterface;

/**
 * Controller routines for medium routes.
 */
class MediumImageController extends ControllerBase {

  public function modal() {

 //   $form['image_upload'] = editor_image_upload_settings_form('Medium');

    $form = \Drupal::formBuilder()->getForm('Drupal\editor\Form\EditorImageDialog::buildForm');
    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\editor\Form\EditorImageDialog
   * @see editor_image_upload_settings_form()
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $form_state->loadInclude('editor', 'admin.inc');
    $form['image_upload'] = editor_image_upload_settings_form($editor);
    return $form;
  }
}
