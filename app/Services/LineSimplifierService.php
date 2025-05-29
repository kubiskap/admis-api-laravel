<?php

namespace App\Services;

use geoPHP;

class LineSimplifierService
{
    public function simplifyToWkt(string $text, float $tolerance): ?string
    {
        // strip “SRID=…” if present
        if (str_starts_with($text, 'SRID=')) {
            $text = explode(';', $text, 2)[1] ?? $text;
        }

        // load WKT (fallback to WKB HEX if needed)
        $geom = \geoPHP::load($text, 'wkt')
             ?: (preg_match('/^[0-9A-F]+$/i', trim($text))
                 ? \geoPHP::load($text, 'wkb')
                 : null);

        if (!$geom) {
            return $text;                       // could not parse – return as-is
        }

        // run Douglas-Peucker
        $simplified = $geom->simplify(max($tolerance, 0.0));

        // geoPHP returns false/null when simplification fails
        if (!$simplified) {
            return $text;                       // fall back to original geometry
        }

        return $simplified->out('wkt');         // ✅ safe
    }
}
