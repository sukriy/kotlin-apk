<?php

namespace App\Http\Controllers;

use App\Saldo;
use App\TabunganSukarela;
use App\Transaksi;
use App\User;
use DB;
use Illuminate\Http\Request;
use Validator;

class TabunganSukarelaController extends Controller
{
    public function list(Request $request)
    {
        $user = User::where('api_token', $request->api_token)->first();
        $tabunganSukarela = TabunganSukarela::leftJoin('users', 'users.id', '=', 'sukarela.id_user')
            ->join('transaksi', function ($join) {
                $join->on('transaksi.id_pembayaran', '=', 'sukarela.id');
                $join->on('transaksi.pembayaran', '=', DB::raw("'Sukarela'"));
            })
            ->select('sukarela.*', 'users.namalengkap', 'transaksi.tgl_pembayaran', 'transaksi.gambar', 'transaksi.note');

        if ($user->level == 'Anggota') {
            $tabunganSukarela = $tabunganSukarela->where('id_user', $user->id);
        }

        return response()->json([
            'success' => true,
            'data' => $tabunganSukarela->get(),
        ]);
    }

    public function detail(Request $request)
    {
        $data = DB::select(DB::raw('
          SELECT sukarela.*, transaksi.tgl_pembayaran, transaksi.jenis, transaksi.gambar
          FROM sukarela
          left join transaksi on transaksi.id_pembayaran=sukarela.id and transaksi.pembayaran = "Sukarela"
          where sukarela.id = ?
      '), [$request->id]);

        if (count($data) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'data' => $data,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil ketemu detail',
            'data' => $data[0],
        ]);
    }

    public function add(Request $request)
    {
        $user = User::where('api_token', $request->api_token)->select('id', 'level', 'flag')->first();
        $opsi = [
            'nominal' => 'required|numeric:max:11',
            'keterangan' => 'nullable|max:250',
            'tgl_pembayaran' => 'required|date_format:Y-m-d',
        ];
        if ($user->level == 'Anggota') {
            $opsi['jenis'] = 'required|in:Transfer';
            $opsi['gambar'] = 'required|image|max:2048';
        } else {
            $opsi['jenis'] = 'required|in:Transfer,Tunai';
            $opsi['gambar'] = 'nullable|image|max:2048';
        }

        $validator = Validator::make($request->all(), $opsi);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        if ($user->flag != 2) {
            return response()->json([
                'success' => false,
                'message' => 'Status member Anda belum aktif',
            ]);
        }

        DB::beginTransaction();

        try {
            $id_user = $user->id;

            $sukarela = new TabunganSukarela();
            $sukarela->id_user = $id_user;
            $sukarela->nominal = $request->nominal;
            $sukarela->keterangan = $request->input('keterangan', '');
            $sukarela->flag = 1;
            $sukarela->save();

            $transaksi = new Transaksi();
            $transaksi->id_pembayaran = $sukarela->id;
            $transaksi->tgl_pembayaran = $request->tgl_pembayaran;
            $transaksi->nominal = $request->nominal;
            $transaksi->jenis = $request->jenis;
            $transaksi->pembayaran = 'Sukarela';
            $transaksi->flag = 1;
            if ($request->has('gambar')) {
                $imageName = '';
                if ($request->gambar) {
                    $imageName = 'Sukarela_'.$sukarela->id.'.'.$request->gambar->getClientOriginalExtension();
                    $request->gambar->move(public_path('images/TabunganSukarela'), $imageName);
                }
                $transaksi->gambar = $imageName;
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
            'message' => 'Success Add TabunganSukarela',
        ]);
    }

    public function edit(Request $request)
    {
        $user = User::where('api_token', $request->api_token)->select('id', 'level', 'flag')->first();
        $opsi = [
            'id' => 'required|exists:sukarela,id',
            'nominal' => 'required|numeric:max:11',
            'keterangan' => 'nullable|max:250',
            'tgl_pembayaran' => 'required|date_format:Y-m-d',
            'gambar' => 'nullable|image|max:2048',
        ];
        if ($user->level == 'Anggota') {
            $opsi['jenis'] = 'required|in:Transfer';
        } else {
            $opsi['jenis'] = 'required|in:Transfer,Tunai';
        }
        $validator = Validator::make($request->all(), $opsi);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        if ($user->flag != 2) {
            return response()->json([
                'success' => false,
                'message' => 'Status member Anda belum aktif',
            ]);
        }

        $check = TabunganSukarela::where('id', $request->id)->select('id_user', 'flag')->first();

        if ($user->id != $check->id_user) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya yang input tabungan sukarela bisa edit',
            ]);
        }

        if ($check->flag != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Tabungan Sukarela tidak bisa dihapus dikarenakan sudah diapprove',
            ]);
        }

        DB::beginTransaction();

        try {
            $sukarela = TabunganSukarela::findOrFail($request->id);
            $sukarela->nominal = $request->nominal;
            $sukarela->keterangan = $request->input('keterangan', '');
            $sukarela->flag = 1;
            $sukarela->save();

            $transaksi = Transaksi::where('id_pembayaran', $sukarela->id)->where('pembayaran', 'Sukarela')->first();
            $transaksi->tgl_pembayaran = $request->tgl_pembayaran;
            $transaksi->nominal = $request->nominal;
            $transaksi->jenis = $request->jenis;
            $transaksi->pembayaran = 'Sukarela';
            $transaksi->flag = 1;
            if ($request->has('gambar')) {
                $imageName = '';
                if ($request->gambar) {
                    $imageName = 'Sukarela_'.$sukarela->id.'.'.$request->gambar->getClientOriginalExtension();
                    $request->gambar->move(public_path('images/TabunganSukarela'), $imageName);
                }
                $transaksi->gambar = $imageName;
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
            'message' => 'Success Add TabunganSukarela',
        ]);
    }

    public function del(Request $request)
    {
        $user = User::where('api_token', $request->api_token)->select('level', 'id')->first();
        $sukarela = TabunganSukarela::find($request->id);

        DB::beginTransaction();

        try {
            if (
                ($user->level == 'Ketua') ||
                ($sukarela->flag == 1 && $sukarela->id_user == $user->id)
            ) {
                if ($sukarela->flag == 2) {
                    Saldo::minus($sukarela->id_user, $sukarela->nominal);
                }

                $res = Transaksi::where('id_pembayaran', $request->id)->delete();
                $sukarela->delete();
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
            'message' => 'Succes Delete',
        ]);
    }

    public function acc(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:sukarela,id',
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

        if ($level == 'Anggota') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak punya akses',
            ]);
        }

        DB::beginTransaction();

        try {
            $transaksi = Transaksi::where('id_pembayaran', $request->id)->where('pembayaran', 'Sukarela')->first();
            if ($request->acc) {
                $flag = 2;
            } else {
                $flag = 0;
            }
            $transaksi->tgl_acc = date('Y-m-d H:i:s');
            $transaksi->id_acc = $id_user;
            $transaksi->flag = $flag;
            $transaksi->note = $request->note;
            $transaksi->save();

            $sukarela = TabunganSukarela::find($request->id);
            if ($request->acc) {
                $flag = 2;
                if ($sukarela->flag != 2) {
                    Saldo::add($sukarela->id_user, $sukarela->nominal);
                }
            } else {
                $flag = 0;
                if ($sukarela->flag == 2) {
                    Saldo::minus($sukarela->id_user, $sukarela->nominal);
                }
            }
            $sukarela->flag = $flag;
            $sukarela->save();
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
            'message' => 'Success Give Approve',
        ]);
    }
}
