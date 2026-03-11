<?php

namespace App\Services;

/**
 * QrCodeService — generates QR code as base64 PNG data-URI.
 *
 * Pure PHP + GD. No CDN, no composer package required.
 * Implements QR Code Model 2, versions 1-10, EC level M, mask pattern 5.
 * Sufficient for otpauth:// URIs (up to ~200 chars).
 */
class QrCodeService
{
    private const BASE32   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    private const EC_LEVEL = 1; // M = 01 in binary

    // Reed-Solomon GF(256) tables
    private static array $EXP = [];
    private static array $LOG = [];
    private static bool  $gfReady = false;

    // ── Public API ───────────────────────────────────────────

    /**
     * Returns "data:image/png;base64,..." string.
     * Size = pixel size of final image.
     */
    public function dataUri(string $text, int $size = 200): string
    {
        $matrix = $this->buildMatrix($text);
        if ($matrix === null) {
            return '';
        }
        return 'data:image/png;base64,' . base64_encode($this->renderPng($matrix, $size));
    }

    // ── Matrix build ─────────────────────────────────────────

    private function buildMatrix(string $text): ?array
    {
        // Byte encoding
        $bytes   = array_values(unpack('C*', $text));
        $dataLen = count($bytes);

        // Version selection (EC level M capacities for byte mode)
        $caps = [14,26,42,62,84,106,122,154,180,213];
        $version = null;
        foreach ($caps as $i => $cap) {
            if ($dataLen <= $cap) { $version = $i + 1; break; }
        }
        if ($version === null) return null;

        // EC codeword counts (level M)
        $ecCounts   = [10,16,26,36,48,64,72,88,110,130];
        $totalCWArr = [26,44,70,100,134,172,196,242,292,346];
        $ecCount    = $ecCounts[$version - 1];
        $dataCW     = $totalCWArr[$version - 1] - $ecCount;

        // Data bits
        $bits  = '0100'; // byte mode
        $bits .= str_pad(decbin($dataLen), 8, '0', STR_PAD_LEFT);
        foreach ($bytes as $b) {
            $bits .= str_pad(decbin($b), 8, '0', STR_PAD_LEFT);
        }
        $bits .= '0000'; // terminator
        while (strlen($bits) % 8 !== 0) $bits .= '0';
        $pads = ['11101100','00010001'];
        $pi   = 0;
        while (strlen($bits) < $dataCW * 8) { $bits .= $pads[$pi++ % 2]; }

        // Codewords
        $codewords = [];
        for ($i = 0; $i < strlen($bits); $i += 8) {
            $codewords[] = (int) bindec(substr($bits, $i, 8));
        }

        // Reed-Solomon
        $ecCW = $this->rsEncode($codewords, $ecCount);
        $all  = array_merge($codewords, $ecCW);

        // Build symbol
        $sz     = 17 + 4 * $version;
        $matrix = array_fill(0, $sz, array_fill(0, $sz, -1));

        $this->placeFinder($matrix, 0, 0);
        $this->placeFinder($matrix, 0, $sz - 7);
        $this->placeFinder($matrix, $sz - 7, 0);
        $this->placeSeparators($matrix, $sz);
        $this->placeTiming($matrix, $sz);
        $this->placeAlignment($matrix, $version);
        $this->placeFormat($matrix, $sz, 5); // mask 5

        $this->placeData($matrix, $all, $sz);

        // Apply mask 5: (row÷2 + col÷3) % 2 == 0
        for ($r = 0; $r < $sz; $r++) {
            for ($c = 0; $c < $sz; $c++) {
                if ($matrix[$r][$c] >= 0 && !$this->isFunction($r, $c, $version, $sz)) {
                    if ((intdiv($r, 2) + intdiv($c, 3)) % 2 === 0) {
                        $matrix[$r][$c] ^= 1;
                    }
                }
            }
        }

        return $matrix;
    }

    // ── PNG render ───────────────────────────────────────────

