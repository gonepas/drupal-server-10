<?php

declare(strict_types=1);

namespace Drupal\jwt_auth_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for JWT Auth API routes.
 */
final class UserController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

  /**
   * Returns information about the authenticated user.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response.
   */
  public function info(): JsonResponse {
    $user = $this->currentUser();

    return new JsonResponse([
      'uid' => $user->id(),
      'name' => $user->getAccountName(),
      'email' => $user->getEmail(),
      'roles' => $user->getRoles(),
    ]);
  }

}
