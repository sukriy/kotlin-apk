const account = {
    template: `
    <div class="container-fluid">
        <h4 class="page-title">Account</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <button type="button" class="btn btn-primary" v-if="($parent.level != 'Anggota')"  @click='accountAdd()'>Add Account</button>
                    </div>
                    <div class="card-body">
                        <div class='table-responsive'>
                            <table class="table mt-3" id='example'>
                                <thead>
                                    <tr align='center'>
                                        <th scope="col">No</th>
                                        <th scope="col">Nama Lengkap</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Level</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Manage</th>
                                    </tr>
                                </thead>
                                <tbody align='left'>
                                    <tr v-for="list in lists">
                                        <td v-text='list.id'></td>
                                        <td v-text='list.namalengkap'></td>
                                        <td v-text='list.email'></td>
                                        <td v-text='list.level'></td>
                                        <td v-if='list.flag == 1'>Non Active</td>
                                        <td v-else-if='list.flag == 2'>Active</td>
                                        <td v-else-if='list.flag == 3'>Resign</td>
                                        <td v-else-if='list.flag == 3'>Resign</td>
                                        <td v-else-if='list.flag == 4'>Resign</td>
                                        <td v-else>undefined</td>
                                        <td>
                                            <button v-if="($parent.flag != 1)" @click='resign(list.id)' type="button" class="btn btn-danger"><i class='la la-check-circle'>Resign</i></button>
                                            <button v-if="(list.flag == 1)" @click='acc(list.id, list.gambar)' type="button" class="btn btn-primary"><i class='la la-check-circle'>Acc</i></button>
                                            <button v-if="($parent.level != 'Anggota')" @click='edit(list.id)' type="button" class="btn btn-primary"><i class='la la-edit'>Edit</i></button>
                                            <button v-if="($parent.level == 'Ketua')" @click='del(list.id)' type="button" class="btn btn-danger"><i class='la la-trash'>Delete</i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4>Tanggal Berapa Resign?</h4>
                        <br>
                        <input type="text" class="datepicker form-control" placeholder="Tanggl Resign"
                            name='tgl_resign' v-model=tgl_resign v-validate="'required|date_format:dd-MM-yyyy'"
                            autocomplete="off"
                        >
                        <span id='error_modal' class='text-danger'>@{{ errors.first('tgl_resign') }}</span>
                        <br>
                        <button type="button" @click='formInput()' class="form-control btn btn-default">Input</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `,
    mounted() {
        console.log('account component')
        this.loadList();
        var vm = this;
        $( ".datepicker" ).datepicker({
            dateFormat: "dd-mm-yy",
            onSelect: function(dateText, inst) {
                vm.tgl_resign = dateText;
            }
        });
    },
    data() {
        return {
            lists: [],
            tgl_resign: '',
            id: '',
            submit: 0,
        }
    },
    methods:{
        loadList: function(e){
            var vm = this;
            axios.post('/api/account',{
                api_token: vm.$parent.api_token,
            })
            .then(function (response) {
                if(response.data.success){
                    var temp = response.data.message;
                    vm.lists = [];
                    for(var i=0; i<temp.length; i++){
                        vm.lists.push({
                            id: temp[i].id,
                            namalengkap: vm.$parent.cetak(temp[i].namalengkap),
                            level: temp[i].level,
                            email: temp[i].email,
                            flag: temp[i].flag,
                            gambar: temp[i].transaksi_gambar,
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
        resign: function(id){
            this.id = id;
            this.tgl_resign = '';
            $('#error_modal').hide();
            $('#myModal').modal('show');
        },
        formInput: function(){
            console.log('formInput');
            this.$validator.validateAll().then((result) => {
                if (result) {
                    if(this.submit == 1){
                        return;
                    }
                    this.submit = 1;
                    var vm = this;

                    axios.post('/api/resign',{
                        id: vm.id,
                        tgl_resign: vm.$parent.tglForm(vm.tgl_resign),
                        api_token: vm.$parent.api_token,
                    })
                    .then(function (response) {
                        vm.submit = 0;
                        console.log(response.data);
                        if(response.data.success){
                            Swal.fire({
                                title: 'Success!',
                                text: response.data.message,
                                type: 'success',
                            });
                            $('#myModal').modal('hide');
                        }else{
                            Swal.fire({
                                title: 'Error!',
                                text: response.data.message,
                                type: 'warning',
                            });
                        }
                    })
                    .catch(function (error) {
                        vm.submit = 0;
                        alert(error);
                    });
                }
            });
            $('#error_modal').show();
        },
        acc: function(id, gambar){
            var vm = this;
            if(gambar == null){
                Swal.fire({
                  title: 'Approve Pembayaran Wajib!',
                  text: 'Do you want to continue.',
                  html: 'Telah Menerima Pembayaran Wajib Tunai',
                  showConfirmButton: true,
                  showCancelButton: true,
                  confirmButtonText: 'Confirm',
                  cancelButtonText: 'Reject'
                }).then(function(e){
                    if(e.value || e.dismiss == 'cancel'){
                        if(e.value){
                            var response = true;
                        }else if(e.dismiss == 'cancel'){
                            var response = false;
                        }
                        axios.post('/api/account_acc',{
                            'id': id,
                            'flag': response,
                            'api_token': vm.$parent.api_token,
                        })
                        .then(function (response) {
                            if(response.data.success){
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.data.message,
                                    type: 'success',
                                });
                            }else{
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.data.message,
                                    type: 'warning',
                                });
                            }
                            vm.loadList();
                        })
                        .catch(function (error) {
                            alert(error);
                        });
                    }
                })
            }else{
                Swal.fire({
                  title: 'Approve Pembayaran Wajib!',
                  text: 'Do you want to continue.',
                  imageUrl: '/images/pembayaran/'+gambar,
                  imageWidth: 400,
                  imageHeight: 200,
                  showConfirmButton: true,
                  showCancelButton: true,
                  confirmButtonText: 'Confirm',
                  cancelButtonText: 'Reject'
                }).then(function(e){
                    if(e.value || e.dismiss == 'cancel'){
                        if(e.value){
                            var response = true;
                        }else if(e.dismiss == 'cancel'){
                            var response = false;
                        }
                        axios.post('/api/account_acc',{
                            'id': id,
                            'flag': response,
                            'api_token': vm.$parent.api_token,
                        })
                        .then(function (response) {
                            if(response.data.success){
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.data.message,
                                    type: 'success',
                                });
                            }else{
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.data.message,
                                    type: 'warning',
                                });
                            }
                            vm.loadList();
                        })
                        .catch(function (error) {
                            alert(error);
                        });
                    }

                })
            }
        },
        edit: function (e) {
            router.push({ name: 'account_edit', params: { id: e } })
        },
        del: function (val) {
            var vm = this;
            Swal.fire({
                title: 'Delete User!',
                text: 'Do you want to continue',
                type: 'warning',
                showConfirmButton: true,
                showCancelButton: true,
            }).then(function(e){
                if(e.value){
                    axios.post('/api/account_delete',{
                        'id': val,
                        'api_token': vm.$parent.api_token,
                    })
                    .then(function (response) {
                        if(response.data.success){
                            Swal.fire({
                                title: 'Success!',
                                text: response.data.message,
                                type: 'success',
                            });
                        }else{
                            Swal.fire({
                                title: 'Error!',
                                text: response.data.message,
                                type: 'warning',
                            });
                        }
                        vm.loadList();
                    })
                    .catch(function (error) {
                        alert(error);
                    });
                }
            });
        },
        accountAdd: function (e) {
            router.push({ name: 'account_add'})
        }
    }
}
