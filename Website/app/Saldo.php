<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    public $timestamps = false;
    protected $table = 'saldo';
    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        '',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    public static function add($id, $nominal)
    {
        $saldo = Saldo::find($id);
        if ($saldo) {
            $saldo->nominal += $nominal;
            $saldo->save();
        } else {
            $saldo = new Saldo();
            $saldo->id_user = $id;
            $saldo->nominal = $nominal;
            $saldo->save();
        }
    }

    public static function minus($id, $nominal)
    {
        $saldo = Saldo::find($id);
        $saldo->nominal -= $nominal;
        $saldo->save();
    }
}
