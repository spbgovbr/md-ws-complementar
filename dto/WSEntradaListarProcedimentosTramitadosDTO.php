<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 17/07/2014 - criado por mga
*
*/

require_once dirname(__FILE__).'/../../../../SEI.php';

class WSEntradaListarProcedimentosTramitadosDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return null;
  }

  public function montar() {
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DBL, 'IdProcedimento');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DTH, 'DataReferencia');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdUnidadePesquisa');
  }
}
?>