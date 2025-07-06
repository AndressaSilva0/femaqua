<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tool extends Model
{
    use HasFactory;

    protected $table = 'ferramentas';

    protected $fillable = [
        'title',
        'link',
        'description',
        'usuario_id',
    ];

    public $timestamps = false;

    /**
     * Relacionamento com tags (many-to-many)
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'ferramenta_tags', 'ferramenta_id', 'tag_id');
    }

    /**
     * Usuário que cadastrou a ferramenta
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
