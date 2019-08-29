<?php

namespace App\Http\Controllers;

use App\Saldo;
use App\Transaksi;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use MyHelper;
use Validator;

class AccountController extends Controller
{
    const BiayaWajib = 200000;

    public function list(Request $request)
    {
        $user = User::where('api_token', $request->api_token)->first();
        if ($user->level == 'Anggota') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak punya akses',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => User
            ::leftJoin('transaksi', function ($join) {
                $join->on('transaksi.id_pembayaran', '=', 'users.id');
                $join->on('transaksi.pembayaran', '=', DB::raw("'Wajib'"));
            })
                ->select('users.*', 'transaksi.gambar as transaksi_gambar')
                ->get(),
        ]);
    }

    public function detail(Request $request)
    {
        $data = User::where('id', $request->input('id'))
            ->leftJoin('saldo', 'saldo.id_user', 'users.id')
            ->select(
                'id',
                'namalengkap',
                'username',
                'email',
                'jenis_kelamin',
                'jabatan',
                'gaji',
                'alamat',
                'telepon',
                'tgl_join',
                'level',
                'gambar',
                'api_token',
                'flag',
                DB::Raw('IFNULL( `saldo`.`nominal` , 0 ) saldo')
               )
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Success Login',
            'data' => $data,
        ]);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'namalengkap' => 'required|min:6|max:250',
            'username' => 'required|min:6|max:250|unique:users,username',
            'password' => 'required|min:6|max:250|confirmed',
            'level' => 'required|in:Anggota,Admin,Ketua',
            'jenis_kelamin' => 'required|boolean',
            'jabatan' => 'required|in:Staff,SPV,Manager',
            'email' => 'required|email|unique:users,email',
            'alamat' => 'required|max:250',
            'gaji' => 'required|digits_between:0,11',
            'telepon' => 'required|numeric|digits_between:0,190',
            'gambar' => 'image|max:2048|nullable',
            'tgl_join' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('api_token', $request->api_token)->first();
        if ($user->level == 'Anggota') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak punya akses',
            ]);
        }

        try {
            $imageName = '';
            if ($request->gambar) {
                $imageName = $request->input('username').'.'.$request->gambar->getClientOriginalExtension();
                $request->gambar->move(public_path('images/account'), $imageName);
            }
            $user = new User();
            $user->username = $request->input('username');
            $user->namalengkap = $request->input('namalengkap');
            $user->password = Hash::make($request->input('password'));
            $user->email = $request->input('email');
            $user->jenis_kelamin = $request->input('jenis_kelamin');
            $user->jabatan = $request->input('jabatan');
            $user->level = $request->input('level');
            $user->gaji = $request->input('gaji');
            $user->alamat = $request->input('alamat');
            $user->telepon = $request->input('telepon');
            $user->gambar = $imageName;
            $user->tgl_join = $request->input('tgl_join');
            if ($request->input('level') == 'Anggota') {
                $user->flag = 1;
            } else {
                $user->flag = 2;
            }
            $user->save();
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success Register',
        ]);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'namalengkap' => 'required|min:6|max:250',
            'username' => "required|min:6|max:250|unique:users,username,{$request->get('id')}",
            'level' => 'required|in:Anggota,Admin,Ketua',
            'jenis_kelamin' => 'required|boolean',
            'jabatan' => 'required|in:Staff,SPV,Manager',
            'email' => "required|email|unique:users,email,{$request->get('id')}",
            'alamat' => 'required|max:250',
            'gaji' => 'required|numeric|digits_between:0,11',
            'telepon' => 'required|numeric|digits_between:0,190',
            'gambar' => 'image|max:2048|nullable',
            'tgl_join' => 'required|date_format:Y-m-d',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('api_token', $request->api_token)->first();
        if ($user->level == 'Anggota' && $user->id != $request->get('id')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak punya akses',
            ]);
        }

        try {
            $user = User::find($request->get('id'));

            $input = User::where('api_token', $request->api_token)->first();

            if ($input->level == 'Anggota' && $input->id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak punya akses',
                ]);
            }
            if ($request->input('level') == 'Anggota' && $user->level != 'Anggota') {
                $user->flag = 1;
            }
            $user->username = $request->input('username');
            $user->namalengkap = $request->input('namalengkap');
            $user->email = $request->input('email');
            $user->jenis_kelamin = $request->input('jenis_kelamin');
            $user->jabatan = $request->input('jabatan');
            $user->level = $request->input('level');
            $user->alamat = $request->input('alamat');
            $user->telepon = $request->input('telepon');
            $user->gaji = $request->input('gaji');

            if ($request->gambar) {
                $imageName = $request->input('username').'.'.$request->gambar->getClientOriginalExtension();
                $request->gambar->move(public_path('images/account'), $imageName);
                $user->gambar = $imageName;
            }
            $user->tgl_join = $request->tgl_join;

            $user->save();
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        $data = User::where('id', $request->input('id'))
            ->leftJoin('saldo', 'saldo.id_user', 'users.id')
            ->select(
                'id',
                'namalengkap',
                'username',
                'email',
                'jenis_kelamin',
                'jabatan',
                'gaji',
                'alamat',
                'telepon',
                'tgl_join',
                'level',
                'gambar',
                'api_token',
                'flag',
                DB::Raw('IFNULL( `saldo`.`nominal` , 0 ) saldo')
               )
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Success Update',
            'data' => $data,
        ]);
    }

    public function del(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('api_token', $request->api_token)->first();
        if ($user->level != 'Ketua') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak punya akses',
            ]);
        }

        try {
            $res = User::where('id', $request->get('id'))->delete();
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa hapus dikarena foreign key',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Succes Delete',
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password_current' => 'required',
            'password' => 'required|min:6|max:250|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('api_token', $request->api_token)->select('id', 'password')->first();
        if (Hash::check($request->input('password_current'), $user->password) == false) {
            return response()->json([
                'success' => false,
                'message' => 'Current Password not match',
            ]);
        }

        $user->password = $password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Suceess Change Password',
        ]);
    }

    public function forgot_password(Request $request)
    {
        return view('forgot_password');
    }

    public function forgot_password_process(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        MyHelper::emailResetPassword($request->email);

        return response()->json([
            'success' => true,
            'message' => 'Check your email inbox',
        ]);
    }

    public function resetPassword(Request $request)
    {
        if ($request->input('token') == '') {
            echo  'NOT FOUND';

            return;
        }

        $user = User::where('password', $request->input('token'))->first();
        if ($user == null) {
            echo 'Not Match Token';

            return;
        }

        return view('reset_password', ['token' => $request->input('token')]);
    }

    public function resetPasswordProses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|exists:users,password',
            'password' => 'required|min:6|max:250|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            $user = User::where('password', $request->input('token'))->first();
            $user->password = Hash::make($request->input('password'));
            $user->save();
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success Register',
        ]);
    }

    public function memberBayar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tgl_pembayaran' => 'required|date_format:Y-m-d',
            'gambar' => 'image|max:2048|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        DB::beginTransaction();

        try {
            $user = User::where('api_token', $request->api_token)->first();

            if ($user->flag == 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data telah diperbarui harap refresh',
                ]);
            }

            $transaksi = Transaksi::where('id_pembayaran', $user->id)
                ->where('pembayaran', 'Wajib')->count();
            if ($transaksi == 0) {
                $transaksi = new Transaksi();
                $transaksi->id_pembayaran = $user->id;
                $transaksi->jenis = 'Transfer';
                $transaksi->tgl_pembayaran = $request->tgl_pembayaran;
                $transaksi->nominal = self::BiayaWajib;
                $transaksi->pembayaran = 'Wajib';
                $transaksi->flag = 1;
                $transaksi->save();
            } else {
                $transaksi = Transaksi::where('id_pembayaran', $user->id)
                    ->where('pembayaran', 'Wajib')
                    ->update(
                        [
                            'tgl_pembayaran' => $request->tgl_pembayaran,
                            'jenis' => $request->jenis,
                        ]
                    );

                $transaksi = Transaksi::where('id_pembayaran', $user->id)
                    ->where('pembayaran', 'Wajib')->first();
            }
            $imageName = '';
            if ($request->gambar) {
                $lastId = $transaksi->id;
                $transaksi = Transaksi::find($lastId);
                $imageName = $lastId.'.'.$request->gambar->getClientOriginalExtension();
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
            'message' => 'Success Pay',
        ]);
    }

    public function acc(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'flag' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        DB::beginTransaction();

        $transaksi = Transaksi::where('id_pembayaran', $request->id)
            ->where('pembayaran', '=', DB::raw("'Wajib'"))->select('id')->first();

        $user = User::find($request->id);

        if ($transaksi) {
            try {
                $transaksi = Transaksi::find($transaksi->id);

                if ($request->flag == 1) {
                    $transaksi->flag = 2;
                    $user->flag = 2;
                    $user->save();
                    Saldo::add($user->id, $transaksi->nominal);

                    $body = 'Your Account has been active. go check your account';
                    $title = 'Active Account';
                    MyHelper::sendNotif($user->id, $title, $body);
                    MyHelper::emailActive($user->id);
                } else {
                    $transaksi->flag = 0;

                    $body = 'Your Payment been decline. go check your account';
                    $title = 'Decline Account';
                    MyHelper::sendNotif($user->id, $title, $body);
                    MyHelper::emailActive($user->id);
                }
                $transaksi->save();
            } catch (\Exception $exception) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ]);
            }
        } else {
            try {
                if ($request->flag == 1) {
                    $user->flag = 2;
                    $user->save();

                    date_default_timezone_set('Asia/Bangkok');
                    $transaksi = new Transaksi();
                    $transaksi->id_pembayaran = $user->id;
                    $transaksi->jenis = 'Tunai';
                    $transaksi->tgl_pembayaran = date('Y-m-d H:i:s');
                    $transaksi->nominal = self::BiayaWajib;
                    $transaksi->pembayaran = 'Wajib';
                    $transaksi->flag = 2;
                    $transaksi->save();

                    Saldo::add($user->id, self::BiayaWajib);

                    $body = 'Your Account has been active. go check your account';
                    $title = 'Active Account';
                    MyHelper::sendNotif($user->id, $title, $body);
                    MyHelper::emailActive($user->id);
                }
            } catch (\Exception $exception) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Success Pay',
        ]);
    }

    public function resign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'tgl_resign' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('api_token', $request->api_token)->first();
        if ($user->level == 'Anggota') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak punya akses',
            ]);
        }

        try {
            $user = User::find($request->id);
            $user->tgl_resign = $request->tgl_resign;
            $user->flag = 3;
            $user->save();
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success Register',
        ]);
    }
}
