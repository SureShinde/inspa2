<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-push-notification
 * @version   1.1.18
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\PushNotification\Console\Command;

use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param State                  $appState
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        State $appState,
        ObjectManagerInterface $objectManager
    ) {
        $this->appState      = $appState;
        $this->objectManager = $objectManager;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:push-notification:cron')
            ->setDescription('For test purpose')
            ->setDefinition([]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $value, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('frontend');
        } catch (\Exception $e) {
        }

        $jobs = [
            \Mirasvit\PushNotification\Cron\ScheduleNotificationCron::class,
            \Mirasvit\PushNotification\Cron\PushMessageCron::class,
            \Mirasvit\PushNotification\Cron\EventCheckCron::class,
        ];

        foreach ($jobs as $className) {
            $output->write("Run job $className...");
            $job = $this->objectManager->create($className);
            $job->execute();

            $output->writeln("<info>done</info>");
        }
    }
}
