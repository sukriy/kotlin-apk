package com.example.koperasi.models

class PinjamanData(
    val id : Int,
    val id_user : Int,
    val nominal : Int,
    val tenor : Int,
    val bunga : String,
    val cicilan : Int,
    val keterangan : String,
    val tgl_acc_admin : String,
    val id_admin : Int,
    val acc_admin : Int,
    val note_admin : String,
    val tgl_acc_ketua : String,
    val id_ketua : Int,
    val acc_ketua : Int,
    val note_ketua : String,
    val flag : Int,
    val created_at : String,
    val updated_at : String,
    val namalengkap : String,
    val bayar : Int,
    val gambar : String,
    val lastPay : Int,
    val status : String
)