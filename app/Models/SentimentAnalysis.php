<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentimentAnalysis extends Model
{
    protected $fillable = [
        'text',
        'overall_sentiment',
        'positive_score',
        'neutral_score',
        'negative_score',
        'entity_analysis'
    ];

    protected $casts = [
        'entity_analysis' => 'array'
    ];
}