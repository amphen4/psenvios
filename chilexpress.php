<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Chilexpress extends Module
{
    public function __construct()
    {
        $this->name = 'chilexpress';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'amphen4';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Chilexpress');
        $this->description = $this->l('Módulo de envío personalizado para Chilexpress.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() &&
               $this->registerHook('actionCarrierUpdate') &&
               $this->registerHook('displayCarrierExtraContent') &&
               $this->addCarrier();
    }

    public function uninstall()
    {
        $carrier = new Carrier(Configuration::get('CHILEXPRESS_CARRIER_ID'));
        $carrier->delete();

        Configuration::deleteByName('CHILEXPRESS_CARRIER_ID');
        
        return parent::uninstall();
    }

    private function addCarrier()
    {
        $carrier = new Carrier();
        $carrier->name = 'Chilexpress';
        $carrier->is_module = true;
        $carrier->active = true;
        $carrier->range_behavior = 0;
        $carrier->need_range = true;
        $carrier->shipping_external = true;
        $carrier->external_module_name = $this->name;
        $carrier->delivery_price = 0;
        $carrier->max_width = 0;
        $carrier->max_height = 0;
        $carrier->max_depth = 0;
        $carrier->max_weight = 0;
        $carrier->grade = 0;
        $carrier->delay[Configuration::get('PS_LANG_DEFAULT')] = 'Envío por Chilexpress';
        
        if ($carrier->add()) {
            Configuration::updateValue('CHILEXPRESS_CARRIER_ID', (int)$carrier->id);

            $groups = Group::getGroups(true);
            foreach ($groups as $group) {
                Db::getInstance()->insert('carrier_group', array(
                    'id_carrier' => (int)$carrier->id,
                    'id_group' => (int)$group['id_group']
                ));
            }

            $rangePrice = new RangePrice();
            $rangePrice->id_carrier = $carrier->id;
            $rangePrice->delimiter1 = '0';
            $rangePrice->delimiter2 = '10000';
            $rangePrice->add();

            $rangeWeight = new RangeWeight();
            $rangeWeight->id_carrier = $carrier->id;
            $rangeWeight->delimiter1 = '0';
            $rangeWeight->delimiter2 = '10000';
            $rangeWeight->add();

            return true;
        }

        return false;
    }

    public function hookActionCarrierUpdate($params)
    {
        if ((int)$params['id_carrier'] == (int)Configuration::get('CHILEXPRESS_CARRIER_ID')) {
            Configuration::updateValue('CHILEXPRESS_CARRIER_ID', (int)$params['carrier']->id);
        }
    }

    public function hookDisplayCarrierExtraContent($params)
    {
        if ($params['carrier']['id'] == Configuration::get('CHILEXPRESS_CARRIER_ID')) {
            $comunas = $this->getComunas();
            $this->context->smarty->assign(array(
                'comunas' => $comunas,
            ));

            return $this->display(__FILE__, 'views/templates/hook/chilexpress_comunas.tpl');
        }
    }

    private function getComunas()
    {
        $jsonPath = _PS_MODULE_DIR_ . $this->name . '/comunas.json';
        $json = file_get_contents($jsonPath);
        return json_decode($json, true);
    }
}
