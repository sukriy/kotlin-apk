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
import kotlinx.android.synthetic.main.activity_change_password.*
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class changePasswordActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_change_password)

        btn_change.setOnClickListener {
            btn_change.setEnabled(false)
            val password_current = txt_oldPass.text.toString().trim();
            val password = txt_password.text.toString().trim();
            val password_confirmation = txt_retypePassword.text.toString().trim();

            if (password_current.isEmpty()) {
                txt_oldPass.error = "Old Password is required";
                txt_oldPass.requestFocus();
                btn_change.setEnabled(true)
                return@setOnClickListener;
            } else if (password.isEmpty()) {
                txt_password.error = "Password is required";
                txt_password.requestFocus();
                btn_change.setEnabled(true)
                return@setOnClickListener;
            } else if (password_confirmation.isEmpty()) {
                txt_retypePassword.error = "Password Retype is required";
                txt_retypePassword.requestFocus();
                btn_change.setEnabled(true)
                return@setOnClickListener;
            } else if (password_confirmation != password) {
                txt_password.error = "Password not match";
                txt_password.requestFocus();
                btn_change.setEnabled(true)
                return@setOnClickListener;
            } else {
                getWindow().setFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE, WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                pBar.setVisibility(View.VISIBLE);

                val filter = HashMap<String, String>()
                filter["password_current"] = password_current
                filter["password"] = password
                filter["password_confirmation"] = password_confirmation
                filter["api_token"] = SharedPrefManager.getInstance(this).user.api_token.toString()

                RetrofitClient.instance.requestChangePassword(filter)
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
                            btn_change.setEnabled(true)
                        }
                        override fun onResponse(call: Call<DefaultResponse>, response: Response<DefaultResponse>){
                            println("onResponse")
                            println(response.body())
                            Toast.makeText(applicationContext, response.body()?.message, Toast.LENGTH_LONG).show()
                            if(response.body()?.success == true){
                                finish()
                            }
                            getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                            pBar.setVisibility(View.GONE);
                            btn_change.setEnabled(true)
                        }
                    })
            }
        }
    }
    override fun onStart() {
        super.onStart()
        println("onStart changePassword")
        println(SharedPrefManager.getInstance(this).isLoggedIn);
        if(!SharedPrefManager.getInstance(this).isLoggedIn){
            val intent = Intent(this, LoginActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            startActivity(intent)
        }
    }
}
