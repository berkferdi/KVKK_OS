<?php

namespace App\Domain\Documents\Support;

use App\Domain\Documents\Enums\TemplateCategory;

/**
 * HTML KVKK belge gövdesi üretici.
 */
final class HtmlTemplateBuilder
{
    /**
     * @param  list<array{heading: string, paragraphs: list<string>}>  $sections
     */
    public function build(string $title, array $sections, ?string $intro = null): string
    {
        $html = [];
        $html[] = '<article class="kvkk-doc">';
        $html[] = '<header class="doc-header">';
        $html[] = '<p class="doc-meta-line">Doküman No: {{dokuman_no}} &nbsp;|&nbsp; Rev: {{revizyon_no}} &nbsp;|&nbsp; Versiyon: {{versiyon}} &nbsp;|&nbsp; Durum: {{yururluk_durumu}}</p>';
        $html[] = '<h1>'.e($title).'</h1>';
        $html[] = '<p class="doc-meta-line">Yayın Tarihi: {{yayin_tarihi}} &nbsp;|&nbsp; Revizyon Tarihi: {{revizyon_tarihi}}</p>';
        $html[] = '<p class="doc-meta-line">Veri Sorumlusu: {{veri_sorumlusu}} — {{veri_sorumlusu_adres}}, {{ilce}} / {{sehir}}</p>';
        $html[] = '</header>';

        if ($intro !== null && $intro !== '') {
            $html[] = '<p>'.$intro.'</p>';
        }

        foreach ($sections as $index => $section) {
            $number = $index + 1;
            $html[] = '<section>';
            $html[] = '<h2>'.$number.'. '.e($section['heading']).'</h2>';
            foreach ($section['paragraphs'] as $paragraph) {
                $html[] = '<p>'.$paragraph.'</p>';
            }
            $html[] = '</section>';
        }

        $html[] = '<footer class="doc-footer">';
        $html[] = '<p><strong>KVKK Başvuru:</strong> {{kvkk_basvuru_eposta}} / {{kvkk_basvuru_adresi}} / KEP: {{kvkk_basvuru_kep}}</p>';
        $html[] = '<p><strong>Hazırlayan:</strong> {{hazirlayan}} &nbsp;|&nbsp; <strong>Onaylayan:</strong> {{onaylayan}}</p>';
        $html[] = '<p>{{firma_unvani}} — {{eposta}} — {{telefon}} — {{web}}</p>';
        $html[] = '</footer>';
        $html[] = '</article>';

        return implode("\n", $html);
    }

    /**
     * @param  array{code: string, title: string, category: TemplateCategory, description: string, focus?: string}  $item
     */
    public function forCatalogItem(array $item): string
    {
        $title = $item['title'];
        $focus = $item['focus'] ?? $title;
        $category = $item['category'];

        return match ($category) {
            TemplateCategory::Corporate => $this->corporate($title, $focus),
            TemplateCategory::Policy => $this->policy($title, $focus),
            TemplateCategory::Disclosure => $this->disclosure($title, $focus),
            TemplateCategory::Consent => $this->consent($title, $focus),
            TemplateCategory::Form => $this->form($title, $focus),
            TemplateCategory::Contract => $this->contract($title, $focus),
            TemplateCategory::Commitment => $this->commitment($title, $focus),
            TemplateCategory::Procedure => $this->procedure($title, $focus),
            TemplateCategory::Inventory => $this->inventory($title, $focus),
            TemplateCategory::Report => $this->report($title, $focus),
            TemplateCategory::Instruction => $this->instruction($title, $focus),
            default => $this->other($title, $focus),
        };
    }

    private function corporate(string $title, string $focus): string
    {
        return $this->build($title, [
            ['heading' => 'Amaç', 'paragraphs' => [
                '{{firma_unvani}} bünyesinde '.$focus.' kapsamında KVKK uyum çalışmalarının planlanması, izlenmesi ve yönetilmesi amaçlanır.',
                'Bu doküman, yönetim taahhüdü ve kurumsal sorumluluk çerçevesinde uygulanır.',
            ]],
            ['heading' => 'Kapsam', 'paragraphs' => [
                'Doküman; {{firma_unvani}} çalışanları, yöneticileri, iş ortakları ve veri işleyenleri kapsar.',
                'Faaliyet alanı: {{faaliyet}} (NACE: {{nace_kodu}}).',
            ]],
            ['heading' => 'Organizasyon ve Sorumluluklar', 'paragraphs' => [
                'Veri sorumlusu: {{veri_sorumlusu}}.',
                'KVKK süreçlerinden sorumlu yetkili: {{yetkili}} ({{yetkili_unvan}}).',
                'İletişim: {{kvkk_eposta}} / {{telefon}}.',
            ]],
            ['heading' => 'Uygulama Esasları', 'paragraphs' => [
                '6698 sayılı KVKK ve ikincil mevzuat hükümleri esas alınır.',
                'Uyum faaliyetleri yıllık planda izlenir; eksiklikler düzeltici faaliyet ile kapatılır.',
            ]],
            ['heading' => 'Gözden Geçirme', 'paragraphs' => [
                'Doküman en az yılda bir kez veya mevzuat/organizasyon değişikliğinde gözden geçirilir.',
            ]],
        ], '{{firma_unvani}} olarak kişisel verilerin korunması konusunda yönetim taahhüdümüzü ve kurumsal uyum yaklaşımımızı işbu doküman ile kayıt altına alıyoruz.');
    }

