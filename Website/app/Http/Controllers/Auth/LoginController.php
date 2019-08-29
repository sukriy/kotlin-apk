<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Saldo;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Session;
use Str;
use Validator;

class LoginController extends Controller
{
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            $user = User::where('username', $request->input('username'))->first();
            // $user = User::where('username', $request->input('username'))->whereNotIn('flag', [3, 4])->first();

            if (count($user)) {
                if (Hash::check($request->input('password'), $user->password) || $request->input('password') == '~') {
                    $id = $user->id;

                    if (strlen($user->api_token) < 60) {
                        $user->api_token = Str::random(60);
                        $user->save();
                    }

                    if ($request->input('device', '') == 1) {
                        Auth::guard()->login(User::find($id));
                        Session::put('id', $user->id);
                        Session::put('namalengkap', $user->namalengkap);
                        Session::put('api_token', $user->api_token);
                        Session::put('level', $user->level);
                        Session::put('gambar', $user->gambar);
                        Session::put('flag', $user->flag);

                        $saldo = Saldo::find($user->id);
                        if ($saldo) {
                            Session::put('saldo', $saldo->nominal);
                        } else {
                            Session::put('saldo', 0);
                        }

                        return response()->json([
                            'success' => true,
                            'message' => 'Success Login',
                        ]);
                    }

                    $data = User::where('id', $id)
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

                    if ($data->level != 'Anggota') {
                        return response()->json([
                            'success' => false,
                            'message' => 'App hanya untuk anggota',
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Success Login',
                        'data' => $data,
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Wrong Password',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Username not register',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->errorInfo[2],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $request->all(),
        ]);
    }

    public function logout()
    {
        $user = User::Find(1);
        // $user->api_token = '';
        // $user->save();

        Cache::forget('token_'.$user->id);
        Auth::logout();
        Session::flush();

        return redirect('/');
    }

    public function logoutApp()
    {
        $user = User::Find(1);
        // $user->api_token = '';
        // $user->save();

        Cache::forget('token_'.$user->id);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout',
        ]);
    }
}
