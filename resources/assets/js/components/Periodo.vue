<template>
    <main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Periodos
                <button v-on:click="abrirModal('periodo', 'registrar')" type="button" class="btn btn-secondary">
                    <i class="icon-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <select class="form-control col-md-3" v-model="criterio">
                              <option value="periodo_inicio">Inicio</option>
                              <option value="periodo_fin">Fin</option>
                            </select>
                            <input v-on:keyup.enter="ListarPeriodo(buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
                            <button v-on:click="ListarPeriodo(buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
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
                            <th>Inicio de periodo</th>
                            <th>Fin de periodo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="periodo in array_periodo" :key="periodo.id">
                            <td>
                                <button v-on:click="abrirModal('periodo', 'actualizar', periodo)" type="button" class="btn btn-warning btn-sm">
                                  <i class="icon-pencil"></i>
                                </button> &nbsp;
                            </td>
                            <td v-text="periodo.periodo_inicio"></td>
                            <td v-text="periodo.periodo_fin"></td>
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
                            <label class="col-md-3 form-control-label" for="text-input">Inicio del periodo</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="periodo_inicio" class="form-control" placeholder="Inicio del periodo">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Fin del periodo</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="periodo_fin" class="form-control" placeholder="Fin del periodo">
                            </div>
                        </div>
                        <div v-show="error_periodo" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in error_msj_per" :key="error" v-text="error"></div>
                            </div>
                        </div> 
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" v-on:click="cerrarModal()">Cerrar</button>
                    <button v-if="tipo_accion == 1" v-on:click="registrarPeriodo()" type="button" class="btn btn-primary">Guardar</button>
                    <button v-if="tipo_accion == 2" v-on:click="actualizarPeriodo()" type="button" class="btn btn-primary">Actualizar</button>
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
                periodo_inicio : '',
                periodo_fin : '',
                array_periodo : [],
                modal : 0,
                titulo_modal : '',
                tipo_accion : 0,
                error_periodo : 0,
                error_msj_per : 0,
                criterio : 'periodo_inicio',
                buscar : ''
            }
        },
        methods : {
            ListarPeriodo (buscar, criterio){
                let me = this;
                var url = '/periodo?buscar='+buscar+'&criterio='+criterio;

                axios.get(url).then(function (response) {
                    me.array_periodo = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            registrarPeriodo (){
                
                if (this.validarPeriodo()) {
                    return;
                }

                let me = this;

                axios.post('/periodo/registrar', {
                    'periodo_inicio': this.periodo_inicio,
                    'periodo_fin': this.periodo_fin
                }).then(function (){
                    me.cerrarModal();
                    me.ListarPeriodo('', 'periodo_inicio');
                })
                .catch(function (){
                    console.log(error);
                });
            },
            actualizarPeriodo (){

                if (this.validarPeriodo()){
                    return;
                }

                let me = this;

                axios.put('/periodo/actualizar', {
                    'periodo_inicio': this.periodo_inicio,
                    'periodo_fin': this.periodo_fin,
                    'id': this.periodo_id
                }).then(function (){
                    me.cerrarModal();
                    me.ListarPeriodo('', 'periodo_inicio');
                })
                .catch(function (){
                    console.log(error);
                });
            },
            validarPeriodo (){
                this.error_periodo = 0;
                this.error_msj_per =[];

                if (!this.periodo_inicio) this.error_msj_per.push('El inicio del periodo no puede estar vacio');
                if (!this.periodo_fin) this.error_msj_per.push('El final del periodo no puede estar vacio');

                if (this.error_msj_per.length) this.error_periodo = 1;

                return this.error_periodo;
            },
            cerrarModal (){
                this.modal = 0;
                this.titulo_modal = '';
                this.periodo_id = 0;
                this.periodo_inicio = '';
                this.periodo_fin = '';
            },
            abrirModal (modelo, accion, data = []){
                switch (modelo){
                    case "periodo":
                    {
                        switch (accion){
                            case "registrar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Registrar periodo',
                                this.periodo_inicio = '';
                                this.periodo_fin = '';
                                this.tipo_accion = 1;
                                break;
                            }
                            case "actualizar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Actualizar periodo';
                                this.tipo_accion = 2;
                                this.periodo_id = data['id'];
                                this.periodo_inicio = data['periodo_inicio'];
                                this.periodo_fin = data['periodo_fin'];
                                break;
                            }
                        }
                    }
                }
            },
            limpiarBuscar (){
                this.buscar = '';
                this.criterio = 'periodo_inicio';
                this.ListarPeriodo(this.buscar, this.criterio);
            }
        },
        mounted() {
            this.ListarPeriodo(this.buscar, this.criterio);
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