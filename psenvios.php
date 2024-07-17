<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class PsEnvios extends Module
{
    public function __construct()
    {
        $this->name = 'psenvios';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Alejandro Arancibia';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('PS Envios', [], 'Modules.Mymodule.Admin');
        $this->description = $this->trans('Módulo para integrar envios de carriers Chilenos.', [], 'Modules.Mymodule.Admin');

        $this->confirmUninstall = $this->trans('Estás seguro que quieres desinstalar el módulo?', [], 'Modules.Mymodule.Admin');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->trans('No name provided', [], 'Modules.Mymodule.Admin');
        }
    }

}
