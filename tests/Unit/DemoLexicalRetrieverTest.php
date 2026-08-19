<?php

namespace Tests\Unit;

use App\Rag\DemoLexicalRetriever;
use App\Rag\KnowledgeLoader;
use Tests\TestCase;

class DemoLexicalRetrieverTest extends TestCase
{
    private DemoLexicalRetriever $retriever;

    protected function setUp(): void
    {
        parent::setUp();
        $loader = new KnowledgeLoader(base_path('data/knowledge'));
        $this->retriever = new DemoLexicalRetriever($loader);
    }

    public function test_retrieves_relevant_sources_for_inflation(): void
    {
        $results = $this->retriever->retrieve('Apa itu inflasi?', 3);

        $this->assertNotEmpty($results);
        $this->assertEquals('SRC-DEMO-001', $results[0]->sourceId);
        $this->assertStringContainsString('Inflasi', $results[0]->title);
    }

    public function test_retrieves_relevant_sources_for_pdrb(): void
    {
        $results = $this->retriever->retrieve('Jelaskan konsep PDRB atas dasar harga konstan', 3);

        $this->assertNotEmpty($results);
        $this->assertEquals('SRC-DEMO-003', $results[0]->sourceId);
    }

    public function test_returns_empty_for_unrelated_nonsense(): void
    {
        $results = $this->retriever->retrieve('qwerty zxcvbnm asdfghjk', 3);
        $this->assertEmpty($results);
    }
}
