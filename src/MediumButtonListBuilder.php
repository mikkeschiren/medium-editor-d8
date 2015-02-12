<?php

/**
 * @file
 * Contains \Drupal\medium\MediumButtonListBuilder.
 */

namespace Drupal\medium;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines a class to build a list of Medium Button entities.
 *
 * @see \Drupal\medium\Entity\MediumButton
 */
class MediumButtonListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = array('data' => $this->t('ID'), 'class' => 'button-id');
    $header['label'] = array('data' => $this->t('Name'), 'class' => 'button-label');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $bueditor_button) {
    $row['id'] = $bueditor_button->id();
    $row['label'] = $bueditor_button->label();
    return $row + parent::buildRow($bueditor_button);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $bueditor_button) {
    $operations = parent::getDefaultOperations($bueditor_button);
    $operations['duplicate'] = array(
      'title' => t('Duplicate'),
      'weight' => 15,
      'url' => $bueditor_button->urlInfo('duplicate-form'),
    );
    return $operations;
  }

}
