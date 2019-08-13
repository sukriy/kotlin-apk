package com.example.koperasi.activity

import android.app.Activity
import android.app.DatePickerDialog
import android.content.Intent
import android.content.pm.PackageManager
import android.graphics.Bitmap
import android.net.Uri
import android.support.v7.app.AppCompatActivity
import android.os.Bundle
import android.provider.MediaStore
import android.support.v4.app.ActivityCompat
import android.support.v4.content.ContextCompat
import android.view.View
import android.view.WindowManager
import android.widget.DatePicker
import android.widget.Toast
import com.bumptech.glide.Glide
import com.bumptech.glide.request.RequestOptions
import com.example.koperasi.R
import com.example.koperasi.SharedPrefManager
import com.example.koperasi.api.RetrofitClient
import com.example.koperasi.models.DefaultResponse
import com.example.koperasi.models.SukarelaDetailResponse
import kotlinx.android.synthetic.main.activity_sukarela_edit.*
import okhttp3.MediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.io.File
import java.text.SimpleDateFormat
import java.util.*
import kotlin.collections.HashMap

class SukarelaEditActivity : AppCompatActivity() {
    var id_sukarela: String = ""
    var cal = Calendar.getInstance()
    var seletedPhotoUri: Uri? = null
    var bitmap: Bitmap? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_sukarela_edit)

        // create an OnDateSetListener
        val dateSetListener = object : DatePickerDialog.OnDateSetListener {
            override fun onDateSet(view: DatePicker, year: Int, monthOfYear: Int,
                                   dayOfMonth: Int) {
                cal.set(Calendar.YEAR, year)
                cal.set(Calendar.MONTH, monthOfYear)
                cal.set(Calendar.DAY_OF_MONTH, dayOfMonth)

                val myFormat = "dd-MM-yyyy" // mention the format you need
                val sdf = SimpleDateFormat(myFormat, Locale.US)
                txt_tglPmbyrn.setText(sdf.format(cal.getTime()).toString())
            }
        }

        txt_tglPmbyrn.setOnFocusChangeListener { _, hasFocus ->
            if (hasFocus) {
                println("run tglJoin")

                DatePickerDialog(this@SukarelaEditActivity,
                    dateSetListener,
                    // set DatePickerDialog to point to today's date when it loads up
                    cal.get(Calendar.YEAR),
                    cal.get(Calendar.MONTH),
                    cal.get(Calendar.DAY_OF_MONTH)).show()

            }
        }
        gambar.setOnClickListener{
            println("AddUserImage")
            if (ContextCompat.checkSelfPermission(this, android.Manifest.permission.READ_EXTERNAL_STORAGE) == PackageManager.PERMISSION_GRANTED) {
                val intent = Intent(Intent.ACTION_PICK)
                intent.type = "image/*"
                startActivityForResult(intent, 0)
            }else{
                RequestPermission()
            }
        }


        btn_add.setOnClickListener {
            btn_add.setEnabled(false)
            val nominal = txt_nominal.text.toString().trim();
            val tglPmbyrn = txt_tglPmbyrn.text.toString().trim();
            val keterangan = txt_keterangan.text.toString().trim();

            if (nominal.isEmpty()) {
                txt_tglPmbyrn.error = "Nominal is required";
                txt_tglPmbyrn.requestFocus();
                btn_add.setEnabled(true)
                return@setOnClickListener;
            }else if (tglPmbyrn.isEmpty()) {
                txt_tglPmbyrn.error = "Tanggal Pembayaran is required";
                txt_tglPmbyrn.requestFocus();
                btn_add.setEnabled(true)
                return@setOnClickListener;
            } else {
                getWindow().setFlags(
                    WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE,
                    WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE
                );
                pBar.setVisibility(View.VISIBLE);

                val filter = HashMap<String, String>()
                filter["nominal"] = nominal
                val temp = tglPmbyrn.split("\\-".toRegex());
                filter["id"] = id_sukarela
                filter["jenis"] = "Transfer";
                filter["tgl_pembayaran"] = temp[2]+"-"+temp[1]+"-"+temp[0];
                filter["keterangan"] = keterangan
                filter["api_token"] = SharedPrefManager.getInstance(this).user.api_token.toString()

                val attachmentEmpty = RequestBody.create(MediaType.parse("text/plain"), "")
                var body = MultipartBody.Part.createFormData("gambar", "", attachmentEmpty)

                println("uri = "+seletedPhotoUri)
                if(seletedPhotoUri != null && gambar.getDrawable() != null){
                    println("opsi1")
                    val filePath = seletedPhotoUri?.let { it1 -> getRealPathFromURIPath(it1, this@SukarelaEditActivity) }
                    val picture = File(filePath)
                    val requestFile = RequestBody.create(MediaType.parse("image/*"), picture)
                    body = MultipartBody.Part.createFormData("gambar", picture.name, requestFile)
                }

                RetrofitClient.instance.requestTabunganEdit(body, filter)
                    .enqueue(object : Callback<DefaultResponse> {
                        override fun onFailure(call: Call<DefaultResponse>, t: Throwable) {
                            println("onFailure")
                            println(t)
                            if (t.message.toString() != "") {
                                Toast.makeText(applicationContext, t.message, Toast.LENGTH_LONG).show()
                            } else {
                                Toast.makeText(applicationContext, "Check kondisi Internet Anda", Toast.LENGTH_LONG)
                                    .show()
                            }
                            getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                            pBar.setVisibility(View.GONE);
                            btn_add.setEnabled(true)
                        }

                        override fun onResponse(call: Call<DefaultResponse>, response: Response<DefaultResponse>) {
                            println("onResponse")
                            println(response.body())

                            if (response.body()?.success == true) {
                                finish()
                            } else {
                                Toast.makeText(applicationContext, response.body()?.message, Toast.LENGTH_LONG)
                                    .show()
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
        getWindow().setFlags(
            WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE,
            WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE
        );
        pBar.setVisibility(View.VISIBLE);

        val bundle = intent.extras
        val filter = HashMap<String, String>()

        id_sukarela = bundle.getString("id_sukarela")
        println("id_sukarela = "+id_sukarela)

        filter["api_token"] = SharedPrefManager.getInstance(this).user.api_token.toString()
        filter["id"] = id_sukarela

        RetrofitClient.instance.requestSukarelaDetail(filter)
            .enqueue(object : Callback<SukarelaDetailResponse> {
                override fun onFailure(call: Call<SukarelaDetailResponse>, t: Throwable) {
                    println("onFailure")
                    println(t)
                    if (t.message.toString() != "") {
                        Toast.makeText(applicationContext, t.message, Toast.LENGTH_LONG).show()
                    } else {
                        Toast.makeText(applicationContext, "Check kondisi Internet Anda", Toast.LENGTH_LONG)
                            .show()
                    }
                    getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                    pBar.setVisibility(View.GONE);
                    btn_add.setEnabled(true)
                }

                override fun onResponse(call: Call<SukarelaDetailResponse>, response: Response<SukarelaDetailResponse>) {
                    println("onResponse")
                    println(response.body()?.data)

                    if (response.body()?.success == true) {

                        val temp = response.body()?.data
                        val temp_tgl = temp!!.tgl_pembayaran.split("\\-".toRegex());
                        txt_nominal.setText(temp!!.nominal.toString());
                        txt_keterangan.setText(temp.keterangan);
                        txt_tglPmbyrn.setText(temp_tgl[2]+"-"+temp_tgl[1]+"-"+temp_tgl[0]);
                        txt_keterangan.setText(temp.keterangan);

                        if(temp.gambar != ""){
                            val gambar0 = RetrofitClient.BASE_URL+"images/TabunganSukarela/"+temp.gambar
                            println("gambar = "+gambar0)

                            Glide.with(baseContext)
                                .load(gambar0)
                                .into(gambar);
                        }

                    } else {
                        Toast.makeText(applicationContext, response.body()?.message, Toast.LENGTH_LONG)
                            .show()
                        finish()
                    }
                    getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                    pBar.setVisibility(View.GONE);
                    btn_add.setEnabled(true)
                }
            })
    }
    private fun getRealPathFromURIPath(contentURI: Uri, activity: Activity): String? {
        val cursor = activity.contentResolver.query(contentURI, null, null, null, null)
        if (cursor == null) {
            return contentURI.path
        } else {
            cursor.moveToFirst()
            val idx = cursor.getColumnIndex(MediaStore.Images.ImageColumns.DATA)
            return cursor.getString(idx)
        }
    }
    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if(requestCode == 0 && resultCode == Activity.RESULT_OK && data != null){
            try {
                seletedPhotoUri = data.data
                println(seletedPhotoUri)
                bitmap = MediaStore.Images.Media.getBitmap(contentResolver, seletedPhotoUri)
                Glide.with(baseContext)
                    .load(bitmap)
                    .apply(RequestOptions.circleCropTransform())
                    .into(gambar);
            } catch (e: Exception) {
                Toast.makeText(applicationContext, e.message.toString(), Toast.LENGTH_LONG).show()
            }
        }
    }
    fun RequestPermission(){
        if (ContextCompat.checkSelfPermission(this, android.Manifest.permission.READ_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED){
            val permissions = arrayOf(android.Manifest.permission.READ_EXTERNAL_STORAGE)
            ActivityCompat.requestPermissions(this, permissions,0)
        }
    }
}
