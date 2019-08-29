<div class="sidebar">
    <div class="scrollbar-inner sidebar-wrapper">
        <div class="user">
            <div class="photo">
                <img :src=this.gambar>
            </div>
            <div class="info">
                <a>
                    <span>
                        <span v-text=this.namalengkap></span>
                        <span class="user-level" v-if="(this.level == 'Anggota')" v-text=this.saldo></span>
                        <span class="user-level" v-else v-text=this.level></span>
                    </span>
                </a>
            </div>
        </div>
        <ul class="nav">
            <li class="nav-item" v-if='(this.level != "Anggota")'>
                <router-link to = "/account">
                    <i class="la la-table"></i>
                    <p>Account</p>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link to = "/pinjaman">
                    <i class="la la-money"></i>
                    <p>Pinjaman</p>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link to = "/tabunganSukarela">
                    <i class="la la-money"></i>
                    <p>Tabungan Sukarela</p>
                </router-link>
            </li>
            <li class="nav-item" v-if='(this.level != "Anggota")'>
               <router-link to = "/saldo">
                   <i class="la la-money"></i>
                   <p>Saldo</p>
               </router-link>
           </li>
           <li class="nav-item" v-if='(this.level != "Anggota")'>
                <router-link to = "/report">
                    <i class="la la-table"></i>
                    <p>Report</p>
                </router-link>
            </li>
        </ul>
    </div>
</div>
