package com.example.koperasi.activity

import android.app.AlertDialog
import android.content.Intent
import android.support.v7.app.AppCompatActivity
import android.os.Bundle
import android.os.Handler
import android.view.View
import android.view.WindowManager
import android.widget.Toast
import com.bumptech.glide.Glide
import com.bumptech.glide.request.RequestOptions
import com.example.koperasi.MyFirebaseInstanceIdService
import com.example.koperasi.R
import com.example.koperasi.SharedPrefManager
import com.example.koperasi.api.RetrofitClient
import com.example.koperasi.models.DefaultResponse
import com.example.koperasi.models.LoginResponse
import kotlinx.android.synthetic.main.activity_home.*
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import kotlin.collections.HashMap

class HomeActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_home)

        if(!SharedPrefManager.getInstance(this).isLoggedIn){
            val intent = Intent(this, LoginActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            startActivity(intent)
            finish()
        }
        if(SharedPrefManager.getInstance(this).user.flag < 2){
            val intent = Intent(this, PinjamanWajibActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            startActivity(intent)
            finish()
        }
    }

    override fun onStart() {
        super.onStart()

        swipe.setOnRefreshListener {
            this.fun_profile()
            swipe.setRefreshing(false);
            val intent = Intent(this, MyFirebaseInstanceIdService::class.java)
            startService(intent)
        }

        val user = SharedPrefManager.getInstance(this).user
        txt_namaLengkap.text = user.namalengkap
        txt_saldo.text = user.saldo.toString()
        println("api_token = "+user.api_token)
        if(user.gambar != ""){
            val url = RetrofitClient.BASE_URL+"images/account/"
            Glide.with(baseContext)
                .load(url + user.gambar)
                .apply(RequestOptions.circleCropTransform())
                .into(gambar);
        }

        txt_changePassword.setOnClickListener {
            startActivity(Intent(applicationContext, changePasswordActivity::class.java))
        }

        cardPinjaman.setOnClickListener {
            startActivity(Intent(applicationContext, PinjamanActivity::class.java))
        }

        cardSukarela.setOnClickListener {
            startActivity(Intent(applicationContext, SukarelaActivity::class.java))
        }

        cardHistory.setOnClickListener {
            startActivity(Intent(applicationContext, HistoryActivity::class.java))
        }

        cardProfile.setOnClickListener {
            startActivity(Intent(applicationContext, ProfileActivity::class.java))
        }

        btn_logout.setOnClickListener {
            this.fun_logout()
        }

        this.fun_profile()
    }

    fun fun_logout(){
        println("btn_logout")
        // Initialize a new instance of
        val builder = AlertDialog.Builder(this@HomeActivity)

        // Set the alert dialog title
        builder.setTitle("Logout")

        // Display a message on alert dialog
        builder.setMessage("Are you sure?")

        // Set a positive button and its click listener on alert dialog
        builder.setPositiveButton("YES"){_, _ ->
            getWindow().setFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE, WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
            pBar.setVisibility(View.VISIBLE);

            val filter = HashMap<String, String>()
            filter["api_token"] = SharedPrefManager.getInstance(this).user.api_token.toString()

            RetrofitClient.instance.requestLogout(filter)
                .enqueue(object : Callback<DefaultResponse> {
                    override fun onFailure(call: Call<DefaultResponse>, t:Throwable) {
                        SharedPrefManager.getInstance(applicationContext).clear()
                        startActivity(Intent(applicationContext, LoginActivity::class.java))
                        finish()
                        getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                        pBar.setVisibility(View.GONE);
                    }
                    override fun onResponse(call: Call<DefaultResponse>, response: Response<DefaultResponse>){
                        SharedPrefManager.getInstance(applicationContext).clear()
                        startActivity(Intent(applicationContext, LoginActivity::class.java))
                        finish()
                        getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                        pBar.setVisibility(View.GONE);
                    }
                })
        }

        // Display a negative button on alert dialog
        builder.setNegativeButton("No"){_,_ ->
            println("no")
        }


        // Finally, make the alert dialog using builder
        val dialog: AlertDialog = builder.create()

        // Display the alert dialog on app interface
        dialog.show()
    }

    fun fun_profile(){
        println("fun_profile")
        val filter = HashMap<String, String>()
        filter["id"] = SharedPrefManager.getInstance(this).user.id.toString()
        filter["api_token"] = SharedPrefManager.getInstance(this).user.api_token.toString()

        RetrofitClient.instance.requestProfile(filter)
            .enqueue(object : Callback<LoginResponse> {
                override fun onFailure(call: Call<LoginResponse>, t:Throwable){
                    if(t.message.toString() != ""){
                        Toast.makeText(applicationContext, t.message, Toast.LENGTH_LONG).show()
                    }else{
                        Toast.makeText(applicationContext, "Check kondisi Internet Anda", Toast.LENGTH_LONG).show()
                    }
                }
                override fun onResponse(call: Call<LoginResponse>, response: Response<LoginResponse>){
                    if(response.body()?.success == true){
                        if(response.body()?.data != null) {
                            SharedPrefManager.getInstance(applicationContext).saveUser(response.body()?.data!!)
                        }
                    }else{
                        SharedPrefManager.getInstance(applicationContext).clear()
                        startActivity(Intent(applicationContext, LoginActivity::class.java))
                        finish()
                    }
                }
            })
    }

    fun logout_process(){
        SharedPrefManager.getInstance(applicationContext).clear()
        startActivity(Intent(applicationContext, LoginActivity::class.java))
        finish()
        getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
        pBar.setVisibility(View.GONE);
    }

    override fun onResume() {
        super.onResume()
        val intent = Intent(this, MyFirebaseInstanceIdService::class.java)
        startService(intent)
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