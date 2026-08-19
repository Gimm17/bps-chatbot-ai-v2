<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationIndex extends Model
{
    protected $table = 'publication_indexes';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'domain_id',
        'domain_name',
        'title',
        'rl_date',
        'pdf_url',
        'portal_url',
        'file_path',
        'extracted_text',
        'abstract',
        'page_count',
        'file_size_kb',
        'status',
    ];

    protected $casts = [
        'rl_date' => 'date',
        'page_count' => 'integer',
        'file_size_kb' => 'integer',
    ];
}
