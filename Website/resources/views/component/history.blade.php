const history = {
    template: `
    <div class="container-fluid">
        <h4 class="page-title">History</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class='table-responsive'>
                            <table class="table mt-3" id='example'>
                                <thead>
                                    <tr align='center'>
                                        <th scope="col">Tgl</th>
                                        <th scope="col">Jenis</th>
                                        <th scope="col">Nominal</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr align='left' v-for="list in lists">
                                        <td v-text='list.tgl_pembayaran'></td>
                                        <td v-text='list.pembayaran'></td>
                                        <td align='right' v-text='list.nominal'></td>
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
    mounted() {
        console.log('history component')
        this.loadList();
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
            console.log('api_token = '+vm.$parent.api_token)
            axios.post('/api/history',{
                api_token: vm.$parent.api_token,
            })
            .then(function (response) {
                console.log(response.data);
                if(response.data.success){
                    var temp = response.data.data;
                    vm.lists = [];
                    for(var i=0; i<temp.length; i++){
                        vm.lists.push({
                            id: temp[i].id,
                            tgl_pembayaran: vm.$parent.tglCetak(temp[i].tgl_pembayaran),
                            pembayaran: vm.$parent.cetak(temp[i].pembayaran),
                            nominal: vm.$parent.blurN(temp[i].nominal),
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
