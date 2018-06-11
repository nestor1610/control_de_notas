<template>
    <main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Usuarios
                <button v-on:click="abrirModal('usuario', 'registrar')" type="button" class="btn btn-secondary">
                    <i class="icon-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <select class="form-control col-md-3" v-model="criterio">
                              <option value="email">Email</option>
                            </select>
                            <input v-on:keyup.enter="listarUsuario(buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
                            <button v-on:click="listarUsuario(buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            <button v-on:click="limpiarBuscar()" type="submit" class="btn btn-primary">
                                <i class="icon-trash"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Opciones</th>
                            <th>Rol</th>
                            <th>Email</th>
                            <th>Condicion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="usuario in array_usuario" :key="usuario.id">
                            <td>
                                <button v-on:click="abrirModal('usuario', 'actualizar', usuario)" type="button" class="btn btn-warning btn-sm">
                                  <i class="icon-pencil"></i>
                                </button> &nbsp;
                            </td>
                            <td v-text="usuario.rol.nombre"></td>
                            <td v-text="usuario.email"></td>
                            <td>
                                <div v-if="usuario.condicion">
                                    <span class="badge badge-success">Activo</span>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Desactivado</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Fin ejemplo de tabla Listado -->
    </div>
    <!--Inicio del modal agregar/actualizar-->
    <div :class="{'mostrar' : modal}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 v-text="titulo_modal" class="modal-title"></h4>
                    <button type="button" class="close" v-on:click="cerrarModal()" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Rol</label>
                            <div class="col-md-9">
                                <select v-model.trim="rol_id">
                                    <option v-bind:value="0" selected>Seleccione un rol</option>
                                    <option v-for="rol in array_rol" :key="rol.id" v-bind:value="rol.id" >
                                        {{ rol.nombre }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Email</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="email" class="form-control" placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Password</label>
                            <div class="col-md-9">
                                <input type="password" v-model.trim="password" class="form-control" placeholder="Password">
                            </div>
                        </div>
                        <div v-show="error_usuario" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in error_msj_usu" :key="error" v-text="error"></div>
                            </div>
                        </div> 
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" v-on:click="cerrarModal()">Cerrar</button>
                    <button v-if="tipo_accion == 1" v-on:click="registrarUsuario()" type="button" class="btn btn-primary">Guardar</button>
                    <button v-if="tipo_accion == 2" v-on:click="actualizarUsuario()" type="button" class="btn btn-primary">Actualizar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->
    </main>
</template>

<script>
    export default {
        data (){
            return {
                rol_id : 0,
                usuario_id : 0,
                email : '',
                password : '',
                array_usuario : [],
                array_rol : [],
                modal : 0,
                titulo_modal : '',
                tipo_accion : 0,
                error_usuario : 0,
                error_msj_usu : 0,
                criterio : 'email',
                buscar : ''
            }
        },
        methods : {
            listarUsuario (buscar, criterio){
                let me = this;
                var url = '/user?buscar='+buscar+'&criterio='+criterio;

                axios.get(url).then(function (response) {
                    me.array_usuario = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            listarRoles (){
                let me = this;
                var url = '/rol/listar-roles'

                axios.get(url).then(function (response) {
                    me.array_rol = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            registrarUsuario (){
                
                if (this.validarUsuario()) {
                    return;
                }

                let me = this;

                axios.post('/user/registrar', {
                    'email': this.email,
                    'password': this.password,
                    'rol_id': this.rol_id
                }).then(function (response){
                    me.cerrarModal();
                    me.listarUsuario('', 'email');
                })
                .catch(function (error){
                    console.log(error);
                });
            },
            actualizarUsuario (){

                if (this.validarUsuario()){
                    return;
                }

                let me = this;

                axios.put('/user/actualizar', {
                    'email': this.email,
                    'password': this.password,
                    'rol_id': this.rol_id,
                    'id': this.usuario_id
                }).then(function (response){
                    me.cerrarModal();
                    me.listarUsuario('', 'email');
                })
                .catch(function (error){
                    console.log(error);
                });
            },
            validarUsuario (){
                this.error_usuario = 0;
                this.error_msj_usu =[];

                var validar_email = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

                if ( this.rol_id == 0 ) this.error_msj_usu.push('Seleccione un rol');

                if ( !validar_email.test( this.email ) ) this.error_msj_usu.push('El email del usuario debe ser valido');

                if (!this.password) this.error_msj_usu.push('El password del usuario no puede estar vacio');

                if (this.error_msj_usu.length) this.error_usuario = 1;

                return this.error_usuario;
            },
            cerrarModal (){
                this.modal = 0;
                this.titulo_modal = '';
                this.rol_id = 0;
                this.usuario_id = 0;
                this.email = '';
                this.password = '';
            },
            abrirModal (modelo, accion, data = []){
                switch (modelo){
                    case "usuario":
                    {
                        switch (accion){
                            case "registrar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Registrar usuario',
                                this.tipo_accion = 1;
                                this.rol_id = 0;
                                this.email = '';
                                this.password = '';
                                break;
                            }
                            case "actualizar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Actualizar usuario';
                                this.tipo_accion = 2;
                                this.rol_id = data['rol_id'];
                                this.email = data['email'];
                                this.usuario_id = data['id'];
                                break;
                            }
                        }
                    }
                }
            },
            limpiarBuscar (){
                this.buscar = '';
                this.criterio = 'email';
                this.listarUsuario(this.buscar, this.criterio);
            }
        },
        mounted() {
            this.listarUsuario(this.buscar, this.criterio);
            this.listarRoles();
        }
 }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .div-error{
        display: flex;
        justify-content: center;
    }
    .text-error{
        color: red;
        font-weight: bold;
    }
</style>