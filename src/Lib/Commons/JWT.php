<?php

namespace Websyspro\NestPhp\Lib\Commons
{
  use ArrayAccess;
  use DomainException;
  use InvalidArgumentException;
  use UnexpectedValueException;
  
  class BeforeValidException extends UnexpectedValueException {}
  class ExpiredException extends UnexpectedValueException {}
  class SignatureInvalidException extends UnexpectedValueException{}
  
  class JWT
  {
    public static $leeway = 0;
    public static $timestamp = null;
    public static $supportedAlgs = [
      "HS256" => [ "hash_hmac", "SHA256" ],
      "HS512" => [ "hash_hmac", "SHA512" ],
      "HS384" => [ "hash_hmac", "SHA384" ],
      "RS256" => [ "openssl", "SHA256" ],
      "RS384" => [ "openssl", "SHA384" ],
      "RS512" => [ "openssl", "SHA512" ],
    ];

    private const ALGORITHM_NOT_SUPPORTED = "Algorithm not supported";
    private const WRONG_NUMBER_OF_SEGMENTS = "Wrong number of segments";
    private const KEY_MAY_NOT_BE_EMPTY = "Key may not be empty";
    private const INVALID_HEADER_ENCODING = "Invalid header encoding";
    private const INVALID_CLAIMS_ENCODING = "Invalid claims encoding";
    private const INVALID_SIGNATURE_ENCODING = "Invalid signature encoding";
    private const EMPTY_ALGORITHM = "Empty algorithm";
    private const ALGORITHM_NOT_ALLOWED = "Algorithm not allowed";
    private const KID_INVALID_UNABLE_TO_LOOKUP_CORRECT_KEY = "`kid` invalid, unable to lookup correct key";
    private const KID_EMPTY_UNABLE_TO_LOOKUP_CORRECT_KEY = "`kid` empty, unable to lookup correct key";
    private const SIGNATURE_VERIFICATION_FAILED = "Signature verification failed";
    private const CANNOT_HANDLE_TOKEN_PRIOR_TO = "Cannot handle token prior to";
    private const EXPIRED_TOKEN = "Expired token";
    private const OPENSSL_UNABLE_TO_SIGN_DATA = "OpenSSL unable to sign data";
    private const NULL_RESULT_WITH_NON_NULL_INPUT = "Null result with non-null input";

    private static function validateTokens(
      string $headb64,
      string $bodyb64,
      string $cryptob64,
      string | array $key,
      array $allowed_algs = [],
    ): mixed {
      if (( $header = static::jsonDecode(
        static::urlsafeB64Decode(
          $headb64
        ))) === null ){
        throw new UnexpectedValueException(
          static::INVALID_HEADER_ENCODING
        );
      }

      if (( $payload = static::jsonDecode(
        static::urlsafeB64Decode(
          $bodyb64
        ))) === null ) {
        throw new UnexpectedValueException(
          static::INVALID_CLAIMS_ENCODING
        );
      }

      if (( $sig = static::urlsafeB64Decode(
        $cryptob64
      )) === false ) {
        throw new UnexpectedValueException(
          static::INVALID_SIGNATURE_ENCODING
        );
      }

      if ( empty( $header->alg )) {
        throw new UnexpectedValueException(
          static::EMPTY_ALGORITHM
        );
      }

      if (empty(static::$supportedAlgs[$header->alg])) {
        throw new UnexpectedValueException(
          static::ALGORITHM_NOT_SUPPORTED
        );
      }

      if ( in_array( $header->alg, $allowed_algs ) === false ) {
        throw new UnexpectedValueException(
          static::ALGORITHM_NOT_ALLOWED
        );
      }

      if ( is_array( $key ) || $key instanceof ArrayAccess ) {
        if ( isset( $header->kid )) {
          if ( !isset( $key[ $header->kid ])) {
            throw new UnexpectedValueException(
              static::KID_INVALID_UNABLE_TO_LOOKUP_CORRECT_KEY
            );
          }
          $key = $key[$header->kid];
        } else {
          throw new UnexpectedValueException(
            static::KID_EMPTY_UNABLE_TO_LOOKUP_CORRECT_KEY
          );
        }
      }
  
      if ( static::verify( "{$headb64}.{$bodyb64}", $sig, $key, $header->alg ) === false ) {
        throw new SignatureInvalidException(
          static::SIGNATURE_VERIFICATION_FAILED
        );
      }
      
      return $payload;
    }

