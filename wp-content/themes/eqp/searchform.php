<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input type="text" name="s" value="" required placeholder="Búsqueda" />
    <input type="submit" class="submit" id="searchsubmit" value="OK" />
    <div id="search-options-holder">
        <input id="sContenido" name="searchType" type="radio" value="content" checked /> <label for="sContenido">Contenidos</label>
        <input id="sUsuarios" name="searchType" type="radio" value="users" /> <label for="sUsuarios">Usuarios</label>
    </div>
</form>