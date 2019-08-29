const pinjaman = {
    template: `
    <div class="container-fluid">
        <h4 class="page-title">Pinjaman</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div v-if="($parent.level == 'Anggota')" class="card-header">
                        <button type="button" class="btn btn-primary" @click='pinjamanAdd()'>Add Pinjaman</button>
                    </div>
                    <div class="card-body">
                        <div class='table-responsive'>
                            <table class="table mt-3" id='example'>
                                <thead>
                                    <tr align='center'>
                                        <th scope="col">Nama Lengkap</th>
                                        <th scope="col">Nominal</th>
                                        <th scope="col">Cicilan (2%)</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Bayar</th>
                                        <th scope="col">Keterangan</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr  align='left' v-for="list in lists">
                                        <td v-text='list.namalengkap'></td>
                                        <td align='right' v-text='list.nominal'></td>
                                        <td align='right' v-text='list.cicilan'></td>
                                        <td align='right' v-text='list.total'></td>
                                        <td align='right' v-text='list.bayar'></td>
                                        <td v-text='list.keterangan'></td>
                                        <td v-text='list.status'></td>
                                        <td>
                                            <button v-if="(list.flag != 2 || list.flag != 3)" @click='detail(list.id)' type="button" class="btn btn-primary"><i class='la la-edit'>Detail</i></button>
                                            <button v-if="(list.flag == 1 && list.id_user == $parent.id)" @click='edit(list.id)' type="button" class="btn btn-primary"><i class='la la-edit'>Edit</i></button>
                                            <button v-if="((list.flag == 1 && list.id_user == $parent.id) || (list.flag < 4 && $parent.level == 'Ketua'))" @click='del(list.id)' type="button" class="btn btn-danger"><i class='la la-trash'>Delete</i></button>
                                            <button v-if="(list.flag == 3 || list.flag == 4)" @click='bayar(list.id)' type="button" class="btn btn-primary"><i class='la la-check-circle'>Bayar</i></button>
                                            <button v-if="((list.flag == 3 || list.flag == 4) && $parent.level != 'Anggota' && list.gambar != null)" @click='konfirmasi(list.id, list.gambar, list.lastPay)' type="button" class="btn btn-primary"><i class='la la-check-circle'>Konfirmasi</i></button>
                                            <button v-if="($parent.level == 'Admin' && list.flag == 1)" @click='acc(list.id)' type="button" class="btn btn-primary"><i class='la la-check-circle'>Acc</i></button>
                                            <button v-if="($parent.level == 'Ketua' && (list.flag == 1 || list.flag == 2))" @click='acc(list.id)' type="button" class="btn btn-primary"><i class='la la-check-circle'>Acc</i></button>
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
                        <h4>Pembayaran Pinjaman</h4>
                        <br>
                        <div class="form-group">
                            <label for="tgl_pembayaran">Tanggl Pembayaran</label>
                            <input type="text" class="datepicker form-control" placeholder="Tanggl Pembayaran"
                                name='tgl_pembayaran' v-model=tgl_pembayaran v-validate="'required|date_format:dd-MM-yyyy'"
                                autocomplete="off"
                            >
                            <span class='error_modal text-danger'>@{{ errors.first('tgl_pembayaran') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="nominal">Nominal</label>
                            <input type="text" class="form-control" placeholder="Nominal" name='nominal' v-model="nominal"
                                @blur="nominal = $parent.blurN(nominal)" @focus="nominal = $parent.focusN(nominal)"
                                v-validate="'required|nominal|nominalLength:11'"
                            >
                            <span class='error_modal text-danger'>@{{ errors.first('nominal') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="telepon">Gambar</label>
                            <input type="file" class="form-control" name='gambar' accept="image/*" ref='gambar'
                                v-validate="'image'" data-vv-as="image"
                            >
                            <span class='error_modal text-danger'>@{{ errors.first('gambar') }}</span>
                        </div>
                        <br>
                        <button type="button" @click='formInput()' class="form-control btn btn-default">Input</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `,
    mounted() {
        console.log('pinjaman component');
        var vm = this;
        $( ".datepicker" ).datepicker({
            dateFormat: "dd-mm-yy",
            onSelect: function(dateText, inst) {
                vm.tgl_pembayaran = dateText;
            }
        });
        this.loadList();
    },
    data() {
        return {
            lists: [],
            id: '',
            tgl_pembayaran: '',
            nominal: '',
            gambar: '',
            submit: 0
        }
    },
    methods:{
        loadList: function(e){
            var vm = this;
            axios.post('/api/pinjaman',{
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
                  console.log(temp);
                  vm.lists = [];
                  for(var i=0; i<temp.length; i++){
                      vm.lists.push({
                          id: temp[i].id,
                          namalengkap: vm.$parent.cetak(temp[i].namalengkap),
                          nominal: vm.$parent.blurN(temp[i].nominal),
                          cicilan: vm.$parent.blurN(temp[i].cicilan)+' @ '+temp[i].tenor,
                          total: vm.$parent.blurN(temp[i].cicilan * temp[i].tenor),
                          lastPay: vm.$parent.blurN(temp[i].lastPay),
                          bayar: vm.$parent.blurN(temp[i].bayar),
                          keterangan: temp[i].keterangan,
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
        konfirmasi: function(id, gambar, pay){
            console.log('konfirmasi');
            console.log(gambar);
            console.log(pay);
            var vm = this;
            if(gambar == null){
                Swal.fire({
                  title: 'Approve Pembayaran Wajib!',
                  text: 'Do you receive '+pay,
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
                        axios.post('/api/pinjaman_konfirmasi',{
                            'id': id,
                            'flag': response,
                            'api_token': vm.$parent.api_token,
                        })
                        .then(function (response) {
                            console.log(response);
                            if(response.data.success){
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.data.message,
                                    type: 'success',
                                });
                                $('#myModal').modal('hide');
                                vm.loadList();
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
                        axios.post('/api/pinjaman_konfirmasi',{
                            'id': id,
                            'flag': response,
                            'api_token': vm.$parent.api_token,
                        })
                        .then(function (response) {
                            console.log(response.data);
                            if(response.data.success){
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.data.message,
                                    type: 'success',
                                });
                                $('#myModal').modal('hide');
                                vm.loadList();
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
        detail: function (e) {
            console.log('detail');
            router.push({ name: 'pinjaman_detail', params: { id: e } })
        },
        edit: function (e) {
           console.log('edit');
           router.push({ name: 'pinjaman_edit', params: { id: e } })
        },
        del: function (val) {
            console.log('edit');
            var vm = this;
            Swal.fire({
                title: 'Delete Pinjaman!',
                text: 'Do you want to continue',
                type: 'warning',
                showConfirmButton: true,
                showCancelButton: true,
            }).then(function(e){
                if(e.value){
                    axios.post('/api/pinjaman_delete',{
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
        pinjamanAdd: function (e) {
            router.push({ name: 'pinjaman_add'})
        },
        bayar: function(e){
            console.log('bayar');
            this.id = e;
            this.tgl_pembayaran = '';
            this.nominal = '';

            const input = this.$refs.gambar
            input.type = 'text'
            input.type = 'file'

            $('.error_modal').hide();
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

                    let formData = new FormData();
                    formData.append('id', this.id);
                    formData.append('tgl_pembayaran', this.$parent.tglForm(this.tgl_pembayaran));
                    formData.append('nominal', this.$parent.focusN(this.nominal));
                    formData.append('api_token', this.$parent.api_token);
                    if(this.$refs.gambar.files[0]){
                        formData.append('gambar', this.$refs.gambar.files[0]);
                    }

                    axios.post(
                        '/api/pinjaman_pembayaran',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
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
                        vm.submit = 0;
                        alert(error);
                    });
                }else{
                    $('.error_modal').show();
                }
            });
        },
        acc: function(val){
            var vm = this;
            Swal.fire({
                title: 'Approval!',
                text: 'Do you want to continue',
                type: 'warning',
                showConfirmButton: true,
                showCancelButton: true,
                cancelButtonText: 'No',
                input: 'textarea',
                inputPlaceholder: 'Type your message here...',
                showCancelButton: true
            }).then(function(e){
                console.log(e);
                if(e.dismiss != "backdrop"){
                    var acc = true;
                    if(e.dismiss == 'cancel'){
                        acc = false;
                    }
                    axios.post('/api/pinjaman_acc',{
                        'id': val,
                        'acc': acc,
                        'api_token': vm.$parent.api_token,
                        'note': e.value
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
