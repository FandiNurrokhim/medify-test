<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'harga_beli',
        'laba',
        'photo',
        'supplier'
    ];

    protected static function booted()
    {
        static::creating(function ($item) {
            $lastId = MasterItem::withTrashed()->max('id') ?? 0;
            $kode = 'PRD' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            $item->kode = $kode;
        });
    }


    public function categories()
    {
        return $this->belongsToMany(Kategori::class, 'master_kategori', 'master_item_id', 'kategori_id')
            ->withTimestamps();
    }
}
