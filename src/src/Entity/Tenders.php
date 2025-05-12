<?php

namespace App\Entity;

use App\Repository\TendersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TendersRepository::class)]
class Tenders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tender_write','tender_get'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups(['tender_write','tender_get'])]

    private ?string $external_code = null;

    #[ORM\Column(length: 255)]
    #[Groups(['tender_write','tender_get'])]

    private ?string $number = null;

    #[ORM\Column(length: 255)]
    #[Groups(['tender_write','tender_get'])]

    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['tender_write','tender_get'])]

    private ?\DateTime $date_update = null;

    #[ORM\ManyToOne(inversedBy: 'tenders')]
    #[Groups(['tender_write','tender_get'])]

    private ?Status $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExternalCode(): ?string
    {
        return $this->external_code;
    }

    public function setExternalCode(string $external_code): static
    {
        $this->external_code = $external_code;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDateUpdate(): string
    {
        return $this->date_update->format('d.m.Y H:i:s') ;
    }

    public function setDateUpdate(\DateTime $date_update): static
    {
        $this->date_update = $date_update;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }
}
