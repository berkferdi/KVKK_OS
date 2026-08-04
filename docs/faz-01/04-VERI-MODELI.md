# Veri Modeli Taslağı (Kavramsal) — FAZ 02’ye girdi

## Platform

### tenants
id, uuid, name, slug, status, plan, settings(json), timestamps, soft deletes

### users
id, uuid, name, email, password, phone, is_active, last_login_at, timestamps, soft deletes

### tenant_user (pivot)
tenant_id, user_id, is_owner, timestamps

### Spatie tables
roles, permissions, model_has_*, role_has_* (+ teams/tenant kolonu)

### audit_logs
id, tenant_id nullable, user_id nullable, action, auditable_type, auditable_id, old_values, new_values, ip, user_agent, timestamps

## Organization

### companies
id, tenant_id, uuid, trade_name, title, tax_number, tax_office, mersis_number, nace_code, email, phone, address, city, district, authorized_person, authorized_title, activity_summary, has_camera, has_website, has_cookies, employee_count, status, metadata(json), timestamps, soft deletes

### branches
id, tenant_id, company_id, uuid, name, code, address, city, district, phone, is_hq, timestamps, soft deletes

## Compliance (ileriki faz iskeleti — FAZ 02’de yalnızca stub veya sonraki migrate)

### rules / rule_conditions / rule_actions
FAZ 13’te detay; FAZ 10’da minimal evaluator iskeleti.

### document_templates / generated_documents
FAZ 28+

## İndeksler

- Tüm tenant-scoped tablolarda `(tenant_id, …)` composite index
- Unique: companies.tax_number scoped by tenant (opsiyonel iş kuralı)
- users.email global unique
