<?php

namespace Database\Seeders;

use App\Domain\Compliance\Models\ComplianceRule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComplianceRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'code' => 'camera_obligations',
                'name' => 'Kamera yükümlülükleri',
                'description' => 'Kamera varsa aydınlatma, risk, envanter ve VERBİS kayıtları oluşur.',
                'priority' => 10,
                'conditions' => [
                    ['field' => 'has_camera', 'operator' => 'eq', 'value' => true],
                ],
                'actions' => [
                    ['type' => 'require_document', 'code' => 'kamera_aydinlatma', 'title' => 'Kamera Aydınlatma Metni', 'severity' => 'high'],
                    ['type' => 'require_risk', 'code' => 'kamera_riski', 'title' => 'Kamera Risk Analizi', 'severity' => 'high'],
                    ['type' => 'require_inventory', 'code' => 'kamera_envanteri', 'title' => 'Kamera Veri Envanteri', 'severity' => 'medium'],
                    ['type' => 'require_retention', 'code' => 'kamera_saklama', 'title' => 'Kamera Saklama Politikası', 'severity' => 'medium'],
                    ['type' => 'require_audit', 'code' => 'kamera_denetimi', 'title' => 'Kamera Denetim Maddesi', 'severity' => 'medium'],
                    ['type' => 'require_verbis', 'code' => 'kamera_verbis', 'title' => 'VERBİS Kamera Kaydı', 'severity' => 'high'],
                ],
            ],
            [
                'code' => 'website_obligations',
                'name' => 'Web sitesi yükümlülükleri',
                'description' => 'Web sitesi varsa aydınlatma ve gizlilik metinleri gerekir.',
                'priority' => 20,
                'conditions' => [
                    ['field' => 'has_website', 'operator' => 'eq', 'value' => true],
                ],
                'actions' => [
                    ['type' => 'require_document', 'code' => 'web_aydinlatma', 'title' => 'Web Aydınlatma Metni', 'severity' => 'high'],
                    ['type' => 'require_document', 'code' => 'gizlilik_politikasi', 'title' => 'Gizlilik Politikası', 'severity' => 'medium'],
                ],
            ],
            [
                'code' => 'cookie_obligations',
                'name' => 'Çerez yükümlülükleri',
                'description' => 'Çerez işleniyorsa çerez politikası ve banner gerekir.',
                'priority' => 30,
                'conditions' => [
                    ['field' => 'has_cookies', 'operator' => 'eq', 'value' => true],
                ],
                'actions' => [
                    ['type' => 'require_document', 'code' => 'cerez_politikasi', 'title' => 'Çerez Politikası', 'severity' => 'high'],
                    ['type' => 'require_document', 'code' => 'cerez_banner', 'title' => 'Çerez Onay Banner Metni', 'severity' => 'medium'],
                ],
            ],
            [
                'code' => 'employee_scale',
                'name' => 'Personel ölçeği',
                'description' => '50+ personelde eğitim ve prosedür seti önerilir.',
                'priority' => 40,
                'conditions' => [
                    ['field' => 'employee_count', 'operator' => 'gte', 'value' => 50],
                ],
                'actions' => [
                    ['type' => 'require_training', 'code' => 'personel_egitimi', 'title' => 'Personel KVKK Eğitimi', 'severity' => 'medium'],
                    ['type' => 'require_procedure', 'code' => 'personel_proseduru', 'title' => 'Personel Veri İşleme Prosedürü', 'severity' => 'medium'],
                ],
            ],
        ];

        foreach ($rules as $rule) {
            ComplianceRule::query()->updateOrCreate(
                ['tenant_id' => null, 'code' => $rule['code']],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $rule['name'],
                    'description' => $rule['description'],
                    'is_active' => true,
                    'priority' => $rule['priority'],
                    'conditions' => $rule['conditions'],
                    'actions' => $rule['actions'],
                ]
            );
        }
    }
}
