<?
/*
 * CONTROLADORIA GERAL DA UNIÃO - CGU
 *
 * 23/06/2015 - criado por Rafael Leandro Ferreira
 *
 *
 *Este WebService tem o objetivo de atender a necessidade da CGU que não está suportada dentro dos métodos
 *existentes em SeiWS.php.
 *Foi criado este arquivo para não fazer alterações neste arquivo. O ideal é que posteriormente estes métodos sejam incorporados
 *ao SeiWS para estar disponível como um método homologado pelo SEI.
 */

require_once dirname(__FILE__) . '/../../../../SEI.php';

class CguWS extends InfraWS {

    public function getObjInfraLog(){
        return LogSEI::getInstance();
    }

    public function listarDocumentos($SiglaSistema, $IdentificacaoServico, $IdUnidade, $ProtocoloProcedimento, $ProtocoloDocumento, $NumeroDocumento, $Serie, $UnidadeElaboradora, $DataInicial, $DataFinal, $SinRetornarAndamentoGeracao,$SinRetornarAssinaturas,$SinRetornarPublicacao, $SinRetornarDestinatarios){

        try{

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            InfraDebug::getInstance()->gravar(__METHOD__);
            InfraDebug::getInstance()->gravar('SIGLA SISTEMA:'.$SiglaSistema);
            InfraDebug::getInstance()->gravar('IDENTIFICACAO SERVICO:'.$IdentificacaoServico);
            InfraDebug::getInstance()->gravar('ID UNIDADE:'.$IdUnidade);
            InfraDebug::getInstance()->gravar('PROTOCOLO PROCEDIMENTO:'.$ProtocoloProcedimento);
            InfraDebug::getInstance()->gravar('PROTOCOLO DOCUMENTO:'.$ProtocoloDocumento);
            InfraDebug::getInstance()->gravar('NUMERO DOCUMENTO:'.$NumeroDocumento);
            InfraDebug::getInstance()->gravar('SERIE:'.$Serie);
            InfraDebug::getInstance()->gravar('UNIDADE ELABORADORA:'.$UnidadeElaboradora);
            InfraDebug::getInstance()->gravar('DATA INICIAL:'.$DataInicial);
            InfraDebug::getInstance()->gravar('DATA FINAL:'.$DataFinal);
            InfraDebug::getInstance()->gravar('RETORNAR ANDAMENTO GERACAO:'.$SinRetornarAndamentoGeracao);
            InfraDebug::getInstance()->gravar('RETORNAR ASSINATURAS:'.$SinRetornarAssinaturas);
            InfraDebug::getInstance()->gravar('RETORNAR PUBLICACAO:'.$SinRetornarPublicacao);
            InfraDebug::getInstance()->gravar('RETORNAR DESTINATARIOS:'.$SinRetornarDestinatarios);

            SessaoSEI::getInstance(false);

            $objServicoDTO = $this->obterServico($SiglaSistema, $IdentificacaoServico);

            if ($IdUnidade!=null){
                $objUnidadeDTO = $this->obterUnidade($IdUnidade,null);
            }else{
                $objUnidadeDTO = null;
            }

            $this->validarAcessoAutorizado(explode(',',str_replace(' ','',$objServicoDTO->getStrServidor())));

            if ($objUnidadeDTO==null){
                SessaoSEI::getInstance()->simularLogin(null, SessaoSEI::$UNIDADE_TESTE, $objServicoDTO->getNumIdUsuario(), null);
            }else{
                SessaoSEI::getInstance()->simularLogin(null, null, $objServicoDTO->getNumIdUsuario(), $objUnidadeDTO->getNumIdUnidade());
            }

            $objDocumentoDTO = new DocumentoDTO();
            if($ProtocoloDocumento!='') {
                $objDocumentoDTO->setStrProtocoloDocumentoFormatado($ProtocoloDocumento);
            }
            if($ProtocoloProcedimento!='') {
                $objDocumentoDTO->setStrProtocoloProcedimentoFormatado($ProtocoloProcedimento);
            }
            if($NumeroDocumento!='') {
                $objDocumentoDTO->setStrNumero($NumeroDocumento);
            }

            $objDocumentoDTO->setNumIdSerie($Serie);
            $objDocumentoDTO->setNumIdUnidadeGeradoraProtocolo($UnidadeElaboradora);

            $objWSEntradaListarDocumentoDTO = new WSEntradaListarDocumentoDTO();
            $objWSEntradaListarDocumentoDTO->setObjServicoDTO($objServicoDTO);
            $objWSEntradaListarDocumentoDTO->setObjDocumentoDTO($objDocumentoDTO);
            $objWSEntradaListarDocumentoDTO->setObjUnidadeDTO($objUnidadeDTO);

            if($DataInicial!='') {
                $objWSEntradaListarDocumentoDTO->setDtaDataInicialGeracaoProtocolo($DataInicial);
            }
            else{
                $objWSEntradaListarDocumentoDTO->setDtaDataInicialGeracaoProtocolo(null);
            }

            if($DataFinal!='') {
                $objWSEntradaListarDocumentoDTO->setDtaDataFinalGeracaoProtocolo($DataFinal);
            }
            else{
                $objWSEntradaListarDocumentoDTO->setDtaDataFinalGeracaoProtocolo(null);
            }

            if (trim($SinRetornarAndamentoGeracao)!=''){
                $objWSEntradaListarDocumentoDTO->setStrSinRetornarAndamentoGeracao($SinRetornarAndamentoGeracao);
            }else{
                $objWSEntradaListarDocumentoDTO->setStrSinRetornarAndamentoGeracao('N');
            }

            if (trim($SinRetornarAssinaturas)!=''){
                $objWSEntradaListarDocumentoDTO->setStrSinRetornarAssinaturas($SinRetornarAssinaturas);
            }else{
                $objWSEntradaListarDocumentoDTO->setStrSinRetornarAssinaturas('N');
            }

            if (trim($SinRetornarPublicacao)!=''){
                $objWSEntradaListarDocumentoDTO->setStrSinRetornarPublicacao($SinRetornarPublicacao);
            }else{
                $objWSEntradaListarDocumentoDTO->setStrSinRetornarPublicacao('N');
            }

            if (trim($SinRetornarDestinatarios)!=''){
                $objWSEntradaListarDocumentoDTO->setStrSinRetornarDestinatarios($SinRetornarDestinatarios);
            }else{
                $objWSEntradaListarDocumentoDTO->setStrSinRetornarDestinatarios('N');
            }

            $objCguRN = new CguRN();
            $objWSRetornoListarDocumentoDTO = $objCguRN->listarDocumento($objWSEntradaListarDocumentoDTO);

            $i = 0;
            $ret = array();
            foreach($objWSRetornoListarDocumentoDTO as $retObjWSRetornoListarDocumentoDTO) {

                $ret[$i]['IdProcedimento'] = $retObjWSRetornoListarDocumentoDTO->getDblIdProcedimento();
                $ret[$i]['ProcedimentoFormatado'] = $retObjWSRetornoListarDocumentoDTO->getStrProcedimentoFormatado();
                $ret[$i]['IdDocumento'] = $retObjWSRetornoListarDocumentoDTO->getDblIdDocumento();
                $ret[$i]['DocumentoFormatado'] = $retObjWSRetornoListarDocumentoDTO->getStrDocumentoFormatado();
                $ret[$i]['LinkAcesso'] = $retObjWSRetornoListarDocumentoDTO->getStrLinkAcesso();

                $ret[$i]['Serie'] = (object)array('IdSerie' => $retObjWSRetornoListarDocumentoDTO->getNumIdSerie(),
                    'Nome' => $retObjWSRetornoListarDocumentoDTO->getStrNomeSerie());

                $ret[$i]['Numero'] = $retObjWSRetornoListarDocumentoDTO->getStrNumero();
                $ret[$i]['Data'] = $retObjWSRetornoListarDocumentoDTO->getDtaGeracaoProtocolo();

                $ret[$i]['UnidadeElaboradora'] = (object)array('IdUnidade' => $retObjWSRetornoListarDocumentoDTO->getNumIdUnidadeGeradora(),
                    'Sigla' => $retObjWSRetornoListarDocumentoDTO->getStrSiglaUnidadeGeradora(),
                    'Descricao' => $retObjWSRetornoListarDocumentoDTO->getStrDescricaoUnidadeGeradora());


                $objAtividadeDTO = $retObjWSRetornoListarDocumentoDTO->getObjAtividadeDTOGeracao();
                if ($objAtividadeDTO != null) {
                    $ret[$i]['AndamentoGeracao'] = (object)array('Descricao' => $objAtividadeDTO->getStrNomeTarefa(),
                        'DataHora' => $objAtividadeDTO->getDthAbertura(),
                        'Unidade' => (object)array('IdUnidade' => $objAtividadeDTO->getNumIdUnidade(),
                            'Sigla' => $objAtividadeDTO->getStrSiglaUnidade(),
                            'Descricao' => $objAtividadeDTO->getStrDescricaoUnidade()),
                        'Usuario' => (object)array('IdUsuario' => $objAtividadeDTO->getNumIdUsuarioOrigem(),
                            'Sigla' => $objAtividadeDTO->getStrSiglaUsuarioOrigem(),
                            'Nome' => $objAtividadeDTO->getStrNomeUsuarioOrigem()));
                } else {
                    $ret[$i]['AndamentoGeracao'] = null;
                }


                $arrObjAssinaturaDTO = $retObjWSRetornoListarDocumentoDTO->getArrObjAssinaturaDTO();
                $arrAssinaturas = array();
                foreach ($arrObjAssinaturaDTO as $objAssinaturaDTO) {
                    $arrAssinaturas[] = (object)array('Nome' => $objAssinaturaDTO->getStrNome(),
                        'CargoFuncao' => $objAssinaturaDTO->getStrTratamento(),
                        'DataHora' => $objAssinaturaDTO->getDthAberturaAtividade());
                }
                $ret[$i]['Assinaturas'] = $arrAssinaturas;

                $objPublicacaoDTO = $retObjWSRetornoListarDocumentoDTO->getObjPublicacaoDTO();
                if ($objPublicacaoDTO != null) {
                    $ret[$i]['Publicacao'] = (object)array('NomeVeiculo' => $objPublicacaoDTO->getStrNomeVeiculoPublicacao(),
                        'Numero' => $objPublicacaoDTO->getNumNumero(),
                        'DataDisponibilizacao' => $objPublicacaoDTO->getDtaDisponibilizacao(),
                        'DataPublicacao' => $objPublicacaoDTO->getDtaPublicacao(),
                        'Estado' => $objPublicacaoDTO->getStrStaEstado(),
                        'ImprensaNacional' => null);

                    if (!InfraString::isBolVazia($objPublicacaoDTO->getNumIdVeiculoIO())) {
                        $ret[$i]['Publicacao']->ImprensaNacional = (object)array('SiglaVeiculo' => $objPublicacaoDTO->getStrSiglaVeiculoImprensaNacional(),
                            'DescricaoVeiculo' => $objPublicacaoDTO->getStrDescricaoVeiculoImprensaNacional(),
                            'Pagina' => $objPublicacaoDTO->getStrPaginaIO(),
                            'Secao' => $objPublicacaoDTO->getStrNomeSecaoImprensaNacional(),
                            'Data' => $objPublicacaoDTO->getDtaPublicacaoIO());
                    }
                } else {
                    $ret[$i]['Publicacao'] = null;
                }

                $arrObjDestinatarioDTO = $retObjWSRetornoListarDocumentoDTO->getArrObjParticipanteDTO();
                $arrDestinatarios = array();
                foreach ($arrObjDestinatarioDTO as $objDestinatarioDTO) {
                    $arrDestinatarios[] = (object)array('IdContato' => $objDestinatarioDTO->getNumIdContato(),
                        'NomeContato' => $objDestinatarioDTO->getStrNomeContato(),
                        'EmailContato' => $objDestinatarioDTO->getStrEmailContato(),
                        'SiglaUnidade' => $objDestinatarioDTO->getStrSiglaUnidade());

                }
                $ret[$i]['Destinatarios'] = $arrDestinatarios;

                $i++;
            }

            //LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());

            return $ret;

        }catch(Exception $e){
            $this->processarExcecao($e);
        }
    }

