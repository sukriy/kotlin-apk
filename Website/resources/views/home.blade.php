@extends('template.home_template')

@section('content')
   <router-view></router-view>
@endsection

@section('script')
	<script type = "text/javascript">
        var flag = '{{Session::get('flag')}}';
        var level = '{{Session::get('level')}}';
        var urlName = ['pinjaman_add', 'pinjaman_edit','tabunganSukarela_add', 'tabunganSukarela_edit'];

        @component('component.saldo')@endcomponent
        @component('component.saldo_detail')@endcomponent
        @component('component.home')@endcomponent
        @component('component.member')@endcomponent
        @component('component.profile')@endcomponent
        @component('component.history')@endcomponent
        @component('component.report')@endcomponent
        @component('component.changePassword')@endcomponent
        @component('component.account')@endcomponent
        @component('component.account_edit')@endcomponent
        @component('component.account_add')@endcomponent

        @component('component.pinjaman')@endcomponent
        @component('component.pinjaman_add')@endcomponent
        @component('component.pinjaman_edit')@endcomponent
        @component('component.pinjaman_detail')@endcomponent

        @component('component.tabunganSukarela')@endcomponent
        @component('component.tabunganSukarela_add')@endcomponent
        @component('component.tabunganSukarela_edit')@endcomponent
        Vue.use(VeeValidate);

        function guard(to, from, next)
        {
            if(to.name == 'member'){
                if(flag == 2){
                    next('/');
                }else{
                    next();
                }
            }else{
                if(flag == 1 && to.name != 'member'){
                    next('/member');
                }else if(to.name == 'account' && level == 'Anggota'){
                    next(from.fullPath);
                }else if(urlName.indexOf(to.name) >= 0 && level != 'Anggota'){
                    next(from.fullPath);
                }else{
                    next();
                }

            }
        }

        function ucword(string)
        {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        const routes = [
            {
                path: '/',
                name: 'home',
                component: home,
                beforeEnter: guard
            },
            {
                path: '/member',
                name: 'member',
                component: member,
                beforeEnter: guard
            },
            {
                path: '/profile',
                name: 'profile',
                component: profile,
                beforeEnter: guard
            },
            {
                path: '/history',
                name: 'history',
                component: history,
                beforeEnter: guard
            },
            {
                path: '/saldo',
                name: 'saldo',
                component: saldo,
                beforeEnter: guard
            },
            {
               path: '/saldo_detail/:id',
               name: 'saldo_detail',
               component: saldo_detail,
               props: true,
               beforeEnter: guard
            },
            {
                path: '/report',
                name: 'report',
                component: report,
                beforeEnter: guard
            },
            {
                path: '/changePassword',
                name: 'changePassword',
                component: changePassword,
                beforeEnter: guard
            },
            {
                path: '/account',
                name: 'account',
                component: account,
                beforeEnter: guard
            },
            {
                path: '/account_edit/:id',
                name: 'account_edit',
                component: account_edit,
                props: true,
                beforeEnter: guard
            },
            {
                path: '/account_add',
                name: 'account_add',
                component: account_add,
                beforeEnter: guard
            },
            {
                path: '/pinjaman',
                name: 'pinjaman',
                component: pinjaman,
                beforeEnter: guard
            },
            {
                path: '/pinjaman_add',
                name: 'pinjaman_add',
                component: pinjaman_add,
                beforeEnter: guard
            },
            {
                path: '/pinjaman_edit/:id',
                name: 'pinjaman_edit',
                component: pinjaman_edit,
                props: true,
                beforeEnter: guard
            },
            {
                path: '/pinjaman_detail/:id',
                name: 'pinjaman_detail',
                component: pinjaman_detail,
                props: true,
                beforeEnter: guard
            },
            {
                path: '/tabunganSukarela',
                name: 'tabunganSukarela',
                component: tabunganSukarela,
                beforeEnter: guard
            },
            {
                path: '/tabunganSukarela_add',
                name: 'tabunganSukarela_add',
                component: tabunganSukarela_add,
                beforeEnter: guard
            },
            {
                path: '/tabunganSukarela_edit/:id',
                name: 'tabunganSukarela_edit',
                component: tabunganSukarela_edit,
                props: true,
                beforeEnter: guard
            },
        ];

        const router = new VueRouter({
            mode: 'history',
            routes // short for `routes: routes`
        });

        var vm = new Vue({
            el: '#app',
            data: {
               id: "{{Session::get('id')}}",
               namalengkap: ucword("{{Session::get('namalengkap')}}"),
               level: ucword("{{Session::get('level')}}"),
               gambar: "/images/account/"+"{{Session::get('gambar')}}",
               api_token: "{{Session::get('api_token')}}",
               flag: "{{Session::get('flag')}}",
               saldo: "{{ MyHelper::rupiah(Session::get('saldo')) }}",
           },
           router,
           created(){
               this.$validator.extend('nominal',{
                   getMessage(field, val){
                       return 'field must be number';
                   },
                   validate(value, field){
                       if(value == '') return true;
                       return Number.isInteger(parseInt(value.replace(/,/g, '')));
                   }
               });
               this.$validator.extend('nominalLength',{
                   getMessage(field, val){
                       return 'field max length '+val[0]+' digits';
                   },
                   validate(value, field){
                       if(value == '') return true;
                       return value.replace(/,/g, '').length <= field;
                   }
               });
           },
           methods:{
               coba(){
                   console.log('coba');
               },
               calculate(pinjaman, bunga, tenor){
                 bunga = bunga.toString().replace(/%/g, '') / 100;
                 pinjaman = this.focusN(pinjaman);
                 var cal = Math.ceil((pinjaman * bunga) + (pinjaman / tenor));
                 return this.blurN(cal);
               },
               focusN(e){
                   if(e == null) return e;
                   return e.toString().replace(/,/g, '');
               },
               blurN(e) {
                   if(e == null) return e;
                   return e.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
               },
               cetak(e){
                   if(e == null) return e;
                   return e.toString().replace(/\w\S*/g, function(txt){
                       return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                   });
               },
               tglCetak(e){
                   if(e == null) return e;
                   var d = new Date(e);
                   return ("0" + d.getDate()).slice(-2)+'-'+("0" + (d.getMonth() + 1)).slice(-2)+'-'+d.getFullYear();
               },
               tglForm(e){
                   if(e == null) return e;
                   e = e.split("-");
                   e = e[2]+'-'+e[1]+'-'+e[0];
                   var d = new Date(e);
                   return d.getFullYear()+'-'+("0" + (d.getMonth() + 1)).slice(-2)+'-'+("0" + d.getDate()).slice(-2);
               },
           }
        });
	</script>
@endsection
