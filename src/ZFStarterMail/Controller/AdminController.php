<?php
namespace ZFStarterMail\Controller;

use Grid\Grid;
use Grid\Column;
use ZFStarterMail\Model\MailTemplate;
use ZFStarterMail\Model\Manager\MailTemplatesManager;
use ZFStarterMail\Form\Create;
use ZFStarterMail\Form\Edit;
use Zend\Paginator\Adapter\Null;
use Zend\Paginator\Paginator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Controller\Plugin\BootboxConfirm;
use Application\Controller\Plugin\JsRedirect;

/**
 * Class AdminController
 * @method BootboxConfirm bootboxConfirm()
 * @method JsRedirect jsRedirect()
 * @package ZFStarterMail\Controller
 */
class AdminController extends AbstractActionController
{

    //Pages list
    public function indexAction()
    {
        $viewModel = new ViewModel();
        $grid = new Grid();
        $grid->setTemplate('layout/back/grid');
        $grid->setColumns(
            array(
                array(
                    'name' => 'id',
                    'title' => 'ID',
                    'dataPropertyName' => 'id',
                    'cssClass' => 'grid-column-id'
                ),
                array(
                    'name' => 'alias',
                    'title' => 'Alias',
                    'sortable' => true,
                    'dataPropertyName' =>
                        'alias'
                ),
                array(
                    'name' => 'title',
                    'title' => 'Description',
                    'sortable' => true,
                    'dataPropertyName' =>
                        'description'
                ),
                array(
                    'name' => 'title',
                    'title' => 'Subject',
                    'sortable' => true,
                    'dataPropertyName' =>
                        'subject'
                ),
                array(
                    'name' => 'title',
                    'title' => 'Signature',
                    'sortable' => true,
                    'dataPropertyName' =>
                        'signature'
                ),
                array(
                    'name' => 'action',
                    'title' => 'Actions',
                    'cssClass' => 'grid-column-actions',
                    'formatter' => array($this, 'actionsCellFormatter')
                ),
            )
        );

        $paginator = new Paginator($this->getPaginatorAdapter());
        $paginator->setCurrentPageNumber($this->params('page', 1));

        $grid->setItems($paginator);
        $viewModel->setVariable('grid', $grid);

        $paginator = new Paginator(new Null(12));
        $viewModel->setVariable('paginator', $paginator);
        $viewModel->setVariable('flashMessages', $this->flashMessenger()->getMessages());

        return $viewModel;
    }

    public function getPaginatorAdapter()
    {
        /** @var MailTemplatesManager $manager */
        $manager = $this->getServiceLocator()->get('MailTemplatesManager');

        return $manager->getPaginatorAdapter($this->params()->fromQuery('order', null));
    }

    /**
     * @param string $value
     * @param \ZFStarterMail\Model\MailTemplate $row
     * @param \Grid\Column $column
     * @return string
     */
    public function statusCellFormatter($value, $row, $column)
    {
        switch ($value) {
            case MailTemplate::STATUS_ACTIVE:
                return '<span class="label label-success">Active</span>';
            case MailTemplate::STATUS_INACTIVE:
                return '<span class="label label-default">Inactive</span>';
            case MailTemplate::STATUS_REMOVED:
                return '<span class="label label-danger">Removed</span>';
            default:
                return '';
        }
    }

    /**
     * @param null $value
     * @param MailTemplate $mailTemplate
     * @param Column $column
     * @param Grid $grid
     *
     * @return string
     */
    public function actionsCellFormatter($value, $mailTemplate, $column, $grid)
    {
        $items = array();
        $items[] = array(
            'title' => 'Edit',
            'uri' => $this->url()->fromRoute('admin_mail_templates_edit', array('id' => $mailTemplate->id))
        );
        $removeUrl = $this->url()->fromRoute('admin_mail_templates_remove', array('id' => $mailTemplate->id));
        $items[] = array(
            'title' => 'Remove',
            'uri' => $removeUrl,
            'onclick' => $this->bootboxConfirm('Are you sure?', $this->jsRedirect($removeUrl)) . ' return false;'
        );
        $viewModel = new ViewModel();
        $viewModel->setVariable('items', $items);

        return $viewModel;
    }


    /**
     * @return array
     */
    public function createAction()
    {
        $form = new Create();
        $viewModel = new ViewModel();

        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setData($request->getPost());

            if ($form->isValid()) {
                /** @var MailTemplatesManager $manager */
                $manager = $this->getServiceLocator()->get('MailTemplatesManager');
                $manager->createMailTemplate($this->identity(), $this->params()->fromPost());

                $this->flashMessenger()->addSuccessMessage('Created successful');
                return $this->redirect()->toRoute('admin_mail_templates_list');
            } else {
                $this->flashMessenger()->addErrorMessage('Please check form');
            }
        }

        /** @var $viewHelperManager \Zend\View\HelperPluginManager */
        $viewHelperManager = $this->getServiceLocator()->get('viewHelperManager');
        $redactorHelper = $viewHelperManager->get('Redactor'); //($id, $options);
        $redactorHelper->redactor('bodyHtml');
        $viewModel->setVariable('form', $form);
        return $viewModel;
    }


    /**
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $viewModel = new ViewModel();
        $id = (int)$this->params()->fromRoute('id');

        $form = new Edit();
        $form->get('submit')->setLabel('Save');

        /** @var MailTemplatesManager $manager */
        $manager = $this->getServiceLocator()->get('MailTemplatesManager');

        /** @var $mailTemplate \DomainModel\Object\DomainObjectMagic */
        $mailTemplate = $manager->getById($id);
        if (!$mailTemplate) {
            return $this->redirect()->toRoute('admin_mail_templates_list');
        }

        $form->setData($mailTemplate->toArray());

        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setData($request->getPost());
            if ($form->isValid()) {
                $manager->updateMailTemplate($this->identity(), $this->params()->fromPost());

                $this->flashMessenger()->addSuccessMessage('Updated successful');
                return $this->redirect()->toRoute('admin_mail_templates_list');
            } else {
                $this->flashMessenger()->addErrorMessage('Please check form');
            }
        }
        /** @var $viewHelperManager \Zend\View\HelperPluginManager */
        $viewHelperManager = $this->getServiceLocator()->get('viewHelperManager');
        $redactorHelper = $viewHelperManager->get('Redactor'); //($id, $options);
        $redactorHelper->redactor('bodyHtml');

        $viewModel->setVariable('form', $form);
        return $viewModel;
    }


    public function removeAction()
    {
        $id = (int)$this->params()->fromRoute('id');

        if (!$id) {
            $this->flashMessenger()->addErrorMessage('Something wrong');
            return $this->redirect()->toRoute('admin_mail_templates_list');
        }

        /** @var MailTemplatesManager $pagesManager */
        $pagesManager = $this->getServiceLocator()->get('MailTemplatesManager');
        $pagesManager->deleteMailTemplate($id);
        $this->flashMessenger()->addSuccessMessage('Removed successful');
        return $this->redirect()->toRoute('admin_mail_templates_list');
    }
}
