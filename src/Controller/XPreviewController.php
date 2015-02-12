<?php

/**
 * @file
 * Contains \Drupal\medium\Controller\XPreviewController.
 */

namespace Drupal\medium\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Controller\ControllerBase;


/**
 * Controller class for ajax preview path.
 */
class XPreviewController extends ControllerBase {

  /**
   * Handles ajax preview requests.
   */
  public function response(Request $request) {
    $user = $this->currentUser();
    // Check security token for authenticated users.
    if (!$user->isAnonymous()) {
      $token = $request->query->get('token');
      if (!$token || !\Drupal::csrfToken()->validate($token, 'xpreview')) {
        return new JsonResponse(array('output' => $this->t('Invalid security token.'), 'status' => FALSE));
      }
    }
    // Build output
    $data = array('output' => '', 'status' => TRUE);
    // Check input
    $input = $request->request->get('input');
    if (is_string($input) && ($input = trim($input)) !== '') {
      $used_format = filter_fallback_format();
      // Check format
      $format = $request->request->get('format');
      if ($format && is_string($format) && $format !== $used_format) {
        if ($format = entity_load('filter_format', $format)) {
          if ($format->access('use', $user)) {
            $used_format = $format->id();
          }
        }
      }
      $data['usedFormat'] = $used_format;
      $data['output'] = check_markup($input, $used_format);
    }
    return new JsonResponse($data);
  }

}
