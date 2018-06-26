<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li @click="menu=0" class="nav-item">
                <a class="nav-link active" href="#"><i class="icon-speedometer"></i> Escritorio</a>
            </li>
            <li class="nav-title">
                Menu
            </li>
            <li @click="menu=1" class="nav-item">
                <a class="nav-link" href="#"><i class="icon-book-open"></i>Modulo Periodos</a>
            </li>
            <li @click="menu=2" class="nav-item">
                <a class="nav-link" href="#"><i class="icon-book-open"></i>Modulo Secciones</a>
            </li>
            <li @click="menu=3" class="nav-item">
                <a class="nav-link" href="#"><i class="icon-book-open"></i>Modulo Asignaturas</a>
            </li>
            <li @click="menu=4" class="nav-item">
                <a class="nav-link" href="#"><i class="icon-book-open"></i>Modulo Alumnos</a>
            </li>
            <li @click="menu=5" class="nav-item">
                <a class="nav-link" href="#"><i class="icon-book-open"></i>Modulo Notas</a>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-people"></i>Modulo Acceso</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=6" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-user"></i> Usuarios</a>
                    </li>
                    <li @click="menu=7" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-user-following"></i> Roles</a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>