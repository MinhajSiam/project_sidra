<?php

declare(strict_types=1);

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QROutputInterface;
use Throwable;

class QrCodeService {
    /**
     * Generates a crisp vector SVG string for the given payload.
     */
    public static function generateSvg(string $data): string {
        try {
            $options = new QROptions([
                'version'             => 5,
                'outputInterface'     => QROutputInterface::MARKUP_SVG,
                'outputType'          => QRCode::OUTPUT_MARKUP_SVG,
                'eccLevel'            => QRCode::ECC_M,
                'svgUseFill'          => true,
                'svgAddXmlHeader'     => false,
                'drawLightModules'    => true,
                'connectPaths'        => true,
                'imageTransparent'    => true,
            ]);

            return (new QRCode($options))->render($data);
        } catch (Throwable $e) {
            // Fallback lightweight SVG placeholder if error occurs
            return self::generateFallbackSvg($data);
        }
    }

    /**
     * Generates a base64 Data URI for image tags.
     */
    public static function generateDataUri(string $data): string {
        $svg = self::generateSvg($data);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    private static function generateFallbackSvg(string $data): string {
        $encoded = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200">
    <rect width="200" height="200" fill="#f8fafc" stroke="#cbd5e1" stroke-width="2"/>
    <text x="100" y="90" font-family="sans-serif" font-size="12" font-weight="bold" text-anchor="middle" fill="#0f172a">SIDRA QR SECURE</text>
    <text x="100" y="115" font-family="monospace" font-size="8" text-anchor="middle" fill="#64748b">{$encoded}</text>
</svg>
SVG;
    }
}
