<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ConfigService
{
    private array $envVars = [
        'APP_NAME' => 'LinguaProject',
        'APP_LANGUAGE_NAME' => 'Une langue',
        'APP_ORIGINAL_LANGUAGE_NAME' => '',
        'APP_DESCRIPTION' => '',
        'APP_FAVICON' => "favicon.png",
        'APP_BANNER' => "banner.jpg",
        'PER_PAGE_INDEX' => 15,
        'PER_PAGE_CATEGORY' => 14
    ];

    public function __construct(private string $configFile, private string $publicDir)
    {
        $this->hydrateEnvVars();
    }

    public function save(array $newData): bool 
    {
        if (!$this->hasConfigFile()) {
            return false;
        }

        foreach ($newData as $key => $value) {
            $this->setEnv($key, $value);
        }

        return true;
    }

    /**
     * Gets the value of envVars
     *
     * @return array
     */
    public function getEnvVars(): array
    {
        return $this->envVars;
    }

    /**
     * Get the value of publicDir
     */
    public function getPublicDir()
    {
        return $this->publicDir;
    }

    /**
     * Sets the value of envVars
     *
     * @param array $envVars
     *
     * @return self
     */
    public function setEnvVars(array $envVars): self
    {
        $this->envVars = $envVars;

        return $this;
    }

    /**
     * Hydrates envVars
     *
     * @return self
     */
    public function hydrateEnvVars(): self
    {
        $validKeys = array_keys($this->envVars);

        foreach ($validKeys as $key) {
            if (isset($_ENV[$key])) {
                $this->envVars[$key] = $_ENV[$key];
            }
        }

        return $this;
    }

    /**
     * Move an uploaded file to a directory in the server.
     *
     * @param  UploadedFile  $file   the file to move
     * @param  string        $key    the name of env var saving the file name.
     */
    public function move(UploadedFile $file, string $key): bool
    {
        try {
            $path = $this->publicDir;
            $file->move($path, $this->envVars[$key]);

            return true;
        } catch (FileException $th) {
            return false;
        }
    }

    public function hasConfigFile(): bool
    {
        return file_exists($this->configFile);
    }
    

    private function setEnv(string $key, mixed $value): bool
    {
        if ($value instanceof UploadedFile || !isset($this->envVars[$key])) {
            return false;
        }
        $parts = (is_string($this->envVars[$key])) ? '"' : '';
        $status = file_put_contents($this->configFile, str_replace(
            $key . '=' . $parts . $this->envVars[$key] . $parts,
            $key . '=' . $parts . $value . $parts,
            file_get_contents($this->configFile)
        ));

        return is_int($status);
    }
}
