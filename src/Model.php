<?php

namespace Package;

//set_time_limit(0);
ini_set('max_execution_time', 700); //300 seconds = 5 minutes

define('URL_PROVA', 'https://www.pciconcursos.com.br/provas/');
define('URL_ZIP_FILE', 'https://www.pciconcursos.com.br/provas/download/');
define('URL_DOWNLOAD', 'https://arquivo.pciconcursos.com.br/provas/');
define('REGEX_URL', '/https:\/\/www.pciconcursos.com.br\/provas\/download\/[a-z0-9_-][a-z0-9_-][a-z0-9_-][a-z0-9_-]*/');
define('REGEX_LINK_ZIP', '/https:\/\/arquivo\.pciconcursos\.com\.br\/provas\/[0-9a-z]+\/[0-9a-z]+\/[a-zA-Z0-9_]+\.zip/');
define('REGEX_NUM_PAGE', '/Mostrando página [0-9]+ de [0-9]+/');
define('REGEX_URL_FILTROS', "/https:\/\/www.pciconcursos.com.br\/provas\/download\/[a-z0-9_-][a-z0-9_-][a-z0-9_-][a-z0-9_-]+");

//ALIMENTAR FORM SELECT

define('REGEX_PROVA_CARGO', "/<a href=\"(https:\/\/www.pciconcursos.com.br\/provas\/[a-z\-]*\") title=\"([0-9a-zA-Z\-\s\ç\ã\ó\á\ú\õ\í\ê\é\ª\º\â]*)\">([0-9a-zA-Z\-\s\ç\ã\ó\á\ú\õ\í\ê\é\ª\º\â]*)<\/a>/");
define('REGEX_PROVA_TRIBUNAL', "/<a href=\"(https:\/\/www\.pciconcursos\.com\.br\/provas\/[a-z\-]{2,})\">([a-zA-Z\s\ç\á\é\-]{2,})<\/a>/");
define('REGEX_PROVA_ORGANIZADORA', "/<a href=\"(https:\/\/www\.pciconcursos\.com\.br\/provas\/[cesp|esaf|fcc|vunesp]{2,})\">([a-zA-Z\-\s\ç\ã\á]*)<\/a>/");
define('REGEX_PROVA_ANO', "/<a href=\"(https:\/\/www.pciconcursos.com.br\/provas\/[0-9]{4})\">([0-9]{4})<\/a>/");

class Model {

    public $_modeloUrlParaPaginar;
    public $_quantidadeDePaginasEncontradas;
    public $_conteudoHtmlDaPaginaEncontrada;
    public $_modeloUrlComFiltro;
    public $_conteudoDaPaginaComZip;
    public $_linksEncontradosNaPagina;
    public $_listaDeUrlsDeArquivoZip;
    //FILTROS
    public $_cargo;
    public $_banca;
    public $_ano;

    function __construct() {
        
    }

    function setUrlParaBuscarComFiltro($filtro) {
        return URL_PROVA . $filtro;
    }

    function getUrlComFiltro() {

        return $this->_modeloUrlComFiltro;
    }

    function getHtmlDaPagina($url) {
        $content = file_get_contents($url);
        return $content;
    }

    function getLinksDoHtmlDaPagina($content) {

        preg_match_all(REGEX_URL, $content, $linksPage);
        return $linksPage;
    }

    function getLinkZip($string) {
        preg_match_all(REGEX_LINK_ZIP, $string, $matches, PREG_SET_ORDER, 0);
        return $matches;
    }

    /*
     * busca geral
     */

    function getLinkFiltro($url, $filtro) {
        
        //echo 'url:'.$url;
        
        $reg = "/https:\/\/www.pciconcursos.com.br\/provas\/download\/[a-z0-9_-]*{$filtro}[a-z0-9_-]*/";
      
        preg_match_all($reg, $url, $matches, PREG_SET_ORDER, 0);

        if (empty($matches)) {
            return false;
        }else{
         return $matches[0][0];
        }
    }

    public function getLinksZipDaPagina($listaUrlsParaBuscarZip = []) {

        $listaUrlZip = [];

        foreach ($listaUrlsParaBuscarZip as $key => $url) {
            foreach ($url as $key => $u) {
                $content = $this->getConteudoHtmlDaPagina($u);
                $array = $this->getLinkZip($content);
                $zip = $array[0][0];
                array_push($listaUrlZip, $zip);
            }
        }
        return $listaUrlZip;
    }

