<?php
/**
 * CONTROLADORIA GERAL DA UNIAO
 *
 * 03/10/2017 - criado por rafael.ferreira@cgu.gov.br
 *
 */

class MdCguWsComplementarIntegracao extends SeiIntegracao
{

//    public function __construct()
//    {
//    }

    public function getNome()
    {
        return 'Módulo de Webservice de consultas complementar da CGU';
    }

    public function getVersao()
    {
        return '2.0.0';
    }

    public function getInstituicao()
    {
        return 'CGU - Controladoria Geral da União';
    }

//    public function inicializar($strVersaoSEI)
//    {
//        /*
//        if (substr($strVersaoSEI, 0, 2) != '3.'){
//          die('Módulo "'.$this->getNome().'" ('.$this->getVersao().') não é compatível com esta versão do SEI ('.$strVersaoSEI.').');
//        }
//        */
//    }

    public function processarControladorWebServices($strServico)
    {
        $strArq = null;
        switch ($strServico) {
            case 'cgu':
                $strArq = 'cgu.wsdl';
                break;
        }

        if ($strArq!=null){
            $strArq = dirname(__FILE__).'/ws/'.$strArq;
        }
        return $strArq;
    }
}

?>