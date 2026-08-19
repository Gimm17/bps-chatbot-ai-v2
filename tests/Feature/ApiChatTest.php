<?php

namespace Tests\Feature;

use App\Ai\AiProviderInterface;
use App\Ai\ChatProviderOutput;
use Mockery;
use Tests\TestCase;

class ApiChatTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'app' => 'BPS AI Assistant',
            ]);
    }

    public function test_models_endpoint_returns_models_list(): void
    {
        $response = $this->getJson('/api/models');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
                'default',
            ]);
    }

    public function test_chat_rejects_empty_message(): void
    {
        $response = $this->postJson('/api/chat', [
            'message' => '',
        ]);

        $response->assertStatus(400)
            ->assertJsonStructure([
                'error' => ['code', 'message'],
            ]);
    }

    public function test_chat_handles_greeting_without_calling_ai(): void
    {
        $response = $this->postJson('/api/chat', [
            'message' => 'Halo, selamat pagi!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'answered',
            ]);
    }

    public function test_chat_handles_out_of_scope_without_calling_ai(): void
    {
        $response = $this->postJson('/api/chat', [
            'message' => 'Buatkan puisi romantis tentang senja.',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'out_of_scope',
            ]);
    }

    public function test_chat_handles_numeric_clarification(): void
    {
        $response = $this->postJson('/api/chat', [
            'message' => 'Berapa jumlah penduduk di sini?',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'clarification_required',
            ])
            ->assertJsonStructure([
                'clarificationQuestion',
            ]);
    }

    public function test_chat_with_mocked_ai_provider(): void
    {
        $mockProvider = Mockery::mock(AiProviderInterface::class);
        $mockProvider->shouldReceive('chat')
            ->once()
            ->andReturn(new ChatProviderOutput(
                text: json_encode([
                    'status' => 'answered',
                    'answer' => 'Inflasi di Indonesia diukur dari IHK.',
                    'citationSourceIds' => ['SRC-DEMO-001'],
                ])
            ));

        $this->app->instance(AiProviderInterface::class, $mockProvider);

        $response = $this->postJson('/api/chat', [
            'message' => 'Apa itu inflasi?',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'answered',
                'answer' => 'Inflasi di Indonesia diukur dari IHK.',
            ])
            ->assertJsonStructure([
                'requestId',
                'status',
                'answer',
                'citations',
            ]);
    }
}
