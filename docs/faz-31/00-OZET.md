# FAZ 31 — ZIP Özeti

## Kapsam
Firma bazlı KVKK teslim klasörünün (01–15) ZIP paketi olarak üretilmesi ve indirilmesi.

## Çıktılar
- `delivery_packages` tablosu + `DeliveryPackage` model
- `DeliveryFolderLayout`, `DeliveryZipBuilder`, `DeliveryPackageService`
- Web: `/companies/{company}/delivery-packages`
- Yetkiler: `packages.view` / `packages.manage`
- Feature test: `DeliveryPackageTest`

## İş kuralları
- En az bir `generated` belge yoksa paket oluşturulamaz
- ZIP kökü: `KVKK360/{Firma}/01…15 …`
- Belgeler şablon kategorisine göre klasöre yerleşir (kamera→09, web/çerez→10, politika→02, …)
- Yeni hazır paket önceki `ready` paketleri `superseded` yapar
- `15 Teslim Dosyası/MANIFEST.txt` içerik özeti içerir

## Sonraki faz
FAZ 32 — AI Engine (tamamlandı) → FAZ 33 API
