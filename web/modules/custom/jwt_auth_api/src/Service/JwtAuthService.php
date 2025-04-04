<?php

declare(strict_types=1);

namespace Drupal\jwt_auth_api\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Password\PasswordInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\jwt\Authentication\Provider\JwtAuth;
use Drupal\jwt\JsonWebToken\JsonWebToken;
use Drupal\jwt\Transcoder\JwtTranscoderInterface;
use Firebase\JWT\JWT;

/**
 * @todo Add class description.
 */
final class JwtAuthService {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The password service.
   *
   * @var \Drupal\Core\Password\PasswordInterface
   */
  protected $password;

  /**
   * The JWT authentication service.
   *
   * @var \Drupal\jwt\Authentication\Provider\JwtAuth
   */
  protected $jwtAuth;

  /**
   * The JWT transcoder service.
   *
   * @var \Drupal\jwt\Transcoder\JwtTranscoderInterface
   */
  protected $jwtTranscoder;

  /**
   * The time service.
   *
   * @var Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Constructs a JwtAuthService object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Password\PasswordInterface $password
   *   The password service.
   * @param \Drupal\jwt\Authentication\Provider\JwtAuth $jwt_auth
   *   The JWT authentication service.
   * @param \Drupal\jwt\Transcoder\JwtTranscoderInterface $jwt_transcoder
   *   The JWT transcoder service.
   * @param Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    PasswordInterface $password,
    JwtAuth $jwt_auth,
    JwtTranscoderInterface $jwt_transcoder,
    TimeInterface $time
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->password = $password;
    $this->jwtAuth = $jwt_auth;
    $this->jwtTranscoder = $jwt_transcoder;
    $this->time = $time;
  }

  /**
   * Authenticates a user and returns JWT tokens.
   *
   * @param string $username
   *   The username or email.
   * @param string $password
   *   The password.
   *
   * @return array|null
   *   An array containing the JWT access token and refresh token or NULL.
   */
  public function authenticate($username, $password) {
    // Try to load user by username.
    $user_storage = $this->entityTypeManager->getStorage('user');
    $users = $user_storage->loadByProperties(['name' => $username]);
    $user = reset($users);

    // If not found, try by email.
    if (!$user) {
      $users = $user_storage->loadByProperties(['mail' => $username]);
      $user = reset($users);
    }

    // Check if user exists and password is valid.
    if (!$user || !$this->password->check($password, $user->getPassword())) {
      return NULL;
    }

    // Check if user is active.
    if (!$user->isActive()) {
      return NULL;
    }

    // Generate tokens.
    return $this->generateTokens($user->id());
  }

  /**
   * Refreshes an access token using a refresh token.
   *
   * @param string $refresh_token
   *   The refresh token.
   *
   * @return array|null
   *   An array containing the new JWT access token and refresh token or NULL.
   */
  public function refreshToken($refresh_token) {
    try {
      // Decode the refresh token.
      $decoded = $this->jwtTranscoder->decode($refresh_token);

      // Check if it's a valid refresh token.
      if (empty($decoded['jti']) || $decoded['type'] !== 'refresh') {
        return NULL;
      }

      // Check if token has expired.
      if (empty($decoded['exp']) || $decoded['exp'] < $this->time->getRequestTime()) {
        return NULL;
      }

      // Check if user ID exists.
      if (empty($decoded['uid'])) {
        return NULL;
      }

      // Load the user and check if active.
      $user = $this->entityTypeManager->getStorage('user')->load($decoded['uid']);
      if (!$user || !$user->isActive()) {
        return NULL;
      }

      // Generate new tokens.
      return $this->generateTokens($user->id());
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Generates access and refresh tokens for a user.
   *
   * @param int $uid
   *   The user ID.
   *
   * @return array
   *   An array containing the JWT access token and refresh token.
   */
  protected function generateTokens($uid) {
    // Get token expiration settings.
    $access_token_expiration = \Drupal::config('jwt.config')->get('jwt_expiration') ?? 3600;
    $refresh_token_expiration = \Drupal::config('jwt.config')->get('jwt_refresh_token_expiration') ?? 1209600;

    $current_time = $this->time->getRequestTime();

    // Generate access token.
    $access_token_payload = [
      'iat' => $current_time,
      'exp' => $current_time + $access_token_expiration,
      'uid' => $uid,
      'type' => 'access',
      'jti' => uniqid('', TRUE),
    ];

    // Generate refresh token.
    $refresh_token_payload = [
      'iat' => $current_time,
      'exp' => $current_time + $refresh_token_expiration,
      'uid' => $uid,
      'type' => 'refresh',
      'jti' => uniqid('', TRUE),
    ];

    // encode access token
    $access_token = new JsonWebToken();
    $access_token->setClaim('iat', $current_time);
    $access_token->setClaim('exp', $current_time + $access_token_expiration);
    $access_token->setClaim('uid', $uid);
    $access_token->setClaim('type', 'access');
    $access_token->setClaim('jti', uniqid('', TRUE));
    $access_token_encode = $this->jwtTranscoder->encode($access_token);

    // encode refresh token
    $refresh_token = new JsonWebToken();
    $refresh_token->setClaim('iat', $current_time);
    $refresh_token->setClaim('exp', $current_time + $refresh_token_expiration);
    $refresh_token->setClaim('uid', $uid);
    $refresh_token->setClaim('type', 'refresh');
    $refresh_token->setClaim('jti', uniqid('', TRUE));
    $refresh_token_encode = $this->jwtTranscoder->encode($refresh_token);

    return [
      'access_token' => $access_token_encode,
      'refresh_token' => $refresh_token_encode,
      'expires_in' => $access_token_expiration,
    ];
  }

  /**
   * Validates a JWT token and returns the user ID.
   *
   * @param string $token
   *   The JWT token.
   *
   * @return int|null
   *   The user ID or NULL if the token is invalid.
   */
  public function validateToken($token) {
    try {
      // Decode the token.
      $decoded = $this->jwtTranscoder->decode($token);
      $decoded_payload = $decoded->getPayload();

      // Check if it's a valid access token.
      if (empty($decoded_payload['jti']) || $decoded_payload['type'] !== 'access') {
        return NULL;
      }

      // Check if token has expired.
      if (empty($decoded_payload['exp']) || $decoded_payload['exp'] < $this->time->getRequestTime()) {
        return NULL;
      }

      // Return the user ID.
      return !empty($decoded_payload['uid']) ? $decoded_payload['uid'] : NULL;
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

}
