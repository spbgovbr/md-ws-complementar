<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 13/10/2011 - criado por mga
*
*/

require_once dirname(__FILE__).'/../../../../SEI.php';

class WSCguRetornoListarAndamentosDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return null;
  }

  public function montar() {
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DBL, 'IdProcedimento');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'ProcedimentoFormatado');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdTipoProcedimento');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NomeTipoProcedimento');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjAtividadeDTO');
  }
}
?>