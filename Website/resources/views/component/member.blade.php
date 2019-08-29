const member = {
    template: `
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Simpanan Wajib Member</div>
                    </div>
                    <div class="card-body">
                        <form id='formInput'>
                            <div class="form-group">
                                <label for="telepon">Nominal</label>
                                <input type="text" class="form-control" v-model='nominal' disabled>
                            </div>
                            <div class="form-group">
                                <label for="telepon">Tanggl Pembayaran</label>
                                <input type="text" class="datepicker form-control" placeholder="Tanggl Pembayaran"
                                    name='tgl_pembayaran' v-model=tgl_pembayaran v-validate="'required|date_format:dd-MM-yyyy'"
                                    autocomplete="off"
                                >
                                <span class='text-danger'>@{{ errors.first('tgl_pembayaran') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="telepon">Gambar</label>
                                <input type="file" class="form-control" name='gambar' accept="image/*" ref='gambar'
                                    v-validate="'image'" data-vv-as="image"
                                >
                                <span class='text-danger'>@{{ errors.first('gambar') }}</span>
                            </div>
                            <div class="form-group">
                                <button type="button" @click='formInput()' class="form-control btn btn-primary">Input</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `,
    mounted() {
        console.log('account add component');
        var vm = this;
        $( ".datepicker" ).datepicker({
            dateFormat: "dd-mm-yy",
            onSelect: function(dateText, inst) {
                vm.tgl_pembayaran = dateText;
            }
        });
    },
    data() {
        return {
            tgl_pembayaran: '',
            nominal: this.$parent.blurN(200000),
            gambar: '',
            submit: 0,
        }
    },
    methods:{
        formInput: function (e) {
            this.$validator.validateAll().then((result) => {
                if (result) {
                    if(this.submit == 1){
                        return;
                    }
                    console.log('run');
                    this.submit = 1;
                    var vm = this;

                    let formData = new FormData();
                    formData.append('tgl_pembayaran', this.$parent.tglForm(this.tgl_pembayaran));
                    formData.append('jenis', 'Transfer');
                    formData.append('api_token', this.$parent.api_token);
                    if(this.$refs.gambar.files[0]){
                        formData.append('gambar', this.$refs.gambar.files[0]);
                    }

                    axios.post(
                        '/api/member_bayar',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                    ).then(function(response){
                        vm.submit = 0;
                        console.log(response.data);
                        if(response.data.success){
                            Swal.fire({
                              title: 'Success!',
                              text: 'Pembayaran Berhasil, Harap tunggu konfirmasi',
                              type: 'success',
                            })
                        }else{
                            Swal.fire({
                              title: 'Error!',
                              text: response.data.message,
                              type: 'error',
                            })
                        }
                    }).catch(function(error){
                        vm.submit = 0;
                        alert(error);
                    });
                }
            });
        },
    }
}
