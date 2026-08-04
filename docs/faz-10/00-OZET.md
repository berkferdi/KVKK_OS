# FAZ 10 — KVKK Analiz Sihirbazı

## Kapsam

Firma özelliklerinden Rule Engine ile yükümlülük/bulgu üretimi.

## Akış

1. Firma detay → Analiz Sihirbazı
2. `AnalysisWizardService::run`
3. `analysis_runs` + `analysis_findings`
4. Sonuç ekranı

## Yetkiler

- `analysis.view`, `analysis.run`
