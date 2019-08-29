const pinjaman_detail = {
    template: `
    <div class="container-fluid">
        <h4 class="page-title">History</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                       <div class="form-group">
                           <div class="form-inline">
                              <p class="control-label col-sm-3 align-left">Tgl Input</p>
                              <p class="control-label" v-text=created_at></p>
                           </div>
                           <div class="form-inline">
                              <p class="control-label col-sm-3 align-left">Nama Lengkap</p>
                              <p class="control-label" v-text=namalengkap></p>
                           </div>
                           <div class="form-inline">
                              <p class="control-label col-sm-3 align-left">Pinjaman</p>
                              <p class="control-label" v-text=nominal></p>
                           </div>
                           <div class="form-inline">
                              <p class="control-label col-sm-3 align-left">Cicilan (2%)</p>
                              <p class="control-label" v-text=cicilan></p>
                           </div>
                           <div class="form-inline">
                              <p class="control-label col-sm-3 align-left">Total</p>
                              <p class="control-label" v-text=total></p>
                           </div>
                           <div class="form-inline">
                              <p class="control-label col-sm-3 align-left">Bayar</p>
                              <p class="control-label" v-text=bayar></p>
                           </div>
                           <div class="form-inline">
                              <p class="control-label col-sm-3 align-left">Keterangan</p>
                              <p class="control-label" v-text=keterangan></p>
                           </div>
                           <div class="form-inline">
                              <p class="control-label col-sm-3 align-left">Catatan Admin</p>
                              <p class="control-label" v-text=note_admin></p>
                           </div>
                           <div class="form-inline">
                              <p class="control-label col-sm-3 align-left">Catatan Ketua</p>
                              <p class="control-label" v-text=note_ketua></p>
                           </div>
                           <div class="form-inline">
                              <p class="control-label col-sm-3 align-left">Status</p>
                              <p class="control-label" v-text=status></p>
                           </div>
                        </div>
                        <div class='table-responsive'>
                            <table class="table mt-3" id='example'>
                                <thead>
                                    <tr align='center'>
                                        <th scope="col">Tgl</th>
                                        <th scope="col">Nominal</th>
                                        <th scope="col">Pembayaran</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr align='left' v-for="list in lists">
                                        <td v-text='list.tgl_pembayaran'></td>
                                        <td align='right' v-text='list.nominal'></td>
                                        <td v-text='list.jenis'></td>
                                        <td v-if='list.flag == 1'>Pending</td>
                                        <td v-else-if='list.flag == 2'>Approve</td>
                                        <td v-else-if='list.flag == 0'>Reject</td>
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
    props: ['id'],
    mounted() {
        console.log('pinjaman edit component');
        console.log(this.id);
        this.loadList();
    },
    data() {
        return {
            lists: [],
            tgl_resign: '',
            submit: 0,

            created_at: '',
            namalengkap: '',
            nominal: '',
            cicilan: '',
            total: '',
            bayar: '',
            keterangan: '',
            note_admin: '',
            note_ketua: '',
            status: '',
        }
    },
    methods:{
        loadList: function(e){
            var vm = this;
            axios.post('/api/bayar_list',{
               id: vm.id,
                api_token: vm.$parent.api_token,
            })
            .then(function (response) {
                console.log(response.data);
                if(response.data.success){
                   var header = response.data.header[0];
                   console.log('header', header);
                   vm.created_at = vm.$parent.tglCetak(header.created_at)
                   vm.namalengkap = vm.$parent.cetak(header.namalengkap)
                   vm.nominal = vm.$parent.blurN(header.nominal)
                   vm.cicilan = vm.$parent.blurN(header.cicilan)+' @ '+header.tenor
                   vm.total = vm.$parent.blurN(header.cicilan * header.tenor)
                   vm.bayar = vm.$parent.blurN(header.bayar)
                   vm.keterangan = header.keterangan
                   vm.note_admin = header.note_admin
                   vm.note_ketua = header.note_ketua
                   vm.status = header.status

                    var temp = response.data.data;
                    vm.lists = [];
                    for(var i=0; i<temp.length; i++){
                       var gambar =
                        vm.lists.push({
                            id: temp[i].id,
                            tgl_pembayaran: vm.$parent.tglCetak(temp[i].tgl_pembayaran),
                            nominal: vm.$parent.blurN(temp[i].nominal),
                            jenis: temp[i].jenis,
                            flag: temp[i].flag,
                        });
                    }
                }else{
                    Swal.fire({
                      title: 'Error!',
                      text: response.data.message,
                      type: 'error',
                    })
                }
            })
            .catch(function (error) {
                alert(error);
            });
        },
    }
}
