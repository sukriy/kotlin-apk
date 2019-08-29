<div class="main-header">
    <div class="logo-header">
        <router-link to = "/" class="logo">
            Ready Dashboard
        </router-link>
        <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <button class="topbar-toggler more"><i class="la la-ellipsis-v"></i></button>
    </div>
    <nav class="navbar navbar-header navbar-expand-lg">
        <div class="container-fluid">
            <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle profile-pic" data-toggle="dropdown" aria-expanded="false">
                        <img :src=this.gambar alt="user-img" width="40" class="img-circle">
                        <span v-text=this.namalengkap></span>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li>
                            <div class="user-box">
                                <div class="u-img"><img :src=this.gambar alt="user"></div>
                                <div class="u-text">
                                    <h4 v-text=this.namalengkap></h4>
                                    <p class="text-muted" v-if="(this.level == 'Anggota')" v-text=this.saldo></p>
                                    <p class="text-muted" v-else v-text=this.level></p>
                                    <router-link to = "/profile" class="btn btn-rounded btn-danger btn-sm">
                                        View Profile
                                    </router-link>
                                </div>
                            </div>
                        </li>
                        <div class="dropdown-divider"></div>
                        <router-link to = "/history" class="dropdown-item">
                            <i class="ti-user"></i>History
                        </router-link>
                        <div class="dropdown-divider"></div>
                        <router-link to="/changePassword" class="dropdown-item">Change Password</router-link>
                        <a class="dropdown-item" href="/logout"><i class="fa fa-power-off"></i> Logout</a>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>
