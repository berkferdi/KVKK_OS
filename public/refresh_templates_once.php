<?php

/**
 * cPanel / shared hosting: seed şablonlarını bir kez yenilemek için.
 * Artisan komutuna ihtiyaç duymaz; DocumentTemplateSeeder'ı doğrudan çalıştırır.
 *
 * Gereken dosyalar (önce yükleyin):
 * - database/seeders/DocumentTemplateSeeder.php
 * - app/Application/Services/Documents/PlaceholderResolver.php (kamera placeholder'ları için)
 *
 * Kullanım: https://domain/refresh_templates_once.php?token=KVKK360_REFRESH_TEMPLATES
 * Çalıştırdıktan sonra bu dosyayı silin.
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

$seederClass = DocumentTemplateSeeder::class;
if (! class_exists($seederClass)) {
    http_response_code(500);
    echo "HATA: DocumentTemplateSeeder bulunamadı.\n";
    echo "Önce database/seeders/DocumentTemplateSeeder.php dosyasını sunucuya yükleyin.\n";
    exit;
}

try {
    $seeder = $app->make($seederClass);
    $seeder->run();
} catch (Throwable $e) {
    http_response_code(500);
    echo "HATA: Şablon yenileme başarısız.\n";
    echo $e->getMessage()."\n";
    exit;
}

echo "OK: Belge şablonları yenilendi.\n";
echo "Bu dosyayı (public/refresh_templates_once.php) hemen silin.\n";
