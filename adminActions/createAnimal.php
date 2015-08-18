<?php

class createAnimal {

    public function html() {
        ptln('<h1>createAnimal.php</h1>');
        $form = new \dokuwiki\Form\Form();

        //$form->addLabel('animal name','plugin__farmer__animalname')->addClass('block');
        $form->addTextInput('animalname','animal name')->addClass('block edit')->attr('placeholder','animal name');

        $form->addTag('br');

        //$form->addElement(new \dokuwiki\Form\LabelElement('animal subdomain'))->attr('for', 'plugin__farmer__animalsubdomain');
        $form->addTextInput('animalsubdomain','animal subdomain')->addClass('block edit')->attr('placeholder','animal subdomain');

        $form->addTag('br');

        $form->addPasswordInput('adminPassword','Password for admin account')->addClass('block edit')->attr('placeholder','Password for admin account');

        $form->addRadioButton('serversetup', 'Subdomain')->val('subdomain')->attr('type','radio')->addClass('block');

        $form->addRadioButton('serversetup', 'htaccess')->val('htaccess')->attr('type','radio')->addClass('block');

        $form->addButton('farmer__submit','Submit')->attr('type','submit')->val('newAnimal');

        $form->addButton('farmer__reset','Reset')->attr('type','reset');

        echo $form->toHTML();
    }

}
