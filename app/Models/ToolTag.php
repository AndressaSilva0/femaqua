<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolTag extends Model
{
    protected $table = 'ferramenta_tags';

    protected $fillable = ['ferramenta_id', 'tag_id'];

    public $timestamps = false;
}
