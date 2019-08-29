const pinjaman_add = {
    template: `
    <div class="container-fluid">
        <h4 class="page-title">Pinjaman</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Pinjaman Add</div>
                    </div>
                    <div class="card-body">
                        <form id='formInput'>
                            <div class="form-group">
                                <label for="text">Nominal</label>
                                <input type="text" class="form-control" placeholder="Nominal" name='nominal' v-model=nominal
                                    @blur="nominal = $parent.blurN(nominal)" @focus="nominal = $parent.focusN(nominal)"
                                    @change="calculate()"
                                    v-validate="'required|nominal|nominalLength:11|nominalMax'"
                                >
                                <span class='text-danger'>@{{ errors.first('nominal') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="text">Tenor</label>
                                <select class="form-control" name='tenor' v-model=tenor placeholder="Toner"
                                    v-validate="'included:1,2,3,4,5,6,7,8,9,10,11,12'" @change="calculate()"
                                >
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                    <option>6</option>
                                    <option>7</option>
                                    <option>8</option>
                                    <option>9</option>
                                    <option>10</option>
                                    <option>11</option>
                                    <option>12</option>
                                </select>
                                <span class='text-danger'>@{{ errors.first('tenor') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="text">Bunga</label>
                                <b><span v-text=bunga></span></b>
                            </div>
                            <div class="form-group">
                                <label for="text">Cicilan</label>
                                <b><span v-text=cicilan></span></b>
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
        console.log('pinjaman add component');
    },
    created(){
        var vm = this;
        axios.post('/api/account_detail',{
            id: vm.$parent.id,
            api_token: vm.$parent.api_token,
        })
        .then(function (response) {
            var temp = response.data.data;
            console.log(temp);
            var A = new Date();
            var B = new Date(temp.tgl_join);
            var lama = A.getMonth() - B.getMonth() + (12 * (A.getFullYear() - B.getFullYear()));

            if(lama >= 15){
                lama = Math.floor(lama / 12) * 2;
                if(lama > 10){
                    lama = 10;
                }
            }else{
                lama = 0;
                Swal.fire({
                    title: 'Error!',
                    text: 'Anda belum berhak melakukan pinjaman',
                    type: 'warning',
                    showConfirmButton: true,
                }).then(function(e){
                    router.push({ name: 'pinjaman'})
                });
            }
            max = lama * temp.gaji;

            vm.$validator.extend('nominalMax',{
                getMessage(field, val){
                    return 'max pinjaman '+vm.$parent.blurN(max)+' digits';
                },
                validate(value, field){
                    if(value == '') return true;
                    return value.replace(/,/g, '') <= max;
                }
            });
        })
        .catch(function (error) {
            alert(error);
        });
    },
    data() {
        return {
            nominal: '',
            tenor: '',
            bunga: '2%',
            cicilan: '',
            keterangan: '',
            submit: 0,
        }
    },
    methods:{
        calculate: function () {
            if(this.nominal != '' && this.tenor != ''){
                this.cicilan = this.$parent.calculate(this.nominal, this.bunga, this.tenor);
            }
        },
        formInput: function (e) {
            this.$validator.validateAll().then((result) => {
                if (result) {
                    if(this.submit == 1){
                        return;
                    }
                    this.submit = 1;

                    var vm = this;
                    let formData = new FormData();
    				formData.append('nominal', this.$parent.focusN(this.nominal));
    				formData.append('tenor', this.tenor);
    				formData.append('bunga', this.bunga);
    				formData.append('cicilan', this.$parent.focusN(this.cicilan));
    				formData.append('keterangan', this.keterangan);
                    formData.append('api_token', this.$parent.api_token);

                    axios.post('/api/pinjaman_add', formData)
                    .then(function (response) {
                        console.log(response.data);
                        vm.submit = 0;
                        if(response.data.success){
                            Swal.fire({
                                title: 'Success!',
                                text: response.data.message,
                                type: 'success',
                                showConfirmButton: true,
                            }).then(function(e){
                                router.push({ name: 'pinjaman'})
                            });
                        }else{
                            Swal.fire({
                                title: 'Error!',
                                text: response.data.message,
                                type: 'warning',
                            });
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.submit = 0;
                        alert(error);
                    });
                }
            })
        }
    }
}
