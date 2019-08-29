package com.example.koperasi.models

data class DataPinjaman (
    val id: Int,
    val namalengkap: String,
    val nominal: Int,
    val bunga: String,
    val cicilan: Int,
    val tenor: Int,
    val bayar: Int,
    val gambar: String?,
    val status: String,
    val flag: Int
)
