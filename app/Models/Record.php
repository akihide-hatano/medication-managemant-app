<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Record extends Model
{
    use HasFactory;

    protected $table = 'records';
    protected $primaryKey = 'record_id';

    protected $fillable = [
    'user_id',
    'timing_id',
    'taken_at',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function timingTag()
    {
        return $this->belongsTo(TimingTag::class, 'timing_tag_id', 'timing_tag_id');
    }

    public function recordMedications()
    {
        return $this->hasMany(RecordMedication::class, 'record_id', 'record_id');
    }
}
