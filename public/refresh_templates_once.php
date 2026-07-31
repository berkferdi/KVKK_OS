<?php

/**
 * cPanel: KVKK şablon paketini bir kez yüklemek için.
 * Kullanım: /refresh_templates_once.php?token=KVKK360_REFRESH_TEMPLATES
 * Sonra bu dosyayı silin.
 */

declare(strict_types=1);
use Database\Seeders\DocumentTemplateSeeder;
use Illuminate\Contracts\Console\Kernel;

$token = $_GET['token'] ?? '';
if (! hash_equals('KVKK360_REFRESH_TEMPLATES', (string) $token)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Forbidden';
    exit;
}

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

try {
    $kernel->call('migrate', ['--force' => true]);
    $app->make(DocumentTemplateSeeder::class)->run();
} catch (Throwable $e) {
    http_response_code(500);
    echo 'HATA: '.$e->getMessage()."\n";
    exit;
}

echo "OK: Migration + ~148 belge şablonu yenilendi.\n";
echo "Bu dosyayı (public/refresh_templates_once.php) hemen silin.\n";
