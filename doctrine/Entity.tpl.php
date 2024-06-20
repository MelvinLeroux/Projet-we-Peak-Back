<?= "<?php\n" ?>

namespace <?= $namespace ?>;
<?= $use_statements; ?>
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: <?= $repository_class_name ?>::class)]
#[Groups(["<?= strtolower($class_name) ?>"])]
<?php if ($should_escape_table_name): ?>#[ORM\Table(name: '`<?= $table_name ?>`')]
<?php endif ?>
<?php if ($api_resource): ?>
#[ApiResource]
<?php endif ?>
<?php if ($broadcast): ?>
#[Broadcast]
<?php endif ?>
class <?= $class_name."\n" ?>
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