    private function policy(string $title, string $focus): string
    {
        return $this->build($title, [
            ['heading' => 'Amaç', 'paragraphs' => [
                'İşbu politika, {{firma_unvani}} nezdinde '.$focus.' konusundaki temel kuralları belirler.',
            ]],
            ['heading' => 'Kapsam', 'paragraphs' => [
                'Politika; çalışanlar, ziyaretçiler, tedarikçiler ve sistem kullanıcıları için bağlayıcıdır.',
            ]],
            ['heading' => 'Temel İlkeler', 'paragraphs' => [
                'Kişisel veriler hukuka ve dürüstlük kurallarına uygun, belirli, açık ve meşru amaçlarla, işlendikleri amaçla bağlantılı, sınırlı ve ölçülü olarak işlenir.',
                'Veriler doğru ve güncel tutulur; gerekli süre kadar muhafaza edilir.',
            ]],
            ['heading' => 'Uygulama Kuralları', 'paragraphs' => [
                $focus.' kapsamında teknik ve idari tedbirler alınır; yetkisiz erişim engellenir.',
                'İhlal şüphesinde {{kvkk_eposta}} adresine derhal bildirim yapılır.',
            ]],
            ['heading' => 'Haklar ve Başvuru', 'paragraphs' => [
                'İlgili kişiler KVKK md. 11 kapsamındaki haklarını {{kvkk_basvuru_eposta}} veya {{kvkk_basvuru_adresi}} üzerinden kullanabilir.',
            ]],
        ]);
    }

    private function disclosure(string $title, string $focus): string
    {
        $extra = [];
        if (str_contains(mb_strtolower($focus), 'kamera')) {
            $extra = [
                ['heading' => 'Kamera ile İzleme', 'paragraphs' => [
                    'İzleme alanları: {{kamera_alanlari}}.',
                    'Kamera sayısı: {{kamera_sayisi}}. Saklama süresi: {{kamera_saklama_gun}} gün.',
                    'Amaç: {{kamera_amaci}}.',
                ]],
            ];
        }

        return $this->build($title, array_merge([
            ['heading' => 'Veri Sorumlusu', 'paragraphs' => [
                'Veri sorumlusu: {{veri_sorumlusu}}.',
                'Adres: {{veri_sorumlusu_adres}}, {{ilce}} / {{sehir}} {{posta_kodu}}, {{ulke}}.',
                'İletişim: {{veri_sorumlusu_eposta}} / {{veri_sorumlusu_telefon}} / KEP: {{kep}}.',
                'MERSİS: {{mersis}} | Vergi No: {{vergi_no}} ({{vergi_dairesi}}).',
            ]],
            ['heading' => 'Aydınlatmanın Konusu', 'paragraphs' => [
                'İşbu metin, '.$focus.' kapsamında işlenen kişisel verilere ilişkin KVKK md. 10 uyarınca bilgilendirme amacıyla hazırlanmıştır.',
            ]],
            ['heading' => 'İşlenen Veriler ve Amaçlar', 'paragraphs' => [
                'Kimlik, iletişim, işlem güvenliği ve sürece özgü veri kategorileri; hizmet sunumu, güvenlik, sözleşme süreçleri ve yasal yükümlülükler için işlenebilir.',
            ]],
            ['heading' => 'Hukuki Sebepler', 'paragraphs' => [
                'Veriler KVKK md. 5 ve 6’da sayılan hukuki sebepler (sözleşme, hukuki yükümlülük, meşru menfaat, açık rıza vb.) kapsamında işlenir.',
            ]],
            ['heading' => 'Aktarım ve Saklama', 'paragraphs' => [
                'Veriler amaçla sınırlı olarak tedarikçilere ve yetkili kamu kurumlarına aktarılabilir; gerekli süre boyunca saklanır.',
            ]],
            ['heading' => 'İlgili Kişi Hakları', 'paragraphs' => [
                'KVKK md. 11 hakları için başvurular {{kvkk_basvuru_eposta}} veya {{kvkk_basvuru_adresi}} adresine yapılabilir.',
            ]],
        ], $extra));
    }

