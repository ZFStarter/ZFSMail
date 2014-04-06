<?php
/**
 * User: naxel
 * Date: 02.04.14 18:44
 */

namespace ZFStarterMail\Form;

use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\Validator\Regex;

class Create extends Form
{
    public function __construct()
    {
        parent::__construct('create_mail_template');

        $this->setAttribute('method', 'post');

        $this->initElements();
        $this->initInputFilter();
    }

    protected function initElements()
    {
        $this->addAliasElement();
        $this->addDescriptionElement();
        $this->addSubjectElement();
        $this->addBodyHtmlElement();
        $this->addBodyTextElement();
        $this->addFromNameElement();
        $this->addFromEmailElement();
        $this->addSubmitElement();
    }

    protected function initInputFilter()
    {
        $this->initAliasInput();
        $this->initDescriptionInput();
        $this->initSubjectInput();
        $this->initBodyHtmlInput();
        $this->initBodyTextInput();
        $this->initFromNameInput();
        $this->initFromEmailInput();

    }
    //region Elements

    protected function addAliasElement()
    {
        $element = $this->add(
            array(
                'name' => 'alias',
                'attributes' => array(
                    'type' => 'text',
                ),
                'options' => array(
                    'label' => 'Alias:',
                ),
            )
        );
        return $element;
    }

    protected function addDescriptionElement()
    {
        $element = $this->add(
            array(
                'name' => 'description',
                'attributes' => array(
                    'type' => 'text',
                ),
                'options' => array(
                    'label' => 'Description:',
                ),
            )
        );
        return $element;
    }

    protected function addSubjectElement()
    {
        $element = $this->add(
            array(
                'name' => 'subject',
                'attributes' => array(
                    'type' => 'text',
                ),
                'options' => array(
                    'label' => 'Subject:',
                ),
            )
        );
        return $element;
    }

    protected function addBodyHtmlElement()
    {
        $element = $this->add(
            array(
                'name' => 'bodyHtml',
                'type' => 'textarea',
                'attributes' => array(
                    'id' => 'bodyHtml',
                ),
                'options' => array(
                    'label' => 'Content:',
                ),
            )
        );
        return $element;
    }

    protected function addBodyTextElement()
    {
        $element = $this->add(
            array(
                'name' => 'bodyText',
                'type' => 'textarea',
                'options' => array(
                    'label' => 'Body (text):',
                ),
            )
        );
        return $element;
    }

    protected function addFromNameElement()
    {
        $element = $this->add(
            array(
                'name' => 'fromName',
                'attributes' => array(
                    'type' => 'text',
                ),
                'options' => array(
                    'label' => 'From Name:',
                ),
            )
        );
        return $element;
    }

    protected function addFromEmailElement()
    {
        $element = $this->add(
            array(
                'name' => 'fromEmail',
                'attributes' => array(
                    'type' => 'text',
                ),
                'options' => array(
                    'label' => 'From Email:',
                ),
            )
        );
        return $element;
    }


    protected function addSubmitElement()
    {
        $element = $this->add(
            array(
                'name' => 'submit',
                'type' => 'button',
                'attributes' => array('type' => 'submit', 'class' => 'btn-primary'),
                'options' => array('label' => 'Create')
            )
        );
        return $element;
    }
    //endregion

    //region InputFilters

    protected function initAliasInput()
    {
        /** @var Input $input */
        $input = $this->getInputFilter()->get('alias');
        $input->setRequired(true);
        $input->setAllowEmpty(false);
        $input->getFilterChain()
            ->attachByName('StripTags')
            ->attachByName('stringTrim');
        $input->getValidatorChain()
            ->attachByName(
                'regex',
                array(
                    'pattern' => '/^[a-z0-9\-\_]+$/i',
                    'messageTemplates' => array(
                        Regex::INVALID => 'Invalid page alias',
                        Regex::NOT_MATCH => 'Invalid page alias'
                    )
                )
            );
        return $input;
    }


    /**
     * @return Input
     */
    protected function initDescriptionInput()
    {
        /** @var Input $input */
        $input = $this->getInputFilter()->get('description');
        $input->setRequired(true);
        return $input;
    }

    /**
     * @return Input
     */
    protected function initSubjectInput()
    {
        /** @var Input $input */
        $input = $this->getInputFilter()->get('subject');
        $input->setRequired(true);
        return $input;
    }

    /**
     * @return Input
     */
    protected function initBodyHtmlInput()
    {
        /** @var Input $input */
        $input = $this->getInputFilter()->get('bodyHtml');
        $input->setRequired(true);
        return $input;
    }

    /**
     * @return Input
     */
    protected function initBodyTextInput()
    {
        /** @var Input $input */
        $input = $this->getInputFilter()->get('bodyText');
        $input->setRequired(true);
        return $input;
    }

    /**
     * @return Input
     */
    protected function initFromNameInput()
    {
        /** @var Input $input */
        $input = $this->getInputFilter()->get('fromName');
        $input->setRequired(false);
        return $input;
    }

    /**
     * @return Input
     */
    protected function initFromEmailInput()
    {
        /** @var Input $input */
        $input = $this->getInputFilter()->get('fromEmail');
        $input->setRequired(false);
        return $input;
    }
    //endregion
}
