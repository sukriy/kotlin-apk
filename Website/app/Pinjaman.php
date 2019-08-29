<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    protected $table = 'pinjaman';

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

    public static function list($id, $level, $from = '', $to = '')
    {
        $list = Pinjaman::Leftjoin('users', 'users.id', '=', 'pinjaman.id_user')
            ->Leftjoin('transaksi', function ($join) {
                $join->on('transaksi.id_pembayaran', '=', 'pinjaman.id')
                    ->on('transaksi.pembayaran', DB::raw("'Cicilan'"))
                    ->on('transaksi.flag', '<>', DB::raw('0'));
            })
            ->Leftjoin('transaksi as checker', function ($join) {
                $join->on('checker.id_pembayaran', '=', 'pinjaman.id')
                    ->on('checker.pembayaran', DB::raw("'Cicilan'"))
                    ->on('checker.flag', '=', DB::raw('1'));
            })
            ->select(
                'pinjaman.*',
                'users.namalengkap',
                'pinjaman.nominal',
                DB::raw('IFNULL(SUM(transaksi.nominal), 0) as bayar'),
                'checker.gambar',
                DB::raw(
                    '
                    case pinjaman.flag
                    when 0 then "Tolak"
                    when 1 then "Waiting"
                    when 2 then "Acc Admin"
                    when 3 then "Acc Ketua"
                    when 4 then "Proses"
                    when 5 then "Done"
                    end status'
                )
            )
            ->orderBy('transaksi.created_at', 'desc')
            ->groupBy('pinjaman.id');
        if ($level == 'Anggota') {
            $list = $list->where('pinjaman.id_user', $id);
        } elseif ($level == 'detail') {
            $list = $list->where('pinjaman.id', $id);
        }
        if ($from != '' && $to != '') {
            $list = $list->whereBetween('pinjaman.created_at', [$from, $to]);
        }
        $list = $list->get();

        return $list;
    }
}
