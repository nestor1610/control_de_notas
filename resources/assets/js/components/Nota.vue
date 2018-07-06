<template>
    <main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <div class="col-md-12">
                    <p class="h1 text-center">Notas</p>
                </div>
            </div>
            <template v-if="listado == 1">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <select class="form-control col-md-3" v-model.trim="periodo_id" v-on:change="listarSeccion()">
                                    <option v-bind:value="0" selected>Seleccione un período</option>
                                    <option v-for="periodo in array_periodo" :key="periodo.id" v-bind:value="periodo.id" >
                                        {{ periodo.periodo_inicio +'-'+ periodo.periodo_fin }}
                                    </option>
                                </select>
                                <select v-show="periodo_id > 0" class="form-control col-md-3" v-model.trim="seccion_id" v-on:change="listarAsignatura()">
                                    <option v-bind:value="0" selected>Seleccione una sección</option>
                                    <option v-for="seccion in array_seccion" :key="seccion.id" v-bind:value="seccion.id" >
                                        {{ 'Año: ' + seccion.ano + ' - ' + seccion.nombre_seccion  }}
                                    </option>
                                </select>
                                <select v-show="seccion_id > 0" class="form-control col-md-3" v-model.trim="asignatura_id">
                                    <option v-bind:value="0" selected>Seleccione una asignatura</option>
                                    <option v-for="asignatura in array_asignatura" :key="asignatura.id" v-bind:value="asignatura.id" >
                                        {{ asignatura.nombre_asignatura }}
                                    </option>
                                </select>
                                <select v-show="asignatura_id > 0" v-model.trim="lapso" class="form-control  col-md-3" v-on:change="listarNota(1)">
                                    <option v-bind:value="0" selected>Seleccione un lapso</option>
                                    <option v-bind:value="1">
                                        Primer lapso
                                    </option>
                                    <option v-bind:value="2">
                                        Segundo lapso
                                    </option>
                                    <option v-bind:value="3">
                                        Tercer lapso
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 text-left">
                            <button v-on:click="mostrarNota()" type="button" class="btn btn-secondary">
                                <i class="icon-plus"></i>&nbsp;Nueva nota
                            </button>
                        </div>
                        <div class="col-md-2 text-left">
                            <button v-show="seccion_id > 0" v-on:click="aprobadosPdf()" type="button" class="btn btn-success btn-secondary">
                                <i class="icon-book-open"></i>&nbsp;Alumnos aprobados
                            </button>
                        </div>
                        <div class="col-md-2 text-left">
                            <button v-show="seccion_id > 0" v-on:click="notasSeccionPdf()" type="button" class="btn btn-warning btn-secondary">
                                <i class="icon-book-open"></i>&nbsp;Notas de la seccion
                            </button>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <tr>
                                    <th colspan="7">
                                        <p class="h3 text-center">Listado de notas</p>
                                    </th>
                                </tr>
                                <th>Opciones</th>
                                <th>Cédula</th>
                                <th>Apellido</th>
                                <th>Nombre</th>
                                <th>Asignatura</th>
                                <th>Lapso</th>
                                <th>Nota</th>
                            </tr>
                        </thead>
                        <tbody v-if="array_nota.length">
                            <tr v-for="nota in array_nota" :key="nota.id">
                                <td>
                                    <button v-on:click="abrirModal('nota', 'actualizar', nota)" type="button" class="btn btn-warning btn-sm">
                                        <i class="icon-pencil"></i>
                                    </button>
                                    <!-- <button type="button" class="btn btn-success btn-sm">
                                        <i class="icon-eye"></i>
                                    </button> -->
                                    <button v-on:click="cargarPdf(nota.seccion_id, nota.alumno_id)" type="button" class="btn btn-info btn-sm">
                                        <i class="icon-book-open"></i> Notas del alumno
                                    </button>
                                </td>
                                <td v-text="nota.cedula"></td>
                                <td v-text="nota.apellido"></td>
                                <td v-text="nota.nombre"></td>
                                <td v-text="nota.nombre_asignatura"></td>
                                <td v-text="nota.lapso"></td>
                                <td v-text="nota.nota"></td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="7">
                                    <p class="h2 text-center">NO hay notas registradas</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a href="#" class="page-link" v-on:click.prevent="cambiarPagina(pagination.current_page - 1)">
                                    Pre
                                </a>
                            </li>
                            <li class="page-item" v-for="page in pages_number" :key="page" v-bind:class="[page == is_actived ? 'active' : '']">
                                <a href="#" class="page-link" v-on:click.prevent="cambiarPagina(page)" v-text="page">
                                </a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a href="#" class="page-link" v-on:click.prevent="cambiarPagina(pagination.current_page + 1)">
                                    Next
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </template>
            <template v-else-if="listado == 0">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model.trim="periodo_id" v-on:change="listarSeccion()">
                                    <option v-bind:value="0" selected>Seleccione un período</option>
                                    <option v-for="periodo in array_periodo" :key="periodo.id" v-bind:value="periodo.id" >
                                        {{ periodo.periodo_inicio +'-'+ periodo.periodo_fin }}
                                    </option>
                                </select>
                                <select v-show="periodo_id > 0" class="form-control col-md-3" v-model.trim="seccion_id" v-on:change="listarAsignatura()">
                                    <option v-bind:value="0" selected>Seleccione una sección</option>
                                    <option v-for="seccion in array_seccion" :key="seccion.id" v-bind:value="seccion.id" >
                                        {{ 'Año: ' + seccion.ano + ' - ' + seccion.nombre_seccion  }}
                                    </option>
                                </select>
                                <select v-show="seccion_id > 0" class="form-control col-md-3" v-model.trim="asignatura_id">
                                    <option v-bind:value="0" selected>Seleccione una asignatura</option>
                                    <option v-for="asignatura in array_asignatura" :key="asignatura.id" v-bind:value="asignatura.id" >
                                        {{ asignatura.nombre_asignatura }}
                                    </option>
                                </select>
                                <select v-show="asignatura_id > 0" v-model.trim="lapso" class="form-control  col-md-3">
                                    <option v-bind:value="0" selected>Seleccione un lapso</option>
                                    <option v-bind:value="1">
                                        Primer lapso
                                    </option>
                                    <option v-bind:value="2">
                                        Segundo lapso
                                    </option>
                                    <option v-bind:value="3">
                                        Tercer lapso
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div v-show="error_nota" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in error_msj_nota" :key="error" v-text="error">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Alumno <span style="color:red;" v-show="alumno_id == 0">(*Seleccione)</span></label>
                                <div class="form-inline">
                                    <input type="text" class="form-control" v-model="cedula" v-on:keyup.enter="buscarAlumno()" placeholder="Ingrese cedula del alumno">
                                    <button v-on:click="buscarAlumno()" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                    <input type="text" readonly class="form-control" v-model="alumno">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Nota <span style="color:red;" v-show="nota == 0">(*Ingrese)</span></label>
                                <input type="number" value="0" class="form-control" v-model="nota">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <button v-on:click="agregarAlumno()" class="btn btn-success form-control btnagregar">
                                    <i class="icon-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="table-responsive col-md-12">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th colspan="4">
                                            <p class="h3 text-center">Notas ingresadas</p>
                                        </th>
                                    </tr>
                                    <th>Opciones</th>
                                    <th>Cédula</th>
                                    <th>Alumno</th>
                                    <th>Nota</th>
                                </thead>
                                <tbody v-if="array_notas_ingresadas.length">
                                    <tr v-for="(detalle, index) in array_notas_ingresadas" :key="detalle.id">
                                        <td>
                                            <button @click="eliminarAlumno(index)" type="button" class="btn btn-danger btn-sm">
                                                <i class="icon-close"></i>
                                            </button>
                                        </td>
                                        <td v-text="detalle.cedula"></td>
                                        <td v-text="detalle.alumno"></td>
                                        <td>
                                            <input v-model="detalle.nota" type="number" class="form-control">
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="4">
                                            <p class="h2 text-center">NO hay notas ingresadas</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <button type="button" v-on:click="listado = 1" class="btn btn-secondary">Cerrar</button>
                            <button type="button" class="btn btn-primary" v-on:click="registrarNotas()">Registrar Notas</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <!-- Fin ejemplo de tabla Listado -->
    </div>
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
                            <div class="col-md-3">
                            </div>
                            <label class="col-md-3 form-control-label" for="email-input">Nota</label>
                            <div class="col-md-3">
                                <input type="number" v-model.trim="nota" class="form-control" placeholder="Nota">
                            </div>
                        </div>
                        <div v-show="error_nota" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in error_msj_nota" :key="error" v-text="error"></div>
                            </div>
                        </div> 
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" v-on:click="cerrarModal()">Cerrar</button>
                    <button v-on:click="actualizarNota()" type="button" class="btn btn-primary">Actualizar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    </main>
