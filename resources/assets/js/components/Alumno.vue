<template>
    <main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <div class="col-md-12">
                    <p class="h1 text-center">Alumnos</p>
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
                              <option value="alumnos.cedula">Cédula</option>
                              <option value="secciones.ano">año</option>
                              <option value="alumnos.nombre">Nombre</option>
                              <option value="alumnos.apellido">Apellido</option>
                              <option value="secciones.nombre_seccion">Sección</option>
                              <option value="alumnos.email">email</option>
                              <option value="alumnos.telefono">Teléfono</option>
                              <option value="periodos.periodo_inicio">Período inicio</option>
                              <option value="periodos.periodo_fin">Período fín</option>
                            </select>
                            <input onkeypress="return soloLetrasAcentosNumeros(event)" v-on:keyup.enter="listarAlumno(1, buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
                            <button v-on:click="listarAlumno(1, buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            <button v-on:click="limpiarBuscar()" type="submit" class="btn btn-primary">
                                <i class="icon-trash"></i> Limpiar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <button v-on:click="abrirModal('alumno', 'registrar')" type="button" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo Alumno
                        </button>
                    </div>
                </div>
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th colspan="11">
                                <p class="h3 text-center">Listado de alumnos</p>
                            </th>
                        </tr>
                        <tr>
                            <th>Opciones</th>
                            <th>Período</th>
                            <th>Año</th>
                            <th>Sección</th>
                            <th>Cédula</th>
                            <th>Apellido</th>
                            <th>Nombre</th>
                            <th>email</th>
                            <th>teléfono</th>
                            <th>Dirección</th>
                            <th>Condición</th>
                        </tr>
                    </thead>
                    <tbody v-if="array_alumno.length">
                        <tr v-for="alumno in array_alumno" :key="alumno.id">
                            <td>
                                <button v-on:click="abrirModal('alumno', 'actualizar', alumno)" type="button" class="btn btn-warning btn-sm">
                                  <i class="icon-pencil"></i>
                                </button> &nbsp;
                                <template v-if="alumno.condicion">
                                    <button type="button" class="btn btn-danger btn-sm" v-on:click="desactivarAlumno(alumno.id)">
                                        <i class="icon-trash"></i>
                                    </button> &nbsp;
                                </template>
                                <template v-else="alumno.condicion">
                                    <button type="button" class="btn btn-info btn-sm" v-on:click="activarAlumno(alumno.id)">
                                        <i class="icon-check"></i>
                                    </button> &nbsp;
                                </template>
                                <button type="button" class="btn btn-info btn-sm" v-on:click="actaPdf(alumno.id)">
                                    <i class="icon-book-open"></i>
                                </button> &nbsp;
                            </td>
                            <td v-text="alumno.periodo_inicio + '-' + alumno.periodo_fin"></td>
                            <td v-text="alumno.ano"></td>
                            <td v-text="alumno.nombre_seccion"></td>
                            <td v-text="alumno.tipo_documento + '-' + alumno.cedula"></td>
                            <td v-text="alumno.apellido"></td>
                            <td v-text="alumno.nombre"></td>
                            <td v-text="alumno.email"></td>
                            <td>{{ numeroAlumno(alumno) }}</td>
                            <td>{{ direccionAlumno(alumno) }}</td>
                            <td>
                                <div v-if="alumno.condicion">
                                    <span class="badge badge-success">Activó</span>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Desactivadó</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="11">
                                <p class="h2 text-center">NO hay alumnos registrados</p>
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
                            <label class="col-md-3 form-control-label" for="text-input">
                                Período <span style="color:red;" v-show="periodo_id == 0">(*Seleccione)</span>
                            </label>
                            <div class="col-md-3">
                                <select v-model.trim="periodo_id" v-on:change="listarSeccion()">
                                    <option v-bind:value="0" selected>Seleccione un período</option>
                                    <option v-for="periodo in array_periodo" :key="periodo.id" v-bind:value="periodo.id" >
                                        {{ periodo.periodo_inicio +'-'+ periodo.periodo_fin }}
                                    </option>
                                </select>
                            </div>
                            <label class="col-md-3 form-control-label" for="text-input">
                                Sección <span style="color:red;" v-show="seccion_id == 0">(*Seleccione)</span>
                            </label>
                            <div class="col-md-3">
                                <select v-model.trim="seccion_id">
                                    <option v-for="seccion in array_seccion" :key="seccion.id" v-bind:value="seccion.id" >
                                        {{ 'Año: ' + seccion.ano + ' - ' + seccion.nombre_seccion  }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">
                                Cédula <span style="color:red;" v-show="cedula.length == 0">(*Ingresé)</span>
                            </label>
                            <div class="col-md-2">
                                <select v-model.trim="tipo_documento">
                                    <option v-for="documento in array_tipo_doc" :key="documento.id" v-bind:value="documento.id">
                                        {{ documento.doc }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <input onkeypress="return soloNumeros(event)" type="text" v-model.trim="cedula" class="form-control" placeholder="Cedula">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">
                                Nombre <span style="color:red;" v-show="nombre.length == 0">(*Ingresé)</span>
                            </label>
                            <div class="col-md-9">
                                <input v-on:keyup="primeraLetraM()" onkeypress="return soloLetrasConAcentos(event)" type="text" v-model.trim="nombre" class="form-control" placeholder="Nombre">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">
                            Apellido <span style="color:red;" v-show="apellido.length == 0">(*Ingresé)</span>
                            </label>
                            <div class="col-md-9">
                                <input v-on:keyup="primeraLetraM()" onkeypress="return soloLetrasConAcentos(event)" type="text" v-model.trim="apellido" class="form-control" placeholder="Apellido">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Teléfono</label>
                            <div class="col-md-2">
                                <select v-model.trim="cod_telefono">
                                    <option v-for="cod in array_cod_telefono" :key="cod.id" v-bind:value="cod" >
                                        {{ cod }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <input onkeypress="return soloNumeros(event)" type="text" v-model.trim="telefono" class="form-control" placeholder="Telefono">
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
                tipo_documento : 'V',
                cedula : 0,
                cod_telefono : '',
                telefono : '',
                email : '',
                dir_ciudad : '',
                dir_avenida : '',
                dir_calle : '',
                dir_casa : '',
                array_periodo : [],
                array_seccion : [],
                array_alumno : [],
                array_tipo_doc : [
                    {
                        'id' : 'V',
                        'doc' : 'V-'
                    },
                    {
                        'id' : 'E',
                        'doc' : 'E-'
                    },
                ],
                array_cod_telefono : ['0212', '0412', '0414', '0424', '0416', '0426'],
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
                criterio : 'alumnos.cedula',
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
            numeroAlumno (alumno) {
                var cod = '';
                var telefono = '';

                if (alumno.cod_telefono != null) cod = alumno.cod_telefono+'-';
                if (alumno.telefono != null) telefono = alumno.telefono;

                return cod+telefono;
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
            actaPdf (id) {
                window.open('http://127.0.0.1:8000/alumno/pdf/citacion?alumno_id=' + id, '_blank');
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
                    'tipo_documento': this.tipo_documento,
                    'cedula': this.cedula,
                    'cod_telefono': this.cod_telefono,
                    'telefono': this.telefono,
                    'email': this.email,
                    'dir_ciudad': this.dir_ciudad,
                    'dir_avenida': this.dir_avenida,
                    'dir_calle': this.dir_calle,
                    'dir_casa': this.dir_casa
                }).then(function (response){
                    me.cerrarModal();
                    swal(
                      'Registrado',
                      'El alumno ha sido registrado',
                      'success'
                    )
                    me.listarAlumno(1, '', 'alumnos.cedula');
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
                    'tipo_documento': this.tipo_documento,
                    'cedula': this.cedula,
                    'cod_telefono': this.cod_telefono,
                    'telefono': this.telefono,
                    'email': this.email,
                    'dir_ciudad': this.dir_ciudad,
                    'dir_avenida': this.dir_avenida,
                    'dir_calle': this.dir_calle,
                    'dir_casa': this.dir_casa,
                    'id': this.alumno_id
                }).then(function (){
                    me.cerrarModal();
                    swal(
                      'Actualizado',
                      'El alumno ha sido actualizado',
                      'success'
                    )
                    me.listarAlumno(1, '', 'alumnos.cedula');
                })
                .catch(function (error){
                    console.log(error);
                });
            },
            desactivarAlumno (alumno_id){
                swal({
                  title: '¿Estas seguro de desactivar a éste alumno?',
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
                        me.listarAlumno(1, '', 'alumnos.cedula');
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
                  title: '¿Estas seguro de activar a éste alumno?',
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
                        me.listarAlumno(1, '', 'alumnos.cedula');
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
            primeraLetraM () {
                this.nombre = this.nombre.charAt(0).toUpperCase() + this.nombre.slice(1);
                this.apellido = this.apellido.charAt(0).toUpperCase() + this.apellido.slice(1);
            },
            validarAlumno (){
                this.error_alumno = 0;
                this.error_msj_alum =[];

                if (!this.seccion_id) this.error_msj_alum.push('Seleccione la seccion');

                this.cedula = parseInt(this.cedula);

                if (!this.tipo_documento) this.error_msj_alum.push('Seleccione si es Venezolano o Extranjero');

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
                this.tipo_documento = 'V';
                this.cedula = 0;
                this.cod_telefono = '';
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
                                this.tipo_documento = '';
                                this.cedula = 0;
                                this.cod_telefono = '';
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
                                this.tipo_documento = data['tipo_documento'];
                                this.cedula = data['cedula'];
                                this.cod_telefono = data['cod_telefono'];
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
                this.criterio = 'alumnos.cedula';
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