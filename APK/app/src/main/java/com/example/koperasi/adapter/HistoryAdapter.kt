package com.example.koperasi.adapter

import android.support.v7.widget.RecyclerView
import android.view.*
import android.widget.TextView
import com.example.koperasi.R
import com.example.koperasi.models.DataHistory

class HistoryAdapter(val lists: ArrayList<DataHistory>) : RecyclerView.Adapter<HistoryAdapter.ViewHolder>() {

    //this method is returning the view for each item in the list
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): HistoryAdapter.ViewHolder {
        val v = LayoutInflater.from(parent.context).inflate(R.layout.row_history, parent, false)
        return ViewHolder(v)
    }

    //this method is binding the data on the list
    override fun onBindViewHolder(holder: HistoryAdapter.ViewHolder, position: Int) {
        holder.bindItems(lists[position])
    }

    //this method is giving the size of the list
    override fun getItemCount(): Int {
        return lists.size
    }

    //the class is hodling the list view
    class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {

        fun bindItems(data: DataHistory) {
            val txt_tglPembayaran = itemView.findViewById(R.id.txt_tglPembayaran) as TextView?
            val txt_jenis = itemView.findViewById(R.id.txt_jenis) as TextView?
            val txt_nominal = itemView.findViewById(R.id.txt_nominal) as TextView?
            val txt_status = itemView.findViewById(R.id.txt_status) as TextView?

            var temp = data.tgl_pembayaran.split("\\-".toRegex())
            txt_tglPembayaran?.text = temp[2]+"-"+temp[1]+"-"+temp[0]
            txt_jenis?.text = data.pembayaran
            txt_nominal?.text = data.nominal.toString()
            txt_status?.text = data.status

        }
    }
}
