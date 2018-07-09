<template>
    <main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <div class="col-md-12">
                    <p class="h1 text-center">Asignaturas</p>
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
                              <option value="nombre_asignatura">Nombre asignatura</option>
                            </select>
                            <input onkeypress="return soloLetrasConAcentos(event)" v-on:keyup.enter="listarAsignatura(1, buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
                            <button v-on:click="listarAsignatura(1, buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            <button v-on:click="limpiarBuscar()" type="submit" class="btn btn-primary">
                                <i class="icon-trash"></i> Limpiar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <button v-on:click="abrirModal('asignatura', 'registrar')" type="button" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nueva Asignatura
                        </button>
                    </div>
                </div>
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th colspan="3">
                                <p class="h3 text-center">Listado de asignaturas</p>
                            </th>
                        </tr>
                        <tr>
                            <th>Opciones</th>
                            <th>Asignatura</th>
                            <th>Secciones</th>
                        </tr>
                    </thead>
                    <tbody v-if="array_asignatura.length">
                        <tr v-for="asignatura in array_asignatura" :key="asignatura.id">
                            <td>
                                <button v-on:click="abrirModal('asignatura', 'actualizar', asignatura)" type="button" class="btn btn-warning btn-sm">
                                  <i class="icon-pencil"></i>
                                </button> &nbsp;
                                <button v-on:click="abrirModal('asignatura', 'ingresar_seccion', asignatura)" type="button" class="btn btn-success btn-sm">
                                  <i class="icon-plus"></i>
                                </button> &nbsp;
                            </td>
                            <td v-text="asignatura.nombre_asignatura"></td>
                            <td>
                                <ul>
                                    <li v-for="seccion in asignatura.secciones" :key="seccion.id">
                                        {{ 'Año: ' + seccion.ano + ' - ' + seccion.nombre_seccion  }}
                                        <button v-on:click="eliminarSeccion(seccion)" type="button" class="btn btn-danger btn-sm">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="3">
                                <p class="h2 text-center">NO hay asignaturas registradas</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a href="#" class="page-link" v-on:click.prevent="cambiarPagina(pagination.current_page - 1, buscar, criterio)">
                                Pre
                            </a>
                        </li>
                        <li class="page-item" v-for="page in pages_number" :key="page" v-bind:class="[page == is_actived ? 'active' : '']">
                            <a href="#" class="page-link" v-on:click.prevent="cambiarPagina(page, buscar, criterio)" v-text="page">
                            </a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a href="#" class="page-link" v-on:click.prevent="cambiarPagina(pagination.current_page + 1, buscar, criterio)">
                                Next
                            </a>
                        </li>
                    </ul>
                </nav>
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
                            <label class="col-md-3 form-control-label" for="email-input">
                                Asignatura <span style="color:red;" v-show="nombre_asignatura.length == 0">(*Ingrese)</span>
                            </label>
                            <div class="col-md-9">
                                <input v-on:keyup="primeraLetraM()" type="text" onkeypress="return soloLetrasConAcentos(event)" v-model.trim="nombre_asignatura" class="form-control" placeholder="Nombre de la asignatura">
                            </div>
                        </div>
                        <div v-show="error_asignatura" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in error_msj_asig" :key="error" v-text="error"></div>
                            </div>
                        </div> 
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" v-on:click="cerrarModal()">Cerrar</button>
                    <button v-if="tipo_accion == 1" v-on:click="registrarAsignatura()" type="button" class="btn btn-primary">Guardar</button>
                    <button v-if="tipo_accion == 2" v-on:click="actualizarAsignatura()" type="button" class="btn btn-primary">Actualizar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Inicio del modal agregar seccion-->
    <div :class="{'mostrar' : modal == 2}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
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
                            <label class="col-md-3 form-control-label" for="email-input">Secciones</label>
                            <div class="col-md-9">
                                <select v-model.trim="seccion_id">
                                        
                                    <option v-bind:value="0" selected>Ninguna sección</option>
                                    <option v-for="seccion in array_seccion" :key="seccion.id" v-bind:value="seccion.id">
                                        {{ 'Año: ' + seccion.ano + ' - ' + seccion.nombre_seccion  }}
                                    </option>

                                </select>
                            </div>
                        </div>
                        <div v-show="error_asignatura" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in error_msj_asig" :key="error" v-text="error"></div>
                            </div>
                        </div> 
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" v-on:click="cerrarModal()">Cerrar</button>
                    <button v-on:click="ingresarSeccion()" type="button" class="btn btn-primary">Guardar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal seccion-->
    </main>
</template>

