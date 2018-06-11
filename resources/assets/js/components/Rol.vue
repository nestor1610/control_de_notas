<template>
    <main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Roles
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <select class="form-control col-md-3" v-model="criterio">
                              <option value="nombre">Nombre</option>
                              <option value="descripcion">Moreno</option>
                            </select>
                            <input v-on:keyup.enter="listarRol(buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
                            <button v-on:click="listarRol(buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            <button v-on:click="limpiarBuscar()" type="submit" class="btn btn-primary">
                                <i class="icon-trash"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripcion</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="rol in array_rol" :key="rol.id">
                            <td v-text="rol.nombre"></td>
                            <td v-text="rol.descripcion"></td>
                            <td v-text="rol.condicion"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Fin ejemplo de tabla Listado -->
    </div>
    </main>
</template>

<script>
    export default {
        data (){
            return {
                rol_id : 0,
                nombre : '',
                descripcion : '',
                array_rol : [],
                modal : 0,
                titulo_modal : '',
                tipo_accion : 0,
                criterio : 'nombre',
                buscar : ''
            }
        },
        methods : {
            listarRol (buscar, criterio){
                let me = this;
                var url = '/rol?buscar='+buscar+'&criterio='+criterio;

                axios.get(url).then(function (response) {
                    me.array_rol = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            limpiarBuscar (){
                this.buscar = '';
                this.criterio = 'nombre';
                this.listarRol(this.buscar, this.criterio);
            }
        },
        mounted() {
            this.listarRol(this.buscar, this.criterio);
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