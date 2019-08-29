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
import com.example.koperasi.adapter.HistoryAdapter
import com.example.koperasi.api.RetrofitClient
import com.example.koperasi.models.DataHistory
import com.example.koperasi.models.HistoryResponse
import kotlinx.android.synthetic.main.activity_history.*
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class HistoryActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_history)

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
        val lists = ArrayList<DataHistory>()

        val filter = HashMap<String, String>()
        filter["api_token"] = SharedPrefManager.getInstance(this).user.api_token.toString()

        getWindow().setFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE, WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
        pBar.bringToFront();
        pBar.setVisibility(View.VISIBLE);

        RetrofitClient.instance.requestHistory(filter)
            .enqueue(object : Callback<HistoryResponse> {
                override fun onFailure(call: Call<HistoryResponse>, t:Throwable){
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
                override fun onResponse(call: Call<HistoryResponse>, response: Response<HistoryResponse>){
                    println("onResponse")
                    if(response.body()?.success == true){
                        if(response.body()?.data != null){
                            for (temp in response.body()?.data!!) {
                                var status: String
                                if(temp.flag == 1){
                                    status = "Pending"
                                }else if(temp.flag == 2){
                                    status = "Approve"
                                }else if(temp.flag == 0){
                                    status = "Reject"
                                }else{
                                    status = "undefined"
                                }

                                lists.add(DataHistory (temp.id, temp.id_pembayaran, temp.tgl_pembayaran, temp.nominal, temp.jenis, temp.pembayaran, temp.note, status))
                            }
                            //creating our adapter
                            val adapter = HistoryAdapter(lists)

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
