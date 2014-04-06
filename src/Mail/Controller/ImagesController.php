<?php
/**
 * User: naxel
 * Date: 03.04.14 10:01
 */

namespace ZFStarterMail\Controller;

use Zend\File\Transfer\Adapter\Http;
use Zend\File\Transfer\Exception\ExceptionInterface;
use Zend\Filter\File\Rename;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ImagesController extends AbstractActionController
{
    protected $uploadPath = 'uploads/mail/';
    protected $pathToImages = '/uploads/mail/images/';
    protected $pathToThumbs = '/uploads/mail/thumbs/';
    protected $thumbWidth = 120;
    protected $thumbHeight = 90;
    protected $thumbQuality = 100;


    /**
     * Images dir
     *
     * @return string
     */
    public function getPathToImagesDir()
    {
        return getcwd() . '/public/' . $this->pathToImages;
    }

    /**
     * Images thumbs dir
     *
     * @return string
     */
    public function getPathToThumbsDir()
    {
        return getcwd() . '/public/' . $this->pathToThumbs;
    }

    /**
     * list all images
     */
    public function listAction()
    {
        $images = glob($this->getPathToImagesDir() . '*.*');
        $data = array();
        foreach ($images as $image) {
            $thumb = $this->createThumb($image);
            $src = pathinfo($image, PATHINFO_BASENAME);
            $data[] = array(
                'image' => $this->pathToImages . $src,
                'thumb' => $thumb
            );
        }

        return new JsonModel($data);
    }

    /**
     * upload
     *
     * @throws \Exception
     */
    public function uploadAction()
    {
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            try {

                $destination = $this->getPathToImagesDir();

                /* Check destination folder */
                if (!is_dir($destination)) {
                    if (is_writable($this->getPathToImagesDir())) {
                        mkdir($destination, 0777, true);
                    } else {
                        throw new \Exception("Uploads directory is not writable");
                    }
                }

                /* Uploading Document File on Server */
                $upload = new Http();
                try {
                    // upload received file(s)
                    $upload->receive();
                } catch (ExceptionInterface $e) {
                    $e->getMessage();
                }

                // you MUST use following functions for knowing about uploaded file
                // Returns the file name for 'doc_path' named file element
                $filePath = $upload->getFileName('file');

                // Returns the mimetype for the 'file' form element
                $mimeType = $upload->getMimeType('file');

                // mimeType validation
                switch ($mimeType) {
                    case 'image/jpg':
                    case 'image/jpeg':
                    case 'image/pjpeg':
                        $ext = 'jpg';
                        break;
                    case 'image/png':
                        $ext = 'png';
                        break;
                    case 'image/gif':
                        $ext = 'gif';
                        break;
                    default:
                        throw new \Exception('Wrong mimetype of uploaded file. Received "' . $mimeType . '"');
                        break;
                }

                // prepare filename
                $name = pathinfo($filePath, PATHINFO_FILENAME);
                $name = strtolower($name);
                $name = preg_replace('/[^a-z0-9_-]/', '-', $name);

                // rename uploaded file
                $renameFile = $name . '.' . $ext;
                $counter = 0;
                while (file_exists($destination . $renameFile)) {
                    $counter++;
                    $renameFile = $name . '-' . $counter . '.' . $ext;
                }

                $fullFilePath = $destination . $renameFile;

                // rename uploaded file using Zend Framework
                $filterFileRename = new Rename(array('target' => $fullFilePath, 'overwrite' => true));

                $filterFileRename->filter($filePath);

                // create thumb
                $this->createThumb($fullFilePath);

                return new JsonModel(array('filelink' => $this->pathToImages . $renameFile));

            } catch (\Exception $e) {
                return new JsonModel(array('status' => 'error', 'message' => $e->getMessage()));
            }
        } else {
            return new JsonModel(array('status' => 'error', 'message' => 'Internal Error of Uploads controller'));
        }
    }

    /**
     * create thumb for image
     *
     * @param string $file path to original image
     * @throws \Exception
     * @return boolean
     */
    protected function createThumb($file)
    {
        // get original image size
        list($width, $height) = getimagesize($file);

        $tWidth = $this->thumbWidth;
        $tHeight = $this->thumbHeight;
        $fullPath = $this->getPathToThumbsDir();
        $path = $this->pathToThumbs;

        $name = pathinfo($file, PATHINFO_FILENAME);
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        $thumb =
            $name . '_' .
            $tWidth . 'x' .
            $tHeight . '.' .
            $ext;

        // if already exists - return path to file
        if (is_file($fullPath . $thumb)) {
            return $path . $thumb;
        }

        // try to create directory for thumbnails
        if (!is_dir($fullPath)) {
            if (is_writable(realpath($this->getPathToImagesDir() . '../'))) {
                mkdir($fullPath);
            } else {
                throw new \Exception("Uploads directory is not writable");
            }
        }

        if (($width > $tWidth) || ($height > $tHeight)) {

            $tHeight = min(
                $tHeight,
                $height / $width * $tWidth
            );

            $tWidth = min(
                $tWidth,
                $width / $height * $tHeight
            );

            $tHeight = $height / $width * $tWidth;

            // switch statement for image extension
            switch (strtolower($ext)) {
                case 'jpg':
                case 'jpeg':
                    $oImage = imagecreatefromjpeg($file);
                    break;
                case 'gif':
                    $oImage = imagecreatefromgif($file);
                    break;
                case 'png':
                    $oImage = imagecreatefrompng($file);
                    break;
                default:
                    throw new \Exception("Image file has wrong extension");
                    break;
            }

            $oThumb = imagecreatetruecolor($tWidth, $tHeight);

            imagecopyresampled(
                $oThumb,
                $oImage,
                0,
                0,
                0,
                0,
                $tWidth,
                $tHeight,
                $width,
                $height
            );

            imagejpeg($oThumb, $fullPath . $thumb, $this->thumbQuality);

            imagedestroy($oThumb);
            imagedestroy($oImage);
        } else {
            copy($file, $fullPath . $thumb);
        }

        return $path . $thumb;
    }
}
