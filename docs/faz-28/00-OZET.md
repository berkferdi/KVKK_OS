# FAZ 28 — Belge Motoru Özeti

## Kapsam
Placeholder tabanlı belge motoru temeli: şablon kataloğu, firma alanlarından placeholder çözümü, metin üretimi ve önizleme. Word/PDF/ZIP sonraki fazlara bırakıldı.

## Çıktılar
- `document_templates` + `generated_documents` tabloları
- `PlaceholderResolver`, `DocumentRenderer`, `DocumentTemplateService`, `DocumentGenerationService`
- Tenant şablon CRUD: `/document-templates`
- Firma üretimi: `/companies/{company}/generated-documents`
- Seed şablonları: `kamera_aydinlatma`, `web_aydinlatma`, `gizlilik_politikasi`, `cerez_politikasi`
- Yetkiler: `templates.view` / `templates.manage`
- Feature test: `DocumentEngineTest`

## İş kuralları
- Şablondaki `{{placeholder}}` alanları firma kaydından doldurulur
- Eksik/boş zorunlu placeholder varsa üretim `failed` kaydedilir
- Başarılı üretimde aynı şablon+firma için önceki `generated` kayıtlar `superseded` olur

## Sonraki faz
FAZ 29 — Word (tamamlandı) → FAZ 30 PDF