    /**
     * @param $SiglaSistema
     * @param $IdentificacaoServico
     * @param $IdUnidade
     * @param $ProtocoloProcedimento
     * @param $Interessado
     * @param $DescricaoAssunto
     * @param $IdTipoProcedimento
     * @param $NomeTipoProcedimento
     * @param $ClassificacaoAssunto
     * @param $DataInicialRegistroProcedimento
     * @param $DataFinalRegistroProcedimento
     * @param $UnidadeProcedimentoAberto
     * @return array
     * @throws InfraException
     * @throws SoapFault
     */
    public function listarProcedimentos($SiglaSistema, $IdentificacaoServico, $IdUnidade, $ProtocoloProcedimento, $Interessado, $DescricaoAssunto, $IdTipoProcedimento, $NomeTipoProcedimento, $ClassificacaoAssunto, $DataInicialRegistroProcedimento, $DataFinalRegistroProcedimento, $UnidadeProcedimentoAberto ){

        try{

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            InfraDebug::getInstance()->gravar(__METHOD__);
            InfraDebug::getInstance()->gravar('SIGLA SISTEMA:'.$SiglaSistema);
            InfraDebug::getInstance()->gravar('IDENTIFICACAO SERVICO:'.$IdentificacaoServico);
            InfraDebug::getInstance()->gravar('ID UNIDADE:'.$IdUnidade);
            InfraDebug::getInstance()->gravar('PROTOCOLO PROCEDIMENTO:'.$ProtocoloProcedimento);
            InfraDebug::getInstance()->gravar('INTERESSADO:'.$Interessado);
            InfraDebug::getInstance()->gravar('DESCRICAO ASSUNTO:'.$DescricaoAssunto);
            InfraDebug::getInstance()->gravar('ID TIPO PROCEDIMENTO:'.$IdTipoProcedimento);
            InfraDebug::getInstance()->gravar('NOME TIPO PROCEDIMENTO:'.$NomeTipoProcedimento);
            InfraDebug::getInstance()->gravar('CLASSIFICACAO ASSUNTO:'.$ClassificacaoAssunto);
            InfraDebug::getInstance()->gravar('DATA INICIAL REGISTRO PROCEDIMENTO:'.$DataInicialRegistroProcedimento);
            InfraDebug::getInstance()->gravar('DATA FINAL REGISTRO PROCEDIMENTO:'.$DataFinalRegistroProcedimento);
            InfraDebug::getInstance()->gravar('UNIDADE PROCEDIMENTO ABERTO:'.$UnidadeProcedimentoAberto);

            SessaoSEI::getInstance(false);

            $objServicoDTO = $this->obterServico($SiglaSistema, $IdentificacaoServico);

            if ($IdUnidade!=null){
                $objUnidadeDTO = $this->obterUnidade($IdUnidade, null);
            }else{
                $objUnidadeDTO = null;
            }

            $this->validarAcessoAutorizado(explode(',',str_replace(' ','',$objServicoDTO->getStrServidor())));

            if ($objUnidadeDTO==null){
                SessaoSEI::getInstance()->simularLogin(null, SessaoSEI::$UNIDADE_TESTE, $objServicoDTO->getNumIdUsuario(), null);
            }else{
                SessaoSEI::getInstance()->simularLogin(null, null, $objServicoDTO->getNumIdUsuario(), $objUnidadeDTO->getNumIdUnidade());
            }

            $objProcedimentoDTO = new ProcedimentoDTO();
            $objProcedimentoDTO->setStrProtocoloProcedimentoFormatado($ProtocoloProcedimento);

            $objProcedimentoDTO->setStrDescricaoProtocolo($DescricaoAssunto);
            $objProcedimentoDTO->setNumIdTipoProcedimento($IdTipoProcedimento);
            $objProcedimentoDTO->setStrNomeTipoProcedimento($NomeTipoProcedimento);

            $objWSEntradaListarProcedimentoDTO = new WSEntradaListarProcedimentoDTO();
            $objWSEntradaListarProcedimentoDTO->setObjServicoDTO($objServicoDTO);
            $objWSEntradaListarProcedimentoDTO->setObjProcedimentoDTO($objProcedimentoDTO);
            $objWSEntradaListarProcedimentoDTO->setObjUnidadeDTO($objUnidadeDTO);

            if($Interessado!='') {
                $objWSEntradaListarProcedimentoDTO->setStrInteressado($Interessado);
            }
            else{
                $objWSEntradaListarProcedimentoDTO->setStrInteressado(null);
            }

            if($ClassificacaoAssunto!='') {
                $objWSEntradaListarProcedimentoDTO->setStrClassificacaoAssunto($ClassificacaoAssunto);
            }
            else{
                $objWSEntradaListarProcedimentoDTO->setStrClassificacaoAssunto(null);
            }

            if($DataInicialRegistroProcedimento!='') {
                $objWSEntradaListarProcedimentoDTO->setDtaDataInicialRegistroProcedimento($DataInicialRegistroProcedimento);
            }
            else{
                $objWSEntradaListarProcedimentoDTO->setDtaDataInicialRegistroProcedimento(null);
            }

            if($DataFinalRegistroProcedimento!='') {
                $objWSEntradaListarProcedimentoDTO->setDtaDataFinalRegistroProcedimento($DataFinalRegistroProcedimento);
            }
            else{
                $objWSEntradaListarProcedimentoDTO->setDtaDataFinalRegistroProcedimento(null);
            }

            if($UnidadeProcedimentoAberto!='') {
                $objUnidadeProcedimentoAberto = $this->obterUnidade(null,$UnidadeProcedimentoAberto);
                $objWSEntradaListarProcedimentoDTO->setNumUnidadeProcedimentoAberto($objUnidadeProcedimentoAberto->getNumIdUnidade());
            }
            else{
                $objWSEntradaListarProcedimentoDTO->setNumUnidadeProcedimentoAberto(null);
            }

            $objCguRN = new CguRN();
            $objWSRetornoListarProcedimentoDTO = $objCguRN->listarProcedimento($objWSEntradaListarProcedimentoDTO);

            $ret = array();
            $i = 0;

            foreach($objWSRetornoListarProcedimentoDTO as $retObjWSRetornoListarProcedimentoDTO) {

                $ret[$i]['IdProcedimento'] = $retObjWSRetornoListarProcedimentoDTO->getDblIdProcedimento();
                $ret[$i]['ProcedimentoFormatado'] = $retObjWSRetornoListarProcedimentoDTO->getStrProcedimentoFormatado();

                $ret[$i]['TipoProcedimento'] = (object)array('IdTipoProcedimento' => $retObjWSRetornoListarProcedimentoDTO->getNumIdTipoProcedimento(),
                    'Nome' => $retObjWSRetornoListarProcedimentoDTO->getStrNomeTipoProcedimento());

                //LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());
                $i++;
            }
            return $ret;

        }catch(Exception $e){
            $this->processarExcecao($e);
        }
    }

