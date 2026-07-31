<?php

namespace App\Console\Commands;

use Database\Seeders\DocumentTemplateSeeder;
use Illuminate\Console\Command;

class RefreshDocumentTemplatesCommand extends Command
{
    protected $signature = 'documents:refresh-templates';

    protected $description = 'KVKK varsayılan belge şablon paketini (~140) tüm kiracılara uygular';

    public function handle(): int
    {
        $this->call(DocumentTemplateSeeder::class);
        $this->info('Belge şablon paketi yenilendi.');

        return self::SUCCESS;
    }
}
