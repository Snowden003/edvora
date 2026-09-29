<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRMarkupSVG;

class QrCodeService
{
    /**
     * Generate raw SVG markup of QR code for any given data/URL.
     */
    public static function generateSvg(string $data, int $scale = 8): string
    {
        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'outputBase64'    => false,
            'svgAddXmlHeader' => false,
            'scale'           => $scale,
            'drawLightModules'=> true,
        ]);

        return (new QRCode($options))->render($data);
    }

    /**
     * Generate base64 Data URI for direct <img src="..."> usage.
     */
    public static function generateDataUri(string $data, int $scale = 8): string
    {
        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'outputBase64'    => true,
            'svgAddXmlHeader' => false,
            'scale'           => $scale,
            'drawLightModules'=> true,
        ]);

        return (new QRCode($options))->render($data);
    }
}
