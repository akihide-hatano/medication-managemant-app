<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecordMedication extends Model
{
    use HasFactory;

    protected $table = 'record_medications';
    protected $primaryKey = 'record_medication_id';

    protected $fillable = [
        'record_id',
        'medication_id',
        'taken_dosage',
        'is_completed',
        'reason_not_taken',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function record()
    {
        return $this->belongsTo(Record::class, 'record_id', 'record_id');
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class, 'medication_id', 'medication_id');
    }
}
