<?php
if ( $this->secu->es_logeado() )
{
    echo '<div id="form_usr">';
    $aa = img(array('src'=>'images/llave.png', 'height'=>14, 'alt'=>'Cambiar Clave', 'title'=>'Cambiar Clave', 'border'=>'0'));
} else 
    echo '<div id="form_usr">';

echo $idus; 
?>
</div>
