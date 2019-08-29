<?php

Route::get('monthlyEvent', 'HomeController@monthlyEvent');
Route::get('forgot_password', 'AccountController@forgot_password');
Route::post('forgot_password_process', 'AccountController@forgot_password_process');
Route::post('send_email', 'MailController@html_email');
Route::get('/reset_password', 'AccountController@resetPassword');

Auth::routes();
Route::get('/', 'HomeController@index');
Route::get('{any}', 'HomeController@index');

$temp = explode('/', parse_url(url()->current(), PHP_URL_PATH));
$exp = ['account_edit'];
if (isset($temp[1])) {
    if ($temp[1] == 'account_edit') {
        header('Location: /account');
        die();
    }
    if ($temp[1] == 'pinjaman_edit') {
        header('Location: /pinjaman');
        die();
    }
    if ($temp[1] == 'pinjaman_detail') {
        header('Location: /pinjaman');
        die();
    }
    if ($temp[1] == 'tabunganSukarela_edit') {
        header('Location: /tabunganSukarela');
        die();
    }
    if ($temp[1] == 'saldo_detail') {
        header('Location: /saldo');
        die();
    }

    // if (in_array($temp[1], $exp)) {
    //     header('Location: /');
    //     die();
    // }
}
