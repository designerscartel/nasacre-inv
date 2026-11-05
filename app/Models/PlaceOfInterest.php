<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaceOfInterest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'places_of_interest';

    protected $fillable = [
        'name',
        'is_place_of_interest',
        'is_school_speaker',
        'religion_id',
        'contact_name',
        'organisation_email',
        'personal_email',
        'phone',
        'website',
        'last_training',
        'charge_to_speak',
        'notes',
        'do_not_list',
        'source_key',
    ];

    protected $casts = [
        'do_not_list' => 'boolean',
        'is_place_of_interest' => 'boolean',
        'is_school_speaker' => 'boolean',
    ];

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class);
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class);
    }
}
