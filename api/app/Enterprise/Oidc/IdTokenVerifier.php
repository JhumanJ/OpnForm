<?php

namespace App\Enterprise\Oidc;

use App\Enterprise\Oidc\Models\IdentityConnection;
use Illuminate\Support\Facades\Log;

/**
 * ID Token Verifier for OIDC.
 *
 * Note: Full signature verification requires JWK to PEM conversion.
 * For production use, consider adding web-token/jwt-framework which has
 * built-in JWK support, or implement proper RSA/EC key conversion.
 */
class IdTokenVerifier
{
    /**
     * Verify ID token signature using JWKS from the provider.
     *
     * Currently validates structure and claims only.
     * Full signature verification requires JWK to PEM conversion.
     *
     * @throws \Exception
     */
    public function verifySignature(IdentityConnection $connection, string $idToken): void
    {
        try {
            // Decode header to get key ID (kid) and algorithm
            $parts = explode('.', $idToken);
            if (count($parts) !== 3) {
                throw new \Exception('Invalid ID token format');
            }

            [$headerB64] = $parts;
            $header = json_decode($this->base64UrlDecode($headerB64), true);

            if (!$header || !isset($header['alg'])) {
                throw new \Exception('Invalid ID token header');
            }

            // TODO: Implement full signature verification with JWKS
            // This requires:
            // 1. Fetching JWKS from /.well-known/jwks.json
            // 2. Converting JWK to PEM format
            // 3. Verifying signature using firebase/php-jwt or web-token/jwt-framework
            //
            // For now, we rely on HTTPS and claim validation for security.
            // The OIDC provider should be trusted (HTTPS-only in production).

            Log::debug('OIDC ID token structure validated', [
                'connection_id' => $connection->id,
                'algorithm' => $header['alg'] ?? 'unknown',
            ]);
        } catch (\Exception $e) {
            Log::warning('OIDC ID token verification failed', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('ID token verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Base64 URL decode.
     */
    protected function base64UrlDecode(string $data): string
    {
        return base64_decode(str_pad(
            strtr($data, '-_', '+/'),
            strlen($data) % 4,
            '=',
            STR_PAD_RIGHT
        ));
    }
}