    /**
     * @param $SiglaSistema
     * @param $IdentificacaoServico
     * @param $IdUnidade
     * @param $ProtocoloProcedimento
     * @return array
     * @throws InfraException
     * @throws SoapFault
     */
    public function listarAndamentos($SiglaSistema, $IdentificacaoServico, $IdUnidade, $ProtocoloProcedimento )
    {

        try {

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            InfraDebug::getInstance()->gravar(__METHOD__);
            InfraDebug::getInstance()->gravar('SIGLA SISTEMA:' . $SiglaSistema);
            InfraDebug::getInstance()->gravar('IDENTIFICACAO SERVICO:' . $IdentificacaoServico);
            InfraDebug::getInstance()->gravar('ID UNIDADE:' . $IdUnidade);
            InfraDebug::getInstance()->gravar('PROTOCOLO PROCEDIMENTO:' . $ProtocoloProcedimento);

            SessaoSEI::getInstance(false);

            $objServicoDTO = $this->obterServico($SiglaSistema, $IdentificacaoServico);

            if ($IdUnidade != null) {
                $objUnidadeDTO = $this->obterUnidade($IdUnidade, null);
            } else {
                $objUnidadeDTO = null;
            }

            $this->validarAcessoAutorizado(explode(',', str_replace(' ', '', $objServicoDTO->getStrServidor())));

            if ($objUnidadeDTO == null) {
                SessaoSEI::getInstance()->simularLogin(null, SessaoSEI::$UNIDADE_TESTE, $objServicoDTO->getNumIdUsuario(), null);
            } else {
                SessaoSEI::getInstance()->simularLogin(null, null, $objServicoDTO->getNumIdUsuario(), $objUnidadeDTO->getNumIdUnidade());
            }

            $objProcedimentoDTO = new ProcedimentoDTO();
            $objProcedimentoDTO->setStrProtocoloProcedimentoFormatado($ProtocoloProcedimento);

            $objWSEntradaListarAndamentosDTO = new WSEntradaListarAndamentosDTO();
            $objWSEntradaListarAndamentosDTO->setObjServicoDTO($objServicoDTO);
            $objWSEntradaListarAndamentosDTO->setObjProcedimentoDTO($objProcedimentoDTO);
            $objWSEntradaListarAndamentosDTO->setObjUnidadeDTO($objUnidadeDTO);

            $objCguRN = new CguRN();
            $objWSRetornoListarAndamentosDTO = $objCguRN->listarAndamentos($objWSEntradaListarAndamentosDTO);

            $ret = array();
            $i = 0;

            /*?> <pre> <? echo var_dump($objWSRetornoListarAndamentosDTO);?> </pre> <?*/

            $ret['IdProcedimento'] = $objWSRetornoListarAndamentosDTO->getDblIdProcedimento();
            $ret['ProcedimentoFormatado'] = $objWSRetornoListarAndamentosDTO->getStrProcedimentoFormatado();

            $ret['TipoProcedimento'] = (object) array('IdTipoProcedimento' => $objWSRetornoListarAndamentosDTO->getNumIdTipoProcedimento(),
                'Nome' => $objWSRetornoListarAndamentosDTO->getStrNomeTipoProcedimento());

            $arrObjAtividadeDTO = $objWSRetornoListarAndamentosDTO->getArrObjAtividadeDTO();
            $arrAtividades = array();

            foreach ($arrObjAtividadeDTO as $objAtividadeDTO) {

                $arrAtividades[] = (object)array('Descricao' => $objAtividadeDTO->getStrNomeTarefa(),
                    'DataHora' => $objAtividadeDTO->getDthAbertura(),
                    'Unidade' => (object)array('IdUnidade'=> $objAtividadeDTO->getNumIdUnidade(),
                        'Sigla' => $objAtividadeDTO->getStrSiglaUnidade(),
                        'Descricao' => $objAtividadeDTO->getStrDescricaoUnidade()),
                    'Usuario' => (object)array('IdUsuario'=> $objAtividadeDTO->getNumIdUsuarioOrigem(),
                        'Sigla' => $objAtividadeDTO->getStrSiglaUsuarioOrigem(),
                        'Nome' => $objAtividadeDTO->getStrNomeUsuarioOrigem()));

                //LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());
                $i++;
            }
            $ret['Andamentos'] = $arrAtividades;

            return $ret;


         }catch(Exception $e){
            $this->processarExcecao($e);
         }
    }

