<template>
    <main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <div class="col-md-12">
                    <p class="h1 text-center">Usuarios</p>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-2">
                        <p class="h3 text-right">Buscador</p>
                    </div>
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
                    <div class="col-md-4 text-center">
                        <button v-on:click="abrirModal('usuario', 'registrar')" type="button" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo usuario
                        </button>
                    </div>
                </div>
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th colspan="4">
                                <p class="h3 text-center">Listado de usuarios</p>
                            </th>
                        </tr>
                        <tr>
                            <th>Opciones</th>
                            <th>Rol</th>
                            <th>Email</th>
                            <th>Condición</th>
                        </tr>
                    </thead>
                    <tbody v-if="array_usuario.length">
                        <tr v-for="usuario in array_usuario" :key="usuario.id">
                            <td>
                                <button v-on:click="abrirModal('usuario', 'actualizar', usuario)" type="button" class="btn btn-warning btn-sm">
                                  <i class="icon-pencil"></i>
                                </button> &nbsp;
                                <button type="button" class="btn btn-danger btn-sm" v-on:click="eliminarUsuario(usuario.id)">
                                    <i class="icon-trash"></i>
                                </button>
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
                    <tbody v-else>
                        <tr>
                            <td colspan="4">
                                <p class="h2 text-center">NO hay usuarios registrados</p>
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
                            <label class="col-md-3 form-control-label" for="text-input">
                                Rol <span style="color:red;" v-show="rol_id == 0">(*Seleccione)</span>
                            </label>
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
                            <label class="col-md-3 form-control-label" for="text-input">
                                Email <span style="color:red;" v-show="email.length == 0">(*Ingrese)</span>
                            </label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="email" class="form-control" placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">
                                Password <span style="color:red;" v-show="password.length == 0">(*Ingrese)</span>
                            </label>
                            <div class="col-md-9">
                                <input type="password" v-model.trim="password" class="form-control" placeholder="Password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">
                                Confirmar password <span style="color:red;" v-show="password_confirmar.length == 0">(*Ingrese)</span>
                            </label>
                            <div class="col-md-9">
                                <input type="password" v-model.trim="password_confirmar" class="form-control" placeholder="Password">
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
                password_confirmar : '',
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
                    swal(
                      'Registrado',
                      'El usuario ha sido registrado',
                      'success'
                    )
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
                    swal(
                      'Registrado',
                      'El usuario ha sido registrado',
                      'success'
                    )
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

                if (!this.password_confirmar) this.error_msj_usu.push('Confirme el password');

                if ( this.password != this.password_confirmar) this.error_msj_usu.push('El password debe coincidir');

                if (this.error_msj_usu.length) this.error_usuario = 1;

                return this.error_usuario;
            },
            eliminarUsuario (usuario_id){
                swal({
                  title: '¿Estas seguro de eliminar a este usuario?',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Aceptar',
                  cancelButtonText: 'Cancelar',
                  confirmButtonClass: 'btn btn-success',
                  cancelButtonClass: 'btn btn-danger',
                  buttonsStyling: false,
                  reverseButtons: true
                }).then((result) => {
                  if (result.value) {

                    let me = this;

                    axios.delete('/user/delete/' + usuario_id)
                    .then(function (response){
                        var respuesta = response.data;

                        if (respuesta) {
                            
                            me.listarUsuario('', 'email');
                            swal(
                              'Eliminado',
                              'El usuario ha sido eliminado',
                              'success'
                            )

                        } else {
                            swal(
                              'Error',
                              'No puedes eliminar tu usuario',
                              'error'
                            )
                        }
                    })
                    .catch(function (error){
                        console.log(error);
                    });

                  } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                  ) {
                    
                  }
                })
            },
            cerrarModal (){
                this.modal = 0;
                this.titulo_modal = '';
                this.rol_id = 0;
                this.usuario_id = 0;
                this.email = '';
                this.password = '';
                this.password_confirmar = '';
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