<?php

/**
 * @file
 * Contains \Drupal\medium\Controller\MediumController.
 */

namespace Drupal\medium\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller routines for medium routes.
 */
class MediumController extends ControllerBase {

  /**
   * Returns an administrative overview of Medium Editors.
   */
  public function adminOverview() {
    $output['editors'] = array(
      '#type' => 'container',
      '#attributes' => array('class' => array('medium-editor-list')),
      'title' => array('#markup' => '<h2>' . $this->t('Available editors') . '</h2>'),
      'list' => $this->entityManager()->getListBuilder('medium_editor')->render(),
    );
    $output['#attached']['library'][] = 'medium/drupal.medium.admin';
    return $output;
  }
}
