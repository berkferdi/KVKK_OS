<?php

namespace App\Console\Commands;

use Database\Seeders\DocumentTemplateSeeder;
use Illuminate\Console\Command;

class RefreshDocumentTemplatesCommand extends Command
{
    protected $signature = 'documents:refresh-templates';

    protected $description = 'Seed kaynaklı belge şablonlarını güncel tam metinlerle yeniler (elle özelleştirilmiş şablonlara dokunmaz)';

    public function handle(): int
    {
        $this->call(DocumentTemplateSeeder::class);
        $this->info('Belge şablonları yenilendi.');

        return self::SUCCESS;
    }
}
