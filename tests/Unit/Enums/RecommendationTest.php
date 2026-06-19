<?php

namespace Tests\Unit\Enums;

use App\Enums\Recommendation;
use PHPUnit\Framework\TestCase;

class RecommendationTest extends TestCase
{
    public function test_has_three_cases(): void
    {
        $cases = Recommendation::cases();

        $this->assertCount(3, $cases);
    }

    public function test_convocation_has_correct_value(): void
    {
        $this->assertSame('convoquer', Recommendation::Convocation->value);
    }

    public function test_attente_has_correct_value(): void
    {
        $this->assertSame('attente', Recommendation::Attente->value);
    }

    public function test_rejet_has_correct_value(): void
    {
        $this->assertSame('rejeter', Recommendation::Rejet->value);
    }
}
