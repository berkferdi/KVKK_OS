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
KAMERA KAYITLARINA İLİŞKİN 6698 SAYILI KİŞİSEL VERİLERİN KORUNMASI KANUNU KAPSAMINDAKİ AYDINLATMA METNİ

Veri Sorumlusu: {{firma_unvani}}
Adres: {{adres}}, {{ilce}} / {{sehir}}
MERSİS: {{mersis}}
Vergi No: {{vergi_no}} ({{vergi_dairesi}})
İletişim: {{eposta}} / {{telefon}}

{{firma_unvani}} bina girişlerinde ve bina içerisinde yapılan kişisel veri işleme faaliyetleri, Türkiye Cumhuriyeti Anayasası’na, 6698 sayılı Kişisel Verilerin Korunması Kanunu’na (KVKK) ve ilgili diğer mevzuata uygun bir biçimde yürütülmektedir.

1. Yasal Dayanak ve Toplama Yöntemi
Söz konusu kişisel veriler; işyeri, çalışan, ziyaretçi ve tesis güvenliğinin sağlanması amacıyla, KVKK’nın 5. maddesinde yer alan “veri sorumlusunun hukuki yükümlülüğünü yerine getirebilmesi için zorunlu olması” ve “ilgili kişinin temel hak ve özgürlüklerine zarar vermemek kaydıyla, veri sorumlusunun meşru menfaatleri için veri işlenmesinin zorunlu olması” hukuki sebeplerine dayanarak otomatik yolla işlenmektedir.

{{firma_unvani}} hizmet alanında bulunan {{kamera_alanlari}} bölgelerinde toplam {{kamera_sayisi}} adet güvenlik kamerası vasıtasıyla ve bina/tesis güvenliğinin sağlanması amacıyla görüntü kaydı yapılmaktadır. Kayıt işlemi {{firma_unvani}} tarafından denetlenmektedir.

2. Güvenlik Kamerası ile İzleme Faaliyeti
{{firma_unvani}}, bina ve tesis güvenliğinin sağlanması amacıyla, yürürlükte bulunan ilgili mevzuatta öngörülen amaçlarla ve KVK Kanunu’nda sayılan kişisel veri işleme şartlarına uygun olarak güvenlik kamerası izleme faaliyetinde bulunmaktadır.

3. İzleme Faaliyetinin Duyurulması
KVK Kanunu’nun 10. maddesine uygun olarak kişisel veri sahibi aydınlatılmaktadır. {{firma_unvani}}, kamera ile izleme faaliyetine ilişkin olarak birden fazla yöntem ile bildirimde bulunmaktadır. Bu kapsamda izlemenin yapıldığı alanların girişlerine bildirim yazısı asılmaktadır.

4. İzleme Amacı ve İşleme Esasları
{{firma_unvani}}, KVK Kanunu’nun 4. maddesine uygun olarak kişisel verileri işlendikleri amaçla bağlantılı, sınırlı ve ölçülü bir biçimde işlemektedir. Kapalı devre kamera ile izleme faaliyetinin sürdürülmesindeki amaç; işyeri güvenliğinin sağlanması, hırsızlık ve vandalizmin önlenmesi, olası olayların tespiti ve çalışan/ziyaretçi güvenliğinin korunması ile sınırlıdır. Kişinin mahremiyetine güvenlik amaçlarını aşan şekilde müdahale sonucu doğurabilecek alanlarda (örneğin soyunma odaları, tuvaletler ve benzeri mahrem alanlar) izleme yapılmamaktadır.

5. Verilerin Güvenliği
{{firma_unvani}} tarafından KVK Kanunu’nun 12. maddesine uygun olarak, kamera ile izleme faaliyeti sonucunda elde edilen kişisel verilerin güvenliğinin sağlanması için gerekli teknik ve idari tedbirler alınmaktadır.

6. Muhafaza Süresi
{{firma_unvani}} tarafından kamera ile elde edilen kişisel verilerin saklanma süresi {{kamera_saklama_gun}} gündür. Saklama süresinin sonunda kayıtlar silinir, yok edilir veya anonim hale getirilir.

7. Aktarım
İzleme sonucunda elde edilen bilgilere sınırlı sayıda yetkili çalışanın erişimi bulunmaktadır. Kamera görüntüleri, {{firma_unvani}} bina/tesis güvenliğinin sağlanması amacıyla mevzuata uygun olarak yetkili kamu kurumlarına KVK Kanunu’nun 8. maddesinde belirtilen kişisel veri işleme şartları ve amaçları çerçevesinde aktarılabilmektedir. Söz konusu kişisel veriler hukuki uyuşmazlıkların giderilmesi veya ilgili mevzuatı gereği talep halinde adli makamlar veya ilgili kolluk kuvvetlerine aktarılabilecektir.

