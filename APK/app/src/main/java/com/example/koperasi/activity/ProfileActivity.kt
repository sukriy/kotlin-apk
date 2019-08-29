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
import android.widget.ArrayAdapter
import android.widget.DatePicker
import android.widget.Toast
import com.bumptech.glide.Glide
import com.bumptech.glide.request.RequestOptions
import com.example.koperasi.R
import com.example.koperasi.SharedPrefManager
import com.example.koperasi.api.RetrofitClient
import com.example.koperasi.models.LoginResponse
import kotlinx.android.synthetic.main.activity_profile.*
import okhttp3.MediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.io.File
import java.text.SimpleDateFormat
import java.util.*

class ProfileActivity : AppCompatActivity() {

    var cal = Calendar.getInstance()
    var seletedPhotoUri: Uri? = null
    var bitmap: Bitmap? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_profile)

        val list_of_items = arrayOf("Staff", "SPV", "Manager")
        sp_jabatan.adapter = ArrayAdapter(this, android.R.layout.simple_spinner_dropdown_item, list_of_items)

        // create an OnDateSetListener
        val dateSetListener = object : DatePickerDialog.OnDateSetListener {
            override fun onDateSet(view: DatePicker, year: Int, monthOfYear: Int,
                                   dayOfMonth: Int) {
                cal.set(Calendar.YEAR, year)
                cal.set(Calendar.MONTH, monthOfYear)
                cal.set(Calendar.DAY_OF_MONTH, dayOfMonth)

                val myFormat = "dd-MM-yyyy" // mention the format you need
                val sdf = SimpleDateFormat(myFormat, Locale.US)
                txt_tglJoin.setText(sdf.format(cal.getTime()).toString())
            }
        }

        txt_tglJoin.setOnFocusChangeListener { _, hasFocus ->
            if (hasFocus) {
                println("run tglJoin")

                DatePickerDialog(this@ProfileActivity,
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
        btn_update.setOnClickListener {
            btn_update.setEnabled(false)

            val namaLengkap = txt_namaLengkap.text.toString().trim();
            val username = txt_username.text.toString().trim();
            val gaji = txt_gaji.text.toString().trim();
            val email = txt_email.text.toString().trim();
            val alamat = txt_alamat.text.toString().trim();
            val phone = txt_phone.text.toString().trim();
            val tglJoin = txt_tglJoin.text.toString().trim();
            val jabatan = sp_jabatan.getSelectedItem().toString();

            textView3.error = null;

            if (namaLengkap.isEmpty()) {
                txt_namaLengkap.error = "Nama Lengkap is required";
                txt_namaLengkap.requestFocus();
                btn_update.setEnabled(true)
                return@setOnClickListener;
            }else if (username.isEmpty()) {
                txt_username.error = "Username is required";
                txt_username.requestFocus();
                btn_update.setEnabled(true)
                return@setOnClickListener;
            } else if (gaji.isEmpty()) {
                txt_gaji.error = "Gaji is required";
                txt_gaji.requestFocus();
                btn_update.setEnabled(true)
                return@setOnClickListener;
            }else if (radioGroup.checkedRadioButtonId == -1){
                textView3.error = "Jenis Kelamin is required"
                textView3.requestFocus();
                btn_update.setEnabled(true)
                return@setOnClickListener;
            } else if (email.isEmpty()) {
                txt_email.error = "Email is required";
                txt_email.requestFocus();
                btn_update.setEnabled(true)
                return@setOnClickListener;
            } else if (alamat.isEmpty()) {
                txt_alamat.error = "Alamat is required";
                txt_alamat.requestFocus();
                btn_update.setEnabled(true)
                return@setOnClickListener;
            } else if (phone.isEmpty()) {
                txt_phone.error = "Phone number is required";
                txt_phone.requestFocus();
                btn_update.setEnabled(true)
                return@setOnClickListener;
            } else if (tglJoin.isEmpty()) {
                txt_tglJoin.error = "Tgl Join number is required";
                txt_tglJoin.requestFocus();
                btn_update.setEnabled(true)
                return@setOnClickListener;
            } else {
                getWindow().setFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE, WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                pBar.setVisibility(View.VISIBLE);

                val filter = HashMap<String, String>()

                filter["id"] = SharedPrefManager.getInstance(this).user.id.toString()
                filter["username"] = username
                filter["namalengkap"] = namaLengkap
                filter["gaji"] = gaji
                filter["email"] = email
                if(rb_laki.isChecked())
                {
                    filter["jenis_kelamin"] = 1.toString()
                }else if(rb_perempuan.isChecked())
                {
                    filter["jenis_kelamin"] = 0.toString()
                }
                filter["jabatan"] = jabatan
                filter["alamat"] = alamat
                filter["level"] = SharedPrefManager.getInstance(this).user.level.toString()
                filter["telepon"] = phone

                val temp = tglJoin.split("\\-".toRegex());
                filter["tgl_join"] = temp[2]+"-"+temp[1]+"-"+temp[0];
                filter["api_token"] = SharedPrefManager.getInstance(this).user.api_token.toString()

                val attachmentEmpty = RequestBody.create(MediaType.parse("text/plain"), "")
                var body = MultipartBody.Part.createFormData("gambar", "", attachmentEmpty)

                println("uri = "+seletedPhotoUri)
                if(seletedPhotoUri != null && gambar.getDrawable() != null){
                    println("opsi1")
                    val filePath = seletedPhotoUri?.let { it1 -> getRealPathFromURIPath(it1, this@ProfileActivity) }
                    val picture = File(filePath)
                    val requestFile = RequestBody.create(MediaType.parse("image/*"), picture)
                    body = MultipartBody.Part.createFormData("gambar", picture.name, requestFile)
                }

                RetrofitClient.instance.requestProfileEdit(body, filter)
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
                            btn_update.setEnabled(true)
                        }
                        override fun onResponse(call: Call<LoginResponse>, response: Response<LoginResponse>){
                            println("onResponse")
                            println(response.body())

                            if(response.body()?.success == true){
                                Toast.makeText(applicationContext, response.body()?.message, Toast.LENGTH_LONG).show()
                                SharedPrefManager.getInstance(applicationContext).saveUser(response.body()?.data!!)

                                val intent = Intent(applicationContext, ProfileActivity::class.java)
                                intent.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP or Intent.FLAG_ACTIVITY_NEW_TASK
                                applicationContext.startActivity(intent)
                            }else{
                                Toast.makeText(applicationContext, response.body()?.message, Toast.LENGTH_LONG).show()
                            }
                            getWindow().clearFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCHABLE);
                            pBar.setVisibility(View.GONE);
                            btn_update.setEnabled(true)
                        }
                    })
            }
        }
    }
    override fun onStart() {
        super.onStart()
        if(!SharedPrefManager.getInstance(this).isLoggedIn){
            val intent = Intent(this, LoginActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            startActivity(intent)
        }

        val temp = SharedPrefManager.getInstance(this).user
        txt_namaLengkap.setText(temp.namalengkap);
        txt_username.setText(temp.username);
        txt_email.setText(temp.email);

        if(temp.jenis_kelamin == "1"){
            rb_laki.setChecked(true)
        }else if(temp.jenis_kelamin == "0"){
            rb_perempuan.setChecked(true)
        }

        if(temp.jabatan == "Staff") {
            sp_jabatan.setSelection(0)
        } else if(temp.jabatan == "SPV"){
            sp_jabatan.setSelection(1)
        } else if(temp.jabatan == "Manager"){
            sp_jabatan.setSelection(2)
        }
        txt_gaji.setText(temp.gaji.toString());
        txt_alamat.setText(temp.alamat);
        txt_phone.setText(temp.telepon);

        val cetakTgl = temp.tgl_join!!.split("\\-".toRegex());
        txt_tglJoin.setText(cetakTgl[2]+"-"+cetakTgl[1]+"-"+cetakTgl[0])

        if(temp.gambar != ""){
            val gambar0 = RetrofitClient.BASE_URL+"images/account/"+temp.gambar
            println("gambar = "+gambar0)

            Glide.with(baseContext)
                .load(gambar0)
                .apply(RequestOptions.circleCropTransform())
                .into(gambar);
        }
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
