<?php

namespace BasicApp\Test;

class UploadedFile extends \CodeIgniter\HTTP\Files\UploadedFile
{

    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK;
    }

}