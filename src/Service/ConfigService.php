<?php

namespace App\Service;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;
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
        'PER_PAGE_CATEGORY' => 14,
        'APP_CONTACT_EMAIL' => '',
        'APP_CONTACT_NAME' => '',
        'SMTP_USER' => '',
        'SMTP_PASSWORD' => '',
        'SMTP_HOST' => '',
        'SMTP_PORT' => '',
    ];

    public function __construct(private string $configFile, private string $publicDir)
    {
        $this->hydrateEnvVars();
        $this->hydrateDsnParams();
    }

    public function save(array $newData): bool 
    {
        if (!$this->hasConfigFile()) {
            return false;
        }

        $keys = [
            'APP_NAME',
            'APP_LANGUAGE_NAME',
            'APP_ORIGINAL_LANGUAGE_NAME',
            'APP_DESCRIPTION',
            'APP_FAVICON',
            'APP_BANNER',
            'PER_PAGE_INDEX',
            'PER_PAGE_CATEGORY',
            'APP_CONTACT_EMAIL',
            'APP_CONTACT_NAME'
        ];

        foreach ($keys as $key) {
            $this->setEnv($key, $newData[$key]);
        }

        $smtpUser = isset($newData['SMTP_USER']) ? $newData['SMTP_USER'] : '';
        $smtpPassword = isset($newData['SMTP_PASSWORD']) ? $newData['SMTP_PASSWORD'] : '';
        $smtpHost = isset($newData['SMTP_HOST']) ? $newData['SMTP_HOST'] : '';
        $smtpPort = isset($newData['SMTP_PORT']) ? $newData['SMTP_PORT'] : '';

        if (strlen($smtpUser) <= 1 || strlen($smtpPassword) <= 1 || strlen($smtpHost) <= 1 || strlen($smtpPort) <= 1) {
            return true;
        }

        $mailerDSN = "smtp://{$smtpUser}:{$smtpPassword}@{$smtpHost}:{$smtpPort}";
        $this->setEnv('MAILER_DSN', $mailerDSN);

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

    public function hydrateDsnParams(): self
    {
        if (!isset($_ENV['MAILER_DSN'])) {
            return $this;
        }
        
        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);

        if (!$transport instanceof EsmtpTransport) {
            return $this;
        }
        $this->envVars['SMTP_USER'] = $transport->getUsername();
        $this->envVars['SMTP_PASSWORD'] = $transport->getPassword();

        $stream = $transport->getStream();

        if ($stream instanceof SocketStream) {
            $this->envVars['SMTP_HOST'] = $stream->getHost();
            $this->envVars['SMTP_PORT'] = $stream->getPort();
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
        $parts = (is_string($_ENV[$key]) && $key !== 'MAILER_DSN') ? '"' : '';
        $search = $_ENV[$key];

        if ($key === 'MAILER_DSN') {
            if (preg_match('/null:\/\/default/', file_get_contents($this->configFile))) {
                $search = "null://default";
            }
        }

        $status = file_put_contents($this->configFile, str_replace(
            $key . '=' . $parts . $search . $parts,
            $key . '=' . $parts . $value . $parts,
            file_get_contents($this->configFile)
        ));
        return is_int($status);
    }
}
