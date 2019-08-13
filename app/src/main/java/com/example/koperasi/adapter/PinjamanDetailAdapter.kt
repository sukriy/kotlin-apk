package com.example.koperasi.adapter

import android.support.v7.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import com.example.koperasi.R
import com.example.koperasi.models.DataPinjamanDetail

class PinjamanDetailAdapter(val lists: ArrayList<DataPinjamanDetail>) : RecyclerView.Adapter<PinjamanDetailAdapter.ViewHolder>() {

    //this method is returning the view for each item in the list
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): PinjamanDetailAdapter.ViewHolder {
        val v = LayoutInflater.from(parent.context).inflate(R.layout.row_detail_pinjaman, parent, false)
        return ViewHolder(v)
    }

    //this method is binding the data on the list
    override fun onBindViewHolder(holder: PinjamanDetailAdapter.ViewHolder, position: Int) {
        holder.bindItems(lists[position])
    }

    //this method is giving the size of the list
    override fun getItemCount(): Int {
        return lists.size
    }

    //the class is hodling the list view
    class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {

        fun bindItems(data: DataPinjamanDetail) {
            val tgl_pembayaran = itemView.findViewById(R.id.txt_tglPembayaran) as TextView
            val txt_nominal = itemView.findViewById(R.id.txt_tglPmbyrn) as TextView
            val txt_pembayaran = itemView.findViewById(R.id.txt_pembayaran) as TextView
            val txt_status = itemView.findViewById(R.id.txt_status) as TextView


            var temp = data.tgl_pembayaran.split("\\-".toRegex())
            tgl_pembayaran.text = temp[2]+"-"+temp[1]+"-"+temp[0]
            txt_nominal.text = data.nominal.toString()
            txt_pembayaran.text = data.pembayaran.toString()
            txt_status.text = data.status.toString()
        }
    }
}