    private static function getTokens(
      string $jwt
    ): array {
      return explode(
        ".",
        $jwt
      );
    }
  
    public static function decode(
      string $jwt,
      string | array $key,
      array $allowed_algs = []
    ): object {
      if ( empty( $key )) {
        throw new InvalidArgumentException(
          static::KEY_MAY_NOT_BE_EMPTY
        );
      }

      if ( count( static::getTokens( $jwt )) != 3 ) {
        throw new UnexpectedValueException(
          static::WRONG_NUMBER_OF_SEGMENTS
        );
      }
      
      [ $headb64,
        $bodyb64,
        $cryptob64
      ] = static::getTokens( $jwt );

      $payload = static::validateTokens(
        $headb64,
        $bodyb64,
        $cryptob64,
        $key,
        $allowed_algs
      );

      $timestamp = is_null(
        static::$timestamp
      ) ? time() : static::$timestamp;
  
      if ( isset( $payload->nbf ) && $payload->nbf > ( $timestamp + static::$leeway )) {
        throw new BeforeValidException(
          static::CANNOT_HANDLE_TOKEN_PRIOR_TO
        );
      }
  
      if ( isset( $payload->iat ) && $payload->iat > ( $timestamp + static::$leeway )) {
        throw new BeforeValidException(
          static::CANNOT_HANDLE_TOKEN_PRIOR_TO
        );
      }
  
      if ( isset( $payload->exp ) && ( $timestamp - static::$leeway ) >= $payload->exp ) {
        throw new ExpiredException(
          static::EXPIRED_TOKEN
        );
      }
  
      return $payload;
    }
  
    public static function encode(
      object | array $payload,
      string $key,
      string $alg = "HS256",
      mixed $keyId = null,
      array $head = null,
      array $segments = []
    ): string {
      $header = [
        "typ" => "JWT",
        "alg" => $alg
      ];

      if ( $keyId !== null ) {
        $header[ "kid" ] = $keyId;
      }

      if ( isset( $head ) && is_array( $head )) {
        $header = array_merge(
          $head, $header
        );
      }

      $segments[] = static::urlsafeB64Encode(
        static::jsonEncode(
          $header
        )
      );

      $segments[] = static::urlsafeB64Encode(
        static::jsonEncode(
          $payload
        )
      );

      $signing_input = implode(
        '.',
        $segments
      );
  
      $signature = static::sign(
        $signing_input,
        $key,
        $alg
      );

      $segments[] = static::urlsafeB64Encode(
        $signature
      );
  
      return implode(
        '.',
        $segments
      );
    }
  
    public static function sign(
      string $msg,
      mixed $key,
      string $alg = "HS256",
      string $signature = ""
    ) {
      if ( empty( static::$supportedAlgs[ $alg ])) {
        throw new DomainException(
          static::ALGORITHM_NOT_SUPPORTED
        );
      }

      [ $function, $algorithm ] = static::$supportedAlgs[ $alg ];

      if ( $function === "hash_hmac" ) {
        return hash_hmac(
          $algorithm,
          $msg,
          $key,
          true
        );
      }

      if ( $function === "openssl" ) {
        $success = openssl_sign(
          $msg,
          $signature,
          $key,
          $algorithm
        );

        if ( $success === false ) {
          throw new DomainException(
            static::OPENSSL_UNABLE_TO_SIGN_DATA
          );
        } else {
          return $signature;
        }
      }
  
      return "";
    }

    private static function verifyOpenSsl(
      string $msg,
      string $signature,
      string $key,
      string $algorithm
    ): bool {
      $success = openssl_verify(
        $msg,
        $signature,
        $key,
        $algorithm
      );

      if ( $success === 1 ) {
        return true;
      } elseif ( $success === 0 ) {
        return false;
      }

      throw new DomainException(
        sprintf(
          "OpenSSL error: %s",
          openssl_error_string()
        )
      );
    }

