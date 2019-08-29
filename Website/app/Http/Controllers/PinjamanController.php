<?php

namespace App\Http\Controllers;

use App\Pinjaman;
use App\Transaksi;
use App\User;
use DB;
use Illuminate\Http\Request;
use MyHelper;
use Validator;

class pinjamanController extends Controller
{
    public function list(Request $request)
    {
        $user = User::where('api_token', $request->api_token)->first();
        $list = Pinjaman::list($user->id, $user->level);

        return response()->json([
            'success' => true,
            'data' => $list,
        ]);
    }

    public function detail(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Pinjaman::find($request->input('id')),
        ]);
    }

    public function bayar_list(Request $request)
    {
        $user = User::where('api_token', $request->api_token)->first();

        if ($user->level == 'Anggota') {
            $list = Transaksi
      ::join('pinjaman', 'pinjaman.id', '=', 'transaksi.id_pembayaran')
          ->join('users', 'users.id', '=', 'pinjaman.id_user')
          ->where(function ($query) use ($request, $user) {
              $query->where('transaksi.id_pembayaran', $request->id)
                  ->where('pinjaman.id_user', $user->id)
                  ->whereIn('pembayaran', ['Cicilan']);
          })
          ->select('transaksi.*', 'users.namalengkap')
          ->orderBy('transaksi.tgl_pembayaran', 'desc')
          ->get();
        } else {
            $list = Transaksi
        ::join('pinjaman', 'pinjaman.id', '=', 'transaksi.id_pembayaran')
            ->join('users', 'users.id', '=', 'pinjaman.id_user')
            ->where(function ($query) use ($request) {
                $query->where('transaksi.id_pembayaran', $request->id)
                    ->whereIn('pembayaran', ['Cicilan']);
            })
            ->select('transaksi.*', 'users.namalengkap')
            ->orderBy('transaksi.tgl_pembayaran', 'desc')
            ->get();
        }

        $header = Pinjaman::list($request->input('id'), 'detail');

        return response()->json([
            'success' => true,
            'header' => $header,
            'data' => $list,
        ]);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nominal' => 'required|numeric:max:11',
            'tenor' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
            'bunga' => 'required|in:2%',
            'cicilan' => 'required|numeric:max:11',
            'keterangan' => 'max:250',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('api_token', $request->api_token)->select('id', 'gaji', 'tgl_join', 'flag')->first();
        if ($user->flag != 2) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak belum berhak melakukan pinjaman',
            ]);
        }

        $check = Pinjaman::where('id_user', $user->id)
            ->whereNotIn('flag', [0, 5])->count();
        if ($check > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Lunaskan pinjaman yang ada terlebihi dahulu',
            ]);
        }

        $datetime1 = date_create(date('Y-m-d'));
        $datetime2 = date_create($user->tgl_join);
        $interval = date_diff($datetime1, $datetime2);
        $lama = $interval->format('%y') * 12 + $interval->format('%m');

        if ($lama >= 15) {
            $lama = floor($lama / 12) * 2;
            if ($lama > 10) {
                $lama = 10;
            }
            $max = (int) $lama * $user->gaji;

            if ($max < $request->nominal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda meminjam dari jumlah diperbolehkan yaitu '.$max,
                ]);
            }

            $cicilan = MyHelper::calculate($request->nominal, $request->bunga, $request->tenor);
            if ($cicilan != $request->cicilan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalah total cicilan',
                ]);
            }

            $pinjaman = new Pinjaman();
            $pinjaman->id_user = $user->id;
            $pinjaman->nominal = $request->nominal;
            $pinjaman->tenor = $request->tenor;
            $pinjaman->bunga = $request->bunga;
            $pinjaman->cicilan = $request->cicilan;
            $pinjaman->keterangan = $request->input('keterangan', '');
            $pinjaman->flag = 1;
            $pinjaman->save();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Input Pinjaman',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Yang berhak meminjam dari koperasi adalah karyawan tetap',
        ]);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:pinjaman,id',
            'nominal' => 'required|numeric:max:11',
            'tenor' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
            'bunga' => 'required|in:2%',
            'cicilan' => 'required|numeric:max:11',
            'keterangan' => 'max:250',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $pinjaman = Pinjaman::find($request->id);
        $user = User::where('api_token', $request->api_token)->select('id', 'level', 'gaji', 'tgl_join', 'flag')->first();

        if ($user->flag != 2) {
            return response()->json([
                'success' => false,
                'message' => 'Status member Anda belum aktif',
            ]);
        }

        if ($pinjaman->id_user != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak mengedit pinjaman',
            ]);
        }

        if ($pinjaman->flag != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Pinjaman tidak dapat diedit dikarenakan sudah divalidasi',
            ]);
        }

        $datetime1 = date_create(date('Y-m-d'));
        $datetime2 = date_create($user->tgl_join);
        $interval = date_diff($datetime1, $datetime2);
        $lama = $interval->format('%y') * 12 + $interval->format('%m');

        if ($lama >= 15) {
            $lama = floor($lama / 12) * 2;
            if ($lama > 10) {
                $lama = 10;
            }
            $max = (int) $lama * $user->gaji;

            if ($max < $request->nominal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda meminjam dari jumlah diperbolehkan yaitu '.$max,
                ]);
            }
            $cicilan = MyHelper::calculate($request->nominal, $request->bunga, $request->tenor);
            if ($cicilan != $request->cicilan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalah total cicilan',
                ]);
            }

            $pinjaman->nominal = $request->nominal;
            $pinjaman->tenor = $request->tenor;
            $pinjaman->bunga = $request->bunga;
            $pinjaman->cicilan = $request->cicilan;
            $pinjaman->keterangan = $request->input('keterangan', '');
            $pinjaman->save();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Edit Pinjaman',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Yang berhak meminjam dari koperasi adalah karyawan tetap',
        ]);
    }

    public function del(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:pinjaman,id',
        ]);

        try {
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $user = User::where('api_token', $request->api_token)->select('level', 'id')->first();
            $pinjaman = Pinjaman::find($request->id);

            if ($user->level == 'Anggota') {
                if ($pinjaman->acc_admin != null && $pinjaman->acc_ketua != null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Terjadi kesalahan',
                    ]);
                }
            }
            if ($user->level == 'Admin') {
                if ($pinjaman->acc_ketua != null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Terjadi kesalahan',
                    ]);
                }
            }

            $res = Pinjaman::where('id', $request->get('id'))->delete();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil hapus',
        ]);
    }

    public function acc(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:pinjaman,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        date_default_timezone_set('Asia/Bangkok');
        $user = User::where('api_token', $request->api_token)->select('level', 'id')->first();
        $level = $user->level;
        $id_user = $user->id;

        $pinjaman = Pinjaman::find($request->id);
        if ($pinjaman->flag == 0) {
            if ($pinjaman->acc_ketua != null && $level == 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pinjaman telah ditolak',
                ]);
            }
        }

        if ($level == 'Anggota') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak punya akses',
            ]);
        }
        if ($level == 'Admin' && $pinjaman->flag != 3) {
            if ($request->acc) {
                $flag = 2;
            } else {
                $flag = 0;
            }
            $pinjaman->tgl_acc_admin = date('Y-m-d H:i:s');
            $pinjaman->id_admin = $id_user;
            $pinjaman->acc_admin = $request->acc;
            $pinjaman->note_admin = $request->note;
            $pinjaman->flag = $flag;
            $pinjaman->save();

            return response()->json([
                'success' => true,
                'message' => 'Success Give Approve',
            ]);
        }
        if ($level == 'Ketua') {
            if ($request->acc) {
                $flag = 3;
            } else {
                $flag = 0;
            }

            $pinjaman->tgl_acc_ketua = date('Y-m-d H:i:s');
            $pinjaman->id_ketua = $id_user;
            $pinjaman->acc_ketua = $request->acc;
            $pinjaman->note_ketua = $request->note;
            $pinjaman->flag = $flag;
            $pinjaman->save();

            return response()->json([
                'success' => true,
                'message' => 'Success Give Approve',
            ]);
        }
    }

    public function pembayaran(Request $request)
    {
        $user = User::where('api_token', $request->api_token)->select('id', 'level')->first();
        $opsi = [
            'id' => 'required|exists:pinjaman,id',
            'nominal' => 'required|numeric:max:11',
            'tgl_pembayaran' => 'required|date_format:Y-m-d',
        ];
        if ($user->level == 'Anggota') {
            $opsi['gambar'] = 'required|image|max:2048';
        } else {
            $opsi['gambar'] = 'nullable|image|max:2048';
        }

        $validator = Validator::make($request->all(), $opsi);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        $user = User::where('api_token', $request->api_token)->first();
        $pinjaman = Pinjaman::where('id', $request->id)->whereIn('flag', [3, 4])->first();
        $transaksi = Transaksi::where('id_pembayaran', $pinjaman->id)
            ->whereIn('flag', [1])->count();

        if ($transaksi > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ada Transaksi yang belum diapprove',
            ]);
        }

        $transaksi = Transaksi::where('id_pembayaran', $pinjaman->id)
            ->whereNotIn('flag', [0, 5])
            ->where('pembayaran', 'Cicilan')
            ->select(DB::raw('SUM(nominal) as bayar'))
            ->groupBy('id_pembayaran')
            ->first();

        if (count($transaksi) > 0) {
            $bayar = $transaksi->bayar;
        } else {
            $bayar = 0;
        }

        if ($request->nominal > (($pinjaman->cicilan * $pinjaman->tenor) - $bayar)) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran melebihi total/sisa tagihan',
            ]);
        }

        DB::beginTransaction();

        try {
            if ($request->nominal == (($pinjaman->cicilan * $pinjaman->tenor) - $bayar)) {
                $pinjaman->flag = 5;
                $pinjaman->save();
            } else {
                $pinjaman->flag = 4;
                $pinjaman->save();
            }

            $transaksi = new Transaksi();
            $transaksi->id_pembayaran = $pinjaman->id;
            $transaksi->tgl_pembayaran = $request->tgl_pembayaran;
            $transaksi->nominal = $request->nominal;
            $transaksi->pembayaran = 'Cicilan';

            if ($user->level != 'Anggota') {
                $transaksi->flag = 2;
                $transaksi->jenis = 'Tunai';
            } else {
                $transaksi->flag = 1;
                $transaksi->jenis = 'Transfer';
            }
            $transaksi->save();

            if ($request->has('gambar')) {
                $transaksi = Transaksi::find($transaksi->id);
                $imageName = $transaksi->id.'.'.$request->gambar->getClientOriginalExtension();
                $request->gambar->move(public_path('images/pembayaran'), $imageName);
                $transaksi->gambar = $imageName;
                $transaksi->save();
            }
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Success Pembayaran',
        ]);
    }

    public function konfirmasi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:pinjaman,id',
            'flag' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('api_token', $request->api_token)->select('level', 'id')->first();

        if ($user->level == 'Anggota') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak punya akses',
            ]);
        }

        DB::beginTransaction();

        try {
            date_default_timezone_set('Asia/Bangkok');
            $transaksi = Transaksi::where('id_pembayaran', $request->id)
                ->where('pembayaran', 'Cicilan')
                ->where('flag', DB::raw('1'))
                ->first();
            $transaksi->tgl_acc = date('Y-m-d H:i:s');
            $transaksi->id_acc = $user->id;

            if ($request->flag) {
                $transaksi->flag = 2;
            } else {
                $transaksi->flag = 0;
            }
            $transaksi->save();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Success',
        ]);
    }
}
