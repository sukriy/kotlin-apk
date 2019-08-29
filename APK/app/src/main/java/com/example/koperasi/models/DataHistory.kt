package com.example.koperasi.models

data class DataHistory(
    val id: Int,
    val id_pembayaran: Int,
    val tgl_pembayaran: String,
    val nominal: Int,
    val jenis: String,
    val pembayaran: String,
    val note: String?,
    val status: String
)