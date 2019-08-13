package com.example.koperasi.activity

import android.R
import android.content.Intent
import android.support.v7.app.AppCompatActivity
import android.os.Bundle
import android.view.View
import android.view.WindowManager
import android.widget.ArrayAdapter
import android.widget.Toast
import com.example.koperasi.SharedPrefManager
import com.example.koperasi.api.RetrofitClient
import com.example.koperasi.models.DefaultResponse
import kotlinx.android.synthetic.main.activity_pinjaman_add.*
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.lang.Float.parseFloat
import java.lang.Integer.parseInt

class PinjamanAddActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(com.example.koperasi.R.layout.activity_pinjaman_add)

        val list_of_items = arrayOf("1","2","3","4","5","6","7","8","9","10","11","12")
        sp_tenor.adapter = ArrayAdapter(this, R.layout.simple_spinner_dropdown_item, list_of_items)

        btn_add.setOnClickListener {
            btn_add.setEnabled(false)
            val nominal = txt_tglPmbyrn.text.toString().trim();
            val tenor = sp_tenor.selectedItem.toString();
            val keterangan = txt_keterangan.text.toString().trim();

            if (nominal.isEmpty()) {
                txt_tglPmbyrn.error = "Username is required";
                txt_tglPmbyrn.requestFocus();
                btn_add.setEnabled(true)
                return@setOnClickListener;
            } else {
                val cicilan = Math.ceil((0.02 * parseFloat(nominal)) + (parseFloat(nominal) / parseFloat(tenor)))
                txt_cicilan.setText(cicilan.toString())

                getWindow().setFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE, WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                pBar.setVisibility(View.VISIBLE);

                val filter = HashMap<String, String>()
                filter["nominal"] = nominal
                filter["tenor"] = tenor
                filter["bunga"] = "2%"
                filter["cicilan"] = cicilan.toString()
                filter["keterangan"] = keterangan
                filter["api_token"] = SharedPrefManager.getInstance(this).user.api_token.toString()

                RetrofitClient.instance.requestPinjamanAdd(filter)
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
                            btn_add.setEnabled(true)
                        }
                        override fun onResponse(call: Call<DefaultResponse>, response: Response<DefaultResponse>){
                            println("onResponse")
                            println(response.body())

                            if(response.body()?.success == true){
                                finish()
                            }else{
                                Toast.makeText(applicationContext, response.body()?.message, Toast.LENGTH_LONG).show()
                            }
                            getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                            pBar.setVisibility(View.GONE);
                            btn_add.setEnabled(true)
                        }
                    })
            }
        }
    }
    override fun onStart() {
        super.onStart()
        if (!SharedPrefManager.getInstance(this).isLoggedIn) {
            val intent = Intent(applicationContext, HomeActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            startActivity(intent)
        }
    }
}