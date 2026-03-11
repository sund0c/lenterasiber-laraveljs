<?php

namespace App\Services;

/**
 * TotpService — RFC 6238 TOTP implementation.
 * Pure PHP, zero external dependencies.
 * Compatible: Google Authenticator, Authy, Microsoft Authenticator.
 */
class TotpService
{
    private const BASE32   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    private const PERIOD   = 30;
    private const DIGITS   = 6;
    private const WINDOW   = 1;   // ±1 period (clock drift tolerance)
    private const ALGO     = 'sha1';

    // ── Secret ───────────────────────────────────────────────

    /**
     * Generate a random 32-char Base32 secret (160 bits).
     */
    public function generateSecret(): string
    {
        $bytes   = random_bytes(20); // 160 bits
        $ints    = array_values(unpack('C*', $bytes));
        $result  = '';
        $buffer  = 0;
        $bitsLeft = 0;

        foreach ($ints as $byte) {
            $buffer    = ($buffer << 8) | $byte;
            $bitsLeft += 8;
            while ($bitsLeft >= 5) {
                $bitsLeft -= 5;
                $result   .= self::BASE32[($buffer >> $bitsLeft) & 0x1F];
            }
        }

        return substr(str_pad($result, 32, 'A'), 0, 32);
    }

    // ── Code generation ──────────────────────────────────────

    public function getCode(string $secret): string
    {
        $counter = intdiv(time(), self::PERIOD);
        return $this->hotp($secret, $counter);
    }

    public function verify(string $secret, string $code): bool
    {
        $code    = preg_replace('/\D/', '', $code);
        if (strlen($code) !== self::DIGITS) {
            return false;
        }

        $counter = intdiv(time(), self::PERIOD);
        for ($i = -self::WINDOW; $i <= self::WINDOW; $i++) {
            if (hash_equals($this->hotp($secret, $counter + $i), $code)) {
                return true;
            }
        }
        return false;
    }

    // ── Backup codes ─────────────────────────────────────────

    /**
     * Generate 8 backup codes.
     * Returns ['plain' => ['XXXX-XXXX', ...], 'hashed' => ['$2y$...', ...]]
     */
    public function generateBackupCodes(int $count = 8): array
    {
        $plain  = [];
        $hashed = [];
        for ($i = 0; $i < $count; $i++) {
            $raw     = strtoupper(bin2hex(random_bytes(4))); // 8 hex chars
            $display = substr($raw, 0, 4) . '-' . substr($raw, 4); // XXXX-XXXX
            $plain[] = $display;
            $hashed[] = password_hash($raw, PASSWORD_BCRYPT, ['cost' => 13]);
        }
        return ['plain' => $plain, 'hashed' => $hashed];
    }

    /**
     * Verify and consume a backup code.
     * Returns remaining hashed codes on success, null on failure.
     */
    public function verifyBackupCode(string $input, array $hashedCodes): ?array
    {
        $input = strtoupper(preg_replace('/[^A-F0-9]/i', '', $input));
        if (strlen($input) !== 8) {
            return null;
        }

        foreach ($hashedCodes as $idx => $hash) {
            if (password_verify($input, $hash)) {
                unset($hashedCodes[$idx]);
                return array_values($hashedCodes);
            }
        }
        return null;
    }

    // ── URI for QR ───────────────────────────────────────────

    public function getUri(string $secret, string $account): string
    {
        $issuer = config('totp.issuer', 'Lentera Siber Admin');
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=%d&period=%d',
            rawurlencode($issuer),
            rawurlencode($account),
            rawurlencode($secret),
            rawurlencode($issuer),
            self::DIGITS,
            self::PERIOD
        );
    }

    // ── HOTP (RFC 4226 core) ─────────────────────────────────

    private function hotp(string $base32Secret, int $counter): string
    {
        $key  = $this->base32Decode($base32Secret);
        $msg  = pack('N2', intdiv($counter, 0x100000000), $counter & 0xFFFFFFFF);
        $hash = hash_hmac(self::ALGO, $msg, $key, true);
        $ints = array_values(unpack('C*', $hash));

        $offset = $ints[19] & 0x0F;
        $code   = (
            (($ints[$offset]     & 0x7F) << 24) |
            (($ints[$offset + 1] & 0xFF) << 16) |
            (($ints[$offset + 2] & 0xFF) <<  8) |
             ($ints[$offset + 3] & 0xFF)
        ) % (10 ** self::DIGITS);

        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $secret): string
    {
        $secret   = strtoupper(preg_replace('/[^A-Z2-7]/', '', $secret));
        $lookup   = array_flip(str_split(self::BASE32));
        $decoded  = '';
        $buffer   = 0;
        $bitsLeft = 0;

        foreach (str_split($secret) as $char) {
            if (!isset($lookup[$char])) {
                continue;
            }
            $buffer    = ($buffer << 5) | $lookup[$char];
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $decoded  .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $decoded;
    }
}