8. İlgili Kişinin Hakları
KVKK’nın 11. maddesi uyarınca ilgili kişi; kişisel verilerinin işlenip işlenmediğini öğrenme, işlenmişse buna ilişkin bilgi talep etme, işlenme amacını ve amacına uygun kullanılıp kullanılmadığını öğrenme, yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme, eksik veya yanlış işlenmiş olması hâlinde bunların düzeltilmesini isteme, KVKK’nın 7. maddesinde öngörülen şartlar çerçevesinde silinmesini veya yok edilmesini isteme, bu işlemlerin aktarıldığı üçüncü kişilere bildirilmesini isteme, otomatik sistemler ile analiz edilmesi suretiyle aleyhine bir sonucun ortaya çıkmasına itiraz etme ve kanuna aykırı işlenmesi sebebiyle zarara uğraması hâlinde zararın giderilmesini talep etme haklarına sahiptir.

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

Veri Sorumlusu: {{firma_unvani}}
Ticaret Unvanı: {{ticaret_unvani}}
Adres: {{adres}}, {{ilce}} / {{sehir}}
MERSİS: {{mersis}}
Vergi No: {{vergi_no}} ({{vergi_dairesi}})
İletişim: {{eposta}} / {{telefon}}

{{firma_unvani}} (“Şirket”) olarak web sitemizi ziyaret eden ilgili kişileri, 6698 sayılı Kişisel Verilerin Korunması Kanunu (“KVKK”) kapsamında bilgilendirmek amacıyla işbu aydınlatma metnini hazırlamıştır.

1. İşlenen Kişisel Veriler
Web sitemizin ziyaret edilmesi sırasında; kimlik, iletişim, işlem güvenliği, pazarlama ve müşteri işlem verileri ile çerezler aracılığıyla elde edilen trafik verileri işlenebilmektedir.

2. İşlenme Amaçları
Kişisel verileriniz; site işlevselliğinin sağlanması, kullanıcı deneyiminin iyileştirilmesi, bilgi güvenliğinin temini, yasal yükümlülüklerin yerine getirilmesi, talep ve şikayetlerin karşılanması ve meşru menfaatlerimizin korunması amaçlarıyla işlenmektedir.

3. Hukuki Sebepler
Kişisel verileriniz KVKK’nın 5. ve 6. maddelerinde belirtilen; kanunlarda açıkça öngörülmesi, sözleşmenin kurulması veya ifası, hukuki yükümlülüğün yerine getirilmesi, ilgili kişinin temel hak ve özgürlüklerine zarar vermemek kaydıyla meşru menfaat ve açık rıza hukuki sebeplerine dayanılarak işlenmektedir.

4. Aktarım
Kişisel verileriniz, yukarıda belirtilen amaçlarla sınırlı olarak hizmet alınan tedarikçilere, yetkili kamu kurum ve kuruluşlarına ve kanunen yetkili özel kişilere aktarılabilecektir.

5. Saklama Süresi
Kişisel verileriniz, işlendikleri amaç için gerekli olan süre boyunca ve ilgili mevzuatta öngörülen zamanaşımı süreleri boyunca saklanır; süre sonunda silinir, yok edilir veya anonim hale getirilir.

6. İlgili Kişi Hakları
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

Veri Sorumlusu: {{firma_unvani}}
Adres: {{adres}}, {{ilce}} / {{sehir}}
MERSİS: {{mersis}}
İletişim: {{eposta}}

1. Çerez Nedir?
Çerezler, ziyaret ettiğiniz internet siteleri tarafından tarayıcınıza veya cihazınıza kaydedilen küçük metin dosyalarıdır.

2. Kullanılan Çerez Türleri
Zorunlu çerezler: Sitenin temel işlevleri için gereklidir.
Performans / analitik çerezler: Site kullanımının ölçülmesi amacıyla kullanılabilir.
İşlevsel çerezler: Tercihlerinizin hatırlanması için kullanılabilir.

3. Hukuki Sebep ve Amaç
Zorunlu çerezler meşru menfaat / hizmetin sunulması kapsamında; diğer çerezler ise ilgili kişinin açık rızasına dayanılarak kullanılabilir. Amaç; site güvenliği, performans ölçümü ve kullanıcı deneyiminin iyileştirilmesidir.

4. Çerezleri Yönetme
Tarayıcı ayarlarından çerezleri silebilir veya engelleyebilirsiniz. Bazı çerezlerin engellenmesi site işlevlerini etkileyebilir.

5. Haklarınız
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
