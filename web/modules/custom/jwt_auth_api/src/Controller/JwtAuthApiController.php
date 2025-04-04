<?php

declare(strict_types=1);

namespace Drupal\jwt_auth_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for JWT Auth API routes.
 */
final class JwtAuthApiController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {
    $requestz = \Drupal::requestStack()->getCurrentRequest()->query->all();

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

  public function posttest(Request $request = null): JsonResponse {
    $requestz = \Drupal::requestStack()->getCurrentRequest();
    $query = \Drupal::request()->query;
    $content = json_decode($request->getContent(), TRUE);

    return new JsonResponse();
  }

  public function gettest(Request $request = null): JsonResponse {
    $requestz = \Drupal::requestStack()->getCurrentRequest();
    return new JsonResponse();
  }




}
