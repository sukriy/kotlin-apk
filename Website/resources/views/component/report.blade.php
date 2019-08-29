const report = {
    template: `
    <div class="container-fluid">
        <h4 class="page-title">Report</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <form id='formInput'>
                            <div class="form-group form-inline">
                                <label for="inlineinput" class="col-md-3 col-form-label">Jenis</label>
                                <div class="col-md-4 p-0">
                                    <select class="form-control" name='jenis' v-model=jenis
                                        v-validate="'required|included:Sukarela,Pinjaman,Transaksi'"
                                        @change='cetakValue()'
                                    >
                                        <option>Sukarela</option>
                                        <option>Pinjaman</option>
                                        <option>Transaksi</option>
                                    </select>
                                </div>
                                <div class="p-0">
                                    <span class='text-danger'>@{{ errors.first('jenis') }}</span>
                                </div>
                            </div>
                            <div class="form-group form-inline">
                                <label for="inlineinput" class="col-md-3 col-form-label">Tanggal</label>
                                <div class="col-md-4 p-0">
                                    <input type="text" class="datepicker form-control" placeholder="Dari"
                                        name='tgl_dari' v-validate="'required|date_format:dd-MM-yyyy'"
                                    >
                                </div>
                                <div class="col-md-4 p-0">
                                    <input type="text" class="datepicker form-control" placeholder="Sampai"
                                        name='tgl_sampai' v-validate="'required|date_format:dd-MM-yyyy'"
                                    >
                                </div>
                                <div class="p-0">
                                    <span class='text-danger'>@{{ errors.first('tgl_sampai') }}</span>
                                    <br>
                                    <span class='text-danger'>@{{ errors.first('tgl_dari') }}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" @click='formInput()' class="form-control btn btn-primary">Search</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class='table-responsive'>
                            <table class="table mt-3" v-if="(cetak == 'Sukarela')">
                                <thead>
                                    <tr align='center'>
                                        <th scope="col">ID</th>
                                        <th scope="col">Tgl Input</th>
                                        <th scope="col">Nama Lengkap</th>
                                        <th scope="col">Nominal</th>
                                        <th scope="col">Keterangan</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody align='left'>
                                    <tr v-for="list in lists">
                                        <td v-text='list.id'></td>
                                        <td v-text='list.created_at'></td>
                                        <td v-text='list.namalengkap'></td>
                                        <td align='right' v-text='list.nominal'></td>
                                        <td v-text='list.keterangan'></td>
                                        <td v-if='list.flag == 0'>Reject</td>
                                        <td v-else-if='list.flag == 1'>Pending</td>
                                        <td v-else-if='list.flag == 2'>Approve</td>
                                        <td v-else>undefined</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table mt-3" v-else-if="(cetak == 'Pinjaman')">
                                <thead>
                                    <tr align='center'>
                                        <th scope="col">ID</th>
                                        <th scope="col">Tgl Input</th>
                                        <th scope="col">Nama Lengkap</th>
                                        <th scope="col">Nominal</th>
                                        <th scope="col">Cicilan (2%)</th>
                                        <th scope="col">Bayar</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody align='left'>
                                    <tr v-for="list in lists">
                                        <td v-text='list.id'></td>
                                        <td v-text='list.created_at'></td>
                                        <td v-text='list.namalengkap'></td>
                                        <td align='right' v-text='list.nominal'></td>
                                        <td align='right' v-text='list.cicilan'></td>
                                        <td align='right' v-text='list.bayar'></td>
                                        <td v-text='list.status'></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table mt-3" v-else-if="(cetak == 'Transaksi')">
                                <thead>
                                    <tr align='center'>
                                        <th scope="col">ID</th>
                                        <th scope="col">Tgl Pembayaran</th>
                                        <th scope="col">Nama Lengkap</th>
                                        <th scope="col">Pembayaran</th>
                                        <th scope="col">Jenis</th>
                                        <th scope="col">Nominal</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody align='left'>
                                    <tr v-for="list in lists">
                                        <td v-text='list.id'></td>
                                        <td v-text='list.tgl_pembayaran'></td>
                                        <td v-text='list.namalengkap'></td>
                                        <td v-text='list.pembayaran'></td>
                                        <td v-text='list.jenis'></td>
                                        <td align='right' v-text='list.nominal'></td>
                                        <td v-if='list.flag == 0'>Reject</td>
                                        <td v-else-if='list.flag == 1'>Pending</td>
                                        <td v-else-if='list.flag == 2'>Approve</td>
                                        <td v-else>undefined</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `,
    mounted() {
        console.log('report component')
        $( ".datepicker" ).datepicker({
            dateFormat: "dd-mm-yy"
        });
    },
    data() {
        return {
            lists: [],
            tgl_dari: '',
            tgl_sampai: '',
            jenis: '',
            submit: 0,
            cetak: '',
        }
    },
    methods:{
        cetakValue: function(e){
            this.tgl_dari = $('input[name=tgl_dari]').val();
            this.tgl_sampai = $('input[name=tgl_sampai]').val();
        },
        formInput: function (e) {
            this.tgl_dari = $('input[name=tgl_dari]').val();
            this.tgl_sampai = $('input[name=tgl_sampai]').val();
            this.$validator.validateAll().then((result) => {
                if (result) {
                    if(this.submit == 1){
                        return;
                    }
                    this.submit = 1;
                    console.log('run');
                    var vm = this;

                    let formData = new FormData();
                    formData.append('jenis', this.jenis);
                    formData.append('tgl_dari', this.$parent.tglForm(this.tgl_dari));
                    formData.append('tgl_sampai', this.$parent.tglForm(this.tgl_sampai));
                    formData.append('api_token', this.$parent.api_token);
                    axios.post(
                        '/api/report',
                        formData,
                    ).then(function(response){
                        vm.submit = 0;
                        if(response.data.success){
                            var temp = response.data.message;
                            vm.lists = [];
                            vm.cetak = vm.jenis;
                            if(vm.jenis == 'Sukarela'){
                                for(var i=0; i<temp.length; i++){
                                    vm.lists.push({
                                        id: temp[i].id,
                                        created_at: vm.$parent.tglCetak(temp[i].created_at),
                                        namalengkap: vm.$parent.cetak(temp[i].namalengkap),
                                        nominal: vm.$parent.blurN(temp[i].nominal),
                                        keterangan: temp[i].keterangan,
                                        flag: temp[i].flag,
                                    });
                                }
                            }else if(vm.jenis == 'Pinjaman'){
                                for(var i=0; i<temp.length; i++){
                                    vm.lists.push({
                                        id: temp[i].id,
                                        created_at: vm.$parent.tglCetak(temp[i].created_at),
                                        namalengkap: vm.$parent.cetak(temp[i].namalengkap),
                                        nominal: vm.$parent.blurN(temp[i].nominal),
                                        cicilan: temp[i].tenor+' @ '+vm.$parent.blurN(temp[i].cicilan),
                                        bayar: vm.$parent.blurN(temp[i].bayar),
                                        keterangan: temp[i].keterangan,
                                        status: temp[i].status,
                                    });
                                }
                            }else if(vm.jenis == 'Transaksi'){
                               console.log(temp);
                               for (var i in temp) {
                                  vm.lists.push({
                                      id: temp[i].id,
                                      tgl_pembayaran: vm.$parent.tglCetak(temp[i].tgl_pembayaran),
                                      namalengkap: vm.$parent.cetak(temp[i].namalengkap),
                                      jenis: vm.$parent.cetak(temp[i].jenis),
                                      pembayaran: vm.$parent.cetak(temp[i].pembayaran),
                                      nominal: vm.$parent.blurN(temp[i].nominal),
                                      flag: temp[i].flag,
                                  });
                               }
                            }
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
        }
    }
}
