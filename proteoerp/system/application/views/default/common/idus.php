<div id='form_usr' style='vertical-align:text-top;'>
<?php 
if ( $this->secu->es_logeado() )
{
    $aa = img(array('src'=>'images/llave.png', 'height'=>14, 'alt'=>'Cambiar Clave', 'title'=>'Cambiar Clave', 'border'=>'0'));
//    echo $aa.'&nbsp;' ;
}
echo $idus; 
?>
</div>