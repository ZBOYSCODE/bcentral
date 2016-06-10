<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Catalog extends Model
{   
    public function getServiceCatalogSP1($name)
    {
        $wsClient = new WebServiceClient();
        $respnse = $wsClient->getCatalogStepOne($name);
        if($respnse == null)
        {
            return false;
        }
        $result = array();
        $icons = $this->di->get('catalog-icons');
        if(array_key_exists('Name', $respnse) == false)
        {
            foreach ($respnse as $key => $value) 
            {
                $temp = (array)$value;
                $name = (array)$temp['Name'];
                if(array_key_exists($name['_'], $icons))
                {
                    $tempIcon = $icons[$name['_']];
                }
                else
                {
                    $tempIcon = $icons['default'];
                }
                $description = (array)$temp['Description'];
                $description = $description['_'];
                if(strlen($description)>100)
                {
                    $description = substr($description, 0, 96) . '...';
                }
                array_push($result, array('name' => $name['_'], 'icon' => $tempIcon, 'description' => $description));
            }    
        }
        else
        {
            $respnse = (array)$respnse;
            $name = (array)$respnse['Name'];
            if(array_key_exists($name['_'], $icons))
            {
                $tempIcon = $icons[$name['_']];
            }
            else
            {
                $tempIcon = $icons['default'];
            }
            $description = (array)$respnse['Description'];
            $description = $description['_'];
            if(strlen($description)>100)
            {
                $description = substr($description, 0, 97) . '..';
            }
            array_push($result, array('name' => $name['_'], 'icon' => $tempIcon, 'description' => $description));
        }
        /*$result = array(
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
                    );*/
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

    public function getFields($name)
    {
        $fields = array(
                    'detinatario' => false,
                    'ci' => false,
                    'titulo' => true,
                    'descripcion' => true,
                    'desde' => false,
                    'impacto' =>false,
                    'urgencia' => false,
                    'interrupcion' => false,
                    'autorizacion' => false,
                    'adjunto' => true,
                    'hasta' => false
                );
        if($name == 'Solucionar Problema')
        {
            $fields['ci'] = true;
            $fields['impacto'] = true;
            $fields['urgencia'] = true;
            $fields['interrupcion'] = true;
            $fields['detinatario'] = true;
            return $fields;
        }
        $ws = new WebServiceClient();
        $response = $ws->getFields($name);
        $response = (array)$response['OptionDesc'];
        $response = $response['OptionDesc'];
        foreach ($response as $key => $value) 
        {
            $value = (array)$value;
            $value = $value['_'];
            if(strpos($value,'CI'))
            {
                $fields['ci'] = true;
                continue;
            }
            if(strpos($value,'esde'))
            {
                $fields['desde'] = true;
                continue;
            }
            if(strpos($value,'autoriza'))
            {
                $fields['autorizacion'] = true;
                continue;
            }
            if(strpos($value, 'asta'))
            {
                $fields['hasta'] = true;
                continue;
            }
            if(strpos($value,'estinatario'))
            {
                $fields['detinatario'] = true;
                continue;
            }
        }
        return $fields;
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