    function getQuantidadeDePaginasEncontradas($content) {
        if (empty($content)) {
            echo 'sem content';
        }


        preg_match_all(REGEX_NUM_PAGE, $content, $pagina);

        if (empty($pagina[0])) {
            return 0;
        } else {

            return trim(substr($pagina[0][0], 22, 24));
        }
    }

    //##########################################################################   
    //alimentar select form
    function getConteudoDeHtmlDaPaginaInicial() {
        return $this->getHtmlDaPagina(URL_PROVA);
    }

    //alimentar select form
    function getLinksProvaPorCargo() {
        $content = $this->getConteudoDeHtmlDaPaginaInicial();

        //possui 3 grupos : url , title, Texto do link        

        preg_match_all(REGEX_PROVA_CARGO, $content, $listaLinks);

        return $listaLinks;
    }

//alimentar select form
    function getLinksProvaDeTribunais() {
        $content = $this->getConteudoDeHtmlDaPaginaInicial();
        //possui 3 grupos : url , title, Texto do link        

        preg_match_all(REGEX_PROVA_TRIBUNAL, $content, $listaLinks);

        return $listaLinks;
    }

    //alimentar select form
    function getLinksProvaPorOrganizadora() {
        $content = $this->getConteudoDeHtmlDaPaginaInicial();

        //possui 3 grupos : url , title, Texto do link        

        preg_match_all(REGEX_PROVA_ORGANIZADORA, $content, $listaLinks);

        return $listaLinks;
    }

    function getLinksProvaPorAno() {
        $content = $this->getConteudoDeHtmlDaPaginaInicial();
        //possui 3 grupos : url , title, Texto do link        

        preg_match_all(REGEX_PROVA_ANO, $content, $listaLinks);

        return $listaLinks;
    }

    function buscarLinksEmHtml($html) {

        $listaLinks = $this->getLinksDoHtmlDaPagina($html);

        return $listaLinks[0];
    }

    function buscarLinksEmHtmlVariasPaginas($htmlPaginaUm, $url, $quantidadeDePaginas) {
        $listaLinks = [];

        $listaLinks[0] = $this->buscarLinksEmHtml($htmlPaginaUm);

        //buscar links em paginação 


        for ($index = 2; $index <= $quantidadeDePaginas; $index++) {
            //definir paginação 
            $urlPage = $url . '/' . $index;

            //busca a partir da pagina 2
            $html = $this->getHtmlDaPagina($urlPage);

            $linksToZip = $this->buscarLinksEmHtml($html);

            $listaLinks[$index - 1] = $linksToZip;
        }

        return $listaLinks;
    }

       
    
     public function buscarUrlComFiltroAno($listaUrls, $filtro) {


        $_listaUrlsFinalZip = [];
        $_listaNegada = [];

        foreach ($listaUrls as  $urls) {
              
           // var_dump($urls);

                
                foreach ($urls as $key => $url) {
                  
                    $filtrar = $this->getLinkFiltro($url,$filtro);
                  
                    if($filtrar){
                        
                    array_push($_listaUrlsFinalZip, $url);
                    
                     }else{                    
                    array_push($_listaNegada, $url);
     
                    }
                }
              
              
           
        }
   
        return $_listaUrlsFinalZip;
        }
    
        public function buscarUrlComFiltroAnoUmaPagina($listaUrls, $filtro) {


        $_listaUrlsFinalZip = [];
        $_listaNegada = [];

        foreach ($listaUrls as  $url) {
              
          //  var_dump($url);

               
                  
                    $filtrar = $this->getLinkFiltro($url,$filtro);
                  
                    if($filtrar){
                        
                    array_push($_listaUrlsFinalZip, $url);
                    
                     }else{ 
                                        
                    array_push($_listaNegada, $url);
     
                    }
                
              
              
           
        }
   
       // var_dump($_listaNegada);

        return $_listaUrlsFinalZip;
        }
    
    

    function buscarLinkZip($listaUrlParaBuscarZip) {

        $listaZip = [];
        
        foreach ($listaUrlParaBuscarZip as $key => $url) {
            $content = $this->getHtmlDaPagina($url);
            $array = $this->getLinkZip($content);
            $zip = $array[0][0];
            array_push($listaZip, $zip);
        }

        //mostrar zips
        foreach ($listaZip as $value) {
            //echo $value . '<br/>';
        }
        
        return $listaZip;
    }
    
    function print_array($array){
         //mostrar zips
        foreach ($array as $value) {
            echo $value . '<br/>';
        }
    }

    function print_log($valor,$label = ''){
        if(empty($label)){
            $label = 'valor:';
        }

        echo "<script>console.log(".$label." => ".$valor.")</script>";
    }

}
