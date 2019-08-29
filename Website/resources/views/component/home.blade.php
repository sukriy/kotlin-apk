const home = {
    template: `
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                      Welcome to Koperasi
                    </div>
                    <div class="card-body" v-if='(this.$parent.level == "Anggota")'>
                      Untuk tabungan dan pembayaran cicilan dapat disetorkan ke rekening Bank NISP atas nama Koperasi Karyawan Bahtera Abadi nomor 1234567890
                    </div>
                </div>
            </div>
        </div>
    </div>
    `,
    mounted() {
        console.log('Home component');
    },
}
