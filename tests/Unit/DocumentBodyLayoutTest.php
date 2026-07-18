<?php

namespace Tests\Unit;

use App\Infrastructure\Documents\DocumentBodyLayout;
use PHPUnit\Framework\TestCase;

class DocumentBodyLayoutTest extends TestCase
{
    public function test_classifies_numbered_headings_meta_and_signature(): void
    {
        $content = <<<'TXT'
ÇEREZ POLİTİKASI

Veri Sorumlusu: Demo A.Ş.
Adres: Demo Cad. No:1

1. Çerez Nedir?
Bu paragraf açıklama metnidir.

Yetkili: Ayşe Yılmaz (KVKK Sorumlusu)
TXT;

        $blocks = (new DocumentBodyLayout)->blocks($content, 'Çerez Politikası');
        $types = array_column($blocks, 'type');

        $this->assertContains('title', $types);
        $this->assertContains('heading', $types);
        $this->assertContains('meta', $types);
        $this->assertContains('paragraph', $types);
        $this->assertContains('signature', $types);
    }

    public function test_strips_duplicate_document_title_and_extracts_company(): void
    {
        $layout = new DocumentBodyLayout;
        $content = <<<'TXT'
Kamera Aydınlatma Metni

Veri Sorumlusu: Berk Yazılım A.Ş.
Adres: Alanya
TXT;

        $blocks = $layout->blocks($content, 'Kamera Aydınlatma Metni');
        $texts = array_column($blocks, 'text');

        $this->assertNotContains('Kamera Aydınlatma Metni', $texts);
        $this->assertSame('Berk Yazılım A.Ş.', $layout->extractCompanyName($content));
    }
}
