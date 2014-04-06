<?php
namespace ZFStarterMail\Model\Manager;

use Common\ServiceManager\AbstractService;
use DomainObject\Gateway\DomainObjectTableGateway;
use DomainObject\ResultSet\DomainObjectResultSet;
use SelectOptions\SelectOptions;
use ZFStarterMail\Model\MailTemplate;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Paginator\Adapter\DbTableGateway;

/**
 * Class MailTemplatesManager
 * @package ZFStarterMail\Model\Manager
 */
class MailTemplatesManager extends AbstractService
{
    /**
     * @return DomainObjectTableGateway
     */
    protected function getGateway()
    {
        return $this->serviceManager->get('MailTemplatesGateway');
    }

    /**
     * @param string $alias
     *
     * @return MailTemplate|null
     */
    public function findByAlias($alias)
    {
        return $this->getGateway()->select(array('alias' => $alias))->current();
    }

    /**
     * @param int $id
     *
     * @return MailTemplate|null
     */
    public function getById($id)
    {
        return $this->getGateway()->select(array('id' => $id))->current();
    }

    /**
     * @param array|null $order
     *
     * @return DbTableGateway
     */
    public function getPaginatorAdapter($order)
    {
        return new DbTableGateway($this->getGateway(), null, $order);
    }

    /**
     * Create mail template
     *
     * @param \Users\Model\Object\User $user
     * @param array             $data
     *
     * @return null|MailTemplate
     */
    public function createMailTemplate($user, $data)
    {
        $data['created'] = date('Y-m-d H:i:s');
        $data['creator'] = $user->id;

        $mailTemplate = new MailTemplate($data);

        if (!$this->getGateway()->insertObject($mailTemplate)) {
            return null;
        }

        $mailTemplate->id = $this->getGateway()->getLastInsertValue();

        return $mailTemplate;
    }

    /**
     * Update mail template
     *
     * @param \Users\Model\Object\User $user
     * @param array             $data
     *
     * @return null|MailTemplate
     */
    public function updateMailTemplate($user, $data)
    {
        $data['updated'] = date('Y-m-d H:i:s');
        $data['updater'] = $user->id;

        $mailTemplate = new MailTemplate($data);

        if (!$this->getGateway()->updateObject($mailTemplate)) {
            return null;
        }

        return $mailTemplate;
    }

    /**
     * @param SelectOptions $options
     *
     * @return DomainObjectResultSet
     */
    public function getMailTemplates($options)
    {
        $pagesCollection = $this->getGateway()->select(
            function (Select $select) use ($options) {
                foreach ($options->getSortOptionsArray() as $sortOption) {
                    $select->order(array($sortOption->getField() => $sortOption->getDirection()));
                }

                $select
                    ->where(
                        function (Where $where) use ($options) {
                            foreach ($options->getFilterOptionsArray() as $filter) {
                                switch ($filter->getField()) {
                                    case 'alias':
                                        $where->like('alias', $filter->getValue() . '%');
                                        break;
                                    case 'title':
                                        $where->like('title', $filter->getValue() . '%');
                                        break;
                                }
                            }
                        }
                    );
                $select->offset($options->getOffsetOptions()->getOffset());
                $select->limit($options->getLimitOptions()->getLimit());
            }
        );

        return $pagesCollection;
    }

    /**
     * @param int $id
     */
    public function deleteMailTemplate($id)
    {
        $this->getGateway()->delete(array('id' => $id));
    }
}
