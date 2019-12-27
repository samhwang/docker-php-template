<?php

/**
 * PHP version 7.4
 *
 * @package Docker_PHP_Template
 * @author  Sam Huynh <samhwang2112.dev@gmail.com>
 */

namespace Setup;

use Composer\Script\Event;
use Composer\IO\IOInterface;

/**
 * Installer class
 * 
 * @package Docker_PHP_Template
 * @author  Sam Huynh <samhwang2112.dev@gmail.com>
 */
class Installer
{
    /**
     * Post install function
     *
     * @param Event $event - Composer event
     * 
     * @return void
     */
    public static function postInstall(Event $event = null): void
    {
        $io = $event->getIO();
        $io->write('<info>Creating project from boilerplate</info>');

        // Initiate project configurations
        $init_config = $io->askConfirmation('Initiate configs ? [y/N] ', false);
        if ($init_config) {
            self::_initiateConfig($io);
        }

        self::_cleanup();
        self::_initiateGit();

        $io->write('<info>Boilerplate initialized.</info>');

        self::_build();
        $io->write('<info>Build complete.</info>');
    }

    /**
     * Initiate new project's configurations
     * 
     * @param IOInterface $io - Composer IOInterface instance
     * 
     * @return void
     */
    private static function _initiateConfig(IOInterface $io): void
    {
        self::_makeEnvFile($io);

        $compose_setup = $io->askConfirmation('Do you want to set up project information? [y/N]: ', false);
        if ($compose_setup) {
            $name = $io->ask('Project Name [project_name]: ', 'project_name');
            $description = $io->ask('Description: ', 'project_description');
            $author = $io->ask('Author name: ', 'author_name');
            $email = $io->ask('Author email [must be valid email]: ', 'author_email@mail.com');
            $projectInfo = [
                'name' => $name,
                'description' => $description,
                'author' => $author,
                'email' => $email
            ];
        }
        $io->write('<info>This can be edited later in the composer.json and docker-compose files.</info>');
        self::_renamePackage($name);
        self::_makeNewComposerFile($projectInfo);
        self::_renamePackage($name);
    }

    /**
     * Create Env File function
     * 
     * @param IOInterface $io - Composer IOInterface instance
     * 
     * @return void
     */
    private static function _makeEnvFile(IOInterface $io): void
    {
        copy('.env.sample', '.env');
        $env_file = '.env';
        $env_content = file_get_contents('.env.sample');
        $db_setup = $io->askConfirmation('Do you want to set up database connection ? [y/N]: ', false);
        if ($db_setup) {
            $db_host = $io->ask('Please enter database host [db]: ', 'db');
            $db_name = $io->ask('Please enter database name [project_db]: ', 'project_db');
            $db_usr = $io->ask('Please enter database username [project_admin]: ', 'project_admin');
            $db_pw = $io->ask('Please enter database password [project_password]: ', 'project_password');
            $env_content = str_replace('MYSQL_HOST=db', 'MYSQL_HOST=' . $db_host, $env_content);
            $env_content = str_replace('MYSQL_DATABASE=project_db', 'MYSQL_DATABASE=' . $db_name, $env_content);
            $env_content = str_replace('MYSQL_USER=project_admin', 'MYSQL_USER=' . $db_usr, $env_content);
            $env_content = str_replace('MYSQL_PASSWORD=project_password', 'MYSQL_PASSWORD=' . $db_pw, $env_content);
        }
        $io->write('<info>This can be edited later in the .env file.</info>');
        file_put_contents($env_file, $env_content);
    }

    /**
     * Clean up after finishing installation
     * 
     * @return void
     */
    private static function _cleanup(): void
    {
        unlink('composer.dist.json');
        unlink('README.md');
        exec('rm -rf scripts');
    }

    /**
     * Inititate git repository
     * 
     * @return void
     */
    private static function _initiateGit(): void
    {
        exec('rm -rf .git');
        if (!file_exists('../.git')) {
            exec('git init .');
        }

        // Add composer.lock back into tracking
        $ignore_content = file_get_contents('.gitignore');
        $ignore_content = str_replace("\ncomposer.lock", "", $ignore_content);
        file_put_contents('.gitignore', $ignore_content);

        exec("git add .; git commit -m \"Initial commit\";");
    }

    /**
     * Create new composer.json declaration file
     * 
     * @param string[] $projectInfo - project information
     * 
     * @return void
     */
    private static function _makeNewComposerFile(array $projectInfo): void
    {
        unlink('composer.json');
        copy('composer.dist.json', 'composer.json');
        $file = 'composer.json';
        $content = file_get_contents($file);

        $content = str_replace('project_name', $projectInfo['name'], $content);
        $content = str_replace('project_description', $projectInfo['description'], $content);
        $content = str_replace('author_name', $projectInfo['author'], $content);
        $content = str_replace('author_email@mail.com', $projectInfo['email'], $content);

        file_put_contents($file, $content);
    }

    /**
     * Rename project_name with the actual Project Name
     * 
     * @param string $projectName - project name
     * 
     * @return void
     */
    private static function _renamePackage(string $projectName): void
    {
        foreach (glob('{src/App/*.php,tests/App/*}', GLOB_BRACE) as $file) {
            $content = file_get_contents($file);
            $projectName = self::_convertCamelCase($projectName);
            $content = str_replace('Project_Name', $projectName, $content);
            file_put_contents($file, $content);
        }

        foreach (glob('docker-compose.*') as $dockerComposeFile) {
            $content = file_get_contents($dockerComposeFile);
            $content = str_replace('project_name', strtolower($projectName), $content);
            file_put_contents($dockerComposeFile, $content);
        }
    }

    /** 
     * Convert snake_case to Snake_Case
     * 
     * @param string $projectName - project name
     * 
     * @return string
     */
    private static function _convertCamelCase(string $projectName): string
    {
        $string_array = explode('_', $projectName);
        array_walk($string_array, fn(string $elem): string => ucwords($elem));
        return implode('_', $string_array);
    }

    /**
     * Build project dependencies
     * 
     * @return void
     */
    private static function _build(): void
    {
        // Install composer dependencies
        passthru('composer install');
        exec("git add composer.lock; git commit --amend -m \"Initial commit\"");

        // Build Docker images
        passthru('docker-compose pull');
        passthru('docker-compose build');
    }
}
