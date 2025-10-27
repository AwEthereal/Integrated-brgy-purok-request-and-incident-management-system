<?php

namespace App\Helpers;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeHelper
{
    /**
     * Generate QR code SVG
     *
     * @param string $data
     * @param int $size
     * @return string
     */
    public static function generate(string $data, int $size = 300): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd()
        );
        
        $writer = new Writer($renderer);
        
        return $writer->writeString($data);
    }

    /**
     * Generate QR code for website URL
     *
     * @return string
     */
    public static function generateWebsiteQr(): string
    {
        $url = config('app.url');
        return self::generate($url, 320);
    }
}
