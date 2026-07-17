# Yol Haritası ve Modül Bağımlılıkları

## Faz Durumu Kaynağı

Canlı takip: [`docs/PHASE_STATUS.md`](../PHASE_STATUS.md)

## Bağımlılık Diyagramı (özet)

```mermaid
flowchart TD
  F01[01 Analiz] --> F02[02 Database]
  F02 --> F03[03 Auth]
  F03 --> F04[04 Dashboard]
  F03 --> F05[05 Firma]
  F05 --> F06[06 Şube]
  F03 --> F07[07 Kullanıcı]
  F07 --> F08[08 Roller]
  F08 --> F09[09 Yetkiler]
  F05 --> F10[10 Analiz Sihirbazı]
  F10 --> F13[13 Rule Engine]
  F13 --> F11[11 Envanter]
  F13 --> F12[12 Risk]
  F05 --> F14[14-22 Varlıklar]
  F11 --> F23[23 VERBİS]
  F14 --> F28[28 Belge Motoru]
  F28 --> F29[29 Word]
  F28 --> F30[30 PDF]
  F28 --> F31[31 ZIP]
  F10 --> F32[32 AI Engine]
  F03 --> F33[33 API]
  F04 --> F34[34 Bildirimler]
  F02 --> F35[35 Backup]
  F35 --> F36[36 Deployment]
```

## Sprint Tanımı

Her faz sprint’i şunları içerir (uygunsa):

Analiz → Kod → Migration → Seeder → Model → Controller → Service → Repository → View → Routes → Policy → Request → Test → README → CHANGELOG
