<?php
/**
 * User: naxel
 * Date: 03.04.14 10:42
 */

namespace ZFStarterMail\Model;

use ZFStarterMail\Exception\MailInvalidArgumentException;
use ZFStarterMail\Exception\MailTemplateEmptyException;
use ZFStarterMail\Exception\MailTemplateNotFoundException;
use ZFStarterMail\Model\Manager\MailTemplatesManager;
use Users\Model\Object\User;
use Zend\ServiceManager\ServiceManager;

class Mail
{

    /**
     * @param array $data
     * @throws \ZFStarterMail\Exception\MailInvalidArgumentException
     */
    public static function checkParams($data)
    {
        if (!is_array($data)) {
            throw new MailInvalidArgumentException('Incorrect array of email data');
        }
        if (!isset($data['templateName']) || !$data['templateName']) {
            throw new MailInvalidArgumentException('Not found key "templateName"');
        }
        if (!isset($data['toEmail']) || !$data['toEmail']) {
            throw new MailInvalidArgumentException('Not found key "toEmail"');
        }
        if (!isset($data['toName']) || !$data['toName']) {
            throw new MailInvalidArgumentException('Not found key "toName"');
        }
        if (!isset($data['templateName']) || !$data['templateName']) {
            throw new MailInvalidArgumentException('Not found key "templateName"');
        }
    }


    /**
     * @param ServiceManager $serviceManager
     * @param $data
     * @throws \ZFStarterMail\Exception\MailTemplateNotFoundException
     *
     * Example:
     * $data = array(
     *     'templateName' => 'registration',
     *     'toEmail' => 'test@test.com',
     *     'toName' => 'Name',
     *     'params' => array(
     *         'host' => $_SERVER['HTTP_HOST'],
     *     ),
     * );
     * Mail::sendMail($this->getServiceLocator(), $data);
     */
    public static function sendMail(ServiceManager $serviceManager, $data)
    {
        self::checkParams($data);

        /** @var MailTemplatesManager $manager */
        $manager = new MailTemplatesManager();
        $manager->setServiceManager($serviceManager);
        /** @var MailTemplate $template */
        $template = $manager->findByAlias($data['templateName']);
        if (!$template) {
            throw new MailTemplateNotFoundException('Not found mail template');
        }
        $template->toEmail = $data['toEmail'];
        $template->toName = $data['toName'];
        if (isset($data['params']) && is_array($data['params'])) {
            foreach ($data['params'] as $param => $value) {
                $template->assign($param, $value);
            }
        }

        if ($template->signature) {
            self::assignLayout($template);
        }
        $template->send($serviceManager);
    }

    /**
     * Get Layout
     *
     */
    public static function getLayout()
    {
        $layout = realpath(__DIR__ . '/../../../templates/zf-starter-mail/layout.phtml');
        if (!is_file($layout)) {
            throw new MailTemplateNotFoundException('Not found mail template');
        }
        $content = file_get_contents($layout);
        if (!$content) {
            throw new MailTemplateEmptyException('Empty mail template');
        }
        return $content;
    }

    /**
     * Assign Layout
     *
     * @param  MailTemplate $template
     */
    public static function assignLayout(MailTemplate $template)
    {
        if ($layout = self::getLayout()) {
            $template->bodyHtml = str_replace(
                '%body%',
                $template->bodyHtml,
                $layout
            );
        }
    }
}