    /**
     * @param $SiglaSistema
     * @param $IdentificacaoServico
     * @param $IdUnidade
     * @param $ProtocoloProcedimento
     * @return array
     * @throws InfraException
     * @throws SoapFault
     */
    public function listarProcedimentosTramitadosParaArea($SiglaSistema, $IdentificacaoServico, $IdUnidade, $arrRequisicaoConsultaTramite, $idUnidadePesquisa )
    {

        try {

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            InfraDebug::getInstance()->gravar(__METHOD__);
            InfraDebug::getInstance()->gravar('SIGLA SISTEMA:' . $SiglaSistema);
            InfraDebug::getInstance()->gravar('IDENTIFICACAO SERVICO:' . $IdentificacaoServico);
            InfraDebug::getInstance()->gravar('ID UNIDADE:' . $IdUnidade);

            SessaoSEI::getInstance(false);

            $objServicoDTO = $this->obterServico($SiglaSistema, $IdentificacaoServico);

            if ($IdUnidade != null) {
                $objUnidadeDTO = $this->obterUnidade($IdUnidade, null);
            } else {
                $objUnidadeDTO = null;
            }

            $this->validarAcessoAutorizado(explode(',', str_replace(' ', '', $objServicoDTO->getStrServidor())));

            if ($objUnidadeDTO == null) {
                SessaoSEI::getInstance()->simularLogin(null, SessaoSEI::$UNIDADE_TESTE, $objServicoDTO->getNumIdUsuario(), null);
            } else {
                SessaoSEI::getInstance()->simularLogin(null, null, $objServicoDTO->getNumIdUsuario(), $objUnidadeDTO->getNumIdUnidade());
            }

            $arrProtocolos = array();
            $ret = array();

            $i = 0;

            foreach($arrRequisicaoConsultaTramite as $requisicaoConsultaTramite){

                $idProcedimento = $requisicaoConsultaTramite->IdProcedimento;
                $dataAbertura = strtotime($requisicaoConsultaTramite->DataAbertura);
                //$idProcedimento = $requisicaoConsultaTramite['IdProcedimento'];
                //$dataAbertura = strtotime($requisicaoConsultaTramite['DataAbertura']);

                $objWSEntradaListarProcedimentosTramitadosDTO = new WSEntradaListarProcedimentosTramitadosDTO();
                $objWSEntradaListarProcedimentosTramitadosDTO->setDblIdProcedimento($idProcedimento);
                $objWSEntradaListarProcedimentosTramitadosDTO->setDthDataReferencia($dataAbertura);
                $objWSEntradaListarProcedimentosTramitadosDTO->setNumIdUnidadePesquisa($idUnidadePesquisa);

                $objCguRN = new CguRN();
                $objWSRetornoListarAndamentosDTO = $objCguRN->listarProcedimentosTramitadosParaArea($objWSEntradaListarProcedimentosTramitadosDTO);

                $arrObjAtividadeDTO = $objWSRetornoListarAndamentosDTO->getArrObjAtividadeDTO();

                //echo "<br><br>Procedimento" . $idProcedimento . "<br>";
                //var_dump($arrObjAtividadeDTO);

                foreach ($arrObjAtividadeDTO as $objAtividadeDTO) {
                    if($objAtividadeDTO->getDthAbertura() >= $dataAbertura){
                        if($objAtividadeDTO->getNumIdUnidade() == $idUnidadePesquisa){
                            $ret[$i]['IdProtocolo'] = $idProcedimento;
                            //$arrProtocolos[] = (object)array('IdProtocolo'=>$requisicaoConsultaTramite->IdProcedimentoPesquisa);
                        }
                    }
                }
                $i++;
            }
            //$ret['IdProtocolo'] = $arrProtocolos;

            return $ret;

        }catch(Exception $e){
            $this->processarExcecao($e);
        }
    }

