package com.example.koperasi.activity

import android.content.Intent
import android.support.v7.app.AppCompatActivity
import android.os.Bundle
import android.os.Handler
import android.view.View
import android.view.WindowManager
import android.widget.*
import com.example.koperasi.R
import com.example.koperasi.SharedPrefManager
import com.example.koperasi.api.RetrofitClient
import com.example.koperasi.models.LoginResponse
import kotlinx.android.synthetic.main.activity_login.*
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class LoginActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_login)

        if(SharedPrefManager.getInstance(this).isLoggedIn){
            val intent = Intent(this, HomeActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            startActivity(intent)
            finish()
        }
    }

    override fun onStart() {
        super.onStart()
        txt_register.setOnClickListener{
            startActivity(Intent(applicationContext, RegisterActivity::class.java))
        }

        txt_forgot.setOnClickListener{
            startActivity(Intent(applicationContext, resetPasswordActivity::class.java))
        }

        btn_login.setOnClickListener {
            btn_login.setEnabled(false)
            val username = txt_username.text.toString().trim();
            val password = txt_password.text.toString().trim();

            if (username.isEmpty()) {
                txt_username.error = "Username is required";
                txt_username.requestFocus();
                btn_login.setEnabled(true)
                return@setOnClickListener;
            } else if (password.isEmpty()) {
                txt_password.error = "Password is required";
                txt_password.requestFocus();
                btn_login.setEnabled(true)
                return@setOnClickListener;
            } else {
                getWindow().setFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE, WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                pBar.setVisibility(View.VISIBLE);

                val filter = HashMap<String, String>()
                filter["username"] = username
                filter["password"] = password

                RetrofitClient.instance.requestLogin(filter)
                    .enqueue(object : Callback<LoginResponse> {
                        override fun onFailure(call: Call<LoginResponse>, t:Throwable){
                            println("onFailure")
                            println(t)
                            if(t.message.toString() != ""){
                                Toast.makeText(applicationContext, t.message, Toast.LENGTH_LONG).show()
                            }else{
                                Toast.makeText(applicationContext, "Check kondisi Internet Anda", Toast.LENGTH_LONG).show()
                            }
                            getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                            pBar.setVisibility(View.GONE);
                            btn_login.setEnabled(true)
                        }
                        override fun onResponse(call: Call<LoginResponse>, response: Response<LoginResponse>){
                            println("onResponse")
                            println(response.body())

                            if(response.body()?.success == true){
                                SharedPrefManager.getInstance(applicationContext).saveUser(response.body()?.data!!)
                                val intent = Intent(applicationContext, HomeActivity::class.java)
                                intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                                startActivity(intent)
                            }else{
                                Toast.makeText(applicationContext, response.body()?.message, Toast.LENGTH_LONG).show()
                            }
                            getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                            pBar.setVisibility(View.GONE);
                            btn_login.setEnabled(true)
                        }
                    })
            }
        }
    }
    private var doubleBackToExitPressedOnce = false
    override fun onBackPressed() {
        if (doubleBackToExitPressedOnce) {
            super.onBackPressed()
            return
        }

        this.doubleBackToExitPressedOnce = true
        Toast.makeText(this, "Please click BACK again to exit", Toast.LENGTH_SHORT).show()

        Handler().postDelayed(Runnable { doubleBackToExitPressedOnce = false }, 2000)
    }
}