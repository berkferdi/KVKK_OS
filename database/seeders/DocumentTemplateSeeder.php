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
        $tenants = Tenant::query()->get();
        if ($tenants->isEmpty()) {
            return;
        }

        $templates = $this->templateDefinitions();

        foreach ($tenants as $tenant) {
            $this->seedForTenant($tenant, $templates);
        }
    }

    /**
     * @return list<array{code: string, title: string, category: TemplateCategory, description: string, body: string}>
     */
    public function templateDefinitions(): array
    {
        return [
            [
                'code' => 'kamera_aydinlatma',
                'title' => 'Kamera Aydınlatma Metni',
                'category' => TemplateCategory::Camera,
                'description' => 'Kamera sistemleri için KVKK aydınlatma metni (tam metin)',
                'body' => <<<'TXT'
KAMERA AYDINLATMA METNİ

KAMERA KAYITLARINA İLİŞKİN 6698 SAYILI KİŞİSEL VERİLERİN KORUNMASI KANUNU KAPSAMINDAKİ AYDINLATMA METNİ

Veri Sorumlusu: {{firma_unvani}}
Adres: {{adres}}, {{ilce}} / {{sehir}}
MERSİS: {{mersis}}
Vergi No: {{vergi_no}} ({{vergi_dairesi}})
İletişim: {{eposta}} / {{telefon}}

{{firma_unvani}} bina girişlerinde ve bina içerisinde yapılan kişisel veri işleme faaliyetleri, Türkiye Cumhuriyeti Anayasası’na, 6698 sayılı Kişisel Verilerin Korunması Kanunu’na (KVKK) ve ilgili diğer mevzuata uygun bir biçimde yürütülmektedir.

Kamera ile İzleme Faaliyetinin Yasal Dayanağı ve Kişisel Verilerin Toplanma Yöntemi

Söz konusu kişisel veriler; işyeri, çalışan, ziyaretçi ve tesis güvenliğinin sağlanması amacıyla, KVKK’nın 5. maddesinde yer alan “veri sorumlusunun hukuki yükümlülüğünü yerine getirebilmesi için zorunlu olması” ve “ilgili kişinin temel hak ve özgürlüklerine zarar vermemek kaydıyla, veri sorumlusunun meşru menfaatleri için veri işlenmesinin zorunlu olması” hukuki sebeplerine dayanarak otomatik yolla işlenmektedir.

{{firma_unvani}} hizmet alanında bulunan {{kamera_alanlari}} bölgelerinde toplam {{kamera_sayisi}} adet güvenlik kamerası vasıtasıyla ve bina/tesis güvenliğinin sağlanması amacıyla görüntü kaydı yapılmaktadır. Kayıt işlemi {{firma_unvani}} tarafından denetlenmektedir.

KVK Hukukuna Göre Güvenlik Kamerası ile İzleme Faaliyeti Yürütülmesi

{{firma_unvani}}, bina ve tesis güvenliğinin sağlanması amacıyla, yürürlükte bulunan ilgili mevzuatta öngörülen amaçlarla ve KVK Kanunu’nda sayılan kişisel veri işleme şartlarına uygun olarak güvenlik kamerası izleme faaliyetinde bulunmaktadır.

Kamera ile İzleme Faaliyetinin Duyurulması

KVK Kanunu’nun 10. maddesine uygun olarak kişisel veri sahibi aydınlatılmaktadır. {{firma_unvani}}, kamera ile izleme faaliyetine ilişkin olarak birden fazla yöntem ile bildirimde bulunmaktadır. Böylelikle kişisel veri sahibinin temel hak ve özgürlüklerine zarar verilmesinin engellenmesi, şeffaflığın ve kişisel veri sahibinin aydınlatılmasının sağlanması amaçlanmaktadır. Bu kapsamda izlemenin yapıldığı alanların girişlerine bildirim yazısı asılmaktadır.

Kamera ile İzleme Faaliyetinin Yürütülme Amacı ve İşlenmesi

{{firma_unvani}}, KVK Kanunu’nun 4. maddesine uygun olarak kişisel verileri işlendikleri amaçla bağlantılı, sınırlı ve ölçülü bir biçimde işlemektedir. Kapalı devre kamera ile izleme faaliyetinin sürdürülmesindeki amaç; işyeri güvenliğinin sağlanması, hırsızlık ve vandalizmin önlenmesi, olası olayların tespiti ve çalışan/ziyaretçi güvenliğinin korunması ile sınırlıdır. Bu doğrultuda güvenlik kameralarının izleme alanları, sayısı ve izleme süreleri güvenlik amacına ulaşmak için yeterli ve bu amaçla sınırlı olarak uygulamaya alınmaktadır. Kişinin mahremiyetine güvenlik amaçlarını aşan şekilde müdahale sonucu doğurabilecek alanlarda (örneğin soyunma odaları, tuvaletler ve benzeri mahrem alanlar) izleme yapılmamaktadır.

Elde Edilen Verilerin Güvenliğinin Sağlanması

{{firma_unvani}} tarafından KVK Kanunu’nun 12. maddesine uygun olarak, kamera ile izleme faaliyeti sonucunda elde edilen kişisel verilerin güvenliğinin sağlanması için gerekli teknik ve idari tedbirler alınmaktadır.

Kamera ile İzleme Faaliyeti ile Elde Edilen Kişisel Verilerin Muhafaza Süresi

{{firma_unvani}} tarafından kamera ile elde edilen kişisel verilerin saklanma süresi {{kamera_saklama_gun}} gündür. Saklama süresinin sonunda kayıtlar silinir, yok edilir veya anonim hale getirilir; hukuki yükümlülük veya meşru menfaat kapsamında daha uzun süre saklanması gereken kayıtlar ilgili süre boyunca muhafaza edilebilir.

Elde Edilen Kişisel Verilerin Hangi Amaçlarla Kimlere Aktarılacağı

İzleme sonucunda elde edilen bilgilere sınırlı sayıda yetkili çalışanın erişimi bulunmaktadır. Kamera görüntüleri, {{firma_unvani}} bina/tesis güvenliğinin sağlanması amacıyla mevzuata uygun olarak yetkili kamu kurumlarına KVK Kanunu’nun 8. maddesinde belirtilen kişisel veri işleme şartları ve amaçları çerçevesinde aktarılabilmektedir. Kayıtlara erişimi olan sınırlı sayıda kişi gizlilik taahhütnamesi ile eriştiği verilerin gizliliğini koruyacağını beyan etmektedir. Söz konusu kişisel veriler hukuki uyuşmazlıkların giderilmesi veya ilgili mevzuatı gereği talep halinde adli makamlar veya ilgili kolluk kuvvetlerine aktarılabilecektir.

İlgili Kişinin Hakları

KVKK’nın 11. maddesi uyarınca ilgili kişi; kişisel verilerinin işlenip işlenmediğini öğrenme, işlenmişse buna ilişkin bilgi talep etme, işlenme amacını ve amacına uygun kullanılıp kullanılmadığını öğrenme, yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme, eksik veya yanlış işlenmiş olması hâlinde bunların düzeltilmesini isteme, KVKK’nın 7. maddesinde öngörülen şartlar çerçevesinde silinmesini veya yok edilmesini isteme ve bu işlemlerin aktarıldığı üçüncü kişilere bildirilmesini isteme, otomatik sistemler ile analiz edilmesi suretiyle aleyhine bir sonucun ortaya çıkmasına itiraz etme ve kanuna aykırı işlenmesi sebebiyle zarara uğraması hâlinde zararın giderilmesini talep etme haklarına sahiptir.

Başvurular {{eposta}} adresine veya {{adres}} adresine yazılı olarak iletilebilir.

Yetkili: {{yetkili}} ({{yetkili_unvan}})
TXT,
            ],
            [
                'code' => 'web_aydinlatma',
                'title' => 'Web Aydınlatma Metni',
                'category' => TemplateCategory::Web,
                'description' => 'Web sitesi ziyaretçileri için aydınlatma (tam metin)',
                'body' => <<<'TXT'
WEB SİTESİ AYDINLATMA METNİ

6698 SAYILI KİŞİSEL VERİLERİN KORUNMASI KANUNU KAPSAMINDA AYDINLATMA METNİ

Veri Sorumlusu: {{firma_unvani}}
Ticaret Unvanı: {{ticaret_unvani}}
Adres: {{adres}}, {{ilce}} / {{sehir}}
MERSİS: {{mersis}}
Vergi No: {{vergi_no}} ({{vergi_dairesi}})
İletişim: {{eposta}} / {{telefon}}

{{firma_unvani}} (“Şirket”) olarak web sitemizi ziyaret eden ilgili kişileri, 6698 sayılı Kişisel Verilerin Korunması Kanunu (“KVKK”) kapsamında bilgilendirmek amacıyla işbu aydınlatma metnini hazırlamıştır.

İşlenen Kişisel Veriler

Web sitemizin ziyaret edilmesi sırasında; kimlik, iletişim, işlem güvenliği, pazarlama ve müşteri işlem verileri ile çerezler aracılığıyla elde edilen trafik verileri işlenebilmektedir.

Kişisel Verilerin İşlenme Amaçları

Kişisel verileriniz; site işlevselliğinin sağlanması, kullanıcı deneyiminin iyileştirilmesi, bilgi güvenliğinin temini, yasal yükümlülüklerin yerine getirilmesi, talep ve şikayetlerin karşılanması ve meşru menfaatlerimizin korunması amaçlarıyla işlenmektedir.

Hukuki Sebepler

Kişisel verileriniz KVKK’nın 5. ve 6. maddelerinde belirtilen; kanunlarda açıkça öngörülmesi, sözleşmenin kurulması veya ifası, hukuki yükümlülüğün yerine getirilmesi, ilgili kişinin temel hak ve özgürlüklerine zarar vermemek kaydıyla meşru menfaat ve açık rıza hukuki sebeplerine dayanılarak işlenmektedir.

Aktarım

Kişisel verileriniz, yukarıda belirtilen amaçlarla sınırlı olarak hizmet alınan tedarikçilere, yetkili kamu kurum ve kuruluşlarına ve kanunen yetkili özel kişilere aktarılabilecektir.

Saklama Süresi

Kişisel verileriniz, işlendikleri amaç için gerekli olan süre boyunca ve ilgili mevzuatta öngörülen zamanaşımı süreleri boyunca saklanır; süre sonunda silinir, yok edilir veya anonim hale getirilir.

İlgili Kişi Hakları

KVKK’nın 11. maddesi kapsamındaki haklarınızı {{eposta}} üzerinden veya {{adres}} adresine yazılı başvuru ile kullanabilirsiniz.

Yetkili: {{yetkili}} ({{yetkili_unvan}})
TXT,
            ],
            [
                'code' => 'gizlilik_politikasi',
                'title' => 'Gizlilik Politikası',
                'category' => TemplateCategory::Policy,
                'description' => 'Genel gizlilik politikası (tam metin)',
                'body' => <<<'TXT'
GİZLİLİK POLİTİKASI

{{firma_unvani}} (“Şirket”) olarak kişisel verilerinizin gizliliğine ve güvenliğine önem veriyoruz. İşbu Gizlilik Politikası, faaliyetlerimiz kapsamında işlenen kişisel verilere ilişkin genel esasları açıklamaktadır.

1. Veri Sorumlusu
{{firma_unvani}}
Adres: {{adres}}, {{ilce}} / {{sehir}}
MERSİS: {{mersis}}
İletişim: {{eposta}} / {{telefon}}

2. Faaliyet Alanı
{{faaliyet}}

3. Toplanan Veriler
Kimlik, iletişim, müşteri işlem, işlem güvenliği, görsel/işitsel kayıtlar ve mevzuatın zorunlu kıldığı diğer kişisel veri kategorileri işlenebilir.

4. İşleme Amaçları
Hizmet sunumu, sözleşme süreçlerinin yürütülmesi, müşteri ilişkileri, güvenlik, yasal yükümlülüklerin yerine getirilmesi ve meşru menfaatlerimizin özel hayatın gizliliğine zarar vermeyecek şekilde korunması.

5. Hukuki Sebepler
KVKK md. 5 ve 6’da sayılan hukuki sebepler çerçevesinde işleme yapılır.

6. Aktarım ve Saklama
Veriler yalnızca amaçla sınırlı olarak yetkili kişi ve kurumlara aktarılabilir; gerekli süre boyunca saklanır.

7. Güvenlik
KVKK md. 12 uyarınca teknik ve idari tedbirler alınır.

8. Haklarınız
KVKK md. 11 kapsamındaki haklarınız için {{eposta}} adresine başvurabilirsiniz.

Yetkili: {{yetkili}} — {{yetkili_unvan}}
TXT,
            ],
            [
                'code' => 'cerez_politikasi',
                'title' => 'Çerez Politikası',
                'category' => TemplateCategory::Cookie,
                'description' => 'Çerez kullanım politikası (tam metin)',
                'body' => <<<'TXT'
ÇEREZ POLİTİKASI

{{firma_unvani}} (“Şirket”) web sitesinde kullanılan çerezlere ilişkin bilgilendirme metnidir.

Veri Sorumlusu
{{firma_unvani}}
Adres: {{adres}}, {{ilce}} / {{sehir}}
MERSİS: {{mersis}}
İletişim: {{eposta}}

Çerez Nedir?
Çerezler, ziyaret ettiğiniz internet siteleri tarafından tarayıcınıza veya cihazınıza kaydedilen küçük metin dosyalarıdır.

Kullanılan Çerez Türleri
- Zorunlu çerezler: Sitenin temel işlevleri için gereklidir.
- Performans / analitik çerezler: Site kullanımının ölçülmesi amacıyla kullanılabilir.
- İşlevsel çerezler: Tercihlerinizin hatırlanması için kullanılabilir.

Hukuki Sebep ve Amaç
Zorunlu çerezler meşru menfaat / hizmetin sunulması kapsamında; diğer çerezler ise ilgili kişinin açık rızasına dayanılarak kullanılabilir. Amaç; site güvenliği, performans ölçümü ve kullanıcı deneyiminin iyileştirilmesidir.

Çerezleri Yönetme
Tarayıcı ayarlarından çerezleri silebilir veya engelleyebilirsiniz. Bazı çerezlerin engellenmesi site işlevlerini etkileyebilir.

Haklarınız
KVKK md. 11 kapsamındaki haklarınız için {{eposta}} adresine başvurabilirsiniz.

Yetkili: {{yetkili}} ({{yetkili_unvan}})
TXT,
            ],
        ];
    }

    /**
     * @param  list<array{code: string, title: string, category: TemplateCategory, description: string, body: string}>  $templates
     */
    public function seedForTenant(Tenant $tenant, array $templates): void
    {
        foreach ($templates as $data) {
            $template = DocumentTemplate::query()->firstOrNew([
                'tenant_id' => $tenant->id,
                'code' => $data['code'],
            ]);

            // Kullanıcının elle özelleştirdiği şablonları ezme.
            if ($template->exists && $template->source !== 'seed') {
                continue;
            }

            if (! $template->exists) {
                $template->uuid = (string) Str::uuid();
            }

            $bodyChanged = $template->exists && (string) $template->body !== $data['body'];

            $template->fill([
                'title' => $data['title'],
                'category' => $data['category'],
                'description' => $data['description'],
                'body' => $data['body'],
                'output_formats' => ['text', 'docx', 'pdf'],
                'version' => $bodyChanged
                    ? max(1, (int) $template->version + 1)
                    : max(1, (int) ($template->version ?: 1)),
                'is_active' => true,
                'source' => 'seed',
                'metadata' => [],
            ]);
            $template->save();
        }
    }
}
