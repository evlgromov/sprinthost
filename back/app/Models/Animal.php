<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at', 'animal_kind_id'];

    protected $fillable = [
        'animal_kind_id',
        'size',
        'age'
    ];

    public $dateFormat = 'Y-m-d H:m:s';

    public function animal_kind() {
        return $this->belongsTo(AnimalKind::class, 'animal_kind_id');
    }
}
