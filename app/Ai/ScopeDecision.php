<?php

namespace App\Ai;

class ScopeDecision
{
    /**
     * @param  string[]  $missing  Required parameters missing (e.g. ['geography', 'period'])
     */
    public function __construct(
        public readonly bool $inScope,
        public readonly string $intent = 'definition',
        public readonly array $missing = [],
        public readonly ?string $reason = null
    ) {}

    public static function inScope(string $intent = 'definition', array $missing = [], ?string $reason = null): self
    {
        return new self(
            inScope: true,
            intent: $intent,
            missing: $missing,
            reason: $reason
        );
    }

    public static function outOfScope(string $reason = 'Pertanyaan di luar cakupan BPS dan statistik.'): self
    {
        return new self(
            inScope: false,
            intent: 'out_of_scope',
            missing: [],
            reason: $reason
        );
    }
}
