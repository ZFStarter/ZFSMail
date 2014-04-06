<?php
/**
 * User: naxel
 * Date: 03.04.14 17:03
 */

namespace ZFStarterMail\Form;

class Edit extends Create
{
    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct();

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Hidden',
                'name' => 'id',
            )
        );
    }
}
