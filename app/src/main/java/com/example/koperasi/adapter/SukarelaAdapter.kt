package com.example.koperasi.adapter

import android.app.AlertDialog
import android.content.Intent
import android.support.v7.widget.RecyclerView
import android.view.*
import android.widget.TextView
import com.example.koperasi.models.DataSukarela
import android.widget.PopupMenu
import android.widget.Toast
import com.example.koperasi.R
import com.example.koperasi.SharedPrefManager
import com.example.koperasi.activity.SukarelaActivity
import com.example.koperasi.activity.SukarelaEditActivity
import com.example.koperasi.api.RetrofitClient
import com.example.koperasi.models.DefaultResponse
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response


class SukarelaAdapter(val lists: ArrayList<DataSukarela>) : RecyclerView.Adapter<SukarelaAdapter.ViewHolder>() {

    //this method is returning the view for each item in the list
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): SukarelaAdapter.ViewHolder {
        val v = LayoutInflater.from(parent.context).inflate(R.layout.row_sukarela, parent, false)
        return ViewHolder(v)
    }

    //this method is binding the data on the list
    override fun onBindViewHolder(holder: SukarelaAdapter.ViewHolder, position: Int) {
        holder.bindItems(lists[position])
    }

    //this method is giving the size of the list
    override fun getItemCount(): Int {
        return lists.size
    }

    //the class is hodling the list view
    class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {

        fun bindItems(data: DataSukarela) {
            val txt_nominal = itemView.findViewById(R.id.txt_tglPmbyrn) as TextView
            val txt_namaLengkap = itemView.findViewById(R.id.txt_namaLengkap) as TextView
            val txt_note = itemView.findViewById(R.id.txt_note) as TextView
            val txt_status = itemView.findViewById(R.id.txt_status) as TextView
            val buttonViewOption = itemView.findViewById(R.id.textViewOptions) as TextView

            txt_nominal.text = data.nominal.toString()
            txt_namaLengkap.text = data.namalengkap.toString()
            txt_note.text = data.note.toString()
            txt_status.text = data.status.toString()

            if(data.status == "Pending"){
                buttonViewOption.setOnClickListener(View.OnClickListener {
                    val popup = PopupMenu(buttonViewOption.context, buttonViewOption)
                    //inflating menu from xml resource
                    popup.inflate(R.menu.menu_edit_del)
                    //adding click listener
                    popup.setOnMenuItemClickListener(object : PopupMenu.OnMenuItemClickListener {
                        override fun onMenuItemClick(item: MenuItem): Boolean {
                            when (item.getItemId()) {
                                R.id.menu_edit -> {
                                    println("edit")
                                    val intent = Intent(buttonViewOption.context, SukarelaEditActivity::class.java)
                                    intent.putExtra("id_sukarela", data.id.toString())
                                    buttonViewOption.context.startActivity(intent)
                                    return true
                                }
                                R.id.menu_hapus -> {
                                    println("hapus")
                                    // Initialize a new instance of
                                    val builder = AlertDialog.Builder(buttonViewOption.context)

                                    // Set the alert dialog title
                                    builder.setTitle("Delete Tabungan Sukarela")

                                    // Display a message on alert dialog
                                    builder.setMessage("Are you want sure?")

                                    // Set a positive button and its click listener on alert dialog
                                    builder.setPositiveButton("YES"){_, _ ->
                                        // Do something when user press the positive button

                                        val filter = HashMap<String, String>()
                                        filter["id"] = data.id.toString()
                                        filter["api_token"] = SharedPrefManager.getInstance(buttonViewOption.context).user.api_token.toString()


                                        RetrofitClient.instance.requestSukarelaDelete(filter)
                                            .enqueue(object : Callback<DefaultResponse> {
                                                override fun onFailure(call: Call<DefaultResponse>, t:Throwable){
                                                    println("onFailure")
                                                    println(t)
                                                    if(t.message.toString() != ""){
                                                        Toast.makeText(buttonViewOption.context, t.message, Toast.LENGTH_LONG).show()
                                                    }else{
                                                        Toast.makeText(buttonViewOption.context, "Check kondisi Internet Anda", Toast.LENGTH_LONG).show()
                                                    }
                                                }
                                                override fun onResponse(call: Call<DefaultResponse>, response: Response<DefaultResponse>){
                                                    println("onResponse")
                                                    if(response.body()?.success == true){
                                                        Toast.makeText(buttonViewOption.context, response.body()?.message, Toast.LENGTH_LONG).show()

                                                        val intent = Intent(buttonViewOption.context, SukarelaActivity::class.java)
                                                        intent.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP or Intent.FLAG_ACTIVITY_NEW_TASK
                                                        buttonViewOption.context.startActivity(intent)

                                                    }else{
                                                        Toast.makeText(buttonViewOption.context, response.body()?.message, Toast.LENGTH_LONG).show()
                                                    }
                                                }
                                            })

                                    }

                                    // Display a negative button on alert dialog
                                    builder.setNegativeButton("No"){_, _ ->
                                    }


                                    // Finally, make the alert dialog using builder
                                    val dialog: AlertDialog = builder.create()

                                    // Display the alert dialog on app interface
                                    dialog.show()

                                    return true
                                }
                            }
                            return false
                        }
                    })
                    //displaying the popup
                    popup.show()
                })
            }else{
                buttonViewOption.visibility = View.GONE
            }
        }
    }
}