<script>
    export default {
        data (){
            return {
                asignatura_id : 0,
                seccion_id : 0,
                nombre_asignatura : '',
                array_asignatura : [],
                array_seccion : [],
                modal : 0,
                titulo_modal : '',
                tipo_accion : 0,
                error_asignatura : 0,
                error_msj_asig : 0,
                pagination : {
                    'total' : 0,
                    'current_page' :0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0
                },
                offset : 3,
                criterio : 'nombre_asignatura',
                buscar : ''
            }
        },
        computed : {
            is_actived: function (){
                return this.pagination.current_page;
            },
            pages_number: function (){

                if (!this.pagination.to){
                    return [];
                }

                var from = this.pagination.current_page - this.offset;

                if (from < 1) from = 1;

                var to = from + (this.offset * 2);

                if (to >= this.pagination.last_page) to = this.pagination.last_page;

                var page_array = [];

                while (from <= to) {
                    page_array.push(from);
                    from++;
                }

                return page_array;
            }
        },
        methods : {
            listarAsignatura (page, buscar, criterio){
                let me = this;
                var url = '/asignatura?page=' + page + '&buscar='+buscar+'&criterio='+criterio;

                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.array_asignatura = respuesta.asignaturas.data;
                    me.pagination = respuesta.pagination;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            listarSeccion (){
                let me = this;
                var url = '/seccion/listar-seccion?asignatura_id=' + this.asignatura_id;

                axios.get(url).then(function (response) {
                    me.array_seccion = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPagina (page, buscar, criterio){
                let me = this;
                //actualiza la pagina actual
                me.pagination.current_page = page;
                //envia la peticion para visualizar la data de esa pagina
                me.listarAsignatura(page, buscar, criterio);
            },
            registrarAsignatura (){
                
                if (this.validarAsignatura()) {
                    return;
                }

                let me = this;

                axios.post('/asignatura/registrar', {
                    'periodo_id': this.periodo_id,
                    'nombre_asignatura': this.nombre_asignatura
                }).then(function (response){
                    me.cerrarModal();
                    swal(
                      'Registrada',
                      'La asignatura ha sido registrada',
                      'success'
                    )
                    me.listarAsignatura(1, '', 'nombre_asignatura');
                })
                .catch(function (error){
                    console.log(error);
                });
            },
            ingresarSeccion (){

                if (this.validarSeccion()) {
                    return;
                }

                let me = this;

                axios.post('/asignatura/registrar/seccion', {
                    'seccion_id': this.seccion_id,
                    'asignatura_id': this.asignatura_id
                }).then(function (response){

                    var respuesta = response.data;

                    if (respuesta) {

                        me.listarAsignatura(1, '', 'nombre_asignatura');
                        swal(
                          'Asignada',
                          'La asignatura ha sido asignada a la sección',
                          'success'
                        )
                        me.listarSeccion();

                    } else {
                        swal(
                          'Error',
                          'No puedes asignar una asignatura a una sección sin alumnos',
                          'error'
                        )
                    }
                    
                })
                .catch(function (error){
                    console.log(error);
                });
            },
            eliminarSeccion (seccion){
                let me = this;

                me.asignatura_id = seccion.pivot.asignatura_id;
                me.seccion_id = seccion.pivot.seccion_id;

                axios.delete('/asignatura/eliminar/' + me.asignatura_id + '/' + me.seccion_id)
                .then(function (response){
                    console.log(response.data);
                    me.listarAsignatura(1, '', 'nombre_asignatura');
                    swal(
                      'Retirada',
                      'La sección ha sido retirada',
                      'success'
                    )
                    me.asignatura_id = 0;
                    me.seccion_id = 0;
                })
                .catch(function (error){
                    console.log(error);
                });

            },
            actualizarAsignatura (){

                if (this.validarAsignatura()){
                    return;
                }

                let me = this;

                axios.put('/asignatura/actualizar', {
                    'nombre_asignatura': this.nombre_asignatura,
                    'id': this.asignatura_id
                }).then(function (){
                    me.cerrarModal();
                    swal(
                      'Actualizada',
                      'La asignatura ha sido actualizada',
                      'success'
                    )
                    me.listarAsignatura(1, '', 'nombre_asignatura');
                })
                .catch(function (error){
                    console.log(error);
                });
            },
            primeraLetraM () {
                this.nombre_asignatura = this.nombre_asignatura.charAt(0).toUpperCase() + this.nombre_asignatura.slice(1);
            },
            validarAsignatura (){
                this.error_asignatura = 0;
                this.error_msj_asig =[];

                if (!this.nombre_asignatura) this.error_msj_asig.push('El nombre de la asignatura no debe estar vacío');

                if ( this.nombre_asignatura.length > 30 ) this.error_msj_asig.push('El nombre de la asignatura no debe ser mayor de 30 carácteres');

                if (this.error_msj_asig.length) this.error_asignatura = 1;

                return this.error_asignatura;
            },
            validarSeccion (){
                if (!this.seccion_id) return 1;
            },
            cerrarModal (){
                this.modal = 0;
                this.titulo_modal = '';
                this.asignatura_id = 0;
                this.seccion_id = 0;
                this.tipo_accion = 0;
                this.nombre_asignatura = '';
            },
            abrirModal (modelo, accion, data = []){
                switch (modelo){
                    case "asignatura":
                    {
                        switch (accion){
                            case "registrar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Registrar asignatura',
                                this.nombre_asignatura = '';
                                this.tipo_accion = 1;
                                break;
                            }
                            case "actualizar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Actualizar asignatura';
                                this.tipo_accion = 2;
                                this.asignatura_id = data['id'];
                                this.nombre_asignatura = data['nombre_asignatura'];
                                break;
                            }
                            case "ingresar_seccion":
                            {
                                this.asignatura_id = data['id'];
                                this.listarSeccion();
                                this.modal = 2;
                                this.titulo_modal = 'Asignar secciones a ' + data['nombre_asignatura'];
                                this.tipo_accion = 3;
                                break;
                            }
                        }
                    }
                }
            },
            limpiarBuscar (){
                this.buscar = '';
                this.criterio = 'nombre_asignatura';
                this.listarAsignatura(1, this.buscar, this.criterio);
            }
        },
        mounted() {
            this.listarAsignatura(1, this.buscar, this.criterio);
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