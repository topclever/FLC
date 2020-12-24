<?php
global $global, $config;
if(!isset($global['systemRootPath'])){
    require_once '../videos/configuration.php';
}
if(!empty($_GET['PHPSESSID'])){
    session_write_close();
    session_id($_GET['PHPSESSID']);
    _error_log("captcha: session_id changed to ". $_GET['PHPSESSID']);
    session_start();
}
class Captcha{
    private $largura, $altura, $tamanho_fonte, $quantidade_letras;

    function __construct($largura, $altura, $tamanho_fonte, $quantidade_letras) {
        $this->largura = $largura;
        $this->altura = $altura;
        $this->tamanho_fonte = $tamanho_fonte;
        $this->quantidade_letras = 1;//$quantidade_letras;  tempcode
    }


    public function getCaptchaImage() {
        global $global;
        header('Content-type: image/jpeg');
        $imagem = imagecreate($this->largura,$this->altura); // define a largura e a altura da imagem
        $fonte = $global['systemRootPath'] . 'objects/monof55.ttf'; //voce deve ter essa ou outra fonte de sua preferencia em sua pasta
        $preto  = imagecolorallocate($imagem, 0, 0, 0); // define a cor preta
        $branco = imagecolorallocate($imagem, 255, 255, 255); // define a cor branca

        // define a palavra conforme a quantidade de letras definidas no parametro $quantidade_letras
        //$letters = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz23456789';
        // tempcode captcha letter
        // $letters = 'ABCDEFGHIJKLMNOPQRSTUVWLXZ23456789';
        // $palavra = substr(str_shuffle($letters), 0, ($this->quantidade_letras));

        //tempcode math captcha
        $letters = '0123456789';
        $chracters = array();

        if (empty($_SESSION['language'])) {
            $lang = 'us';
        } else {
            $lang = $_SESSION['language'];
        }
        if($lang == 'us') {
            array_push($chracters, 'zero');
            array_push($chracters, "one");
            array_push($chracters, 'two');
            array_push($chracters, 'three');
            array_push($chracters, 'four');
            array_push($chracters, 'five');
            array_push($chracters, 'six');
            array_push($chracters, 'seven');
            array_push($chracters, 'eight');
            array_push($chracters, 'nine'); 
        } else {
            array_push($chracters, iconv("UTF-8", "ISO-8859-1//TRANSLIT", 'zéro'));
            array_push($chracters, "une");
            array_push($chracters, 'deux');
            array_push($chracters, 'Trois');
            array_push($chracters, 'quatre');
            array_push($chracters, 'cinq');
            array_push($chracters, 'six');
            array_push($chracters, 'Sept');
            array_push($chracters, 'huit');
            array_push($chracters, 'neuve');
        }

        $number1 = substr(str_shuffle($letters), 0, ($this->quantidade_letras));
        $number2 = substr(str_shuffle($letters), 0, ($this->quantidade_letras));
        $palavra = $number1."+";
        $palavra .= $chracters[$number2]."=";

        // tempcode contact captcha
        // if(User::isAdmin()){
        //     $palavra = "admin";
        // }
        _session_start();
        $_SESSION["palavra"] = $number1 + $number2; //$palavra; // atribui para a sessao a palavra gerada
        //_error_log("getCaptchaImage: ".$palavra." - session_name ". session_name()." session_id: ". session_id());
        for ($i = 1; $i <= strlen($palavra); $i++) {
            imagettftext(
                $imagem,
                $this->tamanho_fonte,
                rand(-10, 10),
                ($this->tamanho_fonte*$i),
                ($this->tamanho_fonte + 10),
                $branco,
                $fonte,
                substr($palavra, ($i - 1), 1)
            ); // atribui as letras a imagem
        }
        imagejpeg($imagem); // gera a imagem
        imagedestroy($imagem); // limpa a imagem da memoria
        //_error_log("getCaptchaImage _SESSION[palavra] = ($_SESSION[palavra]) - session_name ". session_name()." session_id: ". session_id());
    }

    static public function validation($word) {
        if(User::isAdmin()){
            return true;
        }
        _session_start();
        if(empty($_SESSION["palavra"])){
            _error_log("Captcha validation Error: you type ({$word}) and session is empty - session_name ". session_name()." session_id: ". session_id());
            return false;
        }
        $validation = (strcasecmp($word, $_SESSION["palavra"]) == 0);
        if(!$validation){
            _error_log("Captcha validation Error: you type ({$word}) and session is ({$_SESSION["palavra"]})- session_name ". session_name()." session_id: ". session_id());
        }
        return $validation;
    }

}
