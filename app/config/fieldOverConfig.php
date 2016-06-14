<?php
return new \Phalcon\Config(array(
 'select_dest'=>array('titulo'=>'Destinatario','descripcion'=>'Persona a la que se le entregará el servicio')
,'select_sa'=>array('titulo'=>'Servicio Afectado','descripcion'=>'Servicio Afectado')
,'select_ci'=>array('titulo'=>'Componente','descripcion'=>'Componente de Tecnología (Sistema, Hardware, Software)')
,'title'=>array('titulo'=>'Descripción Breve','descripcion'=>'Descripción Breve')
,'description'=>array('titulo'=>'Otros Antecedentes','descripcion'=>'Otros Antecedentes')
,'select_is'=>array('titulo'=>'Interrupción de Servicio','descripcion'=>'Hay Interrupción de Servicio ?')
,'select_i'=>array('titulo'=>'Impacto','descripcion'=>'Impacto')
,'select_u'=>array('titulo'=>'Urgencia','descripcion'=>'Urgencia')
,'desde'=>array('titulo'=>'Desde','descripcion'=>'Fecha Desde')
,'hasta'=>array('titulo'=>'Hasta','descripcion'=>'Fecha Hasta')
,'file'=>array('titulo'=>'Información Adicional','descripcion'=>'Fecha Información Adicional')
));