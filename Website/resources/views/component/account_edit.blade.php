const account_edit = {
    template: `
    <div class="container-fluid">
        <h4 class="page-title">Account</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Account Add</div>
                    </div>
                    <div class="card-body">
                        <form id='formInput'>
                            <div class="form-group">
                                <label for="text">Nama Lengkap</label>
                                <input type="text" class="form-control" placeholder="Nama Lengkap" name='namalengkap' v-model=namalengkap
                                    v-validate="'required|min:6|max:250'"
                                >
                                <span class='text-danger'>@{{ errors.first('namalengkap') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" placeholder="Username" name='username' v-model=username
                                    v-validate="'required|min:6|max:250'"
                                >
                                <span class='text-danger'>@{{ errors.first('username') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="level">Level</label>
                                <select class="form-control" name='level' v-model=level
                                    v-validate="'required|included:Anggota,Admin,Ketua'"
                                >
                                    <option>Anggota</option>
                                    <option>Admin</option>
                                    <option>Ketua</option>
                                </select>
                                <span class='text-danger'>@{{ errors.first('level') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" placeholder="Email" name='email' v-model=email
                                    v-validate="'required|email'"
                                >
                                <span class='text-danger'>@{{ errors.first('email') }}</span>
                            </div>
                            <div class="form-check">
                                <label>Jenis Kelamin</label><br/>
                                <label class="form-radio-label">
                                    <input class="form-radio-input" type="radio" name="jenis_kelamin" v-model=jenis_kelamin value="1" v-validate="'required'">
                                    <span class="form-radio-sign">Laki-laki</span>
                                </label>
                                <label class="form-radio-label ml-3">
                                    <input class="form-radio-input" type="radio" name="jenis_kelamin" v-model=jenis_kelamin value="0" v-validate="'required'">
                                    <span class="form-radio-sign">Perempuan</span>
                                </label><br>
                                <span class='text-danger'>@{{ errors.first('jenis_kelamin') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="level">Level</label>
                                <select class="form-control" name='jabatan' v-model=jabatan
                                    v-validate="'included:Staff,SPV,Manager'"
                                >
                                    <option>Staff</option>
                                    <option>SPV</option>
                                    <option>Manager</option>
                                </select>
                                <span class='text-danger'>@{{ errors.first('jabatan') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="gaji">Gaji</label>
                                <input type="text" class="form-control" placeholder="Gaji" name='gaji' v-model="gaji"
                                    @blur="gaji = $parent.blurN(gaji)" @focus="gaji = $parent.focusN(gaji)"
                                    v-validate="'required|nominal|nominalLength:11'"
                                >
                                <span class='text-danger'>@{{ errors.first('gaji') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <input type="text" class="form-control" placeholder="Alamat" name='alamat' v-model=alamat
                                    v-validate="'required|max:250'"
                                >
                                <span class='text-danger'>@{{ errors.first('alamat') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="telepon">Nomor Telepon</label>
                                <input type="text" class="form-control" placeholder="Nomor Telepon" name='telepon' v-model=telepon
                                    v-validate="'required|numeric|max:250'"
                                >
                                <span class='text-danger'>@{{ errors.first('telepon') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="telepon">Tanggl Join</label>
                                <input type="text" class="datepicker form-control" placeholder="Tanggl Join"
                                    name='tgl_join' v-model=tgl_join v-validate="'required|date_format:dd-MM-yyyy'"
                                    autocomplete="off"
                                >
                                <span class='text-danger'>@{{ errors.first('tgl_join') }}</span>
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
    props: ['id'],
    mounted() {
        console.log('account edit component');
        var vm = this;
        $( ".datepicker" ).datepicker({
            dateFormat: "dd-mm-yy",
            onSelect: function(dateText, inst) {
                vm.tgl_join = dateText;
            }
        });
    },
    created(){
        var vm = this;
        axios.post('/api/account_detail',{
            id: vm.id,
            api_token: vm.$parent.api_token,
        })
        .then(function (response) {
            var temp = response.data.data;
            console.log(temp.gaji);
            vm.namalengkap= temp.namalengkap;
            vm.username= temp.username;
            vm.email= temp.email;
            vm.jenis_kelamin= temp.jenis_kelamin;
            vm.jabatan= temp.jabatan;
            vm.gaji= vm.$parent.blurN(temp.gaji);
            vm.alamat= temp.alamat;
            vm.telepon= temp.telepon;
            vm.tgl_join= vm.$parent.tglCetak(temp.tgl_join);
            vm.level= temp.level;
            vm.gambar= '';
        })
        .catch(function (error) {
            alert(error);
        });
    },
    data() {
        return {
            namalengkap: '',
            username: '',
            email: '',
            jenis_kelamin: '',
            jabatan: '',
            gaji: '',
            alamat: '',
            telepon: '',
            tgl_join: '',
            level: '',
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
                    this.submit = 1;
                    var vm = this;

                    let formData = new FormData();
                    formData.append('id', this.id);
                    formData.append('username', this.username);
                    formData.append('namalengkap', this.namalengkap);
                    formData.append('jenis_kelamin', this.jenis_kelamin);
                    formData.append('jabatan', this.jabatan);
                    formData.append('email', this.email);
                    formData.append('gaji', this.$parent.focusN(this.gaji));
                    formData.append('alamat', this.alamat);
                    formData.append('telepon', this.telepon);
                    formData.append('tgl_join', this.$parent.tglForm(this.tgl_join));
                    formData.append('level', this.level);
                    formData.append('api_token', this.$parent.api_token);
                    if(this.$refs.gambar.files[0]){
                        formData.append('gambar', this.$refs.gambar.files[0]);
                    }

                    axios.post(
                        '/api/account_edit',
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
                            console.log('login');
                            router.push({ name: 'account'})
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
            });
        },
    }
}
