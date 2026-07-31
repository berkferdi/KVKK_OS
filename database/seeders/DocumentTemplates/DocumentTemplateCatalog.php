<?php

namespace Database\Seeders\DocumentTemplates;

use App\Domain\Documents\Enums\TemplateCategory;

/**
 * KVKK danışmanlık belge şablon kataloğu (~140+).
 */
final class DocumentTemplateCatalog
{
    /**
     * @return list<array{code: string, title: string, category: TemplateCategory, description: string, focus: string}>
     */
    public static function all(): array
    {
        return array_merge(
            self::corporate(),
            self::policy(),
            self::disclosure(),
            self::consent(),
            self::forms(),
            self::contracts(),
            self::commitments(),
            self::procedures(),
            self::inventories(),
            self::reports(),
            self::instructions(),
            self::other(),
        );
    }

    /**
     * @param  list<array{0: string, 1: string, 2?: string}>  $rows
     * @return list<array{code: string, title: string, category: TemplateCategory, description: string, focus: string}>
     */
    private static function map(TemplateCategory $category, array $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                'code' => $row[0],
                'title' => $row[1],
                'category' => $category,
                'description' => $row[2] ?? ($row[1].' — KVKK uyum şablonu'),
                'focus' => $row[1],
            ];
        }

        return $out;
    }

    private static function corporate(): array
    {
        return self::map(TemplateCategory::Corporate, [
            ['kvkk_uyum_yol_haritasi', 'KVKK Uyum Yol Haritası'],
            ['kvkk_uyum_plani', 'KVKK Uyum Planı'],
            ['kvkk_organizasyon_semasi', 'KVKK Organizasyon Şeması'],
            ['kvkk_gorev_sorumluluk_matrisi', 'KVKK Görev ve Sorumluluk Matrisi'],
            ['kvkk_komitesi_gorev_tanimlari', 'KVKK Komitesi Görev Tanımları'],
            ['kvkk_yillik_faaliyet_plani', 'KVKK Yıllık Faaliyet Planı'],
            ['kvkk_ic_yonergesi', 'KVKK İç Yönergesi'],
            ['veri_koruma_organizasyon', 'Veri Koruma Organizasyon Dokümanı'],
            ['yonetim_taahhudu', 'Yönetim Taahhüdü'],
            ['kvkk_uyum_kontrol_listesi', 'KVKK Uyum Kontrol Listesi'],
        ]);
    }

    private static function policy(): array
    {
        return self::map(TemplateCategory::Policy, [
            ['kisisel_verilerin_korunmasi_politikasi', 'Kişisel Verilerin Korunması Politikası'],
            ['gizlilik_politikasi', 'Gizlilik Politikası'],
            ['cerez_politikasi', 'Çerez Politikası'],
            ['saklama_imha_politikasi', 'Saklama ve İmha Politikası'],
            ['bilgi_guvenligi_politikasi', 'Bilgi Güvenliği Politikası'],
            ['kamera_izleme_politikasi', 'Kamera İzleme Politikası'],
            ['internet_kullanim_politikasi', 'İnternet Kullanım Politikası'],
            ['eposta_kullanim_politikasi', 'E-Posta Kullanım Politikası'],
            ['mobil_cihaz_politikasi', 'Mobil Cihaz Politikası'],
            ['temiz_masa_politikasi', 'Temiz Masa Politikası'],
            ['sifre_politikasi', 'Şifre Politikası'],
            ['uzaktan_calisma_politikasi', 'Uzaktan Çalışma Politikası'],
            ['byod_politikasi', 'BYOD (Kendi Cihazını Getir) Politikası'],
            ['fiziksel_guvenlik_politikasi', 'Fiziksel Güvenlik Politikası'],
            ['yedekleme_politikasi', 'Yedekleme Politikası'],
            ['log_yonetimi_politikasi', 'Log Yönetimi Politikası'],
            ['erisim_kontrol_politikasi', 'Erişim Kontrol Politikası'],
            ['veri_siniflandirma_politikasi', 'Veri Sınıflandırma Politikası'],
            ['bulut_hizmetleri_politikasi', 'Bulut Hizmetleri Politikası'],
            ['arsivleme_politikasi', 'Arşivleme Politikası'],
        ]);
    }

    private static function disclosure(): array
    {
        return self::map(TemplateCategory::Disclosure, [
            ['web_aydinlatma', 'Web Sitesi Aydınlatma Metni'],
            ['calisan_aydinlatma', 'Çalışan Aydınlatma Metni'],
            ['musteri_aydinlatma', 'Müşteri Aydınlatma Metni'],
            ['potansiyel_musteri_aydinlatma', 'Potansiyel Müşteri Aydınlatma Metni'],
            ['tedarikci_aydinlatma', 'Tedarikçi Aydınlatma Metni'],
            ['is_ortagi_aydinlatma', 'İş Ortağı Aydınlatma Metni'],
            ['bayi_aydinlatma', 'Bayi Aydınlatma Metni'],
            ['ziyaretci_aydinlatma', 'Ziyaretçi Aydınlatma Metni'],
            ['kamera_aydinlatma', 'Kamera Aydınlatma Metni'],
            ['cagri_merkezi_aydinlatma', 'Çağrı Merkezi Aydınlatma Metni'],
            ['is_basvurusu_aydinlatma', 'İş Başvurusu Aydınlatma Metni'],
            ['stajyer_aydinlatma', 'Stajyer Aydınlatma Metni'],
            ['iletisim_formu_aydinlatma', 'İletişim Formu Aydınlatma Metni'],
            ['etkinlik_katilimcisi_aydinlatma', 'Etkinlik Katılımcısı Aydınlatma Metni'],
            ['cevrimici_toplanti_aydinlatma', 'Çevrimiçi Toplantı Aydınlatma Metni'],
        ]);
    }

    private static function consent(): array
    {
        return self::map(TemplateCategory::Consent, [
            ['calisan_acik_riza', 'Çalışan Açık Rıza'],
            ['musteri_acik_riza', 'Müşteri Açık Rıza'],
            ['tedarikci_acik_riza', 'Tedarikçi Açık Rıza'],
            ['ziyaretci_acik_riza', 'Ziyaretçi Açık Rıza'],
            ['is_basvurusu_acik_riza', 'İş Başvurusu Açık Rıza'],
            ['kamera_acik_riza', 'Kamera Açık Rıza'],
            ['ticari_elektronik_ileti_acik_riza', 'Ticari Elektronik İleti Açık Rıza'],
            ['sms_acik_riza', 'SMS Açık Rıza'],
            ['eposta_pazarlama_acik_riza', 'E-Posta Pazarlama Açık Rıza'],
            ['yurt_disi_aktarim_acik_riza', 'Yurt Dışı Veri Aktarım Açık Rıza'],
        ]);
    }

    private static function forms(): array
    {
        return self::map(TemplateCategory::Form, [
            ['kvkk_basvuru_formu', 'KVKK Başvuru Formu'],
            ['veri_duzeltme_talep_formu', 'Veri Düzeltme Talep Formu'],
            ['veri_silme_talep_formu', 'Veri Silme Talep Formu'],
            ['veri_yok_etme_talep_formu', 'Veri Yok Etme Talep Formu'],
            ['veri_anonimlestirme_talep_formu', 'Veri Anonimleştirme Talep Formu'],
            ['acik_riza_geri_alma_formu', 'Açık Rıza Geri Alma Formu'],
            ['veri_ihlali_bildirim_formu', 'Veri İhlali Bildirim Formu'],
            ['calisan_teslim_tutanagi', 'Çalışan Teslim Tutanağı'],
            ['egitim_katilim_formu', 'Eğitim Katılım Formu'],
            ['ziyaretci_formu', 'Ziyaretçi Formu'],
            ['kamera_kayit_talep_formu', 'Kamera Kayıt Talep Formu'],
            ['veri_aktarim_talep_formu', 'Veri Aktarım Talep Formu'],
            ['imha_tutanagi', 'İmha Tutanağı'],
            ['veri_imha_onay_formu', 'Veri İmha Onay Formu'],
            ['risk_degerlendirme_formu', 'Risk Değerlendirme Formu'],
        ]);
    }

    private static function contracts(): array
    {
        return self::map(TemplateCategory::Contract, [
            ['veri_isleyen_sozlesmesi', 'Veri İşleyen Sözleşmesi'],
            ['veri_paylasim_sozlesmesi', 'Veri Paylaşım Sözleşmesi'],
            ['gizlilik_sozlesmesi', 'Gizlilik Sözleşmesi'],
            ['calisan_gizlilik_sozlesmesi', 'Çalışan Gizlilik Sözleşmesi'],
            ['tedarikci_kvkk_sozlesmesi', 'Tedarikçi KVKK Sözleşmesi'],
            ['bayi_kvkk_sozlesmesi', 'Bayi KVKK Sözleşmesi'],
            ['alt_yuklenici_kvkk_sozlesmesi', 'Alt Yüklenici KVKK Sözleşmesi'],
            ['bulut_hizmeti_veri_isleme_sozlesmesi', 'Bulut Hizmeti Veri İşleme Sözleşmesi'],
            ['yazilim_hizmeti_veri_isleme_sozlesmesi', 'Yazılım Hizmeti Veri İşleme Sözleşmesi'],
            ['dis_hizmet_saglayici_sozlesmesi', 'Dış Hizmet Sağlayıcı Sözleşmesi'],
        ]);
    }

    private static function commitments(): array
    {
        return self::map(TemplateCategory::Commitment, [
            ['calisan_gizlilik_taahhutnamesi', 'Çalışan Gizlilik Taahhütnamesi'],
            ['bilgi_guvenligi_taahhutnamesi', 'Bilgi Güvenliği Taahhütnamesi'],
            ['tedarikci_gizlilik_taahhutnamesi', 'Tedarikçi Gizlilik Taahhütnamesi'],
            ['stajyer_gizlilik_taahhutnamesi', 'Stajyer Gizlilik Taahhütnamesi'],
            ['sistem_kullanim_taahhutnamesi', 'Sistem Kullanım Taahhütnamesi'],
            ['uzaktan_calisma_taahhutnamesi', 'Uzaktan Çalışma Taahhütnamesi'],
            ['yonetici_gizlilik_taahhutnamesi', 'Yönetici Gizlilik Taahhütnamesi'],
            ['veri_isleyen_taahhutnamesi', 'Veri İşleyen Taahhütnamesi'],
        ]);
    }

    private static function procedures(): array
    {
        return self::map(TemplateCategory::Procedure, [
            ['veri_ihlali_mudahale_proseduru', 'Veri İhlali Müdahale Prosedürü'],
            ['kvkk_basvuru_yonetim_proseduru', 'KVKK Başvuru Yönetim Prosedürü'],
            ['veri_saklama_proseduru', 'Veri Saklama Prosedürü'],
            ['veri_imha_proseduru', 'Veri İmha Prosedürü'],
            ['erisim_yetkilendirme_proseduru', 'Erişim Yetkilendirme Prosedürü'],
            ['log_yonetimi_proseduru', 'Log Yönetimi Prosedürü'],
            ['yedekleme_proseduru', 'Yedekleme Prosedürü'],
            ['geri_yukleme_proseduru', 'Geri Yükleme Prosedürü'],
            ['risk_yonetimi_proseduru', 'Risk Yönetimi Prosedürü'],
            ['fiziksel_guvenlik_proseduru', 'Fiziksel Güvenlik Prosedürü'],
            ['sistem_guncelleme_proseduru', 'Sistem Güncelleme Prosedürü'],
            ['olay_yonetimi_proseduru', 'Olay Yönetimi Prosedürü'],
        ]);
    }

    private static function inventories(): array
    {
        return self::map(TemplateCategory::Inventory, [
            ['kisisel_veri_isleme_envanteri', 'Kişisel Veri İşleme Envanteri'],
            ['verbis_envanteri', 'VERBİS Envanteri'],
            ['veri_varliklari_envanteri', 'Veri Varlıkları Envanteri'],
            ['veri_akis_envanteri', 'Veri Akış Envanteri'],
            ['yazilim_envanteri', 'Yazılım Envanteri'],
            ['donanim_envanteri', 'Donanım Envanteri'],
            ['ag_cihazlari_envanteri', 'Ağ Cihazları Envanteri'],
            ['sunucu_envanteri', 'Sunucu Envanteri'],
            ['lisans_envanteri', 'Lisans Envanteri'],
            ['bulut_hizmetleri_envanteri', 'Bulut Hizmetleri Envanteri'],
            ['tedarikci_envanteri', 'Tedarikçi Envanteri'],
            ['ucuncu_taraf_envanteri', 'Üçüncü Taraf Envanteri'],
            ['kamera_envanteri', 'Kamera Envanteri'],
            ['tasinabilir_medya_envanteri', 'Taşınabilir Medya Envanteri'],
            ['is_surecleri_envanteri', 'İş Süreçleri Envanteri'],
        ]);
    }

    private static function reports(): array
    {
        return self::map(TemplateCategory::Report, [
            ['kvkk_risk_analizi', 'KVKK Risk Analizi'],
            ['teknik_tedbirler_raporu', 'Teknik Tedbirler Raporu'],
            ['idari_tedbirler_raporu', 'İdari Tedbirler Raporu'],
            ['uyum_analiz_raporu', 'Uyum Analiz Raporu'],
            ['ic_denetim_raporu', 'İç Denetim Raporu'],
            ['dis_denetim_raporu', 'Dış Denetim Raporu'],
            ['egitim_raporu', 'Eğitim Raporu'],
            ['kamera_denetim_raporu', 'Kamera Denetim Raporu'],
            ['veri_ihlali_degerlendirme_raporu', 'Veri İhlali Değerlendirme Raporu'],
            ['yillik_kvkk_faaliyet_raporu', 'Yıllık KVKK Faaliyet Raporu'],
        ]);
    }

    private static function instructions(): array
    {
        return self::map(TemplateCategory::Instruction, [
            ['masaustu_temizligi_talimati', 'Masaüstü Temizliği Talimatı'],
            ['sifre_olusturma_talimati', 'Şifre Oluşturma Talimatı'],
            ['usb_kullanim_talimati', 'USB Kullanım Talimatı'],
            ['yazici_kullanim_talimati', 'Yazıcı Kullanım Talimatı'],
            ['evrak_imha_talimati', 'Evrak İmha Talimatı'],
            ['sunucu_odasi_giris_talimati', 'Sunucu Odası Giriş Talimatı'],
            ['yedekleme_talimati', 'Yedekleme Talimatı'],
            ['kamera_sistemleri_talimati', 'Kamera Sistemleri Talimatı'],
        ]);
    }

    private static function other(): array
    {
        return self::map(TemplateCategory::Other, [
            ['veri_saklama_takvimi', 'Veri Saklama Takvimi'],
            ['saklama_sureleri_tablosu', 'Saklama Süreleri Tablosu'],
            ['yetki_matrisi', 'Yetki Matrisi'],
            ['veri_akis_diyagrami', 'Veri Akış Diyagramı'],
            ['kisisel_veri_kategorileri_listesi', 'Kişisel Veri Kategorileri Listesi'],
            ['veri_isleme_faaliyetleri_listesi', 'Veri İşleme Faaliyetleri Listesi'],
            ['risk_envanteri', 'Risk Envanteri'],
            ['duzeltici_faaliyet_takip_formu', 'Düzeltici Faaliyet Takip Formu'],
            ['uyum_kontrol_listesi', 'Uyum Kontrol Listesi'],
            ['denetim_kontrol_listesi', 'Denetim Kontrol Listesi'],
            ['egitim_plani', 'Eğitim Planı'],
            ['egitim_katilim_listesi', 'Eğitim Katılım Listesi'],
            ['acil_durum_iletisim_listesi', 'Acil Durum İletişim Listesi'],
            ['veri_ihlali_olay_kayit_defteri', 'Veri İhlali Olay Kayıt Defteri'],
            ['verbis_kontrol_listesi', 'VERBİS Kontrol Listesi'],
        ]);
    }
}