    private static function verifyHashHmac(
      string $msg,
      string $signature,
      string $key,
      string $algorithm
    ): bool {
      $hash = hash_hmac(
        $algorithm,
        $msg,
        $key,
        true
      );

      if ( function_exists( "hash_equals" )) {
        return hash_equals(
          $signature,
          $hash
        );
      }

      $len = min(
        static::safeStrlen( $signature),
        static::safeStrlen( $hash )
      );

      $status = 0;

      for ( $i = 0; $i < $len; $i++ ) {
        $status |= ord( $signature[ $i ]) ^ ord(
          $hash[ $i ]
        );
      }
      $status |= static::safeStrlen( $signature ) ^ static::safeStrlen( $hash );

      return $status === 0;
    }
  
    private static function verify(
      string $msg,
      string $signature,
      string $key,
      string $alg
    ): bool {
      if ( empty( static::$supportedAlgs[ $alg ])) {
        throw new DomainException(
          static::ALGORITHM_NOT_SUPPORTED
        );
      }
  
      [ $function, $algorithm ] = static::$supportedAlgs[ $alg ];

      if ( $function === "openssl" ) {
        return static::verifyOpenSsl(
          $msg,
          $signature,
          $key,
          $algorithm
        );
      }

      if ( $function === "hash_hmac" ) {
        return static::verifyHashHmac(
          $msg,
          $signature,
          $key,
          $algorithm
        );
      }

      return false;
    }

    private static function versionCompare(
    ): bool | int {
      return version_compare(
        PHP_VERSION,
        "5.4.0",
        ">="
      );
    }
  
    public static function jsonDecode(
      string $input
    ): mixed {
      if ( static::versionCompare() && !( defined( "JSON_C_VERSION" ) && PHP_INT_SIZE > 4 )) {
        $obj = json_decode(
          $input,
          false,
          512,
          JSON_BIGINT_AS_STRING
        );
      } else {
        $max_int_length = strlen( (string)PHP_INT_MAX ) - 1;
        $json_without_bigints = preg_replace(
          '/:\s*(-?\d{'.$max_int_length.',})/',
          ': "$1"',
          $input
        );
        $obj = json_decode( $json_without_bigints );
      }
  
      if ( function_exists( "json_last_error" ) && $errno = json_last_error() ) {
        static::handleJsonError( $errno );
      } elseif ( $obj === null && $input !== "null" ) {
        throw new DomainException(
          static::NULL_RESULT_WITH_NON_NULL_INPUT
        );
      }

      return $obj;
    }
  
    public static function jsonEncode(
      object | array $input
    ): bool | string {
      $json = json_encode(
        $input
      );

      if (function_exists( "json_last_error" ) && $errno = json_last_error()) {
          static::handleJsonError( $errno );
      } elseif ( $json === 'null' && $input !== null ) {
        throw new DomainException(
          static::NULL_RESULT_WITH_NON_NULL_INPUT
        );
      }
      return $json;
    }
  
    public static function urlsafeB64Decode(
      string $input
    ): bool | string {
      $remainder = strlen(
        $input
      ) % 4;

      if ( $remainder ) {
        $padlen = 4 - $remainder;
        $input .= str_repeat(
          "=",
          $padlen
        );
      }

      return base64_decode(
        strtr(
          $input,
          "-_",
          "+/"
        )
      );
    }
  
    public static function urlsafeB64Encode(
      string $input
    ): string {
      return str_replace(
        '=',
        '',
        strtr(
          base64_encode(
            $input
          ),
        '+/',
        '-_')
      );
    }
  
    private static function handleJsonError(
      int $errno
    ): never {
      $messages = [
        JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
        JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters'
      ];
  
      throw new DomainException(
        isset( $messages[ $errno ]) === true
          ? $messages[ $errno] : "Unknown JSON error: {$errno}"
      );
    }
  
    private static function safeStrlen(
      string $str
    ): int {
      if ( function_exists( "mb_strlen" )) {
        return mb_strlen(
          $str,
          "8bit"
        );
      }
  
      return strlen(
        $str
      );
    }
  }
}

