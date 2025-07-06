<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = [
        'nome',
    ];

    public $timestamps = false;

    /**
     * Ferramentas associadas a esta tag
     */
    public function tools()
    {
        return $this->belongsToMany(Tool::class, 'ferramenta_tags', 'tag_id', 'ferramenta_id');
    }
}
