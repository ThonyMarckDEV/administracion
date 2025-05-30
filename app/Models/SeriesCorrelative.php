<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeriesCorrelative extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'series_correlatives';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'document_type',
        'serie',
        'correlative',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'document_type' => 'string', // Enum is cast as string
        'correlative' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
}