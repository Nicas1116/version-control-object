<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Objects extends Model
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = ['key','value'];

    protected $casts = [
        'key' => 'string',
        'value' => 'string',
    ];

    public function getSelectedFields(array $fields)
    {
        $array = $this->toArray();
        $obj = array();
        foreach ($fields as $field){
            $obj[$field] = $array[$field];
        }
        return $obj;
    }
}