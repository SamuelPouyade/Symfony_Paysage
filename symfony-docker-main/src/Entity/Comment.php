<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Articles;
use App\Entity\User;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $contenu = null;

    #[ORM\ManyToOne(targetEntity: Articles::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private $article;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function setArticle(Articles $article): self
    {
        $this->article = $article;
        return $this;
    }

    public function getArticle(): ?Articles
    {
        return $this->article;
    }


    public function setUser(Users $user): self
    {
        $this->user = $user;
        return $this;
    }

    // Ajoutez cette méthode pour obtenir l'utilisateur associé au commentaire
    public function getUser(): ?Users
    {
        return $this->user;
    }
}