</template>

<script>
    export default {
        data (){
            return {
                periodo_id : 0,
                seccion_id : 0,
                alumno_id : 0,
                asignatura_id : 0,
                nota_id : 0,
                lapso : 0,
                nota : 0,
                cedula : 0,
                alumno : '',
                array_periodo : [],
                array_seccion : [],
                array_alumno : [],
                array_asignatura : [],
                array_nota : [],
                array_notas_ingresadas : [],
                listado: 1, //Si visualizo el estado
                modal : 0,
                titulo_modal : '',
                tipo_accion : 0,
                error_nota : 0,
                error_msj_nota : 0,
                pagination : {
                    'total' : 0,
                    'current_page' :0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0
                },
                offset : 3
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
            listarNota (page){
                let me = this;
                var url = '/nota?page=' + page + '&seccion_id=' + this.seccion_id + '&asignatura_id=' + this.asignatura_id + '&lapso=' + this.lapso;

                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.array_nota = respuesta.notas.data;
                    me.pagination = respuesta.pagination;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            cargarPdf (seccion_id, alumno_id) {
                window.open('http://127.0.0.1:8000/nota/pdf/alumno?seccion_id='+seccion_id+'&alumno_id='+alumno_id, '_blank');
            },
            aprobadosPdf () {
                window.open('http://127.0.0.1:8000/nota/pdf/aprobados?seccion_id='+this.seccion_id, '_blank');
            },
            notasSeccionPdf () {
                window.open('http://127.0.0.1:8000/seccion/pdf/notas?seccion_id='+this.seccion_id, '_blank');
            },
            listarPeriodo (){
                let me = this;
                var url = '/periodo/listar-periodos';

                axios.get(url).then(function (response) {
                    me.array_periodo = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            listarSeccion (){
                let me = this;
                var url = '/seccion/listar-seccion-alumno?periodo_id=' + this.periodo_id;

                axios.get(url).then(function (response) {
                    me.array_seccion = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            listarAsignatura (){
                let me = this;
                var url = '/asignatura/listar-asignatura?seccion_id=' + this.seccion_id;

                axios.get(url).then(function (response) {
                    me.array_asignatura = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPagina (page){
                let me = this;
                //actualiza la pagina actual
                me.pagination.current_page = page;
                //envia la peticion para visualizar la data de esa pagina
                me.listarNota(page);
            },
            buscarAlumno (){
                let me = this;
                var url = '/alumno/buscar-alumno?cedula=' + me.cedula + '&seccion_id=' + me.seccion_id + '&lapso=' +me.lapso + '&asignatura_id=' +me.asignatura_id;

                axios.get(url).then(function (response) {

                    if (response.data == 1) {
                        swal(
                          'Error',
                          'El alumno ya tiene una nota de ese lapso en esa asignatura',
                          'error'
                        )
                        me.alumno= 'Ya tiene nota';
                        me.alumno_id = 0;
                        return;
                    }

                    me.array_alumno = response.data;

                    if (me.array_alumno.length > 0)
                    {
                        me.alumno = me.array_alumno[0]['nombre'] + ' ' + me.array_alumno[0]['apellido'];
                        me.cedula = me.array_alumno[0]['cedula'];
                        me.alumno_id = me.array_alumno[0]['id'];

                    } else{

                        me.alumno= 'No existe el alumno en esta sección';
                        me.alumno_id = 0;

                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            registrarNotas (){
                
                if ( this.validarNota() ) {
                    return;
                }

                let me = this;

                axios.post('/nota/registrar', {

                    'asignatura_id' : this.asignatura_id,
                    'data': this.array_notas_ingresadas

                }).then(function (){

                    me.listado = 1;
                    me.listarNota(1);
                    swal(
                      'Registradas',
                      'Las notas han sido registradas',
                      'success'
                    )
                    me.array_notas_ingresadas = [];
                })
                .catch(function (){
                    console.log(error);
               });
           },
            actualizarNota (){
                
                if ( this.validarNotaActualizada() ) {
                    return;
                }

                let me = this;

                axios.put('/nota/actualizar', {

                    'nota_id' : this.nota_id,
                    'nota' : this.nota

                }).then(function (){

                    me.cerrarModal();
                    me.listarNota(1);
                    swal(
                      'Actualizada',
                      'La nota ha sido actualizada',
                      'success'
                    )
                })
                .catch(function (){
                    console.log(error);
               });
           },
            validarNota (){
                let me = this;
                this.error_nota = 0;
                this.error_msj_nota =[];

                me.array_notas_ingresadas.map( function (x) {
                    if (x.nota <= 0 || x.nota > 20) {
                        me.error_msj_nota.push( 'Nota debe ser mayor a cero y menor o igual a 20' );
                    }
                } );

                if ( !this.periodo_id ) this.error_msj_nota.push('Seleccione un período');

                if ( !this.seccion_id ) this.error_msj_nota.push('Seleccione una sección');

                if ( !this.asignatura_id ) this.error_msj_nota.push('Seleccione una asignatura');

                if ( !this.lapso ) this.error_msj_nota.push('Seleccione un lapso');

                if ( this.array_notas_ingresadas <= 0 ) this.error_msj_nota.push('Ingrese notas');

                if (this.error_msj_nota.length) this.error_nota = 1;

                return this.error_nota;
            },
            validarNotaActualizada (){
                this.error_nota = 0;
                this.error_msj_nota =[];

                if ( !this.nota ) this.error_msj_nota.push('Ingrese una nota');

                if ( this.nota <= 0 || this.nota > 20 ) this.error_msj_nota.push('Nota debe ser mayor a cero y menor o igual a 20');

                if (this.error_msj_nota.length) this.error_nota = 1;

                return this.error_nota;
            },
            encuentra (alumno_id){
                var sw = false;

                for (var i = 0; i < this.array_notas_ingresadas.length; i++) {
                    
                    if (this.array_notas_ingresadas[i].alumno_id == alumno_id) {
                        
                        sw = true;

                    }

                }

                return sw;
            },
            agregarAlumno (){
                let me = this;

                if (me.alumno_id <= 0 || me.nota <= 0 || me.nota > 20)
                {
                    swal({
                        type: 'error',
                        title: 'Error...',
                        text: 'Ingrese el alumno, y una nota entre 1 y 20.',
                    })
                }
                else {

                    if ( me.encuentra(me.alumno_id) ) {
                        swal({
                            type: 'error',
                            title: 'Error...',
                            text: 'Ese alumno ya se encuentra agregado',
                        })
                    }
                    else{

                        me.array_notas_ingresadas.push({
                            alumno_id : me.alumno_id,
                            cedula : me.cedula,
                            alumno : me.alumno,
                            nota : me.nota,
                            lapso : me.lapso
                        });

                        me.alumno_id = 0;
                        me.cedula = 0;
                        me.alumno = '';
                        me.nota = 0;
                        
                    }
                }
            },
            eliminarAlumno (index){
                let me = this;
                me.array_notas_ingresadas.splice(index, 1);
            },
            mostrarNota (){
                let me = this;
                this.listado = 0;

                me.periodo_id = 0;
                me.seccion_id = 0;
                me.alumno_id = 0;
                me.asignatura_id = 0;
                me.lapso = 0;
                me.array_notas_ingresadas = [];
            },
            cerrarModal (){
                this.modal = 0;
                this.titulo_modal = '';
                this.nota_id = 0;
                this.nota = 0;
            },
            abrirModal (modelo, accion, data = []){
                switch (modelo){
                    case "nota":
                    {
                        switch (accion){
                            case "actualizar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Actualizar nota';
                                this.nota_id = data['id'];
                                this.nota = data['nota'];
                                this.lapso = data['lapso'];
                                break;
                            }
                        }
                    }
                }
            }
        },
        mounted() {
            this.listarPeriodo();
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
    @media (min-width: 600px) {
        .btnagregar {
            margin-top: 2rem;
        }
    }
</style>