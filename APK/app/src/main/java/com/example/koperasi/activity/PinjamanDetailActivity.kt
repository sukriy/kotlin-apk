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
import com.example.koperasi.adapter.PinjamanDetailAdapter
import com.example.koperasi.api.RetrofitClient
import com.example.koperasi.models.DataPinjamanDetail
import com.example.koperasi.models.PinjamanDetailResponse
import kotlinx.android.synthetic.main.activity_pinjaman_detail.*
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

@Suppress("RECEIVER_NULLABILITY_MISMATCH_BASED_ON_JAVA_ANNOTATIONS")
class PinjamanDetailActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_pinjaman_detail)

        //getting recyclerview from xml
        val recyclerView = findViewById(R.id.recyclerView) as RecyclerView

        //adding a layoutmanager
        recyclerView.layoutManager = LinearLayoutManager(this, LinearLayout.VERTICAL, false)

        //crating an arraylist to store users using the data class user
        val lists = ArrayList<DataPinjamanDetail>()

        val filter = HashMap<String, String>()
        filter["api_token"] = SharedPrefManager.getInstance(this).user.api_token.toString()

            val bundle = intent.extras
            filter["id"] = bundle.getString("id_pinjaman", "")


        getWindow().setFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE, WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
        pBar.setVisibility(View.VISIBLE);

        RetrofitClient.instance.requestPinjamanDetail(filter)
            .enqueue(object : Callback<PinjamanDetailResponse> {
                override fun onFailure(call: Call<PinjamanDetailResponse>, t:Throwable){
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
                override fun onResponse(call: Call<PinjamanDetailResponse>, response: Response<PinjamanDetailResponse>){
                    println("onResponse")
                        val header = response.body()?.header?.get(0)
                    if (header != null) {
                        println(header.namalengkap)
                    }

                    if (header != null) {
                        var temp0 = header.created_at.split("\\ ".toRegex());
                        var temp = temp0[0].split("\\-".toRegex())


                        txt_createdAt.setText(temp[2]+"-"+temp[1]+"-"+temp[0])
                        txt_namalengkap.setText(header.namalengkap)
                        txt_pinjaman.setText(header.nominal.toString())
                        txt_cicilan.setText(header.cicilan.toString() + " @ " + header.tenor.toString())
                        txt_bayar.setText(header.bayar.toString())
                        txt_keterangan.setText(header.keterangan)
                        txt_noteAdmin.setText(header.note_admin)
                        txt_noteKetua.setText(header.note_ketua)
                        txt_status.setText(header.status)
                    }

                        if(response.body()?.data != null){
                            for (temp in response.body()?.data!!) {
                                var status: String = ""
                                if (temp.flag == 1){
                                    status = "Pending"
                                }else if(temp.flag == 2){
                                    status = "Approve"
                                }else if(temp.flag == 3){
                                    status = "Reject"
                                }
                                lists.add(DataPinjamanDetail(temp.tgl_pembayaran, temp.nominal, temp.pembayaran, status))
                            }
                            //creating our adapter
                            val adapter = PinjamanDetailAdapter(lists)

                            //now adding the adapter to recyclerview
                            recyclerView.adapter = adapter

                            getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                            pBar.setVisibility(View.GONE);
                        }
                }
            })
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