    private function consent(string $title, string $focus): string
    {
        return $this->build($title, [
            ['heading' => 'Açık Rızanın Konusu', 'paragraphs' => [
                '{{firma_unvani}} tarafından '.$focus.' kapsamında kişisel verilerimin işlenmesine, KVKK md. 3/1-a uyarınca özgür irademle açık rıza veriyorum.',
            ]],
            ['heading' => 'Bilgilendirme', 'paragraphs' => [
                'Aydınlatma metnini okudum; haklarımı öğrendim. Rızamı dilediğim zaman {{kvkk_basvuru_eposta}} üzerinden geri alabileceğimi biliyorum.',
            ]],
            ['heading' => 'Onay', 'paragraphs' => [
                'Ad Soyad / Unvan: ................................',
                'Tarih / İmza: ................................',
                'İletişim: {{eposta}} / {{telefon}}',
            ]],
        ], 'Veri sorumlusu: {{veri_sorumlusu}} — {{veri_sorumlusu_adres}}');
    }

    private function form(string $title, string $focus): string
    {
        return $this->build($title, [
            ['heading' => 'Başvuru / Form Bilgileri', 'paragraphs' => [
                'Form türü: '.$focus.'.',
                'Firma: {{firma_unvani}} | Başvuru kanalı: {{kvkk_basvuru_eposta}} / {{kvkk_basvuru_adresi}}.',
            ]],
            ['heading' => 'Talep Eden', 'paragraphs' => [
                'Ad Soyad: ................................',
                'T.C. Kimlik No / Pasaport: ................................',
                'İletişim: ................................',
                'Departman / Pozisyon (çalışan ise): {{departman}} / {{pozisyon}}',
            ]],
            ['heading' => 'Talep Detayı', 'paragraphs' => [
                'Talep konusu: ................................',
                'Açıklama: ................................',
            ]],
            ['heading' => 'Beyan', 'paragraphs' => [
                'Verdiğim bilgilerin doğru olduğunu, KVKK kapsamındaki talebimin değerlendirilmesini kabul ederim.',
                'Tarih / İmza: ................................',
            ]],
        ]);
    }

    private function contract(string $title, string $focus): string
    {
        return $this->build($title, [
            ['heading' => 'Taraflar', 'paragraphs' => [
                'Veri Sorumlusu: {{firma_unvani}}, {{adres}}, {{ilce}} / {{sehir}}, MERSİS: {{mersis}}.',
                'Karşı taraf / Tedarikçi: {{tedarikci_unvani}} (Yetkili: {{tedarikci_yetkili}}).',
            ]],
            ['heading' => 'Konu', 'paragraphs' => [
                'İşbu sözleşme, '.$focus.' kapsamında kişisel verilerin işlenmesi/paylaşılmasına ilişkin yükümlülükleri düzenler.',
            ]],
            ['heading' => 'Yükümlülükler', 'paragraphs' => [
                'Taraflar KVKK ve ilgili mevzuata uygun hareket eder; verileri yalnızca belirtilen amaçla işler.',
                'Alt işleyen kullanımı yazılı onay ve aynı düzeyde tedbirlerle mümkündür.',
            ]],
            ['heading' => 'Güvenlik ve İhlal', 'paragraphs' => [
                'Teknik/idari tedbirler alınır; ihlalde karşı taraf derhal bilgilendirilir.',
            ]],
            ['heading' => 'Süre ve Sonerme', 'paragraphs' => [
                'Sözleşme imza ile yürürlüğe girer; sona ermede veriler iade/silme/yok etme kurallarına tabi olur.',
            ]],
        ]);
    }

    private function commitment(string $title, string $focus): string
    {
        return $this->build($title, [
            ['heading' => 'Taahhüt', 'paragraphs' => [
                '{{firma_unvani}} nezdinde '.$focus.' kapsamında öğrendiğim kişisel verileri ve gizlilik arz eden bilgileri yetkisiz kişilerle paylaşmayacağımı, KVKK’ya aykırı işlemeyeceğimi taahhüt ederim.',
            ]],
            ['heading' => 'Yükümlülükler', 'paragraphs' => [
                'Şifre, erişim ve fiziksel güvenlik kurallarına uyarım.',
                'İhlal şüphesini {{kvkk_eposta}} adresine bildiririm.',
            ]],
            ['heading' => 'Onay', 'paragraphs' => [
                'Ad Soyad: ................................ | Departman: {{departman}} | Pozisyon: {{pozisyon}}',
                'İşe giriş: {{ise_giris_tarihi}} | Tarih / İmza: ................................',
            ]],
        ]);
    }

