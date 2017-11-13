<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 17/07/2014 - criado por mga
*
*/

require_once dirname(__FILE__) . '/../../../../SEI.php';

class WSEntradaListarDocumentoDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return null;
  }

  public function montar() {
    $this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ, 'ServicoDTO');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ, 'DocumentoDTO');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ, 'UnidadeDTO');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdUnidadeGeradoraProtocolo');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DTA, 'DataInicialGeracaoProtocolo');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DTA, 'DataFinalGeracaoProtocolo');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'SinRetornarAndamentoGeracao');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'SinRetornarAssinaturas');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'SinRetornarPublicacao');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'SinRetornarDestinatarios');

  }
}
?>