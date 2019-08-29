<?php

namespace App\Http\Controllers;

use App\Pinjaman;
use App\Saldo;
use App\Sukarela;
use App\TabunganSukarela;
use App\Transaksi;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use MyHelper;
use Session;
use Validator;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['monthlyEvent', 'UpdateToken', 'history', 'report', 'saldo', 'saldo_detail']]);
        // $this->middleware('auth', ['except' => ['monthlyEvent']]);
    }

    public function index()
    {
        // Cache::flush();
        // dd(Cache::get('token'));
        // dd('aa');

        // $body = 'Test';
        // $title = 'Monthly Reduction';
        // MyHelper::sendNotif('3', $title, $body);

        $user = User::find(Session::get('id'));
        Session::put('namalengkap', $user->namalengkap);
        Session::put('api_token', $user->api_token);
        Session::put('level', $user->level);
        if ($user->gambar == '') {
            Session::put('gambar', 'image.png');
        } else {
            Session::put('gambar', $user->gambar);
        }

        Session::put('flag', $user->flag);
        $saldo = Saldo::find($user->id);
        if ($saldo) {
            Session::put('saldo', $saldo->nominal);
        } else {
            Session::put('saldo', 0);
        }

        return view('home');
    }

    public function UpdateToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $list = Cache::get('token');
        // dd($list);
        $ada = '';
        if ($list != null) {
            foreach ($list as $key => $value) {
                if ($value == $request->fcm_token) {
                    $ada = $key;
                }
            }
        }
        if ($ada != '') {
            unset($list[$ada]);
        }
        $user = User::where('api_token', $request->api_token)->first();
        $list['token_'.$user->id] = $request->fcm_token;
        Cache::put('token', $list);

        return response()->json([
            'success' => true,
            'message' => 'Success',
        ]);
    }

    public function history(Request $request)
    {
        $user = User::where('api_token', $request->api_token)->first();
        $cicilan = Pinjaman::where('id_user', $user->id)->select('id')->get()->toArray();
        $sukarela = TabunganSukarela::where('id_user', $user->id)->select('id')->get()->toArray();

        $transaksi = Transaksi::where(function ($query) use ($user) {
            $query->where('id_pembayaran', $user->id)
                ->whereIn('pembayaran', ['Bulanan', 'Wajib']);
        })
            ->orWhere(function ($query) use ($cicilan) {
                $query->whereIn('id_pembayaran', $cicilan)
                    ->where('pembayaran', DB::raw("'Cicilan'"));
            })
            ->orWhere(function ($query) use ($sukarela) {
                $query->whereIn('id_pembayaran', $sukarela)
                    ->where('pembayaran', DB::raw("'Sukarela'"));
            })
            ->orderBy('transaksi.tgl_pembayaran', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transaksi,
        ]);
    }

    public function monthlyEvent()
    {
        date_default_timezone_set('Asia/Jakarta');
        $users = User::whereIn('flag', [2, 3])->where('level', 'Anggota')->get();
        $bulanan = 50000;
        DB::beginTransaction();

        $transaksi = Transaksi::where('flag', [1])
            ->where('pembayaran', DB::raw("'Cicilan'"))
            ->count();
        if ($transaksi > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Harap validasi semua transaksi',
            ]);
        }

        try {
            foreach ($users as $user) {
                $total = 0;
                $list = [];
                $keterangan = '';
                if ($user->flag == 2) {
                    $pinjaman = Pinjaman::where('id_user', $user->id)->whereIn('flag', [3, 4])->first();

                    if ($pinjaman) {
                        $transaksi = Transaksi::where('id_pembayaran', $pinjaman->id)
                            ->whereNotIn('flag', [0, 5])
                            ->where('pembayaran', DB::raw("'Cicilan'"))
                            ->select(DB::raw('SUM(nominal) as bayar'))
                            ->groupBy('id_pembayaran')
                            ->first();

                        if (count($transaksi) > 0) {
                            $bayar = $transaksi->bayar;
                        } else {
                            $bayar = 0;
                        }

                        $sisa = ($pinjaman->cicilan * $pinjaman->tenor) - $bayar;
                        if ($sisa <= $pinjaman->cicilan) {
                            $bayar = $sisa;
                        } else {
                            $bayar = $pinjaman->cicilan;
                        }

                        $transaksi = new Transaksi();
                        $transaksi->id_pembayaran = $pinjaman->id;
                        $transaksi->tgl_pembayaran = date('Y-m-d');
                        $transaksi->nominal = $bayar;
                        $transaksi->pembayaran = 'Cicilan';
                        $transaksi->jenis = 'Transfer';
                        $transaksi->flag = 2;
                        $transaksi->save();
                        $list['Cicilan'] = $bayar;
                        $total += $bayar;

                        $transaksi = Transaksi::where('id_pembayaran', $pinjaman->id)
                            ->whereNotIn('flag', [0, 5])
                            ->where('pembayaran', DB::raw("'Cicilan'"))
                            ->select(DB::raw('SUM(nominal) as bayar'))
                            ->groupBy('id_pembayaran')
                            ->first();

                        if ($transaksi->bayar == ($pinjaman->cicilan * $pinjaman->tenor)) {
                            $pinjaman->flag = 5;
                            $pinjaman->save();
                        } else {
                            $pinjaman->flag = 4;
                            $pinjaman->save();
                        }
                    }

                    $transaksi = new Transaksi();
                    $transaksi->id_pembayaran = $user->id;
                    $transaksi->tgl_pembayaran = date('Y-m-d');
                    $transaksi->nominal = $bulanan;
                    $transaksi->jenis = 'Transfer';
                    $transaksi->pembayaran = 'Bulanan';
                    $transaksi->flag = 2;
                    $transaksi->save();
                    Saldo::add($user->id, $bulanan);

                    $list['Bulanan'] = $bulanan;
                    $total += $bulanan;
                } else {
                    $pinjaman = Pinjaman::where('id_user', $user->id)->whereIn('flag', [3, 4])->first();
                    if ($pinjaman) {
                        $transaksi = Transaksi::where('id_pembayaran', $pinjaman->id)
                            ->where('pembayaran', DB::raw("'Cicilan'"))
                            ->whereNotIn('flag', [0, 5])
                            ->select(DB::raw('SUM(nominal) as bayar'))
                            ->groupBy('id_pembayaran')
                            ->first();

                        if (count($transaksi) > 0) {
                            $bayar = $transaksi->bayar;
                        } else {
                            $bayar = 0;
                        }

                        $saldo = Saldo::find($user->id);
                        if ($saldo) {
                            $saldo = $saldo->nominal;
                        } else {
                            $saldo = 0;
                        }

                        if (count($pinjaman) > 0) {
                            $sisa = ($pinjaman->cicilan * $pinjaman->tenor) - $bayar;
                            $transaksi = new Transaksi();
                            $transaksi->id_pembayaran = $pinjaman->id;
                            $transaksi->tgl_pembayaran = date('Y-m-d');
                            $transaksi->nominal = $sisa;
                            $transaksi->pembayaran = 'Cicilan';
                            $transaksi->jenis = 'Transfer';
                            $transaksi->flag = 2;
                            $transaksi->save();

                            $list['Cicilan'] = $sisa;
                            $total += $sisa;

                            if ($saldo > 0) {
                                $sisa = (int) $sisa - (int) $saldo;

                                if ($sisa < 0) {
                                    $keterangan = 'Anda mempunyai sisa saldo sebesar '.MyHelper::rupiah(abs($sisa));
                                }
                                $list['Saldo'] = $saldo * -1;
                                $total += ($saldo * -1);
                                $saldo = Saldo::find($user->id);
                                $saldo->nominal = 0;
                                $saldo->save();
                            }

                            $pinjaman->flag = 5;
                            $pinjaman->save();

                            $temp = User::find($user->id);
                            $temp->flag = 4;
                            $temp->save();
                        }
                    }
                }
                $body = 'check your account';
                $title = 'Monthly Reduction';
                MyHelper::sendNotif($user->id, $title, $body);
                MyHelper::emailDetailPotongan($user->id, $list, $total, $keterangan);
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
            'message' => 'Success',
        ]);
    }

    public function saldo(Request $request)
    {
        $user = User::where('api_token', $request->api_token)->first();
        if ($user->level == 'Anggota') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak punya akses',
            ]);
        }
        $list = Saldo::leftJoin('users', 'users.id', 'saldo.id_user')
            ->select('saldo.*', 'users.namalengkap')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $list,
        ]);
    }

    public function saldo_detail(Request $request)
    {
        $user = User::find($request->id);
        $cicilan = Pinjaman::where('id_user', $user->id)->select('id')->get()->toArray();
        $sukarela = TabunganSukarela::where('id_user', $user->id)->select('id')->get()->toArray();

        $transaksi = Transaksi::where(function ($query) use ($user) {
            $query->where('id_pembayaran', $user->id)
                ->whereIn('pembayaran', ['Bulanan', 'Wajib']);
        })
            ->orWhere(function ($query) use ($cicilan) {
                $query->whereIn('id_pembayaran', $cicilan)
                    ->where('pembayaran', DB::raw("'Cicilan'"));
            })
            ->orWhere(function ($query) use ($sukarela) {
                $query->whereIn('id_pembayaran', $sukarela)
                    ->where('pembayaran', DB::raw("'Sukarela'"));
            })
            ->orderBy('transaksi.tgl_pembayaran', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transaksi,
        ]);
    }

    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis' => 'required|in:Sukarela,Pinjaman,Transaksi',
            'tgl_dari' => 'required|date_format:Y-m-d',
            'tgl_sampai' => 'required|date_format:Y-m-d|after_or_equal:'.$request->input('tgl_dari'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        $user = User::where('api_token', $request->api_token)->first();
        $from = $request->input('tgl_dari').' 00:00:00';
        $to = $request->input('tgl_sampai').' 23:59:59';

        if ($request->input('jenis') == 'Sukarela') {
            $list = Sukarela::leftJoin('users', 'users.id', '=', 'sukarela.id_user')
                ->whereBetween('sukarela.created_at', [$from, $to])
                ->select('sukarela.*', 'users.namalengkap');

            if ($user->level == 'Anggota') {
                $list = $list->where('sukarela.id_user', $user->id);
            }
            $list = $list->get();
        } elseif ($request->input('jenis') == 'Pinjaman') {
            $list = Pinjaman::list($user->id, $user->level, $from, $to);
        } elseif ($request->input('jenis') == 'Transaksi') {
            $user = User::where('api_token', $request->api_token)->first();

            if ($user->level == 'Anggota') {
                $list = Transaksi
               ::leftJoin('users', 'users.id', '=', 'transaksi.id_pembayaran')
                   ->where(function ($query) use ($user, $from, $to) {
                       $query->whereBetween('tgl_pembayaran', [$from, $to])
                           ->where('transaksi.id_pembayaran', $user->id)
                           ->whereIn('pembayaran', ['Bulanan', 'Wajib']);
                   })
                   ->select('transaksi.*', 'users.namalengkap')
                   ->get();

                $cicilan = Transaksi
               ::join('pinjaman', 'pinjaman.id', '=', 'transaksi.id_pembayaran')
                   ->leftJoin('users', 'users.id', '=', 'pinjaman.id_user')
                   ->where(function ($query) use ($user, $from, $to) {
                       $query->whereBetween('tgl_pembayaran', [$from, $to])
                           ->where('pinjaman.id_user', $user->id)
                           ->whereIn('pembayaran', ['Cicilan']);
                   })
                   ->select('transaksi.*', 'users.namalengkap')
                   ->get();

                $sukarela = Transaksi
               ::join('sukarela', 'sukarela.id', '=', 'transaksi.id_pembayaran')
                   ->leftJoin('users', 'users.id', '=', 'sukarela.id_user')
                   ->where(function ($query) use ($from, $to) {
                       $query->whereBetween('tgl_pembayaran', [$from, $to])
                           ->where('sukarela.id_user', $user->id)
                           ->whereIn('pembayaran', ['Sukarela']);
                   })
                   ->select('transaksi.*', 'users.namalengkap')
                   ->get();
            } else {
                $list = Transaksi
               ::leftJoin('users', 'users.id', '=', 'transaksi.id_pembayaran')
                   ->where(function ($query) use ($from, $to) {
                       $query->whereBetween('tgl_pembayaran', [$from, $to])
                           ->whereIn('pembayaran', ['Bulanan', 'Wajib']);
                   })
                   ->select('transaksi.*', 'users.namalengkap')
                   ->get();

                $cicilan = Transaksi
               ::join('pinjaman', 'pinjaman.id', '=', 'transaksi.id_pembayaran')
                   ->leftJoin('users', 'users.id', '=', 'pinjaman.id_user')
                   ->where(function ($query) use ($from, $to) {
                       $query->whereBetween('tgl_pembayaran', [$from, $to])
                           ->whereIn('pembayaran', ['Cicilan']);
                   })
                   ->select('transaksi.*', 'users.namalengkap')
                   ->get();

                $sukarela = Transaksi
               ::join('sukarela', 'sukarela.id', '=', 'transaksi.id_pembayaran')
                   ->leftJoin('users', 'users.id', '=', 'sukarela.id_user')
                   ->where(function ($query) use ($from, $to) {
                       $query->whereBetween('tgl_pembayaran', [$from, $to])
                           ->whereIn('pembayaran', ['Sukarela']);
                   })
                   ->select('transaksi.*', 'users.namalengkap')
                   ->get();
            }

            $list = $list->merge($cicilan);
            $list = $list->merge($sukarela);
        }

        return response()->json([
            'success' => true,
            'message' => $list,
        ]);
    }
}
