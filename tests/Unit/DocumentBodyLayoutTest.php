<?php

namespace Tests\Unit;

use App\Infrastructure\Documents\DocumentBodyLayout;
use PHPUnit\Framework\TestCase;

class DocumentBodyLayoutTest extends TestCase
{
    public function test_classifies_headings_meta_paragraphs_and_signature(): void
    {
        $content = <<<'TXT'
GİZLİLİK POLİTİKASI

Veri Sorumlusu: Demo A.Ş.
Adres: Demo Cad. No:1

Bu paragraf açıklama metnidir.

Yetkili: Ayşe Yılmaz (KVKK Sorumlusu)
TXT;

        $blocks = (new DocumentBodyLayout)->blocks($content);
        $types = array_column($blocks, 'type');

        $this->assertContains('heading', $types);
        $this->assertContains('meta', $types);
        $this->assertContains('paragraph', $types);
        $this->assertContains('signature', $types);
    }
}
