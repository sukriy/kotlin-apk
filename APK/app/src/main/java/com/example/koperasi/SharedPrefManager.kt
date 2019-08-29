package com.example.koperasi

import android.content.Context
import com.example.koperasi.models.LoginData

class SharedPrefManager private constructor(private val mCtx: Context) {

    val isLoggedIn: Boolean
        get() {
            val sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE)
            return sharedPreferences.getInt("id", -1) != -1
        }

    val user: LoginData
        get() {
            val sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE)

            return LoginData(
                sharedPreferences.getInt("id", -1),
                sharedPreferences.getString("namalengkap", null),
                sharedPreferences.getString("username", null),
                sharedPreferences.getString("email", null),
                sharedPreferences.getString("jenis_kelamin", null),
                sharedPreferences.getString("jabatan", null),
                sharedPreferences.getInt("gaji", -1),
                sharedPreferences.getString("alamat", null),
                sharedPreferences.getString("telepon", null),
                sharedPreferences.getString("tgl_join", null),
                sharedPreferences.getString("level", null),
                sharedPreferences.getString("gambar", null),
                sharedPreferences.getString("api_token", null),
                sharedPreferences.getInt("flag", -1),
                sharedPreferences.getInt("saldo", -1)
            )
        }


    fun saveUser(user: LoginData) {

        val sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE)
        val editor = sharedPreferences.edit()

        editor.putInt("id", user.id)
        editor.putString("namalengkap", user.namalengkap)
        editor.putString("username", user.username)
        editor.putString("email", user.email)
        editor.putString("jenis_kelamin", user.jenis_kelamin)
        editor.putString("jabatan", user.jabatan)
        editor.putInt("gaji", user.gaji)
        editor.putString("alamat", user.alamat)
        editor.putString("telepon", user.telepon)
        editor.putString("tgl_join", user.tgl_join)
        editor.putString("level", user.level)
        editor.putString("gambar", user.gambar)
        editor.putString("api_token", user.api_token)
        editor.putInt("flag", user.flag)
        editor.putInt("saldo", user.saldo)

        editor.apply()
    }

    fun clear() {
        val sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE)
        val editor = sharedPreferences.edit()
        editor.clear()
        editor.apply()
    }

    companion object {

        private val SHARED_PREF_NAME = "my_shared_preff"

        private var mInstance: SharedPrefManager? = null


        @Synchronized
        fun getInstance(mCtx: Context): SharedPrefManager {
            if (mInstance == null) {
                mInstance = SharedPrefManager(mCtx)
            }
            return mInstance as SharedPrefManager
        }
    }

}
