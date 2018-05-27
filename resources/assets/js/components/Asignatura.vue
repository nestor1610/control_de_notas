<template>
    <main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Asignaturas
                <button v-on:click="abrirModal('asignatura', 'registrar')" type="button" class="btn btn-secondary">
                    <i class="icon-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <select class="form-control col-md-3" v-model="criterio">
                              <option value="nombre_asignatura">Nombre asignatura</option>
                            </select>
                            <input v-on:keyup.enter="listarAsignatura(1, buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
                            <button v-on:click="listarAsignatura(1, buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
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
                            <th>Asignatura</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="asignatura in array_asignatura" :key="asignatura.id">
                            <td>
                                <button v-on:click="abrirModal('asignatura', 'actualizar', asignatura)" type="button" class="btn btn-warning btn-sm">
                                  <i class="icon-pencil"></i>
                                </button> &nbsp;
                            </td>
                            <td v-text="asignatura.nombre_asignatura"></td>
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
                            <label class="col-md-3 form-control-label" for="email-input">Asignatura</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="nombre_asignatura" class="form-control" placeholder="Nombre de la asignatura">
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
    <!--Fin del modal-->
    </main>
</template>

<script>
    export default {
        data (){
            return {
                asignatura_id : 0,
                nombre_asignatura : '',
                array_asignatura : [],
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
                    me.listarAsignatura(1, '', 'nombre_asignatura');
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
                    me.listarAsignatura(1, '', 'nombre_asignatura');
                })
                .catch(function (error){
                    console.log(error);
                });
            },
            validarAsignatura (){
                this.error_asignatura = 0;
                this.error_msj_asig =[];

                if (!this.nombre_asignatura) this.error_msj_asig.push('El nombre de la asignatura no debe estar vacio');

                if ( this.nombre_asignatura.length > 30 ) this.error_msj_asig.push('El nombre de la asignatura no debe ser mayor de 30 caracteres');

                if (this.error_msj_asig.length) this.error_asignatura = 1;

                return this.error_asignatura;
            },
            cerrarModal (){
                this.modal = 0;
                this.titulo_modal = '';
                this.asignatura_id = 0;
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