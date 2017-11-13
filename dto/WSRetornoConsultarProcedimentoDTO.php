<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 13/10/2011 - criado por mga
*
*/

require_once dirname(__FILE__).'/../../../../SEI.php';

class WSRetornoConsultarProcedimentoDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return null;
  }

  public function montar() {
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DBL, 'IdProcedimento');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'ProcedimentoFormatado');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdTipoProcedimento');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NomeTipoProcedimento');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'Especificacao');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DTA, 'Autuacao');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'LinkAcesso');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ, 'AtividadeDTOGeracao');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ, 'AtividadeDTOConclusao');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ, 'AtividadeDTOUltimoAndamento');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjAtividadeDTOAberto');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjRelProtocoloAssuntoDTO');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjObservacaoDTO');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjParticipanteDTOInteressados');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjRelProtocoloProtocoloDTORelacionados');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjRelProtocoloProtocoloDTOAnexados');
  }
}
?>