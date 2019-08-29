const tabunganSukarela = {
    template: `
    <div class="container-fluid">
        <h4 class="page-title">Tabungan Sukarela</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div v-if="($parent.level == 'Anggota')" class="card-header">
                        <button type="button" class="btn btn-primary" @click='tabunganSukarelaAdd()'>Add Tabungan Sukarela</button>
                    </div>
                    <div class="card-body">
                        <div class='table-responsive'>
                            <table class="table mt-3" id='example'>
                                <thead>
                                    <tr align='center'>
                                        <th scope="col">Nama Lengkap</th>
                                        <th scope="col">Nominal</th>
                                        <th scope="col">Tgl Pembayaran</th>
                                        <th scope="col">Keterangan</th>
                                        <th scope="col">Note Admin</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr align='left' v-for="list in lists">
                                        <td v-text='list.namalengkap'></td>
                                        <td align='right' v-text='list.nominal'></td>
                                        <td v-text='list.tgl_pembayaran'></td>
                                        <td v-text='list.keterangan'></td>
                                        <td v-text='list.note'></td>
                                        <td v-if='list.flag == 0'>Reject</td>
                                        <td v-else-if='list.flag == 1'>Pending</td>
                                        <td v-else-if='list.flag == 2'>Approve</td>
                                        <td v-else>undefined</td>
                                        <td>
                                            <button v-if="(list.flag == 1 && list.id_user == $parent.id)" @click='edit(list.id)' type="button" class="btn btn-primary"><i class='la la-edit'>Edit</i></button>
                                            <button v-if="((list.flag == 1 && list.id_user == $parent.id) || $parent.level == 'Ketua')" @click='del(list.id)' type="button" class="btn btn-danger"><i class='la la-trash'>Delete</i></button>
                                            <button v-if="($parent.level != 'Anggota')" @click='acc(list.id, list.gambar)' type="button" class="btn btn-primary"><i class='la la-check-circle'>Acc</i></button>
                                        </td>
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
        console.log('Tabungan Sukarela component')
        this.loadList();
    },
    data() {
        return {
            lists: []
        }
    },
    methods:{
        loadList: function(e){
            var vm = this;
            axios.post('/api/tabunganSukarela',{
                api_token: vm.$parent.api_token,
            })
            .then(function (response) {
               if(response.data.success == false){
                  Swal.fire({
                     title: 'Bermasalah!',
                     text: response.data.message,
                     type: 'success',
                  });
               }else{
                  var temp = response.data.data;
                  vm.lists = [];

                  for(var i=0; i<temp.length; i++){
                      vm.lists.push({
                          id: temp[i].id,
                          namalengkap: vm.$parent.cetak(temp[i].namalengkap),
                          nominal: vm.$parent.blurN(temp[i].nominal),
                          tgl_pembayaran: vm.$parent.tglCetak(temp[i].tgl_pembayaran),
                          keterangan: temp[i].keterangan,
                          note: temp[i].note,
                          gambar: temp[i].gambar,
                          status: temp[i].status,
                          id_user: temp[i].id_user,
                          flag: temp[i].flag,
                      });
                  }
               }
            })
            .catch(function (error) {
                alert(error);
            });
        },
        edit: function (e) {
            router.push({ name: 'tabunganSukarela_edit', params: { id: e } })
        },
        del: function (val) {
            console.log('del');
            var vm = this;
            Swal.fire({
                title: 'Delete Tabungan Sukarela!',
                text: 'Do you want to continue',
                type: 'warning',
                showConfirmButton: true,
                showCancelButton: true,
            }).then(function(e){
                if(e.value){
                    axios.post('/api/tabunganSukarela_delete',{
                        'id': val,
                        'api_token': vm.$parent.api_token,
                    })
                    .then(function (response) {
                        vm.loadList();
                    })
                    .catch(function (error) {
                        alert(error);
                    });
                }
            });
        },
        tabunganSukarelaAdd: function (e) {
            router.push({ name: 'tabunganSukarela_add'})
        },
        acc: function(val, gambar){
            var vm = this;
            Swal.fire({
                title: 'Approve Tabungan Sukarela!',
                text: 'Do you want to continue',
                imageUrl: "/images/TabunganSukarela/"+gambar,
                imageWidth: 400,
                imageHeight: 200,
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Reject',
                input: 'textarea',
                inputPlaceholder: 'Type your message here...',
                showCancelButton: true
            }).then(function(e){
               if(e.dismiss != "backdrop"){
                   var acc = true;
                   if(e.dismiss == 'cancel'){
                       acc = false;
                   }

                    axios.post('/api/tabunganSukarela_acc',{
                        'id': val,
                        'acc': acc,
                        'note': e.value,
                        'api_token': vm.$parent.api_token,
                    })
                    .then(function (response) {
                        console.log(response.data);
                        if(response.data.success){
                            vm.loadList();
                        }else{
                            Swal.fire({
                                title: 'Error!',
                                text: response.data.message,
                                type: 'warning',
                            });
                        }
                    })
                    .catch(function (error) {
                        alert(error);
                    });
                }
            });
        }
    }
}
