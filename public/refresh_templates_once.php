<?php

/**
 * cPanel / shared hosting: seed şablonlarını bir kez yenilemek için.
 * Çalıştırdıktan sonra bu dosyayı silin.
 *
 * Kullanım: https://domain/refresh_templates_once.php?token=KVKK360_REFRESH_TEMPLATES
 */

declare(strict_types=1);
use Illuminate\Contracts\Console\Kernel;

$token = $_GET['token'] ?? '';
if (! hash_equals('KVKK360_REFRESH_TEMPLATES', (string) $token)) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$kernel->call('documents:refresh-templates');

header('Content-Type: text/plain; charset=utf-8');
echo "OK: Belge şablonları yenilendi.\n";
echo "Bu dosyayı (public/refresh_templates_once.php) hemen silin.\n";
