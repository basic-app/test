<?php
/**
 * @author basic-app <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Test;

use Exception;
use ReflectionClass;

class FileCollection extends \CodeIgniter\HTTP\Files\FileCollection
{

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

    public function populateFromFiles(array $files, bool $setGlobalVar = false)
    {
        foreach($files as $key => $filename)
        {
            if (!is_file($filename))
            {
                throw new Exception('File not found: ' . $filename);
            }

            $files[$key] = [
                'name' => basename($filename),
                'type' => mime_content_type($filename),
                'tmp_name' => $filename,
                'error' => 0,
                'size' => filesize($filename)
            ];
        }

        if ($setGlobalVar)
        {
            $_FILES = $files;
        }

        return $this->populateFromArray($files);
    }

    public function assignToRequest(\CodeIgniter\Http\Request $request)
    {
        $reflection = new ReflectionClass($request);
        
        $property = $reflection->getProperty('files');
        $property->setAccessible(true);
        $property->setValue($request, $this);

        return $this;
    }

    protected function createFileObject(array $array)
    {
        if (! isset($array['name']))
        {
            $output = [];

            foreach ($array as $key => $values)
            {
                if (! is_array($values))
                {
                    continue;
                }

                $output[$key] = $this->createFileObject($values);
            }

            return $output;
        }

        return new UploadedFile(
                $array['tmp_name'] ?? null, 
                $array['name'] ?? null, 
                $array['type'] ?? null, 
                $array['size'] ?? null, 
                $array['error'] ?? null
        );
    }


}