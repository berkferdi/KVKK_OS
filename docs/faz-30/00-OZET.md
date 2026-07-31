# FAZ 30 — PDF Özeti

## Kapsam
Belge motoru çıktılarına mPDF ile `.pdf` üretimi ve indirme.

## Çıktılar
- `mpdf/mpdf` bağımlılığı
- `PdfDocumentWriter` + `PdfExportService`
- `generated_documents.pdf_path` kolonu
- Başarılı üretimde otomatik PDF yazımı
- İndirme: `/companies/{company}/generated-documents/{id}/download-pdf`
- Feature test: üretim + PDF indirme

## İş kuralları
- `failed` belgelerden PDF üretilemez / indirilemez
- Dosya yoksa indirmede yeniden üretilir (`ensurePdf`)
- Silmede PDF dosyası da kaldırılır
- Word (`file_path`) ile PDF (`pdf_path`) ayrı tutulur

## Sonraki faz
FAZ 31 — ZIP (tamamlandı) → FAZ 32 AI Engine
