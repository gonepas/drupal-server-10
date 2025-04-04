<?php

declare(strict_types=1);

namespace Drupal\jwt_auth_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\jwt_auth_api\Service\JwtAuthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for JWT Auth API routes.
 */
final class JwtAuthController extends ControllerBase {

  /**
   * The JWT auth service.
   *
   * @var \Drupal\jwt_auth_api\Service\JwtAuthService
   */
  protected JwtAuthService $jwtAuthService;

  /**
   * Constructs a JwtAuthController object.
   *
   * @param \Drupal\jwt_auth_api\Service\JwtAuthService $jwt_auth_service
   *   The JWT auth service.
   */
  public function __construct(JwtAuthService $jwt_auth_service) {
    $this->jwtAuthService = $jwt_auth_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create($container) {
    return new static(
      $container->get('jwt_auth_api.auth_service')
    );
  }



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
   * Login endpoint.
   *
   * @param  Symfony\Component\HttpFoundation\Request  $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response.
   */
  public function login(Request $request = null): JsonResponse {
    $content = json_decode($request->getContent(), TRUE);

    // Check if username and password are provided.
    if (empty($content['username']) || empty($content['password'])) {
      return new JsonResponse([
        'message' => 'Username and password are required.',
      ], 400);
    }

    // Authenticate the user.
    $tokens = $this->jwtAuthService->authenticate($content['username'], $content['password']);

    if (!$tokens) {
      return new JsonResponse([
        'message' => 'Invalid credentials.',
      ], 401);
    }

    return new JsonResponse($tokens);
  }

  /**
   * Refresh token endpoint.
   *
   * @param  \http\Client\Request  $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response.
   */
  public function refresh(Request $request): JsonResponse {
    $content = json_decode($request->getContent(), TRUE);

    // Check if refresh token is provided.
    if (empty($content['refresh_token'])) {
      return new JsonResponse([
        'message' => 'Refresh token is required.',
      ], 400);
    }

    // Refresh the token.
    $tokens = $this->jwtAuthService->refreshToken($content['refresh_token']);

    if (!$tokens) {
      return new JsonResponse([
        'message' => 'Invalid or expired refresh token.',
      ], 401);
    }

    return new JsonResponse($tokens);
  }

  public function posttest($request = null): JsonResponse {
    $requestz = \Drupal::requestStack()->getCurrentRequest()->query->all();
    $content = json_decode($request->getContent(), TRUE);

    return new JsonResponse();
  }

  public function gettest(): JsonResponse {
    return new JsonResponse();
  }

}
