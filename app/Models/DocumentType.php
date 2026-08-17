<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'doc_type_id';

    protected $fillable = [
        'doc_name',
        'category',
    ];
}