    private function renderPng(array $matrix, int $size): string
    {
        $modules  = count($matrix);
        $quiet    = 4;
        $total    = $modules + $quiet * 2;
        $cell     = max(1, (int) round($size / $total));
        $imgSize  = $total * $cell;

        $img   = imagecreatetruecolor($imgSize, $imgSize);
        $white = imagecolorallocate($img, 255, 255, 255);
        $dark  = imagecolorallocate($img, 11, 30, 69); // #0b1e45

        imagefill($img, 0, 0, $white);

        foreach ($matrix as $row => $cols) {
            foreach ($cols as $col => $v) {
                if ($v === 1) {
                    $x = ($col + $quiet) * $cell;
                    $y = ($row + $quiet) * $cell;
                    imagefilledrectangle($img, $x, $y, $x + $cell - 1, $y + $cell - 1, $dark);
                }
            }
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return $png;
    }

    // ── QR function patterns ─────────────────────────────────

    private function placeFinder(array &$m, int $row, int $col): void
    {
        $pat = [
            [1,1,1,1,1,1,1],
            [1,0,0,0,0,0,1],
            [1,0,1,1,1,0,1],
            [1,0,1,1,1,0,1],
            [1,0,1,1,1,0,1],
            [1,0,0,0,0,0,1],
            [1,1,1,1,1,1,1],
        ];
        foreach ($pat as $r => $row_data) {
            foreach ($row_data as $c => $v) {
                $m[$row + $r][$col + $c] = $v;
            }
        }
    }

    private function placeSeparators(array &$m, int $sz): void
    {
        for ($i = 0; $i < 8; $i++) {
            $m[7][$i]        = 0; $m[$i][7]        = 0;
            $m[7][$sz-1-$i]  = 0; $m[$i][$sz-8]    = 0;
            $m[$sz-8][$i]    = 0; $m[$sz-1-$i][7]  = 0;
        }
    }

    private function placeTiming(array &$m, int $sz): void
    {
        for ($i = 8; $i < $sz - 8; $i++) {
            $v = ($i % 2 === 0) ? 1 : 0;
            $m[6][$i] = $v;
            $m[$i][6] = $v;
        }
    }

    private function placeAlignment(array &$m, int $version): void
    {
        $map = [
            2=>[6,18],3=>[6,22],4=>[6,26],5=>[6,30],
            6=>[6,34],7=>[6,22,38],8=>[6,24,42],9=>[6,26,46],10=>[6,28,50]
        ];
        $locs = $map[$version] ?? [];
        foreach ($locs as $r) {
            foreach ($locs as $c) {
                if ($m[$r][$c] !== -1) continue;
                for ($dr = -2; $dr <= 2; $dr++) {
                    for ($dc = -2; $dc <= 2; $dc++) {
                        $m[$r+$dr][$c+$dc] = (abs($dr)==2||abs($dc)==2||(($dr==0)&&($dc==0))) ? 1 : 0;
                    }
                }
            }
        }
    }

    private function placeFormat(array &$m, int $sz, int $mask): void
    {
        $data = (self::EC_LEVEL << 3) | $mask;
        $d    = $data;
        $g    = 0b10100110111;
        $b    = $d << 10;
        while ($this->bitLen($b) >= 11) {
            $b ^= $g << ($this->bitLen($b) - 11);
        }
        $fmt  = (($data << 10) | $b) ^ 0b101010000010010;
        $bits = str_pad(decbin($fmt), 15, '0', STR_PAD_LEFT);

        $pos = [
            [8,0],[8,1],[8,2],[8,3],[8,4],[8,5],[8,7],[8,8],
            [7,8],[5,8],[4,8],[3,8],[2,8],[1,8],[0,8]
        ];
        foreach ($pos as $i => $p) {
            $bit = (int)$bits[$i];
            $m[$p[0]][$p[1]] = $bit;
            if ($i < 8) {
                $m[$p[0]][$sz - 1 - $i] = $bit;
            } else {
                $m[$sz - 15 + $i][$p[1]] = $bit;
            }
        }
        $m[$sz - 8][8] = 1; // dark module
    }

    private function bitLen(int $n): int
    {
        $l = 0; while ($n > 0) { $l++; $n >>= 1; } return $l;
    }

    private function isFunction(int $r, int $c, int $version, int $sz): bool
    {
        if ($r < 9 && $c < 9)           return true;
        if ($r < 9 && $c >= $sz - 8)    return true;
        if ($r >= $sz - 8 && $c < 9)    return true;
        if ($r === 6 || $c === 6)        return true;
        if ($r === 8 || $c === 8)        return true;
        return false;
    }

    private function placeData(array &$m, array $cw, int $sz): void
    {
        $bits = '';
        foreach ($cw as $w) $bits .= str_pad(decbin($w), 8, '0', STR_PAD_LEFT);
        $bits = str_pad($bits, $sz * $sz, '0');

        $bi   = 0;
        $up   = true;
        $col  = $sz - 1;

        while ($col > 0) {
            if ($col === 6) $col--;
            for ($i = 0; $i < $sz; $i++) {
                $row = $up ? $sz - 1 - $i : $i;
                for ($dc = 0; $dc <= 1; $dc++) {
                    $cc = $col - $dc;
                    if ($m[$row][$cc] === -1 && $bi < strlen($bits)) {
                        $m[$row][$cc] = (int)$bits[$bi++];
                    }
                }
            }
            $up  = !$up;
            $col -= 2;
        }
    }

    // ── Reed-Solomon ─────────────────────────────────────────

    private function rsEncode(array $data, int $ecCount): array
    {
        $this->initGF();
        $gen = $this->rsGenerator($ecCount);
        $msg = array_merge($data, array_fill(0, $ecCount, 0));

        for ($i = 0; $i < count($data); $i++) {
            $coef = $msg[$i];
            if ($coef === 0) continue;
            $lc = self::$LOG[$coef];
            for ($j = 0; $j < count($gen); $j++) {
                $msg[$i + $j] ^= self::$EXP[($lc + $gen[$j]) % 255];
            }
        }
        return array_slice($msg, count($data));
    }

    private function rsGenerator(int $degree): array
    {
        $g = [1];
        for ($i = 0; $i < $degree; $i++) {
            $g = $this->polyMul($g, [1, self::$EXP[$i]]);
        }
        return $g;
    }

    private function polyMul(array $a, array $b): array
    {
        $r = array_fill(0, count($a) + count($b) - 1, 0);
        foreach ($a as $i => $av) {
            foreach ($b as $j => $bv) {
                if ($av === 0 || $bv === 0) continue;
                $r[$i+$j] ^= self::$EXP[(self::$LOG[$av] + self::$LOG[$bv]) % 255];
            }
        }
        return $r;
    }

    private function initGF(): void
    {
        if (self::$gfReady) return;
        self::$EXP = array_fill(0, 512, 0);
        self::$LOG = array_fill(0, 256, 0);
        $x = 1;
        for ($i = 0; $i < 255; $i++) {
            self::$EXP[$i] = $x;
            self::$LOG[$x] = $i;
            $x <<= 1;
            if ($x > 255) $x ^= 0x11d;
        }
        for ($i = 255; $i < 512; $i++) self::$EXP[$i] = self::$EXP[$i - 255];
        self::$gfReady = true;
    }
}
