<?php

namespace Tests\Unit;

use App\Rag\Citation;
use App\Rag\RetrievedSource;
use PHPUnit\Framework\TestCase;

class CitationTest extends TestCase
{
    public function test_from_sources_creates_unverified_demo_citations(): void
    {
        $sources = [
            new RetrievedSource(
                sourceId: 'SRC-DEMO-001',
                title: 'Definisi Inflasi',
                url: 'https://bps.go.id/inflasi',
                content: 'Inflasi adalah kenaikan harga secara umum dan terus menerus.',
                score: 2.5
            ),
        ];

        $citations = Citation::fromSources($sources, ['SRC-DEMO-001']);

        $this->assertCount(1, $citations);
        $this->assertEquals('SRC-DEMO-001', $citations[0]->sourceId);
        $this->assertEquals('Definisi Inflasi', $citations[0]->title);
        $this->assertFalse($citations[0]->verified);
    }

    public function test_from_bps_sources_creates_verified_citations(): void
    {
        $bpsSources = [
            'PUB-123' => [
                'title' => 'Statistik Indonesia 2024',
                'url' => 'https://bps.go.id/pub123.pdf',
                'snippet' => 'Ringkasan resmi BPS...',
            ],
        ];

        $citations = Citation::fromBpsSources($bpsSources, ['PUB-123']);

        $this->assertCount(1, $citations);
        $this->assertEquals('PUB-123', $citations[0]->sourceId);
        $this->assertTrue($citations[0]->verified);
    }

    public function test_serializes_correctly_to_json(): void
    {
        $c = new Citation(
            sourceId: 'SRC-1',
            title: 'Title',
            url: 'https://example.com',
            snippet: 'Snippet',
            verified: true
        );

        $json = $c->jsonSerialize();

        $this->assertEquals('SRC-1', $json['sourceId']);
        $this->assertTrue($json['verified']);
    }
}
