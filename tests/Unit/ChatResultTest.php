<?php

namespace Tests\Unit;

use App\Ai\ChatResult;
use PHPUnit\Framework\TestCase;

class ChatResultTest extends TestCase
{
    public function test_parses_clean_json_output(): void
    {
        $json = json_encode([
            'status' => 'answered',
            'answer' => 'Inflasi adalah kenaikan harga secara umum.',
            'citationSourceIds' => ['SRC-DEMO-001'],
        ]);

        $result = ChatResult::parse($json);

        $this->assertEquals('answered', $result->status);
        $this->assertEquals('Inflasi adalah kenaikan harga secara umum.', $result->answer);
        $this->assertEquals(['SRC-DEMO-001'], $result->citationSourceIds);
    }

    public function test_parses_json_wrapped_in_markdown_code_fences(): void
    {
        $raw = "```json\n".json_encode([
            'status' => 'clarification_required',
            'clarificationQuestion' => 'Wilayah mana yang Anda maksud?',
            'citationSourceIds' => [],
        ])."\n```";

        $result = ChatResult::parse($raw);

        $this->assertEquals('clarification_required', $result->status);
        $this->assertEquals('Wilayah mana yang Anda maksud?', $result->clarificationQuestion);
    }

    public function test_strips_nested_json_inside_answer(): void
    {
        $inner = json_encode([
            'status' => 'answered',
            'answer' => 'Jawaban bersih.',
            'citationSourceIds' => ['SRC-DEMO-002'],
        ]);

        $nested = "```json\n".json_encode([
            'status' => 'answered',
            'answer' => "```json\n".$inner."\n```",
            'citationSourceIds' => ['SRC-DEMO-002'],
        ])."\n```";

        $result = ChatResult::parse($nested);

        $this->assertEquals('answered', $result->status);
        $this->assertEquals('Jawaban bersih.', $result->answer);
    }

    public function test_fallback_for_plain_text(): void
    {
        $raw = 'Inflasi dihitung berdasarkan IHK dari komoditas pangan dan non-pangan [SRC-DEMO-001].';

        $result = ChatResult::parse($raw);

        $this->assertEquals('answered', $result->status);
        $this->assertEquals($raw, $result->answer);
        $this->assertEquals(['SRC-DEMO-001'], $result->citationSourceIds);
    }
}
