<?php

namespace Tests\Unit;

use App\Domain\Risk\Enums\RiskLevel;
use App\Domain\Risk\Models\RiskAssessment;
use PHPUnit\Framework\TestCase;

class RiskScoreTest extends TestCase
{
    public function test_score_and_level_matrix(): void
    {
        $this->assertSame(1, RiskAssessment::calculateScore(1, 1));
        $this->assertSame(RiskLevel::Low, RiskAssessment::levelFromScore(1));
        $this->assertSame(RiskLevel::Medium, RiskAssessment::levelFromScore(4));
        $this->assertSame(RiskLevel::High, RiskAssessment::levelFromScore(9));
        $this->assertSame(RiskLevel::Critical, RiskAssessment::levelFromScore(16));
        $this->assertSame(25, RiskAssessment::calculateScore(5, 5));
    }
}
