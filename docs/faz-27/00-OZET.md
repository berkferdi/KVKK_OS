# FAZ 27 — Eğitim Özeti

## Kapsam
Şirket bazlı KVKK eğitim kayıtları: tür, yöntem, plan/gerçekleşme, katılımcı bilgisi ve yıllık tekrar vadesi.

## Çıktılar
- `training_records` migration + `TrainingRecord` model/enum/factory
- `TrainingRecordService` / `TrainingRecordRepository`
- Web CRUD: `/companies/{company}/trainings`
- Policy: `trainings.view` / `trainings.manage`
- Feature test: `TrainingRecordTest`

## İş kuralları
- Durum `completed` ve `next_training_due_at` boşsa: `conducted_at + 1 yıl`
- Plan gecikmesi: `planned` iken `planned_at` geçmişse
- Sonraki eğitim gecikmesi: `next_training_due_at` geçmişse (iptal hariç)

## Sonraki faz
FAZ 28 — Belge Motoru
