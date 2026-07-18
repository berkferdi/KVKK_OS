<?php

namespace Database\Seeders;

use App\Domain\Documents\Enums\TemplateCategory;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Organization\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->where('slug', 'demo-danismanlik')->first();
        if ($tenant === null) {
            return;
        }

        $templates = [
            [
                'code' => 'kamera_aydinlatma',
                'title' => 'Kamera Aydınlatma Metni',
                'category' => TemplateCategory::Camera,
                'description' => 'Kamera sistemleri için KVKK aydınlatma metni',
                'body' => <<<'TXT'
KAMERA İLE İZLEME AYDINLATMA METNİ

Veri Sorumlusu: {{firma_unvani}}
Adres: {{adres}}, {{ilce}} / {{sehir}}
MERSİS: {{mersis}}
Vergi No: {{vergi_no}} ({{vergi_dairesi}})
İletişim: {{eposta}} / {{telefon}}

İşbu metin, {{firma_unvani}} tarafından işletilen kamera sistemleri kapsamında
6698 sayılı KVKK uyarınca ilgili kişileri bilgilendirmek amacıyla hazırlanmıştır.

Yetkili: {{yetkili}} ({{yetkili_unvan}})
TXT,
            ],
            [
                'code' => 'web_aydinlatma',
                'title' => 'Web Aydınlatma Metni',
                'category' => TemplateCategory::Web,
                'description' => 'Web sitesi ziyaretçileri için aydınlatma',
                'body' => <<<'TXT'
WEB SİTESİ AYDINLATMA METNİ

{{firma_unvani}} (“Şirket”) olarak {{eposta}} üzerinden iletişime geçebilirsiniz.
Adresimiz: {{adres}}, {{sehir}}.

Web sitemizi ziyaretiniz sırasında işlenen kişisel verileriniz KVKK kapsamında korunur.
Ticaret unvanı: {{ticaret_unvani}}
MERSİS: {{mersis}}
TXT,
            ],
            [
                'code' => 'gizlilik_politikasi',
                'title' => 'Gizlilik Politikası',
                'category' => TemplateCategory::Policy,
                'description' => 'Genel gizlilik politikası taslağı',
                'body' => <<<'TXT'
GİZLİLİK POLİTİKASI

{{firma_unvani}} gizlilik politikası

1. Veri sorumlusu: {{firma_unvani}}, {{adres}}
2. Faaliyet: {{faaliyet}}
3. İletişim: {{eposta}}, {{telefon}}
4. Yetkili: {{yetkili}} — {{yetkili_unvan}}
TXT,
            ],
            [
                'code' => 'cerez_politikasi',
                'title' => 'Çerez Politikası',
                'category' => TemplateCategory::Cookie,
                'description' => 'Çerez kullanım politikası',
                'body' => <<<'TXT'
ÇEREZ POLİTİKASI

{{firma_unvani}} web sitesinde çerezler kullanılmaktadır.
İletişim: {{eposta}}
Adres: {{adres}}, {{sehir}}
MERSİS: {{mersis}}
TXT,
            ],
        ];

        foreach ($templates as $data) {
            $template = DocumentTemplate::query()->firstOrNew([
                'tenant_id' => $tenant->id,
                'code' => $data['code'],
            ]);

            if (! $template->exists) {
                $template->uuid = (string) Str::uuid();
            }

            $template->fill([
                'title' => $data['title'],
                'category' => $data['category'],
                'description' => $data['description'],
                'body' => $data['body'],
                'output_formats' => ['text', 'docx', 'pdf'],
                'version' => 1,
                'is_active' => true,
                'source' => 'seed',
                'metadata' => [],
            ]);
            $template->save();
        }
    }
}
