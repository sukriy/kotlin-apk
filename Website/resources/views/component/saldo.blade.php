const saldo = {
    template: `
    <div class="container-fluid">
        <h4 class="page-title">Saldo</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class='table-responsive'>
                            <table class="table mt-3" id='example'>
                                <thead>
                                    <tr align='center'>
                                        <th scope="col">Nama Lengkap</th>
                                        <th scope="col">Saldo</th>
                                        <th scope="col">Manage</th>
                                    </tr>
                                </thead>
                                <tbody align='left'>
                                    <tr v-for="list in lists">
                                        <td v-text='list.namalengkap'></td>
                                        <td v-text='list.saldo'></td>
                                        <td>
                                            <button @click='detail(list.id)' type="button" class="btn btn-primary"><i class='la la-check-circle'>Detail</i></button>
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
        console.log('saldo component')
        this.loadList();
    },
    data() {
        return {
            lists: [],
            submit: 0,
        }
    },
    methods:{
        loadList: function(e){
            var vm = this;
            axios.post('/api/saldo',{
                api_token: vm.$parent.api_token,
            })
            .then(function (response) {
                if(response.data.success){
                    var temp = response.data.data;
                    vm.lists = [];
                    for(var i=0; i<temp.length; i++){
                        vm.lists.push({
                            id: temp[i].id_user,
                            namalengkap: vm.$parent.cetak(temp[i].namalengkap),
                            saldo: vm.$parent.blurN(temp[i].nominal)
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
        detail: function (e) {
            router.push({ name: 'saldo_detail', params: { id: e } })
        },
    }
}
