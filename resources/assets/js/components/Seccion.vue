<template>
    <main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Secciones
                <button v-on:click="abrirModal('seccion', 'registrar')" type="button" class="btn btn-secondary">
                    <i class="icon-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <select class="form-control col-md-3" v-model="criterio">
                              <option value="nombre_seccion">Nombre seccion</option>
                              <option value="periodo_inicio">Periodo inicio</option>
                              <option value="periodo_fin">Periodo fin</option>
                            </select>
                            <input v-on:keyup.enter="listarSeccion(1, buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
                            <button v-on:click="listarSeccion(1, buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
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
                            <th>Nombre de la seccion</th>
                            <th>Periodo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="seccion in array_seccion" :key="seccion.id">
                            <td>
                                <button v-on:click="abrirModal('seccion', 'actualizar', seccion)" type="button" class="btn btn-warning btn-sm">
                                  <i class="icon-pencil"></i>
                                </button> &nbsp;
                            </td>
                            <td v-text="seccion.nombre_seccion"></td>
                            <td v-text="seccion.periodo.periodo_inicio + '-' + seccion.periodo.periodo_fin"></td>
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
                            <label class="col-md-3 form-control-label" for="text-input">Seccion</label>
                            <div class="col-md-9">
                                <select v-model.trim="periodo_id">
                                    <option v-bind:value="0"></option>
                                    <option v-for="periodo in array_periodo" :key="periodo.id" v-bind:value="periodo.id" >
                                        {{ periodo.periodo_inicio + '-' + periodo.periodo_fin }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Nombre de la seccion</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="nombre_seccion" class="form-control" placeholder="Nombre de la seccion">
                            </div>
                        </div>
                        <div v-show="error_seccion" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in error_msj_per" :key="error" v-text="error"></div>
                            </div>
                        </div> 
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" v-on:click="cerrarModal()">Cerrar</button>
                    <button v-if="tipo_accion == 1" v-on:click="registrarSeccion()" type="button" class="btn btn-primary">Guardar</button>
                    <button v-if="tipo_accion == 2" v-on:click="actualizarSeccion()" type="button" class="btn btn-primary">Actualizar</button>
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
                periodo_id : 0,
                seccion_id : 0,
                nombre_seccion : '',
                array_periodo: [],
                array_seccion : [],
                modal : 0,
                titulo_modal : '',
                tipo_accion : 0,
                error_seccion : 0,
                error_msj_per : 0,
                pagination : {
                    'total' : 0,
                    'current_page' :0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0
                },
                offset : 3,
                criterio : 'nombre_seccion',
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
            listarSeccion (page, buscar, criterio){
                let me = this;
                var url = '/seccion?page=' + page + '&buscar='+buscar+'&criterio='+criterio;

                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.array_seccion = respuesta.secciones.data;
                    me.pagination = respuesta.pagination;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPagina (page, buscar, criterio){
                let me = this;
                //actualiza la pagina actual
                me.pagination.current_page = page;
                //envia la peticion para visualizar la data de esa pagina
                me.listarSeccion(page, buscar, criterio);
            },
            listarPeriodo (){
                let me = this;

                axios.get('/periodo/listar-periodos').then(function (response) {
                    me.array_periodo = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            registrarSeccion (){
                
                if (this.validarSeccion()) {
                    return;
                }

                let me = this;

                axios.post('/seccion/registrar', {
                    'periodo_id': this.periodo_id,
                    'nombre_seccion': this.nombre_seccion
                }).then(function (){
                    me.cerrarModal();
                    me.listarSeccion(1, '', 'nombre_seccion');
                })
                .catch(function (){
                    console.log(error);
                });
            },
            actualizarSeccion (){

                if (this.validarSeccion()){
                    return;
                }

                let me = this;

                axios.put('/seccion/actualizar', {
                    'periodo_id': this.periodo_id,
                    'nombre_seccion': this.nombre_seccion,
                    'id': this.seccion_id
                }).then(function (){
                    me.cerrarModal();
                    me.listarSeccion(1, '', 'nombre_seccion');
                })
                .catch(function (){
                    console.log(error);
                });
            },
            validarSeccion (){
                this.error_seccion = 0;
                this.error_msj_per =[];

                if (!this.periodo_id) this.error_msj_per.push('Seleccione un periodo');
                if (!this.nombre_seccion) this.error_msj_per.push('El nombre de la seccion no dede estar vacio');

                if (this.error_msj_per.length) this.error_seccion = 1;

                return this.error_seccion;
            },
            cerrarModal (){
                this.modal = 0;
                this.titulo_modal = '';
                this.seccion_id = 0;
                this.periodo_id = '';
                this.nombre_seccion = '';
            },
            abrirModal (modelo, accion, data = []){
                switch (modelo){
                    case "seccion":
                    {
                        switch (accion){
                            case "registrar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Registrar seccion',
                                this.periodo_id = 0;
                                this.nombre_seccion = '';
                                this.tipo_accion = 1;
                                break;
                            }
                            case "actualizar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Actualizar seccion';
                                this.tipo_accion = 2;
                                this.periodo_id = data['periodo_id'];
                                this.seccion_id = data['id'];
                                this.nombre_seccion = data['nombre_seccion'];
                                break;
                            }
                        }
                    }
                }
            },
            limpiarBuscar (){
                this.buscar = '';
                this.criterio = 'nombre_seccion';
                this.listarSeccion(1, this.buscar, this.criterio);
            }
        },
        mounted() {
            this.listarPeriodo();
            this.listarSeccion(1, this.buscar, this.criterio);
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