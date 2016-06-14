<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness;

/**
 * Clase que contiene a las evaluaciones de ticket's cerrados
 */
class Evaluacion extends Model
{
	/**
     * @var string
     */
    public $ticket;
    /**
     * @var string
     */
    public $conforme;
    /**
     * @var integer
     */
    public $preg1;
    /**
     * @var integer
     */
    public $preg2;
    /**
     * @var integer
     */
    public $preg3;
    /**
     * @var integer
     */
    public $preg4;
    /**
     * @var integer
     */
    public $preg5;
    /**
     * @var string
     */
    public $comentario;

    public function Save($data = NULL, $whiteList = NULL)
    {
		$ep = $this->getDi()->getShared('configEvaPath');
		$path = $ep->path;

        if(empty($this->ticket) or empty($this->conforme) or empty($this->preg1)
            or empty($this->preg2) or empty($this->preg3) or empty($this->preg4)
            or empty($this->preg5))
        {
            return false;
        }
		
        $fileName = $this->ticket . ".xml";
        
		if(strlen($path) == 0 or "\\" != substr($path, -1, 1))
        {
            $path = $path . "\\";
        }
        try
        {
            if(file_exists($path . $fileName))
            {
                unlink($path . $fileName);
            }
            $file = fopen($path . $fileName, "w+");
            $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" 
                    . "<EPA>\n"
                    . "\t<TICKET>" . $this->ticket . "</TICKET>\n"
                    . "\t<CONFORME>" . $this->conforme . "</CONFORME>\n"
                    . "\t<PREG1>" .  $this->preg1 . "<PREG1>\n"
                    . "\t<PREG2>" .  $this->preg2 . "<PREG2>\n"
                    . "\t<PREG3>" .  $this->preg3 . "<PREG3>\n"
                    . "\t<PREG4>" .  $this->preg4 . "<PREG4>\n"
                    . "\t<PREG5>" .  $this->preg5 . "<PREG5>\n";
            if(empty($this->comentario))
            {
                $xml = $xml . "\t<COMENTARIOS></COMENTARIOS>\n</EPA>";
            }
            else
            {
                $xml = $xml . "\t<COMENTARIOS>\n\t\t" . $this->comentario . "\n\t</COMENTARIOS>\n"
                    . "</EPA>";
            }
                    
            fputs($file, $xml);
            fclose($file);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
}