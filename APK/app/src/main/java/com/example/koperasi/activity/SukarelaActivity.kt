package com.example.koperasi.activity

import android.content.Intent
import android.support.v7.app.AppCompatActivity
import android.os.Bundle
import android.support.v7.widget.LinearLayoutManager
import android.support.v7.widget.RecyclerView
import android.view.View
import android.view.WindowManager
import android.widget.LinearLayout
import android.widget.Toast
import com.example.koperasi.R
import com.example.koperasi.SharedPrefManager
import com.example.koperasi.adapter.SukarelaAdapter
import com.example.koperasi.api.RetrofitClient
import com.example.koperasi.models.DataSukarela
import com.example.koperasi.models.SukarelaResponse
import kotlinx.android.synthetic.main.activity_sukarela.*
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class SukarelaActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_sukarela)
        println("SukarelaActivity start")

        btn_add.setOnClickListener {
            startActivity(Intent(applicationContext, SukarelaAddActivity::class.java))
        }

    }

    override fun onResume() {
        super.onResume()
        println("SukarelaActivity Resume")
        reloadList()
    }

    override fun onStart() {
        super.onStart()
        if (!SharedPrefManager.getInstance(this).isLoggedIn) {
            val intent = Intent(applicationContext, HomeActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            startActivity(intent)
            finish()
        }
        swipe.setOnRefreshListener {
            this.reloadList()
            swipe.setRefreshing(false);
        }
    }

    fun reloadList(){
        println("reloadList")
        //getting recyclerview from xml
        val recyclerView = findViewById(R.id.recyclerView) as RecyclerView

        //adding a layoutmanager
        recyclerView.layoutManager = LinearLayoutManager(this, LinearLayout.VERTICAL, false)

        //crating an arraylist to store users using the data class user
        val lists = ArrayList<DataSukarela>()

        val filter = HashMap<String, String>()
        filter["api_token"] = SharedPrefManager.getInstance(this).user.api_token.toString()

        getWindow().setFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE, WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
        pBar.bringToFront();
        pBar.setVisibility(View.VISIBLE);

        RetrofitClient.instance.requestSukarela(filter)
            .enqueue(object : Callback<SukarelaResponse> {
                override fun onFailure(call: Call<SukarelaResponse>, t:Throwable){
                    println("onFailure")
                    println(t)
                    if(t.message.toString() != ""){
                        Toast.makeText(applicationContext, t.message, Toast.LENGTH_LONG).show()
                    }else{
                        Toast.makeText(applicationContext, "Check kondisi Internet Anda", Toast.LENGTH_LONG).show()
                    }
                    getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                    pBar.setVisibility(View.GONE);
                }
                override fun onResponse(call: Call<SukarelaResponse>, response: Response<SukarelaResponse>){
                    println("onResponse")
                    if(response.body()?.success == true){
                        if(response.body()?.data != null){
                            for (temp in response.body()?.data!!) {
                                var status = ""
                                if(temp.flag == 0){
                                    status = "Reject"
                                }else if(temp.flag == 1){
                                    status = "Pending"
                                }else if(temp.flag == 2){
                                    status = "Approve"
                                }

                                var note = "";
                                if(temp.note != null){
                                    note = temp.note
                                }
                                lists.add(DataSukarela (temp.id, temp.nominal, temp.keterangan, temp.namalengkap, temp.tgl_pembayaran, temp.gambar, note, status))

                            }
                            //creating our adapter
                            val adapter = SukarelaAdapter(lists)

                            //now adding the adapter to recyclerview
                            recyclerView.adapter = adapter

                            getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                            pBar.setVisibility(View.GONE);
                        }
                    }else{
                        Toast.makeText(applicationContext, response.body()?.message, Toast.LENGTH_LONG).show()
                    }
                    getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                    pBar.setVisibility(View.GONE);
                }
            })
    }
}