const changePassword = {
    template: `
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Change Password</div>
                    </div>
                    <div class="card-body">
                        <form id='formInput'>
                            <div class="form-group">
                                <label for="password_current">Current Password</label>
                                <input type="password" class="form-control" placeholder="Current Password" name='password_current' v-model=password_current
                                    v-validate="'required|min:6|max:250'"
                                >
                                <span class='text-danger'>@{{ errors.first('password_current') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">New Password</label>
                                <input type="password" class="form-control" placeholder="New Password" name='password_confirmation' v-model=password_confirmation
                                    v-validate="'required|min:6|max:250'" ref="password"
                                >
                                <span class='text-danger'>@{{ errors.first('password_confirmation') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="password">Confirm Password</label>
                                <input type="password" class="form-control" placeholder="Confirm Password" name='password' v-model='password'
                                    v-validate="'required|confirmed:password'" data-vv-as="password"
                                >
                                <span class='text-danger'>@{{ errors.first('password') }}</span>
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
        console.log('Change Password component');
    },
    data() {
        return {
            password_current: '',
            password: '',
            password_confirmation: '',
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
                    formData.append('password_current', this.password_current);
                    formData.append('password', this.password);
                    formData.append('password_confirmation', this.password_confirmation);
                    formData.append('api_token', this.$parent.api_token);

                    axios.post('/api/changePassword', formData)
                    .then(function(response){
                        vm.submit = 0;
                        console.log(response.data);
                        if(response.data.success){
                            Swal.fire({
                                title: 'Success!',
                                text: 'Success Change Password',
                                type: 'success',
                                showConfirmButton: true,
                            }).then(function(e){
                                router.push({ name: '/'})
                            });
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
