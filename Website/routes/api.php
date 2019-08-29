<?php

Route::middleware(['APIToken'])->group(function () {
    Route::get('/', function () {
        return 'aaa';
    });

    Route::post('/saldo', 'HomeController@saldo');
    Route::post('/saldo_detail', 'HomeController@saldo_detail');
    Route::post('/report', 'HomeController@report');
    Route::post('/history', 'HomeController@history');
    Route::post('/updateToken', 'HomeController@UpdateToken');

    Route::post('/resign', 'AccountController@resign');
    Route::post('/changePassword', 'AccountController@changePassword');
    Route::post('/account_acc', 'AccountController@acc');
    Route::post('/account', 'AccountController@list');
    Route::post('/account_detail', 'AccountController@detail');
    Route::post('/account_add', 'AccountController@add');
    Route::post('/account_edit', 'AccountController@edit');
    Route::post('/account_delete', 'AccountController@del');
    Route::post('/member_bayar', 'AccountController@memberBayar');

    Route::post('/pinjaman', 'PinjamanController@list');
    Route::post('/bayar_list', 'PinjamanController@bayar_list');
    Route::post('/pinjaman_detail', 'PinjamanController@detail');
    Route::post('/pinjaman_add', 'PinjamanController@add');
    Route::post('/pinjaman_edit', 'PinjamanController@edit');
    Route::post('/pinjaman_delete', 'PinjamanController@del');
    Route::post('/pinjaman_acc', 'PinjamanController@acc');
    Route::post('/pinjaman_pembayaran', 'PinjamanController@pembayaran');
    Route::post('/pinjaman_konfirmasi', 'PinjamanController@konfirmasi');

    Route::post('/tabunganSukarela', 'TabunganSukarelaController@list');
    Route::post('/tabunganSukarela_detail', 'TabunganSukarelaController@detail');
    Route::post('/tabunganSukarela_add', 'TabunganSukarelaController@add');
    Route::post('/tabunganSukarela_edit', 'TabunganSukarelaController@edit');
    Route::post('/tabunganSukarela_delete', 'TabunganSukarelaController@del');
    Route::post('/tabunganSukarela_acc', 'TabunganSukarelaController@acc');
});
Route::post('/reset_password', 'AccountController@resetPasswordProses');
// Route::post('/login', 'AccountController@test');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/register', 'Auth\RegisterController@register');
Route::post('/logout', 'Auth\LoginController@logoutApp');
Route::post('/forgot_password_process', 'AccountController@forgot_password_process');
