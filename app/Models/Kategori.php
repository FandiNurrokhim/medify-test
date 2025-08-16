<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kategori';

    protected $fillable = ['nama', 'kode'];

    protected static function booted()
    {
        static::creating(function ($item) {
            $lastId = Kategori::withTrashed()->max('id') ?? 0;
            $kode = 'KAT' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            $item->kode = $kode;
        });
    }

    public function masterItems()
    {
        return $this->belongsToMany(MasterItem::class, 'master_kategori', 'kategori_id', 'master_item_id');
    }
}
