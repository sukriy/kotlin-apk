const tabunganSukarela_add = {
    template: `
    <div class="container-fluid">
        <h4 class="page-title">Tabungan Sukarela</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Tabungan Sukarela Add</div>
                    </div>
                    <div class="card-body">
                        <form id='formInput'>
                            <div class="form-group">
                                <label for="text">Nominal</label>
                                <input type="text" class="form-control" placeholder="Nominal" name='nominal' v-model=nominal
                                    @blur="nominal = $parent.blurN(nominal)" @focus="nominal = $parent.focusN(nominal)"
                                    v-validate="'required|nominal|nominalLength:11'"
                                >
                                <span class='text-danger'>@{{ errors.first('nominal') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="jenis">Jenis Pembayaran</label>
                                <select class="form-control" name='jenis' v-model=jenis
                                    v-validate="'included:Tunai,Transfer'"
                                >
                                    <option v-if="($parent.level != 'Anggota')">Tunai</option>
                                    <option>Transfer</option>
                                </select>
                                <span class='text-danger'>@{{ errors.first('jenis') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="telepon">Tanggal Pembayaran</label>
                                <input type="text" class="datepicker form-control" placeholder="Tanggal Pembayaran"
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
                                <label for="text">Keterangan</label>
                                <textarea type="text" class="form-control" placeholder="Keterangan" name='keterangan' v-model=keterangan
                                    v-validate="'max:250'"
                                ></textarea>
                                <span class='text-danger'>@{{ errors.first('keterangan') }}</span>
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
        console.log('tabunganSukarela add component');
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
            nominal: '',
            jenis: '',
            keterangan: '',
            tgl_pembayaran: '',
            gambar: '',
            submit: 0,
        }
    },
    methods:{
        calculate: function () {
            console.log('calculate');
            if(this.nominal != '' && this.tenor != ''){
                var bunga = (100 + +this.bunga.toString().replace(/%/g, ''))/100;
                var nominal = this.nominal.toString().replace(/,/g, '');
                this.cicilan = this.$parent.blurN(Math.ceil((nominal*bunga)/this.tenor));
            }
        },
        formInput: function (e) {
            this.$validator.validateAll().then((result) => {
                if (result) {
                    console.log(this.submit);
                    if(this.submit == 1){
                        return;
                    }
                    console.log('run');
                    this.submit = 1;

                    var vm = this;
                    let formData = new FormData();
    				formData.append('nominal', this.$parent.focusN(this.nominal));
    				formData.append('jenis', this.jenis);
    				formData.append('tgl_pembayaran', this.$parent.tglForm(this.tgl_pembayaran));
    				formData.append('keterangan', this.keterangan);
                    formData.append('api_token', this.$parent.api_token);
                    if(this.$refs.gambar.files[0]){
                        formData.append('gambar', this.$refs.gambar.files[0]);
                    }

                    axios.post(
                        '/api/tabunganSukarela_add',
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
                            router.push({ name: 'tabunganSukarela'})
                        }else{
                            Swal.fire({
                                title: 'Error!',
                                text: response.data.message,
                                type: 'warning',
                            });
                        }
                    }).catch(function(error){
                        vm.submit = 0;
                        alert(error);
                    });
                }
            })
        }
    }
}
