<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Session;
use Str;

class RegisterController extends Controller
{
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'namalengkap' => 'required|min:6|max:250',
            'username' => 'required|min:6|max:250|unique:users,username',
            'password' => 'required|min:6|max:250|confirmed',
            'email' => 'required|email|unique:users,email',
            'jabatan' => 'required|in:Staff,SPV,Manager',
            'email' => 'required|email|unique:users,email',
            'alamat' => 'required|max:250',
            'gaji' => 'required|numeric|digits_between:0,11',
            'jenis_kelamin' => 'required|boolean',
            'telepon' => 'required|numeric|digits_between:6,190',
            'gambar' => 'image|max:2048|nullable',
            'tgl_join' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
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
            $user->jabatan = $request->input('jabatan');
            $user->jenis_kelamin = $request->input('jenis_kelamin');
            $user->gaji = $request->input('gaji');
            $user->alamat = $request->input('alamat');
            $user->telepon = $request->input('telepon');
            $user->gambar = $imageName;
            $user->tgl_join = $request->input('tgl_join');
            $user->api_token = Str::random(60);
            $user->flag = 1;
            $user->save();
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->errorInfo[2],
            ]);
        }

        if ($request->input('device', '') == 1) {
            $user = User::where('username', $request->input('username'))->first();
            Auth::guard()->login(User::find($user->id));
            Session::put('id', $user->id);
            Session::put('namalengkap', $user->namalengkap);
            Session::put('api_token', $user->api_token);
            Session::put('level', $user->level);
            Session::put('gambar', $user->gambar);
            Session::put('flag', $user->flag);
            Session::put('saldo', 0);

            return response()->json([
                'success' => true,
                'message' => 'Success Register',
            ]);
        }

        $data = User::where('id', $user->id)
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
            'message' => 'Success Register',
            'data' => $data,
        ]);
    }
}
