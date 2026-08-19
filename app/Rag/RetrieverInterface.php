<?php

namespace App\Rag;

interface RetrieverInterface
{
    /**
     * Retrieve relevant sources for a given question.
     *
     * @return RetrievedSource[]
     */
    public function retrieve(string $question, int $topK = 4): array;
}
