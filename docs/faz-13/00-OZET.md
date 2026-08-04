# FAZ 13 — Rule Engine (iskelet)

## Prensip

KVKK kuralları kodda hard-code edilmez; `compliance_rules` tablosunda tutulur.

## Şema

- `conditions` JSON: `[{field, operator, value}]`
- `actions` JSON: `[{type, code, title, severity, ...}]`

## Operatörler

`eq`, `neq`, `gt`, `gte`, `lt`, `lte`, `truthy`, `falsy`

## Seed örnekleri

Kamera / web / çerez / personel≥50

## Servis

`App\Application\Services\Compliance\RuleEngine`
