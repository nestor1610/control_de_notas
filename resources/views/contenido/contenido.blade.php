@extends('principal')

@section('contenido')

	@if( Auth::user()->rol_id == 1 )

		<template v-if="menu==0">

		</template>
		<template v-if="menu==1">
	    	<periodo></periodo>
		</template>
		<template v-if="menu==2">
	    	<seccion></seccion>
		</template>
		<template v-if="menu==3">
	    	<asignatura></asignatura>
		</template>
		<template v-if="menu==4">
	    	<alumno></alumno>
		</template>
		<template v-if="menu==5">
	    	<nota></nota>
		</template>
		<template v-if="menu==6">
	    	<user></user>
		</template>
		<template v-if="menu==7">
	    	<rol></rol>
		</template>

	@endif
	
@endsection