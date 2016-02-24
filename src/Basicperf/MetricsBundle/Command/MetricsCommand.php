<?php
// src/Basicperf/MetricsBundle/Command/MetricsCommand.php
namespace Basicperf\MetricsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MetricsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metrics:get')
            ->setDescription('Get metrics frontend by page type')
            ->addArgument(
                'date',
                InputArgument::OPTIONAL,
                'Select the day'
            )
            ->addOption(
                'save',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will save metrics in database'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $date = $input->getArgument('date');
        if ($date == '') {
            $date = 1;
        }
        $metrics_basilic = $this->getContainer()->get('basicperf_metrics.apibasilic');
        $get_metrics = $metrics_basilic->GetMetrics($date);

        $date_from = date(DATE_ATOM, mktime(0, 0, 0, date("m"), date("d")-$date, date("Y")));
        $io->title('Temps de load du '.$date_from);

        if ($get_metrics) {
            foreach ($get_metrics as $key => $value) {
                $output->writeln($value["pagetype"].' ----> '.$value["time"]);
            }
        }

        $save = $input->getOption('save');

        if ($save) {
            $set_metrics = $metrics_basilic->SetMetrics($get_metrics);
            if ($set_metrics) {
                $output->writeln("Données sauvegardé");
            }else{
                $output->writeln("Erreur lors de la sauvegarde");
            }

        }

    }
}
?>