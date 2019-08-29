<?php

namespace App;

use Illuminate\Support\Facades\Cache;
use Mail;

class MyHelper
{
    public static function coba()
    {
        echo 'coba';
    }

    public static function calculate($pinjaman, $bunga, $tenor)
    {
        $bunga = preg_replace('/%/', '', $bunga) / 100;
        $cal = (int) ceil(($bunga * $pinjaman) + ($pinjaman / $tenor));

        return $cal;
    }

    public static function sendNotif($userid = '', $title = '', $body = '')
    {
        $list = Cache::get('token');
        if ($list == null) {
            return '';
        }

        if (!array_key_exists('token_'.$userid, $list)) {
            return '';
        }
        $token = $list['token_'.$userid];
        // echo 'userid = '.$userid.'; token = '.$token.'<br>';

        $key = 'AAAAAg4iG5E:APA91bEd-kWNX95VnTcwW71VToLeztvzHitusc7x5ylp9Uucb-14d5_3zY5PANdntugCeuFdwIbYSgVbk079eXWxnaAr21fWYG3DSmWU6UBcVIk1R_seREL9Ai2V2EeW-G-F_xvhYinT';
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = json_encode([
            'to' => $token,
            'notification' => [
                'body' => $body,
                'title' => $title,
                'icon' => '/images/chiby.png',
            ],
        ]);

        $headers = [
            'Authorization: key='.$key,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        // echo $result;
        curl_close($ch);
    }

    public static function emailActive($id)
    {
        $user = User::find($id);
        $data = ['namalengkap' => $user->namalengkap];
        Mail::send('activeAccount', $data, function ($message) use ($user) {
            $message->to($user->email, $user->namalengkap)->subject('Active Account');
            $message->from('xyz@gmail.com', 'Admin Koperasi');
        });
    }

    public static function emailResetPassword($email)
    {
        $user = User::where('email', $email)->first();
        $data = ['token' => $user->password];
        Mail::send('mail', $data, function ($message) use ($user) {
            $message->to($user->email, $user->namalengkap)->subject('Reset Password');
            $message->from('xyz@gmail.com', 'Admin Koperasi');
        });
    }

    public static function emailDetailPotongan($id, $lists, $total, $keterangan = '')
    {
        $user = User::find($id);
        $temp = $user->namalengkap;
        $data = ['lists' => $lists, 'total' => $total, 'namalengkap' => $user->namalengkap, 'keterangan' => $keterangan];
        Mail::send('detailEmail', $data, function ($message) use ($user) {
            $message->to($user->email, $user->namalengkap)->subject('Potongan Gaji');
            $message->from('xyz@gmail.com', 'Admin Koperasi');
        });

        $user = User::find('1');
        $data = ['lists' => $lists, 'total' => $total, 'namalengkap' => $temp, 'keterangan' => $keterangan];
        Mail::send('detailEmail', $data, function ($message) use ($user) {
            $message->to($user->email, $user->namalengkap)->subject('Potongan Gaji CHECKER');
            $message->from('xyz@gmail.com', 'Admin Koperasi');
        });
    }

    public static function rupiah($angka)
    {
        $hasil_rupiah = 'Rp '.number_format($angka, 0, ',', '.');

        return $hasil_rupiah;
    }
}
