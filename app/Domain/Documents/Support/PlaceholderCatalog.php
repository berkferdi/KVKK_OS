<?php

namespace App\Domain\Documents\Support;

/**
 * Merkezi KVKK belge placeholder kataloğu.
 */
final class PlaceholderCatalog
{
    /**
     * Üretimde boşsa hata sayılan anahtarlar.
     *
     * @return list<string>
     */
    public static function required(): array
    {
        return [
            'firma_unvani',
        ];
    }

    /**
     * @return array<string, string> key => açıklama
     */
    public static function definitions(): array
    {
        return [
            // Firma
            'firma_unvani' => 'Resmi unvan',
            'ticaret_unvani' => 'Ticaret unvanı',
            'vergi_no' => 'Vergi numarası',
            'vergi_dairesi' => 'Vergi dairesi',
            'mersis' => 'MERSİS numarası',
            'adres' => 'Adres',
            'ilce' => 'İlçe',
            'sehir' => 'İl',
            'posta_kodu' => 'Posta kodu',
            'ulke' => 'Ülke',
            'telefon' => 'Telefon',
            'eposta' => 'E-posta',
            'web' => 'Web sitesi',
            'kep' => 'KEP adresi',
            'kvkk_eposta' => 'KVKK iletişim e-postası',
            'yetkili' => 'Yetkili kişi',
            'yetkili_unvan' => 'Yetkili unvanı',
            'faaliyet' => 'Faaliyet özeti',
            'nace_kodu' => 'NACE kodu',
            'kurulus_tarihi' => 'Kuruluş tarihi',
            'calisan_sayisi' => 'Çalışan sayısı',
            'sgk_sicil_no' => 'SGK sicil no',
            'ticaret_sicil_no' => 'Ticaret sicil no',
            // Kamera
            'kamera_sayisi' => 'Kamera sayısı',
            'kamera_alanlari' => 'Kamera alanları',
            'kamera_saklama_gun' => 'Kamera saklama (gün)',
            'kamera_amaci' => 'Kamera izleme amacı',
            // Veri sorumlusu
            'veri_sorumlusu' => 'Veri sorumlusu',
            'veri_sorumlusu_adres' => 'Veri sorumlusu adresi',
            'veri_sorumlusu_eposta' => 'Veri sorumlusu e-posta',
            'veri_sorumlusu_telefon' => 'Veri sorumlusu telefon',
            // KVKK başvuru
            'kvkk_basvuru_adresi' => 'KVKK başvuru adresi',
            'kvkk_basvuru_eposta' => 'KVKK başvuru e-posta',
            'kvkk_basvuru_kep' => 'KVKK başvuru KEP',
            // Doküman
            'dokuman_no' => 'Doküman numarası',
            'versiyon' => 'Versiyon',
            'revizyon_no' => 'Revizyon numarası',
            'revizyon_tarihi' => 'Revizyon tarihi',
            'yayin_tarihi' => 'Yayın tarihi',
            'onaylayan' => 'Onaylayan',
            'hazirlayan' => 'Hazırlayan',
            'dokuman_baslik' => 'Doküman başlığı',
            'yururluk_durumu' => 'Yürürlük durumu',
            // İK
            'ise_giris_tarihi' => 'İşe giriş tarihi',
            'departman' => 'Departman',
            'pozisyon' => 'Pozisyon',
            // Tedarikçi
            'tedarikci_unvani' => 'Tedarikçi unvanı',
            'tedarikci_yetkili' => 'Tedarikçi yetkilisi',
        ];
    }
}
