<?php

declare(strict_types=1);

namespace Drupal\jwt_auth_api\Authentication\Provider;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Authentication\AuthenticationProviderInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\jwt_auth_api\Service\JwtAuthService;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines a service provider for the Jwt auth api module.
 *
 * @see https://www.drupal.org/node/2026959
 */
final class JwtAuthApiProvider implements AuthenticationProviderInterface {

  /**
   * The service container.
   *
   * @var \Drupal\Component\DependencyInjection\ContainerInterface
   */
  protected \Drupal\Component\DependencyInjection\ContainerInterface $container;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a JwtAuthApiProvider object.
   *
   * @param  \Drupal\Core\Entity\EntityTypeManagerInterface  $entity_type_manager
   *   The entity type manager.
   * @param  \Drupal\Component\DependencyInjection\ContainerInterface  $container
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    ContainerInterface $container
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->container = $container;
  }

  /**
   * Gets the JWT auth service.
   *
   * @return \Drupal\jwt_auth_api\Service\JwtAuthService
   *   The JWT auth service.
   */
  protected function getJwtAuthService(): JwtAuthService {
    return $this->container->get('jwt_auth_api.auth_service');
  }

  /**
   * {@inheritdoc}
   */
  public function applies(Request $request): bool {
    // Check if the request has an Authorization header with Bearer token.
    $auth = $request->headers->get('Authorization');
    return (bool) preg_match('/^Bearer\s.+/', $auth ?? '');
  }

  /**
   * {@inheritdoc}
   */
  public function authenticate(Request $request): \Drupal\Core\Entity\EntityInterface|\Drupal\Core\Session\AccountInterface|null {
    // Extract the token from the Authorization header.
    $auth_header = $request->headers->get('Authorization');
    preg_match('/^Bearer\s+(.*)$/', $auth_header, $matches);

    if (!isset($matches[1])) {
      return NULL;
    }

    $token = $matches[1];

    // Validate the token and get the user ID.
    $uid = $this->getJwtAuthService()->validateToken($token);

    if (!$uid) {
      return NULL;
    }

    // Load the user.
    $user = $this->entityTypeManager->getStorage('user')->load($uid);

    return $user && $user->isActive() ? $user : NULL;
  }


}
