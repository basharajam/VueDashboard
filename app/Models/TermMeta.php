<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TermMeta extends Model
{
    protected $table="wpdm_termmeta";
    protected $primaryKey="meta_id";
    protected $fillable=[
        'term_id',
        'meta_id',
        'meta_key',
        'meta_value',
    ];

    public $timestamps = false;

}
