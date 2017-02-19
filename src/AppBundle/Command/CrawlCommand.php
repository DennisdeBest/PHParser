<?php

namespace AppBundle\Command;

use AppBundle\Controller\DefaultController;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:crawl')
            ->setDescription('Crawls the web');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$this->get('app.scrap:scrapAction');
        $this->getApplication()->getKernel()->getContainer()->get('app.scrap')->crawlAction();
    }
}
