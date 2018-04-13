<?
/**
* CONTROLADORIA GERAL DA UNIÃO
*
* 25/06/2015 - criado por Rafael Leandro Ferreira
*
*/

require_once dirname(__FILE__) . '/../../../../SEI.php';

class CguRN extends InfraRN {

  public function __construct(){
    parent::__construct();
  }
 
  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }


  protected function listarDocumentoControlado(WSEntradaListarDocumentoDTO $objWSEntradaListarDocumentoDTO){
    try{

    	$objInfraException = new InfraException(); 
    	
    	$objServicoDTO = $objWSEntradaListarDocumentoDTO->getObjServicoDTO();

        $objDocumentoDTO = $objWSEntradaListarDocumentoDTO->getObjDocumentoDTO();

    	$objUnidadeDTO = $objWSEntradaListarDocumentoDTO->getObjUnidadeDTO();

    	if (!InfraUtil::isBolSinalizadorValido($objWSEntradaListarDocumentoDTO->getStrSinRetornarAndamentoGeracao())) {
    		$objInfraException->adicionarValidacao('Sinalizador de retorno para andamento de geração inválido.');
    	}
    	 
    	if (!InfraUtil::isBolSinalizadorValido($objWSEntradaListarDocumentoDTO->getStrSinRetornarAssinaturas())) {
    		$objInfraException->adicionarValidacao('Sinalizador de retorno para assinaturas inválido.');
    	}

    	if (!InfraUtil::isBolSinalizadorValido($objWSEntradaListarDocumentoDTO->getStrSinRetornarPublicacao())) {
    		$objInfraException->adicionarValidacao('Sinalizador de retorno para publicação inválido.');
    	}

        if (!InfraUtil::isBolSinalizadorValido($objWSEntradaListarDocumentoDTO->getStrSinRetornarDestinatarios())) {
            $objInfraException->adicionarValidacao('Sinalizador de retorno para destinatários inválido.');
        }

        $dto = new DocumentoDTO();
    	$dto->retDblIdDocumento();
    	$dto->retDblIdProcedimento();
    	$dto->retStrProtocoloDocumentoFormatado();
    	$dto->retStrProtocoloProcedimentoFormatado();
    	$dto->retNumIdSerie();
    	$dto->retStrNomeSerie();
    	$dto->retStrNumero();
    	$dto->retStrStaNivelAcessoGlobalProtocolo();
    	$dto->retDtaGeracaoProtocolo();
    	$dto->retStrStaProtocoloProtocolo();
    	$dto->retStrSinBloqueado();
        $dto->retStrStaDocumento();

    	$dto->retNumIdUnidadeGeradoraProtocolo();
    	$dto->retStrSiglaUnidadeGeradoraProtocolo();
    	$dto->retStrDescricaoUnidadeGeradoraProtocolo();



        if ($objDocumentoDTO!='') {

            if ($objDocumentoDTO->getStrProtocoloDocumentoFormatado()==''
                && $objDocumentoDTO->getStrProtocoloProcedimentoFormatado() ==''
                && $objDocumentoDTO->getStrNumero()==''
                && $objDocumentoDTO->getNumIdSerie()=='') {

                $objInfraException->lancarValidacao('É necessário informar ao menos um dos parâmetros da pesquisa.');

            }

            if ($objDocumentoDTO->getStrProtocoloDocumentoFormatado()!='') {
                $dto->setStrProtocoloDocumentoFormatadoPesquisa($objDocumentoDTO->getStrProtocoloDocumentoFormatado());
            }
            if ($objDocumentoDTO->getStrProtocoloProcedimentoFormatado()!='') {
                $dto->setStrProtocoloProcedimentoFormatado($objDocumentoDTO->getStrProtocoloProcedimentoFormatado());
            }
            if ($objDocumentoDTO->getStrNumero()!='') {
                $dto->setStrNumero('%'.$objDocumentoDTO->getStrNumero().'%', InfraDTO::$OPER_LIKE);
            }
            if ($objDocumentoDTO->getNumIdSerie()!='') {
                $dto->setNumIdSerie($objDocumentoDTO->getNumIdSerie());
            }
            if ($objDocumentoDTO->getNumIdUnidadeGeradoraProtocolo()!='') {
                $dto->setNumIdUnidadeGeradoraProtocolo($objDocumentoDTO->getNumIdUnidadeGeradoraProtocolo());
            }
        }

        $dto->setStrStaProtocoloProtocolo(array(ProtocoloRN::$TP_DOCUMENTO_GERADO, ProtocoloRN::$TP_DOCUMENTO_RECEBIDO), InfraDTO::$OPER_IN);
        $dto->setStrStaNivelAcessoGlobalProtocolo(ProtocoloRN::$NA_SIGILOSO, InfraDTO::$OPER_DIFERENTE);

        if(trim($objWSEntradaListarDocumentoDTO->getDtaDataInicialGeracaoProtocolo())!=null&&trim($objWSEntradaListarDocumentoDTO->getDtaDataFinalGeracaoProtocolo())!=null) {
            $dto->adicionarCriterio(array('GeracaoProtocolo', 'GeracaoProtocolo'),
                array(InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL),
                array($objWSEntradaListarDocumentoDTO->getDtaDataInicialGeracaoProtocolo(), $objWSEntradaListarDocumentoDTO->getDtaDataFinalGeracaoProtocolo()),
                array(InfraDTO::$OPER_LOGICO_AND));
        }
        else{
            if(trim($objWSEntradaListarDocumentoDTO->getDtaDataInicialGeracaoProtocolo())!=null){
                $dto->setDtaGeracaoProtocolo($objWSEntradaListarDocumentoDTO->getDtaDataInicialGeracaoProtocolo(), InfraDTO::$OPER_MAIOR_IGUAL);
            }
            if(trim($objWSEntradaListarDocumentoDTO->getDtaDataFinalGeracaoProtocolo())!=null){
                $dto->setDtaGeracaoProtocolo($objWSEntradaListarDocumentoDTO->getDtaDataFinalGeracaoProtocolo(), InfraDTO::$OPER_MENOR_IGUAL);
            }
        }

    	$objDocumentoRN = new DocumentoRN();
    	$arrDto = $objDocumentoRN->listarRN0008($dto);

    	if ($arrDto==null){
    		$objInfraException->lancarValidacao('Nenhum documento encontrado com os parâmetros informados.');
      }
        
      $i = 0;
        
      foreach($arrDto as $objDocumentoDTOEncontrado) {
          $objDocumentoRN->bloquearConsultado($objDocumentoDTOEncontrado);

          $objDocumentoDTO = $objDocumentoDTOEncontrado;


          /*
           * Comentado, verificar depois!
           * if ($objUnidadeDTO != null) {
              $objPesquisaProtocoloDTO = new PesquisaProtocoloDTO();
              $objPesquisaProtocoloDTO->setStrStaTipo(ProtocoloRN::$TPP_DOCUMENTOS);
              $objPesquisaProtocoloDTO->setStrStaAcesso(ProtocoloRN::$TAP_AUTORIZADO);
              $objPesquisaProtocoloDTO->setDblIdProtocolo(array($objDocumentoDTO->getDblIdDocumento()));

              $objProtocoloRN = new ProtocoloRN();
              if (count($objProtocoloRN->pesquisarRN0967($objPesquisaProtocoloDTO)) == 0) {
                  $objInfraException->lancarValidacao('Unidade [' . $objUnidadeDTO->getStrSigla() . '] não possui acesso ao documento [' . $objDocumentoDTO->getStrProtocoloDocumentoFormatado() . '].');
              }
          }*/

          $objProcedimentoDTO = new ProcedimentoDTO();
          $objProcedimentoDTO->retDblIdProcedimento();
          $objProcedimentoDTO->retNumIdTipoProcedimento();
          $objProcedimentoDTO->setDblIdProcedimento($objDocumentoDTO->getDblIdProcedimento());

          $objProcedimentoRN = new ProcedimentoRN();
          $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

          $objOperacaoServicoDTO = new OperacaoServicoDTO();

          if ($objUnidadeDTO == null) {
              $this->adicionarCriteriosProcessoDocumento($objOperacaoServicoDTO, $objProcedimentoDTO, $objDocumentoDTO);
          } else {
              $this->adicionarCriteriosUnidadeProcessoDocumento($objOperacaoServicoDTO, $objUnidadeDTO, $objProcedimentoDTO, $objDocumentoDTO);
          }
          $objOperacaoServicoDTO->setNumStaOperacaoServico(OperacaoServicoRN::$TS_CONSULTAR_DOCUMENTO);
          $objOperacaoServicoDTO->setNumIdServico($objServicoDTO->getNumIdServico());

          $objOperacaoServicoRN = new OperacaoServicoRN();
          if ($objOperacaoServicoRN->contar($objOperacaoServicoDTO) == 0) {
              if ($objUnidadeDTO == null) {
                  $objInfraException->lancarValidacao('Nenhum serviço configurado para consulta deste documento [' . $objDocumentoDTO->getStrProtocoloDocumentoFormatado() . '] pelo Serviço [' . $objServicoDTO->getStrIdentificacao() . '].');
              } else {
                  $objInfraException->lancarValidacao('Nenhum serviço configurado para consulta deste documento [' . $objDocumentoDTO->getStrProtocoloDocumentoFormatado() . '] na unidade [' . $objUnidadeDTO->getStrSigla() . '] pelo Serviço [' . $objServicoDTO->getStrIdentificacao() . '].');
              }

          }

          $objInfraException->lancarValidacoes();

          //verifica se o usuário já tem acesso ao processo
          $objAcessoExternoDTO = new AcessoExternoDTO();
          $objAcessoExternoDTO->retNumIdAcessoExterno();
          $objAcessoExternoDTO->setDblIdProtocoloAtividade($objDocumentoDTO->getDblIdProcedimento());
          $objAcessoExternoDTO->setNumIdContatoParticipante($objServicoDTO->getNumIdContatoUsuario());
          $objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_SISTEMA);

          $objAcessoExternoRN = new AcessoExternoRN();
          $objAcessoExternoDTO = $objAcessoExternoRN->consultar($objAcessoExternoDTO);

          if ($objAcessoExternoDTO == null) {

              $objParticipanteDTO = new ParticipanteDTO();
              $objParticipanteDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
              $objParticipanteDTO->setNumIdContato($objServicoDTO->getNumIdContatoUsuario());
              $objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
              $objParticipanteDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
              $objParticipanteDTO->setNumSequencia(0);

              $objParticipanteRN = new ParticipanteRN();
              $objParticipanteDTO = $objParticipanteRN->cadastrarRN0170($objParticipanteDTO);

              $objAcessoExternoDTO = new AcessoExternoDTO();
              $objAcessoExternoDTO->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());
              $objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_SISTEMA);

              $objAcessoExternoRN = new AcessoExternoRN();
              $objAcessoExternoDTO = $objAcessoExternoRN->cadastrar($objAcessoExternoDTO);
          }

          $arrObjWSRetornoConsultarDocumentoDTO[$i] = new WSCguRetornoConsultarDocumentoDTO();
          $arrObjWSRetornoConsultarDocumentoDTO[$i]->setDblIdProcedimento($objDocumentoDTO->getDblIdProcedimento());
          $arrObjWSRetornoConsultarDocumentoDTO[$i]->setStrProcedimentoFormatado($objDocumentoDTO->getStrProtocoloProcedimentoFormatado());
          $arrObjWSRetornoConsultarDocumentoDTO[$i]->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());
          $arrObjWSRetornoConsultarDocumentoDTO[$i]->setStrDocumentoFormatado($objDocumentoDTO->getStrProtocoloDocumentoFormatado());
          $arrObjWSRetornoConsultarDocumentoDTO[$i]->setNumIdSerie($objDocumentoDTO->getNumIdSerie());
          $arrObjWSRetornoConsultarDocumentoDTO[$i]->setStrNomeSerie($objDocumentoDTO->getStrNomeSerie());
          $arrObjWSRetornoConsultarDocumentoDTO[$i]->setStrNumero($objDocumentoDTO->getStrNumero());
          $arrObjWSRetornoConsultarDocumentoDTO[$i]->setDtaGeracaoProtocolo($objDocumentoDTO->getDtaGeracaoProtocolo());

          $arrObjWSRetornoConsultarDocumentoDTO[$i]->setNumIdUnidadeGeradora($objDocumentoDTO->getNumIdUnidadeGeradoraProtocolo());
          $arrObjWSRetornoConsultarDocumentoDTO[$i]->setStrSiglaUnidadeGeradora($objDocumentoDTO->getStrSiglaUnidadeGeradoraProtocolo());
          $arrObjWSRetornoConsultarDocumentoDTO[$i]->setStrDescricaoUnidadeGeradora($objDocumentoDTO->getStrDescricaoUnidadeGeradoraProtocolo());

          if ($objWSEntradaListarDocumentoDTO->getStrSinRetornarAndamentoGeracao() == 'S') {
              $objProcedimentoHistoricoDTO = new ProcedimentoHistoricoDTO();
              $objProcedimentoHistoricoDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
              $objProcedimentoHistoricoDTO->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());
              $objProcedimentoHistoricoDTO->setStrStaHistorico(ProcedimentoRN::$TH_PERSONALIZADO);
              $objProcedimentoHistoricoDTO->setStrSinGerarLinksHistorico('N');
              $objProcedimentoHistoricoDTO->setNumIdTarefa(array(TarefaRN::$TI_GERACAO_DOCUMENTO, TarefaRN::$TI_RECEBIMENTO_DOCUMENTO), InfraDTO::$OPER_IN);
              $objProcedimentoHistoricoDTO->setNumMaxRegistrosRetorno(1);
              $objProcedimentoDTOHistorico = $objProcedimentoRN->consultarHistoricoRN1025($objProcedimentoHistoricoDTO);
              $arrObjAtividadeDTOHistorico = $objProcedimentoDTOHistorico->getArrObjAtividadeDTO();
              $arrObjWSRetornoConsultarDocumentoDTO[$i]->setObjAtividadeDTOGeracao($arrObjAtividadeDTOHistorico[0]);
          } else {
              $arrObjWSRetornoConsultarDocumentoDTO[$i]->setObjAtividadeDTOGeracao(null);
          }


          if ($objWSEntradaListarDocumentoDTO->getStrSinRetornarAssinaturas() == 'S') {
              $objAssinaturaDTO = new AssinaturaDTO();
              $objAssinaturaDTO->retNumIdAssinatura();
              $objAssinaturaDTO->retStrNome();
              $objAssinaturaDTO->retStrTratamento();
              $objAssinaturaDTO->retDthAberturaAtividade();
              $objAssinaturaDTO->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());
              $objAssinaturaDTO->setOrdNumIdAssinatura(InfraDTO::$TIPO_ORDENACAO_ASC);

              $objAssinaturaRN = new AssinaturaRN();
              $arrObjWSRetornoConsultarDocumentoDTO[$i]->setArrObjAssinaturaDTO($objAssinaturaRN->listarRN1323($objAssinaturaDTO));
          } else {
              $arrObjWSRetornoConsultarDocumentoDTO[$i]->setArrObjAssinaturaDTO(array());
          }

          if ($objWSEntradaListarDocumentoDTO->getStrSinRetornarPublicacao() == 'S') {
              $objPublicacaoDTO = new PublicacaoDTO();
              $objPublicacaoDTO->retNumIdPublicacao();
              $objPublicacaoDTO->retDtaDisponibilizacao();
              $objPublicacaoDTO->retNumIdVeiculoIO();
              $objPublicacaoDTO->retDtaPublicacaoIO();
              $objPublicacaoDTO->retStrPaginaIO();
              $objPublicacaoDTO->retDtaPublicacao();
              $objPublicacaoDTO->retNumNumero();
              $objPublicacaoDTO->retStrNomeVeiculoPublicacao();
              $objPublicacaoDTO->retStrDescricaoVeiculoImprensaNacional();
              $objPublicacaoDTO->retStrStaEstado();
              $objPublicacaoDTO->retStrSiglaVeiculoImprensaNacional();
              $objPublicacaoDTO->retStrNomeSecaoImprensaNacional();
              $objPublicacaoDTO->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());

              $objPublicacaoRN = new PublicacaoRN();
              $arrObjWSRetornoConsultarDocumentoDTO[$i]->setObjPublicacaoDTO($objPublicacaoRN->consultarRN1044($objPublicacaoDTO));
          } else {
              $arrObjWSRetornoConsultarDocumentoDTO[$i]->setObjPublicacaoDTO(null);
          }

          if ($objWSEntradaListarDocumentoDTO->getStrSinRetornarDestinatarios() == 'S') {

              $objParticipanteDTO = new ParticipanteDTO();
              $objParticipanteDTO->retNumIdContato();
              $objParticipanteDTO->retStrNomeContato();
              $objParticipanteDTO->retStrEmailContato();
              $objParticipanteDTO->retStrSiglaUnidade();

              $objParticipanteDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdDocumento());
              $objParticipanteDTO->setStrStaParticipacao(array(ParticipanteRN::$TP_DESTINATARIO),InfraDTO::$OPER_IN);

              $objParticipanteDTO->setOrdNumSequencia(InfraDTO::$TIPO_ORDENACAO_ASC);

              $objParticipanteRN = new ParticipanteRN();
              //$arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);

              $arrObjWSRetornoConsultarDocumentoDTO[$i]->setArrObjParticipanteDTO($objParticipanteRN->listarRN0189($objParticipanteDTO));
          } else {
              $arrObjWSRetornoConsultarDocumentoDTO[$i]->setArrObjParticipanteDTO(array());
          }

          if ($objServicoDTO->getStrSinLinkExterno() == 'S') {
              $arrObjWSRetornoConsultarDocumentoDTO[$i]->setStrLinkAcesso(SessaoSEIExterna::getInstance($objAcessoExternoDTO->getNumIdAcessoExterno())->assinarLink(ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/documento_consulta_externa.php?id_acesso_externo=' . $objAcessoExternoDTO->getNumIdAcessoExterno() . '&id_documento=' . $objDocumentoDTO->getDblIdDocumento()));
          } else {
              $arrObjWSRetornoConsultarDocumentoDTO[$i]->setStrLinkAcesso(ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador.php?acao=procedimento_trabalhar&id_procedimento=' . $objDocumentoDTO->getDblIdProcedimento() . '&id_documento=' . $objDocumentoDTO->getDblIdDocumento());
          }

          $i++;

      }

      return $arrObjWSRetornoConsultarDocumentoDTO;

    }catch(Exception $e){
      throw new InfraException('Erro no serviço de consulta de documento.',$e);
    }
  }

    protected function listarProcedimentoControlado(WSEntradaListarProcedimentoDTO $objWSEntradaListarProcedimentoDTO){


        try{

            $objInfraException = new InfraException();

            $objAtividadeRN = new AtividadeRN();
            $objProcedimentoRN = new ProcedimentoRN();

            $objServicoDTO = $objWSEntradaListarProcedimentoDTO->getObjServicoDTO();
            $objProcedimentoDTO = $objWSEntradaListarProcedimentoDTO->getObjProcedimentoDTO();
            $objUnidadeDTO = $objWSEntradaListarProcedimentoDTO->getObjUnidadeDTO();

            $dto = new ProcedimentoDTO();

            $dto->retDblIdProcedimento();
            $dto->retStrProtocoloProcedimentoFormatado();
            $dto->retStrStaNivelAcessoGlobalProtocolo();
            $dto->retNumIdTipoProcedimento();
            $dto->retStrNomeTipoProcedimento();
            $dto->retStrDescricaoProtocolo();
            $dto->retDtaGeracaoProtocolo();

            if ($objProcedimentoDTO->getStrProtocoloProcedimentoFormatado()!='') {
                $dto->setStrProtocoloProcedimentoFormatadoPesquisa(InfraUtil::retirarFormatacao($objProcedimentoDTO->getStrProtocoloProcedimentoFormatado()));
            }

            if(trim($objWSEntradaListarProcedimentoDTO->getStrClassificacaoAssunto())!=null){
                $arrProtocolosResultadoAssuntos = $this->prepararClassificacaoAssunto($objWSEntradaListarProcedimentoDTO->getStrClassificacaoAssunto());
            }
            else{
                $arrProtocolosResultadoAssuntos = array();
            }

            if(trim($objWSEntradaListarProcedimentoDTO->getStrInteressado())!=null){
                $arrProtocolosResultadoInteressados = $this->prepararParticipantes($objWSEntradaListarProcedimentoDTO->getStrInteressado());
            }
            else{
                $arrProtocolosResultadoInteressados = array();
            }

            if($objWSEntradaListarProcedimentoDTO->getNumUnidadeProcedimentoAberto()!=null) {
                $arrProtocolosResultadoAbertos = $this->prepararUnidadeProcedimentoAberto($objWSEntradaListarProcedimentoDTO->getNumUnidadeProcedimentoAberto(), $objServicoDTO->getNumIdContatoUsuario());
            }
            else{
                $arrProtocolosResultadoAbertos = array();
            }

            $arrJuncaoProtocolos = array('arrProtocolosResultadoAssuntos', 'arrProtocolosResultadoInteressados','arrProtocolosResultadoAbertos');
            $arrResultadoProtocolos = $arrProtocolosResultadoAssuntos;

            foreach($arrJuncaoProtocolos as $k){
                if(!empty(${$k})) $out = array_intersect($arrResultadoProtocolos,${$k});
            };

            if (count($arrResultadoProtocolos)>0) {
                $dto->setDblIdProcedimento($arrResultadoProtocolos, InfraDTO::$OPER_IN);
            }

            if($objProcedimentoDTO->getStrDescricaoProtocolo()!='') {
                $dto->setStrDescricaoProtocolo('%'.$objProcedimentoDTO->getStrDescricaoProtocolo().'%',InfraDTO::$OPER_LIKE);
            }

            if($objProcedimentoDTO->getNumIdTipoProcedimento()!='') {
                $dto->setNumIdTipoProcedimento($objProcedimentoDTO->getNumIdTipoProcedimento());
            }

            if($objProcedimentoDTO->getStrNomeTipoProcedimento()!='') {
                $dto->setStrNomeTipoProcedimento('%'.$objProcedimentoDTO->getStrNomeTipoProcedimento().'%',InfraDTO::$OPER_LIKE);
            }

            if(trim($objWSEntradaListarProcedimentoDTO->getDtaDataInicialRegistroProcedimento())!=null&&trim($objWSEntradaListarProcedimentoDTO->getDtaDataFinalRegistroProcedimento())!=null) {
                $dto->adicionarCriterio(array('GeracaoProtocolo', 'GeracaoProtocolo'),
                    array(InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL),
                    array($objWSEntradaListarProcedimentoDTO->getDtaDataInicialRegistroProcedimento(), $objWSEntradaListarProcedimentoDTO->getDtaDataFinalRegistroProcedimento()),
                    array(InfraDTO::$OPER_LOGICO_AND));
            }
            else{
                if(trim($objWSEntradaListarProcedimentoDTO->getDtaDataInicialRegistroProcedimento())!=null){
                    $dto->setDtaGeracaoProtocolo($objWSEntradaListarProcedimentoDTO->getDtaDataInicialRegistroProcedimento(), InfraDTO::$OPER_MAIOR_IGUAL);
                }
                if(trim($objWSEntradaListarProcedimentoDTO->getDtaDataFinalRegistroProcedimento())!=null){
                    $dto->setDtaGeracaoProtocolo($objWSEntradaListarProcedimentoDTO->getDtaDataFinalRegistroProcedimento(), InfraDTO::$OPER_MENOR_IGUAL);
                }
            }

            //$dto->setStrProtocoloProcedimentoFormatadoPesquisa(InfraUtil::retirarFormatacao($objProcedimentoDTO->getStrProtocoloProcedimentoFormatado()));
            $dto->setStrStaNivelAcessoGlobalProtocolo(ProtocoloRN::$NA_SIGILOSO, InfraDTO::$OPER_DIFERENTE);

            $dto->setNumMaxRegistrosRetorno(1000);

            $arrDto = $objProcedimentoRN->listarRN0278($dto);

            if ($arrDto==null){
                $objInfraException->lancarValidacao('Nenhum processo encontrado para os parâmetros informados.');
            }

            $i = 0;

            foreach($arrDto as $objProcedimentoDTO) {

                //$objProcedimentoDTO = $dto;

                if ($objUnidadeDTO != null) {
                    $objPesquisaProtocoloDTO = new PesquisaProtocoloDTO();
                    $objPesquisaProtocoloDTO->setStrStaTipo(ProtocoloRN::$TPP_PROCEDIMENTOS);
                    $objPesquisaProtocoloDTO->setStrStaAcesso(ProtocoloRN::$TAP_AUTORIZADO);
                    $objPesquisaProtocoloDTO->setDblIdProtocolo(array($objProcedimentoDTO->getDblIdProcedimento()));

                    $objProtocoloRN = new ProtocoloRN();
                    if (count($objProtocoloRN->pesquisarRN0967($objPesquisaProtocoloDTO)) == 0) {
                        $objInfraException->lancarValidacao('Unidade [' . $objUnidadeDTO->getStrSigla() . '] não possui acesso ao processo [' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . '].');
                    }
                }


                $objOperacaoServicoDTO = new OperacaoServicoDTO();

                if ($objUnidadeDTO == null) {
                    $this->adicionarCriteriosProcesso($objOperacaoServicoDTO, $objProcedimentoDTO);
                } else {
                    $this->adicionarCriteriosUnidadeProcesso($objOperacaoServicoDTO, $objUnidadeDTO, $objProcedimentoDTO);
                }

                $objOperacaoServicoDTO->setNumStaOperacaoServico(OperacaoServicoRN::$TS_CONSULTAR_PROCEDIMENTO);
                $objOperacaoServicoDTO->setNumIdServico($objServicoDTO->getNumIdServico());


                $objOperacaoServicoRN = new OperacaoServicoRN();
                if ($objOperacaoServicoRN->contar($objOperacaoServicoDTO) == 0) {
                    if ($objUnidadeDTO == null) {
                        $objInfraException->lancarValidacao('Nenhum serviço configurado para consulta deste processo [' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . '] pelo Serviço [' . $objServicoDTO->getStrIdentificacao() . '].');
                    } else {
                        $objInfraException->lancarValidacao('Nenhum serviço configurado para consulta deste processo [' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . '] na unidade [' . $objUnidadeDTO->getStrSigla() . '] pelo Serviço [' . $objServicoDTO->getStrIdentificacao() . '].');
                    }
                }

                $objInfraException->lancarValidacoes();

                //verifica se o usuário já tem acesso ao processo
                $objAcessoExternoDTO = new AcessoExternoDTO();
                $objAcessoExternoDTO->retNumIdAcessoExterno();
                $objAcessoExternoDTO->setDblIdProtocoloAtividade($objProcedimentoDTO->getDblIdProcedimento());
                $objAcessoExternoDTO->setNumIdContatoParticipante($objServicoDTO->getNumIdContatoUsuario());
                $objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_SISTEMA);

                $objAcessoExternoRN = new AcessoExternoRN();
                $objAcessoExternoDTO = $objAcessoExternoRN->consultar($objAcessoExternoDTO);

                if ($objAcessoExternoDTO == null) {

                    $objParticipanteDTO = new ParticipanteDTO();
                    $objParticipanteDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
                    $objParticipanteDTO->setNumIdContato($objServicoDTO->getNumIdContatoUsuario());
                    $objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
                    $objParticipanteDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                    $objParticipanteDTO->setNumSequencia(0);

                    $objParticipanteRN = new ParticipanteRN();
                    $objParticipanteDTO = $objParticipanteRN->cadastrarRN0170($objParticipanteDTO);

                    $objAcessoExternoDTO = new AcessoExternoDTO();
                    $objAcessoExternoDTO->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());
                    $objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_SISTEMA);

                    $objAcessoExternoRN = new AcessoExternoRN();
                    $objAcessoExternoDTO = $objAcessoExternoRN->cadastrar($objAcessoExternoDTO);
                }

                $ArrObjWSRetornoConsultarProcedimentoDTO[$i] = new WSRetornoConsultarProcedimentoDTO();
                $ArrObjWSRetornoConsultarProcedimentoDTO[$i]->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
                $ArrObjWSRetornoConsultarProcedimentoDTO[$i]->setStrProcedimentoFormatado($objProcedimentoDTO->getStrProtocoloProcedimentoFormatado());
                $ArrObjWSRetornoConsultarProcedimentoDTO[$i]->setNumIdTipoProcedimento($objProcedimentoDTO->getNumIdTipoProcedimento());
                $ArrObjWSRetornoConsultarProcedimentoDTO[$i]->setStrNomeTipoProcedimento($objProcedimentoDTO->getStrNomeTipoProcedimento());
                $ArrObjWSRetornoConsultarProcedimentoDTO[$i]->setDtaAutuacao($objProcedimentoDTO->getDtaGeracaoProtocolo());
                $ArrObjWSRetornoConsultarProcedimentoDTO[$i]->setStrEspecificacao($objProcedimentoDTO->getStrDescricaoProtocolo());

                $i++;
            }

            return $ArrObjWSRetornoConsultarProcedimentoDTO;

        }catch(Exception $e){
            throw new InfraException('Erro no serviço de consulta de procedimento.',$e);
        }
    }

    protected function listarAndamentosControlado(WSEntradaListarAndamentosDTO $objWSEntradaListarAndamentosDTO){
        try{

            $objInfraException = new InfraException();

            $objAtividadeRN = new AtividadeRN();
            $objProcedimentoRN = new ProcedimentoRN();

            $objServicoDTO = $objWSEntradaListarAndamentosDTO->getObjServicoDTO();
            $objProcedimentoDTO = $objWSEntradaListarAndamentosDTO->getObjProcedimentoDTO();
            $objUnidadeDTO = $objWSEntradaListarAndamentosDTO->getObjUnidadeDTO();

            $dto = new ProcedimentoDTO();
            $dto->retDblIdProcedimento();
            $dto->retStrProtocoloProcedimentoFormatado();
            $dto->retStrStaNivelAcessoGlobalProtocolo();
            $dto->retNumIdTipoProcedimento();
            $dto->retStrNomeTipoProcedimento();
            $dto->retStrDescricaoProtocolo();
            $dto->retDtaGeracaoProtocolo();
            $dto->setStrProtocoloProcedimentoFormatadoPesquisa(InfraUtil::retirarFormatacao($objProcedimentoDTO->getStrProtocoloProcedimentoFormatado()));
            $dto->setStrStaNivelAcessoGlobalProtocolo(ProtocoloRN::$NA_SIGILOSO, InfraDTO::$OPER_DIFERENTE);

            $dto = $objProcedimentoRN->consultarRN0201($dto);

            if ($dto==null){
                $objInfraException->lancarValidacao('Processo '.$objProcedimentoDTO->getStrProtocoloProcedimentoFormatado().' não encontrado.');
            }

            $objProcedimentoDTO = $dto;

            $objWSRetornoListarAndamentosDTO = new WSCguRetornoListarAndamentosDTO();

            $objWSRetornoListarAndamentosDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
            $objWSRetornoListarAndamentosDTO->setStrProcedimentoFormatado($objProcedimentoDTO->getStrProtocoloProcedimentoFormatado());
            $objWSRetornoListarAndamentosDTO->setNumIdTipoProcedimento($objProcedimentoDTO->getNumIdTipoProcedimento());
            $objWSRetornoListarAndamentosDTO->setStrNomeTipoProcedimento($objProcedimentoDTO->getStrNomeTipoProcedimento());

            $objProcedimentoHistoricoDTO = new ProcedimentoHistoricoDTO();
            $objProcedimentoHistoricoDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
            $objProcedimentoHistoricoDTO->setStrStaHistorico(ProcedimentoRN::$TH_RESUMIDO);
            $objProcedimentoHistoricoDTO->setStrSinGerarLinksHistorico('N');
            //$objProcedimentoHistoricoDTO->setNumMaxRegistrosRetorno(1);
            $objProcedimentoDTOHistorico = $objProcedimentoRN->consultarHistoricoRN1025($objProcedimentoHistoricoDTO);
            $arrObjAtividadeDTOHistorico = $objProcedimentoDTOHistorico->getArrObjAtividadeDTO();

            $objWSRetornoListarAndamentosDTO->setArrObjAtividadeDTO($arrObjAtividadeDTOHistorico);

            return $objWSRetornoListarAndamentosDTO;

        }catch(Exception $e){
            throw new InfraException('Erro no serviço de consulta de andamentos.',$e);
        }
  }


    protected function listarProcedimentosTramitadosParaAreaControlado(WSEntradaListarProcedimentosTramitadosDTO $objWsEntradaListarProcedimentosTramitadosDTO){
        try{

            $objInfraException = new InfraException();

            $objAtividadeRN = new AtividadeRN();
            $objProcedimentoRN = new ProcedimentoRN();

            /*$dto = new ProcedimentoDTO();
            $dto->retDblIdProcedimento();
            $dto->retStrProtocoloProcedimentoFormatado();
            $dto->retStrStaNivelAcessoGlobalProtocolo();
            $dto->retNumIdTipoProcedimento();
            $dto->retStrNomeTipoProcedimento();
            $dto->retStrDescricaoProtocolo();
            $dto->retDtaGeracaoProtocolo();
            $dto->setStrProtocoloProcedimentoFormatadoPesquisa(InfraUtil::retirarFormatacao($objProcedimentoDTO->getStrProtocoloProcedimentoFormatado()));
            $dto->setStrStaNivelAcessoGlobalProtocolo(ProtocoloRN::$NA_SIGILOSO, InfraDTO::$OPER_DIFERENTE);

            $dto = $objProcedimentoRN->consultarRN0201($dto);

            if ($dto==null){
                $objInfraException->lancarValidacao('Processo '.$objProcedimentoDTO->getStrProtocoloProcedimentoFormatado().' não encontrado.');
            }

            $objProcedimentoDTO = $dto;*/

            $objWSRetornoListarAndamentosDTO = new WSCguRetornoListarAndamentosDTO();

            /*$objWSRetornoListarAndamentosDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
            $objWSRetornoListarAndamentosDTO->setStrProcedimentoFormatado($objProcedimentoDTO->getStrProtocoloProcedimentoFormatado());
            $objWSRetornoListarAndamentosDTO->setNumIdTipoProcedimento($objProcedimentoDTO->getNumIdTipoProcedimento());
            $objWSRetornoListarAndamentosDTO->setStrNomeTipoProcedimento($objProcedimentoDTO->getStrNomeTipoProcedimento());*/

            $objProcedimentoHistoricoDTO = new ProcedimentoHistoricoDTO();
            $objProcedimentoHistoricoDTO->setDblIdProcedimento($objWsEntradaListarProcedimentosTramitadosDTO->getDblIdProcedimento());
            $objProcedimentoHistoricoDTO->setStrStaHistorico(ProcedimentoRN::$TH_RESUMIDO);
            $objProcedimentoHistoricoDTO->setStrSinGerarLinksHistorico('N');
            //$objProcedimentoHistoricoDTO->setNumMaxRegistrosRetorno(1);
            $objProcedimentoDTOHistorico = $objProcedimentoRN->consultarHistoricoRN1025($objProcedimentoHistoricoDTO);
            $arrObjAtividadeDTOHistorico = $objProcedimentoDTOHistorico->getArrObjAtividadeDTO();

            $objWSRetornoListarAndamentosDTO->setArrObjAtividadeDTO($arrObjAtividadeDTOHistorico);

            return $objWSRetornoListarAndamentosDTO;

        }catch(Exception $e){
            throw new InfraException('Erro no serviço de consulta de andamentos.',$e);
        }
    }

  private function prepararParticipantes($Interessado){

      $objParticipanteDTO = new ParticipanteDTO();
      $objParticipanteDTO->retDblIdProtocolo();
      $objParticipanteDTO->retNumIdParticipante();
      $objParticipanteDTO->retNumIdContato();
      $objParticipanteDTO->retStrNomeContato();
      $objParticipanteDTO->retStrSiglaContato();
      $objParticipanteDTO->setStrNomeContato('%'.$Interessado.'%',InfraDTO::$OPER_LIKE);

      //filtra somente processos
      $objParticipanteDTO->setStrStaProtocoloProtocolo(ProtocoloRN::$TP_PROCEDIMENTO);

      //como não tem paginação é bom limitar
      $objParticipanteDTO->setNumMaxRegistrosRetorno(500);

      $objParticipanteRN = new ParticipanteRN();
      $arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);

      if (count($arrObjParticipanteDTO)==0){
          throw new InfraException('Nenhum interessado encontrado!'); }

      $ret = array();

      foreach($arrObjParticipanteDTO as $Participante){
          $ret[] = $Participante->getDblIdProtocolo();
      }

      return $ret;
  }

    private function prepararClassificacaoAssunto($ClassificacaoAssunto){


        $objRelProtocoloAssuntoDTO = new RelProtocoloAssuntoDTO();
        $objRelProtocoloAssuntoDTO->retDblIdProtocolo();
        $objRelProtocoloAssuntoDTO->retNumIdAssunto();
        $objRelProtocoloAssuntoDTO->setStrCodigoEstruturadoAssunto($ClassificacaoAssunto);

        //como não tem paginação é bom limitar
        $objRelProtocoloAssuntoDTO->setNumMaxRegistrosRetorno(500);

        $objRelProtocoloAssuntoRN = new RelProtocoloAssuntoRN();
        $arrAssuntos = $objRelProtocoloAssuntoRN->listarRN0188($objRelProtocoloAssuntoDTO);

        if (count($arrAssuntos)==0){
            throw new InfraException('Nenhuma Classificação de Assunto encontrado!'); }

        $ret = array();
        foreach($arrAssuntos as $Assuntos){
            $ret[] = $Assuntos->getDblIdProtocolo();
        }

        return $ret;
    }

  private function prepararUnidadeProcedimentoAberto($numIdUnidade, $numIdUsuario){

      $objPesquisaPendenciaDTO = new PesquisaPendenciaDTO();
      $objPesquisaPendenciaDTO->retDblIdProtocolo();
      $objPesquisaPendenciaDTO->retNumIdUsuario();
      $objPesquisaPendenciaDTO->setNumIdUsuario($numIdUsuario);
      $objPesquisaPendenciaDTO->setNumIdUnidade($numIdUnidade);

      $objAtividadeRN = new AtividadeRN();

      $arrObjPendenciaDTO = $objAtividadeRN->listarPendenciasRN0754($objPesquisaPendenciaDTO);

      if (count($arrObjPendenciaDTO)==0){
          throw new InfraException('Nenhum Procedimento com Pendência para a Unidade Informada!'); }

      $ret = array();

      foreach($arrObjPendenciaDTO as $objPendenciaDTO){
          $ret[] = $objPendenciaDTO->getDblIdProcedimento();
      }

      return $ret;
  }

  private function adicionarCriteriosProcesso(OperacaoServicoDTO $objOperacaoServicoDTO, ProcedimentoDTO $objProcedimentoDTO){
  
    //tipo informado ou qualquer um
    $objOperacaoServicoDTO->adicionarCriterio(array('IdTipoProcedimento','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array($objProcedimentoDTO->getNumIdTipoProcedimento(),null),
        InfraDTO::$OPER_LOGICO_OR);
  }

  private function adicionarCriteriosUnidade(OperacaoServicoDTO $objOperacaoServicoDTO, UnidadeDTO $objUnidadeDTO){
  
    //unidade informada ou qualquer uma
    $objOperacaoServicoDTO->adicionarCriterio(array('IdUnidade','IdUnidade'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array($objUnidadeDTO->getNumIdUnidade(),null),
        InfraDTO::$OPER_LOGICO_OR);
  }
  
  private function adicionarCriteriosUnidadeProcesso(OperacaoServicoDTO $objOperacaoServicoDTO, UnidadeDTO $objUnidadeDTO, ProcedimentoDTO $objProcedimentoDTO){

    //qualquer tipo em qualquer unidade
    $objOperacaoServicoDTO->adicionarCriterio(array('IdTipoProcedimento','IdUnidade'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array(null,null),
        InfraDTO::$OPER_LOGICO_AND,
        'c1');
    
    //este tipo em qualquer unidade
    $objOperacaoServicoDTO->adicionarCriterio(array('IdTipoProcedimento','IdUnidade'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array($objProcedimentoDTO->getNumIdTipoProcedimento(),null),
        InfraDTO::$OPER_LOGICO_AND,
        'c2');
    
    //qualquer tipo nesta unidade
    $objOperacaoServicoDTO->adicionarCriterio(array('IdTipoProcedimento','IdUnidade'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array(null, $objUnidadeDTO->getNumIdUnidade()),
        InfraDTO::$OPER_LOGICO_AND,
        'c3');
    
    //este tipo nesta unidade
    $objOperacaoServicoDTO->adicionarCriterio(array('IdTipoProcedimento','IdUnidade'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array($objProcedimentoDTO->getNumIdTipoProcedimento(), $objUnidadeDTO->getNumIdUnidade()),
        InfraDTO::$OPER_LOGICO_AND,
        'c4');
    
    $objOperacaoServicoDTO->agruparCriterios(array('c1','c2','c3','c4'),
        array(InfraDTO::$OPER_LOGICO_OR, InfraDTO::$OPER_LOGICO_OR, InfraDTO::$OPER_LOGICO_OR));
  }  

  private function adicionarCriteriosProcessoDocumento(OperacaoServicoDTO $objOperacaoServicoDTO, ProcedimentoDTO $objProcedimentoDTO, DocumentoDTO $objDocumentoDTO){
  
    //qualquer série em qualquer tipo de procedimento
    $objOperacaoServicoDTO->adicionarCriterio(array('IdSerie','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array(null,null),
        array(InfraDTO::$OPER_LOGICO_AND),
        'c1');
  
    //esta série em qualquer tipo de procedimento
    $objOperacaoServicoDTO->adicionarCriterio(array('IdSerie','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array($objDocumentoDTO->getNumIdSerie(),null),
        array(InfraDTO::$OPER_LOGICO_AND),
        'c2');
  
    //qualquer série neste tipo de procedimento
    $objOperacaoServicoDTO->adicionarCriterio(array('IdSerie','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array(null, $objProcedimentoDTO->getNumIdTipoProcedimento()),
        array(InfraDTO::$OPER_LOGICO_AND),
        'c3');
  
    //esta série neste tipo de procedimento
    $objOperacaoServicoDTO->adicionarCriterio(array('IdSerie','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array($objDocumentoDTO->getNumIdSerie(), $objProcedimentoDTO->getNumIdTipoProcedimento()),
        array(InfraDTO::$OPER_LOGICO_AND),
        'c4');
  
    $objOperacaoServicoDTO->agruparCriterios(array('c1','c2','c3','c4'),
        array(InfraDTO::$OPER_LOGICO_OR,
              InfraDTO::$OPER_LOGICO_OR,
              InfraDTO::$OPER_LOGICO_OR));
  }

  private function adicionarCriteriosUnidadeProcessoDocumento(OperacaoServicoDTO $objOperacaoServicoDTO, UnidadeDTO $objUnidadeDTO, ProcedimentoDTO $objProcedimentoDTO, DocumentoDTO $objDocumentoDTO){

    //qualquer série em qualquer unidade em qualquer tipo de procedimento
    $objOperacaoServicoDTO->adicionarCriterio(array('IdSerie','IdUnidade','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array(null,null,null),
        array(InfraDTO::$OPER_LOGICO_AND,InfraDTO::$OPER_LOGICO_AND),
        'c1');
    
    //esta série em qualquer unidade em qualquer tipo de procedimento
    $objOperacaoServicoDTO->adicionarCriterio(array('IdSerie','IdUnidade','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array($objDocumentoDTO->getNumIdSerie(),null,null),
        array(InfraDTO::$OPER_LOGICO_AND,InfraDTO::$OPER_LOGICO_AND),
        'c2');
    
    //qualquer série nesta unidade em qualquer tipo de procedimento
    $objOperacaoServicoDTO->adicionarCriterio(array('IdSerie','IdUnidade','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array(null, $objUnidadeDTO->getNumIdUnidade(),null),
        array(InfraDTO::$OPER_LOGICO_AND,InfraDTO::$OPER_LOGICO_AND),
        'c3');
    
    //qualquer série em qualquer unidade neste tipo de procedimento
    $objOperacaoServicoDTO->adicionarCriterio(array('IdSerie','IdUnidade','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array(null, null ,$objProcedimentoDTO->getNumIdTipoProcedimento()),
        array(InfraDTO::$OPER_LOGICO_AND,InfraDTO::$OPER_LOGICO_AND),
        'c4');
    
    //esta série nesta unidade em qualquer tipo de procedimento
    $objOperacaoServicoDTO->adicionarCriterio(array('IdSerie','IdUnidade','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array($objDocumentoDTO->getNumIdSerie(), $objUnidadeDTO->getNumIdUnidade(),null),
        array(InfraDTO::$OPER_LOGICO_AND,InfraDTO::$OPER_LOGICO_AND),
        'c5');
    
    //esta série em qualquer unidade neste tipo de procedimento
    $objOperacaoServicoDTO->adicionarCriterio(array('IdSerie','IdUnidade','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array($objDocumentoDTO->getNumIdSerie(), null,$objProcedimentoDTO->getNumIdTipoProcedimento()),
        array(InfraDTO::$OPER_LOGICO_AND,InfraDTO::$OPER_LOGICO_AND),
        'c6');
    
    //qualquer série nesta unidade neste tipo de procedimento
    $objOperacaoServicoDTO->adicionarCriterio(array('IdSerie','IdUnidade','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array(null, $objUnidadeDTO->getNumIdUnidade(),$objProcedimentoDTO->getNumIdTipoProcedimento()),
        array(InfraDTO::$OPER_LOGICO_AND,InfraDTO::$OPER_LOGICO_AND),
        'c7');
    
    //esta série nesta unidade neste tipo de procedimento
    $objOperacaoServicoDTO->adicionarCriterio(array('IdSerie','IdUnidade','IdTipoProcedimento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array($objDocumentoDTO->getNumIdSerie(), $objUnidadeDTO->getNumIdUnidade(),$objProcedimentoDTO->getNumIdTipoProcedimento()),
        array(InfraDTO::$OPER_LOGICO_AND,InfraDTO::$OPER_LOGICO_AND),
        'c8');
    
    $objOperacaoServicoDTO->agruparCriterios(array('c1','c2','c3','c4','c5','c6','c7','c8'),
        array(InfraDTO::$OPER_LOGICO_OR,
            InfraDTO::$OPER_LOGICO_OR,
            InfraDTO::$OPER_LOGICO_OR,
            InfraDTO::$OPER_LOGICO_OR,
            InfraDTO::$OPER_LOGICO_OR,
            InfraDTO::$OPER_LOGICO_OR,
            InfraDTO::$OPER_LOGICO_OR));
  }
}
?>