<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $table = 'medications';

    protected $primaryKey = 'medication_id';

    protected $fillable = [
        'medication_name',
        'dosage',
        'notes',
        'effects',
        'side_effects',
    ];
    public function recordMedications()
    {
        return $this->hasMany(RecordMedication::class, 'medication_id', 'medication_id');
    }
}