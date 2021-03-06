<?php

namespace Bundle\LichessBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

/**
 * Remove old games to preserve the server inode table
 */
class GameRotateCommand extends BaseCommand
{
    protected $output;
    protected $gameDir;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
            ))
            ->setName('lichess:game:rotate')
        ;
    }

    protected function getMaxNbGames()
    {
        return 50000;
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When the target directory does not exist
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->gameDir = $this->container->getParameter('lichess.persistence.dir');
        $nbGames = $this->getNbGames();
        $maxNbGames = $this->getMaxNbGames();

        $output->writeln(sprintf('%d games, %d max.', $nbGames, $maxNbGames));

        if($nbGames <= $maxNbGames) {
            $output->writeln('Exit.');
            return;
        }

        $nbOldGames = $nbGames - $maxNbGames;
        $output->writeln(sprintf('Will remove %d games.', $nbOldGames));

        $gameHashes = $this->runCommand(sprintf('ls -tu %s | tail -%d', $this->gameDir, $nbOldGames));

        foreach($gameHashes as $gameHash) {
            $this->deleteGame($gameHash);
        }

        $output->writeln('Done');
        
        $nbGames = $this->getNbGames();

        $output->writeln(sprintf('%d games', $nbGames));
    }

    protected function getNbGames()
    {
        $output = $this->runCommand(sprintf('ls %s | wc -l', $this->gameDir));
        return (int) $output[0];
    }

    protected function deleteGame($gameHash)
    {
        $this->output->write('.');
        // remove data
        unlink($this->gameDir.'/'.$gameHash);
    }

    protected function runCommand($command)
    {
        //$this->output->writeln($command);
        exec($command, $output, $code);
        if($code !== 0)
        {
            throw new \RuntimeException(sprintf('Can not run '.$command.' '.implode("\n", $output)));
        }
        return $output;
    }
}
