package com.example.koperasi.api

import com.example.koperasi.models.*
import retrofit2.Call
import retrofit2.http.*
import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.http.POST
import retrofit2.http.Multipart
import kotlin.collections.HashMap


interface Api {

    @POST("login")
    fun requestLogin(@QueryMap filter: HashMap<String, String>): Call<LoginResponse>

    @Multipart
    @POST("register")
    fun uploadFile(@Part file: MultipartBody.Part, @QueryMap filter: HashMap<String, String>): Call<LoginResponse>

    @POST("pinjaman")
    fun requestPinjaman(@QueryMap filter: HashMap<String, String>): Call<PinjamanResponse>

    @POST("pinjaman_add")
    fun requestPinjamanAdd(@QueryMap filter: HashMap<String, String>): Call<DefaultResponse>

    @POST("pinjaman_detail")
    fun requestPinjamanReview(@QueryMap filter: HashMap<String, String>): Call<PinjamanEditResponse>

    @POST("pinjaman_edit")
    fun requestPinjamanEdit(@QueryMap filter: HashMap<String, String>): Call<DefaultResponse>

    @POST("pinjaman_delete")
    fun requestPinjamanDelete(@QueryMap filter: HashMap<String, String>): Call<DefaultResponse>

    @Multipart
    @POST("pinjaman_pembayaran")
    fun requestPinjamanBayar(@Part file: MultipartBody.Part, @QueryMap filter: HashMap<String, String>): Call<DefaultResponse>

    @POST("bayar_list")
    fun requestPinjamanDetail(@QueryMap filter: HashMap<String, String>): Call<PinjamanDetailResponse>

    @POST("account_detail")
    fun requestProfile(@QueryMap filter: HashMap<String, String>): Call<LoginResponse>

    @Multipart
    @POST("member_bayar")
    fun requestMember(@Part file: MultipartBody.Part, @QueryMap filter: HashMap<String, String>): Call<LoginResponse>

    @Multipart
    @POST("account_edit")
    fun requestProfileEdit(@Part file: MultipartBody.Part, @QueryMap filter: HashMap<String, String>): Call<LoginResponse>

    @POST("history")
    fun requestHistory(@QueryMap filter: HashMap<String, String>): Call<HistoryResponse>

    @POST("tabunganSukarela")
    fun requestSukarela(@QueryMap filter: HashMap<String, String>): Call<SukarelaResponse>

    @POST("tabunganSukarela_detail")
    fun requestSukarelaDetail(@QueryMap filter: HashMap<String, String>): Call<SukarelaDetailResponse>

    @Multipart
    @POST("tabunganSukarela_add")
    fun requestTabunganAdd(@Part file: MultipartBody.Part, @QueryMap filter: HashMap<String, String>): Call<DefaultResponse>

    @Multipart
    @POST("tabunganSukarela_edit")
    fun requestTabunganEdit(@Part file: MultipartBody.Part, @QueryMap filter: HashMap<String, String>): Call<DefaultResponse>

    @POST("tabunganSukarela_delete")
    fun requestSukarelaDelete(@QueryMap filter: HashMap<String, String>): Call<DefaultResponse>

    @POST("changePassword")
    fun requestChangePassword(@QueryMap filter: HashMap<String, String>): Call<DefaultResponse>

    @POST("forgot_password_process")
    fun requestForgotPassword(@QueryMap filter: HashMap<String, String>): Call<DefaultResponse>

    @POST("logout")
    fun requestLogout(@QueryMap filter: HashMap<String, String>): Call<DefaultResponse>

    @POST("updateToken")
    fun requestUpdateToken(@QueryMap filter: HashMap<String, String>): Call<DefaultResponse>

}