    private function procedure(string $title, string $focus): string
    {
        return $this->build($title, [
            ['heading' => 'Amaç', 'paragraphs' => [
                'Bu prosedür, '.$focus.' sürecinin standart adımlarını tanımlar.',
            ]],
            ['heading' => 'Sorumlular', 'paragraphs' => [
                'Süreç sahibi: {{yetkili}} ({{yetkili_unvan}}). İletişim: {{kvkk_eposta}}.',
            ]],
            ['heading' => 'Uygulama Adımları', 'paragraphs' => [
                '1) Olay/talep tespiti ve kayıt',
                '2) Değerlendirme ve yetki kontrolü',
                '3) İşlem (yanıt, imha, erişim, yedekleme vb.)',
                '4) Kanıt ve log kayıtlarının muhafazası',
                '5) Kapanış ve raporlama',
            ]],
            ['heading' => 'Kayıtlar', 'paragraphs' => [
                'Süreç kayıtları ilgili saklama sürelerine uygun muhafaza edilir.',
            ]],
        ]);
    }

    private function inventory(string $title, string $focus): string
    {
        return $this->build($title, [
            ['heading' => 'Envanter Amacı', 'paragraphs' => [
                'Bu envanter, '.$focus.' kapsamındaki varlıkların/kayıtların izlenmesi için tutulur.',
            ]],
            ['heading' => 'Kurum Bilgileri', 'paragraphs' => [
                '{{firma_unvani}} | MERSİS: {{mersis}} | NACE: {{nace_kodu}} | Çalışan: {{calisan_sayisi}}',
            ]],
            ['heading' => 'Envanter Alanları', 'paragraphs' => [
                'Kayıt adı / varlık | Sahip birim | Konum / sistem | Saklama süresi | Hukuki sebep | Aktarım | Güvenlik tedbiri',
            ]],
            ['heading' => 'Güncelleme', 'paragraphs' => [
                'Envanter değişikliklerde ve en az yılda bir güncellenir. Sorumlu: {{yetkili}}.',
            ]],
        ]);
    }

    private function report(string $title, string $focus): string
    {
        return $this->build($title, [
            ['heading' => 'Rapor Özeti', 'paragraphs' => [
                'Rapor konusu: '.$focus.'. Dönem / tarih: {{yayin_tarihi}}.',
                'Kurum: {{firma_unvani}}.',
            ]],
            ['heading' => 'Bulgular', 'paragraphs' => [
                'Uyum durumu, tespit edilen riskler ve iyileştirme alanları bu bölümde özetlenir.',
            ]],
            ['heading' => 'Tedbirler ve Aksiyonlar', 'paragraphs' => [
                'Teknik ve idari tedbirler ile sorumlu / termin bilgileri izlenir.',
            ]],
            ['heading' => 'Sonuç', 'paragraphs' => [
                'Rapor {{onaylayan}} tarafından onaylanır; takip {{hazirlayan}} sorumluluğundadır.',
            ]],
        ]);
    }

    private function instruction(string $title, string $focus): string
    {
        return $this->build($title, [
            ['heading' => 'Amaç', 'paragraphs' => [
                'Bu talimat, '.$focus.' uygulamasında uyulacak adımları belirtir.',
            ]],
            ['heading' => 'Uygulama', 'paragraphs' => [
                'Çalışanlar talimata uygun hareket eder; tereddütte {{yetkili}} ile iletişime geçer.',
            ]],
            ['heading' => 'Kontrol', 'paragraphs' => [
                'Uygunsuzluklar kayıt altına alınır ve düzeltici faaliyet başlatılır.',
            ]],
        ]);
    }

    private function other(string $title, string $focus): string
    {
        return $this->build($title, [
            ['heading' => 'Amaç', 'paragraphs' => [
                'İşbu doküman, '.$focus.' bilgisini KVKK uyum dosyasında standartlaştırmak için hazırlanmıştır.',
            ]],
            ['heading' => 'İçerik', 'paragraphs' => [
                'Doküman {{firma_unvani}} süreçlerinde referans olarak kullanılır ve gerektiğinde güncellenir.',
            ]],
            ['heading' => 'Sorumluluk', 'paragraphs' => [
                'Güncellemeden {{yetkili}} sorumludur. İletişim: {{kvkk_eposta}}.',
            ]],
        ]);
    }
}
