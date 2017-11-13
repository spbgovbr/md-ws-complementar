<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 13/10/2011 - criado por mga
*
*/

require_once dirname(__FILE__).'/../../../../SEI.php';

class WSCguRetornoConsultarDocumentoDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return null;
  }

  public function montar() {
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DBL, 'IdProcedimento');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'ProcedimentoFormatado');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DBL, 'IdDocumento');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'DocumentoFormatado');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'LinkAcesso');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdSerie');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NomeSerie');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'Numero');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdUnidadeGeradora');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'SiglaUnidadeGeradora');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'DescricaoUnidadeGeradora');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DTA, 'GeracaoProtocolo');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ, 'AtividadeDTOGeracao');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjAssinaturaDTO');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ, 'PublicacaoDTO');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjParticipanteDTO');
  }
}
?>