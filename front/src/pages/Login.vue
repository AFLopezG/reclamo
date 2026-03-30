<template>
    <q-layout view="hHr LpR lFf">
      <q-page-container class="public-page">
        <q-page class="row items-center justify-center">
          <q-card class="public-card" style="width: 420px; max-width: 92vw">
            <q-card-section class="row items-center">
              <q-avatar rounded size="52px" class="bg-grey-2">
                <img src="img/escudo.jpg" alt="Escudo" />
              </q-avatar>
              <div class="q-ml-md">
                <div class="text-h6">Ingreso al sistema</div>
                <div class="text-caption text-grey-7">Reclamos ATM</div>
              </div>
            </q-card-section>
            <q-separator />
            <q-card-section>
              <q-form @submit.prevent="login" class="q-gutter-sm">
                <q-input outlined dense v-model="name" label="Cuenta" />
                <q-input outlined dense v-model="password" :type="typePassword ? 'password' : 'text'" label="Password">
                  <template v-slot:append>
                    <q-icon @click="typePassword=!typePassword" :name="typePassword?'visibility':'visibility_off'" class="cursor-pointer" />
                  </template>
                </q-input>
                <q-btn color="primary" type="submit" label="Ingresar" icon="login" class="full-width" :loading="loading" />
              </q-form>
            </q-card-section>
          </q-card>
        </q-page>
      </q-page-container>
    </q-layout>
</template>
<script>
import {globalStore} from 'stores/globalStore'
  export default {
    name: 'LoginPage',
    data () {
      return {
        name: '',
        password: '',
        remember: false,
        typePassword: true,
        loading: false,
        store:globalStore()
      }
    },
    mounted () {
      console.log(this.$url)
      if (this.store.isLoggedIn) {
        this.$router.push('/navegador')
      }
    }, 
    methods: {
      login () {
        this.loading = true
        this.$api.post('login', {
          name: this.name,
          password: this.password
        }).then(res => {
          this.$q.notify({
            message: 'Bienvenido',
            color: 'positive',
            icon: 'check_circle',
            position: 'top'
          })
          console.log(res.data)
          this.store.setAuth(res.data.user, res.data.permisos || [])
          this.store.isLoggedIn = true
          this.$api.defaults.headers.common.Authorization = 'Bearer ' + res.data.token
          localStorage.setItem('tokenReclamo', res.data.token)
          this.$router.push('/navegador')
        }).catch(error => {
          console.log(error)
          this.$q.notify({
            message: error,
            color: 'negative',
            position: 'top',
            timeout: 2000
          })
        }).finally(() => {
          this.loading = false
        })
      }
    }
}
</script>
<style scoped>
.cursor-pointer {
  cursor: pointer;
}

@-webkit-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
@-moz-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

.fadeIn {
  opacity:0;
  -webkit-animation:fadeIn ease-in 1;
  -moz-animation:fadeIn ease-in 1;
  animation:fadeIn ease-in 1;

  -webkit-animation-fill-mode:forwards;
  -moz-animation-fill-mode:forwards;
  animation-fill-mode:forwards;

  -webkit-animation-duration:1s;
  -moz-animation-duration:1s;
  animation-duration:1s;
}

.fadeIn.first {
  -webkit-animation-delay: 0.4s;
  -moz-animation-delay: 0.4s;
  animation-delay: 0.4s;
}

.fadeIn.second {
  -webkit-animation-delay: 0.6s;
  -moz-animation-delay: 0.6s;
  animation-delay: 0.6s;
}

.fadeIn.third {
  -webkit-animation-delay: 0.8s;
  -moz-animation-delay: 0.8s;
  animation-delay: 0.8s;
}

.fadeIn.fourth {
  -webkit-animation-delay: 1s;
  -moz-animation-delay: 1s;
  animation-delay: 1s;
}

/* Simple CSS3 Fade-in Animation */
.underlineHover:after {
  display: block;
  left: 0;
  bottom: -10px;
  width: 0;
  height: 2px;
  background-color: #56baed;
  content: "";
  transition: width 0.2s;
}

.underlineHover:hover {
  color: #0d0d0d;
}

.underlineHover:hover:after{
  width: 100%;
}



/* OTHERS */

*:focus {
    outline: none;
} 

#icon {
  width:60%;
}

* {
  box-sizing: border-box;
}
</style>
