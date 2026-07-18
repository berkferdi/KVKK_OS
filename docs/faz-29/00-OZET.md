# FAZ 29 — Word Özeti

## Kapsam
Belge motoru çıktılarına PHPWord ile `.docx` üretimi ve indirme.

## Çıktılar
- `phpoffice/phpword` bağımlılığı
- `WordDocumentWriter` + `WordExportService`
- Başarılı üretimde otomatik `.docx` yazımı (`storage` local disk)
- İndirme: `/companies/{company}/generated-documents/{id}/download`
- Feature test: üretim + Word indirme

## İş kuralları
- `failed` belgelerden Word üretilemez / indirilemez
- Dosya yoksa indirmede yeniden üretilir (`ensureDocx`)
- Silmede Word dosyası da kaldırılır

## Sonraki faz
FAZ 30 — PDF
