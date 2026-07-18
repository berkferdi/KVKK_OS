# FAZ 26 — Denetim Özeti

## Kapsam
Şirket bazlı KVKK uyum denetimleri (iç/dış/kurum/kamera vb.). Sistem `audit_logs` kaydı değildir; regulatory süreç modülüdür.

## Çıktılar
- `compliance_audits` migration + `ComplianceAudit` model/enum/factory
- `ComplianceAuditService` / `ComplianceAuditRepository`
- Web CRUD: `/companies/{company}/audits`
- Policy: `audits.view` / `audits.manage`
- Feature test: `ComplianceAuditTest`

## İş kuralları
- Durum `completed` ve `next_audit_due_at` boşsa: `completed_at + 1 yıl`
- Plan gecikmesi: `planned` iken `planned_at` geçmişse
- Sonraki denetim gecikmesi: `next_audit_due_at` geçmişse (iptal hariç)

## Sonraki faz
FAZ 27 — Eğitim
