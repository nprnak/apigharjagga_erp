<?php

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Dompdf\Dompdf;

$dompdf = new Dompdf(['fontDir' => storage_path('fonts'), 'fontCache' => storage_path('fonts')]);

$fontFile = storage_path('fonts/NotoSansDevanagari.ttf');

if (!file_exists($fontFile)) {
    echo "ERROR: Font file not found at: $fontFile\n";
    exit(1);
}

echo "Font file found: $fontFile (" . round(filesize($fontFile)/1024) . " KB)\n";

$fontMetrics = $dompdf->getFontMetrics();
$fontMetrics->registerFont(
    ['family' => 'noto_devanagari', 'style' => 'normal', 'weight' => 'normal'],
    $fontFile
);

echo "Font 'noto_devanagari' registered successfully!\n";

// Verify
$installedFont = $fontMetrics->getFont('noto_devanagari');
echo "Verified font path: $installedFont\n";
