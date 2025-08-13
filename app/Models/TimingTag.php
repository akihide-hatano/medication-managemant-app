<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimingTag extends Model
{
    use HasFactory;

    protected $table = 'timing_tags';
    protected $primaryKey = 'timing_id';

    protected $fillable = [
        'timing_name',
        'base_time',
    ];

    protected $casts = [
        'base_time' => 'datetime:H:i:s',
    ];

    public function records()
    {
        return $this->hasMany(Record::class, 'timing_id', 'timing_id');
    }
}
