package com.example.koperasi

import android.widget.Toast
import com.example.koperasi.api.RetrofitClient
import com.example.koperasi.models.DefaultResponse
import com.google.firebase.iid.FirebaseInstanceId
import com.google.firebase.iid.FirebaseInstanceIdService
import kotlinx.android.synthetic.main.activity_home.*
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class MyFirebaseInstanceIdService : FirebaseInstanceIdService() {

    val TAG = "PushNotifService"
    lateinit var name: String
    lateinit var fcm_token: String

    override fun onCreate() {
        println("Service onCreate")
        this.onTokenRefresh()
    }

    override fun onTokenRefresh() {
        // Mengambil token perangkat
        var token = FirebaseInstanceId.getInstance().token
        println("Token = "+token)
//        Toast.makeText(applicationContext, token, Toast.LENGTH_LONG).show()

        // Jika ingin mengirim push notifcation ke satu atau sekelompok perangkat,
        // simpan token ke server di sini.
        if(SharedPrefManager.getInstance(this).isLoggedIn){
            val filter = HashMap<String, String>()
            filter["api_token"] = SharedPrefManager.getInstance(this).user.api_token.toString()
            filter["fcm_token"] = token.toString()
            fcm_token = token.toString()

            RetrofitClient.instance.requestUpdateToken(filter)
                .enqueue(object : Callback<DefaultResponse> {
                    override fun onFailure(call: Call<DefaultResponse>, t:Throwable){
                        println("Update token error")
                        Toast.makeText(applicationContext, t.message, Toast.LENGTH_LONG).show()

                    }
                    override fun onResponse(call: Call<DefaultResponse>, response: Response<DefaultResponse>){
                        println("Update token onResponse")
                    }
                })

        }
    }

}