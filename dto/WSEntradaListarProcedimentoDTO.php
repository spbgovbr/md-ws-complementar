<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 17/07/2014 - criado por mga
*
*/

require_once dirname(__FILE__) . '/../../../../SEI.php';

class WSEntradaListarProcedimentoDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return null;
  }

  public function montar() {
    $this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ, 'ServicoDTO');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ, 'ProcedimentoDTO');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ, 'UnidadeDTO');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'Interessado');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'ClassificacaoAssunto');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DTA, 'DataInicialRegistroProcedimento');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DTA, 'DataFinalRegistroProcedimento');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'UnidadeProcedimentoAberto');
  }
}
?>