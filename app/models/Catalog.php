<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Catalog extends Model
{   
    public function getServiceCatalogSP1($name)
    {
        /*
        $wsClient = new WebServiceClient();
        $respnse = $wsClient->getCatalogStepOne($name);
        $result = array();
        $icons = $this->di->get('catalog-icons');
        foreach ($respnse as $key => $value) 
        {
            $temp = (array)$value;
            $temp = (array)$temp['Name'];
            if(array_key_exists($temp['_'], $icons))
            {
                $tempIcon = $icons[$temp['_']];
            }
            else
            {
                $tempIcon = $icons['default'];
            }
            array_push($result, array('name' => $temp['_'], 'icon' => $tempIcon));
        }*/
        $result = array(
                        array(
                            'name' => 'SOA',
                            'icon' => 'fa-toggle-right',
                            'description' => 'Habilitar accesos mediante configuración de permisos, solución de problemas o recuperar datos.'
                        ),
                        array(
                            'name' => 'Sistemas TI',
                            'icon' => 'fa-toggle-right',
                            'description' => 'Habilitar accesos mediante configuración de permisos, solución de problemas o recuperar datos.'
                        ),
                        array(
                            'name' => 'Activo Fijo',
                            'icon' => 'fa-toggle-right',
                            'description' => 'Habilitar accesos mediante configuración de permisos, solución de problemas o recuperar datos.'
                        ),
                        array(
                            'name' => 'Audio y Video',
                            'icon' => 'fa-toggle-right',
                            'description' => 'Habilitar accesos mediante configuración de permisos, solución de problemas o recuperar datos.',
                        ),
                        array(
                            'name' => 'Traslado personas',
                            'icon' => 'fa-toggle-right',
                            'description' => 'Habilitar accesos mediante configuración de permisos, solución de problemas o recuperar datos.'
                        ),
                    );
        return $result;
    }
    public function getServiceCatalogSP2($name)
    {
        $result = array(
                        array(
                            'name' => 'Solucionar problema',
                            'icon' => 'fa-toggle-right',
                            'description' => 'Habilitar accesos mediante configuración de permisos, solución de problemas o recuperar datos.'
                        ),
                        array(
                            'name' => 'Actualizar Datos de Cuenta',
                            'icon' => 'fa-toggle-right',
                            'description' => 'Habilitar accesos mediante configuración de permisos, solución de problemas o recuperar datos.'
                        ),
                        array(
                            'name' => 'Solicitar',
                            'icon' => 'fa-toggle-right',
                            'description' => 'Habilitar accesos mediante configuración de permisos, solución de problemas o recuperar datos.'
                        )
                    );
        return $result;
    }
    public function gatCampos($name)
    {
         $campos = array(
                    'detinatario' => 'si',
                    'ci' => 'si',
                    'titulo' => 'si',
                    'descripcion' => 'si',
                    'desde' => 'no',
                    'impacto' =>'si',
                    'urgencia' => 'si',
                    'interrupcion' => 'si',
                    'autorizacion' => 'no',
                    'adjunto' => 'no',
                    'hasta' => 'no'
                );
         return $campos;
    }
}