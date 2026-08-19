<?php

namespace App\Ai;

use App\Rag\Citation;
use Illuminate\Support\Str;
use JsonSerializable;

class ChatResponse implements JsonSerializable
{
    /**
     * @param  Citation[]  $citations
     */
    public function __construct(
        public readonly string $requestId,
        public readonly string $status,
        public readonly ?string $answer = null,
        public readonly ?string $clarificationQuestion = null,
        public readonly array $citations = []
    ) {}

    public static function create(
        string $status,
        ?string $answer = null,
        ?string $clarificationQuestion = null,
        array $citations = [],
        ?string $requestId = null
    ): self {
        return new self(
            requestId: $requestId ?? ('req_'.Str::random(16)),
            status: $status,
            answer: $answer,
            clarificationQuestion: $clarificationQuestion,
            citations: $citations
        );
    }

    public function jsonSerialize(): array
    {
        $payload = [
            'requestId' => $this->requestId,
            'status' => $this->status,
        ];

        if ($this->answer !== null) {
            $payload['answer'] = $this->answer;
        }

        if ($this->clarificationQuestion !== null) {
            $payload['clarificationQuestion'] = $this->clarificationQuestion;
        }

        if (! empty($this->citations)) {
            $payload['citations'] = array_map(
                fn (Citation $c) => $c->jsonSerialize(),
                $this->citations
            );
        } else {
            $payload['citations'] = [];
        }

        return $payload;
    }
}
