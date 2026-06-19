<?php

namespace Tests\Unit\Enums;

use App\Enums\AnalysisStatus;
use PHPUnit\Framework\TestCase;

class AnalysisStatusTest extends TestCase
{
    public function test_has_four_cases(): void
    {
        $cases = AnalysisStatus::cases();

        $this->assertCount(4, $cases);
    }

    public function test_pending_has_correct_value(): void
    {
        $this->assertSame('pending', AnalysisStatus::Pending->value);
    }

    public function test_processing_has_correct_value(): void
    {
        $this->assertSame('processing', AnalysisStatus::Processing->value);
    }

    public function test_completed_has_correct_value(): void
    {
        $this->assertSame('completed', AnalysisStatus::Completed->value);
    }

    public function test_failed_has_correct_value(): void
    {
        $this->assertSame('failed', AnalysisStatus::Failed->value);
    }
}