    private function obterServico($SiglaSistema, $IdentificacaoServico){

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retNumIdUsuario();
        $objUsuarioDTO->setStrSigla($SiglaSistema);
        $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_SISTEMA);

        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

        if ($objUsuarioDTO==null){
            throw new InfraException('Sistema ['.$SiglaSistema.'] não encontrado.');
        }

        $objServicoDTO = new ServicoDTO();
        $objServicoDTO->retNumIdServico();
        $objServicoDTO->retStrIdentificacao();
        $objServicoDTO->retStrSiglaUsuario();
        $objServicoDTO->retNumIdUsuario();
        $objServicoDTO->retStrServidor();
        $objServicoDTO->retStrSinLinkExterno();
        $objServicoDTO->retNumIdContatoUsuario();
        $objServicoDTO->setNumIdUsuario($objUsuarioDTO->getNumIdUsuario());
        $objServicoDTO->setStrIdentificacao($IdentificacaoServico);

        $objServicoRN = new ServicoRN();
        $objServicoDTO = $objServicoRN->consultar($objServicoDTO);

        if ($objServicoDTO==null){
            throw new InfraException('Serviço ['.$IdentificacaoServico.'] do sistema ['.$SiglaSistema.'] não encontrado.');
        }

        return $objServicoDTO;
    }

    private function obterUnidade($IdUnidade, $SiglaUnidade){

        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeDTO->retNumIdUnidade();
        $objUnidadeDTO->retStrSigla();
        $objUnidadeDTO->retStrDescricao();

        if($IdUnidade!=null) {
            $objUnidadeDTO->setNumIdUnidade($IdUnidade);
        }
        if($SiglaUnidade!=null){
            $objUnidadeDTO->setStrSigla($SiglaUnidade);
        }

        $objUnidadeRN = new UnidadeRN();
        $objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

        if ($objUnidadeDTO==null){
            throw new InfraException('Unidade ['.$IdUnidade.'] não encontrada.');
        }

        return $objUnidadeDTO;
    }
}

/*
 $servidorSoap = new SoapServer("sei.wsdl",array('encoding'=>'ISO-8859-1'));
 $servidorSoap->setClass("SeiWS");

 //Só processa se acessado via POST
 if ($_SERVER['REQUEST_METHOD']=='POST') {
 $servidorSoap->handle();
 }
*/

$servidorSoap = new BeSimple\SoapServer\SoapServer( "cgu.wsdl", array ('encoding'=>'ISO-8859-1',
    'soap_version' => SOAP_1_1,
    'attachment_type'=>BeSimple\SoapCommon\Helper::ATTACHMENTS_TYPE_MTOM));
$servidorSoap->setClass ( "CguWS" );

//Só processa se acessado via POST
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $servidorSoap->handle($HTTP_RAW_POST_DATA);
}
?>