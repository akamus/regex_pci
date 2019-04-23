<?php
namespace Package;

require_once '../vendor/autoload.php';

use Package\Model;


class Controller {

    public $regex = '';
    
    public function __construct() {

        $this->regex = new \Package\Model;
        
    }
    
    public function processar($filtros = []) {
        if(empty($filtros)){            
        $filtros = ['java', '2008'];
        }
        
        var_dump($filtros);
        
        $urlPadrao = $this->regex->setUrlParaBuscarComFiltro($filtros[0]);

        $html = $this->regex->getHtmlDaPagina($urlPadrao);

        $quantidadeDePaginas = $this->regex->getQuantidadeDePaginasEncontradas($html);

// BUSCA DE URLS PRIMEIRO FILTRO - > cargo exemplo

        $_urlsParaBuscarZip = [];
        $listaUrlAposFiltroAno = '';

        if ($quantidadeDePaginas == 1) { //caso retorne Uma página

            $_urlsParaBuscarZip = $this->regex->buscarLinksEmHtml($html);
            $listaUrlAposFiltroAno = $this->regex->buscarUrlComFiltroAnoUmaPagina($_urlsParaBuscarZip, $filtros[1]);
        
            
        } elseif ($quantidadeDePaginas > 1) { //busca por paginação 
            
            $_urlsParaBuscarZip = $this->regex->buscarLinksEmHtmlVariasPaginas($html, $urlPadrao, $quantidadeDePaginas);
             $listaUrlAposFiltroAno = $this->regex->buscarUrlComFiltroAno($_urlsParaBuscarZip, $filtros[1]);

             } else {
            echo 'not found!';
            exit();
        }

       

// MOSTRAR URLS ZIP

        $listaZipFinal = $this->regex->buscarLinkZip($listaUrlAposFiltroAno);

       // var_dump($listaZipFinal);
        echo '******* Copie e cole em um Gerenciador de Download!!! ****** <br/><br/>';
    
      $this->regex->print_array($listaZipFinal);

    }


}
