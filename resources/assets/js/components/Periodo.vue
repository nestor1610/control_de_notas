<template>
    <main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <div class="col-md-12">
                    <p class="h1 text-center">Períodos</p>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-2">
                        <p class="h3 text-right">Buscador</p>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="form-control" for="text-input">Inicio del período</label>
                            <input type="number" v-model="periodo_inicio" class="form-control" placeholder="Inicio">
                            <label class="form-control" for="text-input">Fin del período</label>
                            <input type="number" v-model="periodo_fin" class="form-control" placeholder="Fin">
                            <button v-on:click="listarPeriodo(periodo_inicio, periodo_fin, 1)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            <button v-on:click="limpiarBuscar()" type="submit" class="btn btn-primary">
                                <i class="icon-trash"></i> Limpiar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <button v-on:click="abrirModal('periodo', 'registrar')" type="button" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo período
                        </button>
                    </div>
                    <div class="col-md-12 text-center">
                        <div v-show="error_periodo" class="div-error">
                            <div class="text-center text-error">
                                <div v-for="error in error_msj_per" :key="error" v-text="error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <tr>
                                <th colspan="3">
                                    <p class="h3 text-center">Listado de períodos</p>
                                </th>
                            </tr>
                            <th>Opciones</th>
                            <th>Inicio de período</th>
                            <th>Fin de período</th>
                        </tr>
                    </thead>
                    <tbody v-if="array_periodo.length">
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
                    <tbody v-else>
                        <tr>
                            <td colspan="3">
                                <p class="h2 text-center">NO hay períodos registrados</p>
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
                                Inicio del período <span style="color:red;" v-show="periodo_inicio.length == 0">(*Ingrese)</span>
                            </label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="periodo_inicio" class="form-control" placeholder="Inicio del período">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">
                                Fin del período <span style="color:red;" v-show="periodo_fin.length == 0">(*Ingrese)</span>
                            </label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="periodo_fin" class="form-control" placeholder="Fin del período">
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
                periodo_inicio : 0,
                periodo_fin : 0,
                array_periodo : [],
                modal : 0,
                titulo_modal : '',
                tipo_accion : 0,
                error_periodo : 0,
                error_msj_per : 0
            }
        },
        methods : {
            listarPeriodo (periodo_inicio, periodo_fin, busqueda = 0){

                if ( !busqueda ){

                } else{
                    if ( this.validarBusqueda() ) return;
                }

                let me = this;

                var url = '/periodo?periodo_inicio='+periodo_inicio+'&periodo_fin='+periodo_fin;

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
                }).then(function (response){
                    me.cerrarModal();
                    swal(
                      'Registrado',
                      'El período ha sido registrado',
                      'success'
                    )
                    me.listarPeriodo(0, 0);
                })
                .catch(function (error){
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
                }).then(function (response){
                    me.cerrarModal();
                    swal(
                      'Actualizado',
                      'El período ha sido actualizado',
                      'success'
                    )
                    me.listarPeriodo(0, 0);
                })
                .catch(function (error){
                    console.log(error);
                });
            },
            validarBusqueda (){
                this.error_periodo = 0;
                this.error_msj_per =[];
                this.periodo_inicio = parseInt( this.periodo_inicio );
                this.periodo_fin = parseInt( this.periodo_fin );

                if ( this.periodo_inicio > 2099 ) this.error_msj_per.push('El inicio del período no debe ser mayor del 2099');

                if ( this.periodo_inicio < 2000 ) this.error_msj_per.push('El inicio del período no debe ser menor del 2000');

                if ( this.periodo_fin > 2099 ) this.error_msj_per.push('El fin del período no debe ser mayor del 2099');

                if ( this.periodo_fin < 2000 ) this.error_msj_per.push('El fin del período no debe ser menor del 2000');

                if ( this.periodo_inicio >= this.periodo_fin ) this.error_msj_per.push('El inicio del período no debe ser mayor o igual que el fin del período');

                if (this.error_msj_per.length) this.error_periodo = 1;

                return this.error_periodo;
            },
            validarPeriodo (){
                this.error_periodo = 0;
                this.error_msj_per =[];
                this.periodo_inicio = parseInt( this.periodo_inicio );
                this.periodo_fin = parseInt( this.periodo_fin );

                if (!this.periodo_inicio) this.error_msj_per.push('El inicio del período no puede estar vacío');

                if ( this.periodo_inicio > 2099 ) this.error_msj_per.push('El inicio del período no debe ser mayor del 2099');

                if ( this.periodo_inicio < 2000 ) this.error_msj_per.push('El inicio del período no debe ser menor del 2000');

                if (!this.periodo_fin) this.error_msj_per.push('El final del período no puede estar vacío');

                if ( this.periodo_fin > 2099 ) this.error_msj_per.push('El fin del período no debe ser mayor del 2099');

                if ( this.periodo_fin < 2000 ) this.error_msj_per.push('El fin del período no debe ser menor del 2000');

                if ( this.periodo_inicio >= this.periodo_fin ) this.error_msj_per.push('El inicio del período no debe ser mayor o igual que el fin del período');

                if (this.error_msj_per.length) this.error_periodo = 1;

                return this.error_periodo;
            },
            cerrarModal (){
                this.modal = 0;
                this.titulo_modal = '';
                this.periodo_id = 0;
                this.periodo_inicio = 0;
                this.periodo_fin = 0;
                this.error_periodo = 0;
                this.error_msj_per = '';
            },
            abrirModal (modelo, accion, data = []){
                switch (modelo){
                    case "periodo":
                    {
                        switch (accion){
                            case "registrar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Registrar período',
                                this.periodo_inicio = 0;
                                this.periodo_fin = 0;
                                this.tipo_accion = 1;
                                break;
                            }
                            case "actualizar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Actualizar período';
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
                this.periodo_inicio = 0;
                this.periodo_fin = 0;
                this.error_periodo = 0;
                this.error_msj_per = '';
                this.listarPeriodo(this.periodo_inicio, this.periodo_fin);
            }
        },
        mounted() {
            this.listarPeriodo(this.periodo_inicio, this.periodo_fin);
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