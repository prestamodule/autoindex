<?php
/**
 * 2007-2010 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2010 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\AutoIndex\Command;

use PhpParser\ParserFactory;
//use PrestaShop\HeaderStamp\LicenseHeader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AddAutoIndexCommand extends Command
{
    const DEFAULT_FILTERS = [];

    /**
     * List of folders to exclude from the search
     *
     * @param array $filters
     */
    private $filters;

    protected function configure()
    {
        $this
        ->setName('prestashop:add:index')
        ->setDescription('Automatically add an "index.php" in all your directories or your zip file recursively')
        ->addOption(
            'exclude',
            null,
            InputOption::VALUE_REQUIRED,
            'Comma-separated list of folders to exclude from the update',
            implode(',', self::DEFAULT_FILTERS)
        );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->filters = explode(',', $input->getOption('exclude'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = getcwd();
        if ($dir === false) {
            throw new \Exception('Could not get current directory. Check your permissions.');
        }

        $finder = new Finder();
        $finder
            ->directories()
            ->in($dir)
            ->exclude($this->filters);
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        $output->writeln('Updating directories in ' . strtoupper($dir) . ' folder ...');
        $progress = new ProgressBar($output, count($finder));
        $progress->start();
        $progress->setRedrawFrequency(20);

        foreach ($finder as $file) {
            var_dump($file->getFilename());
            // $indexFind = new Finder();
            // $indexFind
            // ->files()
            // ->name('index.php')
            // ->in($file->getFilename());

            // foreach ($indexFind as $index) {
            //     $contents = $index->getContents();
            //     var_dump($contents);
            // }

            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');
    }

}