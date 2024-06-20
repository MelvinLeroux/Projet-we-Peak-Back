<?php

namespace App\Command;

use App\Entity\Activity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-activities',
    description: 'Add a short description for your command',
)]
class UpdateActivitiesCommandPhpCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Update activities based on specific criteria.')
            ->setHelp('This command updates activities in the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $activities = $this->entityManager->getRepository(Activity::class)->findAll();
        foreach ($activities as $activity) {
            // Update activity properties
            $activity->setupdatedAt(new \DateTimeImmutable());
            
            // Persist changes
            $this->entityManager->persist($activity);
        }
        // Flush changes to the database
        $this->entityManager->flush();

        $output->writeln('Activities updated successfully.');

        return Command::SUCCESS;
    }
}
