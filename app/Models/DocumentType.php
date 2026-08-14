<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $doc_type_id
 * @property string $doc_name
 * @property string|null $category
 */
class DocumentType extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'doc_type_id';

    protected $fillable = ['doc_name', 'category'];
}
