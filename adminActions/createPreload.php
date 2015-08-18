<?php

class createPreload {

    public function createPreloadPHP($animalpath) {
        // todo: check if animalpath is writable
        io_makeFileDir($animalpath . '/foo');

        // todo: move template download to its own function
        file_put_contents($animalpath . '/_animal.zip',fopen('https://www.dokuwiki.org/_media/dokuwiki_farm_animal.zip','r'));
        $zip = new ZipArchive();
        $zip->open($animalpath.'/_animal.zip');
        $zip->extractTo($animalpath);
        $zip->close();
        unlink($animalpath.'/_animal.zip');


        $content = "<?php\n";
        $content .= "if(!defined('DOKU_FARMDIR')) define('DOKU_FARMDIR', '$animalpath');\n";
        $content .= "include(fullpath(dirname(__FILE__)).'/farm.php');\n";

        io_saveFile(DOKU_INC . 'inc/preload.php',$content);
    }

    public function html() {
        ptln('<h1>createPreload.php</h1>');
        $form = new \dokuwiki\Form\Form();

        $form->addTagOpen('div class="form-group"');
        $form->addELement(new \dokuwiki\Form\LabelElement('farm dir'))->attr('for', 'plugin__farmer__farmdir');
        $form->addTextInput('farmdir')->addClass('form-control')->attr('placeholder','farm dir')->id('plugin__farmer__farmdir');
        $form->addTagClose('div');

        $form->addButton('farmer__submit','Submit')->attr('type','submit')->val('newPerload');

        $form->addButton('farmer__reset','Reset')->attr('type','reset');

        echo $form->toHTML();
    }

}
