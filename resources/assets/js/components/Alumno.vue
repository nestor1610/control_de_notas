<template>
    <main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Asignaturas
                <button v-on:click="abrirModal('alumno', 'registrar')" type="button" class="btn btn-secondary">
                    <i class="icon-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <select class="form-control col-md-3" v-model="criterio">
                              <option value="periodos.periodo_inicio">Periodo inicio</option>
                              <option value="periodos.periodo_fin">Periodo fin</option>
                              <option value="secciones.ano">ano</option>
                              <option value="secciones.nombre_seccion">Seccion</option>
                              <option value="alumnos.nombre">Nombre</option>
                              <option value="alumnos.apellido">Apellido</option>
                              <option value="alumnos.cedula">Cedula</option>
                              <option value="alumnos.email">email</option>
                              <option value="alumnos.telefono">Telefono</option>
                            </select>
                            <input v-on:keyup.enter="listarAlumno(1, buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
                            <button v-on:click="listarAlumno(1, buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
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
                            <th>Periodo</th>
                            <th>Año</th>
                            <th>Seccion</th>
                            <th>Cedula</th>
                            <th>Apellido</th>
                            <th>Nombre</th>
                            <th>email</th>
                            <th>telefono</th>
                            <th>Direccion</th>
                            <th>Condicion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="alumno in array_alumno" :key="alumno.id">
                            <td>
                                <button v-on:click="abrirModal('alumno', 'actualizar', alumno)" type="button" class="btn btn-warning btn-sm">
                                  <i class="icon-pencil"></i>
                                </button> &nbsp;
                                <template v-if="alumno.condicion">
                                    <button type="button" class="btn btn-danger btn-sm" v-on:click="desactivarAlumno(alumno.id)">
                                        <i class="icon-trash"></i>
                                    </button>
                                </template>
                                <template v-else="alumno.condicion">
                                    <button type="button" class="btn btn-info btn-sm" v-on:click="activarAlumno(alumno.id)">
                                        <i class="icon-check"></i>
                                    </button>
                                </template>
                            </td>
                            <td v-text="alumno.periodo_inicio + '-' + alumno.periodo_fin"></td>
                            <td v-text="alumno.ano"></td>
                            <td v-text="alumno.nombre_seccion"></td>
                            <td v-text="alumno.cedula"></td>
                            <td v-text="alumno.apellido"></td>
                            <td v-text="alumno.nombre"></td>
                            <td v-text="alumno.email"></td>
                            <td v-text="alumno.telefono"></td>
                            <td>{{ direccionAlumno(alumno) }}</td>
                            <td>
                                <div v-if="alumno.condicion">
                                    <span class="badge badge-success">Activo</span>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Desactivado</span>
                                </div>
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
                            <label class="col-md-3 form-control-label" for="text-input">Periodo</label>
                            <div class="col-md-3">
                                <select v-model.trim="periodo_id" v-on:change="listarSeccion()">
                                    <option v-bind:value="0" selected>Seleccione un periodo</option>
                                    <option v-for="periodo in array_periodo" :key="periodo.id" v-bind:value="periodo.id" >
                                        {{ periodo.periodo_inicio +'-'+ periodo.periodo_fin }}
                                    </option>
                                </select>
                            </div>
                            <label class="col-md-3 form-control-label" for="text-input">Seccion</label>
                            <div class="col-md-3">
                                <select v-model.trim="seccion_id">
                                    <option v-for="seccion in array_seccion" :key="seccion.id" v-bind:value="seccion.id" >
                                        {{ 'Año: ' + seccion.ano + ' - ' + seccion.nombre_seccion  }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Cedula</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="cedula" class="form-control" placeholder="Cedula">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Nombre</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="nombre" class="form-control" placeholder="Nombre">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Apellido</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="apellido" class="form-control" placeholder="Apellido">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Telefono</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="telefono" class="form-control" placeholder="Telefono">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Email</label>
                            <div class="col-md-9">
                                <input type="email" v-model.trim="email" class="form-control" placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Avenida</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="dir_avenida" class="form-control" placeholder="Avenida">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Ciudad</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="dir_ciudad" class="form-control" placeholder="Ciudad">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Calle</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="dir_calle" class="form-control" placeholder="Calle">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Casa</label>
                            <div class="col-md-9">
                                <input type="text" v-model.trim="dir_casa" class="form-control" placeholder="Casa">
                            </div>
                        </div>
                        <div v-show="error_alumno" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in error_msj_alum" :key="error" v-text="error"></div>
                            </div>
                        </div> 
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" v-on:click="cerrarModal()">Cerrar</button>
                    <button v-if="tipo_accion == 1" v-on:click="registrarAlumno()" type="button" class="btn btn-primary">Guardar</button>
                    <button v-if="tipo_accion == 2" v-on:click="actualizarAlumno()" type="button" class="btn btn-primary">Actualizar</button>
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
                nombre : '',
                apellido : '',
                cedula : 0,
                telefono : '',
                email : '',
                dir_ciudad : '',
                dir_avenida : '',
                dir_calle : '',
                dir_casa : '',
                array_periodo : [],
                array_seccion : [],
                array_alumno : [],
                modal : 0,
                titulo_modal : '',
                tipo_accion : 0,
                error_alumno : 0,
                error_msj_alum : 0,
                pagination : {
                    'total' : 0,
                    'current_page' :0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0
                },
                offset : 3,
                criterio : 'periodos.periodo_inicio',
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
            listarAlumno (page, buscar, criterio){
                let me = this;
                var url = '/alumno?page=' + page + '&buscar='+buscar+'&criterio='+criterio;

                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.array_alumno = respuesta.alumnos.data;
                    me.pagination = respuesta.pagination;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            direccionAlumno (alumno){
                var avenida = '';
                var ciudad = '';
                var calle = '';
                var casa = '';

                if (alumno.dir_avenida != null) avenida = alumno.dir_avenida;
                if (alumno.dir_ciudad != null) ciudad = alumno.dir_ciudad;
                if (alumno.dir_calle != null) calle = alumno.dir_calle;
                if (alumno.dir_casa != null) casa = alumno.dir_casa;

                return 'Ciudad: ' + ciudad + ' Avenida: ' + avenida + ' Calle: ' + calle + ' Casa: ' + casa;

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
            cambiarPagina (page, buscar, criterio){
                let me = this;
                //actualiza la pagina actual
                me.pagination.current_page = page;
                //envia la peticion para visualizar la data de esa pagina
                me.listarAlumno(page, buscar, criterio);
            },
            registrarAlumno (){
                
                if (this.validarAlumno()) {
                    return;
                }

                let me = this;

                axios.post('/alumno/registrar', {
                    'seccion_id': this.seccion_id,
                    'nombre': this.nombre,
                    'apellido': this.apellido,
                    'cedula': this.cedula,
                    'telefono': this.telefono,
                    'email': this.email,
                    'dir_ciudad': this.dir_ciudad,
                    'dir_avenida': this.dir_avenida,
                    'dir_calle': this.dir_calle,
                    'dir_casa': this.dir_casa
                }).then(function (response){
                    me.cerrarModal();
                    me.listarAlumno(1, '', 'periodos.periodo_inicio');
                })
                .catch(function (error){
                    console.log(error);
                });
            },
            actualizarAlumno (){

                if (this.validarAlumno()){
                    return;
                }

                let me = this;

                axios.put('/alumno/actualizar', {
                    'seccion_id': this.seccion_id,
                    'nombre': this.nombre,
                    'apellido': this.apellido,
                    'cedula': this.cedula,
                    'telefono': this.telefono,
                    'email': this.email,
                    'dir_ciudad': this.dir_ciudad,
                    'dir_avenida': this.dir_avenida,
                    'dir_calle': this.dir_calle,
                    'dir_casa': this.dir_casa,
                    'id': this.alumno_id
                }).then(function (){
                    me.cerrarModal();
                    me.listarAlumno(1, '', 'periodos.periodo_inicio');
                })
                .catch(function (error){
                    console.log(error);
                });
            },
            desactivarAlumno (alumno_id){
                swal({
                  title: '¿Estas seguro de desactivar a este alumno?',
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

                    axios.put('/alumno/desactivar', {
                        'id': alumno_id
                    }).then(function (response){
                        me.listarAlumno(1, '', 'periodos.periodo_inicio');
                        swal(
                          'Desactivado',
                          'El alumno ha sido desactivado',
                          'success'
                        )
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
            activarAlumno (alumno_id){
                swal({
                  title: '¿Estas seguro de activar a este alumno?',
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

                    axios.put('/alumno/activar', {
                        'id': alumno_id
                    }).then(function (response){
                        me.listarAlumno(1, '', 'periodos.periodo_inicio');
                        swal(
                          'Activado',
                          'El alumno ha sido activado',
                          'success'
                        )
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
            validarAlumno (){
                this.error_alumno = 0;
                this.error_msj_alum =[];

                if (!this.seccion_id) this.error_msj_alum.push('Seleccione la seccion');

                this.cedula = parseInt(this.cedula);

                if (!this.cedula) this.error_msj_alum.push('La cedula del alumno no debe estar vacia');

                if (this.cedula < 17999999) this.error_msj_alum.push('La cedula del alumno no es valida');

                if (!this.nombre) this.error_msj_alum.push('El nombre del alumno no debe estar vacio');

                if ( this.nombre.length > 40 ) this.error_msj_alum.push('El nombre del alumno no debe ser mayor de 40 caracteres');

                if (!this.apellido) this.error_msj_alum.push('El apellido del alumno no debe estar vacio');

                if ( this.apellido.length > 40 ) this.error_msj_alum.push('El apellido del alumno no debe ser mayor de 40 caracteres');

                if (this.error_msj_alum.length) this.error_alumno = 1;

                return this.error_alumno;
            },
            cerrarModal (){
                this.modal = 0;
                this.titulo_modal = '';
                this.periodo_id = 0;
                this.seccion_id = 0;
                this.alumno_id = 0;
                this.nombre = '';
                this.apellido = '';
                this.cedula = 0;
                this.telefono = '';
                this.email = '';
                this.dir_ciudad = '';
                this.dir_avenida = '';
                this.dir_calle = '';
                this.dir_casa = '';
                this.tipo_accion = 0;
            },
            abrirModal (modelo, accion, data = []){
                switch (modelo){
                    case "alumno":
                    {
                        switch (accion){
                            case "registrar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Registrar alumno',
                                this.alumno_id = 0;
                                this.nombre = '';
                                this.apellido = '';
                                this.cedula = 0;
                                this.telefono = '';
                                this.email = '';
                                this.dir_ciudad = '';
                                this.dir_avenida = '';
                                this.dir_calle = '';
                                this.dir_casa = '';
                                this.tipo_accion = 1;
                                break;
                            }
                            case "actualizar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Actualizar alumno';
                                this.tipo_accion = 2;
                                this.alumno_id = data['id'];
                                this.periodo_id = data['periodo_id'];
                                this.listarSeccion();
                                this.seccion_id = data['seccion_id'];
                                this.nombre = data['nombre'];
                                this.apellido = data['apellido'];
                                this.cedula = data['cedula'];
                                this.telefono = data['telefono'];
                                this.email = data['email'];
                                this.dir_ciudad = data['dir_ciudad'];
                                this.dir_avenida = data['dir_avenida'];
                                this.dir_calle = data['dir_calle'];
                                this.dir_casa = data['dir_casa'];
                                break;
                            }
                        }
                    }
                }
            },
            limpiarBuscar (){
                this.buscar = '';
                this.criterio = 'periodos.periodo_inicio';
                this.listarAlumno(1, this.buscar, this.criterio);
            }
        },
        mounted() {
            this.listarAlumno(1, this.buscar, this.criterio);
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
</style>