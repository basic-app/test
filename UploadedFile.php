<?php
/**
 * @author basic-app <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Test;

class UploadedFile extends \CodeIgniter\HTTP\Files\UploadedFile
{

    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK;
    }

}