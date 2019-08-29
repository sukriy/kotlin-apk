package com.example.koperasi.activity

import android.content.Intent
import android.support.v7.app.AppCompatActivity
import android.os.Bundle
import android.view.View
import android.view.WindowManager
import android.widget.*
import com.example.koperasi.R
import com.example.koperasi.SharedPrefManager
import com.example.koperasi.api.RetrofitClient
import com.example.koperasi.models.DefaultResponse
import kotlinx.android.synthetic.main.activity_reset_password.*
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class resetPasswordActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_reset_password)

        txt_register.setOnClickListener{
            startActivity(Intent(applicationContext, LoginActivity::class.java))
            finish()
        }

        btn_process.setOnClickListener {
            btn_process.setEnabled(false)
            val email = txt_email.text.toString().trim();

            if (email.isEmpty()) {
                txt_email.error = "Email is required";
                txt_email.requestFocus();
                btn_process.setEnabled(true)
                return@setOnClickListener;
            } else {
                getWindow().setFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE, WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                pBar.setVisibility(View.VISIBLE);

                val filter = HashMap<String, String>()
                filter["email"] = email

                RetrofitClient.instance.requestForgotPassword(filter)
                    .enqueue(object : Callback<DefaultResponse> {
                        override fun onFailure(call: Call<DefaultResponse>, t:Throwable){
                            println("onFailure")
                            println(t)
                            if(t.message.toString() != ""){
                                Toast.makeText(applicationContext, t.message, Toast.LENGTH_LONG).show()
                            }else{
                                Toast.makeText(applicationContext, "Check kondisi Internet Anda", Toast.LENGTH_LONG).show()
                            }
                            getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                            pBar.setVisibility(View.GONE);
                            btn_process.setEnabled(true)
                        }
                        override fun onResponse(call: Call<DefaultResponse>, response: Response<DefaultResponse>){
                            println("onResponse")
                            println(response.body())

                            Toast.makeText(applicationContext, response.body()?.message, Toast.LENGTH_LONG).show()

                            getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                            pBar.setVisibility(View.GONE);
                            btn_process.setEnabled(true)
                        }
                    })
            }
        }


    }
    override fun onStart() {
        super.onStart()
        println("onStart LoginActivity")
        println(SharedPrefManager.getInstance(this).isLoggedIn);
        if(SharedPrefManager.getInstance(this).isLoggedIn){
            val intent = Intent(this, HomeActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            startActivity(intent)
        }
    }
}
