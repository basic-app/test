<?php

namespace BasicApp\Test;

class FileCollection extends \CodeIgniter\HTTP\Files\FileCollection
{

    /**
     * Taking information from the array, it creates an instance
     * of UploadedFile for each one, saving the results to this->files.
     */
    public function populateFromArray(array $files)
    {
        if (is_array($this->files))
        {
            return;
        }

        $this->files = [];

        if (empty($files))
        {
            return;
        }

        $files = $this->fixFilesArray($files);

        foreach ($files as $name => $file)
        {
            $this->files[$name] = $this->createFileObject($file);
        }
    }

}