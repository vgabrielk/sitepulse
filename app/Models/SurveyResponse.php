<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'session_id',
        'responses',
        'ip_address',
        'submitted_at',
    ];

    protected $casts = [
        'responses' => 'array',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the survey that owns this response
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the session that submitted this response
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get response for a specific question
     */
    public function getResponseForQuestion(string $questionId): mixed
    {
        return $this->responses[$questionId] ?? null;
    }

    /**
     * Set response for a specific question
     */
    public function setResponseForQuestion(string $questionId, mixed $response): void
    {
        $responses = $this->responses ?? [];
        $responses[$questionId] = $response;
        $this->update(['responses' => $responses]);
    }
}
