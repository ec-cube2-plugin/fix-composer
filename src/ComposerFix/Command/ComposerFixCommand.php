<?php

/*
 * This file is part of EC-CUBE2 CLI.
 *
 * (C) Tsuyoshi Tsurushima.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ComposerFix\Command;

use Eccube2\Init;
use Eccube2\Util\Parameter;
use Eccube2\Util\Plugin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ComposerFixCommand extends Command
{
    protected static $defaultName = 'composer-fix';

    /** @var Parameter */
    protected $parameter;

    /** @var Plugin */
    protected $plugin;

    private $parameters = array(
        'ZIP_DOWNLOAD_URL' => '"https://www.post.japanpost.jp/zipcode/dl/kogaki/zip/ken_all.zip"',
        'MODULE_DIR' => '"module/"' ,
        'MODULE_REALDIR' => 'ROOT_REALDIR . MODULE_DIR',
        'MASTER_DATA_REALDIR' => 'ROOT_REALDIR . "var/cache/master/"',
        'PLUGIN_UPLOAD_REALDIR' => 'ROOT_REALDIR . "plugin/"',
        'DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR' => 'ROOT_REALDIR . "var/temp/plugin_update/"',
        'DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR' => 'ROOT_REALDIR . "var/temp/plugin_install/"',
        'LOG_REALFILE' => 'LOG_REALDIR . "site.log"',
        'CUSTOMER_LOG_REALFILE' => 'LOG_REALDIR . "customer.log"',
        'ADMIN_LOG_REALFILE' => 'LOG_REALDIR . "admin.log"',
        'ERROR_LOG_REALFILE' => 'LOG_REALDIR . "error.log"',
        'DB_LOG_REALFILE' => 'LOG_REALDIR . "db.log"',
        'PLUGIN_LOG_REALFILE' => 'LOG_REALDIR . "plugin.log"',
        'OSTORE_LOG_REALFILE' => 'LOG_REALDIR . "ownersstore.log"',
        'CSV_TEMP_REALDIR' => 'ROOT_REALDIR . "temp/csv/"',
        'SMARTY_TEMPLATES_REALDIR' => 'ROOT_REALDIR . "templates/"',
        'COMPILE_REALDIR' => 'ROOT_REALDIR . "var/cache/smarty/" . TEMPLATE_NAME . "/"',
        'COMPILE_ADMIN_REALDIR' => 'ROOT_REALDIR . "var/cache/smarty/admin/"',
        'MOBILE_COMPILE_REALDIR' => 'ROOT_REALDIR . "var/cache/smarty/" . MOBILE_TEMPLATE_NAME . "/"',
        'SMARTPHONE_COMPILE_REALDIR' => 'ROOT_REALDIR . "var/cache/smarty/" . SMARTPHONE_TEMPLATE_NAME . "/"',
        'DOWN_TEMP_REALDIR' => 'ROOT_REALDIR . "var/temp/download/"',
        'DOWN_SAVE_REALDIR'=> 'ROOT_REALDIR . "var/download/"',
    );

    protected function configure()
    {
        $this
            ->setDescription('Composerインストール用の設定を行います。')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        Init::init();

        $this->parameter = new Parameter();
        $this->plugin = new Plugin();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('パラメーター設定');
        $this->setParameters($io, $this->parameters);

        $io->title('キャッシュクリア');
        $cacheClearCommand = $this->getApplication()->find('cache:clear');
        $cacheClearInput = new ArrayInput(array());
        $cacheClearCommand->run($cacheClearInput, $output);

        $io->title('プラグイン設定');
        $io->section('プラグインインストール');
        $installCommand = $this->getApplication()->find('plugin:install');
        $installInput = new ArrayInput(array(
            'code' => 'ComposerFix',
        ));
        $installCommand->run($installInput, $output);

        $io->section('プラグイン有効化');
        $enableCommand = $this->getApplication()->find('plugin:enable');
        $enableInput = new ArrayInput(array(
            'code' => 'ComposerFix',
        ));
        $enableCommand->run($enableInput, $output);
    }

    private function setParameters(SymfonyStyle $io, $parameters)
    {
        foreach ($parameters as $key => $value) {
            $this->parameter->set($key, $value, false);
        }

        $io->success('パラメーターを設定しました。');
    }
}
